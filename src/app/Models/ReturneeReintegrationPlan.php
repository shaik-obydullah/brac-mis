<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturneeReintegrationPlan extends Model
{
    use HasFactory;

    protected $fillable = ['returnee_id', 'staff_id', 'goal', 'activities', 'timeline', 'status'];

    public function returnee()
    {
        return $this->belongsTo(Returnee::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
