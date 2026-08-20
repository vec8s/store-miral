<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Enums\PaymentMethod;
use App\Domains\Commerce\Enums\PaymentStatus;
use App\Domains\Commerce\Enums\ShippingStatus;
use App\Domains\Commerce\Enums\SyncStatus;
use App\Domains\Commerce\Models\Customer;
use App\Domains\Commerce\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(5000, 50000);
        $shipping = $this->faker->numberBetween(1000, 5000);
        $tax = (int) ($subtotal * 0.15);
        $discount = $this->faker->boolean(20) ? $this->faker->numberBetween(500, 2000) : 0;
        $total = $subtotal + $shipping + $tax - $discount;

        return [
            'salla_id' => 'order-'.$this->faker->unique()->uuid(),
            'customer_id' => Customer::factory(),
            'reference_id' => strtoupper($this->faker->unique()->bothify('REF-########')),
            'status' => $this->faker->randomElement(OrderStatus::cases()),
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
            'shipping_status' => $this->faker->randomElement(ShippingStatus::cases()),
            'payment_method' => $this->faker->randomElement(PaymentMethod::cases()),
            'currency' => 'SAR',
            'total_minor' => $total,
            'subtotal_minor' => $subtotal,
            'shipping_cost_minor' => $shipping,
            'tax_amount_minor' => $tax,
            'discount_minor' => $discount,
            'shipping_address' => [
                'name' => $this->faker->name(),
                'line1' => $this->faker->streetAddress(),
                'city' => $this->faker->city(),
                'country' => 'SA',
                'postal_code' => $this->faker->postcode(),
                'phone' => $this->faker->e164PhoneNumber(),
            ],
            'billing_address' => null,
            'extra_attributes' => null,
            'source_updated_at' => now()->toIso8601String(),
            'placed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
            'shipping_status' => ShippingStatus::Delivered,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => ['payment_status' => PaymentStatus::Paid]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => OrderStatus::Cancelled]);
    }

    public function forCustomer(Customer $customer): static
    {
        return $this->state(fn () => ['customer_id' => $customer->id]);
    }
}
