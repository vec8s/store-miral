<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAddress extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالنموذج.
     */
    protected $table = 'user_addresses';

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     */
    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'phone',
        'country',
        'state',
        'city',
        'address_line_1',
        'address_line_2',
        'postal_code',
        'is_default_shipping',
        'is_default_billing',
    ];

    /**
     * تحويل أنواع البيانات (Attribute Casting).
     */
    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing'  => 'boolean',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * العميل صاحب العنوان.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الطلبات التي تستخدم هذا العنوان كعنوان شحن.
     */
    public function shippingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    /**
     * الطلبات التي تستخدم هذا العنوان كعنوان فواتير.
     */
    public function billingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    /* =========================================================================
     | النطاقات المحلية (Query Scopes)
     | ========================================================================= */

    /**
     * Scope لجلب عناوين الشحن الافتراضية.
     */
    public function scopeDefaultShipping($query)
    {
        return $query->where('is_default_shipping', true);
    }

    /**
     * Scope لجلب عناوين الفواتير الافتراضية.
     */
    public function scopeDefaultBilling($query)
    {
        return $query->where('is_default_billing', true);
    }
}
