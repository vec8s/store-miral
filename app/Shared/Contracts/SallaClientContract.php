<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

use App\Domains\Shared\DTOs\BrandDTO;
use App\Domains\Shared\DTOs\CategoryDTO;
use App\Domains\Shared\DTOs\CouponDTO;
use App\Domains\Shared\DTOs\CustomerDTO;
use App\Domains\Shared\DTOs\OrderDTO;
use App\Domains\Shared\DTOs\ProductDTO;

interface SallaClientContract
{
    // ------------------------------------------------------------------
    // Generic HTTP methods (consumed by Endpoint classes)
    // ------------------------------------------------------------------

    /**
     * @param  array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function put(string $endpoint, array $data = []): array;

    /** @return array<string, mixed> */
    public function delete(string $endpoint): array;

    // ------------------------------------------------------------------
    // OAuth2
    // ------------------------------------------------------------------

    /**
     * @return array{access_token: string, refresh_token: string, expires_in: int, token_type: string, scope: string}
     */
    public function authenticate(string $code): array;

    /**
     * @return array{access_token: string, refreshed_at: string}
     */
    public function refreshToken(): array;

    // ------------------------------------------------------------------
    // Typed resource accessors (return DTOs)
    // ------------------------------------------------------------------

    /** @return array<int, ProductDTO> */
    public function getProducts(int $page = 1, int $perPage = 50): array;

    public function getProduct(int $id): ProductDTO;

    /** @return array<int, CategoryDTO> */
    public function getCategories(int $page = 1, int $perPage = 50): array;

    public function getCategory(int $id): CategoryDTO;

    /** @return array<int, BrandDTO> */
    public function getBrands(int $page = 1, int $perPage = 50): array;

    public function getBrand(int $id): BrandDTO;

    /** @return array<int, OrderDTO> */
    public function getOrders(int $page = 1, int $perPage = 50): array;

    public function getOrder(int $id): OrderDTO;

    /** @return array<int, CustomerDTO> */
    public function getCustomers(int $page = 1, int $perPage = 50): array;

    public function getCustomer(int $id): CustomerDTO;

    /** @return array<int, CouponDTO> */
    public function getCoupons(int $page = 1, int $perPage = 50): array;

    public function getCoupon(int $id): CouponDTO;

    // ------------------------------------------------------------------
    // Webhooks
    // ------------------------------------------------------------------

    /** @return array<string, mixed> */
    public function registerWebhook(string $event, string $url): array;

    /** @return array<int, array<string, mixed>> */
    public function getWebhooks(): array;

    public function deleteWebhook(string $webhookId): bool;

    /** @return array{limit: int, remaining: int, reset: int} */
    public function getRateLimitStatus(): array;
}
