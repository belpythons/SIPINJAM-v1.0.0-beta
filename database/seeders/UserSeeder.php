<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        User::query()->delete();

        // Peran & permission dibuat di RolePermissionSeeder (dipanggil lebih dulu
        // oleh DatabaseSeeder) — satu tempat saja.

        // ── 1. Admin Account ────────────────────────────
        $admin = User::create([
            'name' => 'Admin STITEK',
            'email' => 'admin@sipinjam.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // ── 2. Mahasiswa Account ────────────────────────
        $user = User::create([
            'name' => 'Mahasiswa STITEK',
            'email' => 'user@sipinjam.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('mahasiswa');

        // ── 3. Demo / Initial Admin Account ────────────
        $demo = User::create([
            'name' => 'Belva Pranama',
            'nickname' => 'Belva',
            'email' => 'belvapranamasriwibowo@gmail.com',
            'password' => Hash::make('belva123'),
        ]);
        $demo->assignRole('admin');
    }
}
