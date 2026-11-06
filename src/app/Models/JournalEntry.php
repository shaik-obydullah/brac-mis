<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

        $last = static::where('entry_no', 'like', $prefix.'%')
            ->orderByDesc('entry_no')
            ->value('entry_no');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
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
