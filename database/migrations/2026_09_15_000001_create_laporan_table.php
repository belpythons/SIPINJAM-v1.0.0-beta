<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ramping untuk laporan pelanggaran sivitas atas sistem Peminjaman
     * lama. Sengaja terpisah dari tabel `pelanggaran` (milik domain Pengajuan
     * yang diparkir, punya FK wajib ke pengajuan_items) — R4/PROMPT-RILIS.md.
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelapor_id')->constrained('users')->cascadeOnDelete();
            // Wajib diisi: pelaku ditetapkan dari data peminjaman terkait,
            // bukan ditebak (hindari bug lama B-05 "tebak pelaku").
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->string('jenis');
            $table->text('deskripsi')->nullable();
            $table->json('bukti')->nullable();
            $table->string('status')->default('menunggu');
            $table->foreignId('ditindak_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('peminjaman_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
