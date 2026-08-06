<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Returnee extends Model
{
    use HasFactory;

    protected $fillable = [
        'migrant_id', 'beneficiary_id', 'return_date', 'return_reason',
        'origin_country_id', 'current_status',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function migrant()
    {
        return $this->belongsTo(Migrant::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function originCountry()
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function reintegrationPlans()
    {
        return $this->hasMany(ReturneeReintegrationPlan::class);
    }

    public function skillAssessments()
    {
        return $this->hasMany(ReturneeSkillAssessment::class);
    }

    public function livelihoodSupport()
    {
        return $this->hasMany(ReturneeLivelihoodSupport::class);
    }

    public function microfinance()
    {
        return $this->hasMany(ReturneeMicrofinance::class);
    }

    public function followUps()
    {
        return $this->hasMany(ReturneeFollowUp::class);
    }
}
