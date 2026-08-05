<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->slug(3),
            'name' => $this->faker->words(2, true),
            'group' => 'general',
            'description' => $this->faker->sentence(),
            'is_protected' => false,
        ];
    }
}
