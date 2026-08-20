<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        // In production: User::withCount('orders')->withSum('orders', 'total')->paginate(20)
        $customers = [
            ['name' => 'محمد العتيبي',  'email' => 'm.otaibi@example.com',  'phone' => '+966500000000', 'ordersCount' => 3,  'totalSpent' => 1250],
            ['name' => 'مرام البارقي',  'email' => 'maram@example.com',     'phone' => '+966555555555', 'ordersCount' => 2,  'totalSpent' => 840],
            ['name' => 'خالد الشمري',  'email' => 'k.shamri@example.com',  'phone' => '+966511111111', 'ordersCount' => 5,  'totalSpent' => 3200],
            ['name' => 'نورة السبيعي', 'email' => 'noura@example.com',     'phone' => '+966522222222', 'ordersCount' => 1,  'totalSpent' => 165],
        ];

        return view('admin.customers.index', [
            'customers' => $customers,
        ]);
    }
}
