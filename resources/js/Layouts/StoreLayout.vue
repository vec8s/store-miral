<script setup>
import { provide, ref } from "vue";
import Footer from "../Components/Footer.vue";
import Header from "../Components/Header.vue";

const toastMessage = ref("");
const showToast = ref(false);

provide("triggerToast", (msg) => {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
});
</script>

<template>
    <div class="flex flex-col min-h-full bg-paper text-graphite">
        <Header />

        <div v-if="showToast" class="fixed bottom-6 left-6 z-50 bg-obsidian text-white px-5 py-3.5 rounded-btn border border-[#2c2e34] shadow-2xl flex items-center gap-3 text-sm">
            <span class="w-2 h-2 rounded-full bg-ember"></span>
            <span>{{ toastMessage }}</span>
            <button @click="showToast = false" class="text-fog hover:text-white mr-2">&times;</button>
        </div>

        <main class="flex-grow">
            <slot />
        </main>

        <Footer />
    </div>
</template>