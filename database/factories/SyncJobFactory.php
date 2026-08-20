<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Sync\Enums\SyncStatus;
use App\Domains\Sync\Enums\SyncTrigger;
use App\Domains\Sync\Enums\SyncType;
use App\Domains\Sync\Models\SyncJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SyncJob>
 */
class SyncJobFactory extends Factory
{
    protected $model = SyncJob::class;

    public function definition(): array
    {
        $total = $this->faker->numberBetween(10, 500);
        $processed = $this->faker->numberBetween(0, $total);

        return [
            'reference' => Str::uuid()->toString(),
            'resource_type' => 'product',
            'sync_type' => $this->faker->randomElement(SyncType::cases()),
            'status' => SyncStatus::Pending,
            'total_items' => $total,
            'processed_items' => $processed,
            'successful_items' => $processed,
            'failed_items' => 0,
            'batch_size' => 100,
            'attempts' => 0,
            'max_attempts' => 5,
            'filters' => null,
            'metadata' => null,
            'error_message' => null,
            'failure_context' => null,
            'started_at' => null,
            'completed_at' => null,
            'failed_at' => null,
            'next_retry_at' => null,
            'duration_seconds' => null,
            'triggered_by_id' => null,
            'triggered_by_type' => $this->faker->randomElement(SyncTrigger::cases()),
            'triggered_by_source' => $this->faker->randomElement(['cli', 'webhook', 'api', 'scheduler']),
        ];
    }

    public function running(): static
    {
        return $this->state(fn () => [
            'status' => SyncStatus::Running,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => SyncStatus::Completed,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'processed_items' => $this->faker->numberBetween(10, 500),
            'successful_items' => fn (array $attrs) => $attrs['processed_items'],
        ]);
    }

    public function failed(?string $reason = null): static
    {
        return $this->state(fn () => [
            'status' => SyncStatus::Failed,
            'failed_at' => now(),
            'error_message' => $reason ?? $this->faker->sentence(),
            'attempts' => 1,
        ]);
    }

    public function full(): static
    {
        return $this->state(fn () => ['sync_type' => SyncType::Full]);
    }

    public function incremental(): static
    {
        return $this->state(fn () => ['sync_type' => SyncType::Incremental]);
    }

    public function webhook(): static
    {
        return $this->state(fn () => ['sync_type' => SyncType::Webhook]);
    }

    public function triggeredBy(User $user): static
    {
        return $this->state(fn () => [
            'triggered_by_id' => $user->id,
            'triggered_by_type' => SyncTrigger::Manual,
        ]);
    }
}
