<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\ProductStatus;
use App\Domains\Catalog\Enums\ProductVisibility;
use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->numberBetween(1000, 100000);
        $onSale = $this->faker->boolean(30);

        return [
            'salla_id' => 'prod-'.$this->faker->unique()->uuid(),
            'category_id' => null,
            'brand_id' => null,
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'description' => $this->faker->paragraph(),
            'sku' => strtoupper(Str::random(8)),
            'status' => ProductStatus::Active,
            'visibility' => ProductVisibility::Visible,
            'is_featured' => $this->faker->boolean(20),
            'is_on_sale' => $onSale,
            'is_free_shipping' => false,
            'requires_shipping' => true,
            'is_taxable' => true,
            'price_minor' => $price,
            'sale_price_minor' => $onSale ? (int) ($price * 0.8) : null,
            'currency' => 'SAR',
            'quantity' => $this->faker->numberBetween(0, 500),
            'weight' => $this->faker->randomFloat(3, 0.1, 50),
            'weight_unit' => 'kg',
            'main_image_url' => $this->faker->imageUrl(800, 800),
            'view_count' => 0,
            'sold_count' => 0,
            'average_rating' => 0,
            'reviews_count' => 0,
            'source_updated_at' => now()->toIso8601String(),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function onSale(): static
    {
        return $this->state(fn () => ['is_on_sale' => true]);
    }
}
