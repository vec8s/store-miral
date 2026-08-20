<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Health\Checks\SallaHealthCheck;
use App\Shared\Health\HealthReport;
use Tests\TestCase;

class SallaHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_credentials_are_configured(): void
    {
        config()->set('salla.driver', 'http');
        config()->set('salla.client_id', 'real-client');
        config()->set('salla.client_secret', 'real-secret');

        $report = (new SallaHealthCheck($this->app->make(SallaClientContract::class)))->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('salla', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_ok_when_mock_driver_is_active(): void
    {
        config()->set('salla.driver', 'auto');
        config()->set('salla.client_id', '');
        config()->set('salla.client_secret', '');

        $report = (new SallaHealthCheck($this->app->make(SallaClientContract::class)))->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('salla', $report->name());
    }
}