<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DashboardMetric extends Model
{
    use HasFactory;

    protected $fillable = ['metric_name', 'metric_value', 'period', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
