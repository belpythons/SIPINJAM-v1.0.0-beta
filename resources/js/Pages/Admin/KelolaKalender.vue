<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  CalendarDays,
  Trash2,
  CheckCircle2,
  FileImage,
  FileText,
  Upload,
  Star,
  Archive,
  ImageOff,
} from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
  calendars: Array,
});

const form = useForm({
  image: null,
  year: new Date().getFullYear(),
});

const fileInputRef = ref(null);
const filePreview = ref(null);
const isPdf = ref(false);

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.image = file;
    isPdf.value = file.type === 'application/pdf';
    if (!isPdf.value) {
      const reader = new FileReader();
      reader.onload = (event) => {
        filePreview.value = event.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      filePreview.value = null;
    }
  }
};

const submit = () => {
  form.post('/admin/kelola-kalender', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      filePreview.value = null;
      isPdf.value = false;
      if (fileInputRef.value) fileInputRef.value.value = '';
    },
  });
};

const activateCalendar = (id) => {
  form.patch(`/admin/kelola-kalender/${id}/activate`, {
    preserveScroll: true,
  });
};

const deleteCalendar = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus kalender ini?')) {
    form.delete(`/admin/kelola-kalender/${id}`, {
      preserveScroll: true,
    });
  }
};

const failedImages = ref(new Set());
const handleImageError = (id) => {
  failedImages.value.add(id);
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

const isImage = (path) => {
  if (!path) return false;
  return !path.toLowerCase().endsWith('.pdf');
};
</script>

<template>
  <Head title="Kelola Kalender" />

  <div class="px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
          <CalendarDays class="h-5 w-5 text-primary" />
        </div>
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-foreground">Kelola Kalender Akademik</h1>
          <p class="text-sm text-muted-foreground font-medium">
            Unggah, aktifkan, dan kelola kalender akademik aktif untuk mahasiswa.
          </p>
        </div>
      </div>
    </div>

    <!-- Upload Form Card (Clean Minimalist) -->
    <div class="mb-10 bg-card border border-border rounded-2xl shadow-card overflow-hidden">
      <div class="bg-muted/50 border-b border-border px-6 py-4">
        <h2 class="text-base font-semibold text-foreground">Unggah Kalender Baru</h2>
      </div>
      <div class="p-6">
        <form @submit.prevent="submit" class="flex flex-col lg:flex-row gap-6 items-start">
          <!-- Year -->
          <div class="space-y-2 w-full lg:w-48">
            <label for="year" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tahun Akademik</label>
            <input
              id="year"
              v-model="form.year"
              type="number"
              min="2020"
              max="2100"
              class="w-full px-4 py-2.5 rounded-lg border border-border text-sm font-medium bg-background text-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
            />
            <p v-if="form.errors.year" class="text-xs text-destructive font-medium">{{ form.errors.year }}</p>
          </div>

          <!-- File Upload -->
          <div class="space-y-2 flex-1 w-full">
            <label class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Berkas Kalender</label>
            <div
              @click="$refs.fileInputRef.click()"
              class="border border-dashed border-border rounded-lg hover:border-primary p-6 text-center cursor-pointer transition-all bg-muted/30 hover:bg-muted/50 flex flex-col items-center justify-center min-h-[100px]"
            >
              <Upload class="h-8 w-8 text-muted-foreground mb-2" />
              <p class="text-xs font-medium text-foreground">Klik untuk memilih berkas</p>
              <p class="text-[10px] text-muted-foreground mt-1 font-medium">JPEG, PNG, WEBP, PDF (Maks. 5MB)</p>
            </div>
            <input
              ref="fileInputRef"
              type="file"
              accept="image/*,application/pdf"
              class="hidden"
              @change="handleFileChange"
            />
            <p v-if="form.errors.image" class="text-xs text-destructive font-medium">{{ form.errors.image }}</p>

            <!-- File Preview -->
            <div v-if="form.image" class="mt-2 p-3 bg-muted/50 border border-border rounded-lg space-y-2">
              <div class="flex items-center justify-between text-xs text-foreground font-medium">
                <span class="font-bold truncate max-w-[80%]">{{ form.image.name }}</span>
                <span class="text-[10px] text-muted-foreground font-medium shrink-0">({{ (form.image.size / (1024*1024)).toFixed(2) }} MB)</span>
              </div>
              <div v-if="filePreview" class="relative mt-1 overflow-hidden border border-border rounded-lg max-h-24 bg-card flex items-center justify-center">
                <img :src="filePreview" class="max-h-20 object-contain" />
              </div>
              <div v-if="isPdf" class="mt-1 flex items-center gap-2 p-2 bg-destructive/10 border border-destructive/20 rounded-lg text-destructive text-xs font-semibold">
                <FileText class="h-4 w-4 shrink-0" />
                <span>Dokumen PDF terpilih</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="w-full lg:w-auto lg:pt-6">
            <button
              type="submit"
              :disabled="form.processing || !form.image"
              class="w-full lg:w-auto px-6 py-2.5 bg-primary text-primary-foreground font-semibold text-sm rounded-lg shadow-sm transition-all hover:bg-primary/90 focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ form.processing ? 'Mengunggah...' : 'Unggah' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Calendar Grid Cards -->
    <div class="mb-6 flex items-center gap-2">
      <h2 class="text-lg font-bold text-foreground">Daftar Kalender</h2>
      <span class="bg-muted text-muted-foreground text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ calendars.length }}</span>
    </div>

    <div v-if="calendars.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="cal in calendars"
        :key="cal.id"
        class="bg-card border border-border rounded-2xl shadow-card overflow-hidden flex flex-col transition-all duration-200 hover:shadow-md hover:-translate-y-0.5"
      >
        <!-- Image Thumbnail Area -->
        <div class="relative h-48 border-b border-border overflow-hidden flex items-center justify-center bg-card">
          <!-- Image Thumbnail -->
          <img
            v-if="isImage(cal.image_path) && !failedImages.has(cal.id)"
            :src="getImageUrl(cal.image_path)"
            :alt="'Kalender ' + cal.year"
            class="w-full h-full object-cover"
            @error="handleImageError(cal.id)"
          />
          <div v-else-if="failedImages.has(cal.id)" class="flex flex-col items-center justify-center py-6 px-4 bg-slate-50 border border-slate-200 border-dashed rounded-xl w-[90%] h-[90%] gap-2">
            <ImageOff class="h-10 w-10 text-slate-300" />
            <span class="text-xs font-semibold text-slate-400">Gambar tidak dapat dimuat</span>
          </div>
          <!-- PDF Placeholder -->
          <div v-else class="flex flex-col items-center justify-center gap-2 text-gray-400">
            <FileText class="h-16 w-16 stroke-[1.5]" />
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dokumen PDF</span>
          </div>

          <!-- Status Badge Overlay -->
          <div class="absolute top-3 right-3">
            <div
              v-if="cal.is_active"
              class="inline-flex items-center gap-1.5 bg-emerald-500 text-white text-xs font-semibold rounded-full px-3 py-1 shadow-sm"
            >
              <Star class="h-3.5 w-3.5 fill-current" />
              Aktif
            </div>
            <div
              v-else
              class="inline-flex items-center gap-1.5 bg-muted text-muted-foreground text-xs font-semibold rounded-full px-3 py-1 border border-border shadow-sm"
            >
              <Archive class="h-3.5 w-3.5" />
              Arsip
            </div>
          </div>
        </div>

        <!-- Card Content -->
        <div class="p-5 flex-1 flex flex-col">
          <!-- Year Title -->
          <div class="mb-3">
            <h3 class="text-lg font-bold text-foreground">Tahun {{ cal.year }}</h3>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-muted-foreground font-medium">
              <component
                :is="isImage(cal.image_path) ? FileImage : FileText"
                class="h-3.5 w-3.5"
              />
              {{ isImage(cal.image_path) ? 'Gambar' : 'Dokumen PDF' }}
            </div>
          </div>

          <!-- Spacer -->
          <div class="flex-1" />

          <!-- Actions -->
          <div class="flex items-center gap-2 pt-4 border-t border-border mt-3">
            <!-- Activate Button -->
            <button
              v-if="!cal.is_active"
              @click="activateCalendar(cal.id)"
              class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all shadow-sm"
            >
              <CheckCircle2 class="h-4 w-4" />
              Aktifkan
            </button>
            <div
              v-else
              class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-lg cursor-default"
            >
              <CheckCircle2 class="h-4 w-4" />
              Sedang Aktif
            </div>

            <!-- Delete Button -->
            <button
              @click="deleteCalendar(cal.id)"
              class="flex items-center justify-center gap-1.5 py-2 px-3 bg-destructive hover:bg-destructive/90 text-destructive-foreground text-xs font-semibold rounded-lg transition-all shadow-sm"
            >
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-card border border-dashed border-border rounded-2xl py-20 text-center">
      <CalendarDays class="mx-auto h-12 w-12 text-gray-300 stroke-[1.5]" />
      <p class="mt-4 text-sm text-muted-foreground font-semibold">Belum ada kalender akademik</p>
      <p class="text-xs text-muted-foreground/80 mt-1 font-medium">Unggah kalender pertama menggunakan form di atas.</p>
    </div>
  </div>
</template>
