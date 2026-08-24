<x-layouts.admin title="إدارة الطلبات — إدارة ميرال">
  <div class="space-y-8" x-data="{
    async updateStatus(orderId, statusVal, labelStr, colorStr) {
      const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content');
      await fetch('/api/admin/orders/' + orderId + '/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
        body: JSON.stringify({ status: statusVal, label: labelStr, color: colorStr })
      });
      location.reload();
    }
  }">
    
    <!-- Admin Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#e2e8f0]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0f172a] tracking-tight">إدارة الطلبات</h1>
        <p class="text-xs text-[#64748b] mt-1">متابعة طلبات العملاء المزامنة مع سلة وتحديث حالات الشحن والدفع</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-3.5 py-2.5 min-h-[40px] font-bold whitespace-nowrap">&rarr; العودة للرئيسية</a>
    </div>

    <!-- Orders List Card -->
    <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-[#64748b] font-bold">
              <th class="pb-3">رقم الطلب</th>
              <th class="pb-3">اسم العميل</th>
              <th class="pb-3">الجوال والمدينة</th>
              <th class="pb-3">إجمالي الدفع</th>
              <th class="pb-3">طريقة الدفع</th>
              <th class="pb-3">الحالة الحالية</th>
              <th class="pb-3">تحديث الحالة</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            @php
              $ordersList = $orders ?? [
                (object)['id' => 1, 'number' => '1001', 'shipping_name' => 'محمد العتيبي', 'shipping_phone' => '+966500000000', 'shipping_city' => 'الرياض', 'total' => 450, 'payment_method' => 'بطاقة مدى / Apple Pay', 'status' => (object)['value' => 'processing', 'label' => 'جاري التجهيز', 'color' => 'warning']],
                (object)['id' => 2, 'number' => '1002', 'shipping_name' => 'مرام البارقي', 'shipping_phone' => '+966555555555', 'shipping_city' => 'جدة', 'total' => 280, 'payment_method' => 'بطاقة ائتمانية', 'status' => (object)['value' => 'delivered', 'label' => 'تم التوصيل', 'color' => 'success']],
              ];
            @endphp
            @foreach($ordersList as $o)
              @php
                $id = data_get($o, 'id');
                $number = data_get($o, 'number', $id);
                $statusVal = data_get($o, 'status.value', 'pending');
                $statusLabel = is_callable(data_get($o, 'status.label')) ? data_get($o, 'status.label')() : (data_get($o, 'status.label') ?: 'قيد المراجعة');
              @endphp
              <tr class="hover:bg-[#f8fafc] transition">
                <td class="py-3.5 font-bold text-[#0f172a]">
                  <a href="{{ route('orders.show', $id) }}" class="hover:text-[#059669] hover:underline">#{{ $number }}</a>
                </td>
                <td class="py-3.5 font-bold text-[#1e293b]">{{ data_get($o, 'shipping_name') }}</td>
                <td class="py-3.5 text-[#64748b]">{{ data_get($o, 'shipping_city') }} <span class="text-[10px] text-slate-400">({{ data_get($o, 'shipping_phone') }})</span></td>
                <td class="py-3.5 font-extrabold text-[#0f172a]">{{ number_format(data_get($o, 'total', 0), 2) }} <span class="currency-sar">ر.س</span></td>
                <td class="py-3.5 text-[#64748b] font-medium">{{ data_get($o, 'payment_method') }}</td>
                <td class="py-3.5">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $statusLabel }}
                  </span>
                </td>
                <td class="py-3.5">
                  <select @change="
                    const val = $event.target.value;
                    if (val === 'pending') updateStatus({{ $id }}, 'pending', 'قيد المراجعة', 'warning');
                    if (val === 'processing') updateStatus({{ $id }}, 'processing', 'جاري التجهيز', 'info');
                    if (val === 'shipped') updateStatus({{ $id }}, 'shipped', 'تم الشحن', 'info');
                    if (val === 'delivered') updateStatus({{ $id }}, 'delivered', 'تم التوصيل', 'success');
                    if (val === 'cancelled') updateStatus({{ $id }}, 'cancelled', 'ملغي', 'danger');
                  " class="border border-[#cbd5e1] rounded-lg py-1 px-2 text-xs w-32 bg-white text-[#0f172a] focus:ring-1 focus:ring-[#0f172a] focus:border-[#0f172a]">
                    <option value="pending" {{ $statusVal === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="processing" {{ $statusVal === 'processing' ? 'selected' : '' }}>جاري التجهيز</option>
                    <option value="shipped" {{ $statusVal === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                    <option value="delivered" {{ $statusVal === 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                    <option value="cancelled" {{ $statusVal === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                  </select>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-layouts.admin>
