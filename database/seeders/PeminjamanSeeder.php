<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Truncate / Hapus tabel peminjaman
        Peminjaman::query()->delete();

        // 2. Ambil Akun Mahasiswa spesifik
        $zulfa = User::where('email', '202312017@stitek.ac.id')->firstOrFail();
        $miya = User::where('email', '202312023@stitek.ac.id')->firstOrFail();
        $taliya = User::where('email', '202312030@gmail.com')->firstOrFail();
        $salsa = User::where('email', '202312016@stitek.ac.id')->firstOrFail();
        $elin = User::where('email', '202312028@stitek.ac.id')->firstOrFail();
        $brama = User::where('email', '202312066@stitek.ac.id')->firstOrFail();

        // 3. Ambil Ruangan
        $ruang2C = Ruangan::where('nama', 'Ruang Kelas 2C')->firstOrFail();
        $ruang3B = Ruangan::where('nama', 'Ruang Kelas 3B')->firstOrFail();
        $ruang3C = Ruangan::where('nama', 'Ruang Kelas 3C')->firstOrFail();
        $ruang4A = Ruangan::where('nama', 'Ruang Kelas 4A')->firstOrFail();
        $ruang4B = Ruangan::where('nama', 'Ruang Kelas 4B')->firstOrFail();
        $labArsikom = Ruangan::where('nama', 'Lab Arsikom')->firstOrFail();
        $labMultimedia = Ruangan::where('nama', 'Lab Multimedia')->firstOrFail();

        // 4. Ambil Barang
        $proyektor = Barang::where('nama', 'Proyektor')->firstOrFail();
        $smartTV = Barang::where('nama', 'Smart TV')->firstOrFail();
        $soundSystem = Barang::where('nama', 'Sound System')->firstOrFail();
        $microphone = Barang::where('nama', 'Microphone')->firstOrFail();
        $terminalKabel = Barang::where('nama', 'Terminal Kabel')->firstOrFail();
        $mejaTamu = Barang::where('nama', 'Meja Tamu')->firstOrFail();
        $sofaBesar = Barang::where('nama', 'Sofa Besar')->firstOrFail();
        $podium = Barang::where('nama', 'Podium')->firstOrFail();

        // ─────────────── BULAN LALU (APRIL) ───────────────
        $lastMonth = Carbon::now()->subMonth();

        // 1. Zulfa: Ruang 3C (Selesai)
        Peminjaman::create([
            'user_id'         => $zulfa->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $ruang3C->id,
            'nama_item'       => $ruang3C->nama,
            'tanggal'         => $lastMonth->copy()->setDate($lastMonth->year, 4, 5)->format('Y-m-d'),
            'tanggal_mulai'   => $lastMonth->copy()->setDate($lastMonth->year, 4, 5)->format('Y-m-d'),
            'tanggal_selesai' => $lastMonth->copy()->setDate($lastMonth->year, 4, 5)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Rapat Kepanitiaan Bulan Bahasa',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $lastMonth->copy()->setDate($lastMonth->year, 4, 4),
            'completed_at'    => $lastMonth->copy()->setDate($lastMonth->year, 4, 5)->setTime(12, 0),
        ]);

        // 2. Salsa: Terminal Kabel (Selesai)
        Peminjaman::create([
            'user_id'         => $salsa->id,
            'tipe'            => 'barang',
            'barang_id'       => $terminalKabel->id,
            'nama_item'       => $terminalKabel->nama,
            'tanggal'         => $lastMonth->copy()->setDate($lastMonth->year, 4, 10)->format('Y-m-d'),
            'tanggal_mulai'   => $lastMonth->copy()->setDate($lastMonth->year, 4, 10)->format('Y-m-d'),
            'tanggal_selesai' => $lastMonth->copy()->setDate($lastMonth->year, 4, 10)->format('Y-m-d'),
            'jam_mulai'       => '13:00',
            'jam_selesai'     => '17:00',
            'keterangan'      => 'Kebutuhan Listrik Stand Pameran UKM',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $lastMonth->copy()->setDate($lastMonth->year, 4, 9),
            'completed_at'    => $lastMonth->copy()->setDate($lastMonth->year, 4, 10)->setTime(17, 0),
        ]);

        // 3. Elin: Sound System (Selesai)
        Peminjaman::create([
            'user_id'         => $elin->id,
            'tipe'            => 'barang',
            'barang_id'       => $soundSystem->id,
            'nama_item'       => $soundSystem->nama,
            'tanggal'         => $lastMonth->copy()->setDate($lastMonth->year, 4, 15)->format('Y-m-d'),
            'tanggal_mulai'   => $lastMonth->copy()->setDate($lastMonth->year, 4, 15)->format('Y-m-d'),
            'tanggal_selesai' => $lastMonth->copy()->setDate($lastMonth->year, 4, 15)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '16:00',
            'keterangan'      => 'Seminar Regional Pemberdayaan Pemuda',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $lastMonth->copy()->setDate($lastMonth->year, 4, 14),
            'completed_at'    => $lastMonth->copy()->setDate($lastMonth->year, 4, 15)->setTime(16, 0),
        ]);

        // 4. Brama: Microphone (Selesai)
        Peminjaman::create([
            'user_id'         => $brama->id,
            'tipe'            => 'barang',
            'barang_id'       => $microphone->id,
            'nama_item'       => $microphone->nama,
            'tanggal'         => $lastMonth->copy()->setDate($lastMonth->year, 4, 20)->format('Y-m-d'),
            'tanggal_mulai'   => $lastMonth->copy()->setDate($lastMonth->year, 4, 20)->format('Y-m-d'),
            'tanggal_selesai' => $lastMonth->copy()->setDate($lastMonth->year, 4, 20)->format('Y-m-d'),
            'jam_mulai'       => '09:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Debat Calon Ketua Himpunan Mahasiswa',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $lastMonth->copy()->setDate($lastMonth->year, 4, 19),
            'completed_at'    => $lastMonth->copy()->setDate($lastMonth->year, 4, 20)->setTime(12, 0),
        ]);

        // 5. Miya (Pemicu Blokir): Lab Arsikom (Selesai)
        Peminjaman::create([
            'user_id'         => $miya->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $labArsikom->id,
            'nama_item'       => $labArsikom->nama,
            'tanggal'         => $lastMonth->copy()->setDate($lastMonth->year, 4, 25)->format('Y-m-d'),
            'tanggal_mulai'   => $lastMonth->copy()->setDate($lastMonth->year, 4, 25)->format('Y-m-d'),
            'tanggal_selesai' => $lastMonth->copy()->setDate($lastMonth->year, 4, 25)->format('Y-m-d'),
            'jam_mulai'       => '13:00',
            'jam_selesai'     => '16:00',
            'keterangan'      => 'Praktikum Jaringan',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $lastMonth->copy()->setDate($lastMonth->year, 4, 24),
            'completed_at'    => $lastMonth->copy()->setDate($lastMonth->year, 4, 25)->setTime(16, 0),
        ]);

        // ─────────────── BULAN INI (MEI) ───────────────
        $now = Carbon::now();

        // 6. Ruangan Terpakai HARI INI (Absolut): Brama (Ruang 4A, Sedang Dipinjam)
        Peminjaman::create([
            'user_id'         => $brama->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $ruang4A->id,
            'nama_item'       => $ruang4A->nama,
            'tanggal'         => $now->format('Y-m-d'),
            'tanggal_mulai'   => $now->format('Y-m-d'),
            'tanggal_selesai' => $now->format('Y-m-d'),
            'jam_mulai'       => '00:00',
            'jam_selesai'     => '23:59',
            'keterangan'      => 'Pameran Karya Seni Digital Mahasiswa Akhir',
            'status'          => Peminjaman::STATUS_APPROVED,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subDay(),
        ]);

        // 7. Salsa: Smart TV (Sedang Dipinjam, Hari ini)
        Peminjaman::create([
            'user_id'         => $salsa->id,
            'tipe'            => 'barang',
            'barang_id'       => $smartTV->id,
            'nama_item'       => $smartTV->nama,
            'tanggal'         => $now->format('Y-m-d'),
            'tanggal_mulai'   => $now->format('Y-m-d'),
            'tanggal_selesai' => $now->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Presentasi Projek Akhir Mata Kuliah Multimedia',
            'status'          => Peminjaman::STATUS_APPROVED,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subHours(6),
        ]);

        // 8. Elin: Smart TV (Sedang Dipinjam, Hari ini)
        Peminjaman::create([
            'user_id'         => $elin->id,
            'tipe'            => 'barang',
            'barang_id'       => $smartTV->id,
            'nama_item'       => $smartTV->nama,
            'tanggal'         => $now->format('Y-m-d'),
            'tanggal_mulai'   => $now->format('Y-m-d'),
            'tanggal_selesai' => $now->format('Y-m-d'),
            'jam_mulai'       => '13:00',
            'jam_selesai'     => '17:00',
            'keterangan'      => 'Sidang Kelompok Skripsi Sistem Informasi',
            'status'          => Peminjaman::STATUS_APPROVED,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subHours(2),
        ]);

        // Update stok_tersedia menjadi 0 untuk mendemonstrasikan barang habis
        $smartTV->update(['stok_tersedia' => 0]);

        // 9. Taliya (Overdue): Proyektor (Sedang Dipinjam)
        Peminjaman::create([
            'user_id'         => $taliya->id,
            'tipe'            => 'barang',
            'barang_id'       => $proyektor->id,
            'nama_item'       => $proyektor->nama,
            'tanggal'         => $now->copy()->subDays(10)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->subDays(10)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->subDays(7)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '17:00',
            'keterangan'      => 'Kegiatan Pameran UKM Kreatif STITEK',
            'status'          => Peminjaman::STATUS_APPROVED,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subDays(11),
        ]);

        // 10. Zulfa: Ruang 3B (Selesai, 3 hari lalu)
        Peminjaman::create([
            'user_id'         => $zulfa->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $ruang3B->id,
            'nama_item'       => $ruang3B->nama,
            'tanggal'         => $now->copy()->subDays(3)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->subDays(3)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->subDays(3)->format('Y-m-d'),
            'jam_mulai'       => '09:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Perkuliahan Pengganti Matematika Diskrit',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subDays(4),
            'completed_at'    => $now->copy()->subDays(3)->setTime(12, 0),
        ]);

        // 11. Salsa: Meja Tamu (Selesai, 1 minggu lalu)
        Peminjaman::create([
            'user_id'         => $salsa->id,
            'tipe'            => 'barang',
            'barang_id'       => $mejaTamu->id,
            'nama_item'       => $mejaTamu->nama,
            'tanggal'         => $now->copy()->subWeeks(1)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->subWeeks(1)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->subWeeks(1)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '15:00',
            'keterangan'      => 'Rapat Kerja Himpunan Mahasiswa Informatika',
            'status'          => Peminjaman::STATUS_DONE,
            'nomor_surat'     => Peminjaman::generateNomorSurat(),
            'approved_at'     => $now->copy()->subWeeks(1)->subDay(),
            'completed_at'    => $now->copy()->subWeeks(1)->setTime(15, 0),
        ]);

        // 12. Brama: Podium (Menunggu, untuk 2 hari ke depan)
        Peminjaman::create([
            'user_id'         => $brama->id,
            'tipe'            => 'barang',
            'barang_id'       => $podium->id,
            'nama_item'       => $podium->nama,
            'tanggal'         => $now->copy()->addDays(2)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->addDays(2)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->addDays(2)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '12:00',
            'keterangan'      => 'Kebutuhan Lomba Pidato Bahasa Inggris STITEK',
            'status'          => Peminjaman::STATUS_PENDING,
        ]);

        // 13. Elin: Lab Multimedia (Ditolak)
        Peminjaman::create([
            'user_id'         => $elin->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $labMultimedia->id,
            'nama_item'       => $labMultimedia->nama,
            'tanggal'         => $now->copy()->addDays(1)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->addDays(1)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '16:00',
            'keterangan'      => 'Ditolak Admin: Ruangan sedang dipakai untuk ujian sertifikasi.',
            'status'          => Peminjaman::STATUS_REJECTED,
        ]);

        // ─────────────── BULAN DEPAN (JUNI) ───────────────
        $nextMonth = Carbon::now()->addMonth();

        // 14. Zulfa: Ruang 2C (Menunggu, minggu depan)
        Peminjaman::create([
            'user_id'         => $zulfa->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $ruang2C->id,
            'nama_item'       => $ruang2C->nama,
            'tanggal'         => $now->copy()->addWeek()->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->addWeek()->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->addWeek()->format('Y-m-d'),
            'jam_mulai'       => '09:00',
            'jam_selesai'     => '13:00',
            'keterangan'      => 'Kelas Tambahan Kalkulus',
            'status'          => Peminjaman::STATUS_PENDING,
        ]);

        // 15. Salsa: Sofa Besar (Menunggu, 2 minggu lagi)
        Peminjaman::create([
            'user_id'         => $salsa->id,
            'tipe'            => 'barang',
            'barang_id'       => $sofaBesar->id,
            'nama_item'       => $sofaBesar->nama,
            'tanggal'         => $now->copy()->addWeeks(2)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->addWeeks(2)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->addWeeks(2)->format('Y-m-d'),
            'jam_mulai'       => '08:00',
            'jam_selesai'     => '17:00',
            'keterangan'      => 'Kebutuhan Ruang Transit Pembicara Talkshow Nasional',
            'status'          => Peminjaman::STATUS_PENDING,
        ]);

        // 16. Elin: Ruang 4B (Menunggu, 3 minggu lagi)
        Peminjaman::create([
            'user_id'         => $elin->id,
            'tipe'            => 'ruangan',
            'ruangan_id'      => $ruang4B->id,
            'nama_item'       => $ruang4B->nama,
            'tanggal'         => $now->copy()->addWeeks(3)->format('Y-m-d'),
            'tanggal_mulai'   => $now->copy()->addWeeks(3)->format('Y-m-d'),
            'tanggal_selesai' => $now->copy()->addWeeks(3)->format('Y-m-d'),
            'jam_mulai'       => '10:00',
            'jam_selesai'     => '14:00',
            'keterangan'      => 'Diskusi Panel Antar Organisasi Kampus',
            'status'          => Peminjaman::STATUS_PENDING,
        ]);
    }
}
