<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\SallaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(Request $request): Response
    {
        $category = $request->query('category');
        $query = $request->query('q');

        $products = $this->sallaService->getProducts($category, $query);
        $categories = $this->sallaService->getCategories();

        return Inertia::render('Customer/Shop', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $category,
            'searchQuery' => $query,
        ]);
    }

    public function show(int $id): Response
    {
        $product = $this->sallaService->getProductById($id);
        $relatedProducts = $this->sallaService->getRelatedProducts($product);
        $boughtTogether = $this->sallaService->getBoughtTogether($product);
        $storeInfo = $this->sallaService->getStoreInfo();

        return Inertia::render('Customer/Product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'boughtTogether' => $boughtTogether,
            'storeInfo' => $storeInfo,
        ]);
    }
}
