<aside class="hidden lg:flex flex-col items-center w-16 bg-pureWhite sticky top-0 h-screen z-40 py-8 justify-between shadow-soft">
    <div class="flex flex-col items-center gap-8">
        <a href="/" class="w-10 h-10 flex items-center justify-center text-shopViolet font-bold text-2xl tracking-tighter">ر</a>
        
        <nav class="flex flex-col gap-4">
            <a href="/" class="w-12 h-12 flex items-center justify-center rounded-pills hover:bg-canvasMist transition-colors {{ request()->is("/") ? "bg-canvasMist" : "" }}" title="الرئيسية">
                <svg class="w-6 h-6 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </a>
            <a href="/shop" class="w-12 h-12 flex items-center justify-center rounded-pills hover:bg-canvasMist transition-colors {{ request()->is("shop*") ? "bg-canvasMist" : "" }}" title="المتجر">
                <svg class="w-6 h-6 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </a>
            <button @click="$store.cart.isOpen = true" class="w-12 h-12 flex items-center justify-center rounded-pills hover:bg-canvasMist transition-colors relative" title="السلة">
                <svg class="w-6 h-6 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <template x-if="$store.cart.itemCount > 0">
                    <span class="absolute top-2 left-2 w-4 h-4 bg-shopViolet text-white text-[9px] rounded-full flex items-center justify-center font-bold" x-text="$store.cart.itemCount"></span>
                </template>
            </button>
        </nav>
    </div>

    <div class="w-8 h-8 rounded-full border border-faintBorder flex items-center justify-center bg-canvasMist text-body-xs font-medium text-mutedGray">
        ض
    </div>
</aside>

<!-- Mobile bottom nav -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-pureWhite border-t border-faintBorder flex justify-around py-2 z-50">
    <a href="/" class="p-3 text-inkBlack"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></a>
    <a href="/shop" class="p-3 text-inkBlack"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></a>
    <button @click="$store.cart.isOpen = true" class="p-3 text-inkBlack relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <template x-if="$store.cart.itemCount > 0">
            <span class="absolute top-1 left-1 w-4 h-4 bg-shopViolet text-white text-[9px] rounded-full flex items-center justify-center font-bold" x-text="$store.cart.itemCount"></span>
        </template>
    </button>
</nav>
