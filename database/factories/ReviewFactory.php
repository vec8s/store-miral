<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\Product;
use App\Domains\Identity\Models\User;
use App\Domains\Reviews\Enums\ReviewStatus;
use App\Domains\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'rating' => $this->faker->numberBetween(3, 5),
            'title' => $this->faker->words(4, true),
            'content' => $this->faker->paragraph(),
            'status' => ReviewStatus::Approved,
            'is_verified_purchase' => $this->faker->boolean(70),
            'helpful_count' => 0,
            'unhelpful_count' => 0,
            'approved_at' => now(),
        ];
    }
}
