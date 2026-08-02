<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'product_id',
        'variant_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'low_stock_notified',
        'warehouse_location',
    ];

    protected $casts = [
        'quantity'            => 'integer',
        'reserved_quantity'   => 'integer',
        'low_stock_threshold' => 'integer',
        'low_stock_notified'  => 'boolean',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * المنتج الرئيسي المرتبط بهذا المخزون.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * متغير المنتج (إن وجد).
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * سجل سجلات وتتبعات حركة المخزون (In, Out, Reserve, etc).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /* =========================================================================
     | الدوال المساعدة (Helper Accessors)
     | ========================================================================= */

    /**
     * الكمية المتاحة للبيع حالياً (المخزون الكلي مطروحاً منه المحجوز للطلبات قائمة الدفع).
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }
}