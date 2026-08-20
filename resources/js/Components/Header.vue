<script setup>
import { computed, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage();

const user = computed(() => page.props.auth?.user ?? null);
const cartCount = computed(() => page.props.cartCount ?? 0);
const wishlistCount = computed(() => page.props.wishlistCount ?? 0);

const mobileOpen = ref(false);
const searchOpen = ref(false);
const userMenuOpen = ref(false);

const logout = () => router.post("/logout");

const navLinks = [
    { label: "الرئيسية", href: "/" },
    { label: "المتجر", href: "/shop" },
    { label: "الأقسام", href: "/categories" },
    { label: "من نحن", href: "/about" },
    { label: "تواصل معنا", href: "/contact" },
];

const isActive = (href) =>
    href === "/" ? page.url === "/" : page.url.startsWith(href);
</script>

<template>
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-cloud">
        <div class="container-rtl">
            <div class="flex items-center justify-between h-20">

                <Link href="/" class="flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-btn bg-obsidian text-white flex items-center justify-center text-lg font-bold border border-[#2c2e34]">
                        م
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-obsidian leading-tight tracking-tight">ميرال</span>
                        <span class="hidden sm:block text-[11px] text-fog font-normal">متجر الحلي والهدايا</span>
                    </div>
                </Link>

                <nav class="hidden lg:flex items-center gap-8 text-sm font-medium">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :class="isActive(link.href)
                            ? 'text-obsidian font-bold underline underline-offset-8 decoration-2 decoration-ember'
                            : 'text-steel hover:text-obsidian transition'"
                    >{{ link.label }}</Link>
                </nav>

                <div class="flex items-center gap-2.5">
                    <button
                        @click="searchOpen = !searchOpen"
                        class="w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud flex items-center justify-center text-graphite hover:bg-paper transition"
                        title="بحث"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>
                    </button>

                    <Link href="/wishlist" class="relative hidden sm:flex w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud items-center justify-center text-graphite hover:bg-paper transition" title="المفضلة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span v-if="wishlistCount > 0" class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] px-1 rounded-full bg-ember text-white text-[11px] font-bold flex items-center justify-center">
                            {{ wishlistCount }}
                        </span>
                    </Link>

                    <Link href="/cart" class="relative w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud flex items-center justify-center text-graphite hover:bg-paper transition" title="السلة">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span v-if="cartCount > 0" id="cart-badge" class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] px-1 rounded-full bg-obsidian text-white text-[11px] font-bold flex items-center justify-center">
                            {{ cartCount }}
                        </span>
                    </Link>

                    <Link href="/orders" class="relative hidden sm:flex w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud items-center justify-center text-graphite hover:bg-paper transition" title="طلباتي">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </Link>

                    <template v-if="user">
                        <div class="relative">
                            <button @click="userMenuOpen = !userMenuOpen" class="w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud flex items-center justify-center text-graphite hover:bg-paper transition" title="الحساب الشخصي">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </button>

                            <div v-if="userMenuOpen" class="fixed inset-0 z-40" @click="userMenuOpen = false"></div>
                            <div v-if="userMenuOpen" class="absolute left-0 mt-2 w-52 bg-white border border-cloud rounded-[20px] shadow-xl p-2 z-50">
                                <div class="px-3.5 py-2.5 border-b border-cloud mb-1.5">
                                    <p class="text-xs font-bold text-obsidian">{{ user.name }}</p>
                                    <p class="text-[11px] text-fog truncate mt-0.5">{{ user.email }}</p>
                                </div>
                                <Link href="/account/profile" class="flex items-center gap-2.5 px-3 py-2 rounded-badge text-xs font-medium text-graphite hover:bg-paper transition">
                                    <svg class="w-4 h-4 text-fog" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    الملف الشخصي
                                </Link>
                                <Link href="/orders" class="flex items-center gap-2.5 px-3 py-2 rounded-badge text-xs font-medium text-graphite hover:bg-paper transition">
                                    <svg class="w-4 h-4 text-fog" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    طلباتي
                                </Link>
                                <a v-if="user.is_admin" href="/admin" class="flex items-center gap-2.5 px-3 py-2 rounded-badge text-xs font-bold text-ember hover:bg-[#fafafa] transition">
                                    <svg class="w-4 h-4 text-ember" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    لوحة تحكم الإدارة
                                </a>
                                <button @click="logout" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-badge text-xs font-bold text-red-600 hover:bg-red-50 transition mt-1 border-t border-cloud">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    تسجيل الخروج
                                </button>
                            </div>
                        </div>
                    </template>
                    <a v-else href="/login" class="inline-flex items-center gap-1.5 text-xs bg-obsidian text-white px-3.5 py-2.5 rounded-btn font-medium hover:bg-slate transition whitespace-nowrap" title="تسجيل الدخول">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>تسجيل الدخول</span>
                    </a>

                    <a v-if="user?.is_admin" href="/admin" class="hidden sm:inline-flex items-center gap-1.5 text-xs bg-[#fafafa] text-graphite border border-cloud px-3.5 py-2 rounded-pill font-medium hover:bg-paper transition">
                        <span class="w-1.5 h-1.5 rounded-full bg-ember"></span>
                        لوحة الإدارة
                    </a>

                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden w-10 h-10 rounded-btn bg-[#fafafa] border border-cloud flex items-center justify-center text-graphite">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="searchOpen" class="pb-4">
                <form action="/shop" method="GET" class="relative">
                    <input type="search" name="q" placeholder="ابحث عن سلسلة، ساعة، بوكس هدية..." class="input-awesomic pr-11 py-3 text-sm">
                    <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-fog hover:text-obsidian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <nav v-if="mobileOpen" class="lg:hidden pb-5 space-y-1">
                <Link v-for="link in navLinks" :key="link.href" :href="link.href" class="block px-4 py-2.5 rounded-btn text-graphite hover:bg-[#fafafa] font-medium text-sm">
                    {{ link.label }}
                </Link>

                <div class="pt-3 border-t border-cloud space-y-1">
                    <template v-if="user">
                        <Link href="/account/profile" class="flex items-center gap-2 px-4 py-2.5 rounded-btn text-graphite hover:bg-[#fafafa] font-medium text-sm">{{ user.name }}</Link>
                        <Link href="/orders" class="flex items-center gap-2 px-4 py-2.5 rounded-btn text-graphite hover:bg-[#fafafa] font-medium text-sm">طلباتي</Link>
                        <button @click="logout" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-btn text-red-600 hover:bg-red-50 font-bold text-sm">تسجيل الخروج</button>
                    </template>
                    <template v-else>
                        <a href="/login" class="flex items-center gap-2 px-4 py-2.5 rounded-btn bg-obsidian text-white font-medium text-sm justify-center">تسجيل الدخول</a>
                        <a href="/register" class="flex items-center gap-2 px-4 py-2.5 rounded-btn border border-cloud text-graphite font-medium text-sm justify-center">إنشاء حساب جديد</a>
                    </template>
                    <a v-if="user?.is_admin" href="/admin" class="block px-4 py-2.5 rounded-btn bg-[#fafafa] border border-cloud text-graphite font-medium text-sm text-center mt-2">
                        لوحة تحكم الإدارة
                    </a>
                </div>
            </nav>
        </div>
    </header>
</template>