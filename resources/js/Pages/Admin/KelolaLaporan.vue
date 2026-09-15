<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ShieldAlert, Ban, X } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    laporans: { type: Object, required: true },
});

const jenisLabel = {
    belum_kembali: 'Belum Dikembalikan',
    rusak: 'Rusak / Cacat',
    ruangan_berantakan: 'Ruangan Berantakan',
};

const statusBadge = {
    menunggu: 'bg-amber-50 text-amber-700 border-amber-200',
    ditindak: 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ditolak: 'bg-slate-100 text-slate-500 border-slate-200',
};

const sanksiTargetId = ref(null);
const sanksiForm = useForm({ hari: 7, alasan: '' });

const openSanksi = (laporan) => {
    sanksiTargetId.value = laporan.id;
    sanksiForm.reset();
    sanksiForm.hari = 7;
};
const closeSanksi = () => { sanksiTargetId.value = null; sanksiForm.reset(); };
const submitSanksi = () => {
    sanksiForm.patch(`/admin/pelanggaran/${sanksiTargetId.value}/sanksi`, {
        onSuccess: closeSanksi,
        preserveScroll: true,
    });
};

const tolak = (id) => {
    if (confirm('Tolak laporan ini?')) {
        router.patch(`/admin/pelanggaran/${id}/tolak`, {}, { preserveScroll: true });
    }
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
</script>

<template>
    <Head title="Kelola Pelanggaran & Sanksi" />
    <div class="px-6 py-8 lg:px-10">
        <div class="mb-6 flex items-center gap-2">
            <ShieldAlert class="h-5 w-5 text-orange-500" />
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Pelanggaran & Sanksi</h1>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Pelapor</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Terlapor</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Deskripsi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="l in laporans.data" :key="l.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-slate-700">{{ l.pelapor?.name ?? '-' }}</td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ l.peminjaman?.user?.name ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ jenisLabel[l.jenis] ?? l.jenis }}</td>
                        <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate" :title="l.deskripsi">{{ l.deskripsi }}</td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">{{ formatDate(l.created_at) }}</td>
                        <td class="px-5 py-3.5">
                            <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold', statusBadge[l.status]]">
                                {{ l.status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div v-if="l.status === 'menunggu'" class="flex items-center justify-center gap-2">
                                <button @click="openSanksi(l)" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-orange-600 hover:bg-orange-50 transition-colors">Putuskan Sanksi</button>
                                <button @click="tolak(l.id)" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors"><Ban class="h-4 w-4" /></button>
                            </div>
                            <span v-else class="text-xs text-slate-400">{{ l.catatan_admin || '-' }}</span>
                        </td>
                    </tr>
                    <tr v-if="!laporans.data.length"><td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada laporan.</td></tr>
                </tbody>
            </table>

            <Pagination :meta="laporans" />
        </div>

        <!-- Modal Putuskan Sanksi -->
        <div v-if="sanksiTargetId" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" @click.self="closeSanksi">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-800">Putuskan Sanksi</h2>
                    <button @click="closeSanksi" class="text-slate-400 hover:text-slate-600"><X class="h-4 w-4" /></button>
                </div>
                <form @submit.prevent="submitSanksi" class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Bekukan Akun Selama (hari)</label>
                        <input v-model.number="sanksiForm.hari" type="number" min="1" max="365"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" />
                        <p v-if="sanksiForm.errors.hari" class="mt-1 text-xs text-red-500">{{ sanksiForm.errors.hari }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Alasan</label>
                        <textarea v-model="sanksiForm.alasan" rows="3"
                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 resize-none" />
                        <p v-if="sanksiForm.errors.alasan" class="mt-1 text-xs text-red-500">{{ sanksiForm.errors.alasan }}</p>
                    </div>
                    <button type="submit" :disabled="sanksiForm.processing"
                        class="w-full rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 disabled:opacity-50 transition-colors">
                        {{ sanksiForm.processing ? 'Menyimpan...' : 'Jatuhkan Sanksi' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
