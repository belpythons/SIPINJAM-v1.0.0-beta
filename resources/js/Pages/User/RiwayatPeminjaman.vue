<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import {
  ClipboardList,
  Clock,
  CheckCircle2,
  XCircle,
  PackageCheck,
  CalendarDays,
  Building2,
  Package,
  FileText,
  Printer,
  Download,
  Filter,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

defineOptions({ layout: UserLayout });

const props = defineProps({
  bookings: { type: Object, required: true },
  stats: { type: Object, default: () => ({ total: 0, pending: 0, approved: 0, completed: 0 }) },
  filters: { type: Object, default: () => ({}) },
});

const currentStatus = ref(props.filters?.status ?? '');

watch(currentStatus, (newVal) => {
  router.get(
    '/bookings',
    { status: newVal || undefined },
    { preserveState: true, preserveScroll: true, replace: true }
  );
});

const statusConfig = {
  menunggu: {
    label: 'Menunggu',
    color: 'text-amber-600',
    bgColor: 'bg-amber-50',
    borderColor: 'border-amber-200',
    icon: Clock,
    step: 1,
  },
  sedang_dipinjam: {
    label: 'Disetujui',
    color: 'text-blue-600',
    bgColor: 'bg-blue-50',
    borderColor: 'border-blue-200',
    icon: PackageCheck,
    step: 2,
  },
  selesai: {
    label: 'Selesai',
    color: 'text-emerald-600',
    bgColor: 'bg-emerald-50',
    borderColor: 'border-emerald-200',
    icon: CheckCircle2,
    step: 3,
  },
  ditolak: {
    label: 'Ditolak',
    color: 'text-red-600',
    bgColor: 'bg-red-50',
    borderColor: 'border-red-200',
    icon: XCircle,
    step: -1,
  },
};

const getStatusConfig = (status) => statusConfig[status] || statusConfig['menunggu'];

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};

const formatTime = (timeStr) => {
  if (!timeStr) return '-';
  return timeStr.substring(0, 5);
};

const timelineSteps = [
  { key: 'pending',  label: 'Diajukan',  stepNum: 1 },
  { key: 'approved', label: 'Disetujui', stepNum: 2 },
  { key: 'done',     label: 'Selesai',   stepNum: 3 },
];
</script>

<template>
  <Head title="Riwayat Peminjaman & Laporan" />

  <div class="px-6 py-8 lg:px-10">
    <!-- Page Header with Export Report Button -->
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <ClipboardList class="h-6 w-6 text-primary" />
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Peminjaman & Laporan</h1>
        </div>
        <p class="text-xs text-slate-500">
          Daftar seluruh pengajuan peminjaman aset Anda beserta histori status dan dokumen permohonan.
        </p>
      </div>

      <!-- Export Report Button (Integrated Laporan) -->
      <div class="flex items-center gap-2">
        <a
          href="/laporan/export-pdf"
          target="_blank"
          class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-semibold text-primary-foreground shadow-xs transition-all duration-200 hover:opacity-90 cursor-pointer"
        >
          <Download class="h-4 w-4" />
          Unduh Rekap Laporan PDF
        </a>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div class="rounded-xl border border-slate-200 bg-white p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-slate-900">{{ stats.total ?? 0 }}</p>
        <p class="text-xs font-medium text-slate-500 mt-1">Total Peminjaman</p>
      </div>
      <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-amber-600">{{ stats.pending ?? 0 }}</p>
        <p class="text-xs font-medium text-amber-600/70 mt-1">Menunggu</p>
      </div>
      <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-blue-600">{{ stats.approved ?? 0 }}</p>
        <p class="text-xs font-medium text-blue-600/70 mt-1">Disetujui</p>
      </div>
      <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-center shadow-xs">
        <p class="text-2xl font-bold text-emerald-600">{{ stats.completed ?? 0 }}</p>
        <p class="text-xs font-medium text-emerald-600/70 mt-1">Selesai</p>
      </div>
    </div>

    <!-- Filter Status Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-3 shadow-xs">
      <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 px-2">
        <Filter class="h-4 w-4 text-slate-400" />
        <span>Filter Status:</span>
      </div>
      <div class="flex flex-wrap gap-1.5">
        <button
          v-for="f in [
            { key: '', label: 'Semua' },
            { key: 'menunggu', label: 'Menunggu' },
            { key: 'sedang_dipinjam', label: 'Disetujui' },
            { key: 'selesai', label: 'Selesai' },
            { key: 'ditolak', label: 'Ditolak' }
          ]"
          :key="f.key"
          @click="currentStatus = f.key"
          :class="[
            'px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 cursor-pointer',
            currentStatus === f.key
              ? 'bg-primary text-primary-foreground shadow-xs font-semibold'
              : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900'
          ]"
        >
          {{ f.label }}
        </button>
      </div>
    </div>

    <!-- Booking Cards -->
    <div v-if="bookings.data.length > 0" class="space-y-4">
      <Card
        v-for="booking in bookings.data"
        :key="booking.id"
        :class="[
          'overflow-hidden transition-all duration-200 hover:shadow-md',
          booking.status === 'ditolak' ? 'border-red-200' : 'border-slate-200/80',
        ]"
      >
        <CardContent class="p-0">
          <!-- Card Top: Item Info + Status Badge -->
          <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 p-5 pb-3">
            <div class="flex items-start gap-3 min-w-0">
              <div
                :class="[
                  'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                  booking.tipe === 'ruangan' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600',
                ]"
              >
                <component
                  :is="booking.tipe === 'ruangan' ? Building2 : Package"
                  class="h-5 w-5"
                />
              </div>
              <div class="min-w-0">
                <h3 class="text-sm font-bold text-slate-900 truncate">
                  {{ booking.nama_item || (booking.barang?.nama ?? booking.ruangan?.nama ?? '-') }}
                </h3>
                <div class="flex items-center gap-2 mt-0.5">
                  <Badge variant="outline" class="text-[10px] capitalize">
                    {{ booking.tipe === 'ruangan' ? 'Ruangan' : `Barang (${booking.jumlah || 1} unit)` }}
                  </Badge>
                  <span class="text-[11px] text-slate-400">#{{ booking.id }}</span>
                </div>
              </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
              <!-- Download Surat PDF (self-service: sejak diajukan, kecuali ditolak) -->
              <a
                v-if="booking.status !== 'ditolak'"
                :href="'/bookings/' + booking.id + '/pdf'"
                target="_blank"
                class="inline-flex h-8 items-center gap-1.5 bg-primary px-3 text-[11px] font-semibold text-primary-foreground rounded-lg shadow-xs transition-all duration-200 hover:opacity-90"
                title="Download Surat Peminjaman"
              >
                <FileText class="h-3.5 w-3.5" />
                Download Surat
              </a>

              <!-- Cetak Bukti (sejak diajukan, kecuali ditolak) -->
              <a
                v-if="booking.status !== 'ditolak'"
                :href="'/bookings/' + booking.id + '/pdf'"
                target="_blank"
                class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                title="Cetak Bukti Peminjaman"
              >
                <Printer class="h-3.5 w-3.5" />
                Cetak
              </a>

              <Badge
                :class="[
                  getStatusConfig(booking.status).bgColor,
                  getStatusConfig(booking.status).color,
                  getStatusConfig(booking.status).borderColor,
                  'text-[11px] font-semibold shrink-0',
                ]"
              >
                <component :is="getStatusConfig(booking.status).icon" class="h-3 w-3 mr-1" />
                {{ getStatusConfig(booking.status).label }}
              </Badge>
            </div>
          </div>

          <!-- Schedule & Keterangan Info -->
          <div class="px-5 pb-3 flex flex-wrap items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1">
              <CalendarDays class="h-3.5 w-3.5 text-slate-400" />
              {{ formatDate(booking.tanggal_mulai) }} — {{ formatDate(booking.tanggal_selesai) }}
            </span>
            <span class="flex items-center gap-1">
              <Clock class="h-3.5 w-3.5 text-slate-400" />
              {{ formatTime(booking.jam_mulai) }} – {{ formatTime(booking.jam_selesai) }}
            </span>
            <span v-if="booking.keterangan" class="flex items-center gap-1">
              <FileText class="h-3.5 w-3.5 text-slate-400" />
              {{ booking.keterangan }}
            </span>
          </div>

          <!-- Alasan Sistem (bila ada) -->
          <div v-if="booking.alasan_sistem" class="px-5 pb-3 text-xs text-red-600 bg-red-50/50 py-1.5 mx-5 mb-3 rounded-md border border-red-200">
            <strong>Catatan Sistem:</strong> {{ booking.alasan_sistem }}
          </div>

          <!-- Timeline -->
          <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-4">
            <!-- REJECTED Timeline -->
            <div v-if="booking.status === 'ditolak'" class="space-y-2.5">
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-500">
                    <CheckCircle2 class="h-3.5 w-3.5" />
                  </div>
                  <span class="text-xs font-medium text-red-600">Diajukan</span>
                </div>

                <div class="h-px flex-1 bg-red-200 relative">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <XCircle class="h-4 w-4 text-red-400 bg-slate-50 rounded-full" />
                  </div>
                </div>

                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white">
                    <XCircle class="h-3.5 w-3.5" />
                  </div>
                  <span class="text-xs font-bold text-red-600">Ditolak</span>
                </div>
              </div>
            </div>

            <!-- NORMAL Timeline -->
            <div v-else class="flex items-center gap-0">
              <template v-for="(step, idx) in timelineSteps" :key="step.key">
                <div class="flex items-center gap-2 shrink-0">
                  <div
                    :class="[
                      'flex h-7 w-7 items-center justify-center rounded-full transition-colors text-xs font-bold',
                      getStatusConfig(booking.status).step >= step.stepNum
                        ? step.stepNum === 3
                          ? 'bg-emerald-500 text-white'
                          : 'bg-blue-500 text-white'
                        : 'bg-slate-200 text-slate-400',
                    ]"
                  >
                    <CheckCircle2
                      v-if="getStatusConfig(booking.status).step >= step.stepNum"
                      class="h-3.5 w-3.5"
                    />
                    <span v-else>{{ step.stepNum }}</span>
                  </div>
                  <span
                    :class="[
                      'text-xs font-medium hidden sm:inline',
                      getStatusConfig(booking.status).step >= step.stepNum
                        ? step.stepNum === 3 ? 'text-emerald-600' : 'text-blue-600'
                        : 'text-slate-400',
                    ]"
                  >
                    {{ step.label }}
                  </span>
                </div>

                <div
                  v-if="idx < timelineSteps.length - 1"
                  :class="[
                    'h-px flex-1 mx-2 transition-colors',
                    getStatusConfig(booking.status).step > step.stepNum
                      ? 'bg-blue-400'
                      : 'bg-slate-200',
                  ]"
                />
              </template>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <Pagination
      v-if="bookings.data.length > 0"
      :meta="bookings"
      class="mt-4 rounded-2xl border border-slate-200 bg-white"
    />

    <!-- Empty State -->
    <div v-else class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
      <ClipboardList class="mx-auto h-12 w-12 text-slate-300" />
      <p class="mt-4 text-base font-semibold text-slate-500">Belum ada peminjaman ditemukan</p>
      <p class="mt-1 text-sm text-slate-400">
        Silakan ajukan peminjaman melalui katalog Ruangan atau Barang.
      </p>
    </div>
  </div>
</template>
