<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        Ruangan::query()->delete();

        $ruangans = [
            // Kelas Teori (7 ruangan)
            [
                'nama'       => 'Ruang Kelas 2C',
                'lokasi'     => 'Gedung Djuanda',
                'kapasitas'  => 35,
                'deskripsi'  => 'Ruang kelas teori untuk perkuliahan reguler Gedung Djuanda lantai 2.',
                'image_path' => '2C.png',
            ],
            [
                'nama'       => 'Ruang Kelas 2D',
                'lokasi'     => 'Gedung Djuanda',
                'kapasitas'  => 35,
                'deskripsi'  => 'Ruang kelas teori untuk perkuliahan reguler Gedung Djuanda lantai 2.',
                'image_path' => '2D.png',
            ],
            [
                'nama'       => 'Ruang Kelas 3A',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 30,
                'deskripsi'  => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 3.',
                'image_path' => '3A.png',
            ],
            [
                'nama'       => 'Ruang Kelas 3B',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 40,
                'deskripsi'  => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 3.',
                'image_path' => '3B.png',
            ],
            [
                'nama'       => 'Ruang Kelas 3C',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 60,
                'deskripsi'  => 'Ruang kelas besar untuk perkuliahan gabungan Gedung Utama lantai 3.',
                'image_path' => '3C.png',
            ],
            [
                'nama'       => 'Ruang Kelas 4A',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 30,
                'deskripsi'  => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 4.',
                'image_path' => '4A.png',
            ],
            [
                'nama'       => 'Ruang Kelas 4B',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 40,
                'deskripsi'  => 'Ruang kelas perkuliahan reguler Gedung Utama lantai 4.',
                'image_path' => '4B.png',
            ],
            // Laboratorium (4 ruangan)
            [
                'nama'       => 'Lab Mikrocontroller',
                'lokasi'     => 'Gedung Djuanda',
                'kapasitas'  => 25,
                'deskripsi'  => 'Laboratorium Mikrocontroller untuk praktikum embedded system, robotika, dan IoT.',
                'image_path' => 'Lab_mikrocontroller.jpg',
            ],
            [
                'nama'       => 'Lab Arsikom',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 20,
                'deskripsi'  => 'Laboratorium Arsitektur Komputer untuk praktikum perangkat keras dan sistem digital.',
                'image_path' => 'lab_arsikom.jpg',
            ],
            [
                'nama'       => 'Lab Multimedia',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 25,
                'deskripsi'  => 'Laboratorium Multimedia untuk praktikum desain grafis, editing video, dan animasi.',
                'image_path' => 'lab_multimedia.jpg',
            ],
            [
                'nama'       => 'Lab Pemrograman 2',
                'lokasi'     => 'Gedung Utama',
                'kapasitas'  => 45,
                'deskripsi'  => 'Laboratorium Pemrograman untuk praktikum coding tingkat lanjut dan web development.',
                'image_path' => 'lab_pemrograman2.jpg',
            ],
        ];

        foreach ($ruangans as $index => $r) {
            Ruangan::create([
                'kode'       => 'R-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama'       => $r['nama'],
                'kapasitas'  => $r['kapasitas'],
                'lokasi'     => $r['lokasi'],
                'deskripsi'  => $r['deskripsi'],
                'status'     => 'tersedia',
                'image_path' => '/storage/ruangan/' . $r['image_path'],
            ]);
        }
    }
}
