<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SallaWebhookEvent extends BaseModel
{
    use HasFactory;

    protected $table = 'salla_webhook_events';

    protected $fillable = [
        'salla_connection_id',
        'event_key',
        'event_name',
        'salla_order_id',
        'payload',
        'payload_hash',
        'signature_valid',
        'received_at',
        'processed_at',
        'failed_at',
        'attempts',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'signature_valid' => 'boolean',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }
}
