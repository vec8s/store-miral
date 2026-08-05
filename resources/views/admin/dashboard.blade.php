@extends("layouts.admin")

@section("title", "لوحة التحكم — رافال")

@section("content")
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">لوحة التحكم</h1>
            <p class="text-sm text-gray-500 mt-1">نظرة عامة على أداء المتجر</p>
        </div>
        <button type="button" class="btn-outline text-sm">📥 تصدير التقرير</button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpis = [
                ["label" => "إجمالي المبيعات", "value" => number_format($stats["sales"] ?? 0) . ' ر.س', "delta" => "+12.5%", "icon" => "💰", "color" => "bg-green-50 text-green-700"],
                ["label" => "الطلبات",         "value" => $stats["orders"] ?? 0,        "delta" => "+8.2%",  "icon" => "📦", "color" => "bg-blue-50 text-blue-700"],
                ["label" => "العملاء",         "value" => $stats["customers"] ?? 0,     "delta" => "+24",    "icon" => "👥", "color" => "bg-purple-50 text-purple-700"],
                ["label" => "المنتجات",        "value" => $stats["products"] ?? 0,      "delta" => "+3",     "icon" => "🏷️", "color" => "bg-amber-50 text-amber-700"],
            ];
        @endphp
        @foreach($kpis as $k)
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ $k["label"] }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $k["value"] }}</p>
                        <p class="text-xs text-green-600 mt-1">↑ {{ $k["delta"] }} عن الشهر الماضي</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $k["color"] }} flex items-center justify-center text-xl">{{ $k["icon"] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Recent Orders --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-900">أحدث الطلبات</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 hover:text-brand-700">عرض الكل</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-right text-xs text-gray-500 border-b">
                        <tr>
                            <th class="py-2">رقم الطلب</th>
                            <th class="py-2">العميل</th>
                            <th class="py-2">المبلغ</th>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td class="py-3 font-semibold">#{{ $order->number }}</td>
                                <td class="py-3">{{ $order->customer_name ?? ($order->user->name ?? "—") }}</td>
                                <td class="py-3 font-semibold">{{ number_format($order->total, 2) }} ر.س</td>
                                <td class="py-3">
                                    <span class="badge-{{ method_exists($order->status, 'color') ? $order->status->color() : 'info' }}">
                                        {{ method_exists($order->status, 'label') ? $order->status->label() : $order->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">لا توجد طلبات حديثة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="card p-6">
            <h2 class="font-bold text-gray-900 mb-4">الأكثر مبيعاً</h2>
            <div class="space-y-3">
                @forelse($topProducts ?? [] as $i => $p)
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <img src="{{ $p->thumbnail_url ?? 'https://picsum.photos/seed/' . $p->id . '/60/60' }}" class="w-10 h-10 rounded-lg object-cover bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $p->name }}</p>
                            <p class="text-xs text-gray-500">{{ $p->sold_count ?? 0 }} مبيعات</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد بيانات متاحة.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sync status --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-bold text-gray-900">حالة المزامنة مع سلة</h2>
                <p class="text-xs text-gray-500 mt-1">آخر مزامنة: {{ $syncStatus["last_sync_at"] ?? "لم تتم بعد" }}</p>
            </div>
            <form action="{{ route('admin.sync.run') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary text-sm">🔄 مزامنة الآن</button>
            </form>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach(["products" => "المنتجات", "orders" => "الطلبات", "customers" => "العملاء", "coupons" => "الكوبونات"] as $key => $label)
                <div class="rounded-xl border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 mb-1">{{ $label }}</p>
                    <p class="font-bold text-gray-900">{{ $syncStatus[$key] ?? "—" }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
