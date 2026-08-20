<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Webhook\Enums\SignatureAlgorithm;
use App\Domains\Webhook\Models\WebhookEndpoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WebhookEndpoint>
 */
class WebhookEndpointFactory extends Factory
{
    protected $model = WebhookEndpoint::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'url' => 'https://example.test/webhooks/'.$this->faker->slug(1),
            'secret' => encrypt(Str::random(64)),
            'algorithm' => SignatureAlgorithm::HmacSha256,
            'subscribed_events' => ['*'],
            'is_active' => true,
            'timeout_seconds' => 30,
            'max_retries' => 5,
            'description' => $this->faker->optional(0.5)->sentence(),
            'last_triggered_at' => null,
            'total_deliveries' => 0,
            'failed_deliveries' => 0,
            'created_by_id' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function withEvents(array $events): static
    {
        return $this->state(fn () => ['subscribed_events' => $events]);
    }

    public function sha256(): static
    {
        return $this->state(fn () => ['algorithm' => SignatureAlgorithm::Sha256]);
    }
}
