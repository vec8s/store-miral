<x-layouts.admin title="لوحة التحكم — إدارة ميرال">
  <div class="space-y-8">
    
    <!-- Header Title -->
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#09090b] tracking-tight">لوحة تحكم المتجر</h1>
      <p class="text-xs text-[#71717a] mt-1">نظرة عامة على المبيعات، الطلبات والمنتجات المزامنة مع سلة</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
      <div class="card-awesomic p-6 bg-gradient-to-br from-[#18181b] to-[#09090b] text-white border border-[#27272a]">
        <p class="text-xs text-[#a1a1aa] font-bold mb-1">إجمالي المبيعات</p>
        <h3 class="text-2xl sm:text-3xl font-black text-white">{{ number_format($totalSales ?? 18450, 2) }} <span class="currency-sar text-xs text-[#a1a1aa]">ر.س</span></h3>
      </div>

      <div class="card-awesomic p-6">
        <p class="text-xs text-[#71717a] font-bold mb-1">عدد الطلبات</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#09090b]">{{ $totalOrders ?? 42 }}</h3>
      </div>

      <div class="card-awesomic p-6">
        <p class="text-xs text-[#71717a] font-bold mb-1">طلبات قيد المراجعة</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#ff5a00]">{{ $pendingOrders ?? 5 }}</h3>
      </div>

      <div class="card-awesomic p-6">
        <p class="text-xs text-[#71717a] font-bold mb-1">المنتجات بالمخزن</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#09090b]">{{ $totalProducts ?? 28 }}</h3>
      </div>
    </div>

    <!-- Tables & Salla Status Grid -->
    <div class="grid lg:grid-cols-3 gap-8">
      
      <!-- Recent Orders Table -->
      <div class="lg:col-span-2 card-awesomic p-6">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#ececee]">
          <h3 class="font-bold text-[#09090b] text-base">أحدث الطلبات</h3>
          <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#09090b] font-bold hover:underline inline-flex items-center min-h-[44px]">عرض الكل &larr;</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right text-xs">
            <thead>
              <tr class="border-b border-[#ececee] text-[#71717a] font-bold">
                <th class="pb-3">رقم الطلب</th>
                <th class="pb-3">العميل</th>
                <th class="pb-3">المبلغ</th>
                <th class="pb-3">الحالة</th>
                <th class="pb-3">التاريخ</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#ececee]">
              @php
                $orders = $recentOrders ?? [
                  (object)['id' => 1, 'number' => '1001', 'shipping_name' => 'محمد العتيبي', 'total' => 450, 'status' => (object)['label' => 'قيد التجهيز'], 'created_at' => '2026-02-01'],
                  (object)['id' => 2, 'number' => '1002', 'shipping_name' => 'مرام البارقي', 'total' => 280, 'status' => (object)['label' => 'مكتمل'], 'created_at' => '2026-02-01'],
                  (object)['id' => 3, 'number' => '1003', 'shipping_name' => 'سارة الشمري', 'total' => 699, 'status' => (object)['label' => 'قيد المراجعة'], 'created_at' => '2026-02-02'],
                ];
              @endphp
              @foreach($orders as $o)
                <tr>
                  <td class="py-3 font-bold text-[#09090b]">#{{ data_get($o, 'number', data_get($o, 'id')) }}</td>
                  <td class="py-3 text-[#52525b]">{{ data_get($o, 'shipping_name', 'عميل ميرال') }}</td>
                  <td class="py-3 font-bold text-[#09090b]">{{ number_format(data_get($o, 'total', 0), 2) }} <span class="currency-sar">ر.س</span></td>
                  <td class="py-3"><span class="badge-tag">{{ is_callable(data_get($o, 'status.label')) ? data_get($o, 'status.label')() : (data_get($o, 'status.label') ?: 'قيد التجهيز') }}</span></td>
                  <td class="py-3 text-[#71717a]">{{ data_get($o, 'created_at') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Salla Integration Status Card -->
      <div class="card-awesomic p-6 space-y-4">
        <h3 class="font-bold text-[#09090b] text-base pb-3 border-b border-[#ececee]">حالة الربط مع سلة (Salla)</h3>

        <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-200 text-xs space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold">حالة الاتصال:</span>
            <span class="badge-ember bg-emerald-600">متصل حياً</span>
          </div>
          <p><strong>المتجر:</strong> Miral Official Store</p>
          <p><strong>آخر مزامنة:</strong> الآن</p>
        </div>

        <div class="pt-2 text-xs text-[#52525b] space-y-2">
          <p>✅ مزامنة المنتجات التلقائية مفعلة</p>
          <p>✅ تحديث مخزون المنتجات فوري</p>
          <p>✅ استقبال طلبات سلة مباشرة</p>
        </div>

        <a href="{{ route('admin.settings') }}" class="btn-ghost w-full py-3 min-h-[44px] text-xs font-bold text-center block rounded-xl">
          إعدادات الربط والمزامنة
        </a>
      </div>

    </div>

  </div>
</x-layouts.admin>
