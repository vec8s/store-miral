<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\CouponDiscountType;
use App\Domains\Catalog\Enums\CouponStatus;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'code',
        'name',
        'discount_type',
        'discount_minor',
        'currency',
        'discount_percentage',
        'min_order_minor',
        'min_order_currency',
        'usage_limit',
        'usage_limit_per_customer',
        'usage_count',
        'starts_at',
        'expires_at',
        'status',
        'source_updated_at',
        'synced_at',
        'sync_status',
        'extra_attributes',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => CouponDiscountType::class,
            'status' => CouponStatus::class,
            'sync_status' => SyncStatus::class,
            'discount_minor' => 'integer',
            'discount_percentage' => 'integer',
            'min_order_minor' => 'integer',
            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'usage_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
            'extra_attributes' => 'array',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', CouponStatus::Active->value);
    }
}
