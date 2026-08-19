@extends('layouts.app')
@section('title', 'المتجر — رافال')
@section('content')
<div x-data="{
    searchQuery: (new URLSearchParams(window.location.search)).get('q') || '',
    selectedCategory: '',
    minPrice: 0,
    maxPrice: 1000,
    sortOrder: 'name_asc',
    products: @json($products),
    categories: @json($categories),
    get filteredProducts() {
        return this.products.filter(p => {
            const matchesQuery = !this.searchQuery || 
                                 p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                 p.category.name.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchesCategory = !this.selectedCategory || p.category.name === this.selectedCategory;
            const price = p.sale_price || p.price;
            const matchesPrice = price >= this.minPrice && price <= this.maxPrice;
            return matchesQuery && matchesCategory && matchesPrice;
        }).sort((a, b) => {
            const priceA = a.sale_price || a.price;
            const priceB = b.sale_price || b.price;
            if (this.sortOrder === 'price_asc') return priceA - priceB;
            if (this.sortOrder === 'price_desc') return priceB - priceA;
            if (this.sortOrder === 'name_asc') return a.name.localeCompare(b.name, 'ar');
            if (this.sortOrder === 'name_desc') return b.name.localeCompare(a.name, 'ar');
            return 0;
        });
    }
}">

    {{-- Hero Band --}}
    <section class="py-12 lg:py-16 text-center">
        <h1 class="text-display font-semibold text-inkBlack tracking-shop-display">
            متجر<span class="text-shopViolet">.</span>
        </h1>
        <p class="text-body text-mutedGray mt-2 max-w-md mx-auto tracking-shop">تصفح تشكيلتنا الحصرية من المنتجات الفاخرة</p>
    </section>

    {{-- Category Pills Row --}}
    <section class="flex flex-wrap items-center justify-center gap-3 pb-16">
        <button @click="selectedCategory = ''"
                :class="selectedCategory === '' ? 'ring-2 ring-inkBlack' : ''"
                class="inline-flex items-center gap-2 bg-pureWhite border border-faintBorder rounded-full px-4 py-1.5 shadow-soft hover:shadow-lift transition-all">
            <span class="w-4 h-4 rounded-full bg-shopViolet"></span>
            <span class="text-body-lg text-inkBlack tracking-shop-lg">الكل</span>
        </button>
        <template x-for="cat in categories" :key="cat">
            <button @click="selectedCategory = cat"
                    :class="selectedCategory === cat ? 'ring-2 ring-inkBlack' : ''"
                    class="inline-flex items-center gap-2 bg-pureWhite border border-faintBorder rounded-full px-4 py-1.5 shadow-soft hover:shadow-lift transition-all">
                <span class="w-4 h-4 rounded-full bg-warmFog"></span>
                <span class="text-body-lg text-inkBlack tracking-shop-lg" x-text="cat"></span>
            </button>
        </template>
    </section>

    {{-- Section Header --}}
    <section class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <h2 class="text-display font-semibold text-inkBlack tracking-shop-display" x-text="selectedCategory || 'جميع المنتجات'"></h2>
            <svg class="w-4 h-4 text-inkBlack -scale-x-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
        <select x-model="sortOrder" class="bg-pureWhite border border-faintBorder rounded-full px-4 py-2 text-body-sm text-mutedGray focus:outline-none focus:ring-2 focus:ring-shopViolet shadow-soft tracking-shop-meta">
            <option value="name_asc">الاسم (أ - ي)</option>
            <option value="name_desc">الاسم (ي - أ)</option>
            <option value="price_asc">السعر (تصاعدي)</option>
            <option value="price_desc">السعر (تنازلي)</option>
        </select>
    </section>

    {{-- Search + Price (Compact Pills) --}}
    <section class="flex flex-wrap items-center gap-3 mb-12">
        <div class="flex items-center gap-2 bg-pureWhite border border-faintBorder rounded-full px-4 py-2 shadow-soft">
            <svg class="w-4 h-4 text-mutedGray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input type="text" x-model="searchQuery" placeholder="بحث سريع..." class="bg-transparent border-none focus:ring-0 focus:outline-none text-body-sm text-inkBlack placeholder:text-mutedGray/70 w-40 tracking-shop-meta">
        </div>
        <div class="flex items-center gap-2 bg-pureWhite border border-faintBorder rounded-full px-4 py-2 shadow-soft text-body-sm text-mutedGray tracking-shop-meta">
            <span>السعر:</span>
            <input type="number" x-model.number="minPrice" class="w-14 bg-transparent border-none p-0 focus:ring-0 focus:outline-none text-inkBlack text-body-sm" placeholder="0">
            <span class="text-coolStone">—</span>
            <input type="number" x-model.number="maxPrice" class="w-14 bg-transparent border-none p-0 focus:ring-0 focus:outline-none text-inkBlack text-body-sm" placeholder="1000">
            <span>ر.س</span>
        </div>
    </section>

    {{-- Products Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        <template x-for="product in filteredProducts" :key="product.id">
            <article class="group relative bg-pureWhite rounded-cards overflow-hidden shadow-lift hover:shadow-deep transition-shadow duration-300">
                {{-- Badges --}}
                <div class="absolute top-4 start-4 z-10 flex flex-col gap-2">
                    <template x-if="product.sale_price">
                        <span class="bg-pureWhite/90 backdrop-blur text-inkBlack text-caption font-medium px-3 py-1 rounded-full shadow-soft tracking-shop-meta">تخفيض</span>
                    </template>
                </div>

                {{-- Image: 28px card radius, 20px internal frame --}}
                <a :href="'/shop/' + product.id" class="block aspect-square overflow-hidden p-2">
                    <img :src="product.thumbnail_url" :alt="product.name" loading="lazy"
                         class="w-full h-full object-cover rounded-inner group-hover:scale-[1.03] transition-transform duration-500">
                </a>

                {{-- Details --}}
                <div class="px-2 pb-3 flex flex-col gap-3">
                    <div class="px-1">
                        <p class="text-caption text-mutedGray tracking-shop-meta mb-1" x-text="product.category.name"></p>
                        <h3 class="text-body font-semibold text-inkBlack tracking-shop line-clamp-2 min-h-[2.4em]">
                            <a :href="'/shop/' + product.id" x-text="product.name"></a>
                        </h3>
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-body-lg font-semibold text-inkBlack tracking-shop-lg" x-text="(product.sale_price || product.price) + ' ر.س'"></span>
                            <template x-if="product.sale_price">
                                <span class="text-body-sm text-mutedGray line-through tracking-shop-meta" x-text="product.price + ' ر.س'"></span>
                            </template>
                        </div>
                        <button @click.prevent="$store.cart.add(product)" 
                                class="w-10 h-10 rounded-full bg-shopViolet text-white flex items-center justify-center shadow-violet-glow hover:bg-[#4527c9] active:scale-95 transition-all"
                                aria-label="أضف للسلة">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
            </article>
        </template>
    </div>

    {{-- Empty State --}}
    <template x-if="filteredProducts.length === 0">
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-20 h-20 bg-pureWhite rounded-full flex items-center justify-center shadow-lift mb-6">
                <svg class="w-8 h-8 text-mutedGray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            </div>
            <h3 class="text-display font-semibold text-inkBlack tracking-shop-display">لا توجد نتائج</h3>
            <p class="text-body text-mutedGray mt-2 tracking-shop">حاول البحث بكلمات أخرى أو تغيير خيارات التصفية.</p>
        </div>
    </template>
</div>
@endsection
