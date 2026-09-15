<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Inti model data P1: satu kegiatan = satu pengajuan berisi banyak baris aset.
 *
 * Seluruh kolom waktu bertipe datetime TUNGGAL — bukan pasangan date + time
 * seperti `peminjamans` lama. Itulah yang membuat pengecekan bentrok
 * (mulai_at < :end AND selesai_at > :start) dapat memakai indeks dan berlaku
 * sama di semua driver database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('jenis', ['tunggal', 'event'])->default('tunggal');

            // Identitas kegiatan — wajib untuk jenis=event, kosong untuk tunggal.
            $table->string('nama_kegiatan')->nullable();
            $table->string('jenis_kegiatan')->nullable();
            $table->string('unit_penyelenggara')->nullable();
            $table->unsignedInteger('jumlah_peserta')->nullable();
            $table->string('pj_nama')->nullable();
            $table->string('pj_phone')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lampiran_proposal')->nullable();

            // Rentang keseluruhan kegiatan (baris aset boleh punya rentang sendiri).
            $table->dateTime('mulai_at')->nullable();
            $table->dateTime('selesai_at')->nullable();

            $table->string('status')->default('draft');
            $table->string('nomor_surat')->nullable()->unique();
            $table->dateTime('submitted_at')->nullable();

            // Aktor keputusan — yang sama sekali tidak ada di skema lama.
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable();
            $table->text('alasan_penolakan')->nullable();

            // Jalur hybrid: scan surat bertanda tangan basah.
            $table->string('scan_surat_path')->nullable();
            $table->dateTime('ttd_basah_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'submitted_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();

            // Polimorfik: Ruangan atau Barang.
            $table->morphs('assetable');

            $table->unsignedInteger('jumlah')->default(1);
            $table->dateTime('mulai_at');
            $table->dateTime('selesai_at');

            $table->string('status_item')->default('diajukan');
            $table->text('alasan_tolak')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            // Inti pengecekan bentrok & ketersediaan (§6.3).
            $table->index(
                ['assetable_type', 'assetable_id', 'mulai_at', 'selesai_at'],
                'pengajuan_items_slot_index'
            );
            $table->index(['status_item', 'mulai_at']);
        });

        Schema::create('pengajuan_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_role')->nullable();

            $table->string('aksi');
            $table->string('dari_status')->nullable();
            $table->string('ke_status')->nullable();
            $table->text('catatan')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip', 45)->nullable();

            // Append-only: SENGAJA tanpa updated_at. Baris timeline tidak pernah
            // diubah atau dihapus — itulah yang membuatnya layak jadi jejak audit.
            $table->timestamp('created_at')->nullable();

            $table->index(['pengajuan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_timeline');
        Schema::dropIfExists('pengajuan_items');
        Schema::dropIfExists('pengajuan');
    }
};
