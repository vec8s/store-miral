<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Webhook\Enums\WebhookEventStatus;
use App\Domains\Webhook\Enums\WebhookEventType;
use App\Domains\Webhook\Models\WebhookEndpoint;
use App\Domains\Webhook\Models\WebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WebhookEvent>
 */
class WebhookEventFactory extends Factory
{
    protected $model = WebhookEvent::class;

    public function definition(): array
    {
        return [
            'webhook_endpoint_id' => WebhookEndpoint::factory(),
            'event_id' => 'evt-'.$this->faker->unique()->uuid(),
            'event_type' => $this->faker->randomElement(WebhookEventType::cases()),
            'resource_type' => 'product',
            'resource_id' => (string) $this->faker->numberBetween(100000, 999999),
            'payload' => [
                'id' => $this->faker->numberBetween(100000, 999999),
                'name' => $this->faker->words(3, true),
                'event' => $this->faker->word(),
            ],
            'headers' => [
                'X-Salla-Signature' => $this->faker->sha256(),
                'X-Salla-Timestamp' => (string) now()->timestamp,
            ],
            'source_ip' => $this->faker->ipv4(),
            'signature' => $this->faker->sha256(),
            'status' => WebhookEventStatus::Received,
            'attempts' => 0,
            'response_status' => null,
            'response_body' => null,
            'error_message' => null,
            'received_at' => now(),
            'processed_at' => null,
            'failed_at' => null,
            'processing_log' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => ['status' => WebhookEventStatus::Verified]);
    }

    public function processed(): static
    {
        return $this->state(fn () => [
            'status' => WebhookEventStatus::Processed,
            'processed_at' => now(),
            'response_status' => 200,
        ]);
    }

    public function failed(?string $reason = null): static
    {
        return $this->state(fn () => [
            'status' => WebhookEventStatus::Failed,
            'failed_at' => now(),
            'attempts' => 1,
            'error_message' => $reason ?? $this->faker->sentence(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => WebhookEventStatus::Rejected]);
    }

    public function ofType(WebhookEventType $type): static
    {
        return $this->state(fn () => [
            'event_type' => $type,
            'resource_type' => $type->resourceType()->value,
        ]);
    }

    public function forEndpoint(WebhookEndpoint $endpoint): static
    {
        return $this->state(fn () => ['webhook_endpoint_id' => $endpoint->id]);
    }
}
