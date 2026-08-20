<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Storefront\Concerns\NormalizesGift;
use App\Services\SallaService;
use App\Shared\Salla\Checkout\SallaCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    use NormalizesGift;

    public function __construct(
        protected SallaService $sallaService,
        protected SallaCheckoutService $sallaCheckoutService
    ) {}

    public function index(): Response
    {
        $cart = session()->get('cart', []);
        $cart = $this->normalizeGift($cart);
        $subtotal = 0;

        foreach ($cart as $item) {
            $price = data_get($item, 'product.sale_price') ?: data_get($item, 'product.price', 0);
            $subtotal += $price * data_get($item, 'quantity', 1);
        }

        $freeShippingMin = config('store.free_shipping_min');
        $shipping = ($subtotal >= $freeShippingMin || $subtotal === 0) ? 0 : config('store.shipping_fee');
        $total = $subtotal + $shipping;

        return Inertia::render('Customer/Checkout', [
            'cart' => array_values($cart),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'codFee' => config('store.cod_fee'),
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|string|max:100',
        ]);

        $cart = session()->get('cart', []);
        $cart = $this->normalizeGift($cart);
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = data_get($item, 'product.sale_price') ?: data_get($item, 'product.price', 0);
            $subtotal += $price * data_get($item, 'quantity', 1);
        }

        $shipping = ($subtotal >= config('store.free_shipping_min') || $subtotal === 0) ? 0 : config('store.shipping_fee');
        $codFee = $request->input('payment_method') === 'cod' ? config('store.cod_fee') : 0;
        $total = $subtotal + $shipping + $codFee;

        $orderId = rand(1000, 9999);

        // Keep a snapshot of the order (session-based, as agreed) including gift data
        session()->put("order_$orderId", [
            'number' => '100'.$orderId,
            'total' => $total,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'cod_fee' => $codFee,
            'payment_method' => $request->input('payment_method'),
            'status' => ['value' => 'unconfirmed', 'label' => 'الطلب غير مؤكد'],
            'created_at' => now()->format('Y-m-d H:i'),
            'items' => array_values($cart),
            'shipping_name' => $request->input('name'),
            'shipping_phone' => $request->input('phone'),
            'shipping_address' => $request->input('address'),
            'shipping_city' => $request->input('city'),
            'has_gifts' => collect($cart)->contains(fn ($i) => ($i['gift']['enabled'] ?? false)),
        ]);

        // Clear cart
        session()->forget('cart');
        session()->put('cart_count', 0);

        // For online payments, hand the cart to the Salla hosted checkout when
        // it produces a redirectable URL; otherwise keep the local session flow.
        if ($this->isOnlinePayment($request->input('payment_method'))) {
            $items = $this->checkoutItems($cart);

            $checkout = $this->sallaCheckoutService->createSession(
                items: $items,
                customer: [
                    'name' => (string) $request->input('name'),
                    'phone' => (string) $request->input('phone'),
                    'currency' => 'SAR',
                ],
                shipping: [
                    'city' => (string) $request->input('city'),
                    'address' => (string) $request->input('address'),
                ],
            );

            if ($checkout->status === 'created' && $checkout->checkout_url !== null) {
                return redirect()->away($checkout->checkout_url);
            }
        }

        return redirect()->route('orders.show', $orderId)->with('isNew', true);
    }

    private function isOnlinePayment(?string $method): bool
    {
        return in_array((string) $method, ['mada', 'cc', 'credit_card', 'stc_pay', 'apple_pay'], true);
    }

    /**
     * @param  array<int, array<string, mixed>>  $cart
     * @return array<int, array<string, mixed>>
     */
    private function checkoutItems(array $cart): array
    {
        return collect($cart)->map(function (array $item): array {
            $product = $item['product'] ?? [];

            return [
                'product_id' => (int) ($product['id'] ?? 0),
                'name' => (string) ($product['name'] ?? ''),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'price' => (float) (data_get($item, 'product.sale_price') ?: data_get($item, 'product.price', 0)),
            ];
        })->values()->all();
    }
}
