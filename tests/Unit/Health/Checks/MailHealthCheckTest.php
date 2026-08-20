<?php

declare(strict_types=1);

namespace Tests\Unit\Health\Checks;

use App\Shared\Health\Checks\MailHealthCheck;
use App\Shared\Health\HealthReport;
use Tests\TestCase;

class MailHealthCheckTest extends TestCase
{
    public function test_reports_ok_when_mailer_is_configured(): void
    {
        config()->set('mail.default', 'smtp');
        config()->set('mail.mailers.smtp.host', 'smtp.example.com');

        $report = (new MailHealthCheck())->check();

        $this->assertTrue($report->isOk());
        $this->assertSame('mail', $report->name());
        $this->assertInstanceOf(HealthReport::class, $report);
    }

    public function test_reports_failed_when_mailer_has_no_host(): void
    {
        config()->set('mail.default', 'smtp');
        config()->set('mail.mailers.smtp.host', '');

        $report = (new MailHealthCheck())->check();

        $this->assertTrue($report->isFailed());
        $this->assertSame('mail', $report->name());
        $this->assertNotNull($report->error());
    }
}