<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ProductCard from "../../Components/ProductCard.vue";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    searchQuery: {
        type: String,
        default: "",
    },
    selectedCategory: {
        type: String,
        default: null,
    },
});

const query = ref(props.searchQuery);

const submitSearch = () => {
    router.get("/shop", { q: query.value, category: props.selectedCategory || undefined }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <div class="mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight">منتجات ميرال</h1>
                <p class="text-fog text-xs sm:text-sm mt-1">تصفّح التشكيلة الكاملة المزامنة حياً مع نظام سلة</p>
            </div>

            <div class="card-awesomic p-5 mb-8 flex flex-col md:flex-row gap-4 justify-between items-center">
                <form @submit.prevent="submitSearch" class="w-full md:w-80 relative">
                    <input
                        v-model="query"
                        type="search"
                        name="q"
                        placeholder="ابحث باسم المنتج..."
                        class="input-awesomic pr-11 py-2.5 text-xs sm:text-sm"
                    >
                    <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-fog">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>
                    </button>
                </form>

                <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
                    <Link
                        href="/shop"
                        class="px-4 py-2 rounded-pill text-xs font-medium shrink-0 transition"
                        :class="!selectedCategory
                            ? 'bg-obsidian text-white'
                            : 'bg-[#fafafa] text-graphite border border-cloud hover:bg-paper'"
                    >الكل</Link>
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="`/shop?category=${encodeURIComponent(cat.name)}`"
                        class="px-4 py-2 rounded-pill text-xs font-medium shrink-0 transition"
                        :class="selectedCategory === cat.name
                            ? 'bg-obsidian text-white'
                            : 'bg-[#fafafa] text-graphite border border-cloud hover:bg-paper'"
                    >{{ cat.icon }} {{ cat.name }}</Link>
                </div>
            </div>

            <div v-if="products.length === 0" class="card-awesomic p-12 text-center my-12">
                <div class="text-6xl mb-4">
                    <svg class="w-16 h-16 mx-auto text-fog" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-obsidian mb-2">لم يتم العثور على منتجات مطابقة</h3>
                <p class="text-fog text-xs sm:text-sm mb-6">يرجى تجربة كود أو اسم آخر أو تصفح الأقسام الرئيسية</p>
                <Link href="/shop" class="btn-primary text-xs px-6 py-3">عرض جميع المنتجات</Link>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>

        </div>
    </div>
</template>