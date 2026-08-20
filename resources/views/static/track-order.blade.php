<x-layouts.app title="متابعة الطلب — ميرال">
  <div class="container-rtl py-10 sm:py-16 max-w-3xl">
    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-[#09090b] mb-6 text-center">متابعة الطلب</h1>
    <p class="text-center text-sm text-[#52525b] mb-8">لتتبع حالة طلبك، سجّل الدخول إلى حسابك وانتقل إلى صفحة "طلباتي".</p>
    <div class="text-center">
      <a href="{{ route('orders.index') }}" class="btn-primary inline-flex px-7 py-4 min-h-[44px] text-sm font-bold rounded-[14px]">الانتقال إلى طلباتي &larr;</a>
    </div>
  </div>
</x-layouts.app>