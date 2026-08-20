<script setup>
import { Link, router } from "@inertiajs/vue3";
import { reactive } from "vue";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

defineProps({
    cart: {
        type: Array,
        default: () => [],
    },
    subtotal: {
        type: Number,
        default: 0,
    },
    shipping: {
        type: Number,
        default: 0,
    },
    total: {
        type: Number,
        default: 0,
    },
    storeSettings: {
        type: Object,
        default: () => ({ free_shipping_min: 300 }),
    },
});

const giftForms = reactive({});

const ensureGiftForm = (item) => {
    if (!giftForms[item.product.id]) {
        giftForms[item.product.id] = {
            enabled: Boolean(item.gift?.enabled),
            recipient_name: item.gift?.recipient_name ?? "",
            recipient_phone: item.gift?.recipient_phone ?? "",
            message: item.gift?.message ?? "",
            hide_price: Boolean(item.gift?.hide_price),
        };
    }
    return giftForms[item.product.id];
};

const updateCartItem = async (productId, qty) => {
    if (qty < 1) return;

    try {
        await window.axios.post("/api/cart/update", {
            product_id: productId,
            quantity: qty,
        });
        router.reload({ only: ["cart", "subtotal", "shipping", "total"] });
    } catch (err) {
        console.error(err);
    }
};

const removeItem = async (productId) => {
    try {
        await window.axios.post("/api/cart/remove", {
            product_id: productId,
        });
        router.reload({ only: ["cart", "subtotal", "shipping", "total"] });
    } catch (err) {
        console.error(err);
    }
};

const saveGift = async (productId) => {
    const form = giftForms[productId];
    if (!form) return;

    try {
        await window.axios.post("/api/cart/gift", {
            product_id: productId,
            enabled: form.enabled,
            recipient_name: form.recipient_name,
            recipient_phone: form.recipient_phone,
            message: form.message,
            hide_price: form.hide_price,
        });
        router.reload({ only: ["cart"] });
    } catch (err) {
        console.error(err);
    }
};
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-2">سلة التسوق</h1>
            <p class="text-fog text-xs sm:text-sm mb-8">راجع المنتجات قبل الانتقال لمرحلة التحقق والطلب المباشر</p>

            <div v-if="cart.length === 0" class="card-awesomic p-12 text-center my-8">
                <div class="text-6xl mb-4">🛒</div>
                <h3 class="text-lg font-bold text-obsidian mb-2">سلتك فارغة حالياً</h3>
                <p class="text-fog text-xs sm:text-sm mb-6">تصفح التشكيلات الجديدة وأضف حليك المفضلة للسلة</p>
                <Link href="/shop" class="btn-primary text-xs px-8 py-3">التسوق الآن</Link>
            </div>

            <div v-else class="grid lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 items-start">
                <div class="lg:col-span-2 space-y-4">
                    <div v-for="item in cart" :key="item.product.id" class="card-awesomic p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                            <div class="flex items-center gap-4">
                                <img :src="item.product?.thumbnail_url" :alt="item.product?.name" class="w-20 h-20 rounded-[18px] object-cover bg-paper border border-cloud shrink-0">
                                <div>
                                    <span class="text-[11px] text-fog font-medium block">{{ item.product?.category?.name }}</span>
                                    <Link :href="`/shop/${item.product?.id}`" class="font-bold text-obsidian hover:underline text-sm sm:text-base">
                                        {{ item.product?.name }}
                                    </Link>
                                    <p class="text-xs text-fog mt-1">
                                        سعر الوحدة: {{ item.product?.sale_price || item.product?.price }} <span class="currency-sar">ر.س</span>
                                    </p>
                                    <p v-if="item.color" class="text-[11px] text-fog mt-0.5 flex items-center gap-1">
                                        <span>🎨</span> اللون: {{ item.color }}
                                    </p>
                                    <span v-if="item.gift?.enabled" class="badge-ember mt-1.5 inline-block">
                                        🎁 إهداء مفعّل
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between w-full sm:w-auto gap-x-6 gap-y-2 pt-3 sm:pt-0 border-t sm:border-t-0 border-cloud">
                                <div class="flex items-center border border-cloud rounded-badge bg-[#fafafa] px-2 py-1">
                                    <button @click="updateCartItem(item.product.id, item.quantity - 1)" class="w-9 h-9 rounded-[8px] bg-white border border-cloud font-bold text-obsidian hover:bg-paper">-</button>
                                    <span class="px-3 font-bold text-xs text-obsidian">{{ item.quantity }}</span>
                                    <button @click="updateCartItem(item.product.id, item.quantity + 1)" class="w-9 h-9 rounded-[8px] bg-white border border-cloud font-bold text-obsidian hover:bg-paper">+</button>
                                </div>

                                <div class="text-left">
                                    <p class="font-bold text-obsidian text-base">
                                        {{ (item.product?.sale_price || item.product?.price) * item.quantity }} <span class="currency-sar">ر.س</span>
                                    </p>
                                </div>

                                <button @click="removeItem(item.product.id)" class="w-9 h-9 flex items-center justify-center text-fog hover:text-obsidian text-sm font-bold rounded-[8px]" title="حذف">✕</button>
                            </div>
                        </div>

                        <!-- خانة الإهداء -->
                        <div class="mt-4 pt-4 border-t border-cloud">
                            <label class="flex flex-wrap items-center gap-3 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    :checked="ensureGiftForm(item).enabled"
                                    @change="ensureGiftForm(item).enabled = $event.target.checked; saveGift(item.product.id)"
                                    class="w-4 h-4 accent-obsidian"
                                >
                                <span class="text-sm font-bold text-obsidian">🎁 إهداء هذا المنتج</span>
                                <span class="text-[11px] text-fog">تبعية الإهداء على حسب الشخص المستلم</span>
                            </label>

                            <div v-if="ensureGiftForm(item).enabled" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-graphite mb-1.5">اسم المستلم *</label>
                                    <input
                                        v-model="ensureGiftForm(item).recipient_name"
                                        type="text"
                                        class="input-awesomic text-xs sm:text-sm py-2.5"
                                        placeholder="اسم من ستُهدى له"
                                        @change="saveGift(item.product.id)"
                                    >
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-graphite mb-1.5">رقم جوال المستلم (اختياري)</label>
                                    <input
                                        v-model="ensureGiftForm(item).recipient_phone"
                                        type="tel"
                                        class="input-awesomic text-xs sm:text-sm py-2.5"
                                        placeholder="+966 50 000 0000"
                                        @change="saveGift(item.product.id)"
                                    >
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-graphite mb-1.5">رسالة الإهداء</label>
                                    <textarea
                                        v-model="ensureGiftForm(item).message"
                                        rows="2"
                                        class="input-awesomic text-xs sm:text-sm py-2.5"
                                        placeholder="اكتب رسالة تظهر في بطاقة الهدية"
                                        @change="saveGift(item.product.id)"
                                    ></textarea>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="flex flex-wrap items-center gap-3 cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            :checked="ensureGiftForm(item).hide_price"
                                            @change="ensureGiftForm(item).hide_price = $event.target.checked; saveGift(item.product.id)"
                                            class="w-4 h-4 accent-obsidian"
                                        >
                                        <span class="text-xs font-bold text-graphite">إخفاء السعر من الفاتورة</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-awesomic p-6 space-y-4">
                    <h3 class="text-base font-bold text-obsidian pb-3 border-b border-cloud">ملخص الحساب</h3>

                    <div class="flex justify-between text-xs sm:text-sm text-steel">
                        <span>المجموع الفرعي</span>
                        <span class="font-bold text-obsidian">{{ subtotal }} <span class="currency-sar">ر.س</span></span>
                    </div>

                    <div class="flex justify-between text-xs sm:text-sm text-steel">
                        <span>الشحن والتوصيل</span>
                        <span class="font-bold text-obsidian">{{ shipping === 0 ? "مجاني 🎉" : `${shipping} ` }}<span v-if="shipping !== 0" class="currency-sar">ر.س</span></span>
                    </div>

                    <div v-if="subtotal < storeSettings.free_shipping_min" class="bg-[#fafafa] text-graphite border border-cloud p-3 rounded-btn text-xs leading-relaxed">
                        💡 أضف منتجات بقيمة <strong>{{ storeSettings.free_shipping_min - subtotal }} <span class="currency-sar">ر.س</span></strong> للحصول على شحن مجاني!
                    </div>

                    <div class="pt-4 border-t border-cloud flex justify-between items-baseline">
                        <span class="text-sm font-bold text-obsidian">المجموع الكلي</span>
                        <span class="text-2xl font-extrabold text-obsidian">{{ total }} <span class="currency-sar text-sm">ر.س</span></span>
                    </div>

                    <Link href="/checkout" class="btn-primary w-full py-3.5 text-center text-xs sm:text-sm font-medium rounded-btn mt-4">
                        متابعة إتمام الطلب &larr;
                    </Link>
                </div>
            </div>

        </div>
    </div>
</template>