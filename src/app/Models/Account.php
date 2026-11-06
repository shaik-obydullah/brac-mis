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
        $debit = (float) $this->lines()->sum('debit');
        $credit = (float) $this->lines()->sum('credit');

        return $this->is_debit_normal ? $debit - $credit : $credit - $debit;
    }
}
