<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'currency',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'shipping_address_id',
        'billing_address_id',
        'notes',
    ];

    /**
     * تحويل أنواع البيانات (Attribute Casting).
     */
    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_cost'   => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * العميل صاحب الطلب.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * بنود/منتجات الطلب.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * شحنة الطلب (Order -> Shipment).
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * عنوان الشحن المرتبط بالطلب.
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    /**
     * عنوان الفواتير المرتبط بالطلب.
     */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    /* =========================================================================
     | النطاقات المحلية (Query Scopes)
     | ========================================================================= */

    /**
     * Scope لجلب الطلبات المعلقة.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope لجلب طلبات عميل معين.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}