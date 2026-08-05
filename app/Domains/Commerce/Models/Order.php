<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Enums\PaymentMethod;
use App\Domains\Commerce\Enums\PaymentStatus;
use App\Domains\Commerce\Enums\ShippingStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'customer_id',
        'reference_id',
        'status',
        'payment_status',
        'shipping_status',
        'payment_method',
        'currency',
        'total_minor',
        'subtotal_minor',
        'shipping_cost_minor',
        'tax_amount_minor',
        'discount_minor',
        'shipping_address',
        'billing_address',
        'extra_attributes',
        'source_updated_at',
        'placed_at',
        'synced_at',
        'sync_status',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'shipping_status' => ShippingStatus::class,
            'payment_method' => PaymentMethod::class,
            'sync_status' => SyncStatus::class,
            'total_minor' => 'integer',
            'subtotal_minor' => 'integer',
            'shipping_cost_minor' => 'integer',
            'tax_amount_minor' => 'integer',
            'discount_minor' => 'integer',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'extra_attributes' => 'array',
            'source_updated_at' => 'datetime',
            'placed_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
