<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->integer('stok_total');
            $table->integer('stok_tersedia');
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};