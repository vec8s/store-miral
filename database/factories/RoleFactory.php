<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->slug(3),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'level' => 100,
            'is_default' => false,
            'is_protected' => false,
        ];
    }
}
