<?php

declare(strict_types=1);

namespace Tests\Unit\Health;

use App\Shared\Health\HealthReport;
use PHPUnit\Framework\TestCase;

class HealthReportTest extends TestCase
{
    public function test_ok_report_is_healthy(): void
    {
        $report = HealthReport::ok('database', 'Connected');

        $this->assertTrue($report->isOk());
        $this->assertFalse($report->isFailed());
        $this->assertSame('database', $report->name());
        $this->assertSame('Connected', $report->message());
        $this->assertNull($report->error());
    }

    public function test_failed_report_is_not_healthy(): void
    {
        $report = HealthReport::failed('database', 'Connection refused');

        $this->assertTrue($report->isFailed());
        $this->assertFalse($report->isOk());
        $this->assertSame('database', $report->name());
        $this->assertSame('Connection refused', $report->error());
    }

    public function test_reports_duration_is_non_negative(): void
    {
        $report = HealthReport::ok('cache', 'Warm');

        $this->assertGreaterThanOrEqual(0.0, $report->durationMs());
    }

    public function test_to_array_shapes_response_payload(): void
    {
        $report = HealthReport::ok('database', 'Connected');

        $array = $report->toArray();

        $this->assertSame('ok', $array['status']);
        $this->assertSame('database', $array['name']);
        $this->assertSame('Connected', $array['message']);
        $this->assertArrayHasKey('duration_ms', $array);
    }

    public function test_failed_to_array_includes_error(): void
    {
        $report = HealthReport::failed('database', 'Timeout');

        $array = $report->toArray();

        $this->assertSame('failed', $array['status']);
        $this->assertSame('Timeout', $array['error']);
    }
}