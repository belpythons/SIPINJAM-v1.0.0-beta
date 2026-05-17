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
            ['nama' => 'Laboratorium Komputer A', 'lokasi' => 'Kampus Utama - Lt 1', 'kapasitas' => 40, 'fasilitas' => 'AC, Proyektor, 40 Unit PC'],
            ['nama' => 'Laboratorium Komputer B', 'lokasi' => 'Kampus Utama - Lt 1', 'kapasitas' => 30, 'fasilitas' => 'AC, Proyektor, 30 Unit PC'],
            ['nama' => 'Lab Multimedia',          'lokasi' => 'Kampus Utama - Lt 2', 'kapasitas' => 25, 'fasilitas' => 'AC, Proyektor, Papan Tulis'],
            ['nama' => 'Lab Pemrograman',          'lokasi' => 'Kampus Utama - Lt 2', 'kapasitas' => 35, 'fasilitas' => 'AC, Proyektor, 35 Unit PC'],
            ['nama' => 'Ruang Rapat',              'lokasi' => 'Kampus Utama - Lt 3', 'kapasitas' => 20, 'fasilitas' => 'AC, Proyektor, Papan Tulis'],
            ['nama' => 'Kelas Teori 1',            'lokasi' => 'Kampus Utama - Lt 3', 'kapasitas' => 45, 'fasilitas' => 'AC, Papan Tulis'],
            ['nama' => 'Kelas Teori 2',            'lokasi' => 'Kampus Utama - Lt 3', 'kapasitas' => 45, 'fasilitas' => 'AC, Papan Tulis'],
            ['nama' => 'Aula Serbaguna',           'lokasi' => 'Kampus Utama - Lt 4', 'kapasitas' => 100, 'fasilitas' => 'AC, Proyektor, Sound System, Podium'],
            ['nama' => 'Perpustakaan',             'lokasi' => 'Kampus Utama - Lt 1', 'kapasitas' => 50, 'fasilitas' => 'AC, WiFi'],

            // Kampus Djuanda
            ['nama' => 'Lab Kelistrikan',          'lokasi' => 'Kampus Djuanda - Lt 2', 'kapasitas' => 30, 'fasilitas' => 'AC, Peralatan Praktikum'],
            ['nama' => 'Ruang Seminar',            'lokasi' => 'Kampus Djuanda - Lt 2', 'kapasitas' => 60, 'fasilitas' => 'AC, Proyektor, Sound System'],
            ['nama' => 'Kelas Teori 3',            'lokasi' => 'Kampus Djuanda - Lt 2', 'kapasitas' => 40, 'fasilitas' => 'AC, Papan Tulis'],
        ];

        foreach ($ruangans as $index => $r) {
            Ruangan::updateOrCreate(
                ['kode' => 'R-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'      => $r['nama'],
                    'kapasitas' => $r['kapasitas'],
                    'lokasi'    => $r['lokasi'],
                    'deskripsi' => 'Fasilitas: ' . $r['fasilitas'],
                    'status'    => 'tersedia',
                ]
            );
        }
    }
}
