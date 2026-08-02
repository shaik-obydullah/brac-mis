<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturneeSkillAssessment extends Model
{
    use HasFactory;

    protected $fillable = ['returnee_id', 'skill_name', 'proficiency_level', 'certification', 'assessed_by', 'assessed_date'];

    public function returnee()
    {
        return $this->belongsTo(Returnee::class);
    }
}
