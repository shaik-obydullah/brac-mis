<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwarenessProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_name', 'division', 'district', 'upazila', 'address', 'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outcomes()
    {
        return $this->hasMany(AwarenessProgramOutcome::class);
    }
}
