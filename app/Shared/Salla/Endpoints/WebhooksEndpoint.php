<?php

declare(strict_types=1);

namespace App\Shared\Salla\Endpoints;

use App\Shared\Contracts\SallaClientContract;

final class WebhooksEndpoint
{
    public function __construct(
        private readonly SallaClientContract $client,
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function list(): array
    {
        return $this->client->getWebhooks();
    }

    /** @return array<string, mixed> */
    public function register(string $event, string $url): array
    {
        return $this->client->registerWebhook($event, $url);
    }

    public function delete(string $webhookId): bool
    {
        return $this->client->deleteWebhook($webhookId);
    }
}
