<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwarenessProgramOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'awareness_program_id', 'participant_name', 'participant_phone', 'understood', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'understood' => 'boolean',
        ];
    }

    public function awarenessProgram()
    {
        return $this->belongsTo(AwarenessProgram::class);
    }
}
