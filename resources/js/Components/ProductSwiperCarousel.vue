<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, EffectCoverflow, Pagination, Navigation } from "swiper/modules";

// Swiper core styles
import "swiper/css";
import "swiper/css/effect-coverflow";
import "swiper/css/pagination";
import "swiper/css/navigation";

const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },
  autoplayDelay: {
    type: Number,
    default: 2500,
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

const modules = [EffectCoverflow, Autoplay, Pagination, Navigation];

// Ensure we have a rich list of products
const displayProducts = computed(() => {
  if (props.products && props.products.length > 0) {
    return props.products;
  }
  return [];
});
</script>

<template>
  <div class="product-swiper-carousel relative w-full overflow-hidden py-4">
    <div class="w-full max-w-6xl mx-auto px-4">
      <Swiper
        :modules="modules"
        effect="coverflow"
        :grabCursor="true"
        :centeredSlides="true"
        slidesPerView="auto"
        :loop="loop && displayProducts.length >= 3"
        :autoplay="{
          delay: autoplayDelay,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        }"
        :coverflowEffect="{
          rotate: 30,
          stretch: 0,
          depth: 120,
          modifier: 1.2,
          slideShadows: true,
        }"
        :pagination="showPagination ? { clickable: true, dynamicBullets: true } : false"
        :navigation="showNavigation ? { nextEl: '.product-swiper-next', prevEl: '.product-swiper-prev' } : false"
        class="product-carousel-swiper pb-14"
      >
        <SwiperSlide
          v-for="product in displayProducts"
          :key="product.id"
          class="product-slide-item rounded-2xl overflow-hidden shadow-card border border-cloud bg-white transition-all duration-300 group"
        >
          <Link :href="`/shop/${product.id}`" class="block h-full w-full relative">
            <!-- Product Image -->
            <div class="aspect-square w-full bg-paper overflow-hidden relative">
              <img
                :src="product.thumbnail_url || (product.images && product.images[0]) || '/favicon.svg'"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />
              <span
                v-if="product.sale_price"
                class="absolute top-3 right-3 bg-ember text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-sm"
              >
                خصم
              </span>
            </div>

            <!-- Product Details Overlay/Footer -->
            <div class="p-4 bg-white/95 backdrop-blur-sm border-t border-cloud">
              <span class="text-[11px] text-fog font-medium block mb-1">
                {{ (product.category && product.category.name) || 'ميرال' }}
              </span>
              <h3 class="font-bold text-sm text-obsidian line-clamp-1 group-hover:text-ember transition-colors">
                {{ product.name }}
              </h3>
              <div class="mt-2 flex items-center justify-between">
                <div class="font-extrabold text-sm text-obsidian">
                  <span>{{ Number(product.sale_price || product.price).toFixed(2) }}</span>
                  <span class="currency-sar text-xs font-bold text-fog mr-1">ر.س</span>
                </div>
                <span v-if="product.sale_price" class="text-xs text-fog line-through">
                  {{ Number(product.price).toFixed(2) }}
                </span>
              </div>
            </div>
          </Link>
        </SwiperSlide>

        <!-- Custom Navigation Arrows -->
        <template v-if="showNavigation">
          <button
            class="product-swiper-prev absolute right-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-obsidian/80 hover:bg-obsidian text-white flex items-center justify-center backdrop-blur-md shadow-lg transition-all border border-slate/40 cursor-pointer"
            aria-label="السابق"
          >
            <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            class="product-swiper-next absolute left-2 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-obsidian/80 hover:bg-obsidian text-white flex items-center justify-center backdrop-blur-md shadow-lg transition-all border border-slate/40 cursor-pointer"
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
.product-carousel-swiper {
  width: 100%;
  padding-top: 20px;
  padding-bottom: 50px !important;
}

.product-carousel-swiper .product-slide-item {
  width: 260px;
  max-width: 80vw;
  height: auto;
}

@media (min-width: 640px) {
  .product-carousel-swiper .product-slide-item {
    width: 290px;
  }
}

.product-carousel-swiper .swiper-pagination-bullet {
  background-color: #71717a !important;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.product-carousel-swiper .swiper-pagination-bullet-active {
  background-color: #ff5a00 !important;
  width: 20px !important;
  border-radius: 9999px !important;
  opacity: 1;
}

.product-carousel-swiper .swiper-button-disabled {
  opacity: 0.3;
  cursor: not-allowed;
}
</style>
