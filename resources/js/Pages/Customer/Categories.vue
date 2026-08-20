<script setup>
import { Link } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="py-10 sm:py-16 bg-paper">
        <div class="container-rtl">

            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-2">أقسام المتجر الرئيسية</h1>
                <p class="text-fog text-xs sm:text-sm">اختر الفئة المطلوبة لتصفّح التشكيلات المتاحة بالمخزون</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/shop?category=${encodeURIComponent(cat.name)}`"
                    class="card-awesomic group p-4 sm:p-6 lg:p-8 hover:border-obsidian transition-all text-center"
                >
                    <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">{{ cat.icon }}</div>
                    <h3 class="font-bold text-obsidian text-base mb-2">{{ cat.name }}</h3>
                    <span class="btn-ghost text-xs inline-block px-3 py-1">تصفح التشكيلة &larr;</span>
                </Link>
            </div>

        </div>
    </div>
</template>