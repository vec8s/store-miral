<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\CMS\Enums\MenuLocation;
use App\Domains\CMS\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'location' => $this->faker->randomElement(MenuLocation::cases()),
            'description' => $this->faker->optional(0.5)->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function inLocation(MenuLocation $location): static
    {
        return $this->state(fn () => ['location' => $location]);
    }
}
