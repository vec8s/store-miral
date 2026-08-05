<div @click="$store.cart.isOpen = false" x-show="$store.cart.isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" style="display: none;"></div>
<div @click.stop x-show="$store.cart.isOpen" class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col" style="display: none;">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">السلة 🛒</h2>
        <button @click="$store.cart.isOpen = false" class="p-2 hover:bg-gray-100 rounded-lg transition">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto p-6 space-y-4">
        <template x-if="$store.cart.items.length === 0">
            <p class="text-gray-500 text-center py-12">السلة فارغة حالياً</p>
        </template>
        <template x-for="item in $store.cart.items" :key="item.id">
            <div class="flex gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <img :src="item.image" class="w-20 h-20 rounded-lg object-cover">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 text-sm" x-text="item.name"></h3>
                    <p class="text-brand-600 font-bold text-sm mt-1" x-text="`${item.price} ر.س`"></p>
                </div>
            </div>
        </template>
    </div>
    <div class="border-t border-gray-200 p-6 bg-gray-50">
        <div class="flex justify-between items-center mb-4">
            <span class="text-gray-700 font-semibold">المجموع:</span>
            <span class="text-2xl font-bold text-brand-600" x-text="`${$store.cart.total.toFixed(2)} ر.س`"></span>
        </div>
        <a href="/checkout" class="block w-full py-3 bg-brand-600 text-white text-center rounded-lg font-semibold hover:bg-brand-700 transition">إتمام الشراء 💳</a>
    </div>
</div>
