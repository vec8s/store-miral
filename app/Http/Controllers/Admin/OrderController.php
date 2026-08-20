<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): View
    {
        // In production: Order::with('customer')->latest()->paginate(20)
        $orders = $this->getMockOrders();

        return view('admin.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'label' => 'required|string|max:100',
            'color' => 'required|string|max:50',
        ]);

        // In production: Order::findOrFail($id)->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'orderId' => $id,
            'status' => $validated,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getMockOrders(): array
    {
        return [
            ['id' => 1, 'number' => '1001', 'shipping_name' => 'محمد العتيبي', 'shipping_phone' => '+966500000000', 'shipping_city' => 'الرياض', 'total' => 450, 'payment_method' => 'بطاقة مدى', 'status' => ['value' => 'processing', 'label' => 'جاري التجهيز', 'color' => 'warning']],
            ['id' => 2, 'number' => '1002', 'shipping_name' => 'مرام البارقي',  'shipping_phone' => '+966555555555', 'shipping_city' => 'جدة',    'total' => 280, 'payment_method' => 'بطاقة ائتمانية', 'status' => ['value' => 'delivered',  'label' => 'تم التوصيل',   'color' => 'success']],
            ['id' => 3, 'number' => '1003', 'shipping_name' => 'خالد الشمري',  'shipping_phone' => '+966511111111', 'shipping_city' => 'الدمام', 'total' => 780, 'payment_method' => 'Apple Pay', 'status' => ['value' => 'shipped',    'label' => 'تم الشحن',     'color' => 'info']],
        ];
    }
}
