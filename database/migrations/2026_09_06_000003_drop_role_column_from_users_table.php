<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom `users.role` dihapus — Spatie menjadi satu-satunya sumber kebenaran peran.
 *
 * Sebelumnya ada dua sistem otorisasi paralel yang konsisten hanya karena setiap
 * penulis kebetulan menulis keduanya. Penulisan gandanya tidak transaksional,
 * sehingga baris user bisa ter-commit dengan kolom terisi tetapi tanpa peran
 * Spatie — dan kolom itu ada di $fillable, jadi sekalian menjadi permukaan
 * eskalasi hak akses lewat mass-assignment.
 *
 * Peran yang sudah ada aman: seluruh penulis kolom juga memanggil assignRole(),
 * jadi data Spatie sudah lengkap sebelum migrasi ini berjalan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
        });
    }
};
