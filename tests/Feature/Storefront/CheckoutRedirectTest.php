<?php

declare(strict_types=1);

namespace Tests\Feature\Storefront;

use App\Domains\Commerce\Models\CheckoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_online_payment_redirects_to_salla_checkout_when_available(): void
    {
        $this->withoutMiddleware();

        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response([
                'data' => ['checkout_url' => 'https://checkout.salla.sa/abc', 'cart_id' => 'CART-1'],
            ], 201),
        ]);

        session()->put('cart', [
            1 => [
                'product' => ['id' => 1, 'name' => 'ساعة فاخرة', 'price' => 520.0, 'sale_price' => null],
                'quantity' => 1,
                'color' => '',
                'gift' => ['enabled' => false],
            ],
        ]);

        $response = $this->post('/checkout', [
            'name' => 'أحمد',
            'phone' => '0555123456',
            'city' => 'الرياض',
            'address' => 'طريق الملك فهد',
            'payment_method' => 'mada',
        ]);

        $response->assertRedirect('https://checkout.salla.sa/abc');
        $this->assertSame(1, CheckoutSession::count());
        $this->assertSame('created', CheckoutSession::first()->status);
    }

    public function test_online_payment_falls_back_to_local_orders_when_checkout_unavailable(): void
    {
        Http::fake([
            'https://api.salla.dev/store/v2/checkout' => Http::response([], 500),
        ]);

        session()->put('cart', [
            1 => [
                'product' => ['id' => 1, 'name' => 'ساعة فاخرة', 'price' => 520.0],
                'quantity' => 1,
                'gift' => ['enabled' => false],
            ],
        ]);

        $response = $this->post('/checkout', [
            'name' => 'أحمد',
            'phone' => '0555123456',
            'city' => 'الرياض',
            'address' => 'طريق الملك فهد',
            'payment_method' => 'cc',
        ]);

        $this->assertMatchesRegularExpression('#^http://localhost:\d+/orders/\d+$#', $response->headers->get('Location'));
        $this->assertSame(1, CheckoutSession::count());
        $this->assertSame('failed', CheckoutSession::first()->status);
    }

    public function test_cod_payment_keeps_local_session_flow_without_checkout(): void
    {
        session()->put('cart', [
            1 => [
                'product' => ['id' => 1, 'name' => 'ساعة فاخرة', 'price' => 520.0],
                'quantity' => 1,
                'gift' => ['enabled' => false],
            ],
        ]);

        $response = $this->post('/checkout', [
            'name' => 'أحمد',
            'phone' => '0555123456',
            'city' => 'الرياض',
            'address' => 'طريق الملك فهد',
            'payment_method' => 'cod',
        ]);

        $this->assertMatchesRegularExpression('#^http://localhost:\d+/orders/\d+$#', $response->headers->get('Location'));
        $this->assertSame(0, CheckoutSession::count());
    }
}