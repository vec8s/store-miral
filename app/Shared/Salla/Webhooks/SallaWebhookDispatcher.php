<?php

declare(strict_types=1);

namespace App\Shared\Salla\Webhooks;

use App\Shared\Salla\Webhooks\Handlers\OrderWebhookHandler;
use App\Shared\Salla\Webhooks\Handlers\ProductWebhookHandler;
use Illuminate\Contracts\Container\Container;

/**
 * Routes incoming Salla webhook events to the handler that owns them.
 *
 * Supported events (see https://docs.salla.dev for the canonical list):
 *   - order.created, order.updated, order.status.updated, order.cancelled,
 *     order.refunded, order.deleted, order.payment.updated
 *   - product.created, product.updated, product.deleted, product.price.updated,
 *     product.quantity.low, product.status.updated, product.image.updated
 *
 * Unknown events are ignored safely and reported through the log.
 */
final class SallaWebhookDispatcher
{
    /** @var array<string, class-string<SallaWebhookHandlerInterface>> */
    private const HANDLERS = [
        ProductWebhookHandler::PREFIX => ProductWebhookHandler::class,
        OrderWebhookHandler::PREFIX => OrderWebhookHandler::class,
    ];

    public function __construct(private readonly Container $container) {}

    /** @param  array<string, mixed>  $payload */
    public function dispatch(string $event, array $payload): void
    {
        $prefix = explode('.', $event, 2)[0];
        $class = self::HANDLERS[$prefix] ?? null;

        if ($class === null) {
            logger()->info('Salla webhook event ignored.', ['event' => $event]);

            return;
        }

        $handler = $this->container->make($class);

        if (! $handler->supports($event)) {
            logger()->info('Salla webhook event not supported by handler.', ['event' => $event]);

            return;
        }

        $handler->handle($event, $payload);
    }
}
