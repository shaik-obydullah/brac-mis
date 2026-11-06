<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'skill_name', 'proficiency', 'years_of_experience', 'certification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
