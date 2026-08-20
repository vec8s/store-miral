<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SallaService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(): View
    {
        $syncStatus = $this->sallaService->getSyncStatus();
        $recentOrders = $this->getMockRecentOrders();

        $stats = [
            'revenue' => ['value' => '12,450', 'unit' => 'ر.س', 'change' => '+18%', 'trend' => 'up'],
            'orders' => ['value' => '34',      'unit' => 'طلب',  'change' => '+5%',  'trend' => 'up'],
            'products' => ['value' => $syncStatus['sallaProductsCount'] ?: 28, 'unit' => 'منتج', 'change' => '', 'trend' => 'neutral'],
            'customers' => ['value' => '120',     'unit' => 'عميل', 'change' => '+12%', 'trend' => 'up'],
        ];

        return view('admin.dashboard', [
            'syncStatus' => $syncStatus,
            'recentOrders' => $recentOrders,
            'stats' => $stats,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getMockRecentOrders(): array
    {
        return [
            ['id' => 1, 'number' => '1001', 'shipping_name' => 'محمد العتيبي', 'total' => 450, 'status' => ['value' => 'processing', 'label' => 'جاري التجهيز', 'color' => 'warning']],
            ['id' => 2, 'number' => '1002', 'shipping_name' => 'مرام البارقي',  'total' => 280, 'status' => ['value' => 'delivered',  'label' => 'تم التوصيل',   'color' => 'success']],
            ['id' => 3, 'number' => '1003', 'shipping_name' => 'خالد الشمري',  'total' => 780, 'status' => ['value' => 'shipped',    'label' => 'تم الشحن',     'color' => 'info']],
            ['id' => 4, 'number' => '1004', 'shipping_name' => 'نورة السبيعي', 'total' => 165, 'status' => ['value' => 'pending',    'label' => 'قيد المراجعة', 'color' => 'warning']],
        ];
    }
}
