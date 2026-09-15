<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { AlertTriangle, Send } from '@lucide/vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
    peminjamans: { type: Array, default: () => [] },
});

const jenisOptions = [
    { value: 'belum_kembali', label: 'Belum Dikembalikan' },
    { value: 'rusak', label: 'Rusak / Cacat' },
    { value: 'ruangan_berantakan', label: 'Ruangan Berantakan' },
];

const form = useForm({
    peminjaman_id: '',
    jenis: '',
    deskripsi: '',
    bukti: null,
});

const onFileChange = (e) => {
    form.bukti = e.target.files[0] || null;
};

const submit = () => {
    form.post('/lapor-pelanggaran', {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};

const formatPeminjaman = (p) =>
    `${p.user?.name ?? 'Tanpa nama'} — ${p.nama_item ?? p.tipe} (${p.tanggal_mulai} s/d ${p.tanggal_selesai})`;
</script>

<template>
    <Head title="Lapor Pelanggaran" />

    <div class="px-6 py-8 lg:px-10 max-w-2xl">
        <div class="mb-6 flex items-center gap-2">
            <AlertTriangle class="h-5 w-5 text-amber-500" />
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Lapor Pelanggaran</h1>
        </div>
        <p class="mb-6 text-sm text-slate-500">
            Laporkan aset yang belum dikembalikan, rusak, atau ruangan yang ditinggalkan berantakan.
            Pilih peminjaman terkait agar admin dapat menindaklanjuti dengan tepat.
        </p>

        <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6">
            <div>
                <label class="text-xs font-semibold text-slate-600">Peminjaman Terkait</label>
                <select v-model="form.peminjaman_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    <option value="" disabled>Pilih peminjaman...</option>
                    <option v-for="p in peminjamans" :key="p.id" :value="p.id">{{ formatPeminjaman(p) }}</option>
                </select>
                <p v-if="form.errors.peminjaman_id" class="mt-1 text-xs text-red-500">{{ form.errors.peminjaman_id }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600">Jenis Pelanggaran</label>
                <select v-model="form.jenis" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    <option value="" disabled>Pilih jenis...</option>
                    <option v-for="opt in jenisOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <p v-if="form.errors.jenis" class="mt-1 text-xs text-red-500">{{ form.errors.jenis }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                <textarea v-model="form.deskripsi" rows="4" placeholder="Jelaskan kejadian secara singkat dan jelas..."
                    class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 resize-none" />
                <p v-if="form.errors.deskripsi" class="mt-1 text-xs text-red-500">{{ form.errors.deskripsi }}</p>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-600">Foto Bukti <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="file" accept="image/*" @change="onFileChange"
                    class="mt-1 w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-amber-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-amber-700 hover:file:bg-amber-100" />
                <p v-if="form.errors.bukti" class="mt-1 text-xs text-red-500">{{ form.errors.bukti }}</p>
            </div>

            <button type="submit" :disabled="form.processing"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 disabled:opacity-50 transition-colors">
                <Send class="h-4 w-4" /> {{ form.processing ? 'Mengirim...' : 'Kirim Laporan' }}
            </button>
        </form>
    </div>
</template>
