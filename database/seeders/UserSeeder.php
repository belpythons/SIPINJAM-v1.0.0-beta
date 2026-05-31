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
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // ── 1. Admin Account ────────────────────────────
        $admin = User::create([
            'name' => 'Admin STITEK',
            'email' => 'admin@sipinjam.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // ── 2. Mahasiswa Account ────────────────────────
        $user = User::create([
            'name' => 'Mahasiswa STITEK',
            'email' => 'user@sipinjam.test',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        $user->assignRole('user');

        // ── 3. Demo Account ────────────────────────────
        $demo = User::create([
            'name' => 'belva',
            'nickname' => 'Belva',
            'email' => 'belvapranamasriwibowo@gmail.com',
            'password' => Hash::make('belva123'),
            'role' => 'user',
        ]);
        $demo->assignRole('user');

        // ── 4. Additional Student Accounts (Blocked / Active) ──
        $zulfa = User::create([
            'name' => 'Zulfa',
            'email' => '202312017@stitek.ac.id',
            'password' => Hash::make('202312017'),
            'role' => 'user',
            'is_blocked' => false,
        ]);
        $zulfa->assignRole('user');

        $miya = User::create([
            'name' => 'Miya',
            'email' => '202312023@stitek.ac.id',
            'password' => Hash::make('202312023'),
            'role' => 'user',
            'is_blocked' => true,
            'blocked_reason' => 'Merusak fasilitas Lab Arsikom',
        ]);
        $miya->assignRole('user');

        $taliya = User::create([
            'name' => 'Taliya',
            'email' => '202312030@gmail.com',
            'password' => Hash::make('202312030'),
            'role' => 'user',
            'is_blocked' => true,
            'blocked_reason' => 'Terlambat mengembalikan Proyektor selama 7 hari',
        ]);
        $taliya->assignRole('user');

        $salsa = User::create([
            'name' => 'Salsa',
            'email' => '202312016@stitek.ac.id',
            'password' => Hash::make('202312016'),
            'role' => 'user',
            'is_blocked' => false,
        ]);
        $salsa->assignRole('user');

        $elin = User::create([
            'name' => 'Elin',
            'email' => '202312028@stitek.ac.id',
            'password' => Hash::make('202312028'),
            'role' => 'user',
            'is_blocked' => false,
        ]);
        $elin->assignRole('user');

        $brama = User::create([
            'name' => 'Bella Novia Aulia',
            'email' => '202312066@stitek.ac.id',
            'password' => Hash::make('202312066'),
            'role' => 'user',
            'is_blocked' => false,
        ]);
        $brama->assignRole('user');
    }
}
