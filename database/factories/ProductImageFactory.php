<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        $url = $this->faker->imageUrl(800, 800);

        return [
            "product_id" => Product::factory(),
            "url" => $url,
            "thumbnail_url" => $this->faker->imageUrl(150, 150),
            "medium_url" => $this->faker->imageUrl(400, 400),
            "large_url" => $this->faker->imageUrl(1200, 1200),
            "alt_text" => $this->faker->words(3, true),
            "width" => 800,
            "height" => 800,
            "is_main" => false,
            "sort_order" => $this->faker->numberBetween(0, 100),
            "source_updated_at" => now()->toIso8601String(),
            "synced_at" => now(),
        ];
    }

    public function main(): static
    {
        return $this->state(fn () => ["is_main" => true]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn () => ["product_id" => $product->id]);
    }
}
