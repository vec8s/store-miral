<x-layouts.admin title="قائمة العملاء — إدارة ميرال">
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#e2e8f0]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0f172a] tracking-tight">قائمة العملاء</h1>
        <p class="text-xs text-[#64748b] mt-1">سجل العملاء المسجلين والنشطين في المتجر</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-3.5 py-2.5 min-h-[40px] font-bold whitespace-nowrap">&rarr; العودة للرئيسية</a>
    </div>

    <div class="p-6 rounded-2xl bg-white border border-[#e2e8f0] shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-[#64748b] font-bold">
              <th class="pb-3">الاسم</th>
              <th class="pb-3">البريد الإلكتروني</th>
              <th class="pb-3">رقم الجوال</th>
              <th class="pb-3">عدد الطلبات</th>
              <th class="pb-3">إجمالي المشتريات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            @php
              $customersList = $customers ?? [
                (object)['name' => 'محمد العتيبي', 'email' => 'm.otaibi@example.com', 'phone' => '+966500000000', 'ordersCount' => 3, 'totalSpent' => 1250],
                (object)['name' => 'مرام البارقي', 'email' => 'maram@example.com', 'phone' => '+966555555555', 'ordersCount' => 2, 'totalSpent' => 840],
              ];
            @endphp
            @foreach($customersList as $c)
              <tr class="hover:bg-[#f8fafc] transition">
                <td class="py-3.5 font-bold text-[#0f172a]">{{ data_get($c, 'name') }}</td>
                <td class="py-3.5 text-[#64748b]">{{ data_get($c, 'email') }}</td>
                <td class="py-3.5 text-[#64748b]">{{ data_get($c, 'phone') }}</td>
                <td class="py-3.5 font-bold text-[#1e293b]">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                    {{ data_get($c, 'ordersCount', 1) }} طلبات
                  </span>
                </td>
                <td class="py-3.5 font-extrabold text-[#0f172a]">{{ number_format(data_get($c, 'totalSpent', 0), 2) }} <span class="currency-sar">ر.س</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-layouts.admin>
