<div class="fixed bottom-6 left-6 z-50 space-y-3 pointer-events-none" x-data="{ notifications: [] }" x-effect="notifications = $store.toast.messages">
    <template x-for="notif in notifications" :key="notif.id">
        <div class="pointer-events-auto bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm">
            <span class="text-sm font-medium" x-text="notif.message"></span>
        </div>
    </template>
</div>
