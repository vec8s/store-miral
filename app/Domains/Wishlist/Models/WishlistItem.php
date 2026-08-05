<?php

declare(strict_types=1);

namespace App\Domains\Wishlist\Models;

use App\Domains\Catalog\Models\Product;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'product_id',
        'note',
        'price_alert_minor',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'price_alert_minor' => 'integer',
        ];
    }

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
