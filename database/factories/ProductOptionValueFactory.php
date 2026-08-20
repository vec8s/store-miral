<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\ProductOption;
use App\Domains\Catalog\Models\ProductOptionValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOptionValue>
 */
class ProductOptionValueFactory extends Factory
{
    protected $model = ProductOptionValue::class;

    public function definition(): array
    {
        return [
            'product_option_id' => ProductOption::factory(),
            'value' => $this->faker->word(),
            'display_value' => null,
            'color_code' => null,
            'image_url' => null,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }

    public function color(?string $colorName = null, ?string $hex = null): static
    {
        $colors = [
            'red' => '#dc2626',
            'blue' => '#2563eb',
            'green' => '#16a34a',
            'black' => '#000000',
            'white' => '#ffffff',
            'yellow' => '#eab308',
            'pink' => '#ec4899',
            'purple' => '#9333ea',
        ];
        $name = $colorName ?? $this->faker->randomElement(array_keys($colors));
        $code = $hex ?? $colors[$name];

        return $this->state(fn () => [
            'value' => $name,
            'display_value' => ucfirst($name),
            'color_code' => $code,
        ]);
    }

    public function size(?string $size = null): static
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        $value = $size ?? $this->faker->randomElement($sizes);

        return $this->state(fn () => [
            'value' => $value,
            'display_value' => $value,
        ]);
    }

    public function forOption(ProductOption $option): static
    {
        return $this->state(fn () => ['product_option_id' => $option->id]);
    }
}
