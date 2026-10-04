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
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index(['status', 'tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropIndex(['tanggal_mulai', 'tanggal_selesai']);
            $table->dropIndex(['status', 'tipe']);
        });
    }
};
