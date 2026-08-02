<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Migrant extends Model
{
    use HasFactory;

    protected $fillable = [
        'brac_id', 'name', 'gender', 'date_of_birth', 'nid_number',
        'phone', 'passport_number', 'origin_district_id', 'origin_upazila_id',
        'destination_country_id', 'destination_city', 'skill_level',
        'education_level', 'occupation', 'status', 'beneficiary_id',
    ];

    protected static function booted(): void
    {
        static::creating(function ($migrant) {
            if (empty($migrant->brac_id)) {
                $migrant->brac_id = 'MIG-' . strtoupper(Str::random(10));
            }
        });
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function destinationCountry()
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function destinations()
    {
        return $this->hasMany(MigrantDestination::class);
    }

    public function documents()
    {
        return $this->hasMany(MigrantDocument::class);
    }

    public function financialRecords()
    {
        return $this->hasMany(MigrantFinancialRecord::class);
    }

    public function returnee()
    {
        return $this->hasOne(Returnee::class);
    }
}
