<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    public const TYPE_DELAY_ALERT = 'DELAY_ALERT';
    public const TYPE_EXCEPTION_ALERT = 'EXCEPTION_ALERT';
    public const TYPE_STATUS_UPDATE = 'STATUS_UPDATE';
    public const TYPE_SYSTEM_NOTICE = 'SYSTEM_NOTICE';

    public const SEVERITY_INFO = 'INFO';
    public const SEVERITY_WARNING = 'WARNING';
    public const SEVERITY_DANGER = 'DANGER';
    public const SEVERITY_SUCCESS = 'SUCCESS';

    protected $fillable = [
        'user_id',
        'shipment_id',
        'type',
        'title',
        'message',
        'severity',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}

