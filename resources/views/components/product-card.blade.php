@props(['product'])

@php
    $id = data_get($product, 'id');
    $name = data_get($product, 'name');
    $price = data_get($product, 'price', 0);
    $salePrice = data_get($product, 'sale_price');
    $thumbnailUrl = data_get($product, 'thumbnail_url', 'https://picsum.photos/seed/' . $id . '/400/400');
    $categoryName = data_get($product, 'category.name', 'عام');
    $rating = data_get($product, 'reviews_avg_rating', 4.8);
    $reviewsCount = data_get($product, 'reviews_count', 12);
    $discountPercent = ($salePrice && $price > 0) ? round((1 - ($salePrice / $price)) * 100) : 0;
@endphp

<div class="card-awesomic p-3 sm:p-4 group flex flex-col justify-between hover:border-[#09090b] transition-all">
  <div class="relative aspect-square overflow-hidden rounded-[16px] sm:rounded-[24px] bg-[#f4f4f5] mb-3 sm:mb-4">
    <img src="{{ $thumbnailUrl }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
    
    @if($salePrice)
      <span class="absolute top-2 right-2 sm:top-3 sm:right-3 badge-ember text-[10px] sm:text-xs">
        خصم {{ $discountPercent }}%
      </span>
    @endif

    <button onclick="toggleWishlist({{ $id }})" class="absolute top-2 left-2 sm:top-3 sm:left-3 w-7 h-7 sm:w-9 sm:h-9 rounded-[10px] sm:rounded-[12px] bg-white/90 backdrop-blur border border-[#ececee] flex items-center justify-center text-xs sm:text-sm hover:bg-white transition" title="المفضلة">
      ❤️
    </button>
  </div>

  <div class="flex flex-col flex-grow">
    <span class="text-[10px] sm:text-[11px] font-medium text-[#71717a] mb-0.5">{{ $categoryName }}</span>
    <a href="{{ route('shop.show', $id) }}" class="font-bold text-[#09090b] hover:underline line-clamp-1 mb-1.5 text-xs sm:text-sm">
      {{ $name }}
    </a>

    <div class="flex items-center gap-1 text-[11px] sm:text-xs text-[#ff5a00] mb-3">
      <span>★ {{ $rating }}</span>
      <span class="text-[#a1a1aa]">({{ $reviewsCount }})</span>
    </div>

    <div class="mt-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pt-2.5 border-t border-[#ececee]">
      <div>
        <span class="text-sm sm:text-base font-bold text-[#09090b]">
          {{ number_format($salePrice ?: $price, 2) }} <span class="currency-sar">ر.س</span>
        </span>
        @if($salePrice)
          <span class="text-[10px] sm:text-xs text-[#a1a1aa] line-through block">{{ number_format($price, 2) }} <span class="currency-sar">ر.س</span></span>
        @endif
      </div>

      <button onclick="addToCart({{ $id }})" class="btn-primary w-full sm:w-auto text-[11px] sm:text-xs px-2.5 sm:px-3.5 py-2 sm:py-2.5 rounded-[10px] sm:rounded-[12px]" title="إضافة للسلة">
        + إضــافة
      </button>
    </div>
  </div>
</div>
