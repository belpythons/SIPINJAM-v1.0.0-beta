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

const getImageUrl = (path) => {
  if (!path) return '';
  return '/storage/' + path;
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
        <div class="w-10 h-10 bg-orange-400 border-4 border-black shadow-[3px_3px_0px_rgba(0,0,0,1)] flex items-center justify-center">
          <CalendarDays class="h-5 w-5 text-black stroke-[2.5]" />
        </div>
        <div>
          <h1 class="text-2xl font-black tracking-tight text-black">Kelola Kalender Akademik</h1>
          <p class="text-sm text-gray-600 font-semibold">
            Unggah, aktifkan, dan kelola kalender akademik aktif untuk mahasiswa.
          </p>
        </div>
      </div>
    </div>

    <!-- Upload Form Card (Neo-Brutalist) -->
    <div class="mb-10 bg-white border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)] overflow-hidden">
      <div class="bg-orange-400 border-b-4 border-black px-6 py-4">
        <h2 class="text-base font-black text-black uppercase tracking-wider">Unggah Kalender Baru</h2>
      </div>
      <div class="p-6">
        <form @submit.prevent="submit" class="flex flex-col lg:flex-row gap-6 items-start">
          <!-- Year -->
          <div class="space-y-2 w-full lg:w-48">
            <label for="year" class="text-xs font-black text-black uppercase tracking-wider">Tahun Akademik</label>
            <input
              id="year"
              v-model="form.year"
              type="number"
              min="2020"
              max="2100"
              class="w-full px-4 py-3 border-4 border-black text-sm font-bold bg-white outline-none focus:ring-4 focus:ring-orange-300 focus:border-orange-500 transition-all"
            />
            <p v-if="form.errors.year" class="text-xs text-red-600 font-bold">{{ form.errors.year }}</p>
          </div>

          <!-- File Upload -->
          <div class="space-y-2 flex-1 w-full">
            <label class="text-xs font-black text-black uppercase tracking-wider">Berkas Kalender</label>
            <div
              @click="$refs.fileInputRef.click()"
              class="border-4 border-dashed border-black hover:border-orange-500 p-6 text-center cursor-pointer transition-colors bg-gray-50 hover:bg-orange-50 flex flex-col items-center justify-center min-h-[100px]"
            >
              <Upload class="h-8 w-8 text-gray-500 mb-2" />
              <p class="text-xs font-bold text-gray-700">Klik untuk memilih berkas</p>
              <p class="text-[10px] text-gray-400 mt-1 font-semibold">JPEG, PNG, WEBP, PDF (Maks. 5MB)</p>
            </div>
            <input
              ref="fileInputRef"
              type="file"
              accept="image/*,application/pdf"
              class="hidden"
              @change="handleFileChange"
            />
            <p v-if="form.errors.image" class="text-xs text-red-600 font-bold">{{ form.errors.image }}</p>

            <!-- File Preview -->
            <div v-if="form.image" class="mt-2 p-3 bg-gray-50 border-4 border-black space-y-2">
              <div class="flex items-center justify-between text-xs text-gray-700">
                <span class="font-bold truncate max-w-[80%]">{{ form.image.name }}</span>
                <span class="text-[10px] text-gray-400 font-bold shrink-0">({{ (form.image.size / (1024*1024)).toFixed(2) }} MB)</span>
              </div>
              <div v-if="filePreview" class="relative mt-1 overflow-hidden border-2 border-black max-h-24 bg-white flex items-center justify-center">
                <img :src="filePreview" class="max-h-20 object-contain" />
              </div>
              <div v-if="isPdf" class="mt-1 flex items-center gap-2 p-2 bg-red-100 border-2 border-black text-red-800 text-xs font-bold">
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
              class="w-full lg:w-auto px-6 py-3 bg-orange-500 text-white font-black text-sm uppercase tracking-wider border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ form.processing ? 'Mengunggah...' : 'Unggah' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Calendar Grid Cards -->
    <div class="mb-6 flex items-center gap-2">
      <h2 class="text-lg font-black text-black uppercase tracking-wider">Daftar Kalender</h2>
      <span class="bg-black text-white text-xs font-black px-3 py-1">{{ calendars.length }}</span>
    </div>

    <div v-if="calendars.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="cal in calendars"
        :key="cal.id"
        class="bg-white border-4 border-black shadow-[6px_6px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_rgba(0,0,0,1)]"
      >
        <!-- Image Thumbnail Area -->
        <div class="relative h-48 bg-gray-100 border-b-4 border-black overflow-hidden flex items-center justify-center">
          <!-- Image Thumbnail -->
          <img
            v-if="isImage(cal.image_path)"
            :src="getImageUrl(cal.image_path)"
            :alt="'Kalender ' + cal.year"
            class="w-full h-full object-cover"
          />
          <!-- PDF Placeholder -->
          <div v-else class="flex flex-col items-center justify-center gap-2 text-gray-400">
            <FileText class="h-16 w-16 stroke-[1.5]" />
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dokumen PDF</span>
          </div>

          <!-- Status Badge Overlay -->
          <div class="absolute top-3 right-3">
            <div
              v-if="cal.is_active"
              class="inline-flex items-center gap-1.5 bg-green-400 text-black text-xs font-black uppercase tracking-wider px-3 py-1.5 border-3 border-black shadow-[3px_3px_0px_rgba(0,0,0,1)]"
            >
              <Star class="h-3.5 w-3.5 fill-current" />
              Aktif
            </div>
            <div
              v-else
              class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-600 text-xs font-black uppercase tracking-wider px-3 py-1.5 border-3 border-black shadow-[2px_2px_0px_rgba(0,0,0,1)]"
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
            <h3 class="text-xl font-black text-black">Tahun {{ cal.year }}</h3>
            <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 font-semibold">
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
          <div class="flex items-center gap-2 pt-4 border-t-4 border-black mt-3">
            <!-- Activate Button -->
            <button
              v-if="!cal.is_active"
              @click="activateCalendar(cal.id)"
              class="flex-1 flex items-center justify-center gap-1.5 py-2.5 bg-green-400 text-black text-xs font-black uppercase tracking-wider border-3 border-black shadow-[3px_3px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)]"
            >
              <CheckCircle2 class="h-4 w-4 stroke-[2.5]" />
              Aktifkan
            </button>
            <div
              v-else
              class="flex-1 flex items-center justify-center gap-1.5 py-2.5 bg-green-100 text-green-800 text-xs font-black uppercase tracking-wider border-3 border-green-400 cursor-default"
            >
              <CheckCircle2 class="h-4 w-4 stroke-[2.5]" />
              Sedang Aktif
            </div>

            <!-- Delete Button -->
            <button
              @click="deleteCalendar(cal.id)"
              class="flex items-center justify-center gap-1.5 py-2.5 px-4 bg-red-500 text-white text-xs font-black uppercase tracking-wider border-3 border-black shadow-[3px_3px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)]"
            >
              <Trash2 class="h-4 w-4 stroke-[2.5]" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="bg-white border-4 border-dashed border-black py-20 text-center">
      <CalendarDays class="mx-auto h-12 w-12 text-gray-300 stroke-[1.5]" />
      <p class="mt-4 text-sm text-gray-500 font-bold">Belum ada kalender akademik</p>
      <p class="text-xs text-gray-400 mt-1">Unggah kalender pertama menggunakan form di atas.</p>
    </div>
  </div>
</template>
