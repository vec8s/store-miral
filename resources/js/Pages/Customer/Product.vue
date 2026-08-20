<script setup>
import { computed, inject, onMounted, reactive, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        default: () => [],
    },
    boughtTogether: {
        type: Array,
        default: () => [],
    },
    storeInfo: {
        type: Object,
        default: () => ({}),
    },
});

const triggerToast = inject("triggerToast", () => {});
const qty = ref(1);
const activeImage = ref(0);
const selectedColor = ref(props.product.colors?.[0]?.name ?? "");

const gift = reactive({
    enabled: false,
    recipient_name: "",
    recipient_phone: "",
    message: "",
    hide_price: false,
});

const discountPercent = computed(() =>
    props.product.sale_price
        ? Math.round((1 - props.product.sale_price / props.product.price) * 100)
        : 0,
);

const galleryImages = computed(() => {
    const images = props.product.images?.length ? props.product.images : [props.product.thumbnail_url];
    return images.filter(Boolean);
});

const priceIncludesTax = computed(() => "شامل الضريبة");

const addToCart = async () => {
    if (gift.enabled && !gift.recipient_name.trim()) {
        triggerToast("يرجى إدخال اسم المستلم لتفعيل الإهداء");
        return;
    }

    try {
        const { data } = await window.axios.post("/api/cart/add", {
            product_id: props.product.id,
            quantity: qty.value,
            color: selectedColor.value,
            gift_enabled: gift.enabled,
            gift_recipient_name: gift.recipient_name,
            gift_recipient_phone: gift.recipient_phone,
            gift_message: gift.message,
            gift_hide_price: gift.hide_price,
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

const shareProduct = async () => {
    const url = window.location.href;
    try {
        await navigator.clipboard.writeText(url);
        triggerToast("تم نسخ رابط المنتج");
    } catch (e) {
        window.prompt("انسخ رابط المنتج:", url);
    }
};

const recordView = () => {
    try {
        const key = "miral_recently_viewed";
        const current = JSON.parse(localStorage.getItem(key) || "[]");
        const updated = [props.product.id, ...current.filter((id) => id !== props.product.id)].slice(0, 12);
        localStorage.setItem(key, JSON.stringify(updated));
    } catch (e) {
        /* localStorage غير متاح */
    }
};

onMounted(recordView);
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl">

            <div class="flex flex-wrap items-center gap-2 text-xs text-fog mb-8">
                <Link href="/" class="hover:text-obsidian">الرئيسية</Link>
                <span>/</span>
                <Link href="/shop" class="hover:text-obsidian">المتجر</Link>
                <span>/</span>
                <span class="text-obsidian font-medium">{{ product.name }}</span>
            </div>

            <div class="card-awesomic p-6 md:p-10 grid md:grid-cols-2 gap-6 md:gap-10 items-start mb-16">
                <div>
                    <div class="relative aspect-square rounded-[28px] overflow-hidden bg-paper border border-cloud mb-4">
                        <img :src="galleryImages[activeImage] || product.thumbnail_url" :alt="product.name" class="w-full h-full object-cover">
                        <span v-if="product.sale_price" class="absolute top-4 right-4 badge-ember shadow-md">
                            خصم {{ discountPercent }}%
                        </span>
                    </div>

                    <div v-if="galleryImages.length > 1" class="grid grid-cols-4 gap-3">
                        <button
                            v-for="(image, index) in galleryImages"
                            :key="index"
                            @click="activeImage = index"
                            class="aspect-square rounded-[18px] overflow-hidden border transition"
                            :class="activeImage === index ? 'border-obsidian ring-2 ring-obsidian/20' : 'border-cloud hover:border-ash'"
                        >
                            <img :src="image" :alt="`${product.name} ${index + 1}`" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </button>
                    </div>
                </div>

                <div>
                    <span class="badge-filled mb-3">{{ product.category?.name }}</span>
                    <span class="badge-tag mb-3 mr-2">موديل: {{ product.model || product.sku || "—" }}</span>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight mb-3">{{ product.name }}</h1>

                    <div class="flex items-center gap-2 text-xs sm:text-sm text-ember mb-6">
                        <div class="flex">★★★★★</div>
                        <span class="font-bold text-obsidian">{{ product.reviews_avg_rating }}</span>
                        <span class="text-fog font-normal">({{ product.reviews_count }} تقييم من العملاء)</span>
                    </div>

                    <div class="flex flex-wrap items-baseline gap-3 mb-2 pb-6 border-b border-cloud">
                        <span class="text-3xl font-extrabold text-obsidian">
                            {{ product.sale_price || product.price }} <span class="currency-sar text-sm">ر.س</span>
                        </span>
                        <span v-if="product.sale_price" class="text-base text-ash line-through">
                            {{ product.price }} <span class="currency-sar">ر.س</span>
                        </span>
                        <span class="mr-auto badge-tag bg-[#fafafa]">{{ priceIncludesTax }} ✓</span>
                    </div>

                    <div v-if="props.product.colors?.length" class="mb-6">
                        <p class="text-xs font-bold text-graphite mb-2.5">اللون: <span class="text-fog font-medium">{{ selectedColor }}</span></p>
                        <div class="flex items-center gap-2.5">
                            <button
                                v-for="color in props.product.colors"
                                :key="color.name"
                                @click="selectedColor = color.name"
                                class="w-10 h-10 rounded-full border-2 transition flex items-center justify-center"
                                :class="selectedColor === color.name ? 'border-obsidian' : 'border-cloud hover:border-ash'"
                                :style="{ backgroundColor: color.hex }"
                                :title="color.name"
                            ></button>
                        </div>
                    </div>

                    <div class="text-xs sm:text-sm text-steel leading-relaxed mb-8">{{ product.description }}</div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-8">
                        <div class="flex items-center justify-between border border-cloud rounded-btn px-3 py-2 bg-[#fafafa] w-36">
                            <button
                                @click="qty > 1 && qty--"
                                class="w-10 h-10 rounded-[10px] bg-white border border-cloud flex items-center justify-center font-bold text-obsidian hover:bg-paper"
                            >-</button>
                            <span class="font-bold text-obsidian text-sm">{{ qty }}</span>
                            <button
                                @click="qty++"
                                class="w-10 h-10 rounded-[10px] bg-white border border-cloud flex items-center justify-center font-bold text-obsidian hover:bg-paper"
                            >+</button>
                        </div>

                        <button @click="addToCart" class="btn-primary flex-1 py-3.5 text-sm">إضافة إلى السلة</button>

                        <button @click="toggleWishlist" class="btn-ghost p-3.5 rounded-btn" title="المفضلة">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>

                        <button @click="shareProduct" class="btn-ghost p-3.5 rounded-btn" title="مشاركة المنتج">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- خانة الإهداء -->
                    <div class="mb-6 border border-cloud rounded-[20px] p-4 bg-[#fafafa]">
                        <label class="flex flex-wrap items-center gap-3 cursor-pointer select-none">
                            <input v-model="gift.enabled" type="checkbox" class="w-4 h-4 accent-obsidian">
                            <span class="text-sm font-bold text-obsidian">🎁 إهداء هذا المنتج</span>
                            <span class="text-[11px] text-fog">تبعية الإهداء على حسب الشخص المستلم</span>
                        </label>

                        <div v-if="gift.enabled" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-graphite mb-1.5">اسم المستلم *</label>
                                <input v-model="gift.recipient_name" type="text" class="input-awesomic text-xs sm:text-sm py-2.5" placeholder="اسم من ستُهدى له">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-graphite mb-1.5">رقم جوال المستلم (اختياري)</label>
                                <input v-model="gift.recipient_phone" type="tel" class="input-awesomic text-xs sm:text-sm py-2.5" placeholder="+966 50 000 0000">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-graphite mb-1.5">رسالة الإهداء</label>
                                <textarea v-model="gift.message" rows="2" class="input-awesomic text-xs sm:text-sm py-2.5" placeholder="اكتب رسالة تظهر في بطاقة الهدية"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex flex-wrap items-center gap-3 cursor-pointer select-none">
                                    <input v-model="gift.hide_price" type="checkbox" class="w-4 h-4 accent-obsidian">
                                    <span class="text-xs font-bold text-graphite">إخفاء السعر من الفاتورة</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-6 border-t border-cloud text-xs text-steel">
                        <div class="flex items-center gap-2">
                            <span>🚚</span>
                            <span>توصيل سريع لجميع المناطق</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>🎁</span>
                            <span>تغليف هدايا مجاني فاخر</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>💳</span>
                            <span>دفع آمن بالبطاقة أو ابل باي</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>🔄</span>
                            <span>استبدال واسترجاع ميسر</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- عادة ما يتم شراؤها معاً -->
            <div v-if="boughtTogether.length > 0" class="mb-16">
                <h3 class="text-xl font-bold text-obsidian mb-6">عادة ما يتم شراؤها معاً</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <Link
                        v-for="rel in boughtTogether"
                        :key="rel.id"
                        :href="`/shop/${rel.id}`"
                        class="card-awesomic p-4 group flex flex-col justify-between hover:border-obsidian transition-all"
                    >
                        <div class="relative aspect-square overflow-hidden rounded-[20px] bg-paper mb-3 block">
                            <img :src="rel.thumbnail_url" :alt="rel.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div>
                            <span class="font-bold text-obsidian hover:underline line-clamp-1 text-xs sm:text-sm mb-1 block">
                                {{ rel.name }}
                            </span>
                            <p class="text-sm font-extrabold text-obsidian">{{ rel.sale_price || rel.price }} <span class="currency-sar">ر.س</span></p>
                        </div>
                    </Link>
                </div>
            </div>

            <div v-if="relatedProducts.length > 0">
                <h3 class="text-xl font-bold text-obsidian mb-6">منتجات قد تعجبك أيضاً</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <Link
                        v-for="rel in relatedProducts"
                        :key="rel.id"
                        :href="`/shop/${rel.id}`"
                        class="card-awesomic p-4 group flex flex-col justify-between hover:border-obsidian transition-all"
                    >
                        <div class="relative aspect-square overflow-hidden rounded-[20px] bg-paper mb-3 block">
                            <img :src="rel.thumbnail_url" :alt="rel.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div>
                            <span class="font-bold text-obsidian hover:underline line-clamp-1 text-xs sm:text-sm mb-1 block">
                                {{ rel.name }}
                            </span>
                            <p class="text-sm font-extrabold text-obsidian">{{ rel.sale_price || rel.price }} <span class="currency-sar">ر.س</span></p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- وصف مختصر للمتجر + السجل التجاري + الرقم الضريبي -->
            <div class="card-awesomic p-6 md:p-8 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <div>
                        <span class="badge-filled mb-2 inline-block">عن المتجر</span>
                        <p class="text-xs text-steel leading-relaxed">{{ storeInfo.description }}</p>
                    </div>
                    <div class="space-y-2 text-xs text-steel">
                        <p class="flex items-center gap-2"><span>🏢</span> السجل التجاري: <strong class="text-obsidian" dir="ltr">{{ storeInfo.commercial_registration }}</strong></p>
                        <p class="flex items-center gap-2"><span>🧾</span> الرقم الضريبي: <strong class="text-obsidian" dir="ltr">{{ storeInfo.tax_number }}</strong></p>
                    </div>
                    <div class="space-y-2 text-xs text-steel">
                        <p class="flex items-center gap-2"><span>📧</span> {{ storeInfo.email }}</p>
                        <p class="flex items-center gap-2"><span>📞</span> {{ storeInfo.phone }}</p>
                        <p class="flex items-center gap-2"><span>📍</span> {{ storeInfo.address }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>