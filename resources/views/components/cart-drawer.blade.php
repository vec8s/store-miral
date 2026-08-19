{{-- Backdrop --}}
<div @click="$store.cart.isOpen = false" 
     x-show="$store.cart.isOpen" 
     x-transition.opacity.duration.200ms
     class="fixed inset-0 bg-inkBlack/40 backdrop-blur-sm z-40" 
     style="display: none;"></div>

{{-- Drawer Panel --}}
<div @click.stop 
     x-show="$store.cart.isOpen" 
     x-transition:enter="transition ease-out duration-250" 
     x-transition:enter-start="translate-x-full lg:translate-x-1/4" 
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="translate-x-0" 
     x-transition:leave-end="translate-x-full lg:translate-x-1/4"
     class="fixed top-0 bottom-0 end-0 w-full max-w-md bg-pureWhite shadow-deep z-50 flex flex-col rounded-s-cards lg:rounded-s-cards overflow-hidden"
     style="display: none;">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-faintBorder">
        <h2 class="text-display font-semibold text-inkBlack tracking-shop-display">السلة</h2>
        <button @click="$store.cart.isOpen = false" 
                class="w-10 h-10 rounded-full bg-canvasMist hover:bg-faintBorder flex items-center justify-center transition-colors">
            <svg class="w-5 h-5 text-inkBlack" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Items --}}
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-3">
        <template x-if="$store.cart.items.length === 0">
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 bg-canvasMist rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-mutedGray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <p class="text-body text-mutedGray tracking-shop">السلة فارغة حالياً</p>
            </div>
        </template>

        <template x-for="item in $store.cart.items" :key="item.id">
            <div class="flex gap-3 p-3 bg-canvasMist rounded-cards">
                <img :src="item.thumbnail_url || item.image" class="w-20 h-20 rounded-inner object-cover bg-pureWhite">
                <div class="flex-1 flex flex-col justify-between py-1">
                    <div>
                        <h3 class="text-body font-semibold text-inkBlack tracking-shop line-clamp-2" x-text="item.name"></h3>
                        <p class="text-body-sm text-mutedGray tracking-shop-meta mt-1" x-text="`${item.price} ر.س`"></p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 bg-pureWhite rounded-full border border-faintBorder px-2 py-1">
                            <button @click="$store.cart.updateQuantity(item.id, item.quantity - 1)" class="w-6 h-6 flex items-center justify-center text-mutedGray hover:text-inkBlack">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span class="text-body-sm font-medium text-inkBlack w-6 text-center" x-text="item.quantity"></span>
                            <button @click="$store.cart.updateQuantity(item.id, item.quantity + 1)" class="w-6 h-6 flex items-center justify-center text-mutedGray hover:text-inkBlack">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <button @click="$store.cart.remove(item.id)" class="text-mutedGray hover:text-inkBlack text-caption tracking-shop-meta">إزالة</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Footer / Summary --}}
    <div class="border-t border-faintBorder p-6 bg-pureWhite">
        <div class="flex justify-between items-center mb-6">
            <span class="text-body text-mutedGray tracking-shop">المجموع</span>
            <span class="text-display font-semibold text-inkBlack tracking-shop-display" x-text="`${$store.cart.total.toFixed(2)} ر.س`"></span>
        </div>
        <a href="/checkout" 
           class="block w-full h-12 bg-shopViolet text-white text-center rounded-full font-medium shadow-violet-glow hover:bg-[#4527c9] active:scale-[0.98] transition-all flex items-center justify-center text-body-lg tracking-shop-lg">
            إتمام الشراء
        </a>
    </div>
</div>
