<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    public const STATUSES = [
        'valid' => 'Valid',
        'revoked' => 'Revoked',
    ];

    protected $fillable = [
        'course_id', 'course_enrollment_id', 'certificate_number', 'verification_code',
        'recipient_type', 'recipient_id', 'recipient_name', 'issued_date',
        'expiry_date', 'status', 'remarks', 'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id');
    }

    public function recipient()
    {
        return $this->morphTo();
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    public function getValidityLabelAttribute(): string
    {
        if ($this->status === 'revoked') {
            return 'Revoked';
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        return 'Valid';
    }
}
