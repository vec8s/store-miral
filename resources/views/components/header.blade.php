@php
    $cartCount = 0;
    $wishlistCount = 0;
@endphp

<header x-data="{ mobileOpen: false, searchOpen: false }"
        class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-100">
    <div class="container-rtl">
        <div class="flex items-center justify-between h-16">

            <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-gold-500 flex items-center justify-center text-white text-xl font-bold shadow-soft">
                    ر
                </div>
                <span class="hidden sm:block text-xl font-bold text-gray-900">رافال</span>
            </a>

            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-brand-600 transition">الرئيسية</a>
                <a href="{{ route('shop.index') }}" class="text-gray-700 hover:text-brand-600 transition">المتجر</a>
                <a href="{{ url('/categories') }}" class="text-gray-700 hover:text-brand-600 transition">الأقسام</a>
                <a href="{{ url('/about') }}" class="text-gray-700 hover:text-brand-600 transition">من نحن</a>
                <a href="{{ url('/contact') }}" class="text-gray-700 hover:text-brand-600 transition">تواصل معنا</a>
            </nav>

            <div class="flex items-center gap-1">
                <button @click="searchOpen = !searchOpen" class="p-2 rounded-lg hover:bg-gray-100" aria-label="بحث">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('wishlist.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100" aria-label="المفضلة">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100" aria-label="السلة">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>

                @auth
                    <a href="{{ route('account.profile') }}" class="p-2 rounded-lg hover:bg-gray-100" aria-label="حسابي">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex btn-outline text-xs px-3 py-1.5">دخول</a>
                @endauth

                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100" aria-label="القائمة">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="searchOpen" x-transition x-cloak class="pb-4">
            <form action="{{ route('shop.index') }}" method="GET" class="relative">
                <input type="search" name="q" placeholder="ابحث عن منتج..." class="input pr-11 py-3">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </button>
            </form>
        </div>

        <nav x-show="mobileOpen" x-transition x-cloak class="lg:hidden pb-4 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-brand-50 hover:text-brand-700">الرئيسية</a>
            <a href="{{ route('shop.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-brand-50 hover:text-brand-700">المتجر</a>
            <a href="{{ url('/categories') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-brand-50 hover:text-brand-700">الأقسام</a>
            <a href="{{ url('/about') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-brand-50 hover:text-brand-700">من نحن</a>
            <a href="{{ url('/contact') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-brand-50 hover:text-brand-700">تواصل معنا</a>
            @guest
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg bg-brand-600 text-white text-center mt-3">تسجيل الدخول</a>
            @endguest
        </nav>
    </div>
</header>
