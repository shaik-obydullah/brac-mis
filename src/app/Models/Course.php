<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    public const LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ];

    protected $fillable = [
        'code', 'title', 'category', 'description', 'duration_hours',
        'level', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'duration_hours' => 'integer',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
