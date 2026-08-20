<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Webhooks\Handlers;

use App\Domains\Catalog\Enums\ProductStatus;
use App\Domains\Catalog\Models\Product;
use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\ProductSyncService;
use App\Shared\Salla\Webhooks\Handlers\ProductWebhookHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductWebhookHandlerTest extends TestCase
{
    use RefreshDatabase;

    private ProductWebhookHandler $handler;

    /** @var array<string, mixed>|null */
    private ?array $fetchResponse = null;

    private int $fetchCount = 0;

    public function bumpFetch(): void
    {
        $this->fetchCount++;
    }

    /** @return array<string, mixed>|null */
    public function responsePayload(): ?array
    {
        return $this->fetchResponse;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $client = new class($this) implements SallaClientContract {
            public function __construct(private ProductWebhookHandlerTest $test)
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

        $this->handler = new ProductWebhookHandler(new ProductSyncService(), $client);
    }

    private function fullProduct(): array
    {
        return [
            'id' => 101,
            'name' => 'ساعة فاخرة',
            'sku' => 'SKU-101',
            'status' => 'active',
            'price' => ['amount' => 520.0, 'currency' => 'SAR'],
            'quantity' => 10,
        ];
    }

    public function test_supports_product_events(): void
    {
        $this->assertTrue($this->handler->supports('product.created'));
        $this->assertTrue($this->handler->supports('product.updated'));
        $this->assertTrue($this->handler->supports('product.deleted'));
        $this->assertFalse($this->handler->supports('order.created'));
    }

    public function test_updated_event_fetches_and_syncs_product(): void
    {
        $this->fetchResponse = $this->fullProduct();

        $this->handler->handle('product.updated', ['id' => 101, 'name' => 'ساعة فاخرة']);

        $product = Product::where('salla_id', '101')->first();
        $this->assertNotNull($product);
        $this->assertSame('ساعة فاخرة', $product->name);
        $this->assertSame(1, $this->fetchCount);
    }

    public function test_complete_payload_is_synced_without_fetch(): void
    {
        $this->handler->handle('product.created', $this->fullProduct());

        $product = Product::where('salla_id', '101')->first();
        $this->assertNotNull($product);
        $this->assertSame(ProductStatus::Active, $product->status);
        $this->assertSame(0, $this->fetchCount);
    }

    public function test_deleted_event_removes_local_product(): void
    {
        (new ProductSyncService())->syncFromSalla($this->fullProduct());

        $this->handler->handle('product.deleted', ['id' => 101]);

        $this->assertNull(Product::where('salla_id', '101')->first());
        $this->assertTrue(Product::where('salla_id', '101')->withTrashed()->exists());
    }

    public function test_partial_payload_without_fetchable_data_is_ignored(): void
    {
        $this->fetchResponse = null;

        $this->handler->handle('product.price.updated', ['id' => 101]);

        $this->assertNull(Product::where('salla_id', '101')->first());
    }
}