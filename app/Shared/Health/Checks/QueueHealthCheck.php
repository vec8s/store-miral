<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Support\Facades\Queue;
use Throwable;

final class QueueHealthCheck implements HealthCheck
{
    public function __construct(
        private readonly ?string $connection = null,
    ) {}

    public function name(): string
    {
        return 'queue';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            Queue::connection($this->connection ?? (string) config('queue.default'));

            return HealthReport::ok($this->name(), 'Queue connection is available', $this->elapsed($start));
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}