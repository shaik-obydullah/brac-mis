<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrantDocument extends Model
{
    use HasFactory;

    protected $fillable = ['migrant_id', 'type', 'file_path', 'expiry_date'];

    public function migrant()
    {
        return $this->belongsTo(Migrant::class);
    }
}
