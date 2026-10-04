<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean public storage directory
        $publicStoragePath = storage_path('app/public');
        if (File::exists($publicStoragePath)) {
            File::cleanDirectory($publicStoragePath);
        } else {
            File::makeDirectory($publicStoragePath, 0755, true);
        }

        // 2. Copy all files and subfolders recursively from root aset/
        $asetPath = base_path('aset');
        if (File::exists($asetPath)) {
            File::copyDirectory($asetPath, $publicStoragePath);
        }

        // 3. Call all production/baseline seeders in order
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            RuanganSeeder::class,
            BarangSeeder::class,
            CalendarSeeder::class,
            BannerSeeder::class,
            TataTertibSeeder::class,
        ]);
    }
}
