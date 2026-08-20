<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Health\Checks\CacheHealthCheck;
use App\Shared\Health\HealthReport;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_cache_roundtrip_succeeds(): void
    {
        $report = (new CacheHealthCheck())->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('cache', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_failed_when_store_is_invalid(): void
    {
        $report = (new CacheHealthCheck('bogus-store'))->check();

        $this->assertTrue($report->isFailed());
        $this->assertSame('cache', $report->name());
        $this->assertNotNull($report->error());
    }

    public function test_cleans_up_probe_key_after_check(): void
    {
        (new CacheHealthCheck())->check();

        $this->assertNull(Cache::get('health:cache:probe'));
    }
}