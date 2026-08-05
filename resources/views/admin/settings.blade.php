@extends("layouts.admin")

@section("title", "الإعدادات — رافال")

@section("content")
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">الإعدادات</h1>

    @if(session("success"))
        <x-alert type="success">{{ session("success") }}</x-alert>
    @endif

    <div x-data="{ tab: 'general' }">
        {{-- Tab nav --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-6 text-sm font-medium">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'border-b-2 border-brand-600 text-brand-700' : 'text-gray-500 hover:text-gray-700'" class="py-3">عام</button>
                <button @click="tab = 'salla'" :class="tab === 'salla' ? 'border-b-2 border-brand-600 text-brand-700' : 'text-gray-500 hover:text-gray-700'" class="py-3">سلة</button>
                <button @click="tab = 'shipping'" :class="tab === 'shipping' ? 'border-b-2 border-brand-600 text-brand-700' : 'text-gray-500 hover:text-gray-700'" class="py-3">الشحن</button>
            </nav>
        </div>

        {{-- General --}}
        <div x-show="tab === 'general'" x-cloak>
            <form action="{{ route('admin.settings.update') }}" method="POST" class="card p-6 space-y-5 max-w-2xl">
                @csrf 
                @method("PUT")
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">اسم المتجر</label>
                    <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'رافال' }}" class="input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني للتواصل</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="input" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم الجوال</label>
                    <input type="tel" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" class="input" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">العملة</label>
                    <select name="currency" class="input">
                        <option value="SAR" {{ ($settings['currency'] ?? 'SAR') === 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                        <option value="AED" {{ ($settings['currency'] ?? '') === 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                        <option value="USD" {{ ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary px-8 py-2.5">حفظ التغييرات</button>
            </form>
        </div>

        {{-- Salla --}}
        <div x-show="tab === 'salla'" x-cloak>
            <form action="{{ route('admin.settings.salla.update') }}" method="POST" class="card p-6 space-y-5 max-w-2xl">
                @csrf 
                @method("PUT")
                <x-alert type="info">
                    الإعدادات المتقدمة لـ API سلة موجودة في <code class="bg-gray-100 px-1.5 py-0.5 rounded">.env</code> كمتغيرات <code>SALLA_*</code>.
                </x-alert>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Client ID</label>
                    <input type="text" value="{{ config('salla.client_id') }}" disabled class="input bg-gray-50" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Merchant ID</label>
                    <input type="text" value="{{ config('salla.merchant_id') }}" disabled class="input bg-gray-50" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">مزامنة تلقائية</label>
                    <label class="flex items-center gap-2 mt-1">
                        <input type="checkbox" name="auto_sync" value="1" {{ ($settings['auto_sync'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-gray-600">تفعيل المزامنة كل ساعة</span>
                    </label>
                </div>
                <button type="submit" class="btn-primary px-8 py-2.5">حفظ</button>
            </form>
        </div>

        {{-- Shipping --}}
        <div x-show="tab === 'shipping'" x-cloak>
            <form action="{{ route('admin.settings.shipping.update') }}" method="POST" class="card p-6 space-y-5 max-w-2xl">
                @csrf 
                @method("PUT")
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">رسوم الشحن الافتراضية (ر.س)</label>
                    <input type="number" step="0.01" name="default_shipping_fee" value="{{ $settings['default_shipping_fee'] ?? 25 }}" class="input" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">الحد الأدنى للشحن المجاني (ر.س)</label>
                    <input type="number" step="0.01" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] ?? 200 }}" class="input" dir="ltr">
                </div>
                <button type="submit" class="btn-primary px-8 py-2.5">حفظ</button>
            </form>
        </div>
    </div>
</div>
@endsection
