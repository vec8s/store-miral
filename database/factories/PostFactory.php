<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Blog\Enums\PostLayout;
use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Models\PostCategory;
use App\Domains\Identity\Models\User;
use App\Shared\Enums\PublicationStatus;
use App\Shared\Enums\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(5);
        $content = $this->faker->paragraphs(8, true);

        return [
            "post_category_id" => PostCategory::inRandomOrder()->first()?->id,
            "author_id" => User::factory(),
            "title" => rtrim($title, "."),
            "slug" => Str::slug($title) . "-" . $this->faker->unique()->numberBetween(1, 99999),
            "excerpt" => $this->faker->paragraph(),
            "content" => $content,
            "featured_image_url" => $this->faker->imageUrl(1200, 600),
            "status" => PublicationStatus::Draft,
            "visibility" => Visibility::Public,
            "password" => null,
            "is_featured" => $this->faker->boolean(15),
            "allow_comments" => true,
            "view_count" => $this->faker->numberBetween(0, 10000),
            "reading_time_minutes" => max(1, (int) ceil(str_word_count($content) / 200)),
            "published_at" => null,
            "scheduled_at" => null,
            "layout" => PostLayout::Standard,
            "seo_title" => null,
            "seo_description" => null,
            "seo_keywords" => null,
            "canonical_url" => null,
            "og_image_url" => null,
            "robots" => "index,follow",
            "custom_fields" => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            "status" => PublicationStatus::Published,
            "published_at" => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ["status" => PublicationStatus::Draft]);
    }

    public function scheduled(\DateTimeInterface $at): static
    {
        return $this->state(fn () => [
            "status" => PublicationStatus::Scheduled,
            "scheduled_at" => $at,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ["status" => PublicationStatus::Archived]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ["is_featured" => true]);
    }

    public function byAuthor(User $user): static
    {
        return $this->state(fn () => ["author_id" => $user->id]);
    }

    public function inCategory(PostCategory $category): static
    {
        return $this->state(fn () => ["post_category_id" => $category->id]);
    }

    public function popular(int $views = 5000): static
    {
        return $this->state(fn () => [
            "view_count" => $views,
            "status" => PublicationStatus::Published,
            "published_at" => now()->subDays(rand(7, 90)),
        ]);
    }
}
