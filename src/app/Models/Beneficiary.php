<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'brac_id', 'branch_id', 'name', 'father_name', 'mother_name',
        'gender', 'date_of_birth', 'nid_number', 'phone',
        'address_line_1', 'address_line_2', 'union_id', 'upazila_id',
        'district_id', 'division_id', 'occupation', 'monthly_income',
        'family_size', 'status', 'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Beneficiary $beneficiary) {
            if (!$beneficiary->brac_id) {
                $maxId = static::max('id') ?? 0;
                $beneficiary->brac_id = 'BRAC-BEN-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function documents()
    {
        return $this->hasMany(BeneficiaryDocument::class);
    }

    public function households()
    {
        return $this->hasMany(BeneficiaryHousehold::class);
    }

    public function interventions()
    {
        return $this->hasMany(BeneficiaryIntervention::class);
    }

    public function followUps()
    {
        return $this->hasMany(BeneficiaryFollowUp::class);
    }

    public function migrants()
    {
        return $this->hasMany(Migrant::class);
    }

    public function returnees()
    {
        return $this->hasMany(Returnee::class);
    }
}
