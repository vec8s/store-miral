<script setup>
import { inject, reactive } from "vue";
import StoreLayout from "../../Layouts/StoreLayout.vue";

defineOptions({ layout: StoreLayout });

const triggerToast = inject("triggerToast", () => {});

const form = reactive({
    name: "",
    contact: "",
    message: "",
});

const submit = () => {
    triggerToast("تم إرسال رسالتك بنجاح! سنرد عليك قريباً.");
    form.name = "";
    form.contact = "";
    form.message = "";
};
</script>

<template>
    <div class="py-10 sm:py-16 bg-paper">
        <div class="container-rtl max-w-3xl">

            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-obsidian mb-2 tracking-tight">تواصل مع متجر ميرال</h1>
                <p class="text-fog text-xs sm:text-sm">يسعدنا استقبال استفساراتكم واقتراحاتكم في أي وقت</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4 sm:gap-6 md:gap-8 items-start">

                <div class="card-awesomic p-6 space-y-6">
                    <div>
                        <h3 class="font-bold text-obsidian text-xs sm:text-sm mb-1">📍 المقر الرئيسي</h3>
                        <p class="text-xs text-steel">طريق الملك عبدالعزيز، الرياض، المملكة العربية السعودية</p>
                    </div>

                    <div>
                        <h3 class="font-bold text-obsidian text-xs sm:text-sm mb-1">📞 خدمة العملاء (واتساب)</h3>
                        <p class="text-xs text-steel dir-ltr text-right">+966 50 000 0000</p>
                    </div>

                    <div>
                        <h3 class="font-bold text-obsidian text-xs sm:text-sm mb-1">✉️ البريد الإلكتروني</h3>
                        <p class="text-xs text-steel">support@miral.sa</p>
                    </div>

                    <div>
                        <h3 class="font-bold text-obsidian text-xs sm:text-sm mb-1">⏰ أوقات العمل</h3>
                        <p class="text-xs text-steel">من السبت إلى الخميس: 9:00 صباحاً - 10:00 مساءً</p>
                    </div>
                </div>

                <div class="card-awesomic p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-graphite mb-1">الاسم الكامل *</label>
                            <input v-model="form.name" type="text" required class="input-awesomic text-xs py-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-graphite mb-1">رقم الجوال أو الإيميل *</label>
                            <input v-model="form.contact" type="text" required class="input-awesomic text-xs py-2.5">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-graphite mb-1">الرسالة *</label>
                            <textarea v-model="form.message" rows="4" required class="input-awesomic text-xs py-2.5"></textarea>
                        </div>

                        <button type="submit" class="btn-primary w-full py-3 text-xs font-medium">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>
</template>