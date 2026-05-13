<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin statis
        User::updateOrCreate(
            ['email' => 'admin@sipinjam.ac.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // User dummy
        $dummyUsers = [
            ['name' => 'Aisyah Putri',    'email' => 'aisyah@sipinjam.ac.id'],
            ['name' => 'Budi Santoso',     'email' => 'budi@sipinjam.ac.id'],
            ['name' => 'Citra Dewi',       'email' => 'citra@sipinjam.ac.id'],
            ['name' => 'Dimas Prasetyo',   'email' => 'dimas@sipinjam.ac.id'],
            ['name' => 'Eka Rahmawati',    'email' => 'eka@sipinjam.ac.id'],
        ];

        foreach ($dummyUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name'     => $u['name'],
                    'password' => Hash::make('password123'),
                    'role'     => 'user',
                ]
            );
        }
    }
}
