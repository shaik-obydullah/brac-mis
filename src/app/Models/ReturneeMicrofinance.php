<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturneeMicrofinance extends Model
{
    use HasFactory;

    protected $table = 'returnee_microfinance';

    protected $fillable = ['returnee_id', 'loan_amount', 'loan_purpose', 'disbursement_date', 'repayment_schedule', 'status'];

    public function returnee()
    {
        return $this->belongsTo(Returnee::class);
    }
}
