<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import {
  Users, ClipboardList, Clock, DoorOpen, Package, CheckCircle2,
  ArrowRight, Timer, Building2, AlertCircle,
} from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  stats: Object,
  activePeminjamans: { type: Array, default: () => [] },
});

// ── Stat Cards ─────────────────────────────────────
const cards = [
  { label: 'Total Peminjam', value: () => props.stats.users, icon: Users, color: 'from-blue-500 to-blue-600', bg: 'bg-blue-50', text: 'text-blue-600' },
  { label: 'Total Ruangan', value: () => props.stats.ruangan, icon: DoorOpen, color: 'from-violet-500 to-purple-600', bg: 'bg-violet-50', text: 'text-violet-600' },
  { label: 'Total Barang', value: () => props.stats.barang, icon: Package, color: 'from-teal-500 to-teal-600', bg: 'bg-teal-50', text: 'text-teal-600' },
  { label: 'Menunggu Persetujuan', value: () => props.stats.pending, icon: Clock, color: 'from-amber-500 to-orange-500', bg: 'bg-amber-50', text: 'text-amber-600' },
];

// ── Quick Access ───────────────────────────────────
const quickLinks = [
  { label: 'Kelola Peminjaman', href: '/admin/kelola-peminjaman', icon: ClipboardList, color: 'text-blue-600 bg-blue-50' },
  { label: 'Kelola Ruangan', href: '/admin/kelola-ruangan', icon: DoorOpen, color: 'text-violet-600 bg-violet-50' },
  { label: 'Kelola Barang', href: '/admin/kelola-barang', icon: Package, color: 'text-teal-600 bg-teal-50' },
  { label: 'Kelola User', href: '/admin/kelola-user', icon: Users, color: 'text-orange-600 bg-orange-50' },
];

// ── Countdown Timer ────────────────────────────────
const now = ref(Date.now());
let timerInterval = null;

onMounted(() => {
  timerInterval = setInterval(() => { now.value = Date.now(); }, 1000);
});
onUnmounted(() => { clearInterval(timerInterval); });

const getCountdown = (targetDatetime) => {
  const target = new Date(targetDatetime).getTime();
  const diff = target - now.value;
  if (diff <= 0) return { text: 'Waktu habis', expired: true };
  const h = Math.floor(diff / 3600000);
  const m = Math.floor((diff % 3600000) / 60000);
  const s = Math.floor((diff % 60000) / 1000);
  const pad = (n) => String(n).padStart(2, '0');
  if (h >= 24) {
    const d = Math.floor(h / 24);
    const rh = h % 24;
    return { text: `${d}h ${pad(rh)}j ${pad(m)}m`, expired: false };
  }
  return { text: `${pad(h)}j ${pad(m)}m ${pad(s)}d`, expired: false };
};

// ── Validasi Selesai ───────────────────────────────
const processingId = ref(null);
const validasiSelesai = (id) => {
  if (!confirm('Tandai peminjaman ini sebagai selesai? Stok barang akan dikembalikan.')) return;
  processingId.value = id;
  router.patch(`/admin/kelola-peminjaman/${id}/selesai`, {}, {
    preserveScroll: true,
    onFinish: () => { processingId.value = null; },
  });
};

// ── Greeting ───────────────────────────────────────
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
    <!-- ── Hero Banner ─────────────────────────────── -->
    <div class="mb-8 overflow-hidden rounded-none border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)] text-white relative min-h-[220px] flex items-center p-6">
      <img src="/image/hero section admin.png" alt="Admin Hero" class="absolute inset-0 w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/60" />
      
      <div class="relative z-10 max-w-lg bg-yellow-400 border-4 border-black p-5 text-black shadow-[4px_4px_0px_rgba(0,0,0,1)]">
        <h2 class="text-2xl font-black uppercase tracking-tight mb-2">
          {{ greetingMessage }}, {{ $page.props.auth.user?.name }}!
        </h2>
        <h3 class="text-lg font-black uppercase tracking-wide text-red-600 mb-1">
          Selamat Datang di Panel Admin
        </h3>
        <p class="text-sm font-bold leading-relaxed text-gray-900">
          Kelola sistem peminjaman dengan cepat dan efisien hari ini. Pantau penggunaan ruangan, inventaris barang, serta status persetujuan secara real-time.
        </p>
      </div>
    </div>

    <!-- ── Stat Cards ──────────────────────────────── -->
    <div class="mb-8">
      <h2 class="text-lg font-bold text-slate-900 mb-4">Ringkasan Sistem</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard v-for="card in cards" :key="card.label" :label="card.label" :value="card.value()" :icon="card.icon" :color="card.color" :bg-light="card.bg" :text-color="card.text" />
      </div>
    </div>

    <!-- ── Quick Access ────────────────────────────── -->
    <div class="mb-8">
      <h2 class="text-lg font-bold text-slate-900 mb-4">Akses Cepat</h2>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <Link v-for="link in quickLinks" :key="link.href" :href="link.href"
          class="group flex items-center gap-3 rounded-xl border border-border bg-card p-4 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
          <div :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-lg transition-colors', link.color]">
            <component :is="link.icon" class="h-5 w-5" />
          </div>
          <span class="text-sm font-semibold text-foreground">{{ link.label }}</span>
          <ArrowRight class="h-4 w-4 text-slate-300 ml-auto group-hover:text-slate-500 transition-colors" />
        </Link>
      </div>
    </div>

    <!-- ── Peminjaman Sedang Berjalan (Countdown) ─── -->
    <div>
      <div class="flex items-center gap-2 mb-4">
        <Timer class="h-5 w-5 text-orange-500" />
        <h2 class="text-lg font-bold text-slate-900">Peminjaman Sedang Berjalan</h2>
        <span class="text-xs text-slate-400 ml-1">({{ activePeminjamans.length }})</span>
      </div>

      <div v-if="activePeminjamans.length" class="overflow-x-auto rounded-xl border border-border bg-card shadow-sm">
        <table class="w-full text-sm">
          <thead class="border-b border-slate-100 bg-slate-50/80">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Peminjam</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Aset</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tipe</th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jadwal</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Sisa Waktu</th>
              <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="p in activePeminjamans" :key="p.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="px-4 py-3 font-semibold text-slate-800">{{ p.user_name }}</td>
              <td class="px-4 py-3 text-slate-700">{{ p.nama_item }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-semibold',
                  p.tipe === 'ruangan' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-sky-50 text-sky-700 border-sky-200']">
                  <component :is="p.tipe === 'ruangan' ? Building2 : Package" class="h-3 w-3" />
                  {{ p.tipe === 'ruangan' ? 'Ruangan' : 'Barang' }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500">
                {{ p.tanggal_mulai }} — {{ p.tanggal_selesai }}<br/>
                <span class="text-slate-400">{{ p.jam_mulai }} - {{ p.jam_selesai }}</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="['inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold',
                  getCountdown(p.target_datetime).expired ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700']">
                  <Clock class="h-3 w-3" />
                  {{ getCountdown(p.target_datetime).text }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="validasiSelesai(p.id)" :disabled="processingId === p.id"
                  class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-emerald-700 disabled:opacity-50 transition-colors">
                  <CheckCircle2 class="h-3.5 w-3.5" />
                  {{ processingId === p.id ? 'Proses...' : 'Validasi Selesai' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="rounded-xl border border-dashed border-slate-200 bg-white py-12 text-center">
        <CheckCircle2 class="mx-auto h-10 w-10 text-slate-300" />
        <p class="mt-3 text-sm text-slate-400">Tidak ada peminjaman yang sedang berjalan.</p>
      </div>
    </div>
  </div>
</template>
