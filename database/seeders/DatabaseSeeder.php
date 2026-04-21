<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@sipinjam.ac.id',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        ]);

        // Bikin Akun User
        \App\Models\User::create([
            'name' => 'User Mahasiswa',
            'email' => 'user@sipinjam.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }
}
