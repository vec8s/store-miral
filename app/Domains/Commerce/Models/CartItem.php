<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductVariant;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'salla_connection_id',
        'salla_product_id',
        'salla_variant_id',
        'quantity',
        'options',
        'customization',
        'price_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'options' => 'array',
            'customization' => 'array',
            'price_snapshot' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
