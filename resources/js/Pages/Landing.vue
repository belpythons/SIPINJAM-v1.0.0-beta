<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import {
  Building2, Package, Search, MapPin, Users,
  ArrowRight, Sparkles, Layers, ChevronRight,
  Info, LogIn, GraduationCap, CalendarDays, X,
  Clock, ShieldCheck,
} from '@lucide/vue';
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
  ruangans: Array,
  barangs: Array,
  banners: Array,
  calendarEvents: Array,
});

const searchQuery = ref('');
const activeTab = ref('all');
const showLoginDialog = ref(false);
const guestDateRange = ref(null);

// ── Hero Background Carousel ────────────────────────
const carouselImages = computed(() => {
  if (props.banners && props.banners.length > 0) {
    return props.banners.map(b => b.image_path);
  }
  return ['/image/bg blur admin.png', '/image/bg blur user.png'];
});
const activeSlide = ref(0);
let carouselTimer = null;

onMounted(() => {
  carouselTimer = setInterval(() => {
    activeSlide.value = (activeSlide.value + 1) % carouselImages.value.length;
  }, 5000);
});

onUnmounted(() => {
  if (carouselTimer) clearInterval(carouselTimer);
});

// ── Interactive Calendar Event Click Modal ──────────
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

// ── FullCalendar Config ─────────────────────────────
const calendarOptions = computed(() => ({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  events: props.calendarEvents,
  selectable: true,
  editable: false,
  headerToolbar: { left: 'prev', center: 'title', right: 'next' },
  height: 'auto',
  locale: 'id',
  select: handleDateSelect,
  eventClick: handleEventClick,
  validRange: { start: new Date().toISOString().split('T')[0] },
  dayHeaderFormat: { weekday: 'short' },
  slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
}));

function handleDateSelect(selectInfo) {
  guestDateRange.value = { start: selectInfo.startStr, end: selectInfo.endStr };
  const cookieVal = JSON.stringify(guestDateRange.value);
  document.cookie = `sipinjam_guest_dates=${encodeURIComponent(cookieVal)};path=/;max-age=3600;SameSite=Lax`;
  showLoginDialog.value = true;
}

const handlePinjam = () => { showLoginDialog.value = true; };
const confirmLogin = () => {
  showEventDetailsModal.value = false;
  showLoginDialog.value = false;
  router.visit('/login');
};
const closeDialog = () => { showLoginDialog.value = false; };

const filteredRuangans = computed(() =>
  props.ruangans.filter(r =>
    r.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    r.lokasi.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
);
const filteredBarangs = computed(() =>
  props.barangs.filter(b =>
    b.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    b.kategori.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
);
</script>

<template>
  <Head title="Katalog Aset Kampus - SIPINJAM" />

  <div class="min-h-screen bg-background text-foreground font-sans antialiased overflow-x-hidden">
    <!-- ── Header / Hero with Auto-Playing Carousel & Framer Motion Physics ── -->
    <header class="relative py-20 px-6 sm:px-12 overflow-hidden border-b border-border min-h-[460px] flex items-center bg-slate-950">
      <!-- Background Carousel Images -->
      <img
        v-for="(img, idx) in carouselImages"
        :key="img"
        :src="img"
        alt="Background"
        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out scale-105"
        :class="activeSlide === idx ? 'opacity-100' : 'opacity-0'"
      />
      <!-- Dark Overlay -->
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/80 to-slate-900/60" />

      <!-- Floating Animated Orbs -->
      <div class="absolute -top-12 -left-12 w-64 h-64 rounded-full bg-blue-600/20 blur-3xl animate-float-slow pointer-events-none" />
      <div class="absolute -bottom-16 right-20 w-80 h-80 rounded-full bg-indigo-600/20 blur-3xl animate-float-delayed pointer-events-none" />

      <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-12 w-full">
        <!-- Hero Text (Staggered Animation) -->
        <div class="max-w-2xl space-y-6 animate-fade-up">
          <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur-md rounded-full text-blue-200 px-4 py-1.5 text-xs font-semibold tracking-wider uppercase shadow-xs">
            <Sparkles class="w-3.5 h-3.5 text-blue-400 animate-spin-slow" />
            Sistem Peminjaman Aset Kampus Modern
          </div>

          <div class="space-y-2">
            <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight">
              SiPinjam
            </h1>
            <p class="text-xs sm:text-sm tracking-widest uppercase text-blue-200 font-semibold">
              STITEK Bontang — The Knowledgeable and Virtue Campus
            </p>
          </div>

          <p class="text-base sm:text-lg text-slate-200 font-normal leading-relaxed max-w-xl">
            Layanan peminjaman barang dan ruangan perkuliahan secara praktis, terintegrasi, dan terpantau dalam satu platform digital yang modern.
          </p>

          <div class="flex flex-wrap gap-3 pt-2">
            <button
              @click="handlePinjam"
              id="btn-mulai-pinjam"
              class="inline-flex items-center gap-2 bg-primary text-primary-foreground font-semibold text-sm rounded-lg px-6 py-3.5 shadow-md transition-all duration-200 hover:bg-primary/90 hover:shadow-lg hover:-translate-y-0.5 cursor-pointer focus:ring-2 focus:ring-ring focus:ring-offset-2"
            >
              Mulai Peminjaman
              <ArrowRight class="w-4 h-4" />
            </button>
            <a
              href="#katalog"
              class="inline-flex items-center justify-center bg-white/10 text-white backdrop-blur-md border border-white/20 hover:bg-white/20 font-semibold text-sm rounded-lg px-6 py-3.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 cursor-pointer"
            >
              Lihat Katalog Aset
            </a>
          </div>
        </div>

        <!-- Stat Widget (Clean Minimalist Glass Card) -->
        <div class="w-full md:w-80 bg-slate-900/80 backdrop-blur-md border border-white/15 rounded-2xl shadow-card p-6 text-white animate-fade-up-delayed">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold uppercase tracking-widest text-slate-400">Status Operasional</span>
              <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border border-white/80" />
              </span>
            </div>
            <div class="h-px bg-white/10" />
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-white/5 border border-white/10 rounded-xl p-3 shadow-none">
                <p class="text-3xl font-bold text-white">{{ ruangans.length }}</p>
                <p class="text-xs font-medium text-slate-300">Total Ruangan</p>
              </div>
              <div class="bg-white/5 border border-white/10 rounded-xl p-3 shadow-none">
                <p class="text-3xl font-bold text-white">{{ barangs.length }}</p>
                <p class="text-xs font-medium text-slate-300">Total Barang</p>
              </div>
            </div>
          </div>
          <div class="mt-5 flex items-center justify-between text-[10px] font-semibold text-slate-400 pt-4 border-t border-white/10 uppercase tracking-widest">
            <span class="flex items-center gap-1"><ShieldCheck class="w-3.5 h-3.5 text-emerald-400" /> Akses Terverifikasi</span>
            <span>STITEK</span>
          </div>
        </div>
      </div>

      <!-- Carousel Dots Indicator -->
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        <button
          v-for="(img, idx) in carouselImages"
          :key="'dot-' + idx"
          @click="activeSlide = idx"
          :class="[
            'w-2.5 h-2.5 rounded-full border border-white/50 transition-all duration-200 cursor-pointer',
            activeSlide === idx ? 'bg-white scale-125' : 'bg-white/40 hover:bg-white/70',
          ]"
        />
      </div>
    </header>

    <!-- ── FullCalendar Section ────────────────────── -->
    <section class="max-w-7xl mx-auto py-12 px-6 sm:px-12">
      <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
            <CalendarDays class="w-5 h-5 text-primary" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-foreground">Jadwal & Ketersediaan Aset</h2>
            <p class="text-xs text-muted-foreground">Klik item jadwal untuk melihat detail atau drag tanggal untuk reservasi</p>
          </div>
        </div>

        <!-- Legend with shadcn / Lucide Icons -->
        <div class="flex items-center gap-3">
          <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md text-xs font-semibold">
            <Building2 class="h-3.5 w-3.5 text-blue-600" />
            <span>Ruangan (Kampus)</span>
          </div>
          <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-md text-xs font-semibold">
            <Package class="h-3.5 w-3.5 text-amber-600" />
            <span>Barang / Alat</span>
          </div>
        </div>
      </div>

      <div class="bg-card border border-border rounded-xl shadow-xs p-4 sm:p-6 clean-calendar">
        <FullCalendar :options="calendarOptions" />
      </div>
    </section>

    <!-- ── Catalog ─────────────────────────────────── -->
    <main id="katalog" class="max-w-7xl mx-auto py-12 px-6 sm:px-12 space-y-10">
      <!-- Filter + Search -->
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-card border border-border rounded-xl p-4 shadow-xs">
        <div class="flex gap-1 w-full md:w-auto bg-muted rounded-lg p-1">
          <button v-for="tab in [{key:'all',label:'Semua'},{key:'ruangan',label:'Ruangan'},{key:'barang',label:'Barang'}]"
            :key="tab.key" @click="activeTab = tab.key"
            :class="['flex-1 md:flex-none px-5 py-2 text-xs font-semibold rounded-md transition-all duration-200 cursor-pointer',
              activeTab === tab.key ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground']">
            {{ tab.label }}
          </button>
        </div>
        <div class="relative w-full md:w-80">
          <Search class="absolute inset-y-0 left-3 my-auto h-4 w-4 text-muted-foreground" />
          <input v-model="searchQuery" type="text" placeholder="Cari nama atau lokasi aset..."
            class="w-full pl-10 pr-4 py-2.5 border border-input bg-background text-sm rounded-lg placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/20 focus:border-ring transition-all" />
        </div>
      </div>

      <!-- Ruangan -->
      <section v-if="activeTab === 'all' || activeTab === 'ruangan'" class="space-y-6">
        <div class="flex items-center gap-3 pb-3 border-b border-border">
          <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
            <Building2 class="w-4 h-4 text-primary" />
          </div>
          <div>
            <h2 class="text-lg font-bold text-foreground">Ruangan Gedung Utama & Djuanda</h2>
            <p class="text-xs text-muted-foreground">Ruang kelas teori dan laboratorium praktikum</p>
          </div>
        </div>
        <div v-if="filteredRuangans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="ruangan in filteredRuangans" :key="ruangan.id"
            class="group bg-card border border-border rounded-xl shadow-xs overflow-hidden flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <!-- Image at top -->
            <div class="aspect-video w-full overflow-hidden bg-slate-50 border-b border-border relative">
              <img v-if="ruangan.image_path" :src="ruangan.image_path" :alt="ruangan.nama" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                <Building2 class="h-8 w-8" />
              </div>
            </div>
            <div class="p-5 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <span class="text-[10px] font-semibold tracking-wider uppercase bg-muted text-muted-foreground px-2.5 py-1 rounded-md">{{ ruangan.kode }}</span>
                <span v-if="ruangan.is_terpakai" class="inline-flex items-center gap-1.5 text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-md">
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse" />Sedang Terpakai
                </span>
                <span v-else class="inline-flex items-center gap-1.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" />Tersedia
                </span>
              </div>
              <div class="space-y-1">
                <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors">{{ ruangan.nama }}</h3>
                <div class="flex items-center gap-1.5 text-xs text-muted-foreground"><MapPin class="w-3.5 h-3.5" />{{ ruangan.lokasi }}</div>
              </div>
              <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed">{{ ruangan.deskripsi || 'Tidak ada deskripsi tambahan.' }}</p>
            </div>
            <div class="px-5 py-3.5 bg-muted/40 border-t border-border flex items-center justify-between">
              <div class="flex items-center gap-1.5 text-xs font-medium text-muted-foreground"><Users class="w-4 h-4" />{{ ruangan.kapasitas }} Orang</div>
              <button @click="handlePinjam" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-0.5 cursor-pointer">
                Pinjam <ChevronRight class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-16 bg-card border border-border rounded-xl text-muted-foreground text-sm">Tidak ada ruangan yang cocok.</div>
      </section>

      <!-- Barang -->
      <section v-if="activeTab === 'all' || activeTab === 'barang'" class="space-y-6">
        <div class="flex items-center gap-3 pb-3 border-b border-border">
          <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
            <Package class="w-4 h-4 text-amber-600" />
          </div>
          <div>
            <h2 class="text-lg font-bold text-foreground">Barang & Inventaris Peminjaman</h2>
            <p class="text-xs text-muted-foreground">Perangkat elektronik, audio, kabel, dan pendukung perkuliahan</p>
          </div>
        </div>
        <div v-if="filteredBarangs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <div v-for="barang in filteredBarangs" :key="barang.id"
            class="group bg-card border border-border rounded-xl shadow-xs overflow-hidden flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <!-- Image at top -->
            <div class="aspect-video w-full overflow-hidden bg-slate-50 border-b border-border relative">
              <img v-if="barang.image_path" :src="barang.image_path" :alt="barang.nama" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                <Package class="h-8 w-8" />
              </div>
            </div>
            <div class="p-5 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <span class="text-[10px] font-semibold tracking-wider uppercase bg-muted text-muted-foreground px-2.5 py-1 rounded-md">{{ barang.kode }}</span>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md">Stok: {{ barang.stok_tersedia }}</span>
              </div>
              <div class="space-y-1">
                <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors">{{ barang.nama }}</h3>
                <div class="flex items-center gap-1.5 text-xs text-muted-foreground"><Layers class="w-3.5 h-3.5" />Kategori: {{ barang.kategori || 'Inventaris' }}</div>
              </div>
              <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed">{{ barang.deskripsi || 'Tidak ada deskripsi tambahan.' }}</p>
            </div>
            <div class="px-5 py-3.5 bg-muted/40 border-t border-border flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-muted-foreground"><Info class="w-3.5 h-3.5" />Total Stok: {{ barang.stok_total }}</div>
              <button @click="handlePinjam" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-0.5 cursor-pointer">
                Pinjam <ChevronRight class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-16 bg-card border border-border rounded-xl text-muted-foreground text-sm">Tidak ada barang yang cocok.</div>
      </section>
    </main>

    <!-- ── Footer ──────────────────────────────────── -->
    <footer class="bg-foreground py-8 px-6 text-center text-white">
      <div class="max-w-7xl mx-auto space-y-1.5">
        <p class="text-sm font-semibold text-white/90">&copy; 2026 SiPinjam — STITEK Bontang</p>
        <p class="font-medium text-white/50 tracking-wider text-xs">The Knowledgeable and Virtue Campus</p>
      </div>
    </footer>

    <!-- ── Interactive Calendar Event Details Modal (Public) ── -->
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
              <DialogTitle class="text-base font-bold text-foreground">
                {{ selectedCalendarEvent?.nama || selectedCalendarEvent?.title || 'Detail Jadwal' }}
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
          <DialogDescription class="text-xs text-muted-foreground pt-2 space-y-3">
            <div class="rounded-xl border border-border bg-muted/40 p-3.5 space-y-2 text-xs">
              <div class="flex items-center justify-between">
                <span class="font-medium text-muted-foreground">Rentang Tanggal:</span>
                <span class="font-semibold text-foreground">{{ selectedCalendarEvent?.start }} — {{ selectedCalendarEvent?.end }}</span>
              </div>
              <div v-if="selectedCalendarEvent?.jam_mulai" class="flex items-center justify-between">
                <span class="font-medium text-muted-foreground">Jam Operasional:</span>
                <span class="font-semibold text-foreground">{{ selectedCalendarEvent?.jam_mulai }} - {{ selectedCalendarEvent?.jam_selesai }} WITA</span>
              </div>
              <div v-if="selectedCalendarEvent?.keterangan" class="pt-1 border-t border-border">
                <span class="font-medium text-muted-foreground block mb-0.5">Keperluan:</span>
                <p class="text-foreground italic">{{ selectedCalendarEvent?.keterangan }}</p>
              </div>
            </div>
          </DialogDescription>
        </DialogHeader>
        <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-3">
          <Button variant="outline" size="sm" class="w-full sm:w-auto" @click="showEventDetailsModal = false">
            Tutup
          </Button>
          <Button size="sm" class="w-full sm:w-auto bg-primary text-primary-foreground hover:opacity-90" @click="confirmLogin">
            <LogIn class="h-4 w-4 mr-1.5" /> Masuk untuk Pinjam
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- ── Login Dialog ────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showLoginDialog" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDialog" />
          <div class="relative bg-card border border-border rounded-xl shadow-card w-full max-w-md z-10 overflow-hidden">
            <div class="bg-primary p-6 text-white">
              <button @click="closeDialog" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors cursor-pointer"><X class="h-5 w-5" /></button>
              <div class="flex items-center gap-3 mb-3">
                <div class="w-11 h-11 bg-white/15 backdrop-blur-sm rounded-lg flex items-center justify-center">
                  <GraduationCap class="h-6 w-6 text-white" />
                </div>
                <h3 class="text-xl font-bold">Masuk ke SiPinjam</h3>
              </div>
              <p class="text-sm text-white/80">Silakan masuk dengan akun kampus Anda untuk mengakses layanan peminjaman.</p>
            </div>
            <div class="p-6 space-y-4">
              <div v-if="guestDateRange" class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                <p class="text-xs font-semibold text-primary mb-1">Tanggal Terpilih</p>
                <p class="text-sm font-medium text-foreground">{{ guestDateRange.start }} — {{ guestDateRange.end }}</p>
                <p class="text-[10px] text-muted-foreground mt-1">Tanggal akan otomatis terisi di form booking setelah login.</p>
              </div>
              <div class="bg-muted rounded-lg p-4 flex items-start gap-3">
                <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center bg-primary/10 rounded-md"><Info class="h-4 w-4 text-primary" /></div>
                <div class="space-y-0.5">
                  <p class="text-sm font-semibold text-foreground">Informasi Akun</p>
                  <p class="text-xs text-muted-foreground">Gunakan email institusi <span class="font-mono font-semibold">@stitek.ac.id</span> yang telah didaftarkan oleh admin kampus.</p>
                </div>
              </div>
            </div>
            <div class="flex gap-3 p-6 pt-0">
              <button @click="closeDialog" class="flex-1 px-4 py-2.5 border border-border bg-card text-foreground font-medium text-sm rounded-lg transition-all duration-200 hover:bg-muted cursor-pointer">Kembali</button>
              <button @click="confirmLogin" id="btn-confirm-login"
                class="flex-1 px-4 py-2.5 bg-primary text-primary-foreground font-medium text-sm rounded-lg shadow-sm transition-all duration-200 hover:opacity-90 flex items-center justify-center gap-2 cursor-pointer">
                <LogIn class="h-4 w-4" />Masuk Sekarang
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
/* ── Clean FullCalendar Overrides ──────────────────── */
.clean-calendar :deep(.fc) { font-family: 'Inter', sans-serif; }
.clean-calendar :deep(.fc-toolbar-title) { font-size: 1rem !important; font-weight: 700 !important; color: #0f172a !important; }
.clean-calendar :deep(.fc-button) {
  background: hsl(var(--primary)) !important; border: 1px solid transparent !important;
  border-radius: 0.5rem !important; color: hsl(var(--primary-foreground)) !important;
  font-weight: 600 !important; box-shadow: 0 1px 2px rgba(0,0,0,.05) !important;
  padding: 6px 14px !important; transition: all 0.2s !important; font-size: 0.85rem !important;
  text-transform: capitalize !important;
}
.clean-calendar :deep(.fc-button:hover) { opacity: 0.9 !important; }
.clean-calendar :deep(.fc-button-active) { background: hsl(var(--primary)) !important; opacity: 0.85 !important; }
.clean-calendar :deep(.fc-daygrid-day) { border: 1px solid hsl(var(--border)) !important; }
.clean-calendar :deep(.fc-col-header-cell) {
  background: hsl(var(--muted)) !important; color: hsl(var(--muted-foreground)) !important;
  font-weight: 600 !important; font-size: 0.8rem !important; border: 1px solid hsl(var(--border)) !important; padding: 8px 0 !important;
}
.clean-calendar :deep(.fc-day-today) { background: hsl(var(--primary) / 0.06) !important; }
.clean-calendar :deep(.fc-highlight) { background: hsl(var(--primary) / 0.12) !important; }
.clean-calendar :deep(.fc-daygrid-day-number) { font-weight: 600 !important; font-size: 0.85rem; padding: 6px 8px !important; color: #334155 !important; }
.clean-calendar :deep(.fc-scrollgrid) { border: 1px solid hsl(var(--border)) !important; border-radius: 0.5rem !important; overflow: hidden; }
.clean-calendar :deep(th), .clean-calendar :deep(td) { border-color: hsl(var(--border)) !important; }
.clean-calendar :deep(.fc-event) { border-radius: 4px !important; padding: 1px 4px !important; font-size: 0.75rem !important; font-weight: 600 !important; cursor: pointer !important; transition: transform 0.15s ease !important; }
.clean-calendar :deep(.fc-event:hover) { transform: scale(1.02) !important; }

/* ── Framer Motion-style Keyframes ─────────────────── */
@keyframes floatSlow {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-16px) rotate(3deg); }
}

@keyframes floatDelayed {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(14px) rotate(-3deg); }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes spinSlow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-float-slow {
  animation: floatSlow 8s ease-in-out infinite;
}

.animate-float-delayed {
  animation: floatDelayed 10s ease-in-out infinite;
}

.animate-fade-up {
  animation: fadeUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-fade-up-delayed {
  animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
}

.animate-spin-slow {
  animation: spinSlow 12s linear infinite;
}
</style>
