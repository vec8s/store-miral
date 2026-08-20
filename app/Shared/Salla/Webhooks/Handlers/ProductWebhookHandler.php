<?php

declare(strict_types=1);

namespace App\Shared\Salla\Webhooks\Handlers;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\ProductSyncService;
use App\Shared\Salla\Webhooks\SallaWebhookHandlerInterface;

/**
 * Applies product webhook events to the local catalog.
 *
 * Granular events (price.updated, quantity.low, ...) only carry partial data,
 * so the handler re-fetches the full product from Salla before syncing.
 */
final class ProductWebhookHandler implements SallaWebhookHandlerInterface
{
    public const PREFIX = 'product';

    public function __construct(
        private readonly ProductSyncService $products,
        private readonly SallaClientContract $client,
    ) {}

    public function supports(string $event): bool
    {
        return str_starts_with($event, self::PREFIX.'.');
    }

    /** @param  array<string, mixed>  $payload */
    public function handle(string $event, array $payload): void
    {
        if ($event === 'product.deleted') {
            $this->products->syncDeleted((string) $this->productId($payload));

            return;
        }

        if ($this->isComplete($payload)) {
            $this->products->syncFromSalla($payload);

            return;
        }

        $full = $this->fetchFullProduct($payload);

        if ($full !== null) {
            $this->products->syncFromSalla($full);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    private function fetchFullProduct(array $payload): ?array
    {
        $id = $this->productId($payload);

        if ($id === null) {
            return null;
        }

        try {
            $response = $this->client->get("products/{$id}");

            $data = $response['data'] ?? null;

            if (! is_array($data) || $data === []) {
                return null;
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function isComplete(array $payload): bool
    {
        return isset($payload['name'], $payload['price']);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function productId(array $payload): ?string
    {
        $id = $payload['id'] ?? $payload['product']['id'] ?? null;

        return $id !== null ? (string) $id : null;
    }
}
