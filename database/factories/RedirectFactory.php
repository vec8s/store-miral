<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\CMS\Enums\RedirectStatusCode;
use App\Domains\CMS\Models\Redirect;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Redirect>
 */
class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition(): array
    {
        return [
            'source_url' => '/'.$this->faker->unique()->slug(2),
            'target_url' => '/'.$this->faker->slug(2),
            'status_code' => RedirectStatusCode::MovedPermanently,
            'is_active' => true,
            'hit_count' => 0,
            'last_hit_at' => null,
            'notes' => null,
        ];
    }

    public function permanent(): static
    {
        return $this->state(fn () => ['status_code' => RedirectStatusCode::MovedPermanently]);
    }

    public function temporary(): static
    {
        return $this->state(fn () => ['status_code' => RedirectStatusCode::Found]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
