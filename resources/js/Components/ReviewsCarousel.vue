<script setup>
import { computed, ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination, Navigation } from "swiper/modules";

// Swiper core styles
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";

const props = defineProps({
  reviews: {
    type: Array,
    default: () => [],
  },
  autoplayDelay: {
    type: Number,
    default: 2800,
  },
  showNavigation: {
    type: Boolean,
    default: true,
  },
  showPagination: {
    type: Boolean,
    default: true,
  },
  loop: {
    type: Boolean,
    default: true,
  },
});

const modules = [Autoplay, Pagination, Navigation];
const activeIndex = ref(0);

const defaultReviews = [
  {
    id: 1,
    name: "محمد الردادي",
    city: "المدينة المنورة",
    rating: 5,
    comment: "رائع جداً، الجودة فوق الممتاز والتوصيل كان سريع جداً والتغليف فخم ينفع هدية مباشرة.",
    product_name: "سلسلة ذهبية عيار 18",
    image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ يومين",
  },
  {
    id: 2,
    name: "مرام البارقي",
    city: "جدة",
    rating: 5,
    comment: "يجننن ويستحق التجربة ☺️ شكراً من القلب على الإهداء والهدية الرائعة والتفاصيل الجذابة.",
    product_name: "ساعة فاخرة كلاسيك",
    image: "https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ 4 أيام",
  },
  {
    id: 3,
    name: "حسن العتيبي",
    city: "الرياض",
    rating: 5,
    comment: "جداً أنيقة وجميلة، والسلسلة طلعت بالواقع أحلى بكثير من الصور. شكراً متجر ميرال 😍❤️.",
    product_name: "سوار اللؤلؤ الطبيعي",
    image: "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ أسبوع",
  },
  {
    id: 4,
    name: "سارة الشمري",
    city: "الدمام",
    rating: 5,
    comment: "وصلتني الشحنة بوقت قياسي، والتغليف راقي جداً والاهتمام بالتفاصيل يفتح النفس!",
    product_name: "بوكس إهداء ملكي فاخر",
    image: "https://images.unsplash.com/photo-1549465220-1a8b9238cd48?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ أسبوع",
  },
  {
    id: 5,
    name: "عبدالله الدوسري",
    city: "الخبر",
    rating: 5,
    comment: "المسبحة العقيق تفوق الوصف، ملمس ونقاء حجر طبيعي أصلي وخدمة عملاء ممتازة.",
    product_name: "سبحة عقيق يماني أصلي",
    image: "https://images.unsplash.com/photo-1611591475152-4783113f9d52?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ أسبوعين",
  },
  {
    id: 6,
    name: "نورة القحطاني",
    city: "أبها",
    rating: 5,
    comment: "ثاني مرة أطلب منكم وكل مرة تبهروني بالجودة والسرعة، متجري المفضل للهدايا.",
    product_name: "ميدالية فضة مطعمة",
    image: "https://images.unsplash.com/photo-1602751584552-8ba73aad10e1?auto=format&fit=crop&w=600&q=80",
    verified: true,
    date: "منذ أسبوعين",
  },
];

const displayReviews = computed(() => {
  if (props.reviews && props.reviews.length > 0) {
    return props.reviews;
  }
  return defaultReviews;
});

const onSlideChange = (swiper) => {
  activeIndex.value = swiper.realIndex;
};
</script>

<template>
  <div class="reviews-swiper-carousel relative w-full overflow-hidden py-4 select-none">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6">
      <Swiper
        :modules="modules"
        :grabCursor="true"
        :centeredSlides="true"
        :loop="loop && displayReviews.length >= 3"
        :autoplay="{
          delay: autoplayDelay,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        }"
        :spaceBetween="20"
        :breakpoints="{
          320: {
            slidesPerView: 1.15,
            spaceBetween: 16,
          },
          640: {
            slidesPerView: 2,
            spaceBetween: 20,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 24,
          },
          1280: {
            slidesPerView: 3.5,
            spaceBetween: 28,
          }
        }"
        :pagination="showPagination ? { clickable: true, dynamicBullets: true } : false"
        :navigation="showNavigation ? { nextEl: '.review-swiper-next', prevEl: '.review-swiper-prev' } : false"
        @slideChange="onSlideChange"
        class="reviews-carousel-swiper pb-16"
      >
        <SwiperSlide
          v-for="(review, index) in displayReviews"
          :key="review.id || index"
          v-slot="{ isActive }"
          class="review-slide-item"
        >
          <div
            class="h-full rounded-3xl bg-white border border-[#ececee] p-6 sm:p-7 flex flex-col justify-between transition-all duration-500 shadow-sm relative overflow-hidden group"
            :class="[
              isActive 
                ? 'scale-100 shadow-xl border-[#09090b] ring-1 ring-[#09090b]/10 bg-white' 
                : 'scale-[0.93] opacity-70 hover:opacity-100 bg-[#fafafa]'
            ]"
          >
            <!-- Background luxury decoration glow for active card -->
            <div 
              v-if="isActive" 
              class="absolute -top-10 -left-10 w-32 h-32 rounded-full bg-[#ff5a00]/10 blur-2xl pointer-events-none transition-opacity duration-500"
            ></div>

            <div>
              <!-- Top Row: Rating & Verified Badge -->
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-1 text-[#ff5a00] text-sm">
                  <span v-for="star in 5" :key="star">★</span>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                  <span>✓</span> مشترٍ موثق
                </span>
              </div>

              <!-- Review Comment -->
              <p class="text-[#3f3f46] text-xs sm:text-sm leading-relaxed mb-6 font-medium line-clamp-4 group-hover:text-[#09090b] transition-colors">
                "{{ review.comment || review.text }}"
              </p>
            </div>

            <!-- Bottom Row: Reviewer Details & Product Tag -->
            <div class="pt-4 border-t border-[#ececee]/80 flex items-center justify-between gap-3">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#18181b] to-[#3f3f46] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                  {{ review.name ? review.name.charAt(0) : 'م' }}
                </div>
                <div class="min-w-0">
                  <h4 class="font-extrabold text-xs sm:text-sm text-[#09090b] truncate">{{ review.name }}</h4>
                  <span class="text-[11px] text-[#71717a] block truncate">{{ review.city || 'المملكة العربية السعودية' }}</span>
                </div>
              </div>

              <span v-if="review.product_name" class="text-[10px] bg-[#f4f4f5] text-[#52525b] font-medium px-2.5 py-1 rounded-lg border border-[#ececee] shrink-0 truncate max-w-[110px]">
                {{ review.product_name }}
              </span>
            </div>
          </div>
        </SwiperSlide>

        <!-- Navigation Buttons -->
        <template v-if="showNavigation">
          <button
            class="review-swiper-prev absolute right-1 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-[#09090b] hover:bg-[#18181b] text-white flex items-center justify-center shadow-lg transition-all border border-[#27272a] cursor-pointer disabled:opacity-30"
            aria-label="السابق"
          >
            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            class="review-swiper-next absolute left-1 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-[#09090b] hover:bg-[#18181b] text-white flex items-center justify-center shadow-lg transition-all border border-[#27272a] cursor-pointer disabled:opacity-30"
            aria-label="التالي"
          >
            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </template>
      </Swiper>
    </div>
  </div>
</template>

<style>
.reviews-carousel-swiper {
  width: 100%;
  padding-top: 15px;
  padding-bottom: 50px !important;
}

.reviews-carousel-swiper .swiper-slide {
  height: auto;
  display: flex;
}

.reviews-carousel-swiper .swiper-pagination-bullet {
  background-color: #71717a !important;
  opacity: 0.4;
  transition: all 0.3s ease;
}

.reviews-carousel-swiper .swiper-pagination-bullet-active {
  background-color: #09090b !important;
  width: 24px !important;
  border-radius: 9999px !important;
  opacity: 1;
}

.reviews-carousel-swiper .swiper-button-disabled {
  opacity: 0.2;
  cursor: not-allowed;
}
</style>
