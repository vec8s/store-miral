<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductVariant;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->numberBetween(1000, 50000);
        $total = $unitPrice * $quantity;

        return [
            'salla_id' => 'oi-'.$this->faker->unique()->uuid(),
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => ProductVariant::factory(),
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->bothify('SKU-####')),
            'quantity' => $quantity,
            'unit_price_minor' => $unitPrice,
            'total_minor' => $total,
            'options' => null,
        ];
    }

    public function forOrder(Order $order): static
    {
        return $this->state(fn () => ['order_id' => $order->id]);
    }

    public function forProduct(Product $product, ?ProductVariant $variant = null): static
    {
        return $this->state(fn () => [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'name' => $product->name,
        ]);
    }
}
