<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeneficiaryHousehold extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'member_name', 'relationship', 'age', 'occupation', 'monthly_income'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
