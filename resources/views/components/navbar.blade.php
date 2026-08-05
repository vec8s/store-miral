<nav class="sticky top-0 z-50 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center text-white font-bold text-lg group-hover:shadow-lg transition-all">ر</div>
                <span class="text-xl font-bold text-gray-900 hidden sm:inline">رافال</span>
            </a>
            <div class="hidden md:flex flex-1 mx-8">
                <div class="relative w-full max-w-md">
                    <input type="text" placeholder="ابحث عن منتج..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button @click="$store.cart.isOpen = true" class="relative p-2 hover:bg-gray-100 rounded-lg transition-all group">
                    <svg class="w-6 h-6 text-gray-700 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <template x-if="$store.cart.itemCount > 0">
                        <span class="absolute -top-1 -left-1 w-5 h-5 bg-brand-600 text-white text-xs rounded-full flex items-center justify-center font-bold" x-text="$store.cart.itemCount"></span>
                    </template>
                </button>
            </div>
        </div>
    </div>
</nav>
