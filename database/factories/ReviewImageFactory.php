<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Reviews\Models\Review;
use App\Domains\Reviews\Models\ReviewImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewImage>
 */
class ReviewImageFactory extends Factory
{
    protected $model = ReviewImage::class;

    public function definition(): array
    {
        return [
            "review_id" => Review::factory(),
            "url" => $this->faker->imageUrl(800, 800),
            "thumbnail_url" => $this->faker->imageUrl(200, 200),
            "sort_order" => $this->faker->numberBetween(0, 10),
        ];
    }

    public function forReview(Review $review): static
    {
        return $this->state(fn () => ["review_id" => $review->id]);
    }
}
