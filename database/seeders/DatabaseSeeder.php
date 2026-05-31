<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean public storage directory
        $publicStoragePath = storage_path('app/public');
        if (\Illuminate\Support\Facades\File::exists($publicStoragePath)) {
            \Illuminate\Support\Facades\File::cleanDirectory($publicStoragePath);
        } else {
            \Illuminate\Support\Facades\File::makeDirectory($publicStoragePath, 0755, true);
        }

        // 2. Copy all files and subfolders recursively from root aset/
        $asetPath = base_path('aset');
        if (\Illuminate\Support\Facades\File::exists($asetPath)) {
            \Illuminate\Support\Facades\File::copyDirectory($asetPath, $publicStoragePath);
        }

        // 3. Call all seeders in order
        $this->call([
            UserSeeder::class,
            RuanganSeeder::class,
            BarangSeeder::class,
            CalendarSeeder::class,
            BannerSeeder::class,
            PeminjamanSeeder::class,
        ]);
    }
}
