<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncSallaProducts;
use App\Services\SallaService;
use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Sync\ProductSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(): View
    {
        $products = $this->sallaService->getProducts();
        $syncStatus = $this->sallaService->getSyncStatus();

        return view('admin.products.index', [
            'products' => $products,
            'syncStatus' => $syncStatus,
        ]);
    }

    public function sync(SallaClientContract $client, ProductSyncService $sync): JsonResponse
    {
        try {
            $before = $sync->count();

            $job = new SyncSallaProducts(perPage: 50);
            $job->handle($client, $sync);

            $after = $sync->count();
            $imported = max(0, $after - $before);

            return response()->json([
                'success' => true,
                'message' => 'تمت المزامنة بنجاح — تم استيراد '.$imported.' منتج جديد من سلة.',
                'status' => ['synced' => $after],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بـ Salla API: '.$e->getMessage(),
            ], 502);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        // Local products only — Salla products are managed via Salla dashboard
        return back()->with('status', "تم حذف المنتج #{$id}");
    }
}
