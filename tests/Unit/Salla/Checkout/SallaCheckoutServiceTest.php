<?php

declare(strict_types=1);

namespace Tests\Unit\Salla\Checkout;

use App\Domains\Commerce\Models\Cart;
use App\Domains\Commerce\Models\CheckoutSession;
use App\Shared\Salla\Checkout\SallaCheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SallaCheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private SallaCheckoutService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('salla.checkout_base_url', 'https://api.salla.dev/store/v2/checkout');
        config()->set('salla.http.timeout', 30);

        $this->service = new SallaCheckoutService();
    }

    private function items(): array
    {
        return [
            ['product_id' => 101, 'name' => 'ساعة فاخرة', 'quantity' => 1, 'price' => 520.0],
            ['product_id' => 102, 'name' => 'سلسلة ذهبية', 'quantity' => 2, 'price' => 349.0],
        ];
    }

    private function customer(): array
    {
        return [
            'name' => 'أحمد',
            'phone' => '0555123456',
            'email' => 'ahmed@example.com',
            'currency' => 'SAR',
        ];
    }

    private function shipping(): array
    {
        return [
            'city' => 'الرياض',
            'address' => 'طريق الملك فهد',
        ];
    }

    public function test_creates_local_cart_and_session(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response([
                'data' => ['checkout_url' => 'https://checkout.salla.sa/abc123', 'cart_id' => 'CART-1'],
            ], 201),
        ]);

        $session = $this->service->createSession($this->items(), $this->customer(), $this->shipping());

        $this->assertSame(1, Cart::count());
        $this->assertSame(1, CheckoutSession::count());
        $this->assertSame('created', $session->status);
        $this->assertSame('https://checkout.salla.sa/abc123', $session->checkout_url);
        $this->assertSame('CART-1', $session->salla_cart_id);
        $this->assertSame('SAR', $session->currency);
        $this->assertNotNull($session->uuid);
        $this->assertNotNull($session->idempotency_key);
        $this->assertNotNull($session->expires_at);
        $this->assertSame(['subtotal' => 121800, 'total' => 121800], $session->amount_snapshot);
    }

    public function test_marks_session_failed_when_api_unavailable(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response([], 500),
        ]);

        $session = $this->service->createSession($this->items(), $this->customer(), $this->shipping());

        $this->assertSame('failed', $session->status);
        $this->assertNull($session->checkout_url);
        $this->assertNull($session->salla_cart_id);
    }

    public function test_marks_session_failed_when_network_errors(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response([], 500),
        ]);

        $session = $this->service->createSession($this->items(), $this->customer(), $this->shipping());

        $this->assertSame('failed', $session->status);
    }

    public function test_sends_items_customer_and_shipping_to_checkout_api(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response(['data' => ['checkout_url' => 'https://checkout.salla.sa/x']], 201),
        ]);

        $this->service->createSession($this->items(), $this->customer(), $this->shipping());

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $request->url() === 'https://api.salla.dev/store/v2/checkout'
                && count($body['items']) === 2
                && $body['customer']['name'] === 'أحمد'
                && $body['shipping']['city'] === 'الرياض';
        });
    }

    public function test_amount_snapshot_accumulates_quantities(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response(['data' => ['checkout_url' => 'https://checkout.salla.sa/x']], 201),
        ]);

        $session = $this->service->createSession($this->items(), $this->customer(), $this->shipping());

        $expectedSubtotal = (520.0 * 1 + 349.0 * 2) * 100;

        $this->assertSame((int) round($expectedSubtotal), $session->amount_snapshot['subtotal']);
    }
}