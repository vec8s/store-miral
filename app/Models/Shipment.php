<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     * مطابقة تماماً لأعمدة جدول shipments في المايجريشن.
     */
    protected $fillable = [
        'order_id',
        'shipping_method_id',
        'tracking_number',
        'carrier',
        'status',
        'weight',
        'shipping_cost',
        'shipping_label_url',
        'insurance_amount',
        'signature_required',
        'shipped_at',
        'delivered_at',
    ];

    /**
     * تحويل أنواع البيانات (Attribute Casting).
     */
    protected $casts = [
        'weight'             => 'decimal:2',
        'shipping_cost'      => 'decimal:2',
        'insurance_amount'   => 'decimal:2',
        'signature_required' => 'boolean',
        'shipped_at'         => 'datetime',
        'delivered_at'       => 'datetime',
    ];

    /**
     * العلاقة العكسية: الشحنة تنتمي إلى طريقة شحن محددة (ShippingMethod).
     */
    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    /**
     * العلاقة العكسية: الشحنة تنتمي إلى طلب محدد (Order).
     * (ملاحظة: سيتم ربط موديل Order عند إنشائه في الخطوة القادمة).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}