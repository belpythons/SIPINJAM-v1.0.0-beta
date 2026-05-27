<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate — fresh start
        User::query()->delete();

        // Buat role Spatie jika belum ada
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);

        // ── 1. Admin Account ────────────────────────────
        $admin = User::create([
            'name'     => 'Admin STITEK',
            'email'    => 'admin@stitek.ac.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
        $admin->assignRole('admin');

        // ── 2. Mahasiswa Account ────────────────────────
        $user = User::create([
            'name'     => 'Mahasiswa STITEK',
            'email'    => 'mahasiswa@stitek.ac.id',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);
        $user->assignRole('user');
    }
}
