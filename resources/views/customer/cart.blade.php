@extends("layouts.app")

@section("title", "سلة التسوق — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">سلة التسوق</h1>

    @if(($cartItems ?? collect())->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-7xl mb-4">🛒</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">سلتك فارغة</h2>
            <p class="text-gray-500 mb-6">ابدأ التسوّق واكتشف منتجاتنا المميزة</p>
            <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-3">تسوّق الآن</a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="card p-4 flex gap-4 items-center">
                        <img src="{{ $item->product->thumbnail_url ?? 'https://picsum.photos/seed/' . $item->product->id . '/120/120' }}"
                             alt="{{ $item->product->name }}"
                             class="w-24 h-24 rounded-xl object-cover bg-gray-50">

                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mb-1">
                                <a href="{{ route('shop.show', $item->product->slug ?? $item->product->id) }}" class="hover:text-brand-600">
                                    {{ $item->product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-2">{{ number_format($item->product->price, 2) }} ر.س</p>

                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf 
                                @method("PATCH")
                                <select name="quantity" onchange="this.form.submit()" class="input py-1 px-2 text-sm w-20">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </form>
                        </div>

                        <div class="text-left">
                            <p class="font-bold text-lg text-brand-700 mb-2">{{ number_format($item->subtotal, 2) }} ر.س</p>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf 
                                @method("DELETE")
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="lg:col-span-1">
                <div class="card p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">ملخص الطلب</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-600">المجموع الفرعي</dt><dd class="font-semibold">{{ number_format($subtotal ?? 0, 2) }} ر.س</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-600">الشحن</dt><dd class="font-semibold">{{ number_format($shipping ?? 0, 2) }} ر.س</dd></div>
                        @if(($discount ?? 0) > 0)
                            <div class="flex justify-between text-green-700"><dt>الخصم</dt><dd>- {{ number_format($discount, 2) }} ر.س</dd></div>
                        @endif
                        <div class="border-t pt-3 flex justify-between text-base">
                            <dt class="font-bold">الإجمالي</dt>
                            <dd class="font-bold text-brand-700">{{ number_format($total ?? 0, 2) }} ر.س</dd>
                        </div>
                    </dl>

                    <a href="{{ route('checkout.index') }}" class="btn-primary w-full py-3 mt-6 text-center block">متابعة الدفع</a>
                    <a href="{{ route('shop.index') }}" class="btn-ghost w-full py-2.5 mt-2 text-sm text-center block">متابعة التسوق</a>
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection
