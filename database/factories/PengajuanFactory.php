<?php

namespace Database\Factories;

use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengajuan>
 */
class PengajuanFactory extends Factory
{
    protected $model = Pengajuan::class;

    public function definition(): array
    {
        $mulai = $this->faker->dateTimeBetween('+1 week', '+2 months');
        $selesai = (clone $mulai)->modify('+4 hours');

        return [
            'kode' => 'SPJ-'.$this->faker->unique()->numerify('######'),
            'user_id' => User::factory()->peminjam(),
            'jenis' => Pengajuan::JENIS_TUNGGAL,
            'status' => Pengajuan::STATUS_DIAJUKAN,
            'mulai_at' => $mulai,
            'selesai_at' => $selesai,
            'submitted_at' => now(),
        ];
    }

    /** Pengajuan paket kegiatan, lengkap dengan identitas & penanggung jawab. */
    public function event(): static
    {
        return $this->state(fn () => [
            'jenis' => Pengajuan::JENIS_EVENT,
            'nama_kegiatan' => $this->faker->randomElement([
                'Seminar Nasional Teknologi', 'Workshop Robotika', 'Dies Natalis STITEK',
            ]),
            'jenis_kegiatan' => $this->faker->randomElement(['Akademik', 'Kemahasiswaan', 'Pengabdian']),
            'unit_penyelenggara' => $this->faker->randomElement(['HMTI', 'BEM', 'Prodi Informatika']),
            'jumlah_peserta' => $this->faker->numberBetween(50, 300),
            'pj_nama' => $this->faker->name(),
            'pj_phone' => '+62812'.$this->faker->numerify('#######'),
            'deskripsi' => 'Kegiatan tahunan unit penyelenggara.',
        ]);
    }

    public function berstatus(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
