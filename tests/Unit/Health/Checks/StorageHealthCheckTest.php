<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Health\Checks\StorageHealthCheck;
use App\Shared\Health\HealthReport;
use Tests\TestCase;

class StorageHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_storage_roundtrip_succeeds(): void
    {
        $report = (new StorageHealthCheck())->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('storage', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_failed_when_disk_is_invalid(): void
    {
        $report = (new StorageHealthCheck('bogus-disk'))->check();

        $this->assertTrue($report->isFailed());
        $this->assertSame('storage', $report->name());
        $this->assertNotNull($report->error());
    }
}