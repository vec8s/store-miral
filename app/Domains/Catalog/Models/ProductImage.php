<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
        'thumbnail_url',
        'medium_url',
        'large_url',
        'alt_text',
        'width',
        'height',
        'is_main',
        'sort_order',
        'source_updated_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'is_main' => 'boolean',
            'sort_order' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
