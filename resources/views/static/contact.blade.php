@extends("layouts.app")

@section("title", "تواصل معنا — رافال")

@section("content")
<div class="container-rtl py-16 max-w-2xl">
    <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">تواصل معنا</h1>
    <div class="card p-8 text-center space-y-6">
        <p class="text-lg text-gray-700">نسعد بتواصلك معنا في أي وقت لتقديم الخدمة والدعم المطلوب</p>
        <div class="space-y-4 text-gray-700 font-medium">
            <div class="flex items-center justify-center gap-2">
                <span>📧</span>
                <a href="mailto:info@rafal-store.sa" class="hover:text-brand-600 transition" dir="ltr">info@rafal-store.sa</a>
            </div>
            <div class="flex items-center justify-center gap-2">
                <span>📱</span>
                <a href="tel:+966500000000" class="hover:text-brand-600 transition" dir="ltr">+966 50 000 0000</a>
            </div>
            <div class="flex items-center justify-center gap-2">
                <span>📍</span>
                <span>الرياض، المملكة العربية السعودية</span>
            </div>
        </div>
    </div>
</div>
@endsection
