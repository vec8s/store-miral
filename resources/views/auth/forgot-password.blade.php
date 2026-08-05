@extends("layouts.guest")

@section("title", "نسيت كلمة المرور — رافال")
@section("heading", "نسيت كلمة المرور؟ 🔒")
@section("subheading", "أدخل بريدك وسنرسل لك رابطاً لإعادة تعيين كلمة المرور")

@section("content")
    @if (session("status"))
        <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session("status") }}
        </div>
    @endif

    <form method="POST" action="/forgot-password" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old("email") }}" required autofocus
                   class="input" placeholder="you@example.com">
        </div>
        <button type="submit" class="btn-primary w-full py-3">إرسال رابط إعادة التعيين</button>
    </form>
@endsection

@section("footer-link")
    تذكّرت كلمة المرور؟ <a href="/login" class="text-brand-600 hover:text-brand-700 font-semibold">العودة لتسجيل الدخول</a>
@endsection
