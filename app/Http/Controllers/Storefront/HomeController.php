<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\SallaService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(): Response
    {
        $featured = $this->sallaService->getFeaturedProducts(8);
        $categories = $this->sallaService->getCategories();

        return Inertia::render('Customer/Home', [
            'categories' => $categories,
            'featured' => $featured,
            'bestSellers' => $this->sallaService->getBestSellers(8),
            'topRated' => $this->sallaService->getTopRated(8),
            'mostSearched' => $this->sallaService->getMostSearched(8),
            'products' => $this->sallaService->getProducts(),
            'banners' => $this->sallaService->getMockBanners(),
            'posts' => $this->sallaService->getMockPosts(),
            'coupons' => $this->sallaService->getMockCoupons(),
            'storeSettings' => [
                'free_shipping_min' => config('store.free_shipping_min'),
            ],
        ]);
    }
}
