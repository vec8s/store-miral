<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class StorageHealthCheck implements HealthCheck
{
    private const PROBE_PATH = 'health-probe.txt';

    public function __construct(
        private readonly ?string $disk = null,
    ) {}

    public function name(): string
    {
        return 'storage';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            $disk = Storage::disk($this->disk ?? (string) config('filesystems.default'));

            $disk->put(self::PROBE_PATH, 'ok');
            $value = $disk->get(self::PROBE_PATH);
            $disk->delete(self::PROBE_PATH);

            if ($value !== 'ok') {
                return HealthReport::failed($this->name(), 'Storage read-back mismatch', $this->elapsed($start));
            }

            return HealthReport::ok($this->name(), 'Storage disk is healthy', $this->elapsed($start));
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}