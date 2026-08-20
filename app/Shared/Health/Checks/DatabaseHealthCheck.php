<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DatabaseHealthCheck implements HealthCheck
{
    public function __construct(
        private readonly ?string $connection = null,
    ) {}

    public function name(): string
    {
        return 'database';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            DB::connection($this->connection ?? (string) config('database.default'))
                ->select('select 1');

            return HealthReport::ok($this->name(), 'Database connection is healthy', $this->elapsed($start));
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}