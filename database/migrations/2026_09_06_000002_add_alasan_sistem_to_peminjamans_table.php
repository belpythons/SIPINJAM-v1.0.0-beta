<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B-06 — Auto-reject (SLA lewat & stok habis) sebelumnya menimpa kolom
 * `keterangan`, yaitu keperluan yang ditulis sendiri oleh pemohon. Data
 * pengguna hilang tanpa jejak.
 *
 * Kolom `alasan_sistem` memberi tempat terpisah bagi alasan yang ditulis
 * sistem, sehingga `keterangan` tidak pernah lagi ditimpa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->text('alasan_sistem')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn('alasan_sistem');
        });
    }
};
