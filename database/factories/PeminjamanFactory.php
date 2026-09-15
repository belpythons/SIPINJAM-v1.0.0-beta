<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Dokumen;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    protected $model = Peminjaman::class;

    public function definition(): array
    {
        // Get random user (peran Spatie, bukan kolom `role` yang sudah dihapus)
        $user = User::role(config('sipinjam.peran.peminjam'))->inRandomOrder()->first();
        if (! $user) {
            $user = User::factory()->peminjam()->create();
        }

        $tipe = $this->faker->randomElement(['ruangan', 'barang']);

        $barangId = null;
        $ruanganId = null;
        $namaItem = '';

        if ($tipe === 'ruangan') {
            $ruangan = Ruangan::inRandomOrder()->first();
            if ($ruangan) {
                $ruanganId = $ruangan->id;
                $namaItem = $ruangan->nama;
            } else {
                $tipe = 'barang'; // Fallback if no ruangan in DB
            }
        }

        if ($tipe === 'barang') {
            $barang = Barang::inRandomOrder()->first();
            if ($barang) {
                $barangId = $barang->id;
                $namaItem = $barang->nama;
            }
        }

        // Random date between 2026-04-01 and 2026-08-31
        $startDate = Carbon::instance($this->faker->dateTimeBetween('2026-04-01', '2026-08-31'));
        $endDate = (clone $startDate)->addDays($this->faker->randomElement([0, 1]));

        $jamMulai = $this->faker->randomElement(['08:00', '09:00', '10:00', '13:00', '14:00']);
        $jamSelesai = $this->faker->randomElement(['12:00', '15:00', '16:00', '17:00', '18:00']);

        // Weighted status: 'selesai' has highest weight
        $status = $this->faker->randomElement([
            'menunggu', 'menunggu',
            'sedang_dipinjam', 'sedang_dipinjam',
            'ditolak',
            'selesai', 'selesai', 'selesai', 'selesai', 'selesai', // highest weight
        ]);

        $approvedAt = null;
        $completedAt = null;
        $nomorSurat = null;

        if (in_array($status, ['sedang_dipinjam', 'selesai'])) {
            // Satu sumber penomoran: NomorSuratService (T-02).
            $nomorSurat = app(NomorSuratService::class)->terbitkan(Dokumen::JENIS_SURAT_IZIN);

            // Approved at start date or slightly before
            $approvedAt = (clone $startDate)->subHours(rand(1, 24));

            if ($status === 'selesai') {
                // Completed at the end date at the end hour
                $completedAt = (clone $endDate)->setTimeFromTimeString($jamSelesai);
            }
        }

        $keteranganOptions = [
            'Rapat Himpunan Mahasiswa',
            'Praktikum Jaringan Komputer',
            'Sidang Skripsi Akhir',
            'Seminar Nasional STITEK',
            'Workshop Web Development',
            'Kegiatan Unit Kegiatan Mahasiswa (UKM)',
            'Rapat Koordinasi Prodi',
            'Kelas Pengganti Pemrograman',
            'Kerja Kelompok Mahasiswa',
        ];
        $keterangan = $this->faker->randomElement($keteranganOptions);

        return [
            'user_id' => $user->id,
            'tipe' => $tipe,
            'barang_id' => $barangId,
            'ruangan_id' => $ruanganId,
            'nama_item' => $namaItem,
            'tanggal' => $startDate->format('Y-m-d'),
            'tanggal_mulai' => $startDate->format('Y-m-d'),
            'tanggal_selesai' => $endDate->format('Y-m-d'),
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'keterangan' => $keterangan,
            'status' => $status,
            'nomor_surat' => $nomorSurat,
            'approved_at' => $approvedAt,
            'completed_at' => $completedAt,
        ];
    }
}
