<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeneficiaryIntervention extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'type', 'start_date', 'end_date', 'status', 'notes', 'created_by'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
