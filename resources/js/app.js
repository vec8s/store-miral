import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import Alpine from "alpinejs";

const appName = "ميرال — متجر الحلي والهدايا الفاخرة";

const inertiaRoot = document.getElementById("app");

if (!inertiaRoot) {
    window.Alpine = Alpine;
    Alpine.start();
} else {
    createInertiaApp({
        title: (title) => (title ? `${title} — ميرال` : appName),
        resolve: (name) =>
            resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob("./Pages/**/*.vue"),
            ),
        setup({ el, App, props, plugin }) {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .mount(el);
        },
        progress: {
            color: "#ff5a00",
        },
    });
}