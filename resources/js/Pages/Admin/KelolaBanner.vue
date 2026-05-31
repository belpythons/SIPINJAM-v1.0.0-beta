<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Image as ImageIcon, Plus, Trash2, X, Upload } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    banners: Array
});

const showForm = ref(false);
const previewUrl = ref(null);

const form = useForm({
    image_path: null,
});

const openCreate = () => {
    form.reset();
    previewUrl.value = null;
    showForm.value = true;
};

const close = () => {
    showForm.value = false;
    form.reset();
    previewUrl.value = null;
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_path = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post('/admin/kelola-banner', {
        onSuccess: () => {
            close();
        },
        preserveScroll: true
    });
};

const destroy = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus banner ini?')) {
        router.delete(`/admin/kelola-banner/${id}`, {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Kelola Landing Page" />
    
    <div class="px-6 py-8 lg:px-10">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ImageIcon class="h-5 w-5 text-orange-500" />
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Landing Page</h1>
            </div>
            <button 
                @click="openCreate" 
                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-all duration-200 shadow-sm"
            >
                <Plus class="h-4 w-4" /> Tambah Banner
            </button>
        </div>

        <!-- Add Banner Form -->
        <div v-if="showForm" class="mb-8 rounded-2xl border border-orange-200 bg-orange-50/30 p-6 shadow-sm transition-all duration-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">Tambah Banner Baru</h2>
                <button @click="close" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <X class="h-4 w-4" />
                </button>
            </div>
            
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <!-- File Input Box -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">File Gambar Banner</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-40 border border-slate-200 border-dashed rounded-xl cursor-pointer bg-white hover:bg-slate-50/50 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <Upload class="w-8 h-8 text-slate-400 mb-2" />
                                    <p class="text-xs font-medium text-slate-600">Klik untuk unggah atau seret file</p>
                                    <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, JPEG (Maks. 5MB)</p>
                                </div>
                                <input 
                                    type="file" 
                                    class="hidden" 
                                    accept="image/png, image/jpeg, image/jpg" 
                                    @change="handleFileChange" 
                                />
                            </label>
                        </div>
                        <p v-if="form.errors.image_path" class="text-xs text-red-500 mt-1 font-medium">{{ form.errors.image_path }}</p>
                    </div>

                    <!-- Image Preview -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Pratinjau Gambar</label>
                        <div class="w-full h-40 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-center overflow-hidden">
                            <img 
                                v-if="previewUrl" 
                                :src="previewUrl" 
                                alt="Pratinjau" 
                                class="w-full h-full object-cover" 
                            />
                            <div v-else class="text-center p-4">
                                <ImageIcon class="w-8 h-8 text-slate-300 mx-auto mb-1" />
                                <span class="text-xs text-slate-400">Belum ada gambar terpilih</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button 
                        type="button" 
                        @click="close" 
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="form.processing || !form.image_path" 
                        class="rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-all duration-200 shadow-sm"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Banner' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Banner List Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="(banner, index) in banners" 
                :key="banner.id" 
                class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all duration-200"
            >
                <!-- Image Wrapper -->
                <div class="aspect-video w-full overflow-hidden bg-slate-50 border-b border-slate-100">
                    <img 
                        :src="banner.image_path" 
                        :alt="'Banner ' + (index + 1)" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
                    />
                </div>
                
                <!-- Card Footer Info & Action -->
                <div class="flex items-center justify-between p-4 bg-white">
                    <div class="flex flex-col">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Slide Banner</span>
                        <span class="text-sm font-bold text-slate-800">Slide #{{ index + 1 }}</span>
                    </div>
                    
                    <button 
                        @click="destroy(banner.id)" 
                        class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all duration-200" 
                        title="Hapus Banner"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div 
                v-if="!banners.length" 
                class="col-span-full border border-slate-200 border-dashed rounded-2xl bg-white p-12 text-center"
            >
                <ImageIcon class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Banner</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Tambahkan gambar banner baru untuk ditampilkan pada carousel landing page utama.</p>
                <button 
                    @click="openCreate" 
                    class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3.5 py-1.5 text-xs font-semibold text-orange-700 hover:bg-orange-100 transition-colors"
                >
                    <Plus class="h-3.5 w-3.5" /> Tambah Banner
                </button>
            </div>
        </div>
    </div>
</template>
