<div class="fixed bottom-24 lg:bottom-6 start-1/2 -translate-x-1/2 z-[60] space-y-2 pointer-events-none">
    <template x-for="notif in $store.toast.messages" :key="notif.id">
        <div x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-2" 
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="pointer-events-auto bg-inkBlack text-pureWhite px-5 py-3 rounded-full shadow-deep flex items-center gap-3 min-w-[280px]">
            <svg class="w-4 h-4 text-pureWhite shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-body-sm font-medium tracking-shop-meta" x-text="notif.message"></span>
        </div>
    </template>
</div>
