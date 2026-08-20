<x-layouts.app title="حساب جديد — ميرال">
  <div class="container-rtl py-10 sm:py-14 flex justify-center">
    <div class="w-full max-w-md px-4 sm:px-0">
      <div class="card-awesomic p-6 sm:p-8">
        <h1 class="text-2xl font-bold text-[#09090b] text-center">أنشئ حسابك في ميرال ✨</h1>
        <p class="text-sm text-[#71717a] text-center mt-1.5 mb-7">انضم إلينا واستمتع بتجربة تسوق مميزة ومكافآت حصرية</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">الاسم الكامل</label>
                <input id="name" type="text" name="name" value="{{ old("name") }}" required autofocus
                       autocomplete="name" class="input-awesomic" placeholder="محمد عبدالله">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                <input id="email" type="email" name="email" value="{{ old("email") }}" required
                       autocomplete="email" class="input-awesomic" placeholder="you@example.com">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">رقم الجوال (اختياري)</label>
                <input id="phone" type="tel" name="phone" value="{{ old("phone") }}"
                       autocomplete="tel" class="input-awesomic" placeholder="+966 5X XXX XXXX" dir="ltr">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور</label>
                <input id="password" type="password" name="password" required minlength="8"
                       autocomplete="new-password" class="input-awesomic" placeholder="8 أحرف على الأقل">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                       autocomplete="new-password" class="input-awesomic" placeholder="أعد كتابة كلمة المرور">
            </div>
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="terms" required class="mt-0.5 rounded border-gray-300 text-[#ff5a00] focus:ring-[#ff5a00]">
                <span class="text-xs text-gray-600 leading-relaxed">
                    أوافق على <a href="{{ route('terms') }}" class="text-[#ff5a00] hover:underline">شروط الاستخدام</a>
                    و <a href="{{ route('privacy') }}" class="text-[#ff5a00] hover:underline">سياسة الخصوصية</a>
                </span>
            </label>
            <button type="submit" class="btn-primary w-full py-3 min-h-[44px]">إنشاء الحساب</button>
        </form>
      </div>
      <p class="text-sm text-center text-[#52525b] mt-6">
        لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-[#ff5a00] hover:text-[#d94d00] font-semibold">سجّل دخولك</a>
      </p>
    </div>
  </div>
</x-layouts.app>