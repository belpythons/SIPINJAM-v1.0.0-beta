<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import BookingModal from '@/Components/BookingModal.vue';
import {
  Package,
  Layers,
  Search,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';

defineOptions({ layout: UserLayout });

const props = defineProps({
  barangs: Array,
});

const searchQuery = ref('');

const filteredBarangs = computed(() => {
  const q = searchQuery.value.toLowerCase();
  if (!q) return props.barangs;
  return props.barangs.filter(
    (b) =>
      b.nama.toLowerCase().includes(q) ||
      (b.kategori && b.kategori.toLowerCase().includes(q))
  );
});

const showBookingModal = ref(false);
const selectedAsset = ref(null);

const openBooking = (barang) => {
  selectedAsset.value = { ...barang, tipe: 'barang' };
  showBookingModal.value = true;
};
</script>

<template>
  <Head title="Katalog Barang" />

  <div class="px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <Package class="h-5 w-5 text-blue-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Katalog Barang</h1>
      </div>
      <p class="text-sm text-slate-500">
        Pilih barang/inventaris kampus STITEK Bontang yang ingin Anda pinjam.
      </p>
    </div>

    <!-- Toolbar: Search & Info -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-sm font-semibold text-slate-700">Daftar Barang Aktif</h2>
        <p class="text-xs text-slate-400">Total: {{ filteredBarangs.length }} barang</p>
      </div>

      <div class="relative w-full sm:w-72">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <Input
          v-model="searchQuery"
          placeholder="Cari barang..."
          class="pl-10 bg-white border-slate-200"
        />
      </div>
    </div>

    <!-- Catalog Grid -->
    <div
      v-if="filteredBarangs.length"
      class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
    >
      <Card
        v-for="barang in filteredBarangs"
        :key="barang.id"
        @click="openBooking(barang)"
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

    <!-- Empty State -->
    <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-white py-16 text-center">
      <Package class="mx-auto h-10 w-10 text-slate-300" />
      <p class="mt-3 text-sm text-slate-400">Tidak ada barang ditemukan</p>
    </div>

    <!-- Booking Modal -->
    <BookingModal
      v-model:open="showBookingModal"
      :asset="selectedAsset"
    />
  </div>
</template>
