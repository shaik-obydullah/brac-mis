<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    public const PROFILE_GENERAL_USER = 'general_user';

    public const PROFILE_REINTEGRATED = 'reintegrated';

    public const PROFILE_POTENTIAL_CUSTOMER = 'potential_customer';

    public const PROFILE_TYPES = [
        self::PROFILE_GENERAL_USER => 'General User',
        self::PROFILE_REINTEGRATED => 'Reintegrated Profile',
        self::PROFILE_POTENTIAL_CUSTOMER => 'Potential Customer Profile',
    ];

    protected $fillable = ['name', 'email', 'password', 'phone', 'status', 'profile_type', 'branch_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function skills()
    {
        return $this->hasMany(UserSkill::class);
    }

    public function migrationHistories()
    {
        return $this->hasMany(UserMigrationHistory::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function providedSupports()
    {
        return $this->hasMany(ImmediateSupport::class, 'provided_by');
    }

    public function supports()
    {
        return $this->hasMany(ImmediateSupport::class);
    }

    public function scopeReintegrated($query)
    {
        return $query->where('profile_type', self::PROFILE_REINTEGRATED);
    }

    public function scopePotentialCustomers($query)
    {
        return $query->where('profile_type', self::PROFILE_POTENTIAL_CUSTOMER);
    }

    public function isPotentialCustomer(): bool
    {
        return $this->profile_type === self::PROFILE_POTENTIAL_CUSTOMER;
    }

    public function getProfileTypeLabelAttribute(): string
    {
        return self::PROFILE_TYPES[$this->profile_type] ?? ucfirst((string) $this->profile_type);
    }
}
