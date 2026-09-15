<script setup>
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { FileText, Download, Inbox } from '@lucide/vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
    peminjamans: { type: Object, required: true },
    stats: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const status = ref(props.filters?.status ?? '');

watch(status, (value) => {
    router.get('/laporan', { status: value || undefined }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const statusMap = {
    menunggu: { label: 'Menunggu', cls: 'bg-amber-50 text-amber-700 border-amber-200' },
    sedang_dipinjam: { label: 'Sedang Dipinjam', cls: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    ditolak: { label: 'Ditolak', cls: 'bg-red-50 text-red-700 border-red-200' },
    selesai: { label: 'Selesai', cls: 'bg-slate-100 text-slate-600 border-slate-200' },
};

const formatDate = (d) =>
    d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
</script>

<template>
    <Head title="Laporan Saya" />

    <div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <FileText class="h-5 w-5 text-primary" />
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Peminjaman Saya</h1>
            </div>

            <a
                href="/laporan/export-pdf"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
            >
                <Download class="h-4 w-4" />
                Unduh PDF
            </a>
        </div>

        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Total</p>
                <p class="mt-1 text-2xl font-bold text-foreground">{{ stats.total ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Menunggu</p>
                <p class="mt-1 text-2xl font-bold text-foreground">{{ stats.pending ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Sedang Dipinjam</p>
                <p class="mt-1 text-2xl font-bold text-foreground">{{ stats.approved ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Selesai</p>
                <p class="mt-1 text-2xl font-bold text-foreground">{{ stats.done ?? 0 }}</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-4">
            <label for="filter-status" class="mb-1 block text-sm font-medium text-foreground">Saring status</label>
            <select
                id="filter-status"
                v-model="status"
                class="w-full rounded-lg border border-border bg-background px-3 py-2 text-base text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring sm:w-64"
            >
                <option value="">Semua status</option>
                <option value="menunggu">Menunggu</option>
                <option value="sedang_dipinjam">Sedang Dipinjam</option>
                <option value="selesai">Selesai</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-border bg-muted/50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Item</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Jadwal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Keperluan</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="p in peminjamans.data" :key="p.id" class="hover:bg-muted/40">
                            <td class="px-5 py-3">
                                <p class="font-medium text-foreground">{{ p.nama_item }}</p>
                                <p class="text-xs text-muted-foreground">{{ p.tipe === 'ruangan' ? 'Ruangan' : `Barang · ${p.jumlah} unit` }}</p>
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ formatDate(p.tanggal_mulai) }}
                                <span v-if="p.tanggal_selesai !== p.tanggal_mulai"> – {{ formatDate(p.tanggal_selesai) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-foreground">{{ p.keterangan || '-' }}</p>
                                <!-- B-06: alasan sistem tampil terpisah, tidak menimpa keterangan pemohon -->
                                <p v-if="p.alasan_sistem" class="mt-1 text-xs italic text-destructive">
                                    {{ p.alasan_sistem }}
                                </p>
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
                                <p class="font-medium text-foreground">Belum ada peminjaman</p>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    Mulai dengan mengecek ketersediaan
                                    <Link href="/ruangan" class="text-primary underline">ruangan</Link>
                                    atau
                                    <Link href="/barang" class="text-primary underline">barang</Link>.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :meta="peminjamans" />
        </div>
    </div>
</template>
