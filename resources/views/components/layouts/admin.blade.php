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
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full bg-[#f4f4f5] text-[#18181b]">

  <!-- Admin Main Navigation Header -->
  <header class="bg-[#18181b] text-white border-b border-[#27272a] sticky top-0 z-40">
    <div class="container-rtl">
      <div class="flex items-center justify-between h-16 gap-3">
        
        <div class="flex items-center gap-2 sm:gap-4 min-w-0">
          <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold text-lg text-white whitespace-nowrap">
            <span class="w-8 h-8 rounded-lg bg-[#ff5a00] text-white flex items-center justify-center text-sm">م</span>
            <span class="hidden min-[420px]:inline">ميرال الإدارة</span>
            <span class="min-[420px]:hidden">الإدارة</span>
          </a>
          <span class="badge-ember text-[10px] hidden sm:inline-flex">Salla Admin Panel</span>
        </div>

        <div class="flex items-center gap-2 sm:gap-4 text-xs shrink-0">
          <a href="{{ route('home') }}" target="_blank" class="hover:text-[#a1a1aa] flex items-center gap-1 whitespace-nowrap">
            <span>🌐</span> <span class="hidden sm:inline">معاينة المتجر</span>
          </a>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-red-400 hover:text-red-300 font-bold py-2 min-h-[44px] whitespace-nowrap">
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
      <div class="flex flex-wrap gap-2 text-xs font-bold mb-6 sm:mb-8 p-1.5 bg-white border border-[#ececee] rounded-[16px]">
        <a href="{{ route('admin.dashboard') }}" class="px-3 sm:px-4 py-3 min-h-[44px] rounded-[12px] transition flex items-center whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-[#09090b] text-white' : 'text-[#52525b] hover:bg-[#f4f4f5]' }}">📊 لوحة التحكم</a>
        <a href="{{ route('admin.products.index') }}" class="px-3 sm:px-4 py-3 min-h-[44px] rounded-[12px] transition flex items-center whitespace-nowrap {{ request()->routeIs('admin.products.*') ? 'bg-[#09090b] text-white' : 'text-[#52525b] hover:bg-[#f4f4f5]' }}">💎 المنتجات</a>
        <a href="{{ route('admin.orders.index') }}" class="px-3 sm:px-4 py-3 min-h-[44px] rounded-[12px] transition flex items-center whitespace-nowrap {{ request()->routeIs('admin.orders.*') ? 'bg-[#09090b] text-white' : 'text-[#52525b] hover:bg-[#f4f4f5]' }}">📦 الطلبات</a>
        <a href="{{ route('admin.customers.index') }}" class="px-3 sm:px-4 py-3 min-h-[44px] rounded-[12px] transition flex items-center whitespace-nowrap {{ request()->routeIs('admin.customers.*') ? 'bg-[#09090b] text-white' : 'text-[#52525b] hover:bg-[#f4f4f5]' }}">👥 العملاء</a>
        <a href="{{ route('admin.settings') }}" class="px-3 sm:px-4 py-3 min-h-[44px] rounded-[12px] transition flex items-center whitespace-nowrap {{ request()->routeIs('admin.settings') ? 'bg-[#09090b] text-white' : 'text-[#52525b] hover:bg-[#f4f4f5]' }}">⚙️ إعدادات سلة</a>
      </div>

      {{ $slot }}
    </div>
  </main>

  <footer class="py-6 text-center text-xs text-[#71717a] border-t border-[#ececee] bg-white">
    Laravel 13 Salla Architecture Admin Panel © 2026
  </footer>
</body>
</html>
