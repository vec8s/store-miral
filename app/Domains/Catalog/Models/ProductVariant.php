<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'product_id',
        'name',
        'sku',
        'barcode',
        'price_minor',
        'sale_price_minor',
        'currency',
        'quantity',
        'weight',
        'is_default',
        'is_available',
        'source_updated_at',
        'synced_at',
        'sync_status',
        'sync_error',
        'extra_attributes',
    ];

    protected function casts(): array
    {
        return [
            'sync_status' => SyncStatus::class,
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'quantity' => 'integer',
            'weight' => 'decimal:3',
            'is_default' => 'boolean',
            'is_available' => 'boolean',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
            'extra_attributes' => 'array',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
