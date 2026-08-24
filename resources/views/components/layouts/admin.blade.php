<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'لوحة تحكم الإدارة — ميرال' }}</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full bg-[#f4f4f5] text-[#18181b] antialiased">

  <!-- Admin Main Navigation Header -->
  <header class="bg-[#18181b] text-white border-b border-[#27272a] sticky top-0 z-40 shadow-sm">
    <div class="container-rtl">
      <div class="flex items-center justify-between h-16 gap-3">
        
        <div class="flex items-center gap-3 min-w-0">
          <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-bold text-lg text-white whitespace-nowrap">
            <span class="w-8 h-8 rounded-lg bg-[#ff5a00] text-white flex items-center justify-center text-sm font-black shadow-sm">م</span>
            <span class="font-extrabold tracking-tight">ميرال — الإدارة</span>
          </a>
          <span class="text-[11px] bg-[#ff5a00]/15 text-[#ff5a00] font-semibold px-2.5 py-0.5 rounded-full border border-[#ff5a00]/30 hidden sm:inline-flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-[#ff5a00]"></span>
            تكامل منصة سلة
          </span>
        </div>

        <div class="flex items-center gap-3 sm:gap-4 text-xs shrink-0">
          <a href="{{ route('home') }}" target="_blank" class="text-zinc-300 hover:text-white flex items-center gap-1.5 py-2 px-3 rounded-lg hover:bg-zinc-800 transition whitespace-nowrap">
            <span>🌐</span> <span>معاينة المتجر</span>
          </a>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 px-3 py-2 rounded-lg font-bold min-h-[36px] transition whitespace-nowrap">
              تسجيل الخروج
            </button>
          </form>
        </div>

      </div>
    </div>
  </header>

  <!-- Admin Body Container -->
  <main class="flex-grow py-6 sm:py-8 bg-[#f4f4f5]">
    <div class="container-rtl">
      <!-- Top Admin Nav Tabs -->
      <div class="flex flex-wrap gap-2 text-xs font-bold mb-6 sm:mb-8 p-1.5 bg-white border border-[#ececee] rounded-[16px] shadow-sm">
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 min-h-[40px] rounded-[12px] transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-[#09090b] text-white shadow-sm' : 'text-[#52525b] hover:text-[#09090b] hover:bg-[#f4f4f5]' }}">
          <span>📊</span> لوحة التحكم
        </a>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 min-h-[40px] rounded-[12px] transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('admin.products.*') ? 'bg-[#09090b] text-white shadow-sm' : 'text-[#52525b] hover:text-[#09090b] hover:bg-[#f4f4f5]' }}">
          <span>💎</span> المنتجات
        </a>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 min-h-[40px] rounded-[12px] transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('admin.orders.*') ? 'bg-[#09090b] text-white shadow-sm' : 'text-[#52525b] hover:text-[#09090b] hover:bg-[#f4f4f5]' }}">
          <span>📦</span> الطلبات
        </a>
        <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 min-h-[40px] rounded-[12px] transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('admin.customers.*') ? 'bg-[#09090b] text-white shadow-sm' : 'text-[#52525b] hover:text-[#09090b] hover:bg-[#f4f4f5]' }}">
          <span>👥</span> العملاء
        </a>
        <a href="{{ route('admin.settings') }}" class="px-4 py-2.5 min-h-[40px] rounded-[12px] transition flex items-center gap-1.5 whitespace-nowrap {{ request()->routeIs('admin.settings') ? 'bg-[#09090b] text-white shadow-sm' : 'text-[#52525b] hover:text-[#09090b] hover:bg-[#f4f4f5]' }}">
          <span>⚙️</span> إعدادات الربط
        </a>
      </div>

      {{ $slot }}
    </div>
  </main>

  <footer class="py-5 text-center text-xs text-[#71717a] border-t border-[#ececee] bg-white">
    متجر ميرال — لوحة الإدارة المتكاملة مع سلة © 2026
  </footer>
</body>
</html>
