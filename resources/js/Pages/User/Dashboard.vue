<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import BookingModal from '@/Components/BookingModal.vue';
import StatCard from '@/Components/StatCard.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import {
  ClipboardList,
  PackageCheck,
  Clock,
  CheckCircle2,
  Building2,
  Package,
  Users,
  MapPin,
  Layers,
  Search,
  Sparkles,
  AlertCircle,
  X,
  Plus,
  History,
  CalendarDays,
} from '@lucide/vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';

defineOptions({ layout: UserLayout });

const props = defineProps({
  stats: Object,
  ruangans: Array,
  barangs: Array,
  calendarEvents: { type: Array, default: () => [] },
});

const page = usePage();
const flash = computed(() => page.props.flash);
const pageErrors = computed(() => page.props.errors);

// ── Toast State ──────────────────────────────────────
const showSuccessToast = ref(false);
const showErrorToast = ref(false);
const errorMessages = ref([]);
let successTimer = null;
let errorTimer = null;

// Watch for flash success
watch(() => flash.value?.success, (msg) => {
  if (msg) {
    showSuccessToast.value = true;
    clearTimeout(successTimer);
    successTimer = setTimeout(() => { showSuccessToast.value = false; }, 5000);
  }
}, { immediate: true });

// Watch for errors (validation + flash.error)
watch([() => pageErrors.value, () => flash.value?.error], ([errors, flashErr]) => {
  const msgs = [];
  if (flashErr) msgs.push(flashErr);
  if (errors && typeof errors === 'object') {
    Object.values(errors).forEach(e => {
      if (Array.isArray(e)) msgs.push(...e);
      else if (typeof e === 'string') msgs.push(e);
    });
  }
  if (msgs.length) {
    errorMessages.value = msgs;
    showErrorToast.value = true;
    clearTimeout(errorTimer);
    errorTimer = setTimeout(() => { showErrorToast.value = false; }, 8000);
  }
}, { immediate: true });

// ── Search & Filter ────────────────────────────────
const searchQuery = ref('');

const filteredRuangans = computed(() => {
  const q = searchQuery.value.toLowerCase();
  if (!q) return props.ruangans;
  return props.ruangans.filter(
    (r) =>
      r.nama.toLowerCase().includes(q) ||
      (r.lokasi && r.lokasi.toLowerCase().includes(q))
  );
});

const filteredBarangs = computed(() => {
  const q = searchQuery.value.toLowerCase();
  if (!q) return props.barangs;
  return props.barangs.filter(
    (b) =>
      b.nama.toLowerCase().includes(q) ||
      (b.kategori && b.kategori.toLowerCase().includes(q))
  );
});

// ── Booking Modal State ────────────────────────────
const showBookingModal = ref(false);
const selectedAsset = ref(null);
const prefillDates = ref(null);

const openBooking = (asset, type) => {
  selectedAsset.value = { ...asset, tipe: type };
  prefillDates.value = null;
  showBookingModal.value = true;
};

const openQuickBooking = () => {
  selectedAsset.value = null;
  prefillDates.value = null;
  showBookingModal.value = true;
};

// ── Guest Cookie Detection (from Landing page drag) ─
onMounted(() => {
  const cookies = document.cookie.split(';').map(c => c.trim());
  const guestCookie = cookies.find(c => c.startsWith('sipinjam_guest_dates='));
  if (guestCookie) {
    try {
      const val = decodeURIComponent(guestCookie.split('=')[1]);
      const parsed = JSON.parse(val);
      if (parsed.start && parsed.end) {
        prefillDates.value = parsed;
        if (props.ruangans?.length > 0) {
          selectedAsset.value = { ...props.ruangans[0], tipe: 'ruangan' };
        }
        showBookingModal.value = true;
      }
    } catch (e) {
      // Ignore malformed cookie
    }
    document.cookie = 'sipinjam_guest_dates=;path=/;max-age=0';
  }
});

// ── Localized Date ────────────────────────────────
const currentDateString = computed(() => {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date());
});

// ── FullCalendar Config ────────────────────────────
const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  events: props.calendarEvents,
  locale: 'id',
  selectable: true,
  select: (info) => {
    prefillDates.value = { start: info.startStr, end: info.endStr };
    showBookingModal.value = true;
  },
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,dayGridWeek',
  },
  height: 'auto',
  dayMaxEvents: 3,
  eventDisplay: 'block',
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  },
}));

// ── Stat Card Definitions ──────────────────────────
const statCards = computed(() => [
  {
    label: 'Total Peminjaman',
    value: props.stats.total,
    icon: ClipboardList,
    color: 'from-blue-500 to-blue-600',
    bgLight: 'bg-blue-50',
    textColor: 'text-blue-600',
  },
  {
    label: 'Sedang Dipinjam',
    value: props.stats.sedang_dipinjam,
    icon: PackageCheck,
    color: 'from-amber-500 to-orange-500',
    bgLight: 'bg-amber-50',
    textColor: 'text-amber-600',
  },
  {
    label: 'Menunggu',
    value: props.stats.menunggu,
    icon: Clock,
    color: 'from-violet-500 to-purple-600',
    bgLight: 'bg-violet-50',
    textColor: 'text-violet-600',
  },
  {
    label: 'Selesai',
    value: props.stats.selesai,
    icon: CheckCircle2,
    color: 'from-emerald-500 to-green-600',
    bgLight: 'bg-emerald-50',
    textColor: 'text-emerald-600',
  },
]);

// ── Adaptive Greeting ──────────────────────────────
const greetingMessage = computed(() => {
  const hour = new Date().getHours();
  if (hour >= 5 && hour < 11) return 'Selamat Pagi';
  if (hour >= 11 && hour < 15) return 'Selamat Siang';
  if (hour >= 15 && hour < 18.5) return 'Selamat Sore';
  return 'Selamat Malam';
});
</script>

<template>
  <Head title="Dashboard" />

  <div class="px-6 py-8 lg:px-10">
    <!-- ── Adaptive Hero Banner Section ──────────────── -->
    <div class="mb-8 overflow-hidden rounded-2xl border border-border shadow-card relative min-h-[260px] flex items-center p-8 bg-slate-900">
      <img src="/image/hero section user.png" alt="User Hero" class="absolute inset-0 w-full h-full object-cover opacity-70" />
      <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/60 to-transparent" />
      
      <div class="relative z-10 max-w-xl text-white drop-shadow-md">
        <p class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2">{{ currentDateString }}</p>
        <h2 class="text-3xl font-extrabold tracking-tight mb-2 drop-shadow-md">
          Selamat Datang, {{ $page.props.auth.user?.name }}!
        </h2>
        <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-widest mb-4">
          Portal Peminjaman Mahasiswa
        </h3>
        <p class="text-sm font-medium leading-relaxed text-slate-200 mb-6 max-w-md drop-shadow-sm">
          Temukan dan pinjam ruangan atau barang untuk kebutuhan kegiatanmu dengan mudah. Pastikan Anda membaca tata tertib peminjaman sebelum mengajukan permohonan.
        </p>
        <button
          @click="openQuickBooking"
          class="inline-flex items-center justify-center bg-primary hover:bg-primary/90 text-primary-foreground font-semibold text-xs rounded-lg px-5 py-3 shadow-md transition-all duration-200 hover:-translate-y-0.5"
        >
          Buat Peminjaman Baru
        </button>
      </div>
    </div>

    <!-- ── Quick Access Cards ────────────────────────── -->
    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
      <button
        @click="openQuickBooking"
        id="btn-quick-booking"
        class="group flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-blue-200 hover:-translate-y-0.5 text-left"
      >
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
          <Plus class="h-6 w-6" />
        </div>
        <div>
          <p class="text-sm font-bold text-foreground">Mulai Pinjam</p>
          <p class="text-xs text-muted-foreground mt-0.5">Ajukan peminjaman barang atau ruangan baru</p>
        </div>
      </button>

      <Link
        href="/bookings"
        id="btn-quick-riwayat"
        class="group flex items-center gap-4 rounded-xl border border-border bg-card p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:border-violet-200 hover:-translate-y-0.5 text-left"
      >
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600 group-hover:bg-violet-100 transition-colors">
          <History class="h-6 w-6" />
        </div>
        <div>
          <p class="text-sm font-bold text-foreground">Riwayat Peminjaman</p>
          <p class="text-xs text-muted-foreground mt-0.5">Lihat status dan histori peminjaman Anda</p>
        </div>
      </Link>
    </div>

    <!-- ── Page Header ──────────────────────────────── -->
    <div class="mb-8 flex items-center justify-between border-b border-slate-100 pb-5">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <Sparkles class="h-5 w-5 text-blue-500" />
          <h1 class="text-xl font-bold tracking-tight text-slate-900">Ringkasan Aktivitas</h1>
        </div>
        <p class="text-xs text-slate-500">
          Kelola dan tinjau status peminjaman aset kampus secara real-time.
        </p>
      </div>
    </div>

    <!-- ── Floating Toast Notifications ─────────────── -->
    <!-- Success Toast -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-x-8"
      enter-to-class="opacity-100 translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-x-0"
      leave-to-class="opacity-0 translate-x-8"
    >
      <div
        v-if="showSuccessToast && flash?.success"
        class="fixed top-6 right-6 z-50 flex items-start gap-3 rounded-xl border border-emerald-200 bg-white px-4 py-3.5 shadow-lg shadow-emerald-500/10 max-w-sm"
      >
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
          <CheckCircle2 class="h-4.5 w-4.5 text-emerald-500" />
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-slate-800">Berhasil</p>
          <p class="text-xs text-slate-500 mt-0.5">{{ flash.success }}</p>
        </div>
        <button @click="showSuccessToast = false" class="shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
          <X class="h-4 w-4" />
        </button>
      </div>
    </Transition>

    <!-- Error Toast -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 translate-x-8"
      enter-to-class="opacity-100 translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-x-0"
      leave-to-class="opacity-0 translate-x-8"
    >
      <div
        v-if="showErrorToast && errorMessages.length"
        class="fixed top-6 right-6 z-50 flex items-start gap-3 rounded-xl border border-red-200 bg-white px-4 py-3.5 shadow-lg shadow-red-500/10 max-w-sm"
      >
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50">
          <AlertCircle class="h-4.5 w-4.5 text-red-500" />
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-red-800">Terjadi Kesalahan</p>
          <ul class="mt-0.5 space-y-0.5">
            <li v-for="(msg, i) in errorMessages" :key="i" class="text-xs text-red-600">
              {{ msg }}
            </li>
          </ul>
        </div>
        <button @click="showErrorToast = false" class="shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
          <X class="h-4 w-4" />
        </button>
      </div>
    </Transition>

    <!-- ── Stat Cards ───────────────────────────────── -->
    <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <StatCard
        v-for="stat in statCards"
        :key="stat.label"
        :label="stat.label"
        :value="stat.value"
        :icon="stat.icon"
        :color="stat.color"
        :bg-light="stat.bgLight"
        :text-color="stat.textColor"
      />
    </div>

    <!-- ── Interactive Calendar ──────────────────────── -->
    <div class="mb-10">
      <div class="flex items-center gap-2 mb-4">
        <CalendarDays class="h-5 w-5 text-blue-500" />
        <h2 class="text-lg font-bold text-slate-900">Jadwal Peminjaman</h2>
      </div>
      <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
        <FullCalendar :options="calendarOptions" />
      </div>
      <div class="mt-3 flex items-center gap-5">
        <div class="flex items-center gap-2">
          <span class="inline-block h-3 w-3 rounded-sm" style="background-color:#2563eb" />
          <span class="text-xs text-muted-foreground">Ruangan</span>
        </div>
        <div class="flex items-center gap-2">
          <span class="inline-block h-3 w-3 rounded-sm" style="background-color:#64748b" />
          <span class="text-xs text-muted-foreground">Barang</span>
        </div>
      </div>
    </div>

    <!-- ── Asset Catalog ────────────────────────────── -->
    <div class="space-y-6">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-lg font-bold text-slate-900">Katalog Aset Kampus</h2>
          <p class="text-xs text-slate-500">Klik pada kartu untuk memulai peminjaman</p>
        </div>

        <!-- Search -->
        <div class="relative w-full sm:w-72">
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
          <Input
            v-model="searchQuery"
            placeholder="Cari aset..."
            class="pl-10 bg-white"
          />
        </div>
      </div>

      <Tabs default-value="ruangan" class="w-full">
        <TabsList class="w-full sm:w-auto bg-slate-100 p-1 rounded-xl">
          <TabsTrigger
            value="ruangan"
            class="gap-1.5 data-[state=active]:bg-white data-[state=active]:shadow-sm rounded-lg px-5"
          >
            <Building2 class="h-4 w-4" />
            Ruangan
            <Badge variant="secondary" class="ml-1 text-[10px] px-1.5 py-0">
              {{ filteredRuangans.length }}
            </Badge>
          </TabsTrigger>
          <TabsTrigger
            value="barang"
            class="gap-1.5 data-[state=active]:bg-white data-[state=active]:shadow-sm rounded-lg px-5"
          >
            <Package class="h-4 w-4" />
            Barang
            <Badge variant="secondary" class="ml-1 text-[10px] px-1.5 py-0">
              {{ filteredBarangs.length }}
            </Badge>
          </TabsTrigger>
        </TabsList>

        <!-- ── Tab: Ruangan ─────────────────────────── -->
        <TabsContent value="ruangan" class="mt-6">
          <div
            v-if="filteredRuangans.length"
            class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
          >
            <Card
              v-for="ruangan in filteredRuangans"
              :key="ruangan.id"
              @click="openBooking(ruangan, 'ruangan')"
              class="group cursor-pointer overflow-hidden border-slate-200/80 transition-all duration-200 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5"
            >
              <CardContent class="p-0">
                <div class="p-5 space-y-3">
                  <!-- Header -->
                  <div class="flex items-start justify-between">
                    <Badge variant="outline" class="text-[10px] font-bold tracking-wider uppercase text-slate-500 border-slate-200">
                      {{ ruangan.kode }}
                    </Badge>
                    <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] hover:bg-emerald-50">
                      <span class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500 inline-block" />
                      Tersedia
                    </Badge>
                  </div>

                  <!-- Name & Location -->
                  <div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                      {{ ruangan.nama }}
                    </h3>
                    <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                      <MapPin class="h-3.5 w-3.5 text-slate-400" />
                      {{ ruangan.lokasi || 'Kampus STITEK' }}
                    </div>
                  </div>

                  <!-- Description -->
                  <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                    {{ ruangan.deskripsi || 'Ruangan kampus siap digunakan.' }}
                  </p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-5 py-3">
                  <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                    <Users class="h-4 w-4 text-slate-400" />
                    {{ ruangan.kapasitas }} Orang
                  </div>
                  <span class="text-xs font-bold text-blue-500 opacity-0 transition-opacity group-hover:opacity-100">
                    Pinjam →
                  </span>
                </div>
              </CardContent>
            </Card>
          </div>
          <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
            <Building2 class="mx-auto h-10 w-10 text-slate-300" />
            <p class="mt-3 text-sm text-slate-400">Tidak ada ruangan ditemukan</p>
          </div>
        </TabsContent>

        <!-- ── Tab: Barang ──────────────────────────── -->
        <TabsContent value="barang" class="mt-6">
          <div
            v-if="filteredBarangs.length"
            class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
          >
            <Card
              v-for="barang in filteredBarangs"
              :key="barang.id"
              @click="openBooking(barang, 'barang')"
              class="group cursor-pointer overflow-hidden border-slate-200/80 transition-all duration-200 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5"
            >
              <CardContent class="p-0">
                <div class="p-5 space-y-3">
                  <!-- Header -->
                  <div class="flex items-start justify-between">
                    <Badge variant="outline" class="text-[10px] font-bold tracking-wider uppercase text-slate-500 border-slate-200">
                      {{ barang.kode }}
                    </Badge>
                    <div class="flex items-center gap-1.5">
                      <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] hover:bg-emerald-50">
                        Tersedia: {{ barang.stok_tersedia }}
                      </Badge>
                      <Badge
                        v-if="barang.sedang_dipinjam > 0"
                        class="bg-amber-50 text-amber-700 border-amber-200 text-[10px] hover:bg-amber-50"
                      >
                        Dipinjam: {{ barang.sedang_dipinjam }}
                      </Badge>
                    </div>
                  </div>

                  <!-- Name & Category -->
                  <div>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                      {{ barang.nama }}
                    </h3>
                    <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                      <Layers class="h-3.5 w-3.5 text-slate-400" />
                      {{ barang.kategori || 'Inventaris' }}
                    </div>
                  </div>

                  <!-- Description -->
                  <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                    {{ barang.deskripsi || 'Barang inventaris kampus.' }}
                  </p>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-5 py-3">
                  <div class="text-xs text-slate-500">
                    Total Stok: <span class="font-semibold text-slate-700">{{ barang.stok_total }}</span>
                  </div>
                  <span class="text-xs font-bold text-blue-500 opacity-0 transition-opacity group-hover:opacity-100">
                    Pinjam →
                  </span>
                </div>
              </CardContent>
            </Card>
          </div>
          <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
            <Package class="mx-auto h-10 w-10 text-slate-300" />
            <p class="mt-3 text-sm text-slate-400">Tidak ada barang ditemukan</p>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </div>

  <!-- ── Booking Modal ──────────────────────────────── -->
  <BookingModal
    v-model:open="showBookingModal"
    :asset="selectedAsset"
    :prefill-dates="prefillDates"
    :ruangans="ruangans"
    :barangs="barangs"
  />
</template>
