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
        // Buat role Spatie jika belum ada
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);

        // Super Admin — satu-satunya akun awal.
        // Semua akun user baru dibuat melalui panel Admin atau Google Login.
        $admin = User::updateOrCreate(
            ['email' => 'admin@sipinjam.ac.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Assign Spatie role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
