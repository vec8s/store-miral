<x-layouts.admin title="قائمة العملاء — إدارة ميرال">
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ececee]">
      <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#09090b] tracking-tight">قائمة العملاء</h1>
        <p class="text-xs text-[#71717a] mt-1">سجل المسجلين والعملاء النشطين بالمتجر</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn-ghost text-xs px-4 py-3 min-h-[44px] font-bold whitespace-nowrap">&rarr; العودة للوحة الرئيسية</a>
    </div>

    <div class="card-awesomic p-6">
      <div class="overflow-x-auto">
        <table class="w-full text-right text-xs">
          <thead>
            <tr class="border-b border-[#ececee] text-[#71717a] font-bold">
              <th class="pb-3">الاسم</th>
              <th class="pb-3">البريد الإلكتروني</th>
              <th class="pb-3">رقم الجوال</th>
              <th class="pb-3">عدد الطلبات</th>
              <th class="pb-3">إجمالي المشتريات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ececee]">
            @php
              $customersList = $customers ?? [
                (object)['name' => 'محمد العتيبي', 'email' => 'm.otaibi@example.com', 'phone' => '+966500000000', 'ordersCount' => 3, 'totalSpent' => 1250],
                (object)['name' => 'مرام البارقي', 'email' => 'maram@example.com', 'phone' => '+966555555555', 'ordersCount' => 2, 'totalSpent' => 840],
              ];
            @endphp
            @foreach($customersList as $c)
              <tr class="hover:bg-[#fafafa] transition">
                <td class="py-3 font-bold text-[#09090b]">{{ data_get($c, 'name') }}</td>
                <td class="py-3 text-[#52525b]">{{ data_get($c, 'email') }}</td>
                <td class="py-3 text-[#52525b]">{{ data_get($c, 'phone') }}</td>
                <td class="py-3 font-bold text-[#18181b]">{{ data_get($c, 'ordersCount', 1) }} طلبات</td>
                <td class="py-3 font-extrabold text-[#09090b]">{{ number_format(data_get($c, 'totalSpent', 0), 2) }} <span class="currency-sar">ر.س</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-layouts.admin>
