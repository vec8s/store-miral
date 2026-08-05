@extends("layouts.admin")

@section("title", "الطلبات — لوحة التحكم")

@section("content")
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">الطلبات</h1>
        <div class="flex items-center gap-2">
            <select name="filter_status" onchange="this.form.submit()" class="input py-2 text-sm w-auto">
                <option value="">كل الطلبات</option>
                <option value="pending" {{ request('filter_status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="processing" {{ request('filter_status') === 'processing' ? 'selected' : '' }}>قيد التجهيز</option>
                <option value="shipped" {{ request('filter_status') === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                <option value="completed" {{ request('filter_status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled" {{ request('filter_status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-right text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">رقم الطلب</th>
                        <th class="px-4 py-3">العميل</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الدفع</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders ?? [] as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">#{{ $order->number }}</td>
                            <td class="px-4 py-3">{{ $order->customer_name ?? ($order->user->name ?? "—") }}</td>
                            <td class="px-4 py-3 font-bold">{{ number_format($order->total, 2) }} ر.س</td>
                            <td class="px-4 py-3 text-xs">{{ $order->payment_method ?? "—" }}</td>
                            <td class="px-4 py-3">
                                <span class="badge-{{ method_exists($order->status, 'color') ? $order->status->color() : 'info' }}">
                                    {{ method_exists($order->status, 'label') ? $order->status->label() : $order->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ optional($order->created_at)->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-left">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-brand-600 hover:text-brand-700 text-xs font-semibold">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات مسجلة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($orders) && method_exists($orders, 'links'))
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
