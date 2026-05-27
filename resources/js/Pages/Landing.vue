<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
  Building2, 
  Package, 
  Search, 
  MapPin, 
  Users, 
  ArrowRight,
  Sparkles,
  Layers,
  ChevronRight,
  Info,
  LogIn,
  GraduationCap
} from '@lucide/vue';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const props = defineProps({
  ruangans: Array,
  barangs: Array
});

const searchQuery = ref('');
const activeTab = ref('all'); // 'all', 'ruangan', 'barang'
const showLoginDialog = ref(false);

const handlePinjam = () => {
  showLoginDialog.value = true;
};

const confirmLogin = () => {
  showLoginDialog.value = false;
  router.visit('/login');
};

const filteredRuangans = computed(() => {
  return props.ruangans.filter(r => 
    r.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    r.lokasi.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const filteredBarangs = computed(() => {
  return props.barangs.filter(b => 
    b.nama.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    b.kategori.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>

<template>
  <Head title="Katalog Aset Kampus" />

  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-blue-600 selection:text-white font-sans antialiased">
    <!-- Header / Hero Section -->
    <header class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-950 text-white py-16 px-6 sm:px-12 border-b border-indigo-900/50 shadow-2xl">
      <!-- Decorative Glow elements -->
      <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl translate-y-1/2"></div>
      </div>

      <div class="max-w-7xl mx-auto relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-12">
        <div class="max-w-2xl space-y-6">
          <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/25 px-3 py-1 rounded-full text-indigo-300 text-xs font-semibold uppercase tracking-wider backdrop-blur-sm">
            <Sparkles class="w-3.5 h-3.5" />
            Sistem Peminjaman Aset Kampus
          </div>
          
          <div class="space-y-2">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-200 bg-clip-text text-transparent">
              SiPinjam
            </h1>
            <p class="text-xs tracking-[0.25em] uppercase text-indigo-300/80 font-semibold">
              STITEK Bontang — The Knowledgeable and Virtue Campus
            </p>
          </div>

          <p class="text-lg text-slate-300 font-light leading-relaxed max-w-xl">
            Layanan peminjaman barang dan ruangan kampus STITEK Bontang secara praktis, terintegrasi, dan terpantau dalam satu platform SPA.
          </p>

          <div class="flex flex-wrap gap-4 pt-2">
            <button 
              @click="handlePinjam"
              id="btn-mulai-pinjam"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-blue-600/20 transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 group"
            >
              Mulai Peminjaman 
              <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
            </button>
            <a 
              href="#katalog" 
              class="inline-flex items-center justify-center border border-slate-700 hover:border-slate-500 bg-slate-900/50 hover:bg-slate-900 text-slate-300 hover:text-white font-medium px-6 py-3 rounded-xl backdrop-blur-sm transition-all duration-200"
            >
              Lihat Katalog Aset
            </a>
          </div>
        </div>

        <!-- Stat Card Widget -->
        <div class="w-full md:w-80 bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-md shadow-2xl flex flex-col justify-between">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Operasional</span>
              <span class="flex h-2.5 w-2.5 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
              </span>
            </div>
            
            <div class="border-t border-white/5 my-3"></div>

            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <p class="text-2xl font-bold text-white">{{ ruangans.length }}</p>
                <p class="text-xs text-slate-400">Total Ruangan</p>
              </div>
              <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                <p class="text-2xl font-bold text-white">{{ barangs.length }}</p>
                <p class="text-xs text-slate-400">Total Barang</p>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex items-center justify-between text-xs text-slate-400 bg-white/5 -mx-6 -mb-6 p-4 rounded-b-2xl border-t border-white/5">
            <span>Aksesibilitas Terjamin</span>
            <span class="flex items-center gap-1 font-semibold text-blue-300">
              STITEK Bontang
            </span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Catalog -->
    <main id="katalog" class="max-w-7xl mx-auto py-12 px-6 sm:px-12 space-y-10">
      
      <!-- Filter controls and Search -->
      <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
        <!-- Tabs -->
        <div class="flex gap-1 p-1 bg-slate-100 rounded-xl w-full md:w-auto">
          <button 
            @click="activeTab = 'all'"
            :class="[
              'flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
              activeTab === 'all' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Semua Aset
          </button>
          <button 
            @click="activeTab = 'ruangan'"
            :class="[
              'flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
              activeTab === 'ruangan' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Ruangan
          </button>
          <button 
            @click="activeTab = 'barang'"
            :class="[
              'flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200',
              activeTab === 'barang' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-slate-600 hover:text-slate-900'
            ]"
          >
            Barang
          </button>
        </div>

        <!-- Search input -->
        <div class="relative w-full md:w-80">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <Search class="w-4 h-4" />
          </span>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari nama atau lokasi aset..."
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all duration-200 placeholder:text-slate-400"
          />
        </div>
      </div>

      <!-- Ruangan Section -->
      <section v-if="activeTab === 'all' || activeTab === 'ruangan'" class="space-y-6">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200">
          <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
            <Building2 class="w-4.5 h-4.5" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-slate-900">Ruangan Gedung Utama & Djuanda</h2>
            <p class="text-xs text-slate-500">Ruang kelas teori dan laboratorium praktikum yang dapat dipesan</p>
          </div>
        </div>

        <div v-if="filteredRuangans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="ruangan in filteredRuangans" 
            :key="ruangan.id"
            class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-lg hover:border-slate-300 transition-all duration-200 flex flex-col justify-between"
          >
            <div class="p-6 space-y-4">
              <!-- Card Header -->
              <div class="flex items-start justify-between gap-3">
                <span class="text-[10px] font-bold tracking-wider uppercase bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                  {{ ruangan.kode }}
                </span>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Tersedia
                </span>
              </div>

              <!-- Name & Location -->
              <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                  {{ ruangan.nama }}
                </h3>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                  <MapPin class="w-3.5 h-3.5 text-slate-400" />
                  {{ ruangan.lokasi }}
                </div>
              </div>

              <!-- Description -->
              <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                {{ ruangan.deskripsi || 'Tidak ada deskripsi tambahan.' }}
              </p>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-4 rounded-b-2xl">
              <div class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold">
                <Users class="w-4 h-4 text-slate-400" />
                <span>Kapasitas: {{ ruangan.kapasitas }} Orang</span>
              </div>

              <button 
                @click="handlePinjam"
                class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-500 active:text-blue-700 transition-colors group-hover:translate-x-0.5 duration-200"
              >
                Pinjam Ruangan
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 bg-white rounded-2xl border border-slate-100 text-slate-400 text-sm">
          Tidak ada ruangan yang cocok dengan pencarian Anda.
        </div>
      </section>

      <!-- Barang Section -->
      <section v-if="activeTab === 'all' || activeTab === 'barang'" class="space-y-6">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-200">
          <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
            <Package class="w-4.5 h-4.5" />
          </div>
          <div>
            <h2 class="text-xl font-bold text-slate-900">Barang & Inventaris Peminjaman</h2>
            <p class="text-xs text-slate-500">Daftar perangkat elektronik, audio, kabel, dan pendukung perkuliahan</p>
          </div>
        </div>

        <div v-if="filteredBarangs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="barang in filteredBarangs" 
            :key="barang.id"
            class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-lg hover:border-slate-300 transition-all duration-200 flex flex-col justify-between"
          >
            <div class="p-6 space-y-4">
              <!-- Card Header -->
              <div class="flex items-start justify-between gap-3">
                <span class="text-[10px] font-bold tracking-wider uppercase bg-slate-100 text-slate-600 px-2 py-0.5 rounded">
                  {{ barang.kode }}
                </span>
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Stok: {{ barang.stok_tersedia }} Unit
                </span>
              </div>

              <!-- Name & Category -->
              <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                  {{ barang.nama }}
                </h3>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                  <Layers class="w-3.5 h-3.5 text-slate-400" />
                  Kategori: {{ barang.kategori || 'Inventaris' }}
                </div>
              </div>

              <!-- Description -->
              <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                {{ barang.deskripsi || 'Tidak ada deskripsi tambahan.' }}
              </p>
            </div>

            <!-- Card Footer -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-4 rounded-b-2xl">
              <div class="flex items-center gap-1 text-xs text-slate-500 font-medium">
                <Info class="w-3.5 h-3.5 text-slate-400" />
                <span>Total Stok: {{ barang.stok_total }}</span>
              </div>

              <button 
                @click="handlePinjam"
                class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-500 active:text-blue-700 transition-colors group-hover:translate-x-0.5 duration-200"
              >
                Pinjam Barang
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 bg-white rounded-2xl border border-slate-100 text-slate-400 text-sm">
          Tidak ada barang yang cocok dengan pencarian Anda.
        </div>
      </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8 px-6 text-center text-slate-500 text-xs">
      <div class="max-w-7xl mx-auto space-y-2">
        <p>&copy; 2026 SiPinjam — STITEK Bontang. All rights reserved.</p>
        <p class="font-medium text-slate-400 tracking-wider">The Knowledgeable and Virtue Campus</p>
      </div>
    </footer>

    <!-- ── Login Confirmation Dialog ───────────────── -->
    <Dialog v-model:open="showLoginDialog">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/20">
            <GraduationCap class="h-7 w-7 text-white" />
          </div>
          <DialogTitle class="text-center text-xl font-bold">
            Masuk ke SiPinjam
          </DialogTitle>
          <DialogDescription class="text-center text-sm text-muted-foreground leading-relaxed pt-2">
            Silakan masuk dengan akun kampus Anda untuk mengakses layanan peminjaman aset. 
            Pastikan Anda sudah terdaftar di sistem akademik STITEK Bontang.
          </DialogDescription>
        </DialogHeader>

        <div class="mt-2 rounded-xl border border-blue-100 bg-blue-50/50 p-4">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
              <Info class="h-4 w-4" />
            </div>
            <div class="space-y-1">
              <p class="text-sm font-semibold text-blue-900">Informasi Akun</p>
              <p class="text-xs text-blue-700 leading-relaxed">
                Gunakan email institusi <span class="font-mono font-semibold">@stitek.ac.id</span> yang telah didaftarkan oleh admin kampus.
              </p>
            </div>
          </div>
        </div>

        <DialogFooter class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
          <Button 
            variant="outline" 
            @click="showLoginDialog = false"
            class="w-full sm:w-auto"
          >
            Kembali
          </Button>
          <Button 
            @click="confirmLogin"
            id="btn-confirm-login"
            class="w-full sm:w-auto gap-2 bg-blue-600 hover:bg-blue-500"
          >
            <LogIn class="h-4 w-4" />
            Masuk Sekarang
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
