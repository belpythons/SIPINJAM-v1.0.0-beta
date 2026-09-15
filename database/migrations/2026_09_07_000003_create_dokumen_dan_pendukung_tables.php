<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dokumen resmi, penomoran, dan tabel pendukung (identitas, notifikasi,
 * tata tertib berversi).
 *
 * Catatan: tabel `settings` dari §6.1 SENGAJA tidak dibuat — seluruh nilai yang
 * disebutkan di sana (kop surat, penanda tangan, jam operasional) sudah tinggal
 * di config/sipinjam.php sejak P0, dan tidak ada kode P1 yang membacanya.
 * Membuatnya sekarang berarti membangun sumber kebenaran kedua. Lihat T-25.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();

            $table->string('jenis');
            $table->string('nomor')->nullable()->unique();
            $table->string('path')->nullable();
            $table->string('hash')->nullable();
            $table->string('qr_token')->nullable()->unique();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['pengajuan_id', 'jenis']);
        });

        /**
         * Penghitung nomor surat per jenis dokumen per tahun.
         *
         * Menggantikan Peminjaman::generateNomorSurat() yang memakai COUNT(*)
         * (T-02): penghapusan baris membuat nomor terpakai ulang, dan dua
         * approve bersamaan menghasilkan nomor kembar.
         *
         * Unique (jenis, tahun) menjamin satu baris penghitung; unique
         * dokumen.nomor menjadi jaring pengaman terakhir yang berlaku di semua
         * driver, tidak bergantung pada lockForUpdate.
         */
        Schema::create('nomor_counters', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('terakhir')->default(0);
            $table->timestamps();

            $table->unique(['jenis', 'tahun']);
        });

        $this->backfillNomorCounters();

        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone');
            $table->string('code_hash');
            $table->dateTime('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('kanal', ['wa', 'email']);
            $table->string('template');
            $table->json('payload')->nullable();
            $table->string('status')->default('menunggu');
            $table->text('error')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'kanal', 'status']);
        });

        Schema::create('tata_tertib_versions', function (Blueprint $table) {
            $table->id();
            $table->string('versi')->unique();
            $table->longText('konten');
            $table->dateTime('berlaku_sejak');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('berlaku_sejak');
        });

        Schema::create('user_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tata_tertib_version_id')->constrained('tata_tertib_versions')->cascadeOnDelete();
            $table->dateTime('disetujui_at');
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            // Satu persetujuan per user per versi — dasar hukum penegakan sanksi.
            $table->unique(['user_id', 'tata_tertib_version_id']);
        });
    }

    /**
     * Isi penghitung dari nomor surat yang sudah terbit di `peminjamans`.
     *
     * Tanpa ini penghitung mulai dari 0 dan menerbitkan ulang nomor yang sudah
     * dipakai — langsung ditolak unique index, dan lebih buruk lagi: dua surat
     * berbeda dengan nomor sama pernah beredar.
     */
    private function backfillNomorCounters(): void
    {
        if (! Schema::hasTable('peminjamans')) {
            return;
        }

        $tertinggi = [];

        DB::table('peminjamans')
            ->whereNotNull('nomor_surat')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$tertinggi) {
                foreach ($rows as $row) {
                    // Format lama: {urut}/INT/SIPINJAM/{tahun}
                    if (! preg_match('#^(\d+)/.*/(\d{4})$#', (string) $row->nomor_surat, $m)) {
                        continue;
                    }

                    $tahun = (int) $m[2];
                    $urut = (int) $m[1];

                    $tertinggi[$tahun] = max($tertinggi[$tahun] ?? 0, $urut);
                }
            });

        foreach ($tertinggi as $tahun => $urut) {
            DB::table('nomor_counters')->insert([
                'jenis' => 'surat_izin',
                'tahun' => $tahun,
                'terakhir' => $urut,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_agreements');
        Schema::dropIfExists('tata_tertib_versions');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('phone_verifications');
        Schema::dropIfExists('nomor_counters');
        Schema::dropIfExists('dokumen');
    }
};
