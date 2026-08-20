<?php

declare(strict_types=1);

use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {

    // ─── Cart ──────────────────────────────────────────────────────────────────
    Route::post('/cart/add', [CartController::class, 'add'])->name('api.cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('api.cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('api.cart.remove');
    Route::post('/cart/gift', [CartController::class, 'gift'])->name('api.cart.gift');

    // ─── Wishlist ──────────────────────────────────────────────────────────────
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('api.wishlist.toggle');

    // ─── Account ───────────────────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {
        Route::put('/account/update', [AccountController::class, 'updateProfile'])->name('api.account.update');
    });
});
