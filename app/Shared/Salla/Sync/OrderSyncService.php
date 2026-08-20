<?php

declare(strict_types=1);

namespace App\Shared\Salla\Sync;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Catalog\Models\Product;
use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Enums\PaymentMethod;
use App\Domains\Commerce\Enums\PaymentStatus;
use App\Domains\Commerce\Models\Order;
use App\Domains\Commerce\Models\OrderItem;
use App\Domains\Commerce\Models\OrderSnapshot;
use App\Domains\Shared\DTOs\OrderDTO;
use Illuminate\Support\Str;

/**
 * Persists Salla order payloads into the local order store.
 *
 * Each sync records an immutable snapshot of the order so the status history
 * is preserved. The current order row always reflects the latest Salla state.
 */
final class OrderSyncService
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function syncFromSalla(array $raw): Order
    {
        $dto = OrderDTO::fromSallaResponse($raw);

        $orderStatus = $this->mapOrderStatus($dto->statusSlug);
        $paymentStatus = $this->mapPaymentStatus($dto->statusSlug, $raw);

        $order = Order::updateOrCreate(
            ['salla_id' => (string) $dto->id],
            [
                'salla_order_id' => (string) $dto->id,
                'reference_id' => $dto->referenceId !== null ? (string) $dto->referenceId : null,
                'public_id' => $this->publicId($dto),
                'local_status' => $orderStatus,
                'salla_status' => $dto->statusSlug,
                'payment_status' => $paymentStatus,
                'payment_method' => $this->normalizePaymentMethod($dto->paymentMethod),
                'currency' => $dto->currency ?? 'SAR',
                'subtotal_minor' => $this->toMinor($dto->subTotal),
                'shipping_cost_minor' => $this->toMinor($dto->shippingCost),
                'tax_amount_minor' => $this->toMinor($dto->tax),
                'discount_minor' => $this->toMinor($dto->discounts),
                'total_minor' => $this->toMinor($dto->total),
                'placed_at' => $dto->placedAt !== null ? $this->parseDate($dto->placedAt) : null,
                'source_updated_at' => $dto->placedAt,
                'last_salla_updated_at' => now(),
                'synced_at' => now(),
                'sync_status' => SyncStatus::Synced,
            ],
        );

        $this->syncItems($order, (array) ($raw['items'] ?? []));

        $this->createSnapshot($order, $raw);

        return $order->refresh();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(Order $order, array $items): void
    {
        $order->items()->delete();

        if ($items === []) {
            return;
        }

        $payload = array_map(function (array $item) use ($order): array {
            $productId = $this->localProductId($item);

            return [
                'order_id' => $order->id,
                'product_id' => $productId,
                'salla_id' => isset($item['id']) ? (string) $item['id'] : null,
                'salla_product_id' => isset($item['product']['id']) ? (string) $item['product']['id'] : (isset($item['product_id']) ? (string) $item['product_id'] : null),
                'salla_variant_id' => isset($item['variant_id']) ? (string) $item['variant_id'] : null,
                'name' => (string) ($item['name'] ?? $item['product']['name'] ?? ''),
                'sku' => isset($item['sku']) ? (string) $item['sku'] : null,
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit_price_minor' => $this->toMinor($this->amount($item['unit_price'] ?? null)),
                'total_minor' => $this->toMinor($this->amount($item['total'] ?? null)),
                'options' => isset($item['options']) ? (array) $item['options'] : null,
            ];
        }, $items);

        OrderItem::insert($payload);
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function createSnapshot(Order $order, array $raw): void
    {
        $versionHash = $this->versionHash($order);

        $latest = $order->snapshots()->latest('id')->first();

        if ($latest !== null && $latest->version_hash === $versionHash) {
            return;
        }

        OrderSnapshot::create([
            'order_id' => $order->id,
            'salla_order_id' => (string) $order->salla_id,
            'source_event_id' => isset($raw['id']) ? (string) $raw['id'] : null,
            'version_hash' => $versionHash,
            'status' => $order->local_status->value,
            'payment_status' => $order->payment_status->value,
            'fulfillment_status' => $order->shipping_status?->value,
            'total' => $order->total_minor,
            'currency' => $order->currency,
            'items_json' => $order->items()->get()->toArray(),
            'payments_json' => isset($raw['payments']) ? (array) $raw['payments'] : null,
            'raw_payload_compressed' => gzcompress(json_encode($raw) ?: '[]') ?: null,
            'captured_at' => now(),
        ]);
    }

    private function versionHash(Order $order): string
    {
        $signature = implode('|', [
            (string) $order->salla_status,
            (string) $order->payment_status->value,
            (string) $order->total_minor,
            (string) $order->subtotal_minor,
        ]);

        return hash('sha256', $signature);
    }

    private function publicId(OrderDTO $dto): string
    {
        $base = 'ORD-'.($dto->referenceId ?? $dto->id);

        return $base.'-'.Str::lower(Str::substr((string) $dto->id, -4));
    }

    private function mapOrderStatus(?string $slug): OrderStatus
    {
        return match (Str::lower((string) $slug)) {
            'new', 'pending', 'on_hold' => OrderStatus::Pending,
            'processing', 'confirmed', 'in_progress' => OrderStatus::Processing,
            'shipped', 'ready_to_ship', 'out_for_delivery' => OrderStatus::Shipped,
            'delivered' => OrderStatus::Delivered,
            'completed' => OrderStatus::Completed,
            'cancelled', 'canceled', 'failed' => OrderStatus::Cancelled,
            'refunded', 'returned' => OrderStatus::Refunded,
            default => OrderStatus::Pending,
        };
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function mapPaymentStatus(?string $slug, array $raw): PaymentStatus
    {
        $payment = $raw['payment'] ?? $raw['payment_info'] ?? null;

        if (is_array($payment)) {
            $paymentSlug = is_array($payment['status'] ?? null)
                ? ($payment['status']['slug'] ?? null)
                : ($payment['status'] ?? null);

            if (is_string($paymentSlug) && $paymentSlug !== '') {
                return match (Str::lower($paymentSlug)) {
                    'paid', 'captured', 'completed' => PaymentStatus::Paid,
                    'refunded' => PaymentStatus::Refunded,
                    'partially_refunded', 'partial_refunded' => PaymentStatus::PartiallyRefunded,
                    'failed', 'cancelled', 'canceled' => PaymentStatus::Failed,
                    default => PaymentStatus::Pending,
                };
            }
        }

        return match (Str::lower((string) $slug)) {
            'completed', 'delivered', 'shipped' => PaymentStatus::Paid,
            'refunded', 'returned' => PaymentStatus::Refunded,
            'cancelled', 'canceled', 'failed' => PaymentStatus::Failed,
            default => PaymentStatus::Pending,
        };
    }

    private function normalizePaymentMethod(?string $method): ?string
    {
        if ($method === null || $method === '') {
            return null;
        }

        $method = Str::lower($method);

        $known = [
            'cod' => 'cod',
            'cash_on_delivery' => 'cod',
            'cashondelivery' => 'cod',
            'cash' => 'cod',
            'card' => 'credit_card',
            'credit_card' => 'credit_card',
            'mada' => 'mada',
            'apple_pay' => 'apple_pay',
            'applepay' => 'apple_pay',
            'stc_pay' => 'stc_pay',
            'stcpay' => 'stc_pay',
            'stc' => 'stc_pay',
            'bank_transfer' => 'bank_transfer',
            'transfer' => 'bank_transfer',
            'mada_transfer' => 'bank_transfer',
        ];

        $mapped = $known[$method] ?? $method;

        return PaymentMethod::tryFrom($mapped)?->value ?? PaymentMethod::Other->value;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function localProductId(array $item): ?int
    {
        $sallaProductId = $item['product']['id'] ?? $item['product_id'] ?? null;

        if ($sallaProductId === null) {
            return null;
        }

        return Product::where('salla_id', (string) $sallaProductId)->value('id');
    }

    /**
     * @param  array<string, mixed>|null  $money
     */
    private function amount(?array $money): float
    {
        if (! is_array($money)) {
            return (float) ($money ?? 0);
        }

        return (float) ($money['amount'] ?? 0);
    }

    private function toMinor(float $amount): int
    {
        return (int) round($amount * 100);
    }

    private function parseDate(string $value): ?\DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
