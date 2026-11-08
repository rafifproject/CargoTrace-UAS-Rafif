<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    public $timestamps = false; // append-only: uses created_at only

    public const TYPE_STATUS_CHANGE = 'STATUS_CHANGE';
    public const TYPE_CHECKPOINT_REACHED = 'CHECKPOINT_REACHED';
    public const TYPE_DEPARTURE = 'DEPARTURE';
    public const TYPE_ARRIVAL = 'ARRIVAL';
    public const TYPE_CUSTOMS_CLEARED = 'CUSTOMS_CLEARED';
    public const TYPE_EXCEPTION_NOTED = 'EXCEPTION_NOTED';

    protected $fillable = [
        'shipment_id',
        'event_type',
        'location_id',
        'location_name_custom',
        'latitude',
        'longitude',
        'event_time',
        'shipment_status_at_event',
        'description',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'created_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

