<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMINISTRATOR = 'ADMINISTRATOR';
    public const ROLE_LOGISTICS_OPERATOR = 'LOGISTICS_OPERATOR';
    public const ROLE_CUSTOMER = 'CUSTOMER';

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'company_name',
        'phone',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function isOperator(): bool
    {
        return $this->role === self::ROLE_LOGISTICS_OPERATOR;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'customer_id');
    }

    public function trackingEventsCreated(): HasMany
    {
        return $this->hasMany(TrackingEvent::class, 'created_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function exceptionsReported(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'reported_by');
    }

    public function exceptionsResolved(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'resolved_by');
    }
}
