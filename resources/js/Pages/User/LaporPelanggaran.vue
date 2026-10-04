<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  AlertTriangle,
  Send,
  ShieldCheck,
  FileCheck2,
  Info,
  Clock,
  CheckCircle2,
  BookOpen,
} from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

defineOptions({ layout: UserLayout });

const props = defineProps({
    peminjamans: { type: Array, default: () => [] },
});

const jenisOptions = [
    { value: 'belum_kembali', label: 'Belum Dikembalikan (Terlambat)' },
    { value: 'rusak', label: 'Barang / Fasilitas Rusak atau Cacat' },
    { value: 'ruangan_berantakan', label: 'Ruangan Ditinggalkan Kotor / Berantakan' },
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

    <div class="max-w-5xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <!-- ── Page Header ─────────────────────────────────── -->
        <div class="mb-8 border-b border-slate-200/80 pb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100 shadow-xs">
                    <AlertTriangle class="h-5 w-5" />
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Lapor Pelanggaran</h1>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Bantu menjaga fasilitas kampus dengan melaporkan aset yang rusak, terlambat kembali, atau ruangan kotor.
                    </p>
                </div>
            </div>
        </div>

        <!-- ── 2-Column Balanced Layout ─────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- ── Left Column: Form Laporan (7 cols) ────────── -->
            <div class="lg:col-span-7">
                <Card class="border-slate-200/80 shadow-xs bg-white rounded-2xl overflow-hidden">
                    <CardHeader class="bg-slate-50/50 border-b border-slate-100 pb-4">
                        <CardTitle class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <FileCheck2 class="h-4 w-4 text-blue-600" />
                            Formulir Pengaduan Pelanggaran
                        </CardTitle>
                        <CardDescription class="text-xs text-slate-500">
                            Isi detail pelanggaran secara akurat untuk segera ditindaklanjuti oleh pengelola kampus.
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent class="p-6">
                        <form @submit.prevent="submit" class="space-y-5">
                            <!-- Peminjaman Terkait -->
                            <div class="space-y-2">
                                <Label class="text-xs font-semibold text-slate-700">
                                    Peminjaman Terkait <span class="text-red-500">*</span>
                                </Label>
                                <select
                                    v-model="form.peminjaman_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="" disabled>Pilih transaksi peminjaman terkait...</option>
                                    <option v-for="p in peminjamans" :key="p.id" :value="p.id">
                                        {{ formatPeminjaman(p) }}
                                    </option>
                                </select>
                                <p v-if="form.errors.peminjaman_id" class="text-xs text-red-500">{{ form.errors.peminjaman_id }}</p>
                            </div>

                            <!-- Jenis Pelanggaran -->
                            <div class="space-y-2">
                                <Label class="text-xs font-semibold text-slate-700">
                                    Jenis Pelanggaran <span class="text-red-500">*</span>
                                </Label>
                                <select
                                    v-model="form.jenis"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="" disabled>Pilih jenis kategori pelanggaran...</option>
                                    <option v-for="opt in jenisOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.jenis" class="text-xs text-red-500">{{ form.errors.jenis }}</p>
                            </div>

                            <!-- Deskripsi -->
                            <div class="space-y-2">
                                <Label class="text-xs font-semibold text-slate-700">
                                    Deskripsi Kejadian <span class="text-red-500">*</span>
                                </Label>
                                <textarea
                                    v-model="form.deskripsi"
                                    rows="4"
                                    placeholder="Jelaskan kondisi kerusakan, ketidaksesuaian barang, atau waktu keterlambatan secara jelas..."
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"
                                />
                                <p v-if="form.errors.deskripsi" class="text-xs text-red-500">{{ form.errors.deskripsi }}</p>
                            </div>

                            <!-- Foto Bukti -->
                            <div class="space-y-2">
                                <Label class="text-xs font-semibold text-slate-700">
                                    Foto Bukti <span class="text-slate-400 font-normal">(opsional)</span>
                                </Label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="onFileChange"
                                    class="w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3.5 file:py-2 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-dashed border-slate-200 rounded-xl p-2"
                                />
                                <p v-if="form.errors.bukti" class="text-xs text-red-500">{{ form.errors.bukti }}</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200"
                                >
                                    <Send class="h-4 w-4" />
                                    {{ form.processing ? 'Mengirim Laporan...' : 'Kirim Laporan Pelanggaran' }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- ── Right Column: Panduan & Informasi (5 cols) ── -->
            <div class="lg:col-span-5 space-y-5">
                <!-- Info Card: Pedoman Pelaporan -->
                <Card class="border-blue-100 bg-blue-50/40 shadow-xs rounded-2xl overflow-hidden">
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2 text-blue-700 font-bold text-sm">
                            <ShieldCheck class="h-4 w-4 text-blue-600" />
                            Kerahasiaan & Penanganan
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3 text-xs text-slate-600 leading-relaxed pt-0">
                        <div class="flex items-start gap-2.5">
                            <CheckCircle2 class="h-4 w-4 text-emerald-500 mt-0.5 shrink-0" />
                            <span>Identitas pelapor dilindungi secara internal oleh pengelola sistem SIPINJAM.</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <CheckCircle2 class="h-4 w-4 text-emerald-500 mt-0.5 shrink-0" />
                            <span>Laporan akan ditinjau langsung oleh Admin Sarpras dan diverifikasi sebelum sanksi diproses.</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <CheckCircle2 class="h-4 w-4 text-emerald-500 mt-0.5 shrink-0" />
                            <span>Sertakan foto bukti yang jelas untuk mempercepat proses penindakan.</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Info Card: Kategori Pelanggaran -->
                <Card class="border-slate-200/80 shadow-xs bg-white rounded-2xl">
                    <CardHeader class="pb-3">
                        <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                            <Info class="h-4 w-4 text-blue-600" />
                            Kategori Pelanggaran Utama
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3.5 text-xs text-slate-600 pt-0">
                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                            <p class="font-bold text-slate-800 mb-0.5 flex items-center gap-1.5">
                                <Clock class="h-3.5 w-3.5 text-amber-500" />
                                Keterlambatan Pengembalian
                            </p>
                            <p class="text-slate-500 text-[11px]">
                                Aset belum dikembalikan melewati batas waktu peminjaman yang disetujui tanpa konfirmasi perpanjangan.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                            <p class="font-bold text-slate-800 mb-0.5 flex items-center gap-1.5">
                                <AlertTriangle class="h-3.5 w-3.5 text-rose-500" />
                                Kerusakan / Hilang
                            </p>
                            <p class="text-slate-500 text-[11px]">
                                Fasilitas atau barang mengalami cacat fisik, patah, tidak berfungsi, atau hilang selama masa peminjaman.
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                            <p class="font-bold text-slate-800 mb-0.5 flex items-center gap-1.5">
                                <ShieldCheck class="h-3.5 w-3.5 text-indigo-500" />
                                Ruangan Kotor & Tidak Terawat
                            </p>
                            <p class="text-slate-500 text-[11px]">
                                Ruangan ditinggalkan dalam kondisi kotor, sampah menumpuk, atau tata letak tidak dikembalikan seperti semula.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Shortcut to Tata Tertib -->
                <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-600 to-indigo-700 p-5 text-white shadow-xs">
                    <h4 class="font-bold text-sm mb-1 flex items-center gap-2">
                        <BookOpen class="h-4 w-4 text-blue-200" />
                        Pahami Tata Tertib Lengkap
                    </h4>
                    <p class="text-xs text-blue-100 mb-4 leading-relaxed">
                        Pelajari hak, kewajiban, dan matriks sanksi akademik peminjaman sarana dan prasarana.
                    </p>
                    <Link
                        href="/tata_tertib"
                        class="inline-flex items-center justify-center rounded-xl bg-white text-blue-700 px-4 py-2 text-xs font-bold shadow-xs hover:bg-blue-50 transition-colors"
                    >
                        Lihat Tata Tertib →
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
