@extends("layouts.app")

@section("title", "حسابي — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">حسابي</h1>

    <div class="grid lg:grid-cols-4 gap-8">
        @include("customer.account._sidebar", ["active" => "profile"])

        <div class="lg:col-span-3">
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">المعلومات الشخصية</h2>

                @if(session("success"))
                    <x-alert type="success" class="mb-5">{{ session("success") }}</x-alert>
                @endif

                <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-5">
                    @csrf 
                    @method("PUT")

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">الاسم الكامل</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="input">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">رقم الجوال</label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" dir="ltr" class="input">
                            @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                            <input type="email" value="{{ auth()->user()->email ?? '' }}" disabled class="input bg-gray-50" dir="ltr">
                            <p class="text-xs text-gray-500 mt-1">لا يمكن تغيير البريد الإلكتروني.</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary px-8 py-2.5">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
