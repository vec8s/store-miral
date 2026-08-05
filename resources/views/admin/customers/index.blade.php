@extends("layouts.admin")

@section("title", "العملاء — لوحة التحكم")

@section("content")
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">العملاء</h1>
        <p class="text-sm text-gray-500">{{ isset($customers) && method_exists($customers, 'total') ? $customers->total() : count($customers ?? []) }} عميل</p>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-right text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">العميل</th>
                        <th class="px-4 py-3">البريد</th>
                        <th class="px-4 py-3">الجوال</th>
                        <th class="px-4 py-3">الطلبات</th>
                        <th class="px-4 py-3">إجمالي الإنفاق</th>
                        <th class="px-4 py-3">تاريخ التسجيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers ?? [] as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">
                                        {{ mb_substr($customer->name ?? 'ع', 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600" dir="ltr">{{ $customer->email }}</td>
                            <td class="px-4 py-3 text-gray-600" dir="ltr">{{ $customer->phone ?? "—" }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $customer->orders_count ?? 0 }}</td>
                            <td class="px-4 py-3 font-semibold text-brand-700">{{ number_format($customer->total_spent ?? 0, 2) }} ر.س</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ optional($customer->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">لا يوجد عملاء مسجلين</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($customers) && method_exists($customers, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
