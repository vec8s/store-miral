<x-layouts.app title="إعادة تعيين كلمة المرور — ميرال">
  <div class="container-rtl py-10 sm:py-14 flex justify-center">
    <div class="w-full max-w-md px-4 sm:px-0">
      <div class="card-awesomic p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-[#09090b] text-center">إعادة تعيين كلمة المرور 🔑</h1>
        <p class="text-sm text-[#71717a] text-center mt-1.5 mb-7">أدخل كلمة المرور الجديدة لحسابك</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? request('email') }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old("email") }}"
                       required autocomplete="email" class="input-awesomic" placeholder="you@example.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور الجديدة</label>
                <input id="password" type="password" name="password" required minlength="8"
                       autocomplete="new-password" class="input-awesomic" placeholder="8 أحرف على الأقل">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                       autocomplete="new-password" class="input-awesomic" placeholder="أعد كتابة كلمة المرور">
            </div>
            <button type="submit" class="btn-primary w-full py-3 min-h-[44px]">إعادة تعيين كلمة المرور</button>
        </form>
      </div>
      <p class="text-sm text-center text-[#52525b] mt-6">
        تذكّرت كلمة المرور؟ <a href="{{ route('login') }}" class="text-[#ff5a00] hover:text-[#d94d00] font-semibold">العودة لتسجيل الدخول</a>
      </p>
    </div>
  </div>
</x-layouts.app>