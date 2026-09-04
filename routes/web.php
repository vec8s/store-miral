<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Salla\SallaOAuthController;
use App\Http\Controllers\Salla\SallaWebhookController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\ShopController;
use App\Http\Controllers\Storefront\SocialAuthController;
use App\Http\Controllers\Storefront\WishlistController;
use App\Services\SallaService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Home & Static ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', function (SallaService $salla) {
    return Inertia::render('Customer/About');
})->name('about');
Route::get('/contact', function () {
    return Inertia::render('Customer/Contact');
})->name('contact');
Route::get('/categories', function (SallaService $salla) {
    return Inertia::render('Customer/Categories', [
        'categories' => $salla->getCategories(),
    ]);
})->name('categories.index');
Route::get('/faq', fn () => view('static.faq'))->name('faq');
Route::get('/shipping', fn () => view('static.shipping'))->name('shipping');
Route::get('/returns', fn () => view('static.returns'))->name('returns');
Route::get('/privacy', fn () => view('static.privacy'))->name('privacy');
Route::get('/terms', fn () => view('static.terms'))->name('terms');
Route::get('/track-order', fn () => view('static.track-order'))->name('track-order');

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'),
        route('shop.index'),
        route('categories.index'),
        route('about'),
        route('contact'),
        route('faq'),
        route('shipping'),
        route('returns'),
        route('privacy'),
        route('terms'),
    ];

    $today = now()->toDateString();

    return response()
        ->view('static.sitemap', [
            'urls' => $urls,
            'lastmod' => $today,
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow:\n\nSitemap: ".url('/sitemap.xml')."\n",
    200,
    ['Content-Type' => 'text/plain'],
))->name('robots');

// ─── Shop ─────────────────────────────────────────────────────────────────────
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{id}', [ShopController::class, 'show'])->where('id', '[0-9]+')->name('shop.show');

// ─── Cart ─────────────────────────────────────────────────────────────────────
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

// ─── Wishlist ─────────────────────────────────────────────────────────────────
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// ─── Checkout ─────────────────────────────────────────────────────────────────
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

// ─── Orders ───────────────────────────────────────────────────────────────────
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'show'])->where('id', '[0-9]+')->name('orders.show');

// ─── Account ──────────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// ─── Auth ─────────────────────────────────────────────────────────────────────
// GET views (مخصصة حسب تصميم المتجر) — المعالجة الفعلية (POST) عبر Fortify routes.
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

// ─── Social Login (Google / Apple) ────────────────────────────────────────────
Route::prefix('auth')->middleware('guest')->group(function () {
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'apple'])
        ->name('auth.social.redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'apple'])
        ->name('auth.social.callback');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::post('/{id}/status', [AdminOrderController::class, 'updateStatus'])->where('id', '[0-9]+')->name('status');
    });

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Salla Sync & OAuth
    Route::prefix('sync')->name('sync.')->group(function () {
        Route::post('/run', [AdminProductController::class, 'sync'])->name('run');
    });

    Route::prefix('integrations/salla')->name('salla.')->group(function () {
        Route::get('/connect', [SallaOAuthController::class, 'connect'])->name('connect');
        Route::get('/callback', [SallaOAuthController::class, 'callback'])->name('callback');
    });

    Route::get('/migrate', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء وتحديث كافة جداول قاعدة البيانات بنجاح!',
                'output' => \Illuminate\Support\Facades\Artisan::output(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('migrate');
});

require __DIR__.'/settings.php';

// ─── Salla OAuth Callback Alias ───────────────────────────────────────────────
Route::get('/salla/callback', [SallaOAuthController::class, 'callback'])->name('salla.callback');

// ─── Salla Webhook (ingress) ──────────────────────────────────────────────────
// CSRF is skipped because Salla signs the body with HMAC-SHA256 instead of
// carrying a session token. Authenticity is verified in the controller.
Route::post('/webhooks/salla', SallaWebhookController::class)
    ->name('salla.webhook')
    ->withoutMiddleware(ValidateCsrfToken::class);
