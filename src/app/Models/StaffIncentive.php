<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffIncentive extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const PAID_FROM = [
        'cash' => 'Cash in Hand',
        'bank' => 'Bank Account',
    ];

    protected $fillable = [
        'staff_id', 'title', 'amount', 'paid_from', 'incentive_date', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'incentive_date' => 'date',
    ];

    public function getPaidFromLabelAttribute(): string
    {
        return self::PAID_FROM[$this->paid_from] ?? self::PAID_FROM['cash'];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
