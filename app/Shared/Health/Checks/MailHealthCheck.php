<?php

declare(strict_types=1);

namespace App\Shared\Health\Checks;

use App\Shared\Health\HealthCheck;
use App\Shared\Health\HealthReport;
use Throwable;

final class MailHealthCheck implements HealthCheck
{
    public function __construct(
        private readonly ?string $mailer = null,
    ) {}

    public function name(): string
    {
        return 'mail';
    }

    public function check(): HealthReport
    {
        $start = microtime(true);

        try {
            $mailer = $this->mailer ?? (string) config('mail.default');
            $config = (array) config("mail.mailers.{$mailer}");

            if (($config['transport'] ?? $config['driver'] ?? '') === 'smtp' && empty($config['host'])) {
                return HealthReport::failed($this->name(), 'SMTP mailer has no host configured', $this->elapsed($start));
            }

            return HealthReport::ok($this->name(), 'Mail configuration is present', $this->elapsed($start));
        } catch (Throwable $e) {
            return HealthReport::failed($this->name(), $e->getMessage(), $this->elapsed($start));
        }
    }

    private function elapsed(float $start): float
    {
        return (microtime(true) - $start) * 1000;
    }
}