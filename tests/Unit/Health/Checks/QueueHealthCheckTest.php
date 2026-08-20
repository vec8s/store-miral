<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Health\Checks\QueueHealthCheck;
use App\Shared\Health\HealthReport;
use Tests\TestCase;

class QueueHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_queue_connection_is_available(): void
    {
        $report = (new QueueHealthCheck())->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('queue', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_failed_when_connection_is_invalid(): void
    {
        $report = (new QueueHealthCheck('bogus-connection'))->check();

        $this->assertTrue($report->isFailed());
        $this->assertSame('queue', $report->name());
        $this->assertNotNull($report->error());
    }
}