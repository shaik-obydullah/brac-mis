<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'employee_id', 'designation', 'branch_id', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function beneficiaryFollowUps()
    {
        return $this->hasMany(BeneficiaryFollowUp::class);
    }

    public function returneeFollowUps()
    {
        return $this->hasMany(ReturneeFollowUp::class);
    }

    public function returneeReintegrationPlans()
    {
        return $this->hasMany(ReturneeReintegrationPlan::class);
    }
}
