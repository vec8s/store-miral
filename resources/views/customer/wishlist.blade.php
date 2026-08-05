@extends("layouts.app")

@section("title", "المفضلة — رافال")

@section("content")
<div class="container-rtl py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">قائمة المفضلة</h1>
            <p class="text-gray-500 text-sm mt-1">{{ ($wishlist ?? collect())->count() }} منتج في المفضلة</p>
        </div>
        <a href="{{ route('shop.index') }}" class="btn-ghost text-sm">متابعة التسوق</a>
    </div>

    @if(($wishlist ?? collect())->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-7xl mb-4">💝</div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">قائمة المفضلة فارغة</h2>
            <p class="text-gray-500 mb-6">أضف منتجاتك المفضلة لتسهيل العودة إليها لاحقاً</p>
            <a href="{{ route('shop.index') }}" class="btn-primary px-8 py-3">تصفّح المنتجات</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($wishlist as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif
</div>
@endsection
