<x-layouts.app title="نسيت كلمة المرور — ميرال">
  <div class="container-rtl py-10 sm:py-14 flex justify-center">
    <div class="w-full max-w-md px-4 sm:px-0">
      <div class="card-awesomic p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-[#09090b] text-center">نسيت كلمة المرور؟ 🔒</h1>
        <p class="text-sm text-[#71717a] text-center mt-1.5 mb-7">أدخل بريدك وسنرسل لك رابطاً لإعادة تعيين كلمة المرور</p>

        @if (session("status"))
            <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session("status") }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old("email") }}" required autofocus
                       class="input-awesomic" placeholder="you@example.com">
            </div>
            <button type="submit" class="btn-primary w-full py-3 min-h-[44px]">إرسال رابط إعادة التعيين</button>
        </form>
      </div>
      <p class="text-sm text-center text-[#52525b] mt-6">
        تذكّرت كلمة المرور؟ <a href="{{ route('login') }}" class="text-[#ff5a00] hover:text-[#d94d00] font-semibold">العودة لتسجيل الدخول</a>
      </p>
    </div>
  </div>
</x-layouts.app>