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
use App\Shared\Salla\Exceptions\SallaApiException;
use App\Shared\Salla\Exceptions\SallaAuthException;
use App\Shared\Salla\Exceptions\SallaRateLimitException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

final class SallaClient implements SallaClientContract
{
    public function __construct(
        private readonly SallaAuthenticator $authenticator,
    ) {}

    public function authenticate(string $code): array
    {
        return $this->authenticator->exchangeCode($code);
    }

    public function refreshToken(): array
    {
        $this->authenticator->refreshAccessToken();

        return [
            'access_token' => $this->authenticator->getAccessToken(),
            'refreshed_at' => now()->toIso8601String(),
        ];
    }

    /** @return array<int, ProductDTO> */
    public function getProducts(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('products', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): ProductDTO => ProductDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getProduct(int $id): ProductDTO
    {
        $response = $this->get("products/{$id}");

        return ProductDTO::fromSallaResponse($response['data']);
    }

    /** @return array<int, CategoryDTO> */
    public function getCategories(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('categories', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): CategoryDTO => CategoryDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getCategory(int $id): CategoryDTO
    {
        $response = $this->get("categories/{$id}");

        return CategoryDTO::fromSallaResponse($response['data']);
    }

    /** @return array<int, BrandDTO> */
    public function getBrands(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('brands', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): BrandDTO => BrandDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getBrand(int $id): BrandDTO
    {
        $response = $this->get("brands/{$id}");

        return BrandDTO::fromSallaResponse($response['data']);
    }

    /** @return array<int, OrderDTO> */
    public function getOrders(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('orders', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): OrderDTO => OrderDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getOrder(int $id): OrderDTO
    {
        $response = $this->get("orders/{$id}");

        return OrderDTO::fromSallaResponse($response['data']);
    }

    /** @return array<int, CustomerDTO> */
    public function getCustomers(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('customers', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): CustomerDTO => CustomerDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getCustomer(int $id): CustomerDTO
    {
        $response = $this->get("customers/{$id}");

        return CustomerDTO::fromSallaResponse($response['data']);
    }

    /** @return array<int, CouponDTO> */
    public function getCoupons(int $page = 1, int $perPage = 50): array
    {
        $response = $this->get('coupons', [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return array_map(
            static fn (array $row): CouponDTO => CouponDTO::fromSallaResponse($row),
            $response['data'] ?? [],
        );
    }

    public function getCoupon(int $id): CouponDTO
    {
        $response = $this->get("coupons/{$id}");

        return CouponDTO::fromSallaResponse($response['data']);
    }

    /** @return array<string, mixed> */
    public function registerWebhook(string $event, string $url): array
    {
        return $this->post('webhooks', [
            'event' => $event,
            'url' => $url,
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    public function getWebhooks(): array
    {
        $response = $this->get('webhooks');

        return $response['data'] ?? [];
    }

    public function deleteWebhook(string $webhookId): bool
    {
        $this->delete("webhooks/{$webhookId}");

        return true;
    }

    /** @return array{limit: int, remaining: int, reset: int} */
    public function getRateLimitStatus(): array
    {
        $response = $this->get('rate-limit');

        return [
            'limit' => (int) ($response['limit'] ?? 0),
            'remaining' => (int) ($response['remaining'] ?? 0),
            'reset' => (int) ($response['reset'] ?? 0),
        ];
    }

    // ------------------------------------------------------------------
    // Public HTTP helpers
    // ------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->handle(
            $this->buildRequest()->get($this->url($endpoint), $params),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->handle(
            $this->buildRequest()->post($this->url($endpoint), $data),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->handle(
            $this->buildRequest()->put($this->url($endpoint), $data),
        );
    }

    /** @return array<string, mixed> */
    public function delete(string $endpoint): array
    {
        return $this->handle(
            $this->buildRequest()->delete($this->url($endpoint)),
        );
    }

    private function buildRequest(): PendingRequest
    {
        $timeout = (int) config('salla.http.timeout', 30);
        $retryTimes = (int) config('salla.http.retry_times', 3);
        $retryDelay = (int) config('salla.http.retry_delay_ms', 500);

        return Http::withToken($this->authenticator->getAccessToken())
            ->timeout($timeout)
            ->acceptJson()
            ->retry(
                times: $retryTimes,
                sleepMilliseconds: $retryDelay,
                when: fn (Throwable $e): bool => $this->shouldRetry($e),
            );
    }

    private function url(string $endpoint): string
    {
        $base = config('salla.base_url') ?? 'https://api.salla.dev/admin';
        $ver = config('salla.api_version') ?? 'v2';

        return rtrim($base, '/').'/'.$ver.'/'.ltrim($endpoint, '/');
    }

    /**
     * @return array<string, mixed>
     */
    private function handle(Response $response): array
    {
        if ($response->successful()) {
            /** @var array<string, mixed> $json */
            $json = $response->json() ?? [];

            return $json;
        }

        $status = $response->status();
        $body = $response->json() ?? [];
        $message = (string) ($body['message'] ?? $response->body());

        throw match (true) {
            $status === 401 => new SallaAuthException($message, $status, $body),
            $status === 429 => new SallaRateLimitException(
                message: $message,
                code: $status,
                retryAfter: (int) ($response->header('Retry-After') ?? 0),
                response: $body,
            ),
            default => new SallaApiException($message, $status, $body),
        };
    }

    private function shouldRetry(Throwable $e): bool
    {
        if ($e instanceof ConnectionException) {
            return true;
        }

        return $e instanceof SallaApiException && $e->getCode() >= 500;
    }
}
