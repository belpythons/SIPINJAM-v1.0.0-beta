<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ClipboardList, CheckCircle2, XCircle } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({ peminjamans: Array });

const statusMap = {
    menunggu: { label: 'Menunggu', cls: 'bg-amber-50 text-amber-700 border-amber-200' },
    sedang_dipinjam: { label: 'Disetujui', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    ditolak: { label: 'Ditolak', cls: 'bg-red-50 text-red-700 border-red-200' },
    selesai: { label: 'Selesai', cls: 'bg-slate-100 text-slate-600 border-slate-200' },
};

const approve = (id) => {
    router.patch(`/admin/kelola-peminjaman/${id}/setujui`, {}, { preserveScroll: true });
};
const reject = (id) => {
    router.patch(`/admin/kelola-peminjaman/${id}/tolak`, {}, { preserveScroll: true });
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
</script>

<template>
    <Head title="Kelola Peminjaman" />
    <div class="px-6 py-8 lg:px-10">
        <div class="mb-8 flex items-center gap-2">
            <ClipboardList class="h-5 w-5 text-orange-500" />
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kelola Peminjaman</h1>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Peminjam</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tipe</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Keperluan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="p in peminjamans" :key="p.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800">{{ p.user?.name || '-' }}</p>
                            <p class="text-[11px] text-slate-400">{{ p.user?.email || '' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-slate-700">{{ p.nama_item || '-' }}</td>
                        <td class="px-5 py-3.5">
                            <span :class="['inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold', p.tipe === 'ruangan' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-sky-50 text-sky-700 border-sky-200']">
                                {{ p.tipe === 'ruangan' ? 'Ruangan' : 'Barang' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ formatDate(p.tanggal_mulai) }} – {{ formatDate(p.tanggal_selesai) }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 max-w-[180px] truncate">{{ p.keterangan || '-' }}</td>
                        <td class="px-5 py-3.5">
                            <span :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold', statusMap[p.status]?.cls || 'bg-slate-100 text-slate-500']">
                                {{ statusMap[p.status]?.label || p.status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div v-if="p.status === 'menunggu'" class="flex items-center justify-center gap-2">
                                <button @click="approve(p.id)" class="inline-flex items-center gap-1 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-600 transition-colors">
                                    <CheckCircle2 class="h-3.5 w-3.5" /> Setujui
                                </button>
                                <button @click="reject(p.id)" class="inline-flex items-center gap-1 rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-600 transition-colors">
                                    <XCircle class="h-3.5 w-3.5" /> Tolak
                                </button>
                            </div>
                            <span v-else class="text-xs text-slate-400">—</span>
                        </td>
                    </tr>
                    <tr v-if="!peminjamans.length">
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">Belum ada data peminjaman.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
