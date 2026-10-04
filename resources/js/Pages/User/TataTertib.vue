<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  ShieldCheck,
  UserCheck,
  Package,
  CalendarClock,
  QrCode,
  RefreshCw,
  AlertTriangle,
  Wrench,
  CheckCircle2,
  FileText,
  Info,
  ArrowRight,
  Sparkles,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

defineOptions({ layout: UserLayout });

const activeTab = ref('all');

const policyMatrix = [
  {
    modul: '1. Kualifikasi Akun',
    icon: UserCheck,
    komponen: 'RBAC & Guard Middleware',
    isi: 'Akun terverifikasi aktif, bebas dari sanksi penangguhan dan denda.',
    reason: 'Mencegah fraud, akun fiktif, dan moral hazard peminjaman.',
  },
  {
    modul: '2. Kuota Pinjam',
    icon: Package,
    komponen: 'Limit Item & Kuota Tracker',
    isi: 'Maksimal meminjam 3 unit item bersamaan per pengguna.',
    reason: 'Pemerataan inventaris kampus dan pembatasan risiko akumulasi kerugian.',
  },
  {
    modul: '3. Durasi & Booking',
    icon: CalendarClock,
    komponen: 'Scheduler Auto-Cancel (7 Hari)',
    isi: 'Durasi standar 7 hari; auto-cancel bila tidak lapor verifikasi surat dalam 7 hari.',
    reason: 'Efisiensi perputaran aset dan mencegah penahanan stok gantung.',
  },
  {
    modul: '4. Serah Terima',
    icon: QrCode,
    komponen: 'Surat Resmi & Inspeksi Fisik',
    isi: 'Pemeriksaan kelengkapan fisik 2 arah saat check-in dan check-out aset.',
    reason: 'Pembuktian legalitas transfer tanggung jawab fisik & kondisi awal.',
  },
  {
    modul: '5. Perpanjangan',
    icon: RefreshCw,
    komponen: 'Bebas Antrean Reservasi',
    isi: 'Maksimal 1 kali perpanjangan, syarat: belum ada reservasi antrean pengguna lain.',
    reason: 'Fleksibilitas tanpa mengorbankan hak antrean pengguna lain.',
  },
  {
    modul: '6. Sanksi & Keterlambatan',
    icon: AlertTriangle,
    komponen: 'Pembekuan Akun N Hari',
    isi: 'Penangguhan hak pinjam selama masa keterlambatan atau keputusan admin.',
    reason: 'Efek jera (deterrent effect) guna menjamin ketepatan jadwal inventaris.',
  },
  {
    modul: '7. Ganti Rugi Kerusakan',
    icon: Wrench,
    komponen: 'Incident Report & Tanggung Jawab',
    isi: 'Wajib mengganti biaya servis resmi, sparepart, atau unit identik.',
    reason: 'Perlindungan aset kampus dan menjaga kontinuitas operasional.',
  },
];

const detailedArticles = [
  {
    id: 'kualifikasi',
    number: '2.1',
    title: 'Ketentuan Kelayakan Peminjam',
    icon: UserCheck,
    color: 'text-blue-600 bg-blue-50 border-blue-200',
    uxFrontend: 'Status verifikasi akun sivitas aktif (@stitek.ac.id). Jika akun berstatus ditangguhkan (is_blocked), akses pengajuan peminjaman otomatis dinonaktifkan.',
    uxBackend: 'Middleware memeriksa is_blocked == false dan batasan sanksi aktif sebelum mengizinkan pembuatan transaksi booking baru.',
    rule: 'Pengguna wajib berstatus sivitas aktif dan terverifikasi. Pengguna dengan penangguhan aktif dilarang membuat pengajuan peminjaman baru hingga masa sanksi berakhir.',
    reasons: [
      'Mitigasi Gagal Kembali: Meminjamkan aset kepada pengguna bermasalah melipatgandakan risiko aset hilang atau tidak kembali.',
      'Integritas Data Audit: Verifikasi identitas memastikan penanggung jawab dapat dimintai pertanggungjawaban secara hukum & institusional.',
    ],
  },
  {
    id: 'kuota',
    number: '2.2',
    title: 'Batas Kuota Peminjaman (Item Limit)',
    icon: Package,
    color: 'text-amber-600 bg-amber-50 border-amber-200',
    uxFrontend: 'Katalog menampilkan stok real-time (tersedia vs dipinjam). Pengajuan dibatasi sesuai ketersediaan fisik dan kuota wajar per kegiatan.',
    uxBackend: 'Validasi database dengan continuous pessimistic locking mencegah overbooking dan race conditions saat lonjakan pemesanan.',
    rule: 'Setiap pengguna berhak meminjam unit item dalam jumlah yang disetujui sesuai kebutuhan operasional dan ketersediaan stok.',
    reasons: [
      'Prinsip Keadilan Akses (Fair Share): Menghindari monopoli pemakaian inventaris oleh segelintir kelompok.',
      'Pembatasan Risiko (Loss Cap): Membatasi jumlah potensi kerugian jika terjadi musibah pada peminjam.',
    ],
  },
  {
    id: 'reservasi',
    number: '2.3',
    title: 'Reservasi & Pembatalan Otomatis (Auto-Cancel 7 Hari)',
    icon: CalendarClock,
    color: 'text-indigo-600 bg-indigo-50 border-indigo-200',
    uxFrontend: 'Kalender interaktif ketersediaan aset. Setelah submit, user langsung dapat mengunduh surat izin berkop untuk ditandatangani dan dibawa ke admin.',
    uxBackend: 'Background scheduler (AutoRejectPendingBookings) secara otomatis menolak permohonan yang berstatus pending lebih dari 7 hari tanpa validasi.',
    rule: 'Pengajuan reservasi wajib diajukan sebelum waktu pemakaian. Apabila surat peminjaman tidak ditandatangani dan dikonfirmasi dalam waktu 7 hari, sistem otomatis membatalkan peminjaman.',
    reasons: [
      'Mencegah Stok Gantung (Phantom Booking): Mencegah penahanan barang tanpa kepastian yang merugikan peminjam lain.',
      'Kesiapan Staf Pengelola: Memberikan kepastian jadwal bagi admin logistik untuk menyiapkan aset kampus.',
    ],
  },
  {
    id: 'serah-terima',
    number: '2.4',
    title: 'Validasi Serah Terima (Check-out & Check-in)',
    icon: QrCode,
    color: 'text-emerald-600 bg-emerald-50 border-emerald-200',
    uxFrontend: 'Surat permohonan resmi berkop institusi STITEK Bontang dicetak dan ditandatangani peminjam serta pejabat penanggung jawab.',
    uxBackend: 'Pencatatan status transisi dari Menunggu -> Disetujui (Sedang Dipinjam) -> Selesai dengan audit trail waktu dan admin pemroses.',
    rule: 'Penyerahan dan pengembalian barang wajib divalidasi oleh petugas pengelola setelah memeriksa kondisi fisik dan kelengkapan unit secara bersama-sama.',
    reasons: [
      'Bukti Valid (Non-repudiation): Menghilangkan sanggahan kondisi awal barang rusak atau belum diserahkan.',
      'Integritas Pencatatan: Menjamin keselarasan antara kondisi riil di lapangan dengan sistem database.',
    ],
  },
  {
    id: 'perpanjangan',
    number: '2.5',
    title: 'Batasan Perpanjangan (Renewal)',
    icon: RefreshCw,
    color: 'text-violet-600 bg-violet-50 border-violet-200',
    uxFrontend: 'Pengguna dapat mengonfirmasi perpanjangan kepada pengelola sebelum tanggal jatuh tempo berakhir.',
    uxBackend: 'Pengecekan konflik jadwal memastikan tidak ada peminjaman lain yang bertabrakan pada rentang waktu perpanjangan.',
    rule: 'Perpanjangan durasi pinjam wajib diajukan sebelum tenggat waktu berakhir, dengan syarat tidak ada pengguna lain yang telah memesan aset tersebut.',
    reasons: [
      'Kepastian Antrean: Menghindari kekecewaan peminjam berikutnya akibat perpanjangan mendadak peminjam lama.',
      'Disiplin Waktu: Mendorong manajemen waktu kegiatan kampus yang terukur dan tertib.',
    ],
  },
  {
    id: 'sanksi',
    number: '2.6',
    title: 'Sanksi Keterlambatan & Penegakan Disiplin',
    icon: AlertTriangle,
    color: 'text-rose-600 bg-rose-50 border-rose-200',
    uxFrontend: 'Pengguna yang melanggar akan menerima catatan sistem dan status pembekuan akun (is_blocked) dengan rincian tanggal berakhirnya sanksi.',
    uxBackend: 'Admin dapat memutuskan pembekuan akun selama N hari berdasarkan laporan pelanggaran yang terverifikasi.',
    rule: 'Keterlambatan pengembalian atau pelanggaran tata tertib kebersihan/keamanan dikenakan sanksi administratif berupa pembekuan hak pinjam akun.',
    reasons: [
      'Efek Jera (Deterrent Effect): Menjamin kepatuhan sivitas terhadap batas waktu dan pemeliharaan sarana kampus.',
      'Kompensasi Operasional: Mencegah gangguan terhadap jadwal perkuliahan atau praktikum mahasiswa lain.',
    ],
  },
  {
    id: 'ganti-rugi',
    number: '2.7',
    title: 'Ganti Rugi Kerusakan dan Kehilangan',
    icon: Wrench,
    color: 'text-red-600 bg-red-50 border-red-200',
    uxFrontend: 'Sivitas dapat melaporkan aset rusak atau bermasalah melalui form Lapor Pelanggaran disertai bukti deskripsi.',
    uxBackend: 'Pencatatan laporan kerusakan langsung dihubungkan dengan histori peminjaman terkait untuk ditindaklanjuti pengelola.',
    rule: 'Peminjam bertanggung jawab penuh atas keutuhan aset. Segala bentuk kerusakan akibat kelalaian atau kehilangan unit wajib diganti biaya perbaikan atau unit setara.',
    reasons: [
      'Keberlanjutan Sarana Kampus: Melindungi aset institusi dari penyusutan dini akibat kelalaian pemakaian.',
      'Rasa Tanggung Jawab: Memastikan seluruh sivitas memperlakukan fasilitas kampus dengan kehati-hatian tinggi.',
    ],
  },
];
</script>

<template>
  <Head title="Tata Tertib & Regulasi Peminjaman" />

  <div class="px-6 py-8 lg:px-10 max-w-6xl mx-auto space-y-10 font-sans antialiased">
    <!-- ── Header Banner ────────────────────────────── -->
    <div class="rounded-2xl bg-gradient-to-br from-blue-700 via-indigo-700 to-slate-900 p-8 sm:p-10 text-white shadow-card relative overflow-hidden">
      <!-- Background pattern -->
      <div
        class="absolute inset-0 opacity-10 pointer-events-none"
        style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 24px 24px;"
      />

      <div class="relative z-10 max-w-3xl space-y-4">
        <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-3.5 py-1 text-xs font-semibold uppercase tracking-wider backdrop-blur-sm text-blue-100">
          <Sparkles class="h-3.5 w-3.5 text-blue-300" />
          Regulasi & Tata Tertib Resmi Kampus
        </div>

        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
          Tata Tertib & Ketentuan Peminjaman Aset
        </h1>

        <p class="text-sm sm:text-base text-blue-100/90 leading-relaxed font-medium">
          Pedoman operasional, hak, kewajiban, dan justifikasi kebijakan peminjaman ruangan dan barang inventaris di lingkungan Sekolah Tinggi Teknologi Bontang (STITEK).
        </p>

        <div class="flex flex-wrap gap-4 pt-2 text-xs font-medium text-blue-200">
          <span class="inline-flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-white/15">
            <ShieldCheck class="h-4 w-4 text-emerald-300" /> Versi 1.0 (Berlaku Penuh)
          </span>
          <span class="inline-flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-white/15">
            <Info class="h-4 w-4 text-blue-300" /> Berlaku untuk Seluruh Sivitas Akademika
          </span>
        </div>
      </div>
    </div>

    <!-- ── 1. Matriks Rangkuman Kebijakan ──────────── -->
    <section class="space-y-4">
      <div class="flex items-center gap-3 pb-2 border-b border-border">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
          <FileText class="h-5 w-5" />
        </div>
        <div>
          <h2 class="text-xl font-bold text-foreground tracking-tight">1. Matriks Rangkuman Kebijakan</h2>
          <p class="text-xs text-muted-foreground">Ikhtisar modul ketentuan peminjaman beserta justifikasi sistem</p>
        </div>
      </div>

      <div class="overflow-hidden rounded-xl border border-border bg-card shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="border-b border-border bg-muted/60 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              <tr>
                <th class="px-5 py-3.5">Modul Kebijakan</th>
                <th class="px-5 py-3.5">Komponen Sistem</th>
                <th class="px-5 py-3.5">Inti Ketentuan</th>
                <th class="px-5 py-3.5">Justifikasi (Reason)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border text-foreground">
              <tr v-for="(item, idx) in policyMatrix" :key="idx" class="hover:bg-muted/30 transition-colors">
                <td class="px-5 py-3.5 font-semibold text-foreground whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <component :is="item.icon" class="h-4 w-4 text-primary shrink-0" />
                    <span>{{ item.modul }}</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 text-xs text-muted-foreground font-medium">
                  <Badge variant="outline" class="bg-muted/50 border-border text-foreground font-normal">
                    {{ item.komponen }}
                  </Badge>
                </td>
                <td class="px-5 py-3.5 text-xs font-medium max-w-xs leading-relaxed">
                  {{ item.isi }}
                </td>
                <td class="px-5 py-3.5 text-xs text-muted-foreground max-w-xs leading-relaxed">
                  {{ item.reason }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ── 2. Bedah Detail Regulasi (7 Modul) ───────── -->
    <section class="space-y-6">
      <div class="flex items-center gap-3 pb-2 border-b border-border">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
          <ShieldCheck class="h-5 w-5" />
        </div>
        <div>
          <h2 class="text-xl font-bold text-foreground tracking-tight">2. Bedah Detail: Desain Sistem, Aturan, & Reason</h2>
          <p class="text-xs text-muted-foreground">Penjelasan komprehensif alur implementasi UX, klausul aturan, dan analisis risiko</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6">
        <Card
          v-for="art in detailedArticles"
          :key="art.id"
          class="overflow-hidden border-border bg-card shadow-xs transition-all duration-200 hover:shadow-md"
        >
          <CardContent class="p-6 space-y-5">
            <!-- Article Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-border">
              <div class="flex items-center gap-3">
                <div :class="['flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border', art.color]">
                  <component :is="art.icon" class="h-5 w-5" />
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-primary font-mono">{{ art.number }}</span>
                    <h3 class="text-base font-bold text-foreground">{{ art.title }}</h3>
                  </div>
                  <p class="text-xs text-muted-foreground mt-0.5">Spesifikasi Operasional & Kepatuhan</p>
                </div>
              </div>
              <Badge variant="outline" class="self-start sm:self-auto text-[10px] uppercase font-bold tracking-wider text-muted-foreground">
                Kebijakan Aktif
              </Badge>
            </div>

            <!-- Bagian A: Desain Sistem & UX -->
            <div class="space-y-2">
              <p class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                <CheckCircle2 class="h-3.5 w-3.5" /> A. Desain Sistem & Alur UX
              </p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs bg-muted/40 p-3.5 rounded-lg border border-border">
                <div>
                  <span class="font-semibold text-foreground">Frontend Experience:</span>
                  <p class="text-muted-foreground mt-0.5 leading-relaxed">{{ art.uxFrontend }}</p>
                </div>
                <div>
                  <span class="font-semibold text-foreground">Backend & Guard Logic:</span>
                  <p class="text-muted-foreground mt-0.5 leading-relaxed">{{ art.uxBackend }}</p>
                </div>
              </div>
            </div>

            <!-- Bagian B: Klausul Aturan -->
            <div class="space-y-1.5">
              <p class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                <FileText class="h-3.5 w-3.5" /> B. Klausul Isi Aturan
              </p>
              <blockquote class="border-l-4 border-primary bg-primary/5 px-4 py-3 rounded-r-lg text-xs font-medium text-foreground italic leading-relaxed">
                "{{ art.rule }}"
              </blockquote>
            </div>

            <!-- Bagian C: Justifikasi (Reason) -->
            <div class="space-y-1.5">
              <p class="text-xs font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                <Info class="h-3.5 w-3.5" /> C. Justifikasi & Analisis Risiko
              </p>
              <ul class="space-y-1.5 text-xs text-muted-foreground">
                <li v-for="(reason, rIdx) in art.reasons" :key="rIdx" class="flex items-start gap-2">
                  <span class="h-1.5 w-1.5 rounded-full bg-primary mt-1.5 shrink-0" />
                  <span class="leading-relaxed">{{ reason }}</span>
                </li>
              </ul>
            </div>
          </CardContent>
        </Card>
      </div>
    </section>

    <!-- ── Call to Action / Mulai Pinjam ──────────── -->
    <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h3 class="text-base font-bold text-foreground">Sudah Memahami Seluruh Ketentuan?</h3>
        <p class="text-xs text-muted-foreground">Mulai cek ketersediaan aset dan ajukan peminjaman ruangan atau barang.</p>
      </div>
      <div class="flex gap-2">
        <Link
          href="/dashboard"
          class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-primary-foreground font-semibold text-xs rounded-lg px-5 py-2.5 shadow-xs transition-all"
        >
          Buka Katalog Dashboard <ArrowRight class="h-4 w-4" />
        </Link>
      </div>
    </div>
  </div>
</template>
