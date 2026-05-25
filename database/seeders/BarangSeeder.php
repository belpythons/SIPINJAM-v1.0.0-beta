<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            ['nama' => 'Smart TV',           'stok' => 1,  'kategori' => 'Elektronik'],
            ['nama' => 'Microphone',         'stok' => 3,  'kategori' => 'Audio'],
            ['nama' => 'Meja Tamu',          'stok' => 2,  'kategori' => 'Furnitur'],
            ['nama' => 'Sofa Kecil',         'stok' => 2,  'kategori' => 'Furnitur'],
            ['nama' => 'Sofa Besar',         'stok' => 1,  'kategori' => 'Furnitur'],
            ['nama' => 'Terminal Kabel',     'stok' => 7,  'kategori' => 'Kabel & Aksesoris'],
            ['nama' => 'Proyektor',          'stok' => 4,  'kategori' => 'Elektronik'],
            ['nama' => 'Mikrotik Jaringan',  'stok' => 13, 'kategori' => 'Jaringan'],
            ['nama' => 'Motherboard',        'stok' => 4,  'kategori' => 'Komputer'],
            ['nama' => 'Sound System',       'stok' => 2,  'kategori' => 'Audio'],
            ['nama' => 'Kipas Turbo',        'stok' => 3,  'kategori' => 'Pendingin'],
        ];

        foreach ($barangs as $index => $b) {
            Barang::updateOrCreate(
                ['kode' => 'B-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'           => $b['nama'],
                    'stok_total'     => $b['stok'],
                    'stok_tersedia'  => $b['stok'],
                    'kategori'       => $b['kategori'],
                    'deskripsi'      => 'Inventaris kampus STITEK Bontang: ' . $b['nama'],
                    'status'         => 'tersedia',
                ]
            );
        }
    }
}
