<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        Barang::query()->delete();

        $barangs = [
            // Elektronik (6 items)
            [
                'nama'       => 'Proyektor',
                'stok'       => 4,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Proyektor LCD Epson untuk perkuliahan dan presentasi kelas.',
                'image_path' => 'proyektor.jpeg',
            ],
            [
                'nama'       => 'Smart TV',
                'stok'       => 2,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Smart TV UHD 55 inci untuk penayangan media interaktif.',
                'image_path' => 'smart tv.jpeg',
            ],
            [
                'nama'       => 'Sound System',
                'stok'       => 3,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Perangkat sound system portable + amplifier untuk acara perkuliahan besar.',
                'image_path' => 'sound system.jpeg',
            ],
            [
                'nama'       => 'Mikrotik Jaringan',
                'stok'       => 10,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Routerboard Mikrotik untuk praktikum jaringan komputer dan IoT.',
                'image_path' => 'mikrotik jaringan.jpeg',
            ],
            [
                'nama'       => 'Microphone',
                'stok'       => 6,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Microphone wireless UHF untuk pemandu seminar dan perkuliahan.',
                'image_path' => 'microphone.jpeg',
            ],
            [
                'nama'       => 'Terminal Kabel',
                'stok'       => 15,
                'kategori'   => 'Elektronik',
                'deskripsi'  => 'Stop kontak terminal listrik 5 lubang dengan kabel panjang 5 meter.',
                'image_path' => 'terminal kabel.jpeg',
            ],
            // Furnitur (4 items)
            [
                'nama'       => 'Meja Tamu',
                'stok'       => 5,
                'kategori'   => 'Furnitur',
                'deskripsi'  => 'Meja tamu kayu minimalis untuk rapat senat dan ruang tunggu VIP.',
                'image_path' => 'meja tamu.jpeg',
            ],
            [
                'nama'       => 'Sofa Besar',
                'stok'       => 2,
                'kategori'   => 'Furnitur',
                'deskripsi'  => 'Sofa panjang empuk kapasitas 3 orang untuk ruang transit dosen.',
                'image_path' => 'sofa besar.jpeg',
            ],
            [
                'nama'       => 'Sofa Kecil',
                'stok'       => 4,
                'kategori'   => 'Furnitur',
                'deskripsi'  => 'Sofa single minimalis untuk diskusi kecil atau ruang dosen.',
                'image_path' => 'sofa kecil.jpeg',
            ],
            [
                'nama'       => 'Podium',
                'stok'       => 2,
                'kategori'   => 'Furnitur',
                'deskripsi'  => 'Podium kayu jati lambang kampus untuk pidato dan seminar akademik.',
                'image_path' => 'podium.jpeg',
            ],
        ];

        foreach ($barangs as $index => $b) {
            Barang::create([
                'kode'           => 'B-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama'           => $b['nama'],
                'stok_total'     => $b['stok'],
                'stok_tersedia'  => $b['stok'],
                'kategori'       => $b['kategori'],
                'deskripsi'      => $b['deskripsi'],
                'status'         => 'tersedia',
                'image_path'     => '/storage/barang/' . $b['image_path'],
            ]);
        }
    }
}
