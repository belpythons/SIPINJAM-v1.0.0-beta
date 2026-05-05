<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('tipe')->default('ruangan')->after('user_id');        // ruangan / barang
            $table->string('nama_item')->nullable()->after('tipe');              // nama ruangan/barang
            $table->date('tanggal')->nullable()->after('nama_item');             // tanggal pakai
            $table->time('jam_mulai')->nullable()->after('tanggal');             // jam mulai
            $table->time('jam_selesai')->nullable()->after('jam_mulai');         // jam selesai
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'nama_item', 'tanggal', 'jam_mulai', 'jam_selesai']);
        });
    }
};