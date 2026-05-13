<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = [
            // Kampus Utama
            ['nama' => 'Perpustakaan',     'lokasi' => 'Kampus Utama - Lt 1'],
            ['nama' => 'Lab Arsikom',      'lokasi' => 'Kampus Utama - Lt 1'],
            ['nama' => 'Lab Multimedia',   'lokasi' => 'Kampus Utama - Lt 2'],
            ['nama' => 'Lab Pemrograman',  'lokasi' => 'Kampus Utama - Lt 2'],
            ['nama' => 'Ruang 3A',         'lokasi' => 'Kampus Utama - Lt 3'],
            ['nama' => 'Ruang 3B',         'lokasi' => 'Kampus Utama - Lt 3'],
            ['nama' => 'Ruang 3C',         'lokasi' => 'Kampus Utama - Lt 3'],
            ['nama' => 'Ruang 4A',         'lokasi' => 'Kampus Utama - Lt 4'],
            ['nama' => 'Ruang 4B',         'lokasi' => 'Kampus Utama - Lt 4'],
            // Kampus Djuanda
            ['nama' => 'Ruang 2A',         'lokasi' => 'Kampus Djuanda - Lt 2'],
            ['nama' => 'Ruang 2B',         'lokasi' => 'Kampus Djuanda - Lt 2'],
            ['nama' => 'Lab Kelistrikan',  'lokasi' => 'Kampus Djuanda - Lt 2'],
        ];

        $fasilitasPool = ['AC', 'Papan Tulis', 'Proyektor'];

        foreach ($ruangans as $index => $r) {
            // Pilih 1-3 fasilitas secara random
            $jumlahFasilitas = rand(1, count($fasilitasPool));
            $fasilitasTerpilih = implode(', ', array_slice(
                collect($fasilitasPool)->shuffle()->all(),
                0,
                $jumlahFasilitas
            ));

            Ruangan::updateOrCreate(
                ['kode' => 'R-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'      => $r['nama'],
                    'kapasitas' => rand(30, 50),
                    'lokasi'    => $r['lokasi'],
                    'deskripsi' => 'Fasilitas: ' . $fasilitasTerpilih,
                    'status'    => 'tersedia',
                ]
            );
        }
    }
}
