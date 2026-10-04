<script setup>
import { computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import {
  Home,
  ClipboardList,
  DoorOpen,
  Package,
  ShieldCheck,
  CalendarDays,
  LogOut,
  AlertTriangle,
} from '@lucide/vue';

import sidebarLogo from '@images/side bar user.png';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const currentUrl = computed(() => page.url);

const navItems = [
    { label: 'Dashboard', icon: Home, href: '/dashboard' },
    { label: 'Riwayat Peminjaman', icon: ClipboardList, href: '/bookings' },
    { label: 'Ruangan', icon: DoorOpen, href: '/ruangan' },
    { label: 'Barang', icon: Package, href: '/barang' },
    { label: 'Tata Tertib', icon: ShieldCheck, href: '/tata_tertib' },
    { label: 'Kalender Akademik', icon: CalendarDays, href: '/kalender' },
    { label: 'Lapor Pelanggaran', icon: AlertTriangle, href: '/lapor-pelanggaran' },
];

const isActive = (href) => {
    if (href === '/dashboard') return currentUrl.value === '/dashboard';
    return currentUrl.value === href || currentUrl.value.startsWith(href + '/');
};

const logout = () => router.post('/logout');

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-blue-700 via-blue-600 to-indigo-700 text-white shadow-xl overflow-hidden">
        <!-- ── Avatar & Profile (Top) ──────────────────── -->
        <div class="relative z-10 flex flex-col items-center pt-6 pb-4 px-5 shrink-0 border-b border-white/10 bg-black/5">
            <Link href="/profile" class="group flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full border-2 border-white/40 overflow-hidden bg-white/10 flex items-center justify-center mb-2 group-hover:border-blue-300 transition-colors shadow-sm">
                    <img v-if="user?.avatar" :src="user.avatar" :alt="user?.name" class="w-full h-full object-cover" />
                    <span v-else class="text-lg font-bold text-white">{{ getInitials(user?.name) }}</span>
                </div>
                <p class="text-sm font-bold text-white truncate max-w-[200px] group-hover:text-blue-200 transition-colors">{{ user?.name || 'User' }}</p>
                <p class="text-[11px] text-blue-100/70 truncate max-w-[200px]">{{ user?.email || '' }}</p>
            </Link>
        </div>

        <!-- ── Navigation (Scrollable Area with Bottom-Right Watermark Logo) ── -->
        <div class="relative flex-1 min-h-0 overflow-hidden">
            <!-- Aesthetic Watermark Background Logo filling the nav area -->
            <img
                :src="sidebarLogo"
                alt="SIPINJAM Logo Background"
                class="absolute inset-0 w-full h-full object-contain object-bottom-right opacity-[0.45] pointer-events-none select-none mix-blend-screen scale-105"
            />

            <nav class="relative z-10 h-full overflow-y-auto px-4 py-3 space-y-1 custom-scrollbar">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    :class="[
                        'flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-all duration-200',
                        isActive(item.href)
                            ? 'bg-white/20 text-white shadow-sm font-semibold backdrop-blur-xs'
                            : 'text-blue-100/80 hover:bg-white/10 hover:text-white',
                    ]"
                >
                    <component :is="item.icon" class="h-[18px] w-[18px] shrink-0" />
                    <span class="truncate">{{ item.label }}</span>
                </Link>
            </nav>
        </div>

        <!-- ── Sticky Footer (Logout Button Always Visible) ── -->
        <div class="relative z-10 shrink-0 mt-auto border-t border-white/10 bg-black/20 p-4">
            <button
                @click="logout"
                id="btn-sidebar-logout"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-500/90 hover:text-white transition-all duration-200 shadow-sm cursor-pointer"
            >
                <LogOut class="h-4 w-4 shrink-0" />
                <span>Keluar Akun</span>
            </button>
        </div>
    </aside>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.4);
}
</style>
