<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import BookingModal from '@/Components/BookingModal.vue';
import {
  Building2,
  MapPin,
  Users,
  Search,
  Sparkles,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';

defineOptions({ layout: UserLayout });

const props = defineProps({
  ruangans: Array,
});

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

const showBookingModal = ref(false);
const selectedAsset = ref(null);

const openBooking = (ruangan) => {
  selectedAsset.value = { ...ruangan, tipe: 'ruangan' };
  showBookingModal.value = true;
};

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('/image/') || path.startsWith('image/')) {
    if (path.startsWith('image/')) return '/' + path;
    return path;
  }
  if (path.startsWith('public/')) {
    return '/storage/' + path.substring(7);
  }
  if (path.startsWith('http') || path.startsWith('/storage') || path.startsWith('storage/')) {
    if (path.startsWith('storage/')) return '/' + path;
    return path;
  }
  const cleanPath = path.startsWith('/') ? path.slice(1) : path;
  return '/storage/' + cleanPath;
};
</script>

<template>
  <Head title="Katalog Ruangan" />

  <div class="px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <Building2 class="h-5 w-5 text-blue-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Katalog Ruangan</h1>
      </div>
      <p class="text-sm text-slate-500">
        Pilih ruangan kampus STITEK Bontang yang ingin Anda pinjam.
      </p>
    </div>

    <!-- Toolbar: Search & Info -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-sm font-semibold text-slate-700">Daftar Ruangan Aktif</h2>
        <p class="text-xs text-slate-400">Total: {{ filteredRuangans.length }} ruangan</p>
      </div>

      <div class="relative w-full sm:w-72">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <Input
          v-model="searchQuery"
          placeholder="Cari ruangan..."
          class="pl-10 bg-white border-slate-200"
        />
      </div>
    </div>

    <!-- Catalog Grid -->
    <div
      v-if="filteredRuangans.length"
      class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
    >
      <Card
        v-for="ruangan in filteredRuangans"
        :key="ruangan.id"
        @click="openBooking(ruangan)"
        class="group cursor-pointer overflow-hidden border-slate-200/80 transition-all duration-200 hover:shadow-lg hover:border-blue-200 hover:-translate-y-0.5"
      >
        <CardContent class="p-0">
          <!-- Image Banner -->
          <div class="relative w-full aspect-[4/3] overflow-hidden bg-slate-100 border-b border-slate-200">
            <img
              v-if="ruangan.image_path"
              :src="getImageUrl(ruangan.image_path)"
              alt="Foto Ruangan"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
            <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-1.5 select-none">
              <Building2 class="h-8 w-8 stroke-[1.5]" />
              <span class="text-xs font-medium">No Image</span>
            </div>
          </div>
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

    <!-- Empty State -->
    <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
      <Building2 class="mx-auto h-10 w-10 text-slate-300" />
      <p class="mt-3 text-sm text-slate-400">Tidak ada ruangan ditemukan</p>
    </div>

    <!-- Booking Modal -->
    <BookingModal
      v-model:open="showBookingModal"
      :asset="selectedAsset"
    />
  </div>
</template>
