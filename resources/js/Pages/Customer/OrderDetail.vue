<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    isNew: {
        type: Boolean,
        default: false,
    },
});

const statusSteps = [
    { key: "received", label: "تم استلام الطلب" },
    { key: "processing", label: "جاري التجهيز" },
    { key: "shipped", label: "تم الشحن" },
    { key: "delivered", label: "تم التوصيل" },
];

const statusOrder = ["received", "processing", "shipped", "delivered"];
const currentIndex = statusOrder.indexOf(props.order.status?.value);

const isStepReached = (index) => currentIndex >= index;

const isCod = computed(() => props.order.payment_method === "cod");
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl max-w-4xl">

            <div v-if="isNew" class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-6 rounded-[16px] mb-8 flex items-center gap-4">
                <div class="text-4xl">🎉</div>
                <div>
                    <h3 class="font-bold text-lg">تم استلام طلبك بنجاح!</h3>
                    <p class="text-sm text-emerald-700 mt-1">شكراً لتسوقك من ميرال. جاري تحضير طلبك وسيتم إشعارك فور الشحن.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-obsidian">تفاصيل الطلب #{{ order.number }}</h1>
                    <p class="text-fog text-xs mt-1">تاريخ الطلب: {{ order.created_at }}</p>
                </div>
                <span class="badge-filled text-sm px-3 py-1">{{ order.status?.label }}</span>
            </div>

            <div class="card-awesomic p-6 mb-8">
                <h3 class="text-sm font-bold text-obsidian mb-6">متابعة حالة الشحن</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 text-center text-xs font-semibold text-fog relative">
                    <div
                        v-for="(step, index) in statusSteps"
                        :key="step.key"
                        class="space-y-2"
                        :class="isStepReached(index) ? 'text-obsidian font-bold' : ''"
                    >
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm"
                            :class="isStepReached(index) ? 'bg-obsidian text-white' : 'bg-cloud text-fog'"
                        >{{ index === 0 ? "✓" : index + 1 }}</div>
                        <span>{{ step.label }}</span>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4 sm:gap-6 md:gap-8">

                <div class="md:col-span-2 card-awesomic p-6 space-y-4">
                    <h3 class="text-base font-bold text-obsidian pb-3 border-b border-cloud">المنتجات المطلوبة</h3>

                    <div class="space-y-4">
                        <template v-if="order.items && order.items.length > 0">
                            <div v-for="item in order.items" :key="item.product?.id" class="flex items-center gap-4 text-sm">
                                <img :src="item.product?.thumbnail_url" alt="" class="w-16 h-16 rounded-[12px] object-cover bg-paper shrink-0">
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-bold text-obsidian">{{ item.product?.name }}</h4>
                                    <p class="text-xs text-fog mt-0.5">الكمية: {{ item.quantity }}</p>
                                    <p v-if="item.color" class="text-[11px] text-fog mt-0.5">🎨 اللون: {{ item.color }}</p>
                                    <div v-if="item.gift?.enabled" class="mt-1.5 bg-[#fafafa] border border-cloud rounded-btn px-3 py-2 text-[11px] text-steel space-y-0.5">
                                        <p class="flex items-center gap-1.5"><span>🎁</span> هدية إلى: <strong class="text-obsidian">{{ item.gift.recipient_name }}</strong></p>
                                        <p v-if="item.gift.recipient_phone">📱 جوال المستلم: {{ item.gift.recipient_phone }}</p>
                                        <p v-if="item.gift.message" class="italic">"{{ item.gift.message }}"</p>
                                        <p v-if="item.gift.hide_price" class="text-ember font-bold">السعر مخفي من الفاتورة</p>
                                    </div>
                                </div>
                                <span class="font-extrabold text-obsidian shrink-0">
                                    {{ (item.product?.sale_price || item.product?.price) * item.quantity }} <span class="currency-sar">ر.س</span>
                                </span>
                            </div>
                        </template>
                        <p v-else class="text-xs text-fog">سلسلة ذهبية فاخرة × 1</p>
                    </div>

                    <div class="pt-4 border-t border-cloud space-y-2 text-xs text-steel">
                        <div class="flex justify-between">
                            <span>المجموع الفرعي</span>
                            <span class="font-bold text-obsidian">{{ order.subtotal }} <span class="currency-sar">ر.س</span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>الشحن والتوصيل</span>
                            <span class="font-bold text-obsidian">{{ order.shipping === 0 ? "مجاني" : `${order.shipping} ` }}<span v-if="order.shipping !== 0" class="currency-sar">ر.س</span></span>
                        </div>
                        <div v-if="order.cod_fee" class="flex justify-between">
                            <span>رسوم الدفع عند الاستلام</span>
                            <span class="font-bold text-obsidian">{{ order.cod_fee }} <span class="currency-sar">ر.س</span></span>
                        </div>
                        <div class="flex justify-between text-base font-black text-obsidian pt-2 border-t border-cloud">
                            <span>إجمالي الدفع</span>
                            <span>{{ order.total }} <span class="currency-sar">ر.س</span></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card-awesomic p-6 space-y-3 text-xs">
                        <h3 class="text-sm font-bold text-obsidian pb-2 border-b border-cloud">بيانات المستلم والعنوان</h3>
                        <p><strong class="text-obsidian">الاسم:</strong> {{ order.shipping_name }}</p>
                        <p><strong class="text-obsidian">الجوال:</strong> {{ order.shipping_phone }}</p>
                        <p><strong class="text-obsidian">المدينة:</strong> {{ order.shipping_city }}</p>
                        <p><strong class="text-obsidian">العنوان:</strong> {{ order.shipping_address }}</p>
                    </div>

                    <div class="card-awesomic p-6 space-y-3 text-xs">
                        <h3 class="text-sm font-bold text-obsidian pb-2 border-b border-cloud">معلومات الدفع</h3>
                        <p><strong class="text-obsidian">طريقة الدفع:</strong> {{ order.payment_method }}</p>
                        <p><strong class="text-obsidian">حالة الدفع:</strong>
                            <span v-if="isCod" class="badge-ember">الدفع عند الاستلام</span>
                            <span v-else class="badge-filled">تم الدفع</span>
                        </p>
                    </div>

                    <Link href="/orders" class="btn-ghost w-full py-2.5 text-center text-xs font-bold block rounded-[12px]">
                        &rarr; العودة لكافة الطلبات
                    </Link>
                </div>

            </div>

        </div>
    </div>
</template>