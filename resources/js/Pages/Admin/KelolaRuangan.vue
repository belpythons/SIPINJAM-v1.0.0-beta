<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { DoorOpen, Plus, Pencil, Trash2, X, Image as ImageIcon } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ ruangans: Object });

const showForm = ref(false);
const editingId = ref(null);
const imagePreview = ref(null);
const existingImage = ref(null);

const form = useForm({
    nama: '', kode: '', kapasitas: '', lokasi: '', deskripsi: '', status: 'tersedia', image_path: null,
});

const openCreate = () => { 
    editingId.value = null; 
    form.reset(); 
    imagePreview.value = null;
    existingImage.value = null;
    showForm.value = true; 
};
const openEdit = (r) => {
    editingId.value = r.id;
    form.nama = r.nama; form.kode = r.kode; form.kapasitas = r.kapasitas;
    form.lokasi = r.lokasi || ''; form.deskripsi = r.deskripsi || ''; form.status = r.status;
    form.image_path = null;
    imagePreview.value = null;
    existingImage.value = r.image_path || null;
    showForm.value = true;
};
const close = () => { 
    showForm.value = false; 
    form.reset(); 
    editingId.value = null; 
    imagePreview.value = null;
    existingImage.value = null;
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_path = file;
        imagePreview.value = URL.createObjectURL(file);
    } else {
        form.image_path = null;
        imagePreview.value = null;
    }
};

const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(`/admin/kelola-ruangan/${editingId.value}`, { onSuccess: close, preserveScroll: true });
    } else {
        form.post('/admin/kelola-ruangan', { onSuccess: close, preserveScroll: true });
    }
};
const destroy = (id) => { if (confirm('Hapus ruangan ini?')) router.delete(`/admin/kelola-ruangan/${id}`, { preserveScroll: true }); };

</script>

<template>
    <Head title="Kelola Ruangan" />
    <div class="px-6 py-8 lg:px-10">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <DoorOpen class="h-5 w-5 text-orange-500" />
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Ruangan</h1>
            </div>
            <button @click="openCreate" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors shadow-sm">
                <Plus class="h-4 w-4" /> Tambah
            </button>
        </div>

        <!-- Inline Form -->
        <div v-if="showForm" class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/30 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">{{ editingId ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</h2>
                <button @click="close" class="text-slate-400 hover:text-slate-600 transition-colors"><X class="h-4 w-4" /></button>
            </div>
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-xs font-semibold text-slate-600">Nama</label><input v-model="form.nama" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Kode</label><input v-model="form.kode" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Kapasitas</label><input v-model="form.kapasitas" type="number" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Lokasi</label><input v-model="form.lokasi" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" /></div>
                <div class="md:col-span-2"><label class="text-xs font-semibold text-slate-600">Deskripsi</label><textarea v-model="form.deskripsi" rows="2" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 resize-none" /></div>
                <div><label class="text-xs font-semibold text-slate-600">Status</label>
                    <select v-model="form.status" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="tersedia">Tersedia</option><option value="tidak_tersedia">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="text-xs font-semibold text-slate-600">Unggah Foto Baru</label>
                        <input type="file" @change="handleFileChange" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 bg-white" accept="image/png, image/jpeg, image/jpg" />
                        <p v-if="form.errors.image_path" class="text-xs text-red-500 mt-1">{{ form.errors.image_path }}</p>
                    </div>
                    <div class="h-10 w-16 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center">
                        <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-cover" />
                        <img v-else-if="existingImage" :src="existingImage" class="h-full w-full object-cover" />
                        <div v-else class="text-slate-300">
                            <ImageIcon class="h-5 w-5" />
                        </div>
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-colors shadow-sm">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Foto</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kapasitas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="r in ruangans.data" :key="r.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ r.kode }}</td>
                        <td class="px-5 py-3.5">
                            <div class="h-10 w-16 overflow-hidden rounded-lg border border-slate-100 bg-slate-50">
                                <img v-if="r.image_path" :src="r.image_path" :alt="r.nama" class="h-full w-full object-cover" />
                                <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                    <ImageIcon class="h-4 w-4" />
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ r.nama }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.lokasi || '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.kapasitas }}</td>
                        <td class="px-5 py-3.5">
                            <span v-if="r.is_terpakai" class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold bg-rose-50 text-rose-700 border-rose-200">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-rose-500 inline-block animate-pulse" />
                                Sedang Terpakai
                            </span>
                            <span v-else :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold', r.status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200']">
                                <span v-if="r.status === 'tersedia'" class="mr-1 h-1.5 w-1.5 rounded-full bg-emerald-500 inline-block" />
                                {{ r.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit(r)" class="rounded-lg p-1.5 text-slate-400 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="Edit"><Pencil class="h-4 w-4" /></button>
                                <button @click="destroy(r.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus"><Trash2 class="h-4 w-4" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!ruangans.data.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada data ruangan.</td></tr>
                </tbody>
            </table>

            <Pagination :meta="ruangans" />
        </div>

    </div>
</template>
