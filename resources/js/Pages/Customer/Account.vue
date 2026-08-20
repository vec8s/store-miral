<script setup>
import { computed, reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const form = reactive({
    name: user.value?.name ?? "",
    email: user.value?.email ?? "",
    phone: user.value?.phone ?? "",
});

const updateProfile = async () => {
    try {
        const { data } = await window.axios.put("/api/account/update", form);

        if (data.success) {
            router.reload();
        }
    } catch (err) {
        console.error(err);
    }
};
</script>

<template>
    <div class="py-12 bg-paper">
        <div class="container-rtl max-w-2xl">

            <div class="flex flex-wrap items-center justify-between gap-2 mb-8">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-extrabold text-obsidian tracking-tight mb-1">الملف الشخصي</h1>
                        <span v-if="user?.is_admin" class="badge-ember text-xs">مدير النظام (Admin)</span>
                        <span v-else class="badge-tag text-xs">عميل</span>
                    </div>
                    <p class="text-fog text-xs sm:text-sm">إدارة حسابك وبياناتك الشخصية في متجر ميرال</p>
                </div>
                <button @click="router.post('/logout')" class="btn-ghost text-xs text-red-600 hover:bg-red-50 px-4 py-2 flex items-center gap-1.5 font-bold min-h-[44px]">
                    <span>🚪</span> تسجيل الخروج
                </button>
            </div>

            <div class="card-awesomic p-6 md:p-8">
                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-graphite mb-1.5">الاسم الكامل</label>
                        <input v-model="form.name" type="text" name="name" class="input-awesomic text-xs sm:text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-graphite mb-1.5">البريد الإلكتروني</label>
                        <input v-model="form.email" type="email" name="email" class="input-awesomic text-xs sm:text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-graphite mb-1.5">رقم الجوال</label>
                        <input v-model="form.phone" type="tel" name="phone" class="input-awesomic text-xs sm:text-sm py-2.5">
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-xs sm:text-sm font-medium rounded-btn mt-4">
                        حفظ التغييرات &larr;
                    </button>
                </form>
            </div>

        </div>
    </div>
</template>