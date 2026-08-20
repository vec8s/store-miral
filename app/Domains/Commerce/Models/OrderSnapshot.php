<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSnapshot extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_connection_id',
        'order_id',
        'salla_order_id',
        'source_event_id',
        'version_hash',
        'status',
        'payment_status',
        'fulfillment_status',
        'total',
        'currency',
        'customer_json',
        'receiver_json',
        'shipping_json',
        'items_json',
        'payments_json',
        'shipments_json',
        'raw_payload_compressed',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'customer_json' => 'array',
            'receiver_json' => 'array',
            'shipping_json' => 'array',
            'items_json' => 'array',
            'payments_json' => 'array',
            'shipments_json' => 'array',
            'captured_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
