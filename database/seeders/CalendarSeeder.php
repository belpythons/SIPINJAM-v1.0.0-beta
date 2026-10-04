<?php

namespace Database\Seeders;

use App\Models\Calendar;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        Calendar::query()->delete();

        $calendars = [
            [
                'year'       => 2021,
                'is_active'  => false,
                'image_path' => '/storage/kalender akademik/2021.jpg',
            ],
            [
                'year'       => 2022,
                'is_active'  => false,
                'image_path' => '/storage/kalender akademik/2022.jpg',
            ],
            [
                'year'       => 2023,
                'is_active'  => false,
                'image_path' => '/storage/kalender akademik/2023.jpg',
            ],
            [
                'year'       => 2024,
                'is_active'  => false,
                'image_path' => '/storage/kalender akademik/2024.jpg',
            ],
            [
                'year'       => 2025,
                'is_active'  => true,
                'image_path' => '/storage/kalender akademik/2025.jpg',
            ],
        ];

        foreach ($calendars as $cal) {
            Calendar::create($cal);
        }
    }
}
