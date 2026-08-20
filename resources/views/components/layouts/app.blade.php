<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'ميرال — متجر الحلي والهدايا الفاخرة' }}</title>
  <meta name="description" content="متجر ميرال — حلي وهدايا فاخرة: سلاسل، ساعات، بوكس هدايا وأكثر. جودة استثنائية مرتبطة بمنصة سلة.">
  <meta name="robots" content="index, follow">

  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="ميرال">
  <meta property="og:title" content="{{ $title ?? 'ميرال — متجر الحلي والهدايا الفاخرة' }}">
  <meta property="og:description" content="متجر ميرال — حلي وهدايا فاخرة: سلاسل، ساعات، بوكس هدايا وأكثر. جودة استثنائية مرتبطة بمنصة سلة.">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta name="twitter:card" content="summary_large_image">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full bg-[#f4f4f5] text-[#18181b]" x-data="{ mobileOpen: false, searchOpen: false, toastMessage: '', showToast: false }">

  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-[#ececee]">
    <div class="container-rtl">
      <div class="flex items-center justify-between h-20">

        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
          <div class="w-10 h-10 rounded-[14px] bg-[#09090b] text-white flex items-center justify-center text-lg font-bold border border-[#2c2e34]">
            م
          </div>
          <div class="flex flex-col">
            <span class="text-xl font-bold text-[#09090b] leading-tight tracking-tight">ميرال</span>
            <span class="hidden sm:block text-[11px] text-[#71717a] font-normal">متجر الحلي والهدايا</span>
          </div>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-[#18181b]">
          <a href="{{ route('home') }}" class="hover:text-[#09090b] transition {{ request()->routeIs('home') ? 'text-[#09090b] font-bold underline underline-offset-8 decoration-2 decoration-[#ff5a00]' : 'text-[#52525b]' }}">الرئيسية</a>
          <a href="{{ route('shop.index') }}" class="hover:text-[#09090b] transition {{ request()->routeIs('shop.*') ? 'text-[#09090b] font-bold underline underline-offset-8 decoration-2 decoration-[#ff5a00]' : 'text-[#52525b]' }}">المتجر</a>
          <a href="{{ route('categories.index') }}" class="hover:text-[#09090b] transition {{ request()->routeIs('categories.*') ? 'text-[#09090b] font-bold underline underline-offset-8 decoration-2 decoration-[#ff5a00]' : 'text-[#52525b]' }}">الأقسام</a>
          <a href="{{ route('about') }}" class="hover:text-[#09090b] transition {{ request()->routeIs('about') ? 'text-[#09090b] font-bold underline underline-offset-8 decoration-2 decoration-[#ff5a00]' : 'text-[#52525b]' }}">من نحن</a>
          <a href="{{ route('contact') }}" class="hover:text-[#09090b] transition {{ request()->routeIs('contact') ? 'text-[#09090b] font-bold underline underline-offset-8 decoration-2 decoration-[#ff5a00]' : 'text-[#52525b]' }}">تواصل معنا</a>
        </nav>

        <!-- Header Actions -->
        <div class="flex items-center gap-1.5 sm:gap-2.5">
          <!-- Search Toggle -->
          <button @click="searchOpen = !searchOpen" class="w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] flex items-center justify-center text-[#18181b] hover:bg-[#f4f4f5] transition" title="بحث">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
            </svg>
          </button>

          <!-- Wishlist -->
          <a href="{{ route('wishlist.index') }}" class="hidden sm:flex relative w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] items-center justify-center text-[#18181b] hover:bg-[#f4f4f5] transition" title="المفضلة">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            @if(session('wishlist_count', 0) > 0)
              <span id="wishlist-badge" class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] px-1 rounded-full bg-[#ff5a00] text-white text-[11px] font-bold flex items-center justify-center">
                {{ session('wishlist_count') }}
              </span>
            @endif
          </a>

          <!-- Cart -->
          <a href="{{ route('cart.index') }}" class="relative w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] flex items-center justify-center text-[#18181b] hover:bg-[#f4f4f5] transition" title="السلة">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span id="cart-badge" class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] px-1 rounded-full bg-[#09090b] text-white text-[11px] font-bold flex items-center justify-center {{ session('cart_count', 0) === 0 ? 'hidden' : '' }}">
              {{ session('cart_count', 0) }}
            </span>
          </a>

          <!-- User Orders Icon -->
          <a href="{{ route('orders.index') }}" class="hidden sm:flex relative w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] items-center justify-center text-[#18181b] hover:bg-[#f4f4f5] transition" title="طلباتي">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </a>

          <!-- User Profile / Auth Dropdown -->
          @auth
            <div class="relative" x-data="{ userMenuOpen: false }">
              <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] flex items-center justify-center text-[#18181b] hover:bg-[#f4f4f5] transition" title="الحساب الشخصي">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div x-show="userMenuOpen" x-transition x-cloak class="absolute left-0 mt-2 w-52 bg-white border border-[#ececee] rounded-[20px] shadow-xl p-2 z-50">
                <div class="px-3.5 py-2.5 border-b border-[#ececee] mb-1.5">
                  <p class="text-xs font-bold text-[#09090b]">{{ auth()->user()->name }}</p>
                  <p class="text-[11px] text-[#71717a] truncate mt-0.5">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[44px] rounded-[12px] text-xs font-medium text-[#18181b] hover:bg-[#f4f4f5] transition">
                  <svg class="w-4 h-4 text-[#71717a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  الملف الشخصي
                </a>
                <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[44px] rounded-[12px] text-xs font-medium text-[#18181b] hover:bg-[#f4f4f5] transition">
                  <svg class="w-4 h-4 text-[#71717a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  طلباتي
                </a>
                @if(auth()->user()->is_admin ?? false)
                  <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 min-h-[44px] rounded-[12px] text-xs font-bold text-[#ff5a00] hover:bg-[#fafafa] transition">
                    <svg class="w-4 h-4 text-[#ff5a00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    لوحة تحكم الإدارة
                  </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button type="submit" class="w-full text-right flex items-center gap-2.5 px-3 py-2.5 min-h-[44px] rounded-[12px] text-xs font-bold text-red-600 hover:bg-red-50 transition mt-1 border-t border-[#ececee]">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    تسجيل الخروج
                  </button>
                </form>
              </div>
            </div>
          @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs bg-[#09090b] text-white px-3.5 py-2.5 min-h-[44px] rounded-[14px] font-medium hover:bg-[#27272a] transition whitespace-nowrap" title="تسجيل الدخول">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              <span>تسجيل الدخول</span>
            </a>
          @endauth

          <!-- Admin Link Pill -->
          @if(auth()->check() && (auth()->user()->is_admin ?? false))
            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs bg-[#fafafa] text-[#18181b] border border-[#ececee] px-3.5 py-2.5 min-h-[44px] rounded-[10000px] font-medium hover:bg-[#f4f4f5] transition">
              <span class="w-1.5 h-1.5 rounded-full bg-[#ff5a00]"></span>
              لوحة الإدارة
            </a>
          @endif

          <!-- Mobile Menu Button -->
          <button @click="mobileOpen = !mobileOpen" class="lg:hidden w-10 h-10 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] flex items-center justify-center text-[#18181b]">
            <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Search Slide Down -->
      <div x-show="searchOpen" x-transition x-cloak class="pb-4">
        <form action="{{ route('shop.index') }}" method="GET" class="relative">
          <input type="search" name="q" value="{{ request('q') }}" placeholder="ابحث عن سلسلة، ساعة، بوكس هدية..." class="input-awesomic pr-11 py-3 text-sm">
          <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[#71717a] hover:text-[#09090b]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
            </svg>
          </button>
        </form>
      </div>

      <!-- Mobile Menu Nav -->
      <nav x-show="mobileOpen" x-transition x-cloak class="lg:hidden pb-5 space-y-1">
        <a href="{{ route('home') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">الرئيسية</a>
        <a href="{{ route('shop.index') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">المتجر</a>
        <a href="{{ route('categories.index') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">الأقسام</a>
        <a href="{{ route('about') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">من نحن</a>
        <a href="{{ route('contact') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">تواصل معنا</a>

        <div class="pt-3 border-t border-[#ececee] space-y-1">
          @auth
            <a href="{{ route('account.profile') }}" class="flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">
              <span>👤</span> الملف الشخصي ({{ auth()->user()->name }})
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-[14px] text-[#18181b] hover:bg-[#fafafa] font-medium text-sm">
              <span>📦</span> طلباتي
            </a>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="w-full text-right flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-[14px] text-red-600 hover:bg-red-50 font-bold text-sm">
                <span>🚪</span> تسجيل الخروج
              </button>
            </form>
          @else
            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-[14px] bg-[#09090b] text-white font-medium text-sm justify-center">
              <span>🔑</span> تسجيل الدخول
            </a>
            <a href="{{ route('register') }}" class="flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-[14px] border border-[#ececee] text-[#18181b] font-medium text-sm justify-center">
              <span>✨</span> إنشاء حساب جديد
            </a>
          @endauth
          @if(auth()->check() && (auth()->user()->is_admin ?? false))
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 min-h-[44px] rounded-[14px] bg-[#fafafa] border border-[#ececee] text-[#18181b] font-medium text-sm text-center mt-2">
              لوحة تحكم الإدارة
            </a>
          @endif
        </div>
      </nav>
    </div>
  </header>

  <!-- Notification Toast -->
  <div x-show="showToast" x-transition x-cloak class="fixed bottom-4 inset-x-4 sm:inset-x-auto sm:left-6 z-50 bg-[#09090b] text-white px-5 py-3.5 rounded-[14px] border border-[#2c2e34] shadow-2xl flex items-center gap-3 text-sm sm:max-w-md">
    <span class="w-2 h-2 rounded-full bg-[#ff5a00]"></span>
    <span x-text="toastMessage"></span>
    <button @click="showToast = false" class="text-[#71717a] hover:text-white mr-2">&times;</button>
  </div>

  <!-- Page Main Content -->
  <main class="flex-grow">
    {{ $slot }}
  </main>

  <!-- Footer -->
  <footer class="bg-[#18181b] text-[#a1a1aa] mt-24 pt-16 pb-12 border-t border-[#27272a]">
    <div class="container-rtl grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12">
      
      <div>
        <div class="flex items-center gap-3 mb-5">
          <div class="w-9 h-9 rounded-[12px] bg-white text-[#09090b] flex items-center justify-center font-bold text-base">
            م
          </div>
          <span class="text-xl font-bold text-white tracking-tight">ميرال</span>
        </div>
        <p class="text-sm text-[#71717a] leading-relaxed mb-6">
          منصة حلي وهدايا فاخرة توفر تشكيلات عصرية بمعايير استثنائية مرتبطة بالكامل مع منصة سلة.
        </p>
        <div class="flex items-center gap-2">
          <span class="badge-ember">Salla Sync Active</span>
        </div>
      </div>

      <div>
        <h4 class="text-white font-semibold text-sm mb-4">روابط سريعة</h4>
        <ul class="space-y-2.5 text-sm">
          <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">جميع المنتجات</a></li>
          <li><a href="{{ route('categories.index') }}" class="hover:text-white transition">تصفح الأقسام</a></li>
          <li><a href="{{ route('cart.index') }}" class="hover:text-white transition">سلة التسوق</a></li>
          <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition">قائمة المفضلة</a></li>
          <li><a href="{{ route('orders.index') }}" class="hover:text-white transition">متابعة الطلبات</a></li>
        </ul>
      </div>

      <div>
        <h4 class="text-white font-semibold text-sm mb-4">خدمة العملاء</h4>
        <ul class="space-y-2.5 text-sm">
          <li><a href="{{ route('about') }}" class="hover:text-white transition">من نحن</a></li>
          <li><a href="{{ route('contact') }}" class="hover:text-white transition">تواصل معنا</a></li>
          <li><span class="text-[#71717a]">الرياض — المملكة العربية السعودية</span></li>
          <li><span class="text-[#71717a]">support@miral.sa</span></li>
        </ul>
      </div>

      <div>
        <h4 class="text-white font-semibold text-sm mb-4">طرق الدفع والتكامل</h4>
        <p class="text-xs text-[#71717a] leading-relaxed mb-4">
          يتم تحصيل الدفع ومعالجة العمليات عبر بوابة سلة الرسمية المعتمدة.
        </p>
        <div class="flex flex-wrap gap-2 text-xs font-normal">
          <span class="badge-tag border-[#3f3f46] text-white">مدى</span>
          <span class="badge-tag border-[#3f3f46] text-white"> Pay</span>
          <span class="badge-tag border-[#3f3f46] text-white">VISA</span>
          <span class="badge-tag border-[#3f3f46] text-white">MasterCard</span>
          <span class="badge-ember">سلة Salla</span>
        </div>
      </div>

    </div>

    <div class="container-rtl mt-16 pt-8 border-t border-[#27272a] flex flex-col sm:flex-row justify-between items-center text-xs text-[#71717a] gap-4">
      <div> جميع الحقوق محفوظة © 2026 — متجر ميرال</div>
      <div class="flex items-center gap-4">
        <span>Laravel 13 Storefront</span>
        <span>•</span>
        <span>Salla Integrated</span>
      </div>
    </div>
  </footer>
</body>
</html>
