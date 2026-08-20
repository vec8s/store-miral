<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Settings\Models\SallaToken;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SallaToken>
 */
class SallaTokenFactory extends Factory
{
    protected $model = SallaToken::class;

    public function definition(): array
    {
        return [
            'merchant_id' => 'merchant-'.$this->faker->unique()->uuid(),
            'access_token' => encrypt('fake-access-'.$this->faker->uuid()),
            'refresh_token' => encrypt('fake-refresh-'.$this->faker->uuid()),
            'token_type' => 'Bearer',
            'scope' => 'read:products write:products read:orders',
            'access_token_expires_at' => now()->addHours(12),
            'refresh_token_expires_at' => now()->addDays(30),
            'metadata' => ['environment' => 'test'],
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'access_token_expires_at' => now()->subHour(),
        ]);
    }

    public function needsRefresh(): static
    {
        return $this->state(fn () => [
            'access_token_expires_at' => now()->addMinutes(5),
        ]);
    }
}
