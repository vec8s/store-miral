<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingMethod extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     * مطابقة 100% لأعمدة جدول shipping_methods في الـ Migration.
     */
    protected $fillable = [
        'carrier_code',
        'name',
        'logo_path',
        'description',
        'calculation_rules',
        'estimated_days_min',
        'estimated_days_max',
        'price',
        'free_shipping_threshold',
        'max_weight',
        'min_order_amount',
        'is_active',
        'sort_order',
    ];

    /**
     * تحويل أنواع البيانات عند القراءة والحفظ (Attribute Casting).
     * يضمن تحويل الـ JSON إلى PHP Array تلقائياً وضبط الأنواع الرقمية والمنطقية.
     */
    protected $casts = [
        'calculation_rules'       => 'array',
        'price'                   => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'max_weight'              => 'decimal:2',
        'min_order_amount'        => 'decimal:2',
        'is_active'               => 'boolean',
        'estimated_days_min'      => 'integer',
        'estimated_days_max'      => 'integer',
        'sort_order'              => 'integer',
    ];

    /**
     * العلاقة مع الشحنات: طريقة الشحن الواحدة تمتلك عدة شحنات (Shipments).
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Scope لجلب طرق الشحن النشطة فقط والمجهزة للظهور في صفحة الدفع.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }
}