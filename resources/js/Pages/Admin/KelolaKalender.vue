<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  CalendarDays,
  Plus,
  Trash2,
  CheckCircle2,
  AlertCircle,
  FileImage,
  FileText,
  Upload,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';

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
</script>

<template>
  <Head title="Kelola Kalender" />

  <div class="px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <CalendarDays class="h-5 w-5 text-orange-500" />
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Kalender Akademik</h1>
      </div>
      <p class="text-sm text-slate-500">
        Unggah, aktifkan, dan kelola kalender akademik aktif untuk mahasiswa.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left 2 Cols: Table list -->
      <div class="lg:col-span-2 space-y-6">
        <Card class="border-slate-200 shadow-sm bg-white overflow-hidden">
          <div class="p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Daftar Kalender Akademik</h2>

            <div v-if="calendars.length" class="overflow-x-auto">
              <table class="w-full text-left border-collapse text-xs">
                <thead>
                  <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                    <th class="py-3 px-4">Tahun</th>
                    <th class="py-3 px-4">Tipe Berkas</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                  <tr v-for="cal in calendars" :key="cal.id" class="hover:bg-slate-50/40">
                    <td class="py-3.5 px-4 font-bold text-slate-900">Tahun {{ cal.year }}</td>
                    <td class="py-3.5 px-4 font-medium text-slate-500">
                      <span class="flex items-center gap-1.5">
                        <component
                          :is="cal.image_path.toLowerCase().endsWith('.pdf') ? FileText : FileImage"
                          class="h-4 w-4 text-slate-400"
                        />
                        {{ cal.image_path.toLowerCase().endsWith('.pdf') ? 'Dokumen PDF' : 'Gambar' }}
                      </span>
                    </td>
                    <td class="py-3.5 px-4">
                      <Badge
                        v-if="cal.is_active"
                        class="bg-emerald-50 text-emerald-700 border-emerald-200 text-[10px] font-semibold"
                      >
                        Aktif
                      </Badge>
                      <Badge
                        v-else
                        class="bg-slate-100 text-slate-500 border-slate-200 text-[10px] font-normal"
                      >
                        Tidak Aktif
                      </Badge>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <!-- Activate Button -->
                        <Button
                          v-if="!cal.is_active"
                          size="xs"
                          variant="outline"
                          @click="activateCalendar(cal.id)"
                          class="h-7 text-[10px] text-emerald-600 hover:text-emerald-700 border-emerald-200 hover:bg-emerald-50"
                        >
                          Aktifkan
                        </Button>
                        
                        <!-- Delete Button -->
                        <Button
                          size="xs"
                          variant="ghost"
                          @click="deleteCalendar(cal.id)"
                          class="h-7 text-red-500 hover:text-red-600 hover:bg-red-50"
                        >
                          <Trash2 class="h-3.5 w-3.5" />
                        </Button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 text-center">
              <CalendarDays class="mx-auto h-10 w-10 text-slate-300" />
              <p class="mt-3 text-sm text-slate-400">Belum ada kalender akademik</p>
            </div>
          </div>
        </Card>
      </div>

      <!-- Right 1 Col: Upload form -->
      <div>
        <Card class="border-slate-200 shadow-sm bg-white overflow-hidden">
          <div class="p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Unggah Kalender Baru</h2>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Year -->
              <div class="space-y-2">
                <Label for="year" class="text-xs font-semibold text-slate-600">Tahun Akademik</Label>
                <Input
                  id="year"
                  v-model="form.year"
                  type="number"
                  min="2020"
                  max="2100"
                  class="text-sm border-slate-200 focus:ring-orange-500/20 focus:border-orange-500"
                />
                <p v-if="form.errors.year" class="text-xs text-red-500">{{ form.errors.year }}</p>
              </div>

              <!-- Upload field -->
              <div class="space-y-2">
                <Label class="text-xs font-semibold text-slate-600">Berkas Kalender (Gambar/PDF)</Label>
                
                <div
                  @click="$refs.fileInputRef.click()"
                  class="border-2 border-dashed border-slate-200 hover:border-orange-400 rounded-xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50 hover:bg-orange-50/10 flex flex-col items-center justify-center min-h-[140px]"
                >
                  <Upload class="h-8 w-8 text-slate-400 mb-2 group-hover:text-orange-500" />
                  <p class="text-xs font-medium text-slate-600">Klik untuk memilih berkas</p>
                  <p class="text-[10px] text-slate-400 mt-1">Mendukung JPEG, PNG, WEBP, PDF (Maks. 5MB)</p>
                </div>

                <input
                  ref="fileInputRef"
                  type="file"
                  accept="image/*,application/pdf"
                  class="hidden"
                  @change="handleFileChange"
                />

                <p v-if="form.errors.image" class="text-xs text-red-500">{{ form.errors.image }}</p>
              </div>

              <!-- Selected File Preview -->
              <div v-if="form.image" class="p-3 bg-slate-50 border border-slate-200/60 rounded-xl space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-600">
                  <span class="font-semibold truncate max-w-[80%]">{{ form.image.name }}</span>
                  <span class="text-[10px] text-slate-400 shrink-0">({{ (form.image.size / (1024*1024)).toFixed(2) }} MB)</span>
                </div>

                <!-- Image preview -->
                <div v-if="filePreview" class="relative mt-2 rounded-lg overflow-hidden border border-slate-100 max-h-32 bg-white flex items-center justify-center">
                  <img :src="filePreview" class="max-h-28 object-contain" />
                </div>

                <!-- PDF icon preview -->
                <div v-if="isPdf" class="mt-2 flex items-center gap-2 p-2.5 rounded-lg bg-red-50 border border-red-100 text-red-700 text-xs">
                  <FileText class="h-5 w-5 shrink-0" />
                  <span>Dokumen Kalender PDF terpilih</span>
                </div>
              </div>

              <Button
                type="submit"
                :disabled="form.processing || !form.image"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold transition-colors mt-2"
              >
                {{ form.processing ? 'Mengunggah...' : 'Unggah Kalender' }}
              </Button>
            </form>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>
