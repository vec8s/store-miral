@extends("layouts.app")

@section("title", "إتمام الطلب — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">إتمام الطلب</h1>

    @guest
        <x-alert type="info" class="mb-6">
            لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="font-semibold underline">سجّل دخولك</a> لتسريع عملية الشراء.
        </x-alert>
    @endguest

    <form action="{{ route('checkout.place') }}" method="POST" class="grid lg:grid-cols-3 gap-8">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            {{-- Contact --}}
            <section class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">معلومات التواصل</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">الاسم الكامل *</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم الجوال *</label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required dir="ltr" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="input">
                    </div>
                </div>
            </section>

            {{-- Shipping address --}}
            <section class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">عنوان الشحن</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">المدينة *</label>
                        <select name="city" required class="input">
                            <option value="">اختر المدينة</option>
                            @foreach(["الرياض","جدة","مكة","المدينة","الدمام","الخبر","الطائف","تبوك","أبها","حائل"] as $city)
                                <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">العنوان التفصيلي *</label>
                        <textarea name="address" rows="3" required class="input">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">الرمز البريدي</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" dir="ltr" class="input">
                    </div>
                </div>
            </section>

            {{-- Payment --}}
            <section class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">طريقة الدفع</h2>
                <p class="text-sm text-gray-500 mb-4">سيتم توجيهك إلى صفحة الدفع الآمنة بعد تأكيد الطلب.</p>

                <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-brand-500 bg-brand-50 cursor-pointer">
                    <input type="radio" name="payment_method" value="salla" checked class="mt-0.5 text-brand-600 focus:ring-brand-500">
                    <div>
                        <p class="font-semibold text-gray-900">الدفع الإلكتروني</p>
                        <p class="text-sm text-gray-600 mt-0.5">بطاقة ائتمان · مدى · Apple Pay · تحويل بنكي</p>
                    </div>
                </label>
            </section>
        </div>

        {{-- Summary --}}
        <aside class="lg:col-span-1">
            <div class="card p-6 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-4">ملخص الطلب</h3>
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto scrollbar-thin">
                    @foreach($cartItems ?? [] as $item)
                        <div class="flex items-center gap-3 text-sm">
                            <img src="{{ $item->product->thumbnail_url ?? 'https://picsum.photos/seed/' . $item->product->id . '/80/80' }}" class="w-12 h-12 rounded-lg object-cover">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ number_format($item->product->price, 2) }} ر.س</p>
                            </div>
                            <p class="font-semibold whitespace-nowrap">{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <dl class="space-y-2 text-sm border-t pt-4">
                    <div class="flex justify-between"><dt class="text-gray-600">المجموع الفرعي</dt><dd>{{ number_format($subtotal ?? 0, 2) }} ر.س</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-600">الشحن</dt><dd>{{ number_format($shipping ?? 0, 2) }} ر.س</dd></div>
                    <div class="flex justify-between text-base font-bold border-t pt-3 mt-3">
                        <dt>الإجمالي</dt><dd class="text-brand-700">{{ number_format($total ?? 0, 2) }} ر.س</dd>
                    </div>
                </dl>

                <button type="submit" class="btn-primary w-full py-3 mt-6">تأكيد الطلب</button>

                <p class="text-xs text-gray-400 text-center mt-4">بالنقر على "تأكيد الطلب" فأنت توافق على شروط الاستخدام.</p>
            </div>
        </aside>
    </form>
</div>
@endsection
