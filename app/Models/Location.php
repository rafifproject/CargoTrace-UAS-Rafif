<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public const TYPE_PORT = 'PORT';
    public const TYPE_AIRPORT = 'AIRPORT';
    public const TYPE_WAREHOUSE = 'WAREHOUSE';
    public const TYPE_CUSTOMS_OFFICE = 'CUSTOMS_OFFICE';

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    protected $fillable = [
        'name',
        'city',
        'country',
        'address',
        'location_type',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function shipmentsOrigin(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    public function shipmentsDestination(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }

    public function shipmentsCurrent(): HasMany
    {
        return $this->hasMany(Shipment::class, 'current_location_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }
}

