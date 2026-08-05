@extends("layouts.guest")

@section("title", "تسجيل الدخول — رافال")
@section("heading", "أهلاً بعودتك 👋")
@section("subheading", "سجّل دخولك للوصول لحسابك ومتابعة طلباتك")

@section("content")
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="/login" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old("email") }}" required autofocus
                   autocomplete="email" class="input" placeholder="you@example.com">
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
                <a href="/forgot-password" class="text-xs text-brand-600 hover:text-brand-700">نسيت كلمة المرور؟</a>
            </div>
            <input id="password" type="password" name="password" required
                   autocomplete="current-password" class="input" placeholder="••••••••">
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-gray-600">تذكّرني</span>
        </label>
        <button type="submit" class="btn-primary w-full py-3">تسجيل الدخول</button>
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
            <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-gray-400">أو</span></div>
        </div>
        <a href="/auth/salla" class="btn-outline w-full py-3">🛒 متابعة عبر سلة</a>
    </form>
@endsection

@section("footer-link")
    ليس لديك حساب؟ <a href="/register" class="text-brand-600 hover:text-brand-700 font-semibold">أنشئ حساباً جديداً</a>
@endsection
