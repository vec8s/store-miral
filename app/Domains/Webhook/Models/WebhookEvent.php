<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Models;

use App\Domains\Webhook\Enums\WebhookEventStatus;
use App\Domains\Webhook\Enums\WebhookEventType;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends BaseModel
{
    use HasFactory;

    protected $fillable = ['webhook_endpoint_id', 'event_id', 'event_type', 'resource_type', 'resource_id', 'payload', 'headers', 'source_ip', 'signature', 'status', 'attempts', 'response_status', 'response_body', 'error_message', 'received_at', 'processed_at', 'failed_at'];

    protected function casts(): array
    {
        return ['event_type' => WebhookEventType::class, 'status' => WebhookEventStatus::class, 'payload' => 'array', 'headers' => 'array', 'attempts' => 'integer', 'response_status' => 'integer', 'received_at' => 'datetime', 'processed_at' => 'datetime', 'failed_at' => 'datetime'];
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}
