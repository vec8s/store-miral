@extends("layouts.app")

@section("title", "طلب #" . $order->number . " — رافال")

@section("content")
<div class="container-rtl py-10">
    <a href="{{ route('orders.index') }}" class="text-sm text-brand-600 hover:text-brand-700 mb-4 inline-block font-medium">← العودة لطلباتي</a>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">طلب #{{ $order->number }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <span class="badge-{{ method_exists($order->status, 'color') ? $order->status->color() : 'info' }} text-base px-4 py-1.5">
            {{ method_exists($order->status, 'label') ? $order->status->label() : $order->status }}
        </span>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            {{-- Items --}}
            <section class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">المنتجات</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 py-3 first:pt-0 last:pb-0">
                            <img src="{{ $item->product->thumbnail_url ?? 'https://picsum.photos/seed/' . $item->product->id . '/80/80' }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-16 h-16 rounded-xl object-cover bg-gray-50">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $item->quantity }} × {{ number_format($item->price, 2) }} ر.س</p>
                            </div>
                            <p class="font-bold text-brand-700">{{ number_format($item->subtotal, 2) }} ر.س</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Timeline --}}
            @if(!empty($order->statusHistory))
                <section class="card p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">حالة الطلب</h2>
                    <ol class="relative border-r border-gray-200 mr-3 space-y-6">
                        @foreach($order->statusHistory as $history)
                            <li class="mr-6">
                                <span class="absolute -right-1.5 w-3 h-3 bg-brand-500 rounded-full"></span>
                                <p class="font-semibold text-sm">
                                    {{ method_exists($history->status, 'label') ? $history->status->label() : $history->status }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $history->created_at->format('Y-m-d H:i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>

        <aside class="lg:col-span-1 space-y-4">
            {{-- Summary --}}
            <div class="card p-6">
                <h3 class="font-bold text-gray-900 mb-4">الملخص</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-600">المجموع الفرعي</dt><dd>{{ number_format($order->subtotal, 2) }} ر.س</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">الشحن</dt><dd>{{ number_format($order->shipping, 2) }} ر.س</dd></div>
                    @if(($order->discount ?? 0) > 0)
                        <div class="flex justify-between text-green-700"><dt>الخصم</dt><dd>- {{ number_format($order->discount, 2) }} ر.س</dd></div>
                    @endif
                    <div class="flex justify-between text-base font-bold border-t pt-3 mt-3"><dt>الإجمالي</dt><dd class="text-brand-700">{{ number_format($order->total, 2) }} ر.س</dd></div>
                </dl>
            </div>

            {{-- Address --}}
            <div class="card p-6">
                <h3 class="font-bold text-gray-900 mb-3">عنوان الشحن</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $order->shipping_name }}<br>
                    <span dir="ltr" class="inline-block">{{ $order->shipping_phone }}</span><br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }} {{ $order->shipping_postal_code }}
                </p>
            </div>

            @if(isset($order->status->value) && $order->status->value === 'pending')
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf 
                    @method("PATCH")
                    <button type="submit" class="btn-ghost w-full py-2.5 text-red-600 hover:bg-red-50">إلغاء الطلب</button>
                </form>
            @endif
        </aside>
    </div>
</div>
@endsection
