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
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ececee]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#09090b] tracking-tight">إدارة الطلبات</h1>
        <p class="text-xs text-[#71717a] mt-1">متابعة الطلبات وتغيير حالة الشحن والتوصيل</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-4 py-3 min-h-[44px] font-bold whitespace-nowrap">&rarr; العودة للوحة الرئيسية</a>
    </div>

    <!-- Orders List Card -->
    <div class="card-awesomic p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="border-b border-[#ececee] text-[#71717a] font-bold">
              <th class="pb-3">رقم الطلب</th>
              <th class="pb-3">اسم العميل</th>
              <th class="pb-3">الجوال والمدينة</th>
              <th class="pb-3">إجمالي الدفع</th>
              <th class="pb-3">طريقة الدفع</th>
              <th class="pb-3">الحالة الحالية</th>
              <th class="pb-3">تحديث الحالة</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ececee]">
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
              <tr class="hover:bg-[#fafafa] transition">
                <td class="py-3 font-bold text-[#09090b]">
                  <a href="{{ route('orders.show', $id) }}" class="hover:underline">#{{ $number }}</a>
                </td>
                <td class="py-3 font-bold text-[#18181b]">{{ data_get($o, 'shipping_name') }}</td>
                <td class="py-3 text-[#52525b]">{{ data_get($o, 'shipping_city') }} ({{ data_get($o, 'shipping_phone') }})</td>
                <td class="py-3 font-extrabold text-[#09090b]">{{ number_format(data_get($o, 'total', 0), 2) }} <span class="currency-sar">ر.س</span></td>
                <td class="py-3 text-[#52525b]">{{ data_get($o, 'payment_method') }}</td>
                <td class="py-3">
                  <span class="badge-tag">{{ $statusLabel }}</span>
                </td>
                <td class="py-3">
                  <select @change="
                    const val = $event.target.value;
                    if (val === 'pending') updateStatus({{ $id }}, 'pending', 'قيد المراجعة', 'warning');
                    if (val === 'processing') updateStatus({{ $id }}, 'processing', 'جاري التجهيز', 'info');
                    if (val === 'shipped') updateStatus({{ $id }}, 'shipped', 'تم الشحن', 'info');
                    if (val === 'delivered') updateStatus({{ $id }}, 'delivered', 'تم التوصيل', 'success');
                    if (val === 'cancelled') updateStatus({{ $id }}, 'cancelled', 'ملغي', 'danger');
                  " class="input-awesomic py-1 text-xs w-32">
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
