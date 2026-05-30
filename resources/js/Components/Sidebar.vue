<script setup>
import { computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import { Home, ClipboardList, DoorOpen, Package, ShieldCheck, CalendarDays, LogOut, User } from '@lucide/vue';

import sidebarLogo from '@images/side bar user.png';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentUrl = computed(() => page.url);

const navItems = [
    { label: 'Riwayat Peminjaman', icon: ClipboardList, href: '/bookings' },
    { label: 'Ruangan', icon: DoorOpen, href: '/ruangan' },
    { label: 'Barang', icon: Package, href: '/barang' },
    { label: 'Tata Tertib', icon: ShieldCheck, href: '/tata_tertib' },
    { label: 'Kalender Akademik', icon: CalendarDays, href: '/kalender' },
];

const isActive = (href) => currentUrl.value === href || currentUrl.value.startsWith(href + '/');
const logout = () => router.post('/logout');

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-blue-700 via-blue-600 to-indigo-700 text-white">

        <!-- ── Avatar & Profile (Top) ──────────────────── -->
        <div class="flex flex-col items-center pt-8 pb-6 px-5">
            <Link href="/profile" class="group flex flex-col items-center">
                <div class="w-20 h-20 rounded-full border-[3px] border-white/30 overflow-hidden bg-white/10 flex items-center justify-center mb-3 group-hover:border-blue-300 transition-colors">
                    <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover" />
                    <span v-else class="text-xl font-bold text-white/70">{{ getInitials(user?.name) }}</span>
                </div>
                <p class="text-sm font-bold text-white truncate max-w-full group-hover:text-blue-200 transition-colors">{{ user?.name || 'User' }}</p>
                <p class="text-[11px] text-blue-200/70 truncate max-w-full">{{ user?.email || '' }}</p>
            </Link>
        </div>

        <!-- ── Navigation ──────────────────────────────── -->
        <nav class="flex-1 px-4 space-y-1">
            <Link href="/dashboard"
                :class="[
                    'flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200',
                    currentUrl === '/dashboard'
                        ? 'bg-white/20 text-white shadow-sm'
                        : 'text-blue-100/80 hover:bg-white/10 hover:text-white',
                ]"
            >
                <Home class="h-[18px] w-[18px] shrink-0" />
                <span class="truncate">Dashboard</span>
            </Link>
            <Link
                v-for="item in navItems"
                :key="item.href"
                :href="item.href"
                :class="[
                    'flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200',
                    isActive(item.href)
                        ? 'bg-white/20 text-white shadow-sm'
                        : 'text-blue-100/80 hover:bg-white/10 hover:text-white',
                ]"
            >
                <component :is="item.icon" class="h-[18px] w-[18px] shrink-0" />
                <span class="truncate">{{ item.label }}</span>
            </Link>
        </nav>

        <!-- ── Logo SIPINJAM ───────────────────────────── -->
        <div class="flex items-center justify-center px-6 py-4">
            <img :src="sidebarLogo" alt="SIPINJAM" class="h-28 w-auto object-contain opacity-80" />
        </div>

        <!-- ── Logout ──────────────────────────────────── -->
        <div class="px-4 pb-5">
            <button
                @click="logout"
                class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-blue-100/80 hover:bg-white/10 hover:text-white transition-all duration-200"
            >
                <LogOut class="h-[18px] w-[18px] shrink-0" />
                <span>Keluar</span>
            </button>
        </div>
    </aside>
</template>
