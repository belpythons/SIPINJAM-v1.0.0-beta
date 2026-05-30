<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  CalendarDays,
  Download,
  ImageIcon,
  ImageOff,
} from '@lucide/vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
  calendar: Object,
});

const imageError = ref(false);
const handleImageError = () => {
  imageError.value = true;
};

const getImageUrl = (path) => {
  if (!path) return '';
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
  <Head title="Kalender Akademik" />

  <div class="px-6 py-8 lg:px-10 max-w-4xl">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
            <CalendarDays class="h-5 w-5 text-primary" />
          </div>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Kalender Akademik</h1>
            <p class="text-xs text-muted-foreground">
              Kalender akademik aktif STITEK Bontang (gambar statis)
            </p>
          </div>
        </div>
      </div>

      <!-- Export PDF Button -->
      <a
        v-if="calendar"
        href="/kalender/export-pdf"
        class="inline-flex items-center justify-center gap-2 bg-primary text-primary-foreground px-4 py-2.5 text-sm font-semibold rounded-lg shadow-sm transition-all duration-200 hover:opacity-90 sm:self-center"
      >
        <Download class="h-4 w-4" />
        Unduh PDF
      </a>
    </div>

    <!-- Calendar Image Card (Static) -->
    <div v-if="calendar" class="bg-card border border-border rounded-xl shadow-card overflow-hidden">
      <div class="bg-muted px-6 py-4 border-b border-border">
        <h2 class="text-base font-bold text-foreground">Kalender Akademik Tahun {{ calendar.year }}</h2>
        <p class="text-xs text-muted-foreground mt-0.5">Sekolah Tinggi Teknologi Bontang</p>
      </div>

      <div class="p-6">
        <div class="relative overflow-hidden rounded-lg flex items-center justify-center min-h-[300px]">
          <template v-if="!imageError">
            <img
              :src="getImageUrl(calendar.image_path)"
              alt="Kalender Akademik"
              class="max-w-full h-auto object-contain select-none max-h-[80vh] py-4"
              @error="handleImageError"
            />
          </template>
          <div v-else class="flex flex-col items-center justify-center py-20 bg-slate-50 border border-slate-200 border-dashed rounded-xl w-full mx-6 my-6 gap-3">
            <ImageOff class="h-12 w-12 text-slate-300" />
            <span class="text-sm font-semibold text-slate-400">Gagal memuat gambar kalender</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-card border border-border rounded-xl shadow-card py-20 text-center">
      <div class="w-16 h-16 bg-muted rounded-full mx-auto flex items-center justify-center mb-4">
        <ImageIcon class="h-8 w-8 text-muted-foreground" />
      </div>
      <p class="text-base font-bold text-foreground">Belum ada kalender akademik aktif</p>
      <p class="mt-1 text-sm text-muted-foreground">
        Silakan hubungi administrator kampus untuk mengunggah kalender akademik.
      </p>
    </div>
  </div>
</template>
