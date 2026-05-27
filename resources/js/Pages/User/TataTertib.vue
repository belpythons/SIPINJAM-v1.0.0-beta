<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  ShieldCheck,
  FileText,
  Clock,
  ThumbsUp,
  AlertTriangle,
  Info,
} from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineOptions({ layout: UserLayout });

const rules = [
  {
    title: 'Prosedur Pengajuan',
    icon: FileText,
    color: 'text-blue-600',
    bg: 'bg-blue-50',
    desc: 'Pengajuan peminjaman ruangan atau barang wajib dilakukan maksimal 2 hari sebelum waktu penggunaan melalui aplikasi SiPinjam.',
  },
  {
    title: 'Waktu Penggunaan',
    icon: Clock,
    color: 'text-amber-600',
    bg: 'bg-amber-50',
    desc: 'Penggunaan ruangan & barang inventaris disesuaikan dengan jadwal akademik STITEK Bontang dan persetujuan dari pihak Biro Administrasi.',
  },
  {
    title: 'Tanggung Jawab Aset',
    icon: ThumbsUp,
    color: 'text-emerald-600',
    bg: 'bg-emerald-50',
    desc: 'Peminjam bertanggung jawab penuh atas kebersihan, ketertiban, dan keutuhan aset selama masa penggunaan hingga pengembalian selesai.',
  },
  {
    title: 'Pelanggaran & Sanksi',
    icon: AlertTriangle,
    color: 'text-rose-600',
    bg: 'bg-rose-50',
    desc: 'Keterlambatan pengembalian barang atau kerusakan fasilitas kampus akibat kelalaian peminjam dapat dikenakan sanksi pemblokiran izin pinjam.',
  },
];
</script>

<template>
  <Head title="Tata Tertib Peminjaman" />

  <div class="px-6 py-8 lg:px-10 max-w-4xl">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <ShieldCheck class="h-5 w-5 text-blue-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tata Tertib Peminjaman</h1>
      </div>
      <p class="text-sm text-slate-500">
        Panduan dan tata tertib resmi peminjaman aset kampus STITEK Bontang.
      </p>
    </div>

    <!-- Main Card -->
    <Card class="border-slate-200 shadow-sm overflow-hidden mb-8">
      <CardHeader class="bg-slate-50/50 border-b border-slate-100 py-4 px-6 flex flex-row items-center gap-2">
        <Info class="h-4.5 w-4.5 text-blue-500" />
        <CardTitle class="text-sm font-bold text-slate-800">Ketentuan Umum Peminjam</CardTitle>
      </CardHeader>
      <CardContent class="p-6 space-y-6">
        <p class="text-xs leading-relaxed text-slate-600">
          Untuk menjaga kelancaran kegiatan akademik dan non-akademik di lingkungan kampus Sekolah Tinggi Teknologi (STITEK) Bontang, seluruh civitas akademika wajib mematuhi aturan dan tata tertib peminjaman sarana dan prasarana di bawah ini:
        </p>

        <!-- Rules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="rule in rules"
            :key="rule.title"
            class="flex items-start gap-4 p-4 rounded-xl border border-slate-100 hover:shadow-sm transition-shadow bg-white"
          >
            <div :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-lg', rule.bg]">
              <component :is="rule.icon" :class="['h-5 w-5', rule.color]" />
            </div>
            <div>
              <h3 class="text-sm font-bold text-slate-800">{{ rule.title }}</h3>
              <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ rule.desc }}</p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Tagline Info -->
    <div class="text-center">
      <p class="text-[10px] tracking-[0.25em] uppercase font-semibold text-slate-400">
        The Knowledgeable and Virtue Campus
      </p>
    </div>
  </div>
</template>
