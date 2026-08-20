<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Throwable;

final class SallaHealthCheck implements HealthCheck
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    public function name(): string
    {
        return 'salla';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            // The resolved client is either the real API client (credentials
            // configured) or the mock client (credentials absent). Resolving it
            // is enough to confirm the integration is wired up.
            $clientClass = $this->client::class;

            $isMock = str_ends_with($clientClass, 'MockSallaClient');

            return HealthReport::ok(
                $this->name(),
                $isMock ? 'Salla mock driver active (no credentials)' : 'Salla API client is wired',
                $this->elapsed($start),
            );
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}