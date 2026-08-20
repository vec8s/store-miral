<script setup>
import { Link } from "@inertiajs/vue3";
import ProductCard from "../../Components/ProductCard.vue";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

defineProps({
    wishlist: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-2">المفضلة</h1>
            <p class="text-fog text-xs sm:text-sm mb-8">قطع الحلي والهدايا المحفوظة للرجوع إليها لاحقاً</p>

            <div v-if="wishlist.length === 0" class="card-awesomic p-12 text-center my-8">
                <div class="text-6xl mb-4">🤍</div>
                <h3 class="text-lg font-bold text-obsidian mb-2">قائمة المفضلة فارغة حالياً</h3>
                <p class="text-fog text-xs sm:text-sm mb-6">تصفح المتجر وانقر على رمز القلب لإضافة القطع لقائمتك</p>
                <Link href="/shop" class="btn-primary text-xs px-8 py-3">تصفح المتجر</Link>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <ProductCard v-for="product in wishlist" :key="product.id" :product="product" />
            </div>

        </div>
    </div>
</template>