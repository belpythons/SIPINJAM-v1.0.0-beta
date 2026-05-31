<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        Banner::query()->delete();

        $banners = [
            ['image_path' => '/storage/landing page 1.png'],
            ['image_path' => '/storage/landing page 2.png'],
            ['image_path' => '/storage/landing page 3.jpeg'],
            ['image_path' => '/storage/landing page 4.jpeg'],
            ['image_path' => '/storage/landing page 5.jpeg'],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
