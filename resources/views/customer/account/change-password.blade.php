@extends("layouts.app")

@section("title", "تغيير كلمة المرور — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">تغيير كلمة المرور</h1>

    <div class="grid lg:grid-cols-4 gap-8">
        @include("customer.account._sidebar", ["active" => "password"])

        <div class="lg:col-span-3">
            <div class="card p-6 max-w-xl">
                @if(session("success"))
                    <x-alert type="success" class="mb-5">{{ session("success") }}</x-alert>
                @endif

                <form action="{{ route('account.password.update') }}" method="POST" class="space-y-5">
                    @csrf 
                    @method("PUT")

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" required class="input" autocomplete="current-password">
                        @error("current_password") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور الجديدة</label>
                        <input type="password" name="password" required class="input" autocomplete="new-password">
                        @error("password") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="password_confirmation" required class="input" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn-primary px-8 py-2.5">تحديث كلمة المرور</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
