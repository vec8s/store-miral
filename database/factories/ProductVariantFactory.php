<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        $price = $this->faker->numberBetween(1000, 100000);

        return [
            "salla_id" => "var-" . $this->faker->unique()->uuid(),
            "product_id" => Product::factory(),
            "name" => $this->faker->words(2, true),
            "sku" => strtoupper($this->faker->unique()->bothify("VAR-####-??")),
            "barcode" => $this->faker->optional(0.5)->ean13(),
            "price_minor" => $price,
            "sale_price_minor" => null,
            "currency" => "SAR",
            "quantity" => $this->faker->numberBetween(0, 200),
            "weight" => $this->faker->randomFloat(3, 0.1, 50),
            "is_default" => false,
            "is_available" => true,
            "source_updated_at" => now()->toIso8601String(),
            "synced_at" => now(),
            "sync_status" => SyncStatus::Synced->value,
            "sync_error" => null,
            "extra_attributes" => null,
        ];
    }

    public function default(): static
    {
        return $this->state(fn () => ["is_default" => true]);
    }

    public function unavailable(): static
    {
        return $this->state(fn () => [
            "is_available" => false,
            "quantity" => 0,
        ]);
    }

    public function onSale(): static
    {
        return $this->state(function () {
            $price = $this->faker->numberBetween(1000, 100000);
            return [
                "price_minor" => $price,
                "sale_price_minor" => (int) ($price * 0.8),
            ];
        });
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn () => ["product_id" => $product->id]);
    }
}
