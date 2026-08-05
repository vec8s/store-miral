@extends("layouts.guest")

@section("title", "حساب جديد — رافال")
@section("heading", "أنشئ حسابك في رافال ✨")
@section("subheading", "انضم إلينا واستمتع بتجربة تسوق مميزة ومكافآت حصرية")

@section("content")
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="/register" class="space-y-5">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">الاسم الكامل</label>
            <input id="name" type="text" name="name" value="{{ old("name") }}" required autofocus
                   autocomplete="name" class="input" placeholder="محمد عبدالله">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old("email") }}" required
                   autocomplete="email" class="input" placeholder="you@example.com">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">رقم الجوال (اختياري)</label>
            <input id="phone" type="tel" name="phone" value="{{ old("phone") }}"
                   autocomplete="tel" class="input" placeholder="+966 5X XXX XXXX" dir="ltr">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور</label>
            <input id="password" type="password" name="password" required
                   autocomplete="new-password" class="input" placeholder="8 أحرف على الأقل">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   autocomplete="new-password" class="input" placeholder="أعد كتابة كلمة المرور">
        </div>
        <label class="flex items-start gap-2 cursor-pointer">
            <input type="checkbox" name="terms" required class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-xs text-gray-600 leading-relaxed">
                أوافق على <a href="/terms" class="text-brand-600 hover:underline">شروط الاستخدام</a>
                و <a href="/privacy" class="text-brand-600 hover:underline">سياسة الخصوصية</a>
            </span>
        </label>
        <button type="submit" class="btn-primary w-full py-3">إنشاء الحساب</button>
    </form>
@endsection

@section("footer-link")
    لديك حساب بالفعل؟ <a href="/login" class="text-brand-600 hover:text-brand-700 font-semibold">سجّل دخولك</a>
@endsection
