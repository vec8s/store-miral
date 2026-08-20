<?php

declare(strict_types=1);

namespace Tests\Unit\Salla;

use App\Shared\Salla\Exceptions\SallaApiException;
use App\Shared\Salla\Exceptions\SallaAuthException;
use App\Shared\Salla\Exceptions\SallaRateLimitException;
use App\Shared\Salla\SallaAuthenticator;
use App\Shared\Salla\SallaClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SallaClientTest extends TestCase
{
    private SallaClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('salla.base_url', 'https://api.salla.dev/admin');
        config()->set('salla.api_version', 'v2');
        config()->set('salla.http.timeout', 10);
        config()->set('salla.http.retry_times', 0);
        config()->set('salla.http.retry_delay_ms', 100);
        config()->set('salla.cache.token_key', 'salla_access_token');

        Cache::put('salla_access_token', 'real-access-token', 3600);

        $authenticator = new SallaAuthenticator('client', 'secret', 'https://localhost/callback');
        $this->client = new SallaClient($authenticator);
    }

    public function test_get_products_returns_product_dtos_from_api(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/products?*' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'سلسلة ذهبية', 'price' => ['amount' => 349.0, 'currency' => 'SAR']],
                    ['id' => 2, 'name' => 'ساعة فاخرة', 'price' => ['amount' => 520.0, 'currency' => 'SAR']],
                ],
                'pagination' => ['total' => 2],
            ], 200),
        ]);

        $products = $this->client->getProducts(1, 2);

        $this->assertCount(2, $products);
        $this->assertSame(1, $products[0]->id);
        $this->assertSame('سلسلة ذهبية', $products[0]->name);
        $this->assertSame(349.0, $products[0]->price);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/admin/v2/products')
                && str_contains($request->url(), 'page=1')
                && str_contains($request->url(), 'per_page=2');
        });
    }

    public function test_get_product_returns_single_dto(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/products/7' => Http::response([
                'data' => ['id' => 7, 'name' => 'ميدالية فضية', 'price' => ['amount' => 275.0, 'currency' => 'SAR']],
            ], 200),
        ]);

        $product = $this->client->getProduct(7);

        $this->assertSame(7, $product->id);
        $this->assertSame('ميدالية فضية', $product->name);
    }

    public function test_get_categories_returns_category_dtos(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/categories*' => Http::response([
                'data' => [
                    ['id' => 1, 'name' => 'السلاسل'],
                    ['id' => 2, 'name' => 'الساعات'],
                ],
            ], 200),
        ]);

        $categories = $this->client->getCategories();

        $this->assertCount(2, $categories);
        $this->assertSame('السلاسل', $categories[0]->name);
    }

    public function test_unauthorized_response_throws_auth_exception(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/products*' => Http::response(['message' => 'Unauthenticated.'], 401),
        ]);

        $this->expectException(SallaAuthException::class);
        $this->expectExceptionCode(401);

        $this->client->getProducts();
    }

    public function test_rate_limit_response_throws_rate_limit_exception_with_retry_after(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/products*' => Http::response(
                ['message' => 'Too Many Requests'],
                429,
                ['Retry-After' => '30'],
            ),
        ]);

        $this->expectException(SallaRateLimitException::class);

        try {
            $this->client->getProducts();
        } catch (SallaRateLimitException $e) {
            $this->assertSame(429, $e->getCode());
            $this->assertSame(30, $e->retryAfter());

            throw $e;
        }
    }

    public function test_server_error_response_throws_api_exception(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/products*' => Http::response(['message' => 'Internal error'], 500),
        ]);

        $this->expectException(SallaApiException::class);
        $this->expectExceptionCode(500);

        $this->client->getProducts();
    }

    public function test_post_registers_webhook(): void
    {
        Http::fake([
            'https://api.salla.dev/admin/v2/webhooks' => Http::response([
                'data' => ['id' => 9001, 'event' => 'order.created', 'url' => 'https://example.test/hook'],
            ], 201),
        ]);

        $result = $this->client->registerWebhook('order.created', 'https://example.test/hook');

        $this->assertSame(9001, $result['data']['id']);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.salla.dev/admin/v2/webhooks'
                && $request['event'] === 'order.created';
        });
    }
}