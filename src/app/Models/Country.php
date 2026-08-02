<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'currency', 'status'];

    public function migrants()
    {
        return $this->hasMany(Migrant::class, 'destination_country_id');
    }

    public function migrantDestinations()
    {
        return $this->hasMany(MigrantDestination::class, 'country_id');
    }
}
