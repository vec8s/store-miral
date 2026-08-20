<?php

declare(strict_types=1);

namespace App\Shared\Salla;

use App\Domains\Shared\DTOs\BrandDTO;
use App\Domains\Shared\DTOs\CategoryDTO;
use App\Domains\Shared\DTOs\CouponDTO;
use App\Domains\Shared\DTOs\CustomerDTO;
use App\Domains\Shared\DTOs\OrderDTO;
use App\Domains\Shared\DTOs\ProductDTO;
use App\Shared\Contracts\SallaClientContract;

/**
 * Deterministic, network-free implementation of the Salla client contract.
 * Returns fixture data so the storefront stays functional during development
 * and whenever Salla credentials are absent.
 */
final class MockSallaClient implements SallaClientContract
{
    private const DEFAULT_PER_PAGE = 50;

    public function authenticate(string $code): array
    {
        return [
            'access_token' => 'mock-access-token',
            'refresh_token' => 'mock-refresh-token',
            'expires_in' => 14400,
            'token_type' => 'Bearer',
            'scope' => 'store.products:read',
        ];
    }

    public function refreshToken(): array
    {
        return [
            'access_token' => 'mock-access-token',
            'refreshed_at' => now()->toIso8601String(),
        ];
    }

    /** @return array<int, ProductDTO> */
    public function getProducts(int $page = 1, int $perPage = 50): array
    {
        $all = $this->fixture('products');

        return array_map(
            static fn (array $row): ProductDTO => ProductDTO::fromSallaResponse($row),
            array_slice($all, ($page - 1) * $perPage, $perPage),
        );
    }

    public function getProduct(int $id): ProductDTO
    {
        $products = $this->fixture('products');
        $row = $products[$id - 1] ?? $products[0];

        return ProductDTO::fromSallaResponse($row);
    }

    /** @return array<int, CategoryDTO> */
    public function getCategories(int $page = 1, int $perPage = 50): array
    {
        return $this->fixtureDtos('categories', CategoryDTO::class, $page, $perPage);
    }

    public function getCategory(int $id): CategoryDTO
    {
        $rows = $this->fixture('categories');

        return CategoryDTO::fromSallaResponse($rows[$id - 1] ?? $rows[0]);
    }

    /** @return array<int, BrandDTO> */
    public function getBrands(int $page = 1, int $perPage = 50): array
    {
        return $this->fixtureDtos('brands', BrandDTO::class, $page, $perPage);
    }

    public function getBrand(int $id): BrandDTO
    {
        $rows = $this->fixture('brands');

        return BrandDTO::fromSallaResponse($rows[$id - 1] ?? $rows[0]);
    }

    /** @return array<int, OrderDTO> */
    public function getOrders(int $page = 1, int $perPage = 50): array
    {
        return $this->fixtureDtos('orders', OrderDTO::class, $page, $perPage);
    }

    public function getOrder(int $id): OrderDTO
    {
        $rows = $this->fixture('orders');

        return OrderDTO::fromSallaResponse($rows[$id - 1] ?? $rows[0]);
    }

    /** @return array<int, CustomerDTO> */
    public function getCustomers(int $page = 1, int $perPage = 50): array
    {
        return $this->fixtureDtos('customers', CustomerDTO::class, $page, $perPage);
    }

    public function getCustomer(int $id): CustomerDTO
    {
        $rows = $this->fixture('customers');

        return CustomerDTO::fromSallaResponse($rows[$id - 1] ?? $rows[0]);
    }

    /** @return array<int, CouponDTO> */
    public function getCoupons(int $page = 1, int $perPage = 50): array
    {
        return $this->fixtureDtos('coupons', CouponDTO::class, $page, $perPage);
    }

    public function getCoupon(int $id): CouponDTO
    {
        $rows = $this->fixture('coupons');

        return CouponDTO::fromSallaResponse($rows[$id - 1] ?? $rows[0]);
    }

    /** @return array<string, mixed> */
    public function registerWebhook(string $event, string $url): array
    {
        return [
            'id' => 9001,
            'event' => $event,
            'url' => $url,
            'status' => 'active',
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function getWebhooks(): array
    {
        return [
            ['id' => 9001, 'event' => 'order.created', 'url' => 'https://example.test/hooks/order', 'status' => 'active'],
        ];
    }

    public function deleteWebhook(string $webhookId): bool
    {
        return true;
    }

    /** @return array{limit: int, remaining: int, reset: int} */
    public function getRateLimitStatus(): array
    {
        return ['limit' => 1000, 'remaining' => 1000, 'reset' => 3600];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->genericResponse($endpoint, $params);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->genericResponse($endpoint, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->genericResponse($endpoint, $data);
    }

    /** @return array<string, mixed> */
    public function delete(string $endpoint): array
    {
        return ['success' => true];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function genericResponse(string $endpoint, array $params = []): array
    {
        $resource = trim($endpoint, '/');
        $page = (int) ($params['page'] ?? 1);
        $perPage = (int) ($params['per_page'] ?? self::DEFAULT_PER_PAGE);

        $segments = explode('/', $resource);
        $resourceName = $segments[0];
        $rows = $this->fixture($resourceName);

        if (count($segments) > 1) {
            $id = (int) end($segments);
            $row = $rows[$id - 1] ?? ($rows[0] ?? []);

            return ['data' => $row];
        }

        return [
            'data' => array_slice($rows, ($page - 1) * $perPage, $perPage),
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => count($rows),
            ],
        ];
    }

    /**
     * @param  class-string  $dto
     * @return array<int, object>
     */
    private function fixtureDtos(string $resource, string $dto, int $page, int $perPage): array
    {
        $rows = array_slice($this->fixture($resource), ($page - 1) * $perPage, $perPage);

        return array_map(static fn (array $row): object => $dto::fromSallaResponse($row), $rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fixture(string $resource): array
    {
        return match ($resource) {
            'products' => [
                [
                    'id' => 1,
                    'name' => 'سلسلة ذهبية',
                    'sku' => 'MIR-0001',
                    'type' => 'product',
                    'status' => 'sale',
                    'price' => ['amount' => 349.0, 'currency' => 'SAR'],
                    'quantity' => 50,
                    'categories' => [1],
                    'images' => [
                        ['id' => 1, 'original' => 'https://picsum.photos/seed/miral0/400/400', 'thumbnail' => 'https://picsum.photos/seed/miral0/200/200'],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'ساعة فاخرة',
                    'sku' => 'MIR-0002',
                    'type' => 'product',
                    'status' => 'sale',
                    'price' => ['amount' => 520.0, 'currency' => 'SAR'],
                    'quantity' => 25,
                    'categories' => [2],
                    'images' => [
                        ['id' => 2, 'original' => 'https://picsum.photos/seed/miral1/400/400', 'thumbnail' => 'https://picsum.photos/seed/miral1/200/200'],
                    ],
                ],
                [
                    'id' => 3,
                    'name' => 'بوكس هدايا',
                    'sku' => 'MIR-0003',
                    'type' => 'product',
                    'status' => 'sale',
                    'price' => ['amount' => 199.0, 'currency' => 'SAR'],
                    'quantity' => 100,
                    'categories' => [3],
                    'images' => [
                        ['id' => 3, 'original' => 'https://picsum.photos/seed/miral2/400/400', 'thumbnail' => 'https://picsum.photos/seed/miral2/200/200'],
                    ],
                ],
                [
                    'id' => 4,
                    'name' => 'سبحة عقيق',
                    'sku' => 'MIR-0004',
                    'type' => 'product',
                    'status' => 'sale',
                    'price' => ['amount' => 449.0, 'currency' => 'SAR'],
                    'quantity' => 40,
                    'categories' => [4],
                    'images' => [
                        ['id' => 4, 'original' => 'https://picsum.photos/seed/miral3/400/400', 'thumbnail' => 'https://picsum.photos/seed/miral3/200/200'],
                    ],
                ],
                [
                    'id' => 5,
                    'name' => 'ميدالية فضية',
                    'sku' => 'MIR-0005',
                    'type' => 'product',
                    'status' => 'sale',
                    'price' => ['amount' => 275.0, 'currency' => 'SAR'],
                    'quantity' => 60,
                    'categories' => [5],
                    'images' => [
                        ['id' => 5, 'original' => 'https://picsum.photos/seed/miral4/400/400', 'thumbnail' => 'https://picsum.photos/seed/miral4/200/200'],
                    ],
                ],
            ],
            'categories' => [
                ['id' => 1, 'name' => 'السلاسل', 'icon' => '⛓️', 'products_count' => 12],
                ['id' => 2, 'name' => 'الساعات', 'icon' => '⌚', 'products_count' => 8],
                ['id' => 3, 'name' => 'الهدايا', 'icon' => '🎁', 'products_count' => 15],
            ],
            'brands' => [
                ['id' => 1, 'name' => 'ميرال', 'logo' => 'https://picsum.photos/seed/brand1/200/200'],
                ['id' => 2, 'name' => 'روج', 'logo' => 'https://picsum.photos/seed/brand2/200/200'],
            ],
            'orders' => [
                [
                    'id' => 1000,
                    'reference_id' => 5001,
                    'date' => ['date' => '2026-08-10T14:30:00+03:00', 'timezone' => '+03:00'],
                    'status' => ['id' => 1, 'name' => 'قيد المعالجة', 'slug' => 'processing'],
                    'amounts' => [
                        'sub_total' => ['amount' => 200.0, 'currency' => 'SAR'],
                        'shipping_cost' => ['amount' => 25.0, 'currency' => 'SAR'],
                        'tax' => ['amount' => 30.0, 'currency' => 'SAR'],
                        'discounts' => ['amount' => 10.0, 'currency' => 'SAR'],
                        'total' => ['amount' => 245.0, 'currency' => 'SAR'],
                    ],
                    'items' => [['id' => 1], ['id' => 2]],
                    'currency' => 'SAR',
                ],
            ],
            'customers' => [
                [
                    'id' => 1,
                    'first_name' => 'أحمد',
                    'last_name' => 'علي',
                    'mobile' => '555123456',
                    'mobile_code' => '966',
                    'email' => 'ahmed@example.com',
                    'city' => 'الرياض',
                    'country' => 'SA',
                    'currency' => 'SAR',
                ],
            ],
            'coupons' => [
                ['id' => 1, 'code' => 'MIRAL15', 'name' => 'خصم 15%', 'value' => 15, 'type' => 'percentage'],
                ['id' => 2, 'code' => 'GIFT2', 'name' => 'خصم 10% عند قطعتين', 'value' => 10, 'type' => 'percentage'],
            ],
            default => [],
        };
    }
}