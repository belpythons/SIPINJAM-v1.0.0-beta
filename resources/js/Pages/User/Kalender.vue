<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  CalendarDays,
  Download,
  AlertCircle,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';

defineOptions({ layout: UserLayout });

const props = defineProps({
  calendar: Object,
});
</script>

<template>
  <Head title="Kalender Akademik" />

  <div class="px-6 py-8 lg:px-10 max-w-4xl">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <CalendarDays class="h-5 w-5 text-blue-500" />
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kalender Akademik</h1>
        </div>
        <p class="text-sm text-slate-500">
          Kalender akademik aktif Sekolah Tinggi Teknologi (STITEK) Bontang.
        </p>
      </div>

      <!-- Export PDF Button (Only if calendar is active) -->
      <a
        v-if="calendar"
        href="/kalender/export-pdf"
        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors sm:self-center"
      >
        <Download class="h-4 w-4" />
        Unduh PDF
      </a>
    </div>

    <!-- Calendar View Card -->
    <Card v-if="calendar" class="border-slate-200 shadow-sm overflow-hidden bg-white">
      <CardContent class="p-6">
        <div class="text-center mb-6">
          <h2 class="text-base font-bold text-slate-800">Kalender Akademik Tahun {{ calendar.year }}</h2>
          <p class="text-xs text-slate-400 mt-0.5">Sekolah Tinggi Teknologi Bontang</p>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-slate-100 flex items-center justify-center bg-slate-50">
          <!-- Calendar Image -->
          <img
            :src="'/storage/' + calendar.image_path"
            alt="Kalender Akademik"
            class="max-w-full h-auto object-contain select-none max-h-[80vh] py-4"
          />
        </div>
      </CardContent>
    </Card>

    <!-- Empty State -->
    <div v-else class="rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
      <CalendarDays class="mx-auto h-12 w-12 text-slate-300" />
      <p class="mt-4 text-base font-semibold text-slate-500">Belum ada kalender akademik aktif</p>
      <p class="mt-1 text-sm text-slate-400">
        Silakan hubungi administrator kampus untuk mengunggah kalender akademik.
      </p>
    </div>
  </div>
</template>
