<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        $key = $this->faker->unique()->slug(2);

        return [
            "group" => "general",
            "key" => $key,
            "value" => $this->faker->word(),
            "type" => "string",
            "is_public" => false,
        ];
    }

    public function public(): static
    {
        return $this->state(fn () => ["is_public" => true]);
    }

    public function inGroup(string $group): static
    {
        return $this->state(fn () => ["group" => $group]);
    }

    public function boolean(bool $value = true): static
    {
        return $this->state(fn () => [
            "type" => "boolean",
            "value" => $value ? "1" : "0",
        ]);
    }

    public function integer(int $value): static
    {
        return $this->state(fn () => [
            "type" => "integer",
            "value" => (string) $value,
        ]);
    }

    public function json(array $value): static
    {
        return $this->state(fn () => [
            "type" => "json",
            "value" => json_encode($value),
        ]);
    }
}
