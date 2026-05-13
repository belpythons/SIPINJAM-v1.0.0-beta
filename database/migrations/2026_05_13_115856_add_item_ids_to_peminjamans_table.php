<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->after('tipe')->constrained('barangs')->nullOnDelete();
            $table->foreignId('ruangan_id')->nullable()->after('barang_id')->constrained('ruangans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropForeign(['barang_id']);
            $table->dropForeign(['ruangan_id']);
            $table->dropColumn(['barang_id', 'ruangan_id']);
        });
    }
};
