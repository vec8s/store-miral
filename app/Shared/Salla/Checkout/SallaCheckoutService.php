<?php

declare(strict_types=1);

namespace App\Shared\Salla\Checkout;

use App\Domains\Commerce\Models\Cart;
use App\Domains\Commerce\Models\CheckoutSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Creates a Salla hosted checkout session for the storefront cart.
 *
 * The store checkout API lives on a separate base URL from the admin API and
 * does not use the merchant OAuth token, so requests are issued directly.
 *
 * The response shape of Salla's store checkout endpoint is not formally
 * documented, so the response is parsed defensively: the service looks for a
 * redirectable checkout URL and a cart identifier in the response body, and
 * degrades to a local fallback when Salla is unreachable.
 */
final class SallaCheckoutService
{
    public function __construct() {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $customer
     * @param  array<string, mixed>  $shipping
     */
    public function createSession(
        array $items,
        array $customer,
        array $shipping,
        ?int $userId = null,
    ): CheckoutSession {
        $cart = Cart::create([
            'user_id' => $userId,
            'session_id' => session()->getId(),
            'version' => 1,
            'currency' => (string) ($customer['currency'] ?? 'SAR'),
            'meta' => ['items' => $items],
        ]);

        $uuid = (string) Str::uuid();
        $idempotencyKey = $this->idempotencyKey($items, $customer);
        $amount = $this->cartTotal($items);

        $session = CheckoutSession::create([
            'uuid' => $uuid,
            'user_id' => $userId,
            'cart_id' => $cart->id,
            'version' => 1,
            'idempotency_key' => $idempotencyKey,
            'amount_snapshot' => ['subtotal' => $amount['subtotal'], 'total' => $amount['total']],
            'currency' => (string) ($customer['currency'] ?? 'SAR'),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        try {
            $response = Http::timeout((int) config('salla.http.timeout', 30))
                ->acceptJson()
                ->asJson()
                ->post($this->endpoint(), [
                    'items' => $items,
                    'customer' => $customer,
                    'shipping' => $shipping,
                    'redirect_after_payment' => route('home'),
                    'redirect_after_cancel' => route('cart.index'),
                ]);

            if ($response->successful()) {
                $body = $response->json();

                if (is_array($body)) {
                    $this->applySallaResponse($session, $body);
                }
            } else {
                $session->update(['status' => 'failed']);
            }
        } catch (Throwable $e) {
            $session->update(['status' => 'failed']);
        }

        return $session->refresh();
    }

    private function endpoint(): string
    {
        return rtrim((string) config('salla.checkout_base_url', 'https://api.salla.dev/store/v2/checkout'), '/');
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function applySallaResponse(CheckoutSession $session, array $body): void
    {
        $data = is_array($body['data'] ?? null) ? $body['data'] : $body;

        $checkoutUrl = $data['checkout_url'] ?? $data['url'] ?? $body['checkout_url'] ?? null;
        $cartId = $data['cart_id'] ?? $data['id'] ?? $body['cart_id'] ?? null;

        $update = ['status' => 'created'];

        if (is_string($checkoutUrl) && $checkoutUrl !== '') {
            $update['checkout_url'] = $checkoutUrl;
        }

        if ($cartId !== null) {
            $update['salla_cart_id'] = (string) $cartId;
        }

        $session->update($update);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function idempotencyKey(array $items, array $customer): string
    {
        $signature = md5(serialize([$items, $customer]));

        return 'chk-'.substr($signature, 0, 16).'-'.time();
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{subtotal: int, total: int}
     */
    private function cartTotal(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 1);
            $subtotal += $price * $qty;
        }

        $subtotalMinor = (int) round($subtotal * 100);

        return ['subtotal' => $subtotalMinor, 'total' => $subtotalMinor];
    }
}
