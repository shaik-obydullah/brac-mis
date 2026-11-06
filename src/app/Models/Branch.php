<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public const TYPE_HEAD_OFFICE = 'head_office';

    public const TYPE_DISTRICT = 'district';

    public const TYPE_UPAZILA = 'upazila';

    protected $fillable = ['name', 'code', 'type', 'parent_id', 'division', 'district', 'upazila', 'status'];

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
