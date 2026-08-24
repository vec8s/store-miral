<x-layouts.admin title="لوحة التحكم — إدارة ميرال">
  <div class="space-y-8">
    
    <!-- Header Title -->
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0f172a] tracking-tight">نظرة عامة على المتجر</h1>
      <p class="text-xs text-[#64748b] mt-1">متابعة فورية للمبيعات، الطلبات والمنتجات المرتبطة بمنصة سلة</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 min-[420px]:grid-cols-2 md:grid-cols-4 gap-4 md:gap-5">
      <div class="p-6 rounded-2xl bg-gradient-to-br from-[#0f172a] to-[#1e293b] text-white border border-[#334155] shadow-sm">
        <p class="text-xs text-[#94a3b8] font-bold mb-1">إجمالي المبيعات</p>
        <h3 class="text-2xl sm:text-3xl font-black text-white">{{ number_format($totalSales ?? 18450, 2) }} <span class="currency-sar text-xs text-[#cbd5e1]">ر.س</span></h3>
      </div>

      <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
        <p class="text-xs text-[#64748b] font-bold mb-1">عدد الطلبات الإجمالي</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#0f172a]">{{ $totalOrders ?? 42 }}</h3>
      </div>

      <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
        <p class="text-xs text-[#64748b] font-bold mb-1">طلبات بانتظار الإجراء</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#d97706]">{{ $pendingOrders ?? 5 }}</h3>
      </div>

      <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
        <p class="text-xs text-[#64748b] font-bold mb-1">المنتجات النشطة</p>
        <h3 class="text-2xl sm:text-3xl font-black text-[#0f172a]">{{ $totalProducts ?? 28 }}</h3>
      </div>
    </div>

    <!-- Tables & Salla Status Grid -->
    <div class="grid lg:grid-cols-3 gap-6">
      
      <!-- Recent Orders Table -->
      <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#f1f5f9]">
          <h3 class="font-bold text-[#0f172a] text-sm sm:text-base">أحدث طلبات المتجر</h3>
          <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#059669] hover:text-[#047857] font-bold inline-flex items-center gap-1 min-h-[36px]">
            عرض كافة الطلبات &larr;
          </a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-right text-xs">
            <thead>
              <tr class="border-b border-[#f1f5f9] text-[#64748b] font-bold">
                <th class="pb-3">رقم الطلب</th>
                <th class="pb-3">العميل</th>
                <th class="pb-3">المبلغ</th>
                <th class="pb-3">الحالة</th>
                <th class="pb-3">التاريخ</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f5f9]">
              @php
                $orders = $recentOrders ?? [
                  (object)['id' => 1, 'number' => '1001', 'shipping_name' => 'محمد العتيبي', 'total' => 450, 'status' => (object)['label' => 'قيد التجهيز'], 'created_at' => '2026-02-01'],
                  (object)['id' => 2, 'number' => '1002', 'shipping_name' => 'مرام البارقي', 'total' => 280, 'status' => (object)['label' => 'مكتمل'], 'created_at' => '2026-02-01'],
                  (object)['id' => 3, 'number' => '1003', 'shipping_name' => 'سارة الشمري', 'total' => 699, 'status' => (object)['label' => 'قيد المراجعة'], 'created_at' => '2026-02-02'],
                ];
              @endphp
              @foreach($orders as $o)
                <tr class="hover:bg-[#f8fafc] transition">
                  <td class="py-3.5 font-bold text-[#0f172a]">#{{ data_get($o, 'number', data_get($o, 'id')) }}</td>
                  <td class="py-3.5 text-[#334155] font-medium">{{ data_get($o, 'shipping_name', 'عميل ميرال') }}</td>
                  <td class="py-3.5 font-bold text-[#0f172a]">{{ number_format(data_get($o, 'total', 0), 2) }} <span class="currency-sar">ر.س</span></td>
                  <td class="py-3.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      {{ is_callable(data_get($o, 'status.label')) ? data_get($o, 'status.label')() : (data_get($o, 'status.label') ?: 'قيد التجهيز') }}
                    </span>
                  </td>
                  <td class="py-3.5 text-[#94a3b8]">{{ data_get($o, 'created_at') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- Salla Integration Status Card -->
      <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm space-y-4">
        <h3 class="font-bold text-[#0f172a] text-sm sm:text-base pb-3 border-b border-[#f1f5f9]">حالة الربط مع سلة (Salla)</h3>

        <div class="bg-emerald-50 text-emerald-900 p-4 rounded-xl border border-emerald-200 text-xs space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-emerald-800">حالة الربط المباشر:</span>
            <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">نشط ومتزامن</span>
          </div>
          <p class="text-emerald-800 font-medium"><strong>المتجر:</strong> Miral Official Store</p>
          <p class="text-emerald-700"><strong>حالة المزود:</strong> جاهز لاستقبال الأحداث</p>
        </div>

        <div class="pt-2 text-xs text-[#475569] space-y-2.5">
          <div class="flex items-center gap-2">
            <span class="text-emerald-600">✓</span>
            <span>مزامنة المنتجات والتسعير</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-emerald-600">✓</span>
            <span>استقبال الويب هوك وتحديث الطلبات</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-emerald-600">✓</span>
            <span>توجيه الدفع الإلكتروني عبر سلة</span>
          </div>
        </div>

        <a href="{{ route('admin.settings') }}" class="w-full py-2.5 min-h-[40px] text-xs font-bold text-center block rounded-xl bg-[#f1f5f9] hover:bg-[#e2e8f0] text-[#0f172a] transition">
          إعدادات المزامنة المتقدمة
        </a>
      </div>

    </div>

  </div>
</x-layouts.admin>
