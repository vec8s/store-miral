<script setup>
import { Link } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-2">قائمة الطلبات</h1>
            <p class="text-fog text-xs sm:text-sm mb-8">استعرض سجل المشتريات ومتابعة حالة التجهيز والشحن</p>

            <div v-if="orders.length === 0" class="card-awesomic p-12 text-center my-8">
                <div class="text-6xl mb-4">📦</div>
                <h3 class="text-lg font-bold text-obsidian mb-2">لا توجد طلبات سابقة</h3>
                <p class="text-fog text-xs sm:text-sm mb-6">ابدأ تجربة التسوق الأولى واختر من تشكيلاتنا الفاخرة</p>
                <Link href="/shop" class="btn-primary text-xs px-8 py-3">تصفح المنتجات</Link>
            </div>

            <div v-else class="space-y-4 max-w-4xl">
                <div v-for="order in orders" :key="order.id" class="card-awesomic p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-3">
                            <span class="font-extrabold text-obsidian text-base">رقم الطلب #{{ order.number }}</span>
                            <span class="badge-tag">{{ order.status?.label }}</span>
                        </div>
                        <p class="text-xs text-fog">التاريخ: {{ order.created_at }}</p>
                        <p class="text-xs text-steel">وسيلة الدفع: {{ order.payment_method }}</p>
                    </div>

                    <div class="flex items-center justify-between w-full sm:w-auto gap-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-cloud">
                        <div class="text-left">
                            <p class="text-[11px] text-fog">المبلغ الإجمالي</p>
                            <p class="text-lg font-extrabold text-obsidian">{{ order.total }} <span class="currency-sar text-xs">ر.س</span></p>
                        </div>

                        <Link :href="`/orders/${order.id}`" class="btn-ghost px-4 py-2 text-xs min-h-[44px]">
                            تفاصيل الطلب &larr;
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>