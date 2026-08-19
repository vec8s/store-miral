@props(["product"])

@php
    $price       = $product->price ?? null;
    $salePrice   = $product->sale_price ?? null;
    $hasDiscount = $salePrice !== null && $salePrice < $price;
    $image       = $product->thumbnail_url ?? asset("images/placeholder-product.svg");
    $isNew       = $product->created_at?->gt(now()->subDays(7)) ?? false;
@endphp

<article class="group relative bg-pureWhite rounded-cards overflow-hidden shadow-lift hover:shadow-deep transition-shadow duration-300" x-data="{ liked: false }">
    {{-- Badges --}}
    <div class="absolute top-4 start-4 z-10 flex flex-col gap-2">
        @if($isNew)
            <span class="bg-pureWhite/90 backdrop-blur text-inkBlack text-caption font-medium px-3 py-1 rounded-full shadow-soft tracking-shop-meta">جديد</span>
        @endif
        @if($hasDiscount)
            @php $discountPct = (int) round((($price - $salePrice) / $price) * 100); @endphp
            <span class="bg-pureWhite/90 backdrop-blur text-inkBlack text-caption font-medium px-3 py-1 rounded-full shadow-soft tracking-shop-meta">-{{ $discountPct }}%</span>
        @endif
    </div>

    {{-- Wishlist toggle --}}
    <button @click.prevent="liked = !liked"
            class="absolute top-4 end-4 z-10 w-9 h-9 rounded-full bg-pureWhite/90 backdrop-blur shadow-soft flex items-center justify-center transition hover:shadow-lift"
            aria-label="إضافة للمفضلة">
        <svg :class="liked ? 'fill-inkBlack' : 'fill-none'" class="w-4 h-4 text-inkBlack" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
    </button>

    {{-- Image: zero card padding, white frame via inner 20px radius --}}
    <a href="{{ route('shop.show', $product->slug ?? $product->id) }}" class="block aspect-square overflow-hidden p-2">
        <img src="{{ $image }}"
             alt="{{ $product->name }}"
             loading="lazy"
             class="w-full h-full object-cover rounded-inner group-hover:scale-[1.03] transition-transform duration-500">
    </a>

    {{-- Body --}}
    <div class="px-2 pb-3 flex flex-col gap-3">
        <div class="px-1">
            @if($product->category?->name ?? false)
                <p class="text-caption text-mutedGray tracking-shop-meta mb-1">{{ $product->category->name }}</p>
            @endif
            <h3 class="text-body font-semibold text-inkBlack tracking-shop line-clamp-2 min-h-[2.4em]">
                <a href="{{ route('shop.show', $product->slug ?? $product->id) }}">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <div class="flex items-center justify-between px-1">
            <div class="flex items-baseline gap-2">
                @if($price !== null)
                    <span class="text-body-lg font-semibold text-inkBlack tracking-shop-lg">{{ number_format($salePrice ?? $price, 2) }} ر.س</span>
                    @if($hasDiscount)
                        <span class="text-body-sm text-mutedGray line-through tracking-shop-meta">{{ number_format($price, 2) }}</span>
                    @endif
                @else
                    <span class="text-body-sm text-mutedGray tracking-shop-meta">—</span>
                @endif
            </div>

            <button @click.prevent="$store.cart.add({
                        id: {{ $product->id }},
                        name: '{{ addslashes($product->name) }}',
                        price: {{ $product->sale_price ?? $product->price ?? 0 }},
                        thumbnail_url: '{{ $image }}'
                    })"
                    class="w-10 h-10 rounded-full bg-shopViolet text-white flex items-center justify-center shadow-violet-glow hover:bg-[#4527c9] active:scale-95 transition-all"
                    aria-label="أضف للسلة">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
        </div>
    </div>
</article>
