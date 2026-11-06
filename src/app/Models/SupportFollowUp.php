<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFollowUp extends Model
{
    use HasFactory;

    public const METHODS = [
        'phone_call' => 'Phone Call',
        'home_visit' => 'Home Visit',
        'office_visit' => 'Office Visit',
        'community_visit' => 'Community Visit',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'missed' => 'Missed',
    ];

    protected $fillable = [
        'immediate_support_id', 'follow_up_date', 'method', 'status', 'is_reintegrated',
        'reintegrated_at', 'outcome', 'notes', 'next_follow_up_date', 'followed_by',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'next_follow_up_date' => 'date',
            'is_reintegrated' => 'boolean',
            'reintegrated_at' => 'datetime',
        ];
    }

    public function support()
    {
        return $this->belongsTo(ImmediateSupport::class, 'immediate_support_id');
    }

    public function followedBy()
    {
        return $this->belongsTo(User::class, 'followed_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? ucfirst(str_replace('_', ' ', $this->method));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
