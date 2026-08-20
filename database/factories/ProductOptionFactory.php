<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\ProductOptionType;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOption>
 */
class ProductOptionFactory extends Factory
{
    protected $model = ProductOption::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->randomElement(['Color', 'Size', 'Material', 'Style']),
            'display_name' => null,
            'type' => $this->faker->randomElement(ProductOptionType::cases()),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_required' => true,
        ];
    }

    public function color(): static
    {
        return $this->state(fn () => [
            'name' => 'Color',
            'type' => ProductOptionType::Color,
        ]);
    }

    public function size(): static
    {
        return $this->state(fn () => [
            'name' => 'Size',
            'type' => ProductOptionType::Size,
        ]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn () => ['product_id' => $product->id]);
    }
}
