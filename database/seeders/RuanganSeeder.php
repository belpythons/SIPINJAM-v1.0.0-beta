<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = [
            // ── Gedung Utama ──────────────────────────────
            ['nama' => 'Lab Arsikom',       'lokasi' => 'Gedung Utama',   'kapasitas' => 15, 'deskripsi' => 'Laboratorium Arsitektur Komputer untuk praktikum perangkat keras dan sistem digital.'],
            ['nama' => 'Lab Multimedia',    'lokasi' => 'Gedung Utama',   'kapasitas' => 20, 'deskripsi' => 'Laboratorium Multimedia untuk praktikum desain grafis, editing video, dan animasi.'],
            ['nama' => 'Lab Pemrograman',   'lokasi' => 'Gedung Utama',   'kapasitas' => 45, 'deskripsi' => 'Laboratorium Pemrograman untuk praktikum coding dan pengembangan perangkat lunak.'],
            ['nama' => 'Ruang Kelas 3A',    'lokasi' => 'Gedung Utama',   'kapasitas' => 25, 'deskripsi' => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 3.'],
            ['nama' => 'Ruang Kelas 3B',    'lokasi' => 'Gedung Utama',   'kapasitas' => 40, 'deskripsi' => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 3.'],
            ['nama' => 'Ruang Kelas 3C',    'lokasi' => 'Gedung Utama',   'kapasitas' => 60, 'deskripsi' => 'Ruang kelas besar untuk perkuliahan gabungan Gedung Utama lantai 3.'],
            ['nama' => 'Ruang Kelas 4A',    'lokasi' => 'Gedung Utama',   'kapasitas' => 30, 'deskripsi' => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 4.'],
            ['nama' => 'Ruang Kelas 4B',    'lokasi' => 'Gedung Utama',   'kapasitas' => 40, 'deskripsi' => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 4.'],

            // ── Gedung Djuanda ────────────────────────────
            ['nama' => 'Lab Kelistrikan',   'lokasi' => 'Gedung Djuanda', 'kapasitas' => 25, 'deskripsi' => 'Laboratorium Kelistrikan untuk praktikum rangkaian listrik dan elektronika.'],
            ['nama' => 'Ruang Kelas 2A',    'lokasi' => 'Gedung Djuanda', 'kapasitas' => 35, 'deskripsi' => 'Ruang kelas perkuliahan reguler Gedung Djuanda lantai 2.'],
        ];

        foreach ($ruangans as $index => $r) {
            Ruangan::updateOrCreate(
                ['kode' => 'R-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'      => $r['nama'],
                    'kapasitas' => $r['kapasitas'],
                    'lokasi'    => $r['lokasi'],
                    'deskripsi' => $r['deskripsi'],
                    'status'    => 'tersedia',
                ]
            );
        }
    }
}
