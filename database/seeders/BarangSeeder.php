<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            'Smart TV',
            'Kipas Turbo',
            'Mic',
            'Charger',
            'Taplak',
            'Kursi',
            'Meja',
            'Terminal Kabel',
        ];

        foreach ($barangs as $index => $nama) {
            $stok = rand(5, 100);

            Barang::updateOrCreate(
                ['kode' => 'B-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'nama'           => $nama,
                    'stok_total'     => $stok,
                    'stok_tersedia'  => $stok,
                    'kategori'       => 'Inventaris',
                    'deskripsi'      => 'Barang inventaris: ' . $nama,
                    'status'         => 'tersedia',
                ]
            );
        }
    }
}
