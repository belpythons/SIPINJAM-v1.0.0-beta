<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ShieldCheck, Plus, X } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    versions: { type: Array, required: true },
    aktifId: { type: Number, default: null },
});

const showForm = ref(false);
const form = useForm({ versi: '', konten: '', berlaku_sejak: '' });

const openCreate = () => { form.reset(); showForm.value = true; };
const close = () => { showForm.value = false; form.reset(); };
const submit = () => form.post('/admin/kelola-tata-tertib', { onSuccess: close, preserveScroll: true });

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
</script>

<template>
    <Head title="Kelola Tata Tertib" />
    <div class="px-6 py-8 lg:px-10">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <ShieldCheck class="h-5 w-5 text-orange-500" />
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Tata Tertib</h1>
            </div>
            <button @click="openCreate" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors">
                <Plus class="h-4 w-4" /> Buat Versi Baru
            </button>
        </div>

        <div v-if="showForm" class="mb-6 rounded-2xl border border-orange-200 bg-orange-50/30 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-800">Versi Tata Tertib Baru</h2>
                <button @click="close" class="text-slate-400 hover:text-slate-600"><X class="h-4 w-4" /></button>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600">Kode Versi</label>
                    <input v-model="form.versi" placeholder="mis. 2.0 - Revisi Sanksi" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" />
                    <p v-if="form.errors.versi" class="mt-1 text-xs text-red-500">{{ form.errors.versi }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Isi Tata Tertib</label>
                    <textarea v-model="form.konten" rows="10" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" />
                    <p v-if="form.errors.konten" class="mt-1 text-xs text-red-500">{{ form.errors.konten }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Berlaku Sejak <span class="text-slate-400 font-normal">(kosongkan = sekarang)</span></label>
                    <input v-model="form.berlaku_sejak" type="datetime-local" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" />
                    <p v-if="form.errors.berlaku_sejak" class="mt-1 text-xs text-red-500">{{ form.errors.berlaku_sejak }}</p>
                </div>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-orange-600 px-6 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-colors">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Versi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Berlaku Sejak</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Dibuat</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="v in versions" :key="v.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ v.versi }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ formatDate(v.berlaku_sejak) }}</td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">{{ formatDate(v.created_at) }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span v-if="v.id === aktifId" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-semibold text-emerald-700">Aktif</span>
                            <span v-else class="text-xs text-slate-400">—</span>
                        </td>
                    </tr>
                    <tr v-if="!versions.length"><td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada versi tata tertib.</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
