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
  FileText,
  MessageCircle,
  ShieldCheck,
  BookOpen,
  Smartphone,
  Download,
} from '@lucide/vue';
import { isPwaInstallable, promptPwaInstall } from '@/pwa';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

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

// ── Himbauan Pasca-Pengajuan ─────────────────────────
const adminWaNumber = import.meta.env.VITE_ADMIN_WA_NUMBER || '628123456789';
const newBookingId = ref(null);
const showHimbauanModal = ref(false);
const waLink = computed(() =>
  `https://wa.me/${adminWaNumber}?text=Halo%20Admin%2C%20saya%20sudah%20mengajukan%20peminjaman%20%23${newBookingId.value}%20dan%20ingin%20konfirmasi.`
);

watch(() => flash.value?.booking_created_id, (id) => {
  if (id) {
    newBookingId.value = id;
    showHimbauanModal.value = true;
  }
}, { immediate: true });

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

// ── Tata Tertib Awareness Modal State ────────────────
const showTataTertibModal = ref(false);
const dontShowAgain = ref(false);

const ackTataTertib = () => {
  if (dontShowAgain.value) {
    localStorage.setItem('sipinjam_tatatertib_ack', 'true');
  }
  showTataTertibModal.value = false;
};

const openTataTertibModal = () => {
  showTataTertibModal.value = true;
};

// ── PWA Install Banner State ────────────────────────
const isPwaBannerDismissed = ref(false);

const handlePwaInstall = async () => {
  const installed = await promptPwaInstall();
  if (installed) {
    isPwaBannerDismissed.value = true;
  }
};

const dismissPwaBanner = () => {
  isPwaBannerDismissed.value = true;
  try {
    sessionStorage.setItem('sipinjam_pwa_dismissed', 'true');
  } catch (e) {}
};

// ── Guest Cookie Detection (from Landing page drag) ─
onMounted(() => {
  try {
    if (sessionStorage.getItem('sipinjam_pwa_dismissed') === 'true') {
      isPwaBannerDismissed.value = true;
    }
  } catch (e) {}

  const tatatertibAck = localStorage.getItem('sipinjam_tatatertib_ack');
  if (!tatatertibAck) {
    showTataTertibModal.value = true;
  }

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

// ── Live Real-time Clock ──────────────────────────
const currentLiveTime = ref(new Date());
let liveTimeTimer = null;

onMounted(() => {
  liveTimeTimer = setInterval(() => {
    currentLiveTime.value = new Date();
  }, 1000);
});

onUnmounted(() => {
  if (liveTimeTimer) clearInterval(liveTimeTimer);
});

const currentLiveDateTimeString = computed(() => {
  const d = currentLiveTime.value;
  const datePart = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(d);
  const timePart = d.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false
  });
  return `${datePart} • ${timePart} WITA`;
});

// ── Interactive Calendar Event Click Modal ────────
const selectedCalendarEvent = ref(null);
const showEventDetailsModal = ref(false);

const handleEventClick = (info) => {
  selectedCalendarEvent.value = {
    title: info.event.title,
    start: info.event.startStr,
    end: info.event.endStr,
    ...info.event.extendedProps,
  };
  showEventDetailsModal.value = true;
};

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
  eventClick: handleEventClick,
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

const getImageUrl = (path) => {
  if (!path) return '/image/logo.png';
  if (path.startsWith('http')) return path;
  if (path.startsWith('/storage/') || path.startsWith('storage/')) {
    return path.startsWith('/') ? path : '/' + path;
  }
  return '/storage/' + path;
};
</script>

<template>
  <Head title="Dashboard" />

  <div class="px-6 py-8 lg:px-10">
    <!-- ── Adaptive Hero Banner Section ──────────────── -->
    <div class="mb-8 overflow-hidden rounded-2xl border border-border shadow-card relative min-h-[260px] flex items-center p-8 bg-slate-900">
      <img src="/image/hero section user.png" alt="User Hero" class="absolute inset-0 w-full h-full object-cover opacity-70" />
      <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/60 to-transparent" />
      
      <div class="relative z-10 max-w-xl text-white drop-shadow-md">
        <div class="inline-flex items-center gap-2 bg-blue-500/20 border border-blue-400/30 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wider text-blue-300 mb-3 backdrop-blur-xs font-mono">
          <Clock class="h-3.5 w-3.5 animate-pulse text-blue-400" />
          <span>{{ currentLiveDateTimeString }}</span>
        </div>
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

    <!-- ── PWA Install Suggestion Banner ─────────────── -->
    <div
      v-if="isPwaInstallable && !isPwaBannerDismissed"
      class="mb-8 overflow-hidden rounded-2xl border border-indigo-200/80 bg-gradient-to-r from-indigo-50/90 via-blue-50/60 to-white p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
    >
      <div class="flex items-start gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
          <Smartphone class="h-6 w-6" />
        </div>
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Pasang Aplikasi SIPINJAM</h3>
            <Badge class="bg-indigo-100 text-indigo-800 border-indigo-200 text-[10px]">Akses Cepat PWA</Badge>
          </div>
          <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
            Pasang SIPINJAM langsung di layar beranda perangkat Anda untuk pengalaman native app tanpa perlu membuka browser.
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
        <button
          @click="dismissPwaBanner"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors shadow-2xs cursor-pointer"
        >
          Nanti Saja
        </button>
        <button
          @click="handlePwaInstall"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-4 py-2 text-xs font-semibold text-white transition-colors shadow-xs cursor-pointer"
        >
          <Download class="h-3.5 w-3.5" /> Pasang Sekarang
        </button>
      </div>
    </div>

    <!-- ── Tata Tertib Awareness Suggestion Banner ───── -->
    <div class="mb-8 overflow-hidden rounded-2xl border border-blue-200/80 bg-gradient-to-r from-blue-50/90 via-indigo-50/60 to-white p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-start gap-3.5">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
          <ShieldCheck class="h-6 w-6" />
        </div>
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Patuhi Tata Tertib Peminjaman Kampus</h3>
            <Badge class="bg-blue-100 text-blue-800 border-blue-200 text-[10px] hidden sm:inline-flex">Wajib Dipahami</Badge>
          </div>
          <p class="text-xs text-slate-600 max-w-2xl leading-relaxed">
            Seluruh peminjam wajib menjaga keutuhan aset, mengembalikan tepat waktu, dan mematuhi batas operasional (07:00 - 22:00 WITA) demi kenyamanan bersama.
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
        <button
          @click="openTataTertibModal"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl border border-blue-200 bg-white px-3.5 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition-colors shadow-2xs cursor-pointer"
        >
          <BookOpen class="h-3.5 w-3.5 text-blue-600" /> Ringkasan
        </button>
        <Link
          href="/tata_tertib"
          class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-xs font-semibold text-white transition-colors shadow-xs"
        >
          Lihat Tata Tertib Lengkap →
        </Link>
      </div>
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
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
        <div class="flex items-center gap-2">
          <CalendarDays class="h-5 w-5 text-blue-500" />
          <h2 class="text-lg font-bold text-slate-900">Jadwal Peminjaman Aktif</h2>
        </div>
        <!-- Legend with shadcn / Lucide Icons -->
        <div class="flex items-center gap-3">
          <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md text-xs font-semibold">
            <Building2 class="h-3.5 w-3.5 text-blue-600" />
            <span>Peminjaman Ruangan</span>
          </div>
          <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-md text-xs font-semibold">
            <Package class="h-3.5 w-3.5 text-amber-600" />
            <span>Peminjaman Barang</span>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-border bg-card p-4 sm:p-6 shadow-xs clean-calendar">
        <FullCalendar :options="calendarOptions" />
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
                <!-- Image Banner -->
                <div class="relative w-full h-48 overflow-hidden bg-slate-100 border-b border-slate-200 rounded-t-md">
                  <img
                    :src="ruangan.image_path ? getImageUrl(ruangan.image_path) : '/image/logo.png'"
                    alt="Foto Ruangan"
                    class="w-full h-48 object-cover rounded-t-md transition-transform duration-300 group-hover:scale-105"
                  />
                </div>
                <div class="p-5 space-y-3">
                  <!-- Header -->
                  <div class="flex items-start justify-between">
                    <Badge variant="outline" class="text-[10px] font-bold tracking-wider uppercase text-slate-500 border-slate-200">
                      {{ ruangan.kode }}
                    </Badge>
                    <Badge v-if="ruangan.is_terpakai" class="bg-rose-50 text-rose-700 border-rose-200 text-[10px] hover:bg-rose-50">
                      <span class="mr-1 h-1.5 w-1.5 rounded-full bg-rose-500 inline-block animate-pulse" />
                      Sedang Terpakai
                    </Badge>
                    <Badge v-else class="bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] hover:bg-emerald-50">
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
                <!-- Image Banner -->
                <div class="relative w-full h-48 overflow-hidden bg-slate-100 border-b border-slate-200 rounded-t-md">
                  <img
                    :src="barang.image_path ? getImageUrl(barang.image_path) : '/image/logo.png'"
                    alt="Foto Barang"
                    class="w-full h-48 object-cover rounded-t-md transition-transform duration-300 group-hover:scale-105"
                  />
                </div>
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

  <!-- ── Himbauan Pasca-Pengajuan ──────────────────────── -->
  <Dialog :open="showHimbauanModal" @update:open="showHimbauanModal = $event">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <div class="flex items-center gap-3 mb-1">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <CheckCircle2 class="h-5 w-5" />
          </div>
          <DialogTitle class="text-base font-bold text-slate-900">Peminjaman Berhasil Diajukan</DialogTitle>
        </div>
        <DialogDescription class="text-sm text-slate-600 space-y-2 pt-2">
          <p>Langkah selanjutnya:</p>
          <ol class="list-decimal list-inside space-y-1 text-slate-700">
            <li>Unduh surat peminjaman di bawah ini.</li>
            <li>Tanda tangani surat tersebut.</li>
            <li>Hubungi admin setelah surat ditandatangani, dan sekali lagi setelah aset dikembalikan.</li>
          </ol>
          <p class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-xs text-amber-800">
            Bila tidak ada konfirmasi ke admin dalam <strong>7 hari</strong>, peminjaman akan otomatis ditolak sistem.
          </p>
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex-col gap-2 sm:flex-col">
        <a
          :href="`/bookings/${newBookingId}/pdf`"
          target="_blank"
          class="inline-flex w-full h-9 items-center justify-center gap-2 bg-primary text-primary-foreground rounded-lg text-sm font-semibold shadow-sm hover:opacity-90 transition-all"
        >
          <FileText class="h-4 w-4" /> Unduh Surat Peminjaman
        </a>
        <a
          :href="waLink"
          target="_blank"
          class="inline-flex w-full h-9 items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-colors"
        >
          <MessageCircle class="h-4 w-4" /> Hubungi Admin via WhatsApp
        </a>
        <Button variant="ghost" size="sm" class="w-full text-slate-500" @click="showHimbauanModal = false">
          Tutup
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- ── Interactive Calendar Event Details Modal ─────── -->
  <Dialog :open="showEventDetailsModal" @update:open="showEventDetailsModal = $event">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <div class="flex items-center gap-3 mb-1">
          <div
            :class="[
              'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border',
              selectedCalendarEvent?.tipe === 'ruangan'
                ? 'bg-blue-50 text-blue-600 border-blue-200'
                : 'bg-amber-50 text-amber-600 border-amber-200',
            ]"
          >
            <component
              :is="selectedCalendarEvent?.tipe === 'ruangan' ? Building2 : Package"
              class="h-5 w-5"
            />
          </div>
          <div>
            <DialogTitle class="text-base font-bold text-slate-900">
              {{ selectedCalendarEvent?.nama || selectedCalendarEvent?.title || 'Detail Peminjaman' }}
            </DialogTitle>
            <Badge
              variant="outline"
              class="mt-1 text-[10px] uppercase font-bold tracking-wider"
              :class="selectedCalendarEvent?.tipe === 'ruangan' ? 'text-blue-600' : 'text-amber-600'"
            >
              Peminjaman {{ selectedCalendarEvent?.tipe === 'ruangan' ? 'Ruangan' : 'Barang' }} Aktif
            </Badge>
          </div>
        </div>
        <DialogDescription class="text-xs text-slate-500 pt-2 space-y-3">
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-medium text-slate-500">Rentang Tanggal:</span>
              <span class="font-semibold text-slate-800">{{ selectedCalendarEvent?.start }} — {{ selectedCalendarEvent?.end }}</span>
            </div>
            <div v-if="selectedCalendarEvent?.jam_mulai" class="flex items-center justify-between">
              <span class="font-medium text-slate-500">Jam Operasional:</span>
              <span class="font-semibold text-slate-800">{{ selectedCalendarEvent?.jam_mulai }} - {{ selectedCalendarEvent?.jam_selesai }} WITA</span>
            </div>
            <div v-if="selectedCalendarEvent?.keterangan" class="pt-1 border-t border-slate-200">
              <span class="font-medium text-slate-500 block mb-0.5">Keperluan:</span>
              <p class="text-slate-700 italic">{{ selectedCalendarEvent?.keterangan }}</p>
            </div>
          </div>
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="mt-2">
        <Button size="sm" class="w-full bg-primary text-primary-foreground hover:opacity-90" @click="showEventDetailsModal = false">
          Tutup Detail
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- ── Tata Tertib Pop-Up Awareness Modal ───────────── -->
  <Dialog :open="showTataTertibModal" @update:open="showTataTertibModal = $event">
    <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <div class="flex items-center gap-3 mb-1">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
            <ShieldCheck class="h-6 w-6" />
          </div>
          <div>
            <DialogTitle class="text-base font-bold text-slate-900">
              Tata Tertib Peminjaman Sarpras
            </DialogTitle>
            <DialogDescription class="text-xs text-slate-500">
              Pedoman peminjaman ruangan dan barang STITEK Bontang
            </DialogDescription>
          </div>
        </div>
      </DialogHeader>

      <div class="space-y-3.5 text-xs text-slate-600 pt-2">
        <p class="text-slate-700 font-medium">
          Demi kelancaran kegiatan bersama dan pemeliharaan fasilitas kampus, mohon perhatikan poin penting berikut:
        </p>

        <div class="space-y-2.5">
          <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-[10px]">1</div>
            <div>
              <p class="font-bold text-slate-800">Pengajuan & Persetujuan</p>
              <p class="text-[11px] text-slate-500 mt-0.5">Pengajuan wajib diajukan minimal H-1 sebelum kegiatan dan menunggu persetujuan admin sebelum sarpras digunakan.</p>
            </div>
          </div>

          <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-[10px]">2</div>
            <div>
              <p class="font-bold text-slate-800">Jam Operasional & Pengembalian</p>
              <p class="text-[11px] text-slate-500 mt-0.5">Penggunaan fasilitas berlaku pukul 07:00 - 22:00 WITA. Pengembalian wajib tepat waktu sesuai permohonan.</p>
            </div>
          </div>

          <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-[10px]">3</div>
            <div>
              <p class="font-bold text-slate-800">Kebersihan & Keutuhan Aset</p>
              <p class="text-[11px] text-slate-500 mt-0.5">Peminjam wajib menjaga kebersihan ruangan, mematikan AC/lampu setelah selesai, dan menjaga kondisi barang.</p>
            </div>
          </div>

          <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
            <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-[10px]">4</div>
            <div>
              <p class="font-bold text-slate-800">Sanksi Pelanggaran</p>
              <p class="text-[11px] text-slate-500 mt-0.5">Keterlambatan, kerusakan, atau kehilangan dikenakan sanksi ganti rugi hingga pembatasan izin peminjaman akun.</p>
            </div>
          </div>
        </div>

        <div class="pt-2 flex items-center justify-between border-t border-slate-100">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input v-model="dontShowAgain" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" />
            <span class="text-xs text-slate-600 font-medium">Jangan tampilkan popup ini otomatis lagi</span>
          </label>
        </div>
      </div>

      <DialogFooter class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 mt-4 pt-3 border-t border-slate-100">
        <Link
          href="/tata_tertib"
          class="inline-flex items-center justify-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 py-2"
          @click="showTataTertibModal = false"
        >
          <BookOpen class="h-3.5 w-3.5" /> Baca Tata Tertib Lengkap
        </Link>
        <Button
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-xs"
          @click="ackTataTertib"
        >
          Saya Telah Memahami
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
/* ── Clean FullCalendar Button & Layout Overrides ──────────────────── */
.clean-calendar :deep(.fc) { font-family: 'Inter', sans-serif; }
.clean-calendar :deep(.fc-toolbar-title) { font-size: 1.1rem !important; font-weight: 700 !important; color: #0f172a !important; }
.clean-calendar :deep(.fc-button) {
  background: hsl(var(--primary)) !important;
  border: 1px solid transparent !important;
  border-radius: 0.5rem !important;
  color: hsl(var(--primary-foreground)) !important;
  font-weight: 600 !important;
  box-shadow: 0 1px 2px rgba(0,0,0,.05) !important;
  padding: 6px 14px !important;
  transition: all 0.2s !important;
  font-size: 0.85rem !important;
  text-transform: capitalize !important;
}
.clean-calendar :deep(.fc-button:hover) { opacity: 0.9 !important; }
.clean-calendar :deep(.fc-button-active) { background: hsl(var(--primary)) !important; opacity: 0.85 !important; }
.clean-calendar :deep(.fc-daygrid-day) { border: 1px solid hsl(var(--border)) !important; }
.clean-calendar :deep(.fc-col-header-cell) {
  background: hsl(var(--muted)) !important;
  color: hsl(var(--muted-foreground)) !important;
  font-weight: 600 !important;
  font-size: 0.8rem !important;
  border: 1px solid hsl(var(--border)) !important;
  padding: 8px 0 !important;
}
.clean-calendar :deep(.fc-day-today) { background: hsl(var(--primary) / 0.06) !important; }
.clean-calendar :deep(.fc-highlight) { background: hsl(var(--primary) / 0.12) !important; }
.clean-calendar :deep(.fc-daygrid-day-number) { font-weight: 600 !important; font-size: 0.85rem; padding: 6px 8px !important; color: #334155 !important; }
.clean-calendar :deep(.fc-scrollgrid) { border: 1px solid hsl(var(--border)) !important; border-radius: 0.5rem !important; overflow: hidden; }
.clean-calendar :deep(th), .clean-calendar :deep(td) { border-color: hsl(var(--border)) !important; }
.clean-calendar :deep(.fc-event) { border-radius: 4px !important; padding: 1px 4px !important; font-size: 0.75rem !important; font-weight: 600 !important; }
</style>
