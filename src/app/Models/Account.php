<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public const TYPE_ASSET = 'asset';

    public const TYPE_LIABILITY = 'liability';

    public const TYPE_EQUITY = 'equity';

    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    public const TYPES = [
        self::TYPE_ASSET => 'Asset',
        self::TYPE_LIABILITY => 'Liability',
        self::TYPE_EQUITY => 'Equity',
        self::TYPE_INCOME => 'Income',
        self::TYPE_EXPENSE => 'Expense',
    ];

    public const NORMAL_BALANCE = [
        self::TYPE_ASSET => 'debit',
        self::TYPE_EXPENSE => 'debit',
        self::TYPE_LIABILITY => 'credit',
        self::TYPE_EQUITY => 'credit',
        self::TYPE_INCOME => 'credit',
    ];

    protected $fillable = [
        'code', 'name', 'type', 'normal_balance', 'parent_id', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function normalBalanceFor(string $type): string
    {
        return self::NORMAL_BALANCE[$type] ?? 'debit';
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('code');
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getIsDebitNormalAttribute(): bool
    {
        return $this->normal_balance === 'debit';
    }

    public function getBalanceAttribute(): float
    {
        // Draft entries are not part of the ledger yet, so the account balance
        // must match the posted-only balance shown in the financial reports.
        if (array_key_exists('posted_debit', $this->attributes) || array_key_exists('posted_credit', $this->attributes)) {
            // The accounts index eager-loads these sums to avoid one query per row.
            $debit = (float) ($this->attributes['posted_debit'] ?? 0);
            $credit = (float) ($this->attributes['posted_credit'] ?? 0);
        } else {
            $totals = $this->lines()
                ->whereHas('entry', fn ($query) => $query->posted())
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $debit = (float) ($totals->total_debit ?? 0);
            $credit = (float) ($totals->total_credit ?? 0);
        }

        return $this->is_debit_normal ? $debit - $credit : $credit - $debit;
    }
}
