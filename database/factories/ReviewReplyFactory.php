<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Reviews\Models\Review;
use App\Domains\Reviews\Models\ReviewReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewReply>
 */
class ReviewReplyFactory extends Factory
{
    protected $model = ReviewReply::class;

    public function definition(): array
    {
        return [
            "review_id" => Review::factory(),
            "user_id" => User::factory(),
            "content" => $this->faker->paragraph(),
        ];
    }

    public function forReview(Review $review): static
    {
        return $this->state(fn () => ["review_id" => $review->id]);
    }

    public function fromUser(User $user): static
    {
        return $this->state(fn () => ["user_id" => $user->id]);
    }
}
