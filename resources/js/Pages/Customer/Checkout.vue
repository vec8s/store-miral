<script setup>
import { computed, reactive, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const props = defineProps({
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
    codFee: {
        type: Number,
        default: 10,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const paymentMethod = ref("mada");

const codFeeVisible = computed(() => paymentMethod.value === "cod");

const grandTotal = computed(() => props.total + (codFeeVisible.value ? props.codFee : 0));

const shippingForm = reactive({
    name: "",
    phone: "",
    city: "الرياض",
    address: "",
});

if (user.value) {
    shippingForm.name = user.value.name ?? "";
    shippingForm.phone = user.value.phone ?? "";
}

const placeOrder = () => {
    router.post("/checkout", {
        ...shippingForm,
        payment_method: paymentMethod.value,
    });
};
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-2">إتمام الشراء</h1>
            <p class="text-fog text-xs sm:text-sm mb-8">أدخل تفاصيل الشحن لإجراء التحقق المباشر المزامَن مع منصة سلة</p>

            <form @submit.prevent="placeOrder" class="grid lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 items-start">

                <div class="lg:col-span-2 space-y-6">
                    <div class="card-awesomic p-6">
                        <h3 class="text-base font-bold text-obsidian mb-4 pb-2 border-b border-cloud flex items-center gap-2">
                            <span>📍</span> بيانات الشحن والعنوان
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-graphite mb-1.5">الاسم الكامل *</label>
                                <input v-model="shippingForm.name" type="text" name="name" required class="input-awesomic text-xs sm:text-sm py-2.5">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-graphite mb-1.5">رقم الجوال *</label>
                                <input v-model="shippingForm.phone" type="tel" name="phone" required class="input-awesomic text-xs sm:text-sm py-2.5" placeholder="+966 50 000 0000">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-graphite mb-1.5">المدينة *</label>
                                <select v-model="shippingForm.city" name="city" class="input-awesomic text-xs sm:text-sm py-2.5">
                                    <option value="الرياض">الرياض</option>
                                    <option value="جدة">جدة</option>
                                    <option value="الدمام">الدمام</option>
                                    <option value="مكة المكرمة">مكة المكرمة</option>
                                    <option value="المدينة المنورة">المدينة المنورة</option>
                                    <option value="الخبر">الخبر</option>
                                    <option value="أبها">أبها</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-graphite mb-1.5">العنوان بالتفصيل *</label>
                                <input v-model="shippingForm.address" type="text" name="address" required class="input-awesomic text-xs sm:text-sm py-2.5" placeholder="اسم الحي، الشارع، رقم البناء">
                            </div>
                        </div>
                    </div>

                    <div class="card-awesomic p-6">
                        <h3 class="text-base font-bold text-obsidian mb-4 pb-2 border-b border-cloud flex items-center gap-2">
                            <span>💳</span> وسيلة الدفع المعتمدة لدى سلة
                        </h3>

                        <div class="space-y-3">
                            <label
                                class="flex flex-wrap items-center justify-between gap-2 p-4 rounded-[16px] border cursor-pointer transition"
                                :class="paymentMethod === 'mada' ? 'border-obsidian bg-[#fafafa]' : 'border-cloud bg-white hover:border-ash'"
                            >
                                <div class="flex items-center gap-3">
                                    <input v-model="paymentMethod" type="radio" name="payment_method" value="mada" class="accent-obsidian">
                                    <span class="font-bold text-xs sm:text-sm text-obsidian">بطاقة مدى / Apple Pay</span>
                                </div>
                                <span class="badge-tag">Salla Live Gateway</span>
                            </label>

                            <label
                                class="flex flex-wrap items-center justify-between gap-2 p-4 rounded-[16px] border cursor-pointer transition"
                                :class="paymentMethod === 'cc' ? 'border-obsidian bg-[#fafafa]' : 'border-cloud bg-white hover:border-ash'"
                            >
                                <div class="flex items-center gap-3">
                                    <input v-model="paymentMethod" type="radio" name="payment_method" value="cc" class="accent-obsidian">
                                    <span class="font-bold text-xs sm:text-sm text-obsidian">بطاقة ائتمانية (VISA / MasterCard)</span>
                                </div>
                                <span class="badge-tag">Salla Live Gateway</span>
                            </label>

                            <label
                                class="flex flex-wrap items-center justify-between gap-2 p-4 rounded-[16px] border cursor-pointer transition"
                                :class="paymentMethod === 'cod' ? 'border-obsidian bg-[#fafafa]' : 'border-cloud bg-white hover:border-ash'"
                            >
                                <div class="flex items-center gap-3">
                                    <input v-model="paymentMethod" type="radio" name="payment_method" value="cod" class="accent-obsidian">
                                    <span class="font-bold text-xs sm:text-sm text-obsidian">الدفع عند الاستلام</span>
                                </div>
                                <span class="badge-ember">رسوم إضافية 10 <span class="currency-sar text-white">ر.س</span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-awesomic p-6 space-y-4">
                    <h3 class="text-base font-bold text-obsidian pb-3 border-b border-cloud">ملخص الاصناف</h3>

                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1 scrollbar-none">
                        <div v-for="item in cart" :key="item.product.id" class="flex items-center gap-3 text-xs">
                            <img :src="item.product?.thumbnail_url" alt="" class="w-12 h-12 rounded-badge object-cover bg-paper border border-cloud shrink-0">
                            <div class="flex-grow">
                                <p class="font-bold text-obsidian line-clamp-1">{{ item.product?.name }}</p>
                                <p class="text-fog">الكمية: {{ item.quantity }}</p>
                            </div>
                            <span class="font-bold text-obsidian shrink-0">
                                {{ (item.product?.sale_price || item.product?.price) * item.quantity }} <span class="currency-sar">ر.س</span>
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-cloud space-y-2 text-xs text-steel">
                        <div class="flex justify-between">
                            <span>المجموع الفرعي</span>
                            <span class="font-bold text-obsidian">{{ subtotal }} <span class="currency-sar">ر.س</span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>تكلفة الشحن</span>
                            <span class="font-bold text-obsidian">{{ shipping === 0 ? "مجاني" : `${shipping} ` }}<span v-if="shipping !== 0" class="currency-sar">ر.س</span></span>
                        </div>
                        <div v-if="codFeeVisible" class="flex justify-between">
                            <span>رسوم الدفع عند الاستلام</span>
                            <span class="font-bold text-obsidian">{{ codFee }} <span class="currency-sar">ر.س</span></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-cloud flex justify-between items-baseline">
                        <span class="text-sm font-bold text-obsidian">المبلغ الإجمالي</span>
                        <span class="text-2xl font-extrabold text-obsidian">{{ grandTotal }} <span class="currency-sar text-sm">ر.س</span></span>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-center text-xs sm:text-sm font-medium rounded-btn mt-4">
                        إرسال الطلب وإتمامه مباشرة &larr;
                    </button>
                </div>

            </form>

        </div>
    </div>
</template>