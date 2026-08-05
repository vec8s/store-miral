@extends("layouts.app")

@section("title", "عناويني — رافال")

@section("content")
<div class="container-rtl py-10">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">عناويني</h1>

    <div class="grid lg:grid-cols-4 gap-8">
        @include("customer.account._sidebar", ["active" => "addresses"])

        <div class="lg:col-span-3 space-y-4">
            @if(session("success"))
                <x-alert type="success" class="mb-5">{{ session("success") }}</x-alert>
            @endif

            @forelse($addresses ?? [] as $address)
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-900">{{ $address->label ?? "المنزل" }}</h3>
                                @if(!empty($address->is_default))
                                    <span class="badge-success text-xs px-2.5 py-0.5">افتراضي</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $address->recipient_name }} · <span dir="ltr">{{ $address->phone }}</span><br>
                                {{ $address->address_line }}<br>
                                {{ $address->city }} {{ $address->postal_code }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('account.addresses.edit', $address->id) }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">تعديل</a>
                            <form action="{{ route('account.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟')">
                                @csrf 
                                @method("DELETE")
                                <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center">
                    <div class="text-6xl mb-3">📍</div>
                    <p class="text-gray-500 mb-4">لا توجد عناوين محفوظة حتى الآن</p>
                    <a href="{{ route('account.addresses.create') }}" class="btn-primary inline-block px-6 py-2.5">إضافة عنوان جديد</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
