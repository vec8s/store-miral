<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Blog\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PostCategory>
 */
class PostCategoryFactory extends Factory
{
    protected $model = PostCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            "name" => ucwords($name),
            "slug" => Str::slug($name) . "-" . $this->faker->unique()->numberBetween(1, 99999),
            "description" => $this->faker->optional(0.7)->sentence(),
            "sort_order" => $this->faker->numberBetween(0, 100),
            "parent_id" => null,
        ];
    }

    public function childOf(PostCategory $parent): static
    {
        return $this->state(fn () => ["parent_id" => $parent->id]);
    }
}
