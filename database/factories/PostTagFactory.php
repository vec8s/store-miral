<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Blog\Models\PostTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PostTag>
 */
class PostTagFactory extends Factory
{
    protected $model = PostTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            "name" => $name,
            "slug" => Str::slug($name) . "-" . $this->faker->unique()->numberBetween(1, 99999),
            "description" => null,
            "posts_count" => 0,
        ];
    }

    public function popular(int $count = 50): static
    {
        return $this->state(fn () => ["posts_count" => $count]);
    }
}
