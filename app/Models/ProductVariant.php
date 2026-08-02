<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة.
     */
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'is_active',
        'label',
    ];

    /**
     * تحويل أنواع البيانات.
     */
    protected $casts = [
        'price'      => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * المنتج الأساسي الذي ينتمي إليه هذا المتغير.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * سجل المخزون المرتبط بهذا المتغير.
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'product_variant_id');
    }
}
