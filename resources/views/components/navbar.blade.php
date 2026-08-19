<nav class="sticky top-0 z-40 bg-canvasMist/80 backdrop-blur-md py-4 lg:py-6">
    <div class="max-w-[1200px] mx-auto px-4 lg:px-8">
        <div class="relative flex items-center">
            <!-- Search Pill -->
            <div class="w-full max-w-2xl mx-auto relative">
                <div class="relative flex items-center bg-pureWhite rounded-full border border-[rgba(5,41,77,0.1)] shadow-soft overflow-hidden h-[56px]">
                    <input 
                        type="text" 
                        placeholder="ما الذي تبحث عنه اليوم؟" 
                        class="w-full h-full ps-6 pe-14 bg-transparent border-none focus:ring-0 focus:outline-none text-body-lg text-mutedGray placeholder:text-mutedGray/70 font-gt tracking-shop-lg"
                        x-model="window.shopSearchQuery"
                        @keydown.enter.prevent="window.location.href = '/shop?q=' + encodeURIComponent(window.shopSearchQuery || '')"
                    >
                    <button 
                        @click="window.location.href = '/shop?q=' + encodeURIComponent(window.shopSearchQuery || '')"
                        class="absolute end-1.5 top-1/2 -translate-y-1/2 w-[48px] h-[48px] bg-shopViolet rounded-full flex items-center justify-center shadow-violet-glow hover:bg-[#4527c9] transition-colors"
                        aria-label="بحث"
                    >
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    window.shopSearchQuery = new URLSearchParams(window.location.search).get('q') || '';
</script>
