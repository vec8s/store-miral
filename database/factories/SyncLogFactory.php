<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Sync\Enums\SyncAction;
use App\Domains\Sync\Models\SyncJob;
use App\Domains\Sync\Models\SyncLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SyncLog>
 */
class SyncLogFactory extends Factory
{
    protected $model = SyncLog::class;

    public function definition(): array
    {
        return [
            'sync_job_id' => SyncJob::factory(),
            'resource_type' => 'product',
            'resource_id' => (string) $this->faker->numberBetween(100000, 999999),
            'salla_id' => 'prod-'.$this->faker->uuid(),
            'action' => $this->faker->randomElement([
                SyncAction::Create,
                SyncAction::Update,
            ]),
            'status' => 'success',
            'attempt_number' => 1,
            'duration_ms' => $this->faker->numberBetween(50, 1000),
            'before_state' => null,
            'after_state' => null,
            'error_message' => null,
            'error_context' => null,
            'source_ip' => $this->faker->ipv4(),
            'occurred_at' => now(),
        ];
    }

    public function create(): static
    {
        return $this->state(fn () => ['action' => SyncAction::Create]);
    }

    public function update(): static
    {
        return $this->state(fn () => ['action' => SyncAction::Update]);
    }

    public function delete(): static
    {
        return $this->state(fn () => ['action' => SyncAction::Delete]);
    }

    public function error(?string $message = null): static
    {
        return $this->state(fn () => [
            'action' => SyncAction::Error,
            'status' => 'failed',
            'error_message' => $message ?? $this->faker->sentence(),
        ]);
    }

    public function forJob(SyncJob $job): static
    {
        return $this->state(fn () => ['sync_job_id' => $job->id]);
    }

    public function forResource(string $type, string $id, string $sallaId): static
    {
        return $this->state(fn () => [
            'resource_type' => $type,
            'resource_id' => $id,
            'salla_id' => $sallaId,
        ]);
    }
}
