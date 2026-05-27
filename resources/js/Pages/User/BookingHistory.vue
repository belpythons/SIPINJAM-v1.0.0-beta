<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  ClipboardList,
  Clock,
  CheckCircle2,
  XCircle,
  PackageCheck,
  CalendarDays,
  Building2,
  Package,
  AlertCircle,
  FileText,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

defineOptions({ layout: UserLayout });

const props = defineProps({
  bookings: Array,
  stats: Object,
});

const page = usePage();

// ── Status Helpers ─────────────────────────────────
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

// ── Timeline Steps Definition ──────────────────────
const timelineSteps = [
  { key: 'pending',  label: 'Diajukan',  stepNum: 1 },
  { key: 'approved', label: 'Disetujui', stepNum: 2 },
  { key: 'done',     label: 'Selesai',   stepNum: 3 },
];
</script>

<template>
  <Head title="Riwayat Peminjaman" />

  <div class="px-6 py-8 lg:px-10">
    <!-- ── Page Header ──────────────────────────────── -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <ClipboardList class="h-5 w-5 text-blue-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Peminjaman</h1>
      </div>
      <p class="text-sm text-slate-500">
        Daftar seluruh pengajuan peminjaman aset Anda beserta status terkini.
      </p>
    </div>

    <!-- ── Summary Stats ────────────────────────────── -->
    <div class="mb-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div class="rounded-xl border border-slate-200 bg-white p-4 text-center">
        <p class="text-2xl font-bold text-slate-900">{{ stats.total }}</p>
        <p class="text-xs text-slate-500 mt-1">Total</p>
      </div>
      <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ stats.pending }}</p>
        <p class="text-xs text-amber-600/70 mt-1">Menunggu</p>
      </div>
      <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ stats.approved }}</p>
        <p class="text-xs text-blue-600/70 mt-1">Disetujui</p>
      </div>
      <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-center">
        <p class="text-2xl font-bold text-emerald-600">{{ stats.completed }}</p>
        <p class="text-xs text-emerald-600/70 mt-1">Selesai</p>
      </div>
    </div>

    <!-- ── Booking Cards ────────────────────────────── -->
    <div v-if="bookings.length > 0" class="space-y-4">
      <Card
        v-for="booking in bookings"
        :key="booking.id"
        :class="[
          'overflow-hidden transition-all duration-200 hover:shadow-md',
          booking.status === 'ditolak' ? 'border-red-200' : 'border-slate-200/80',
        ]"
      >
        <CardContent class="p-0">
          <!-- Card Top: Item Info + Status Badge -->
          <div class="flex items-start justify-between gap-4 p-5 pb-3">
            <div class="flex items-start gap-3 min-w-0">
              <div
                :class="[
                  'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                  booking.tipe === 'ruangan' ? 'bg-indigo-50 text-indigo-600' : 'bg-blue-50 text-blue-600',
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
                  <Badge variant="outline" class="text-[10px] capitalize">{{ booking.tipe }}</Badge>
                  <span class="text-[11px] text-slate-400">#{{ booking.id }}</span>
                </div>
              </div>
            </div>
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

          <!-- Schedule Info -->
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

          <!-- ── Visual Progress Timeline ───────────── -->
          <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-4">
            <!-- REJECTED: Red broken timeline -->
            <div v-if="booking.status === 'ditolak'" class="space-y-2.5">
              <div class="flex items-center gap-3">
                <!-- Step 1: Submitted (was done) -->
                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-red-500">
                    <CheckCircle2 class="h-3.5 w-3.5" />
                  </div>
                  <span class="text-xs font-medium text-red-600">Diajukan</span>
                </div>

                <!-- Connector (broken) -->
                <div class="h-px flex-1 bg-red-200 relative">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <XCircle class="h-4 w-4 text-red-400 bg-slate-50 rounded-full" />
                  </div>
                </div>

                <!-- Step: Rejected -->
                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white">
                    <XCircle class="h-3.5 w-3.5" />
                  </div>
                  <span class="text-xs font-bold text-red-600">Ditolak</span>
                </div>
              </div>

              <!-- Rejection note -->
              <div
                v-if="booking.keterangan && booking.keterangan.includes('Dibatalkan sistem')"
                class="flex items-start gap-2 rounded-lg bg-red-50 border border-red-100 px-3 py-2"
              >
                <AlertCircle class="h-3.5 w-3.5 mt-0.5 shrink-0 text-red-400" />
                <p class="text-[11px] text-red-600 leading-relaxed">
                  {{ booking.keterangan }}
                </p>
              </div>
            </div>

            <!-- NORMAL: Green/Blue progressive timeline -->
            <div v-else class="flex items-center gap-0">
              <template v-for="(step, idx) in timelineSteps" :key="step.key">
                <!-- Step circle -->
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

                <!-- Connector line -->
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

            <!-- Timestamps -->
            <div class="mt-2.5 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-slate-400">
              <span>Diajukan: {{ formatDate(booking.created_at) }}</span>
              <span v-if="booking.approved_at">Disetujui: {{ formatDate(booking.approved_at) }}</span>
              <span v-if="booking.completed_at">Selesai: {{ formatDate(booking.completed_at) }}</span>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State -->
    <div v-else class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
      <ClipboardList class="mx-auto h-12 w-12 text-slate-300" />
      <p class="mt-4 text-base font-semibold text-slate-500">Belum ada peminjaman</p>
      <p class="mt-1 text-sm text-slate-400">
        Mulai ajukan peminjaman dari halaman Dashboard.
      </p>
    </div>
  </div>
</template>
