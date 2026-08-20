<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\SallaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(): Response
    {
        $wishlistIds = session()->get('wishlist', []);
        $products = [];

        foreach ($wishlistIds as $id) {
            $p = $this->sallaService->getProductById((int) $id);
            if ($p) {
                $products[] = $p;
            }
        }

        session()->put('wishlist_count', count($wishlistIds));

        return Inertia::render('Customer/Wishlist', [
            'wishlist' => $products,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $wishlist = session()->get('wishlist', []);

        $added = false;
        if (in_array($productId, $wishlist, true)) {
            $wishlist = array_values(array_filter($wishlist, fn ($id) => $id !== $productId));
        } else {
            $wishlist[] = $productId;
            $added = true;
        }

        session()->put('wishlist', $wishlist);
        session()->put('wishlist_count', count($wishlist));

        return response()->json([
            'success' => true,
            'added' => $added,
            'wishlistCount' => count($wishlist),
        ]);
    }
}
