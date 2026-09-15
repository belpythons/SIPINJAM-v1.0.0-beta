<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Dokumen;
use App\Models\Pelanggaran;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use App\Models\Sanksi;
use App\Models\TataTertibVersion;
use App\Models\User;
use App\Services\NomorSuratService;
use App\Services\PengajuanStateMachine;
use Illuminate\Database\Seeder;

/**
 * Skenario demo untuk struktur data baru.
 *
 * Empat skenario yang menunjukkan apa yang TIDAK bisa direpresentasikan skema
 * lama: paket event berisi banyak aset, pengajuan tunggal, pengajuan bermasalah
 * lengkap dengan pelanggaran + sanksi aktif, serta riwayat 3 bulan ke belakang.
 */
class PengajuanDemoSeeder extends Seeder
{
    public function run(): void
    {
        $peminjam = User::role(config('sipinjam.peran.peminjam'))->inRandomOrder()->first();

        if ($peminjam === null) {
            $this->command?->warn('PengajuanDemoSeeder dilewati: belum ada pengguna berperan peminjam.');

            return;
        }

        $this->tataTertib($peminjam);
        $this->paketEvent($peminjam);
        $this->asetTunggal($peminjam);
        $this->bermasalahDenganSanksi($peminjam);
        $this->riwayatTigaBulan($peminjam);
    }

    /** Tata tertib versi 1 — dasar hukum penegakan sanksi. */
    private function tataTertib(User $peminjam): void
    {
        TataTertibVersion::firstOrCreate(
            ['versi' => '1.0'],
            [
                'konten' => "1. Peminjam bertanggung jawab atas kebersihan dan keamanan aset.\n"
                    ."2. Pengembalian tepat waktu sesuai jadwal yang disetujui.\n"
                    .'3. Kerusakan atau kehilangan menjadi tanggung jawab peminjam.',
                'berlaku_sejak' => now()->subMonths(6),
            ],
        );
    }

    /**
     * Skenario 1 — paket kegiatan: satu aula + beberapa barang dalam SATU berkas.
     *
     * Inilah yang mustahil di skema lama, di mana satu pengajuan = satu aset.
     */
    private function paketEvent(User $peminjam): void
    {
        $ruangan = Ruangan::inRandomOrder()->first();
        $barang = Barang::inRandomOrder()->take(4)->get();

        if ($ruangan === null || $barang->isEmpty()) {
            return;
        }

        $mulai = now()->addDays(14)->setTime(8, 0);
        $selesai = $mulai->copy()->setTime(17, 0);

        $pengajuan = Pengajuan::create([
            'kode' => 'SPJ-DEMO-EVENT',
            'user_id' => $peminjam->id,
            'jenis' => Pengajuan::JENIS_EVENT,
            'nama_kegiatan' => 'Seminar Nasional Teknologi Terapan',
            'jenis_kegiatan' => 'Akademik',
            'unit_penyelenggara' => 'Himpunan Mahasiswa Teknik Informatika',
            'jumlah_peserta' => 250,
            'pj_nama' => 'Ketua Panitia Seminar',
            'pj_phone' => '+628123456789',
            'deskripsi' => 'Seminar tahunan dengan pembicara dari industri.',
            'mulai_at' => $mulai,
            'selesai_at' => $selesai,
            'status' => Pengajuan::STATUS_DISETUJUI,
            'submitted_at' => now()->subDays(10),
            'nomor_surat' => app(NomorSuratService::class)->terbitkan(Dokumen::JENIS_SURAT_IZIN),
            'approved_at' => now()->subDays(3),
        ]);

        PengajuanItem::create([
            'pengajuan_id' => $pengajuan->id,
            'assetable_type' => Ruangan::class,
            'assetable_id' => $ruangan->id,
            'jumlah' => 1,
            'mulai_at' => $mulai,
            'selesai_at' => $selesai,
            'status_item' => PengajuanItem::STATUS_DISETUJUI,
        ]);

        foreach ($barang as $b) {
            PengajuanItem::create([
                'pengajuan_id' => $pengajuan->id,
                'assetable_type' => Barang::class,
                'assetable_id' => $b->id,
                'jumlah' => min(2, max(1, (int) $b->stok_total)),
                'mulai_at' => $mulai,
                'selesai_at' => $selesai,
                'status_item' => PengajuanItem::STATUS_DISETUJUI,
            ]);
        }

        app(PengajuanStateMachine::class)->catat(
            $pengajuan,
            'Data contoh dibuat oleh seeder',
            null,
            Pengajuan::STATUS_DISETUJUI,
        );
    }

    /** Skenario 2 — pengajuan aset tunggal. */
    private function asetTunggal(User $peminjam): void
    {
        $barang = Barang::inRandomOrder()->first();

        if ($barang === null) {
            return;
        }

        $mulai = now()->addDays(5)->setTime(9, 0);

        $pengajuan = Pengajuan::create([
            'kode' => 'SPJ-DEMO-TUNGGAL',
            'user_id' => $peminjam->id,
            'jenis' => Pengajuan::JENIS_TUNGGAL,
            'deskripsi' => 'Presentasi tugas akhir.',
            'mulai_at' => $mulai,
            'selesai_at' => $mulai->copy()->addHours(3),
            'status' => Pengajuan::STATUS_DIAJUKAN,
            'submitted_at' => now(),
        ]);

        PengajuanItem::create([
            'pengajuan_id' => $pengajuan->id,
            'assetable_type' => Barang::class,
            'assetable_id' => $barang->id,
            'jumlah' => 1,
            'mulai_at' => $mulai,
            'selesai_at' => $mulai->copy()->addHours(3),
            'status_item' => PengajuanItem::STATUS_DIAJUKAN,
        ]);
    }

    /**
     * Skenario 3 — pengajuan bermasalah: pelanggaran menunjuk BARIS ASET tertentu,
     * dan sanksinya menunjuk pelanggaran itu.
     *
     * Rantai inilah yang membuat sanksi dapat dipertanggungjawabkan; pada kode
     * lama pelaku ditebak dari kedekatan waktu (B-05).
     */
    private function bermasalahDenganSanksi(User $peminjam): void
    {
        $ruangan = Ruangan::inRandomOrder()->first();

        if ($ruangan === null) {
            return;
        }

        $mulai = now()->subDays(7)->setTime(8, 0);

        $pengajuan = Pengajuan::create([
            'kode' => 'SPJ-DEMO-BERMASALAH',
            'user_id' => $peminjam->id,
            'jenis' => Pengajuan::JENIS_TUNGGAL,
            'deskripsi' => 'Rapat organisasi.',
            'mulai_at' => $mulai,
            'selesai_at' => $mulai->copy()->addHours(4),
            'status' => Pengajuan::STATUS_BERMASALAH,
            'submitted_at' => now()->subDays(12),
        ]);

        $item = PengajuanItem::create([
            'pengajuan_id' => $pengajuan->id,
            'assetable_type' => Ruangan::class,
            'assetable_id' => $ruangan->id,
            'jumlah' => 1,
            'mulai_at' => $mulai,
            'selesai_at' => $mulai->copy()->addHours(4),
            'status_item' => PengajuanItem::STATUS_BERMASALAH,
        ]);

        $pelanggaran = Pelanggaran::create([
            'user_id' => $pengajuan->user_id,   // dari data, bukan tebakan
            'pengajuan_item_id' => $item->id,
            'jenis' => 'tidak_bersih',
            'poin' => 2,
            'deskripsi' => 'Ruangan ditinggalkan dalam keadaan kotor; sampah tidak dibuang.',
            'status_tindak_lanjut' => Pelanggaran::TINDAK_MENUNGGU,
        ]);

        Sanksi::create([
            'user_id' => $pengajuan->user_id,
            'pelanggaran_id' => $pelanggaran->id,
            'jenis' => Sanksi::JENIS_BLOKIR,
            'mulai_at' => now()->subDays(2),
            'sampai_at' => now()->addDays(5),
            'alasan' => 'Pelanggaran kebersihan ruangan.',
        ]);
    }

    /** Skenario 4 — riwayat 3 bulan ke belakang yang sudah tuntas. */
    private function riwayatTigaBulan(User $peminjam): void
    {
        $ruangan = Ruangan::inRandomOrder()->first();

        if ($ruangan === null) {
            return;
        }

        foreach (range(1, 12) as $i) {
            $mulai = now()->subDays($i * 7)->setTime(8, 0);

            $pengajuan = Pengajuan::create([
                'kode' => 'SPJ-DEMO-RIWAYAT-'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'user_id' => $peminjam->id,
                'jenis' => Pengajuan::JENIS_TUNGGAL,
                'deskripsi' => 'Kegiatan rutin mingguan.',
                'mulai_at' => $mulai,
                'selesai_at' => $mulai->copy()->addHours(3),
                'status' => Pengajuan::STATUS_SELESAI,
                'submitted_at' => $mulai->copy()->subDays(3),
                'created_at' => $mulai->copy()->subDays(3),
            ]);

            PengajuanItem::create([
                'pengajuan_id' => $pengajuan->id,
                'assetable_type' => Ruangan::class,
                'assetable_id' => $ruangan->id,
                'jumlah' => 1,
                'mulai_at' => $mulai,
                'selesai_at' => $mulai->copy()->addHours(3),
                'status_item' => PengajuanItem::STATUS_SELESAI,
            ]);
        }
    }
}
