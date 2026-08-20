<?php

declare(strict_types=1);

namespace Tests\Unit\Salla;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\MockSallaClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MockSallaClientTest extends TestCase
{
    private MockSallaClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new MockSallaClient();
    }

    public function test_implements_salla_client_contract(): void
    {
        $this->assertInstanceOf(SallaClientContract::class, $this->client);
    }

    public function test_get_products_returns_deterministic_product_dtos(): void
    {
        $products = $this->client->getProducts();

        $this->assertNotEmpty($products);

        $first = $products[0];
        $this->assertSame(1, $first->id);
        $this->assertSame('سلسلة ذهبية', $first->name);
        $this->assertNotNull($first->price);

        $again = $this->client->getProducts();
        $this->assertSame($first->id, $again[0]->id);
        $this->assertSame($first->name, $again[0]->name);
    }

    public function test_get_products_respects_pagination(): void
    {
        $all = $this->client->getProducts(1, 100);
        $pageTwo = $this->client->getProducts(2, 3);

        $this->assertCount(2, $pageTwo);
        $this->assertNotSame($all[0]->id, $pageTwo[0]->id);
        $this->assertSame(4, $pageTwo[0]->id);
    }

    public function test_get_product_returns_single_dto(): void
    {
        $product = $this->client->getProduct(1);

        $this->assertSame(1, $product->id);
        $this->assertSame('سلسلة ذهبية', $product->name);
    }

    public function test_get_categories_returns_category_dtos(): void
    {
        $categories = $this->client->getCategories();

        $this->assertNotEmpty($categories);
        $this->assertSame(1, $categories[0]->id);
    }

    public function test_get_brands_returns_brand_dtos(): void
    {
        $brands = $this->client->getBrands();

        $this->assertNotEmpty($brands);
        $this->assertSame(1, $brands[0]->id);
    }

    public function test_get_orders_returns_order_dtos(): void
    {
        $orders = $this->client->getOrders();

        $this->assertNotEmpty($orders);
        $this->assertSame(1000, $orders[0]->id);
    }

    public function test_get_customers_returns_customer_dtos(): void
    {
        $customers = $this->client->getCustomers();

        $this->assertNotEmpty($customers);
        $this->assertSame(1, $customers[0]->id);
    }

    public function test_get_coupons_returns_coupon_dtos(): void
    {
        $coupons = $this->client->getCoupons();

        $this->assertNotEmpty($coupons);
        $this->assertSame('MIRAL15', $coupons[0]->code);
    }

    public function test_authenticate_returns_deterministic_tokens_without_network(): void
    {
        $data = $this->client->authenticate('any-code');

        $this->assertArrayHasKey('access_token', $data);
        $this->assertSame('mock-access-token', $data['access_token']);
    }

    public function test_refresh_token_returns_deterministic_result_without_network(): void
    {
        $data = $this->client->refreshToken();

        $this->assertArrayHasKey('access_token', $data);
        $this->assertSame('mock-access-token', $data['access_token']);
    }

    public function test_mock_client_never_hits_the_network(): void
    {
        $this->client->getProducts();
        $this->client->getOrders();
        $this->client->getCategories();
        $this->client->authenticate('code');
        $this->client->refreshToken();
        $this->client->registerWebhook('order.created', 'https://example.test/hook');
        $this->client->get('products', ['page' => 1]);

        Http::assertNothingSent();
    }

    public function test_generic_get_returns_fixture_for_endpoint(): void
    {
        $data = $this->client->get('products', ['page' => 1]);

        $this->assertArrayHasKey('data', $data);
        $this->assertNotEmpty($data['data']);
    }

    public function test_register_webhook_returns_success_payload(): void
    {
        $result = $this->client->registerWebhook('order.created', 'https://example.test/hook');

        $this->assertArrayHasKey('id', $result);
        $this->assertTrue((bool) ($result['id'] ?? false));
    }
}