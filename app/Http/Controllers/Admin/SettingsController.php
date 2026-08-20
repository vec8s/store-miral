<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SallaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected SallaService $sallaService
    ) {}

    public function index(): View
    {
        $syncStatus = $this->sallaService->getSyncStatus();

        $settings = [
            'store_name' => config('store.store_name'),
            'store_phone' => config('store.store_phone'),
            'store_email' => config('store.store_email'),
            'shipping_fee' => config('store.shipping_fee'),
            'free_shipping_min' => config('store.free_shipping_min'),
            'salla_merchant' => 'Miral Store — Salla Sync',
        ];

        return view('admin.settings', [
            'settings' => $settings,
            'syncStatus' => $syncStatus,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_phone' => 'required|string|max:50',
            'store_email' => 'required|email|max:255',
            'shipping_fee' => 'required|numeric|min:0',
            'free_shipping_min' => 'required|numeric|min:0',
        ]);

        // In production: persist to Settings model or .env via a secure writer
        // For now we return with a success flash
        return back()->with('status', 'تم حفظ الإعدادات بنجاح');
    }
}
