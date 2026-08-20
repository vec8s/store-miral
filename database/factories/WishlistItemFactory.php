<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\Product;
use App\Domains\Wishlist\Models\Wishlist;
use App\Domains\Wishlist\Models\WishlistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WishlistItem>
 */
class WishlistItemFactory extends Factory
{
    protected $model = WishlistItem::class;

    public function definition(): array
    {
        return [
            'wishlist_id' => Wishlist::factory(),
            'product_id' => Product::factory(),
            'note' => $this->faker->optional(0.3)->sentence(),
            'price_alert_minor' => $this->faker->optional(0.2)->numberBetween(1000, 50000),
            'currency' => 'SAR',
        ];
    }

    public function forWishlist(Wishlist $wishlist): static
    {
        return $this->state(fn () => ['wishlist_id' => $wishlist->id]);
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn () => [
            'product_id' => $product->id,
            'currency' => $product->currency,
        ]);
    }

    public function withPriceAlert(int $minor): static
    {
        return $this->state(fn () => ['price_alert_minor' => $minor]);
    }
}
