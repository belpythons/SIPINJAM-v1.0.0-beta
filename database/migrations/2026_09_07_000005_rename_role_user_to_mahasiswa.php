<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Migrasikan peran lama `user` menjadi `mahasiswa` (§P1 butir 5).
 *
 * Dilakukan dengan MENGGANTI NAMA baris peran, bukan membuat peran baru lalu
 * memindahkan anggotanya. Karena `model_has_roles` menunjuk role_id, seluruh
 * penetapan peran yang sudah ada ikut terbawa tanpa satu pun baris disentuh —
 * tidak ada jendela waktu saat pengguna kehilangan perannya.
 *
 * Idempoten: bila `mahasiswa` sudah ada, migrasi ini tidak melakukan apa pun.
 */
return new class extends Migration
{
    public function up(): void
    {
        $adaUser = DB::table('roles')->where('name', 'user')->where('guard_name', 'web')->exists();
        $adaMahasiswa = DB::table('roles')->where('name', 'mahasiswa')->where('guard_name', 'web')->exists();

        if ($adaUser && ! $adaMahasiswa) {
            DB::table('roles')
                ->where('name', 'user')
                ->where('guard_name', 'web')
                ->update(['name' => 'mahasiswa', 'updated_at' => now()]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $adaMahasiswa = DB::table('roles')->where('name', 'mahasiswa')->where('guard_name', 'web')->exists();
        $adaUser = DB::table('roles')->where('name', 'user')->where('guard_name', 'web')->exists();

        if ($adaMahasiswa && ! $adaUser) {
            DB::table('roles')
                ->where('name', 'mahasiswa')
                ->where('guard_name', 'web')
                ->update(['name' => 'user', 'updated_at' => now()]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
