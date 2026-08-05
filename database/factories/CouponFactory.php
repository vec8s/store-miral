<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\CouponDiscountType;
use App\Domains\Catalog\Enums\CouponStatus;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;
    public function definition(): array
    {
        return [
            'salla_id' => 'coupon-' . $this->faker->unique()->uuid(),
            'code' => strtoupper($this->faker->unique()->bothify('CODE####')),
            'name' => $this->faker->words(3, true),
            'discount_type' => CouponDiscountType::Fixed,
            'discount_minor' => $this->faker->numberBetween(500, 5000),
            'currency' => 'SAR',
            'discount_percentage' => null,
            'min_order_minor' => null,
            'min_order_currency' => 'SAR',
            'usage_limit' => 100,
            'usage_count' => 0,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonths(3),
            'status' => CouponStatus::Active,
            'source_updated_at' => now()->toIso8601String(),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }
}
