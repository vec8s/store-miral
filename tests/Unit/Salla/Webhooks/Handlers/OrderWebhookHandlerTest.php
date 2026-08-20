<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Webhooks\Handlers;

use App\Domains\Commerce\Enums\OrderStatus;
use App\Domains\Commerce\Models\Order;
use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\OrderSyncService;
use App\Shared\Salla\Webhooks\Handlers\OrderWebhookHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWebhookHandlerTest extends TestCase
{
    use RefreshDatabase;

    private OrderWebhookHandler $handler;

    /** @var array<string, mixed>|null */
    private ?array $fetchResponse = null;

    private int $fetchCount = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $client = new class($this) implements SallaClientContract {
            public function __construct(private OrderWebhookHandlerTest $test)
            {
            }

            public function get(string $endpoint, array $params = []): array
            {
                $this->test->bumpFetch();

                return ['data' => $this->test->responsePayload() ?? []];
            }

            public function post(string $endpoint, array $data = []): array
            {
                return [];
            }

            public function put(string $endpoint, array $data = []): array
            {
                return [];
            }

            public function delete(string $endpoint): array
            {
                return ['success' => true];
            }

            public function authenticate(string $code): array
            {
                return [];
            }

            public function refreshToken(): array
            {
                return [];
            }

            public function getProducts(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getProduct(int $id): \App\Domains\Shared\DTOs\ProductDTO
            {
                return \App\Domains\Shared\DTOs\ProductDTO::fromSallaResponse([]);
            }

            public function getCategories(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getCategory(int $id): \App\Domains\Shared\DTOs\CategoryDTO
            {
                return \App\Domains\Shared\DTOs\CategoryDTO::fromSallaResponse([]);
            }

            public function getBrands(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getBrand(int $id): \App\Domains\Shared\DTOs\BrandDTO
            {
                return \App\Domains\Shared\DTOs\BrandDTO::fromSallaResponse([]);
            }

            public function getOrders(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getOrder(int $id): \App\Domains\Shared\DTOs\OrderDTO
            {
                return \App\Domains\Shared\DTOs\OrderDTO::fromSallaResponse([]);
            }

            public function getCustomers(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getCustomer(int $id): \App\Domains\Shared\DTOs\CustomerDTO
            {
                return \App\Domains\Shared\DTOs\CustomerDTO::fromSallaResponse([]);
            }

            public function getCoupons(int $page = 1, int $perPage = 50): array
            {
                return [];
            }

            public function getCoupon(int $id): \App\Domains\Shared\DTOs\CouponDTO
            {
                return \App\Domains\Shared\DTOs\CouponDTO::fromSallaResponse([]);
            }

            public function registerWebhook(string $event, string $url): array
            {
                return [];
            }

            public function getWebhooks(): array
            {
                return [];
            }

            public function deleteWebhook(string $webhookId): bool
            {
                return true;
            }

            public function getRateLimitStatus(): array
            {
                return ['limit' => 1000, 'remaining' => 1000, 'reset' => 3600];
            }
        };

        $this->handler = new OrderWebhookHandler(new OrderSyncService(), $client);
    }

    public function bumpFetch(): void
    {
        $this->fetchCount++;
    }

    /** @return array<string, mixed>|null */
    public function responsePayload(): ?array
    {
        return $this->fetchResponse;
    }

    private function fullOrder(int $id = 1000): array
    {
        return [
            'id' => $id,
            'reference_id' => 500,
            'date' => ['date' => '2026-08-15 10:00:00'],
            'status' => ['slug' => 'processing', 'name' => 'قيد المعالجة'],
            'payment_method' => 'mada',
            'amounts' => [
                'sub_total' => ['amount' => 520.0, 'currency' => 'SAR'],
                'shipping_cost' => ['amount' => 20.0, 'currency' => 'SAR'],
                'tax' => ['amount' => 27.3, 'currency' => 'SAR'],
                'discounts' => ['amount' => 0.0, 'currency' => 'SAR'],
                'total' => ['amount' => 567.3, 'currency' => 'SAR'],
            ],
            'currency' => 'SAR',
            'items' => [],
        ];
    }

    public function test_supports_order_events(): void
    {
        $this->assertTrue($this->handler->supports('order.created'));
        $this->assertTrue($this->handler->supports('order.status.updated'));
        $this->assertTrue($this->handler->supports('order.deleted'));
        $this->assertFalse($this->handler->supports('product.created'));
    }

    public function test_created_event_fetches_and_syncs_order(): void
    {
        $this->fetchResponse = $this->fullOrder();

        $this->handler->handle('order.created', ['id' => 1000]);

        $order = Order::where('salla_id', '1000')->first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::Processing, $order->local_status);
        $this->assertSame(1, $this->fetchCount);
    }

    public function test_status_updated_event_syncs_new_status(): void
    {
        $this->fetchResponse = $this->fullOrder();
        $this->handler->handle('order.created', ['id' => 1000]);

        $this->fetchResponse = array_replace($this->fullOrder(), ['status' => ['slug' => 'delivered', 'name' => 'تم التسليم']]);
        $this->handler->handle('order.status.updated', ['id' => 1000]);

        $order = Order::where('salla_id', '1000')->first();
        $this->assertSame(OrderStatus::Delivered, $order->local_status);
        $this->assertSame(2, $this->fetchCount);
    }

    public function test_refunded_event_syncs_refund_status(): void
    {
        $this->fetchResponse = array_replace($this->fullOrder(), ['status' => ['slug' => 'refunded', 'name' => 'مسترجع']]);

        $this->handler->handle('order.refunded', ['id' => 1000]);

        $order = Order::where('salla_id', '1000')->first();
        $this->assertSame(OrderStatus::Refunded, $order->local_status);
    }

    public function test_deleted_event_marks_order_cancelled(): void
    {
        $this->fetchResponse = $this->fullOrder();
        $this->handler->handle('order.created', ['id' => 1000]);

        $this->handler->handle('order.deleted', ['id' => 1000]);

        $order = Order::where('salla_id', '1000')->first();
        $this->assertSame(OrderStatus::Cancelled, $order->local_status);
        $this->assertSame('deleted', $order->salla_status);
    }

    public function test_unknown_event_payload_without_id_is_ignored(): void
    {
        $this->handler->handle('order.payment.updated', []);

        $this->assertSame(0, Order::count());
        $this->assertSame(0, $this->fetchCount);
    }
}