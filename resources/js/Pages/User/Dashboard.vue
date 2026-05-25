<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import BookingModal from '@/Components/BookingModal.vue';
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
});

const page = usePage();
const flash = computed(() => page.props.flash);

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

const openBooking = (asset, type) => {
  selectedAsset.value = { ...asset, tipe: type };
  showBookingModal.value = true;
};

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
</script>

<template>
  <Head title="Dashboard" />

  <div class="px-6 py-8 lg:px-10">
    <!-- ── Page Header ──────────────────────────────── -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <Sparkles class="h-5 w-5 text-blue-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
      </div>
      <p class="text-sm text-slate-500">
        Selamat datang, <span class="font-semibold text-slate-700">{{ $page.props.auth.user?.name }}</span>. Kelola peminjaman aset kampus Anda di sini.
      </p>
    </div>

    <!-- ── Flash Message ────────────────────────────── -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="flash?.success"
        class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
      >
        <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-500" />
        {{ flash.success }}
      </div>
    </Transition>

    <!-- ── Stat Cards ───────────────────────────────── -->
    <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div
        v-for="stat in statCards"
        :key="stat.label"
        class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5"
      >
        <!-- Gradient accent bar -->
        <div
          :class="['absolute inset-x-0 top-0 h-1 bg-gradient-to-r', stat.color]"
        />
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
              {{ stat.label }}
            </p>
            <p class="mt-2 text-3xl font-bold tabular-nums text-slate-900">
              {{ stat.value }}
            </p>
          </div>
          <div
            :class="[
              'flex h-10 w-10 items-center justify-center rounded-xl transition-transform group-hover:scale-110',
              stat.bgLight,
            ]"
          >
            <component :is="stat.icon" :class="['h-5 w-5', stat.textColor]" />
          </div>
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
  />
</template>
