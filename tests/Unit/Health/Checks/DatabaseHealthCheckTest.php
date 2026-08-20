<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Health\Checks\DatabaseHealthCheck;
use App\Shared\Health\HealthReport;
use Tests\TestCase;

class DatabaseHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_database_responds(): void
    {
        $report = (new DatabaseHealthCheck())->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('database', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_failed_when_connection_is_invalid(): void
    {
        $report = (new DatabaseHealthCheck('bogus-connection'))->check();

        $this->assertTrue($report->isFailed());
        $this->assertSame('database', $report->name());
        $this->assertNotNull($report->error());
    }
}