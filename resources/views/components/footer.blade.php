<footer class="bg-gray-900 text-gray-300 mt-20">
    <div class="container-rtl py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center gap-2 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-gold-500 flex items-center justify-center text-white text-xl font-bold">ر</div>
                <span class="text-xl font-bold text-white">رافال</span>
            </div>
            <p class="text-sm leading-relaxed">متجر رافال — صيحة في عالم الحلي نقدم باقات تناسب جميع الفئات من الرياض إلى جميع أرجاء المملكة.</p>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-4">روابط سريعة</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ url('/') }}" class="hover:text-brand-400 transition">الرئيسية</a></li>
                <li><a href="{{ route('shop.index') }}" class="hover:text-brand-400 transition">المتجر</a></li>
                <li><a href="{{ url('/categories') }}" class="hover:text-brand-400 transition">الأقسام</a></li>
                <li><a href="{{ url('/about') }}" class="hover:text-brand-400 transition">من نحن</a></li>
                <li><a href="{{ url('/contact') }}" class="hover:text-brand-400 transition">تواصل معنا</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-4">خدمة العملاء</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ url('/shipping') }}" class="hover:text-brand-400 transition">سياسة الشحن</a></li>
                <li><a href="{{ url('/returns') }}" class="hover:text-brand-400 transition">سياسة الإرجاع</a></li>
                <li><a href="{{ url('/privacy') }}" class="hover:text-brand-400 transition">سياسة الخصوصية</a></li>
                <li><a href="{{ url('/faq') }}" class="hover:text-brand-400 transition">الأسئلة الشائعة</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-4">تواصل معنا</h3>
            <ul class="space-y-3 text-sm">
                <li>📍 الرياض، المملكة العربية السعودية</li>
                <li dir="ltr">📱 +966 50 000 0000</li>
                <li>📧 info@rafal-store.sa</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-gray-800">
        <div class="container-rtl py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400">
            <p>© {{ date('Y') }} متجر رافال. جميع الحقوق محفوظة.</p>
            <p>مدعوم بـ ❤️ في المملكة العربية السعودية</p>
        </div>
    </div>
</footer>
