<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'salla_id' => 'cat-' . $this->faker->unique()->uuid(),
            'parent_id' => null,
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 99999),
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(600, 600),
            'sort_order' => 0,
            'is_visible' => true,
            'source_updated_at' => now()->toIso8601String(),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }
}
