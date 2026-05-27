<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import { Users, ClipboardList, Clock, DoorOpen, Package, CheckCircle2 } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ stats: Object });

const cards = [
    { label: 'Pending', value: () => props.stats.pending, icon: Clock, color: 'from-amber-500 to-orange-500', bg: 'bg-amber-50', text: 'text-amber-600' },
    { label: 'Disetujui', value: () => props.stats.approved, icon: CheckCircle2, color: 'from-emerald-500 to-green-600', bg: 'bg-emerald-50', text: 'text-emerald-600' },
    { label: 'Total User', value: () => props.stats.users, icon: Users, color: 'from-orange-500 to-amber-600', bg: 'bg-orange-50', text: 'text-orange-600' },
    { label: 'Ruangan', value: () => props.stats.ruangan, icon: DoorOpen, color: 'from-violet-500 to-purple-600', bg: 'bg-violet-50', text: 'text-violet-600' },
    { label: 'Barang', value: () => props.stats.barang, icon: Package, color: 'from-teal-500 to-teal-600', bg: 'bg-teal-50', text: 'text-teal-600' },
];

const greetingMessage = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 5 && hour < 11) return 'Selamat Pagi';
  if (hour >= 11 && hour < 15) return 'Selamat Siang';
  if (hour >= 15 && hour < 18.5) return 'Selamat Sore';
  return 'Selamat Malam';
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="px-6 py-8 lg:px-10">
        <!-- ── Adaptive Hero Banner Section ──────────────── -->
        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-orange-600 to-amber-500 p-6 shadow-sm text-white relative">
            <div class="relative z-10">
                <h2 class="text-xl font-bold tracking-tight md:text-2xl">{{ greetingMessage }}, {{ $page.props.auth.user?.name }}!</h2>
                <p class="text-xs text-orange-100/90 mt-1 md:text-sm">
                    Selamat mengelola fasilitas kampus STITEK Bontang. Jaga efisiensi peminjaman aset hari ini.
                </p>
            </div>
            <!-- Subtle visual accents -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-xl pointer-events-none" />
            <div class="absolute right-20 -bottom-20 w-60 h-60 bg-amber-500/10 rounded-full blur-2xl pointer-events-none" />
        </div>

        <div class="mb-8 flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">Ringkasan Sistem</h1>
                <p class="text-xs text-slate-500">Ringkasan status peminjaman dan aset kampus STITEK Bontang.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <StatCard
                v-for="card in cards"
                :key="card.label"
                :label="card.label"
                :value="card.value()"
                :icon="card.icon"
                :color="card.color"
                :bg-light="card.bg"
                :text-color="card.text"
            />
        </div>
    </div>
</template>
