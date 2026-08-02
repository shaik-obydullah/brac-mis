<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrantFinancialRecord extends Model
{
    use HasFactory;

    protected $fillable = ['migrant_id', 'type', 'amount', 'currency', 'description', 'date'];

    public function migrant()
    {
        return $this->belongsTo(Migrant::class);
    }
}
