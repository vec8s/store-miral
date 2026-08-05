<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Wishlist\Enums\WishlistVisibility;
use App\Domains\Wishlist\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    protected $model = Wishlist::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'My Wishlist',
            'visibility' => WishlistVisibility::Private,
            'description' => null,
        ];
    }
}
