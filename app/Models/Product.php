<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'is_active',
        'is_featured',
        'stock_status',
        'meta_title',
        'meta_description',
    ];

    /**
     * تحويل أنواع البيانات (Attribute Casting).
     */
    protected $casts = [
        'price'       => 'decimal:2',
        'sale_price'  => 'decimal:2',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * القسم الرئيسي الذي ينتمي إليه المنتج.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * العلامة التجارية المرتبطة بالمنتج.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * المتغيرات أو الخصائص الفرعية للمنتج (مثل المقاسات والألوان).
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /* =========================================================================
     | النطاقات المحلية (Query Scopes)
     | ========================================================================= */

    /**
     * Scope للمنتجات المفعّلة فقط.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope للمنتجات المميزة (Featured).
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope للمنتجات المتوفرة في المخزن.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }
}
