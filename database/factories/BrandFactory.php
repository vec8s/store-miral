<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'salla_id' => 'brand-'.$this->faker->unique()->uuid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'logo_url' => $this->faker->imageUrl(300, 300),
            'description' => $this->faker->paragraph(),
            'is_visible' => true,
            'sort_order' => 0,
            'source_updated_at' => now()->toIso8601String(),
            'synced_at' => now(),
            'sync_status' => SyncStatus::Synced->value,
        ];
    }
}
