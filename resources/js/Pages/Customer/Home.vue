<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import ProductCard from "../../Components/ProductCard.vue";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    featured: {
        type: Array,
        default: () => [],
    },
    bestSellers: {
        type: Array,
        default: () => [],
    },
    topRated: {
        type: Array,
        default: () => [],
    },
    mostSearched: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    banners: {
        type: Array,
        default: () => [],
    },
    posts: {
        type: Array,
        default: () => [],
    },
    coupons: {
        type: Array,
        default: () => [],
    },
    storeSettings: {
        type: Object,
        default: () => ({}),
    },
});

const activeBanner = ref(0);
const sortOrder = ref("desc");
let sliderTimer = null;

const sortedProducts = computed(() => {
    const list = [...props.products];
    return list.sort((a, b) =>
        sortOrder.value === "asc"
            ? (a.sale_price || a.price) - (b.sale_price || b.price)
            : (b.sale_price || b.price) - (a.sale_price || a.price),
    );
});

const recentlyViewed = ref([]);

const startSlider = () => {
    sliderTimer = setInterval(() => {
        activeBanner.value = (activeBanner.value + 1) % props.banners.length;
    }, 6000);
};

const goToBanner = (index) => {
    activeBanner.value = index;
    clearInterval(sliderTimer);
    startSlider();
};

const loadRecentlyViewed = () => {
    try {
        const raw = localStorage.getItem("miral_recently_viewed");
        if (!raw) return;
        const ids = JSON.parse(raw);
        const byId = new Map(props.products.map((p) => [p.id, p]));
        recentlyViewed.value = ids
            .map((id) => byId.get(id))
            .filter(Boolean)
            .slice(0, 8);
    } catch (e) {
        recentlyViewed.value = [];
    }
};

onMounted(() => {
    if (props.banners.length > 1) startSlider();
    loadRecentlyViewed();
});

onBeforeUnmount(() => {
    if (sliderTimer) clearInterval(sliderTimer);
});
</script>

<template>
    <div>

        <!-- 1. سلايدر البانر الرئيسي -->
        <section class="py-12 sm:py-20 bg-paper border-b border-cloud">
            <div class="container-rtl">
                <div class="card-awesomic bg-graphite text-white p-6 sm:p-14 relative overflow-hidden border border-slate">
                    <div class="absolute -top-12 -left-12 w-64 h-64 rounded-full bg-ember/10 blur-3xl"></div>

                    <div class="relative z-10 grid lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                        <div class="lg:col-span-8 text-right">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-badge bg-obsidian text-white text-xs font-medium mb-6 border border-[#2c2e34]">
                                <span class="w-2 h-2 rounded-full bg-ember"></span>
                                {{ banners[activeBanner]?.badge }}
                            </div>

                            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-[1.1] mb-6">
                                {{ banners[activeBanner]?.title }}
                            </h1>

                            <p class="text-sm sm:text-base text-ash leading-relaxed max-w-2xl mb-8">
                                {{ banners[activeBanner]?.subtitle }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3">
                                <a :href="banners[activeBanner]?.url || '/shop'" class="btn-primary text-sm px-7 py-3.5">
                                    {{ banners[activeBanner]?.label }} &larr;
                                </a>
                                <Link href="/about" class="btn-ghost text-sm px-6 py-3.5 border-slate text-white">
                                    عن ميرال
                                </Link>
                            </div>
                        </div>

                        <div class="lg:col-span-4 hidden lg:flex items-center justify-center">
                            <div class="text-[9rem] leading-none">{{ banners[activeBanner]?.image }}</div>
                        </div>
                    </div>

                    <div v-if="banners.length > 1" class="relative z-10 mt-10 flex items-center gap-2">
                        <button
                            v-for="(banner, index) in banners"
                            :key="banner.id"
                            @click="goToBanner(index)"
                            class="h-2 rounded-full transition-all"
                            :class="index === activeBanner ? 'w-8 bg-ember' : 'w-2 bg-slate hover:bg-ash'"
                            :aria-label="`الشريحة ${index + 1}`"
                        ></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- أقسام المتجر -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">أقسام المتجر</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">تصفّح المنتجات حسب الفئة المطلوبة</p>
                    </div>
                    <Link href="/categories" class="btn-neutral text-xs px-4 py-2 min-h-[44px]">عرض جميع الأقسام &rarr;</Link>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="`/shop?category=${encodeURIComponent(cat.name)}`"
                        class="card-awesomic p-6 hover:border-obsidian transition-all text-center group flex flex-col items-center justify-center"
                    >
                        <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">{{ cat.icon }}</div>
                        <span class="font-bold text-obsidian text-sm">{{ cat.name }}</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- 2. مقترحاتنا -->
        <section class="py-10 sm:py-16 bg-white border-y border-cloud">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">مقترحاتنا</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">اختيارات مختارة بعناية من فريق ميرال</p>
                    </div>
                    <Link href="/shop" class="btn-neutral text-xs px-4 py-2 min-h-[44px]">تسوق المتجر &larr;</Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in featured" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 3. الأكثر مبيعاً -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <span class="badge-ember mb-3 inline-block">الرائج</span>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">الأكثر مبيعاً</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">المنتجات الأكثر طلباً لدى عملاء ميرال</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in bestSellers" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 4. الأعلى تقييماً -->
        <section class="py-10 sm:py-16 bg-white border-y border-cloud">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <span class="badge-tag mb-3 inline-block">مميزة</span>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">الأعلى تقييماً</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">قطع نالت إعجاب عملائنا بأعلى التقييمات</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in topRated" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 5. الأكثر بحثاً -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <span class="badge-ember mb-3 inline-block">الأكثر بحثاً</span>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">الأكثر بحثاً</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">الأكثر استفساراً وطلبات بحثاً من زوار المتجر</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in mostSearched" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 6. ترتيب بالسعر -->
        <section class="py-10 sm:py-16 bg-white border-y border-cloud">
            <div class="container-rtl">
                <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">جميع المنتجات</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">ترتيب المنتجات حسب السعر حسب رغبتك</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            @click="sortOrder = 'desc'"
                            class="text-xs px-4 py-2.5 rounded-btn font-medium transition min-h-[44px]"
                            :class="sortOrder === 'desc' ? 'bg-obsidian text-white' : 'bg-[#fafafa] border border-cloud text-graphite hover:bg-paper'"
                        >السعر: الأعلى للأدنى</button>
                        <button
                            @click="sortOrder = 'asc'"
                            class="text-xs px-4 py-2.5 rounded-btn font-medium transition min-h-[44px]"
                            :class="sortOrder === 'asc' ? 'bg-obsidian text-white' : 'bg-[#fafafa] border border-cloud text-graphite hover:bg-paper'"
                        >السعر: الأدنى للأعلى</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in sortedProducts" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 7. آراء العملاء -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">آراء عملاء ميرال</h2>
                    <p class="text-xs sm:text-sm text-fog mt-1.5">تقييمات وتجارب حقيقية لعملائنا بعد التوصيل والطلب</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card-awesomic p-6">
                        <div class="flex items-center gap-1 mb-3 text-ember text-sm">★★★★★</div>
                        <p class="text-iron text-xs sm:text-sm leading-relaxed mb-6">"رائع جداً، الجودة فوق الممتاز والتوصيل كان سريع جداً والتغليف فخم ينفع هدية مباشرة."</p>
                        <span class="font-bold text-obsidian text-xs block">— محمد الردادي</span>
                    </div>

                    <div class="card-awesomic p-6">
                        <div class="flex items-center gap-1 mb-3 text-ember text-sm">★★★★★</div>
                        <p class="text-iron text-xs sm:text-sm leading-relaxed mb-6">"يجننن ويستحق التجربة ☺️ شكراً من القلب على الفستان والهدية الرائعة والتفاصيل الجذابة."</p>
                        <span class="font-bold text-obsidian text-xs block">— مرام البارقي</span>
                    </div>

                    <div class="card-awesomic p-6">
                        <div class="flex items-center gap-1 mb-3 text-ember text-sm">★★★★★</div>
                        <p class="text-iron text-xs sm:text-sm leading-relaxed mb-6">"جداً أنيقة وجميلة، والسلسلة طلعت بالواقع أحلى بكثير من الصور. شكراً ميرال 😍❤️."</p>
                        <span class="font-bold text-obsidian text-xs block">— حسن العتيبي</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. شاهدته مؤخراً -->
        <section v-if="recentlyViewed.length > 0" class="py-10 sm:py-16 bg-white border-y border-cloud">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">شاهدته مؤخراً</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">منتجات قمت بزيارتها في زياراتك السابقة</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in recentlyViewed" :key="product.id" :product="product" />
                </div>
            </div>
        </section>

        <!-- 9. منشورات يتم استعراضها باستمرار -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="flex flex-wrap items-end justify-between gap-2 mb-10">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">منشورات يتم استعراضها باستمرار</h2>
                        <p class="text-xs sm:text-sm text-fog mt-1">مقالات وأدلة يقرؤها زوار المتجر باستمرار</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a v-for="post in posts" :key="post.id" :href="post.url" class="card-awesomic p-6 group hover:border-obsidian transition-all block">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="badge-tag text-[11px]">{{ post.tag }}</span>
                            <span class="text-[11px] text-fog">{{ post.read_time }} قراءة</span>
                        </div>
                        <h3 class="font-bold text-obsidian text-sm sm:text-base mb-2 group-hover:underline leading-snug">{{ post.title }}</h3>
                        <p class="text-xs text-steel leading-relaxed mb-5">{{ post.excerpt }}</p>
                        <span class="text-[11px] text-fog">{{ post.created_at }}</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 10. أكواد الخصم -->
        <section class="py-10 sm:py-16 bg-white border-y border-cloud">
            <div class="container-rtl">
                <div class="text-center max-w-xl mx-auto mb-10">
                    <span class="badge-ember mb-3 inline-block">عروض حصرية</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">أكواد الخصم</h2>
                    <p class="text-xs sm:text-sm text-fog mt-1.5">فعّل كود الخصم عند إتمام الطلب للحصول على تخفيض فوري</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="coupon in coupons" :key="coupon.id" class="card-awesomic p-6 border-dashed">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                            <span class="font-extrabold text-obsidian text-sm sm:text-base tracking-wide bg-[#fafafa] border border-cloud rounded-badge px-4 py-2">
                                {{ coupon.code }}
                            </span>
                            <span class="text-[11px] text-fog">{{ coupon.label }}</span>
                        </div>
                        <p class="text-xs text-steel leading-relaxed flex items-center gap-1.5">
                            <span>🎯</span> {{ coupon.condition }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- مميزات المتجر -->
        <section class="py-10 sm:py-16 bg-paper">
            <div class="container-rtl">
                <div class="card-awesomic bg-graphite text-white p-6 sm:p-12 border border-slate">
                    <div class="max-w-xl mb-10">
                        <span class="badge-ember mb-4">مميزات المتجر</span>
                        <h2 class="text-3xl font-bold tracking-tight text-white mb-3">تطبيق معايير السلاسة والجودة العالية</h2>
                        <p class="text-sm text-ash leading-relaxed">نضمن لك تجربة شراء آمنة وسريعة مع معالجة حية للطلبات عبر بوابة سلة.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="p-6 rounded-[24px] bg-slate border border-iron">
                            <div class="text-3xl mb-3">🚚</div>
                            <h4 class="font-bold text-white text-base mb-1">توصيل سريع</h4>
                            <p class="text-xs text-ash leading-relaxed">تغطية شاملة لكافة مدن ومناطق المملكة عبر شحن سلة الفوري.</p>
                        </div>

                        <div class="p-6 rounded-[24px] bg-slate border border-iron">
                            <div class="text-3xl mb-3">💎</div>
                            <h4 class="font-bold text-white text-base mb-1">جودة مضمونة</h4>
                            <p class="text-xs text-ash leading-relaxed">قطع حلي أصلية مصنعة طبقاً لأعلى المواصفات والمعايير.</p>
                        </div>

                        <div class="p-6 rounded-[24px] bg-slate border border-iron">
                            <div class="text-3xl mb-3">🎁</div>
                            <h4 class="font-bold text-white text-base mb-1">تغليف فاخر</h4>
                            <p class="text-xs text-ash leading-relaxed">علب إهداء أنيقة وجاهزة للمناسبات الخاصة والذكريات.</p>
                        </div>

                        <div class="p-6 rounded-[24px] bg-slate border border-iron">
                            <div class="text-3xl mb-3">🛡️</div>
                            <h4 class="font-bold text-white text-base mb-1">دفع إلكتروني آمن</h4>
                            <p class="text-xs text-ash leading-relaxed">تكامل مباشر وحماية مشفرة مع بوابة الدفع الرسمية.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>