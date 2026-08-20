<?php

declare(strict_types=1);

namespace App\Shared\Salla\Webhooks;

interface SallaWebhookHandlerInterface
{
    public function supports(string $event): bool;

    /** @param  array<string, mixed>  $payload */
    public function handle(string $event, array $payload): void;
}
