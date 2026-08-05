@extends("layouts.app")

@section("title", "طلباتي — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">طلباتي</h1>

    @if(($orders ?? collect())->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-7xl mb-4">📦</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">لا توجد طلبات بعد</h2>
            <p class="text-gray-500 mb-6">ابدأ التسوّق وستظهر طلباتك هنا</p>
            <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-3">تسوّق الآن</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="card p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-4 border-b border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">رقم الطلب</p>
                            <p class="font-bold text-gray-900">#{{ $order->number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">التاريخ</p>
                            <p class="text-sm text-gray-900">{{ $order->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">الحالة</p>
                            <span class="badge-{{ method_exists($order->status, 'color') ? $order->status->color() : 'info' }}">
                                {{ method_exists($order->status, 'label') ? $order->status->label() : $order->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">الإجمالي</p>
                            <p class="font-bold text-brand-700">{{ number_format($order->total, 2) }} ر.س</p>
                        </div>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn-outline py-2 px-4 text-sm">التفاصيل</a>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($order->items->take(4) as $item)
                            <img src="{{ $item->product->thumbnail_url ?? 'https://picsum.photos/seed/' . $item->product->id . '/60/60' }}"
                                 alt="{{ $item->product->name }}"
                                 title="{{ $item->product->name }}"
                                 class="w-14 h-14 rounded-lg object-cover bg-gray-50">
                        @endforeach
                        @if($order->items->count() > 4)
                            <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-xs text-gray-600 font-bold">
                                +{{ $order->items->count() - 4 }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
