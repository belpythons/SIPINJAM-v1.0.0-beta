<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { BarChart3, Download, FileSpreadsheet, Inbox } from '@lucide/vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    peminjamans: { type: Object, required: true },
    stats: { type: Object, default: () => ({}) },
    topRuangan: { type: Array, default: () => [] },
    topBarang: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const startDate = ref(props.filters?.start_date ?? '');
const endDate = ref(props.filters?.end_date ?? '');

const terapkanFilter = () => {
    router.get('/admin/laporan', {
        start_date: startDate.value || undefined,
        end_date: endDate.value || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true });
};

const exportQuery = () =>
    new URLSearchParams({ start_date: startDate.value ?? '', end_date: endDate.value ?? '' }).toString();

const statusMap = {
    menunggu: { label: 'Menunggu', cls: 'bg-amber-50 text-amber-700 border-amber-200' },
    sedang_dipinjam: { label: 'Sedang Dipinjam', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    ditolak: { label: 'Ditolak', cls: 'bg-red-50 text-red-700 border-red-200' },
    selesai: { label: 'Selesai', cls: 'bg-slate-100 text-slate-600 border-slate-200' },
};

const formatDate = (d) =>
    d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

const ringkasan = [
    { key: 'total', label: 'Total Pengajuan' },
    { key: 'pending', label: 'Menunggu' },
    { key: 'approved', label: 'Sedang Dipinjam' },
    { key: 'done', label: 'Selesai' },
    { key: 'rejected', label: 'Ditolak' },
    { key: 'total_users', label: 'Pengguna' },
    { key: 'total_ruangan', label: 'Ruangan' },
    { key: 'total_barang', label: 'Barang' },
];
</script>

<template>
    <Head title="Laporan" />

    <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <BarChart3 class="h-5 w-5 text-primary" />
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Peminjaman</h1>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    :href="`/admin/laporan/export-pdf?${exportQuery()}`"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                >
                    <Download class="h-4 w-4" /> PDF
                </a>
                <a
                    :href="`/admin/laporan/export-excel?${exportQuery()}`"
                    class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                >
                    <FileSpreadsheet class="h-4 w-4" /> CSV
                </a>
            </div>
        </div>

        <!-- Filter rentang tanggal -->
        <div class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-border bg-card p-4">
            <div>
                <label for="start-date" class="mb-1 block text-sm font-medium text-foreground">Dari tanggal</label>
                <input
                    id="start-date"
                    v-model="startDate"
                    type="date"
                    class="rounded-lg border border-border bg-background px-3 py-2 text-base text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                />
            </div>
            <div>
                <label for="end-date" class="mb-1 block text-sm font-medium text-foreground">Sampai tanggal</label>
                <input
                    id="end-date"
                    v-model="endDate"
                    type="date"
                    class="rounded-lg border border-border bg-background px-3 py-2 text-base text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                />
            </div>
            <button
                type="button"
                class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                @click="terapkanFilter"
            >
                Terapkan
            </button>
        </div>

        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-4">
            <div v-for="r in ringkasan" :key="r.key" class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">{{ r.label }}</p>
                <p class="mt-1 text-2xl font-bold text-foreground">{{ stats[r.key] ?? 0 }}</p>
            </div>
        </div>

        <!-- Utilisasi -->
        <div class="mb-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-border bg-card p-4">
                <h2 class="mb-3 font-semibold text-foreground">Ruangan Paling Sering Dipinjam</h2>
                <ul class="space-y-2">
                    <li v-for="r in topRuangan" :key="r.ruangan_id" class="flex justify-between text-sm">
                        <span class="text-foreground">{{ r.ruangan?.nama ?? '—' }}</span>
                        <span class="font-medium text-muted-foreground">{{ r.total }}x</span>
                    </li>
                    <li v-if="!topRuangan.length" class="text-sm text-muted-foreground">Belum ada data.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <h2 class="mb-3 font-semibold text-foreground">Barang Paling Sering Dipinjam</h2>
                <ul class="space-y-2">
                    <li v-for="b in topBarang" :key="b.barang_id" class="flex justify-between text-sm">
                        <span class="text-foreground">{{ b.barang?.nama ?? '—' }}</span>
                        <span class="font-medium text-muted-foreground">{{ b.total }}x</span>
                    </li>
                    <li v-if="!topBarang.length" class="text-sm text-muted-foreground">Belum ada data.</li>
                </ul>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-border bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Peminjam</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Item</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Jadwal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="p in peminjamans.data" :key="p.id" class="hover:bg-muted/40">
                            <td class="px-5 py-3">
                                <p class="font-medium text-foreground">{{ p.user?.name ?? '—' }}</p>
                                <p class="text-xs text-muted-foreground">{{ p.user?.email }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-foreground">{{ p.nama_item }}</p>
                                <p class="text-xs text-muted-foreground">{{ p.tipe === 'ruangan' ? 'Ruangan' : `Barang · ${p.jumlah} unit` }}</p>
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ formatDate(p.tanggal_mulai) }}
                                <span v-if="p.tanggal_selesai !== p.tanggal_mulai"> – {{ formatDate(p.tanggal_selesai) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-medium"
                                    :class="statusMap[p.status]?.cls"
                                >
                                    {{ statusMap[p.status]?.label ?? p.status }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="!peminjamans.data.length">
                            <td colspan="4" class="px-5 py-14 text-center">
                                <Inbox class="mx-auto mb-3 h-8 w-8 text-muted-foreground" />
                                <p class="font-medium text-foreground">Tidak ada data pada rentang tanggal ini</p>
                                <p class="mt-1 text-sm text-muted-foreground">Coba ubah rentang tanggalnya.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :meta="peminjamans" />
        </div>
    </div>
</template>
