<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\CMS\Enums\PageTemplate;
use App\Domains\CMS\Models\Page;
use App\Domains\Identity\Models\User;
use App\Shared\Enums\PublicationStatus;
use App\Shared\Enums\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->paragraph(),
            'status' => PublicationStatus::Draft,
            'visibility' => Visibility::Public,
            'template' => PageTemplate::Default,
            'layout' => 'default',
            'view_count' => 0,
            'robots' => 'index,follow',
            'author_id' => User::factory(),
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => PublicationStatus::Published, 'published_at' => now()]);
    }
}
