<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_DELIVERED = 'DELIVERED';

    public const TRANSPORT_SEA = 'SEA';
    public const TRANSPORT_AIR = 'AIR';
    public const TRANSPORT_LAND = 'LAND';

    public const DELAY_NONE = 'NONE';
    public const DELAY_LOW = 'LOW';
    public const DELAY_MEDIUM = 'MEDIUM';
    public const DELAY_HIGH = 'HIGH';

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_location_id',
        'destination_location_id',
        'delivery_country',
        'delivery_city',
        'delivery_district',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'is_last_mile',
        'courier_name',
        'courier_vehicle',
        'coffee_variety',
        'coffee_grade',
        'current_location_id',
        'transport_mode',
        'cargo_type',
        'weight',
        'volume',
        'expected_departure',
        'expected_arrival',
        'actual_departure',
        'actual_arrival',
        'status',
        'invoice_number',
        'shipping_cost',
        'payment_status',
        'payment_method',
        'paid_at',
        'estimated_time_arrival',
        'delay_minutes',
        'delay_severity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_departure' => 'datetime',
            'expected_arrival' => 'datetime',
            'actual_departure' => 'datetime',
            'actual_arrival' => 'datetime',
            'estimated_time_arrival' => 'datetime',
            'paid_at' => 'datetime',
            'weight' => 'float',
            'volume' => 'float',
            'shipping_cost' => 'float',
            'delay_minutes' => 'integer',
            'is_last_mile' => 'boolean',
            'delivery_lat' => 'float',
            'delivery_lng' => 'float',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'desc');
    }

    public function trackingEventsAsc(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->orderBy('created_at', 'desc');
    }

    public function openExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->whereIn('status', [
            ShipmentException::STATUS_OPEN,
            ShipmentException::STATUS_INVESTIGATING,
        ]);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Recalculate delay duration and delay severity based on expected arrival and ETA.
     */
    public function recalculateDelay(): void
    {
        if (! $this->expected_arrival || ! $this->estimated_time_arrival) {
            $this->delay_minutes = 0;
            $this->delay_severity = self::DELAY_NONE;
            return;
        }

        $expected = Carbon::parse($this->expected_arrival);
        $eta = Carbon::parse($this->estimated_time_arrival);

        if ($eta->greaterThan($expected)) {
            $diffMinutes = (int) $expected->diffInMinutes($eta);
            $this->delay_minutes = $diffMinutes;

            $hours = $diffMinutes / 60;
            if ($hours <= 6) {
                $this->delay_severity = self::DELAY_LOW;
            } elseif ($hours <= 24) {
                $this->delay_severity = self::DELAY_MEDIUM;
            } else {
                $this->delay_severity = self::DELAY_HIGH;
            }
        } else {
            $this->delay_minutes = 0;
            $this->delay_severity = self::DELAY_NONE;
        }
    }

    public function isDelayed(): bool
    {
        return $this->delay_minutes > 0;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'PAID';
    }

    public function getTotalCommodityValueAttribute(): float
    {
        return (float) $this->items->sum('declared_value');
    }

    public function getTotalInvoiceAmountAttribute(): float
    {
        return (float) ($this->total_commodity_value + ($this->shipping_cost ?? 0));
    }

    public function getFormattedDelayAttribute(): string
    {
        if ($this->delay_minutes <= 0) {
            return 'Tepat Waktu';
        }

        $days = floor($this->delay_minutes / 1440);
        $hours = floor(($this->delay_minutes % 1440) / 60);

        if ($days > 0) {
            return "+{$days} hari" . ($hours > 0 ? " {$hours} jam" : '');
        }

        return "+{$hours} jam";
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_BOOKED,
            self::STATUS_PICKED_UP,
            self::STATUS_IN_TRANSIT,
        ]);
    }

    public function scopeDelayed(Builder $query): Builder
    {
        return $query->where('delay_minutes', '>', 0);
    }
}

