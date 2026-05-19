<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            ['nama' => 'Proyektor Epson',       'stok' => 10, 'kategori' => 'Elektronik'],
            ['nama' => 'Kabel HDMI',             'stok' => 20, 'kategori' => 'Kabel & Aksesoris'],
            ['nama' => 'Mikrofon Wireless',      'stok' => 8,  'kategori' => 'Audio'],
            ['nama' => 'Speaker Portable',       'stok' => 5,  'kategori' => 'Audio'],
            ['nama' => 'Laptop Asus',            'stok' => 6,  'kategori' => 'Komputer'],
            ['nama' => 'Pointer Presenter',      'stok' => 10, 'kategori' => 'Aksesoris'],
            ['nama' => 'Extension Kabel 10m',    'stok' => 15, 'kategori' => 'Kabel & Aksesoris'],
            ['nama' => 'Papan Tulis Portable',   'stok' => 4,  'kategori' => 'Peralatan Kelas'],
            ['nama' => 'Kamera DSLR Canon',      'stok' => 3,  'kategori' => 'Elektronik'],
            ['nama' => 'Tripod Kamera',          'stok' => 5,  'kategori' => 'Aksesoris'],
        ];

        foreach ($barangs as $index => $b) {
            Barang::updateOrCreate(
                ['kode' => 'B-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'           => $b['nama'],
                    'stok_total'     => $b['stok'],
                    'stok_tersedia'  => $b['stok'],
                    'kategori'       => $b['kategori'],
                    'deskripsi'      => 'Inventaris kampus: ' . $b['nama'],
                    'status'         => 'tersedia',
                ]
            );
        }
    }
}
