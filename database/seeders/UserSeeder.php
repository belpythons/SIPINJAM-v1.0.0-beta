<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin — satu-satunya akun awal.
        // Semua akun user baru dibuat melalui panel Admin.
        User::updateOrCreate(
            ['email' => 'admin@sipinjam.ac.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );
    }
}
