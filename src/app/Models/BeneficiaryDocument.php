<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeneficiaryDocument extends Model
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'type', 'file_path'];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
