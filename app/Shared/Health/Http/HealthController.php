<?php

declare(strict_types=1);

namespace App\Shared\Health\Http;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exposes the application health at GET /up.
 *
 * Without query parameters it returns a minimal payload suitable for load
 * balancers and uptime monitors:
 *
 *   {"status":"ok"}
 *
 * With ?details=1 it includes per-check results:
 *
 *   {"status":"ok","checks":{"database":{...},"cache":{...}}}
 */
final class HealthController
{
    /**
     * @param  array<int, HealthCheck>  $checks
     */
    public function __construct(
        private readonly array $checks,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var array<string, HealthReport> $reports */
        $reports = [];

        foreach ($this->checks as $check) {
            $reports[$check->name()] = $check->check();
        }

        $healthy = ! in_array(false, array_map(
            static fn (HealthReport $report): bool => $report->isOk(),
            $reports,
        ), true);

        $payload = ['status' => $healthy ? 'ok' : 'degraded'];

        if ($request->boolean('details')) {
            $payload['checks'] = array_map(
                static fn (HealthReport $report): array => $report->toArray(),
                $reports,
            );
        }

        return response()->json($payload, $healthy ? 200 : 503);
    }
}