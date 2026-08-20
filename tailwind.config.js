import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Cairo", "Tajawal", ...defaultTheme.fontFamily.sans],
                display: ["Tajawal", "Cairo", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                obsidian: "#09090b",
                graphite: "#18181b",
                slate: "#27272a",
                iron: "#3f3f46",
                steel: "#52525b",
                fog: "#71717a",
                ash: "#a1a1aa",
                mist: "#d4d4d8",
                cloud: "#ececee",
                paper: "#f4f4f5",
                snow: "#ffffff",
                ember: "#ff5a00",
                'magenta-spark': "#fe45e2",
                brand: {
                    50:  "#fdf6ed",
                    100: "#fae8cd",
                    200: "#f4d199",
                    300: "#edb260",
                    400: "#e69637",
                    500: "#d97b1c",
                    600: "#bf6115",
                    700: "#994914",
                    800: "#7c3c17",
                    900: "#673318",
                },
                gold: {
                    400: "#d4af37",
                    500: "#c5a028",
                    600: "#a88520",
                },
            },
            borderRadius: {
                'card': '36px',
                'badge': '12px',
                'btn': '14px',
                'pill': '10000px',
            },
            boxShadow: {
                soft: "0 2px 12px -2px rgb(0 0 0 / 0.08)",
                card: "0 4px 24px -6px rgb(0 0 0 / 0.10)",
            },
        },
    },
    plugins: [forms, typography],
};
