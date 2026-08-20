<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(): Response
    {
        $mockOrders = [
            [
                'id' => 1,
                'number' => '1001',
                'total' => 374.00,
                'subtotal' => 349.00,
                'shipping' => 25.00,
                'payment_method' => 'بطاقة مدى / Apple Pay',
                'status' => [
                    'value' => 'processing',
                    'label' => 'جاري التجهيز',
                    'color' => 'warning',
                ],
                'created_at' => now()->subDays(2)->format('Y-m-d H:i'),
            ],
        ];

        return Inertia::render('Customer/Orders', [
            'orders' => $mockOrders,
        ]);
    }

    public function show(int $id): Response
    {
        $saved = session()->get("order_$id");

        if ($saved !== null) {
            return Inertia::render('Customer/OrderDetail', [
                'order' => $saved,
                'isNew' => (bool) session()->pull('isNew', false),
            ]);
        }

        $order = [
            'id' => $id,
            'number' => '100'.$id,
            'total' => 374.00,
            'subtotal' => 349.00,
            'shipping' => 25.00,
            'payment_method' => 'بطاقة مدى / Apple Pay',
            'status' => [
                'value' => 'processing',
                'label' => 'جاري التجهيز',
                'color' => 'warning',
            ],
            'created_at' => now()->format('Y-m-d H:i'),
            'items' => [
                [
                    'id' => 1,
                    'quantity' => 1,
                    'product' => [
                        'id' => 1,
                        'name' => 'سلسلة ذهبية فاخرة',
                        'price' => 349.00,
                        'sale_price' => null,
                        'thumbnail_url' => '',
                        'category' => ['name' => 'سلاسل'],
                    ],
                    'gift' => [
                        'enabled' => false,
                        'recipient_name' => '',
                        'recipient_phone' => '',
                        'message' => '',
                        'hide_price' => false,
                    ],
                ],
            ],
            'shipping_name' => 'محمد العتيبي',
            'shipping_phone' => '+966500000000',
            'shipping_address' => 'حي الياسمين، الشارع العام',
            'shipping_city' => 'الرياض',
            'has_gifts' => false,
        ];

        return Inertia::render('Customer/OrderDetail', [
            'order' => $order,
            'isNew' => (bool) session()->pull('isNew', false),
        ]);
    }
}
