<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrantDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'migrant_id', 'country_id', 'city', 'employer_name',
        'employer_contact', 'contract_start', 'contract_end',
        'salary_amount', 'salary_currency', 'status',
    ];

    public function migrant()
    {
        return $this->belongsTo(Migrant::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
