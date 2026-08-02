<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeneficiaryFollowUp extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'staff_id', 'type', 'date', 'notes', 'next_date', 'status'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
