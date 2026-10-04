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

        // ── 4. Additional 45 Student Accounts (including 2 Blocked Accounts) ──
        for ($i = 1; $i <= 45; $i++) {
            $nim = str_pad(202312000 + $i, 9, '0', STR_PAD_LEFT);
            $isBlocked = false;
            $blockedReason = null;

            if ($i === 10) {
                $isBlocked = true;
                $blockedReason = 'Melanggar tata tertib ruangan: Meninggalkan sampah makanan dan mengotori Lab Multimedia setelah peminjaman.';
            } elseif ($i === 20) {
                $isBlocked = true;
                $blockedReason = 'Melanggar tata tertib barang: Terlambat mengembalikan Proyektor Epson selama lebih dari 7 hari.';
            }

            $dummyUser = User::create([
                'name' => 'Dummy Student ' . $i,
                'email' => "{$nim}@stitek.ac.id",
                'password' => Hash::make($nim),
                'role' => 'user',
                'is_blocked' => $isBlocked,
                'blocked_reason' => $blockedReason,
            ]);
            $dummyUser->assignRole('user');
        }
    }
}
