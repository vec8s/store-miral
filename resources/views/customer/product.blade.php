@extends("layouts.app")
@section("title", $product->name . " — رافال")
@section("content")
<div class="py-8 lg:py-12">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-caption text-mutedGray tracking-shop-meta mb-8">
        <a href="/" class="hover:text-inkBlack transition-colors">الرئيسية</a>
        <span class="text-coolStone">/</span>
        <a href="/shop" class="hover:text-inkBlack transition-colors">المتجر</a>
        <span class="text-coolStone">/</span>
        <span class="text-inkBlack">{{ $product->name }}</span>
    </nav>

    <div x-data="{ qty: 1 }" class="grid lg:grid-cols-2 gap-8 lg:gap-12">
        {{-- Image Card --}}
        <div>
            <div class="bg-pureWhite rounded-cards shadow-lift overflow-hidden aspect-square">
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
        </div>

        {{-- Details --}}
        <div class="flex flex-col gap-6">
            <div>
                @if($product->category ?? false)
                    <a href="/shop" class="text-caption text-mutedGray tracking-shop-meta hover:text-inkBlack transition-colors">
                        {{ $product->category->name }}
                    </a>
                @endif
                <h1 class="text-display font-semibold text-inkBlack tracking-shop-display mt-2">{{ $product->name }}</h1>
            </div>

            {{-- Rating --}}
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= 4 ? 'text-inkBlack fill-inkBlack' : 'text-coolStone' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.447a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.447a1 1 0 00-1.176 0l-3.366 2.447c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.063 9.384c-.784-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.957z"/></svg>
                    @endfor
                </div>
                <span class="text-caption text-mutedGray tracking-shop-meta">({{ $product->reviews_count ?? 23 }} تقييم)</span>
            </div>

            {{-- Price --}}
            <div class="flex items-baseline gap-3">
                <span class="text-display font-semibold text-inkBlack tracking-shop-display">{{ number_format($product->sale_price ?? $product->price, 2) }} <span class="text-body-lg">ر.س</span></span>
                @if($product->sale_price ?? false)
                    <span class="text-body-lg text-mutedGray line-through tracking-shop-lg">{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            {{-- Description --}}
            <div class="text-body text-mutedGray leading-relaxed tracking-shop">
                {!! $product->description ?? '<p>منتج فاخر من تشكيلة رافال المميزة.</p>' !!}
            </div>

            {{-- Actions --}}
            <div class="flex items-stretch gap-3 mt-4">
                {{-- Quantity Pill --}}
                <div class="flex items-center bg-pureWhite border border-faintBorder rounded-full px-2 shadow-soft">
                    <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-12 flex items-center justify-center text-mutedGray hover:text-inkBlack transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <input type="number" x-model.number="qty" min="1" max="{{ $product->stock ?? 99 }}" class="w-14 text-center bg-transparent border-none focus:ring-0 focus:outline-none text-body-lg font-medium text-inkBlack tracking-shop-lg">
                    <button type="button" @click="qty = Math.min({{ $product->stock ?? 99 }}, qty + 1)" class="w-10 h-12 flex items-center justify-center text-mutedGray hover:text-inkBlack transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                {{-- Add to Cart --}}
                <button type="button" 
                        @click="$store.cart.add({
                            id: {{ $product->id }},
                            name: '{{ addslashes($product->name) }}',
                            price: {{ $product->sale_price ?? $product->price }},
                            thumbnail_url: '{{ $product->thumbnail_url }}'
                        }, qty)" 
                        class="flex-1 h-12 bg-shopViolet text-white rounded-full font-medium shadow-violet-glow hover:bg-[#4527c9] active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-body-lg tracking-shop-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    أضف إلى السلة
                </button>
            </div>

            {{-- Features --}}
            <div class="grid grid-cols-2 gap-3 mt-6">
                <div class="flex items-center gap-2 p-3 bg-pureWhite rounded-cards shadow-soft">
                    <svg class="w-5 h-5 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-body-sm text-mutedGray tracking-shop-meta">توصيل سريع</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-pureWhite rounded-cards shadow-soft">
                    <svg class="w-5 h-5 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="text-body-sm text-mutedGray tracking-shop-meta">إرجاع 14 يوم</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-pureWhite rounded-cards shadow-soft">
                    <svg class="w-5 h-5 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-body-sm text-mutedGray tracking-shop-meta">منتج أصلي 100%</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-pureWhite rounded-cards shadow-soft">
                    <svg class="w-5 h-5 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span class="text-body-sm text-mutedGray tracking-shop-meta">دفع آمن</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
