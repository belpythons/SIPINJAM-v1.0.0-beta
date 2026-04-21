<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('peminjamans', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke tabel users (siapa yang meminjam)
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
        // Status peminjaman untuk dihitung di dashboard
        $table->enum('status', ['menunggu', 'sedang_dipinjam', 'selesai', 'ditolak'])->default('menunggu');
        
        // Tanggal peminjaman
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        
        // Catatan tambahan jika diperlukan
        $table->text('keterangan')->nullable();
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
