<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\ProductOptionType;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'display_name',
        'type',
        'sort_order',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductOptionType::class,
            'sort_order' => 'integer',
            'is_required' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class)->orderBy('sort_order');
    }
}
