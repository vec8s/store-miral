<?php

declare(strict_types=1);

namespace App\Shared\Health;

interface HealthCheck
{
    public function name(): string;

    public function check(): HealthReport;
}