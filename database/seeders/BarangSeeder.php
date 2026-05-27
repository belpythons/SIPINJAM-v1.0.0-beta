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
            ['nama' => 'Smart TV',           'stok' => 1,  'kategori' => 'Elektronik',        'deskripsi' => 'Smart TV 55" untuk presentasi dan media pembelajaran.'],
            ['nama' => 'Microphone',         'stok' => 3,  'kategori' => 'Audio',              'deskripsi' => 'Mikrofon wireless untuk seminar dan perkuliahan.'],
            ['nama' => 'Meja Tamu',          'stok' => 2,  'kategori' => 'Furnitur',            'deskripsi' => 'Meja tamu lipat untuk acara dan rapat.'],
            ['nama' => 'Sofa Kecil',         'stok' => 2,  'kategori' => 'Furnitur',            'deskripsi' => 'Sofa kecil untuk ruang tunggu dan area diskusi.'],
            ['nama' => 'Sofa Besar',         'stok' => 1,  'kategori' => 'Furnitur',            'deskripsi' => 'Sofa besar kapasitas 3 orang untuk ruang tamu.'],
            ['nama' => 'Terminal Kabel',     'stok' => 7,  'kategori' => 'Kabel & Aksesoris',  'deskripsi' => 'Terminal listrik multi-colokan untuk kebutuhan praktikum.'],
            ['nama' => 'Proyektor',          'stok' => 4,  'kategori' => 'Elektronik',          'deskripsi' => 'Proyektor LCD untuk presentasi di ruang kelas.'],
            ['nama' => 'Mikrotik Jaringan',  'stok' => 13, 'kategori' => 'Jaringan',            'deskripsi' => 'Perangkat router Mikrotik untuk praktikum jaringan komputer.'],
            ['nama' => 'Motherboard',        'stok' => 4,  'kategori' => 'Komputer',            'deskripsi' => 'Motherboard PC untuk praktikum arsitektur komputer.'],
            ['nama' => 'Sound System',       'stok' => 2,  'kategori' => 'Audio',              'deskripsi' => 'Speaker aktif dan amplifier untuk acara kampus.'],
            ['nama' => 'Kipas Turbo',        'stok' => 3,  'kategori' => 'Pendingin',           'deskripsi' => 'Kipas angin turbo portable untuk ruangan.'],
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
            ]);
        }
    }
}
