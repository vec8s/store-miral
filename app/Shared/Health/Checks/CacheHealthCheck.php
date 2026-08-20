<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class CacheHealthCheck implements HealthCheck
{
    private const PROBE_KEY = 'health:cache:probe';

    private const PROBE_VALUE = 'ok';

    public function __construct(
        private readonly ?string $store = null,
    ) {}

    public function name(): string
    {
        return 'cache';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            $cache = Cache::store($this->store ?? (string) config('cache.default'));

            $cache->put(self::PROBE_KEY, self::PROBE_VALUE, 10);
            $value = $cache->get(self::PROBE_KEY);
            $cache->forget(self::PROBE_KEY);

            if ($value !== self::PROBE_VALUE) {
                return HealthReport::failed($this->name(), 'Cache read-back mismatch', $this->elapsed($start));
            }

            return HealthReport::ok($this->name(), 'Cache store is healthy', $this->elapsed($start));
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}