<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Storefront\Concerns\NormalizesGift;
use App\Services\SallaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    use NormalizesGift;

    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(Request $request): Response
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

        session()->put('cart_count', count($cart));

        return Inertia::render('Customer/Cart', [
            'cart' => array_values($cart),
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
            'storeSettings' => [
                'free_shipping_min' => $freeShippingMin,
            ],
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $quantity = max(1, (int) $request->input('quantity', 1));

        $product = $this->sallaService->getProductById($productId);
        if (! $product) {
            return response()->json(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => $quantity,
                'color' => (string) $request->input('color', ''),
                'gift' => [
                    'enabled' => (bool) $request->boolean('gift_enabled'),
                    'recipient_name' => (string) $request->input('gift_recipient_name', ''),
                    'recipient_phone' => (string) $request->input('gift_recipient_phone', ''),
                    'message' => (string) $request->input('gift_message', ''),
                    'hide_price' => (bool) $request->boolean('gift_hide_price'),
                ],
            ];
        }

        session()->put('cart', $cart);
        session()->put('cart_count', count($cart));

        return response()->json([
            'success' => true,
            'cartCount' => count($cart),
            'message' => 'تمت إضافتها لسلة التسوق بنجاح',
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $cart = session()->get('cart', []);
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
        }

        session()->put('cart', $cart);
        session()->put('cart_count', count($cart));

        return response()->json([
            'success' => true,
            'cartCount' => count($cart),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');

        $cart = session()->get('cart', []);
        unset($cart[$productId]);

        session()->put('cart', $cart);
        session()->put('cart_count', count($cart));

        return response()->json([
            'success' => true,
            'cartCount' => count($cart),
        ]);
    }

    public function gift(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');

        $cart = session()->get('cart', []);
        if (! isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => 'المنتج غير موجود في السلة'], 404);
        }

        $cart[$productId]['gift'] = [
            'enabled' => (bool) $request->boolean('enabled'),
            'recipient_name' => (string) $request->input('recipient_name', ''),
            'recipient_phone' => (string) $request->input('recipient_phone', ''),
            'message' => (string) $request->input('message', ''),
            'hide_price' => (bool) $request->boolean('hide_price'),
        ];

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => $cart[$productId]['gift']['enabled']
                ? 'تم تفعيل الإهداء لهذا المنتج'
                : 'تم إلغاء الإهداء لهذا المنتج',
        ]);
    }
}
