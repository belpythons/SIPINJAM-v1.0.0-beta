<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { DoorOpen, Plus, Pencil, Trash2, X } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ ruangans: Array });

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    nama: '', kode: '', kapasitas: '', lokasi: '', deskripsi: '', status: 'tersedia',
});

const openCreate = () => { editingId.value = null; form.reset(); showForm.value = true; };
const openEdit = (r) => {
    editingId.value = r.id;
    form.nama = r.nama; form.kode = r.kode; form.kapasitas = r.kapasitas;
    form.lokasi = r.lokasi || ''; form.deskripsi = r.deskripsi || ''; form.status = r.status;
    showForm.value = true;
};
const close = () => { showForm.value = false; form.reset(); editingId.value = null; };

const submit = () => {
    if (editingId.value) {
        form.put(`/admin/kelola-ruangan/${editingId.value}`, { onSuccess: close, preserveScroll: true });
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
            <button @click="openCreate" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors">
                <Plus class="h-4 w-4" /> Tambah
            </button>
        </div>

        <!-- Inline Form -->
        <div v-if="showForm" class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/30 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">{{ editingId ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}</h2>
                <button @click="close" class="text-slate-400 hover:text-slate-600"><X class="h-4 w-4" /></button>
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
                <div class="flex items-end">
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-colors">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nama</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kapasitas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="r in ruangans" :key="r.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ r.kode }}</td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ r.nama }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.lokasi || '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ r.kapasitas }}</td>
                        <td class="px-5 py-3.5">
                            <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold', r.status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200']">
                                {{ r.status === 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEdit(r)" class="rounded-lg p-1.5 text-slate-400 hover:bg-orange-50 hover:text-orange-600 transition-colors"><Pencil class="h-4 w-4" /></button>
                                <button @click="destroy(r.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors"><Trash2 class="h-4 w-4" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!ruangans.length"><td colspan="6" class="px-5 py-12 text-center text-slate-400">Belum ada data ruangan.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
