<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturneeLivelihoodSupport extends Model
{
    use HasFactory;

    protected $table = 'returnee_livelihood_support';

    protected $fillable = ['returnee_id', 'type', 'amount', 'provider', 'start_date', 'end_date', 'status'];

    public function returnee()
    {
        return $this->belongsTo(Returnee::class);
    }
}
