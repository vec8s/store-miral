<script setup>
import { computed, inject } from "vue";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const triggerToast = inject("triggerToast", () => {});

const discountPercent = computed(() =>
    props.product.sale_price
        ? Math.round((1 - props.product.sale_price / props.product.price) * 100)
        : 0,
);

const categoryName = computed(() => props.product.category?.name ?? "");

const addToCart = async () => {
    try {
        const { data } = await window.axios.post("/api/cart/add", {
            product_id: props.product.id,
            quantity: 1,
        });

        if (data.success) {
            triggerToast(data.message || "تمت الإضافة إلى السلة بنجاح");
        }
    } catch (err) {
        console.error(err);
    }
};

const toggleWishlist = async () => {
    try {
        const { data } = await window.axios.post("/api/wishlist/toggle", {
            product_id: props.product.id,
        });

        if (data.success) {
            triggerToast(
                data.added
                    ? "تمت إضافة المنتج للمفضلة"
                    : "تم حذف المنتج من المفضلة",
            );
        }
    } catch (err) {
        console.error(err);
    }
};
</script>

<template>
    <div class="card-awesomic p-4 group flex flex-col justify-between hover:border-obsidian transition-all">
        <div class="relative aspect-square overflow-hidden rounded-[24px] bg-paper mb-4">
            <img
                :src="product.thumbnail_url"
                :alt="product.name"
                loading="lazy"
                decoding="async"
                width="400"
                height="400"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
            >

            <span v-if="product.sale_price" class="absolute top-3 right-3 badge-ember">
                خصم {{ discountPercent }}%
            </span>

            <button
                @click="toggleWishlist"
                class="absolute top-3 left-3 w-9 h-9 rounded-badge bg-white border border-cloud flex items-center justify-center text-graphite hover:bg-[#fafafa] transition"
                title="إضافة للمفضلة"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        <div class="flex flex-col flex-grow">
            <span class="text-[11px] font-medium text-fog mb-1">{{ categoryName }}</span>
            <a :href="`/shop/${product.id}`" class="font-bold text-obsidian hover:underline line-clamp-1 mb-2 text-sm">
                {{ product.name }}
            </a>

            <div class="flex items-center gap-1 text-xs text-ember mb-4">
                <span>★ {{ product.reviews_avg_rating }}</span>
                <span class="text-ash">({{ product.reviews_count }})</span>
            </div>

            <div class="mt-auto flex items-center justify-between pt-3 border-t border-cloud">
                <div>
                    <span class="text-base font-bold text-obsidian">
                        {{ product.sale_price || product.price }} <span class="currency-sar">ر.س</span>
                    </span>
                    <span v-if="product.sale_price" class="text-xs text-ash line-through block">
                        {{ product.price }} <span class="currency-sar">ر.س</span>
                    </span>
                </div>

                <button
                    @click="addToCart"
                    class="btn-primary text-xs px-3.5 py-2.5 rounded-badge"
                    title="إضافة للسلة"
                >+ إضافة</button>
            </div>
        </div>
    </div>
</template>