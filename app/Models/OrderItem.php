<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'sku',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    /**
     * تحويل أنواع البيانات (Attribute Casting).
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * الطلب الذي ينتمي إليه البند.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * المنتج الاصلي (في حال وجد).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}