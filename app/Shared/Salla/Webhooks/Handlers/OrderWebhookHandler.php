<?php

declare(strict_types=1);

namespace App\Shared\Salla\Webhooks\Handlers;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Models\Order;
use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\OrderSyncService;
use App\Shared\Salla\Webhooks\SallaWebhookHandlerInterface;

/**
 * Applies order webhook events to the local order store.
 *
 * Webhook events carry only partial order data, so the handler re-fetches the
 * full order from Salla before syncing to guarantee consistent snapshots.
 */
final class OrderWebhookHandler implements SallaWebhookHandlerInterface
{
    public const PREFIX = 'order';

    public function __construct(
        private readonly OrderSyncService $orders,
        private readonly SallaClientContract $client,
    ) {}

    public function supports(string $event): bool
    {
        return str_starts_with($event, self::PREFIX.'.');
    }

    /** @param  array<string, mixed>  $payload */
    public function handle(string $event, array $payload): void
    {
        if ($event === 'order.deleted') {
            $this->handleDeleted($payload);

            return;
        }

        $full = $this->fetchFullOrder($payload);

        if ($full !== null) {
            $this->orders->syncFromSalla($full);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    private function fetchFullOrder(array $payload): ?array
    {
        $id = $this->orderId($payload);

        if ($id === null) {
            return null;
        }

        try {
            $response = $this->client->get("orders/{$id}");

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
    private function handleDeleted(array $payload): void
    {
        $id = $this->orderId($payload);

        if ($id === null) {
            return;
        }

        $order = Order::where('salla_id', $id)->first();

        if ($order === null) {
            return;
        }

        $order->update([
            'local_status' => OrderStatus::Cancelled,
            'salla_status' => 'deleted',
            'sync_status' => SyncStatus::Synced,
            'synced_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function orderId(array $payload): ?string
    {
        $id = $payload['id'] ?? $payload['order']['id'] ?? null;

        return $id !== null ? (string) $id : null;
    }
}
