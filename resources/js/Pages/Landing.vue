<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import {
  Building2, Package, Search, MapPin, Users,
  ArrowRight, Sparkles, Layers, ChevronRight,
  Info, LogIn, GraduationCap, CalendarDays, X,
} from '@lucide/vue';

const props = defineProps({
  ruangans: Array,
  barangs: Array,
});

const searchQuery = ref('');
const activeTab = ref('all');
const showLoginDialog = ref(false);
const guestDateRange = ref(null);

// ── FullCalendar Config ─────────────────────────────
const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  selectable: true,
  editable: false,
  headerToolbar: { left: 'prev', center: 'title', right: 'next' },
  height: 'auto',
  locale: 'id',
  select: handleDateSelect,
  validRange: { start: new Date().toISOString().split('T')[0] },
  dayHeaderFormat: { weekday: 'short' },
  slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
});

function handleDateSelect(selectInfo) {
  guestDateRange.value = { start: selectInfo.startStr, end: selectInfo.endStr };
  const cookieVal = JSON.stringify(guestDateRange.value);
  document.cookie = `sipinjam_guest_dates=${encodeURIComponent(cookieVal)};path=/;max-age=3600;SameSite=Lax`;
  showLoginDialog.value = true;
}

const handlePinjam = () => { showLoginDialog.value = true; };
const confirmLogin = () => { showLoginDialog.value = false; router.visit('/login'); };
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
  <Head title="Katalog Aset Kampus" />

  <div class="min-h-screen bg-background text-foreground font-sans antialiased">
    <!-- ── Header / Hero ──────────────────────────── -->
    <header class="relative bg-primary py-16 px-6 sm:px-12 overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-primary to-blue-700 opacity-90" />
      <div class="absolute -right-20 -top-20 w-72 h-72 bg-white/5 rounded-full blur-3xl" />
      <div class="absolute right-40 -bottom-32 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl" />

      <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-12">
        <div class="max-w-2xl space-y-5">
          <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-1.5 rounded-full text-white text-xs font-semibold uppercase tracking-wider">
            <Sparkles class="w-3.5 h-3.5" />
            Sistem Peminjaman Aset Kampus
          </div>
          <div class="space-y-2">
            <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-white">SiPinjam</h1>
            <p class="text-sm tracking-widest uppercase text-white/80 font-medium">STITEK Bontang — The Knowledgeable and Virtue Campus</p>
          </div>
          <p class="text-lg text-white/90 font-normal leading-relaxed max-w-xl">
            Layanan peminjaman barang dan ruangan kampus secara praktis, terintegrasi, dan terpantau dalam satu platform.
          </p>
          <div class="flex flex-wrap gap-3 pt-2">
            <button @click="handlePinjam" id="btn-mulai-pinjam"
              class="inline-flex items-center gap-2 bg-white text-primary font-semibold px-6 py-3 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group">
              Mulai Peminjaman
              <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-0.5" />
            </button>
            <a href="#katalog"
              class="inline-flex items-center justify-center border border-white/40 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 hover:bg-white/10">
              Lihat Katalog Aset
            </a>
          </div>
        </div>

        <!-- Stat Widget -->
        <div class="w-full md:w-80 bg-card border border-white/20 backdrop-blur-sm rounded-xl p-6 shadow-soft text-white">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold uppercase tracking-wider text-white/80">Status Operasional</span>
              <span class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400" />
              </span>
            </div>
            <div class="border-t border-white/15 my-3" />
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <p class="text-2xl font-bold text-white">{{ ruangans.length }}</p>
                <p class="text-xs font-medium text-white/70">Total Ruangan</p>
              </div>
              <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <p class="text-2xl font-bold text-white">{{ barangs.length }}</p>
                <p class="text-xs font-medium text-white/70">Total Barang</p>
              </div>
            </div>
          </div>
          <div class="mt-5 flex items-center justify-between text-xs font-medium text-white/60 pt-4 border-t border-white/15">
            <span>Aksesibilitas Terjamin</span>
            <span>STITEK</span>
          </div>
        </div>
      </div>
    </header>

    <!-- ── FullCalendar Section ────────────────────── -->
    <section class="max-w-7xl mx-auto py-12 px-6 sm:px-12">
      <div class="mb-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
          <CalendarDays class="w-5 h-5 text-primary" />
        </div>
        <div>
          <h2 class="text-xl font-bold text-foreground">Pilih Tanggal Peminjaman</h2>
          <p class="text-xs text-muted-foreground">Drag untuk memilih rentang tanggal → login → booking otomatis terisi</p>
        </div>
      </div>
      <div class="bg-card border border-border rounded-xl shadow-card p-4 sm:p-6 clean-calendar">
        <FullCalendar :options="calendarOptions" />
      </div>
    </section>

    <!-- ── Catalog ─────────────────────────────────── -->
    <main id="katalog" class="max-w-7xl mx-auto py-12 px-6 sm:px-12 space-y-10">
      <!-- Filter + Search -->
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-card border border-border rounded-xl p-4 shadow-card">
        <div class="flex gap-1 w-full md:w-auto bg-muted rounded-lg p-1">
          <button v-for="tab in [{key:'all',label:'Semua'},{key:'ruangan',label:'Ruangan'},{key:'barang',label:'Barang'}]"
            :key="tab.key" @click="activeTab = tab.key"
            :class="['flex-1 md:flex-none px-5 py-2 text-sm font-medium rounded-md transition-all duration-200',
              activeTab === tab.key ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']">
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
            class="group bg-card border border-border rounded-xl shadow-card overflow-hidden flex flex-col justify-between transition-all duration-200 hover:shadow-soft hover:-translate-y-0.5">
            <div class="p-5 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <span class="text-[10px] font-semibold tracking-wider uppercase bg-muted text-muted-foreground px-2.5 py-1 rounded-md">{{ ruangan.kode }}</span>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" />Tersedia
                </span>
              </div>
              <div class="space-y-1">
                <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors">{{ ruangan.nama }}</h3>
                <div class="flex items-center gap-1.5 text-xs text-muted-foreground"><MapPin class="w-3.5 h-3.5" />{{ ruangan.lokasi }}</div>
              </div>
              <p class="text-xs text-muted-foreground line-clamp-2 leading-relaxed">{{ ruangan.deskripsi || 'Tidak ada deskripsi tambahan.' }}</p>
            </div>
            <div class="px-5 py-3.5 bg-muted/50 border-t border-border flex items-center justify-between">
              <div class="flex items-center gap-1.5 text-xs font-medium text-muted-foreground"><Users class="w-4 h-4" />{{ ruangan.kapasitas }} Orang</div>
              <button @click="handlePinjam" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-0.5">
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
            class="group bg-card border border-border rounded-xl shadow-card overflow-hidden flex flex-col justify-between transition-all duration-200 hover:shadow-soft hover:-translate-y-0.5">
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
            <div class="px-5 py-3.5 bg-muted/50 border-t border-border flex items-center justify-between">
              <div class="flex items-center gap-1 text-xs text-muted-foreground"><Info class="w-3.5 h-3.5" />Total Stok: {{ barang.stok_total }}</div>
              <button @click="handlePinjam" class="text-xs font-semibold text-primary hover:text-primary/80 transition-colors flex items-center gap-0.5">
                Pinjam <ChevronRight class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-16 bg-card border border-border rounded-xl text-muted-foreground text-sm">Tidak ada barang yang cocok.</div>
      </section>
    </main>

    <!-- ── Footer ──────────────────────────────────── -->
    <footer class="bg-foreground py-8 px-6 text-center">
      <div class="max-w-7xl mx-auto space-y-1.5">
        <p class="text-sm font-semibold text-white/90">&copy; 2026 SiPinjam — STITEK Bontang</p>
        <p class="font-medium text-white/50 tracking-wider text-xs">The Knowledgeable and Virtue Campus</p>
      </div>
    </footer>

    <!-- ── Login Dialog ────────────────────────────── -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showLoginDialog" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDialog" />
          <div class="relative bg-card border border-border rounded-xl shadow-soft w-full max-w-md z-10 overflow-hidden">
            <div class="bg-primary p-6 text-white">
              <button @click="closeDialog" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors"><X class="h-5 w-5" /></button>
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
              <button @click="closeDialog" class="flex-1 px-4 py-2.5 border border-border bg-card text-foreground font-medium text-sm rounded-lg transition-all duration-200 hover:bg-muted">Kembali</button>
              <button @click="confirmLogin" id="btn-confirm-login"
                class="flex-1 px-4 py-2.5 bg-primary text-primary-foreground font-medium text-sm rounded-lg shadow-sm transition-all duration-200 hover:opacity-90 flex items-center justify-center gap-2">
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
.clean-calendar :deep(.fc-toolbar-title) { font-size: 1rem !important; font-weight: 700 !important; }
.clean-calendar :deep(.fc-button) {
  background: hsl(var(--primary)) !important; border: 1px solid transparent !important;
  border-radius: 0.5rem !important; color: hsl(var(--primary-foreground)) !important;
  font-weight: 600 !important; box-shadow: 0 1px 2px rgba(0,0,0,.05) !important;
  padding: 6px 14px !important; transition: all 0.2s !important; font-size: 0.85rem !important;
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
.clean-calendar :deep(.fc-daygrid-day-number) { font-weight: 600 !important; font-size: 0.85rem; padding: 6px 8px !important; }
.clean-calendar :deep(.fc-scrollgrid) { border: 1px solid hsl(var(--border)) !important; border-radius: 0.5rem !important; overflow: hidden; }
.clean-calendar :deep(th), .clean-calendar :deep(td) { border-color: hsl(var(--border)) !important; }
</style>
