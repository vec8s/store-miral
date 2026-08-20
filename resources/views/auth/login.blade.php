<x-layouts.app title="تسجيل الدخول — ميرال">
  <div class="container-rtl py-10 sm:py-14 flex justify-center">
    <div class="w-full max-w-md px-4 sm:px-0">
      <div class="card-awesomic p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-[#09090b] text-center">أهلاً بعودتك 👋</h1>
        <p class="text-sm text-[#71717a] text-center mt-1.5 mb-7">سجّل دخولك للوصول لحسابك ومتابعة طلباتك</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old("email") }}" required autofocus
                       autocomplete="email" class="input-awesomic" placeholder="you@example.com">
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-[#ff5a00] hover:text-[#d94d00]">نسيت كلمة المرور؟</a>
                </div>
                <input id="password" type="password" name="password" required
                       autocomplete="current-password" class="input-awesomic" placeholder="••••••••">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#ff5a00] focus:ring-[#ff5a00]">
                <span class="text-sm text-gray-600">تذكّرني</span>
            </label>
            <button type="submit" class="btn-primary w-full py-3 min-h-[44px]">تسجيل الدخول</button>
        </form>
      </div>
      <p class="text-sm text-center text-[#52525b] mt-6">
        ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-[#ff5a00] hover:text-[#d94d00] font-semibold">أنشئ حساباً جديداً</a>
      </p>
    </div>
  </div>
</x-layouts.app>