<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    public const STATUSES = [
        'enrolled' => 'Enrolled',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'dropped' => 'Dropped',
    ];

    protected $fillable = [
        'course_id', 'participant_type', 'participant_id', 'enrolled_at',
        'completed_at', 'status', 'score', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'date',
            'completed_at' => 'date',
            'score' => 'decimal:2',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function participant()
    {
        return $this->morphTo();
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function getParticipantNameAttribute(): string
    {
        return $this->participant->name ?? 'Unknown participant';
    }

    public function getParticipantTypeLabelAttribute(): string
    {
        return match ($this->participant_type) {
            'user' => 'User',
            default => ucfirst((string) $this->participant_type),
        };
    }
}
