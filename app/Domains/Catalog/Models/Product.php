<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\ProductStatus;
use App\Domains\Catalog\Enums\ProductVisibility;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'salla_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'sku',
        'mpn',
        'barcode',
        'status',
        'visibility',
        'is_featured',
        'is_on_sale',
        'is_free_shipping',
        'requires_shipping',
        'is_taxable',
        'price_minor',
        'sale_price_minor',
        'currency',
        'quantity',
        'low_stock_threshold',
        'weight',
        'weight_unit',
        'dimensions',
        'main_image_url',
        'view_count',
        'sold_count',
        'average_rating',
        'reviews_count',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'source_updated_at',
        'synced_at',
        'sync_status',
        'sync_error',
        'extra_attributes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'visibility' => ProductVisibility::class,
            'sync_status' => SyncStatus::class,
            'is_featured' => 'boolean',
            'is_on_sale' => 'boolean',
            'is_free_shipping' => 'boolean',
            'requires_shipping' => 'boolean',
            'is_taxable' => 'boolean',
            'price_minor' => 'integer',
            'sale_price_minor' => 'integer',
            'quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'weight' => 'decimal:3',
            'dimensions' => 'array',
            'view_count' => 'integer',
            'sold_count' => 'integer',
            'average_rating' => 'decimal:2',
            'reviews_count' => 'integer',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
            'extra_attributes' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(\App\Domains\Media\Models\Media::class, 'mediable');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domains\Reviews\Models\Review::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('visibility', ProductVisibility::Visible->value);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::Active->value);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
