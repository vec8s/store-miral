<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\CMS\Enums\MenuItemType;
use App\Domains\CMS\Models\Menu;
use App\Domains\CMS\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'title' => $this->faker->words(2, true),
            'url' => $this->faker->optional(0.5)->url(),
            'type' => MenuItemType::Link,
            'reference_id' => null,
            'target' => '_self',
            'icon' => null,
            'css_class' => null,
            'description' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function external(): static
    {
        return $this->state(fn () => [
            'type' => MenuItemType::External,
            'url' => $this->faker->url(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function forMenu(Menu $menu): static
    {
        return $this->state(fn () => ['menu_id' => $menu->id]);
    }

    public function childOf(MenuItem $parent): static
    {
        return $this->state(fn () => ['parent_id' => $parent->id]);
    }
}
