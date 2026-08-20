<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Jobs;

use App\Domains\Catalog\Models\Product;
use App\Jobs\SyncSallaProducts;
use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\ProductSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SyncSallaProductsTest extends TestCase
{
    use RefreshDatabase;

    private SallaClientContract $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new class implements SallaClientContract {
            public int $callCount = 0;

            public function get(string $endpoint, array $params = []): array
            {
                $this->callCount++;

                $page = (int) ($params['page'] ?? 1);
                $perPage = (int) ($params['per_page'] ?? 50);

                $rows = [
                    ['id' => 1, 'name' => 'منتج أول', 'status' => 'active', 'price' => ['amount' => 10.0, 'currency' => 'SAR'], 'quantity' => 5],
                    ['id' => 2, 'name' => 'منتج ثان', 'status' => 'active', 'price' => ['amount' => 20.0, 'currency' => 'SAR'], 'quantity' => 3],
                ];

                return [
                    'data' => array_slice($rows, ($page - 1) * $perPage, $perPage),
                    'pagination' => [
                        'page' => $page,
                        'per_page' => $perPage,
                        'total' => 2,
                        'last_page' => 1,
                    ],
                ];
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
    }

    public function test_syncs_all_products_from_first_page(): void
    {
        Queue::fake();

        $job = new SyncSallaProducts(perPage: 50);
        $job->handle($this->client, new ProductSyncService());

        $this->assertSame(2, Product::count());
        $this->assertSame('منتج أول', Product::where('salla_id', '1')->first()->name);
        $this->assertSame(1, $this->client->callCount);
    }

    public function test_does_not_dispatch_next_page_when_last_page_reached(): void
    {
        Queue::fake();

        $job = new SyncSallaProducts(perPage: 50);
        $job->handle($this->client, new ProductSyncService());

        Queue::assertNothingPushed();
    }

    public function test_dispatch_of_job_itself_is_queued(): void
    {
        Queue::fake();

        SyncSallaProducts::dispatch(50);

        Queue::assertPushed(SyncSallaProducts::class, 1);
    }
}