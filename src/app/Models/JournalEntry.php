<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_POSTED = 'posted';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_POSTED => 'Posted',
    ];

    protected $fillable = [
        'entry_no', 'entry_date', 'reference', 'description',
        'branch_id', 'status', 'created_by', 'posted_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public static function nextEntryNo(CarbonInterface|string|null $date = null): string
    {
        $date = $date instanceof CarbonInterface ? Carbon::parse($date) : Carbon::parse($date ?? now());
        $prefix = 'JE-'.$date->format('Y').'-';

        // Order by the length of the number first so the sequence still
        // increments correctly after 9,999 entries in one year. The locking
        // read is important: it sees the latest committed rows even under
        // MySQL's REPEATABLE READ isolation, so a retry can pick the next
        // number instead of reading the same stale snapshot again.
        $last = static::where('entry_no', 'like', $prefix.'%')
            ->orderByRaw('LENGTH(entry_no) DESC')
            ->orderByDesc('entry_no')
            ->lockForUpdate()
            ->value('entry_no');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a journal entry with the next available number for its year.
     *
     * Entry numbers are a human-readable sequence, so two transactions can
     * legitimately try to claim the same number at the same time. Retrying on
     * the unique index (instead of relying on a read-then-write gap) keeps
     * concurrent posts from failing with a duplicate-key error.
     */
    public static function createWithNextEntryNo(array $attributes): self
    {
        $attempts = 0;

        while (true) {
            try {
                return DB::transaction(function () use ($attributes) {
                    $attributes['entry_no'] = static::nextEntryNo($attributes['entry_date'] ?? null);

                    return static::create($attributes);
                });
            } catch (QueryException $exception) {
                if (++$attempts >= 5 || ! static::isRetryableException($exception)) {
                    throw $exception;
                }
            }
        }
    }

    private static function isRetryableException(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;

        return $driverCode === 1062      // duplicate unique key
            || $driverCode === 1213      // MySQL deadlock
            || $driverCode === 1205      // lock wait timeout
            || $sqlState === '40001'     // serialization failure
            || str_contains($exception->getMessage(), 'journal_entries.entry_no');
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function getTotalDebitAttribute(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->lines->sum('credit');
    }

    public function getIsBalancedAttribute(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.005;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
