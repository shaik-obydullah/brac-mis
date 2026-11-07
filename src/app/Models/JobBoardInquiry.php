<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobBoardInquiry extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'shared' => 'Contact shared',
        'closed' => 'Closed',
    ];

    protected $fillable = [
        'user_id', 'employer_name', 'employer_organization', 'employer_email',
        'employer_phone', 'job_title', 'message', 'status', 'handled_by',
        'handled_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }
}
