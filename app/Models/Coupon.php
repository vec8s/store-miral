<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'max_uses',
        'max_uses_per_user',
        'uses_count',
        'applies_to',
        'description',
        'banner_image',
        'user_id',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value'               => 'decimal:2',
        'min_order_amount'    => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses'            => 'integer',
        'max_uses_per_user'   => 'integer',
        'uses_count'          => 'integer',
        'applies_to'          => 'array',
        'is_active'           => 'boolean',
        'starts_at'           => 'datetime',
        'expires_at'          => 'datetime',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * سجلات استخدام الكوبون.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * العميل المخصص له الكوبون (إذا كان كوبوناً شخصياً).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* =========================================================================
     | Scopes & Helpers
     | ========================================================================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * التحقق من صلاحية الكوبون للاستخدام.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
