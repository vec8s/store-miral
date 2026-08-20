<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Identity\Enums\RoleCode;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'is_admin' => $request->user()->hasAnyRole([
                        RoleCode::SuperAdmin,
                        RoleCode::Admin,
                        RoleCode::Manager,
                    ]),
                ] : null,
            ],
            'cartCount' => fn () => (int) $request->session()->get('cart_count', 0),
            'wishlistCount' => fn () => (int) $request->session()->get('wishlist_count', 0),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
