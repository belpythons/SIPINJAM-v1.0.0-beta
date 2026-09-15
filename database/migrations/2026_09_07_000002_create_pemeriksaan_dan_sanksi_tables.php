<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Serah terima, pelanggaran, dan sanksi.
 *
 * Prinsip yang dikunci di skema: `pelanggaran.pengajuan_item_id` WAJIB ada
 * isinya, dan `sanksi.pelanggaran_id` menunjuk pelanggaran tertentu. Dengan
 * begitu tidak ada jalur data yang memungkinkan sanksi dijatuhkan tanpa bukti
 * yang menunjuk baris aset tertentu — cacat B-05 (pelaku ditebak lewat kueri
 * tanggal) tidak dapat terulang lewat model ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serah_terima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_item_id')->constrained('pengajuan_items')->cascadeOnDelete();
            $table->enum('tipe', ['keluar', 'kembali']);
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('waktu');
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('kondisi')->nullable();

            $table->json('checklist')->nullable();
            $table->json('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->string('ttd_peminjam_path')->nullable();

            $table->timestamps();

            $table->index(['pengajuan_item_id', 'tipe']);
        });

        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Wajib: pelaku selalu diturunkan dari baris aset yang diperiksa,
            // tidak pernah ditebak.
            $table->foreignId('pengajuan_item_id')->constrained('pengajuan_items')->cascadeOnDelete();

            $table->string('jenis');
            $table->unsignedInteger('poin')->default(0);
            $table->text('deskripsi')->nullable();
            $table->json('bukti')->nullable();
            $table->decimal('nilai_ganti_rugi', 15, 2)->nullable();
            $table->string('status_tindak_lanjut')->default('menunggu');
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_penyelesaian')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index('status_tindak_lanjut');
        });

        Schema::create('sanksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pelanggaran_id')->nullable()->constrained('pelanggaran')->nullOnDelete();

            $table->enum('jenis', ['peringatan', 'blokir']);
            $table->dateTime('mulai_at');
            $table->dateTime('sampai_at')->nullable();
            $table->text('alasan')->nullable();

            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dicabut_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('dicabut_at')->nullable();
            $table->text('alasan_pencabutan')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'sampai_at']);
        });

        Schema::create('asset_blackouts', function (Blueprint $table) {
            $table->id();
            $table->morphs('assetable');
            $table->dateTime('mulai_at');
            $table->dateTime('selesai_at');
            $table->enum('alasan', ['pemeliharaan', 'libur', 'acara_internal'])->default('pemeliharaan');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(
                ['assetable_type', 'assetable_id', 'mulai_at', 'selesai_at'],
                'asset_blackouts_slot_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_blackouts');
        Schema::dropIfExists('sanksi');
        Schema::dropIfExists('pelanggaran');
        Schema::dropIfExists('serah_terima');
    }
};
