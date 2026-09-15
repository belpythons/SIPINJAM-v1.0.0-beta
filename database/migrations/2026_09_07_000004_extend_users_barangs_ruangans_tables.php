<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perubahan tabel lama sesuai §6.2.
 *
 * Kolom identitas & telepon dibuat di sini (P1) tetapi baru dipakai antarmuka
 * di P2 — memisahkan perubahan skema dari perubahan UI menjaga kriteria
 * "tidak ada perubahan perilaku yang terlihat pengguna" pada fase ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kontak — disimpan ternormalisasi E.164 (lihat App\Support\PhoneNumber).
            $table->string('phone')->nullable()->after('email');
            $table->dateTime('phone_verified_at')->nullable()->after('phone');

            // Identitas sivitas.
            $table->string('identity_number')->nullable()->after('phone_verified_at');
            $table->string('user_type')->nullable()->after('identity_number');
            $table->string('program_studi')->nullable()->after('user_type');
            $table->string('unit_kerja')->nullable()->after('program_studi');
            $table->string('jabatan')->nullable()->after('unit_kerja');
            $table->string('angkatan')->nullable()->after('jabatan');

            // Cache yang diturunkan dari tabel pelanggaran (diisi mulai P5).
            $table->unsignedInteger('poin_pelanggaran')->default(0)->after('angkatan');

            $table->dateTime('pwa_prompt_dismissed_at')->nullable();
            $table->dateTime('onboarding_completed_at')->nullable();

            // Unique yang mengizinkan banyak NULL — perilaku standar SQL untuk
            // unique index, jadi user tanpa nomor tidak saling bentrok.
            $table->unique('phone');
            $table->index('identity_number');
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('nilai_perolehan', 15, 2)->nullable()->after('stok_tersedia');
            $table->string('satuan')->default('unit')->after('nilai_perolehan');
            $table->string('kondisi')->default('baik')->after('satuan');
            $table->string('lokasi_penyimpanan')->nullable()->after('kondisi');
            $table->boolean('is_consumable')->default(false)->after('lokasi_penyimpanan');
            $table->boolean('butuh_operator')->default(false)->after('is_consumable');
            $table->unsignedInteger('min_lead_time_jam')->nullable()->after('butuh_operator');
            $table->json('role_diizinkan')->nullable()->after('min_lead_time_jam');
        });

        Schema::table('ruangans', function (Blueprint $table) {
            $table->json('fasilitas')->nullable()->after('kapasitas');
            $table->time('jam_buka')->nullable()->after('fasilitas');
            $table->time('jam_tutup')->nullable()->after('jam_buka');
            $table->unsignedInteger('buffer_menit')->nullable()->after('jam_tutup');
            $table->string('pengelola_unit')->nullable()->after('buffer_menit');
            $table->boolean('butuh_persetujuan_khusus')->default(false)->after('pengelola_unit');
            $table->unsignedInteger('min_lead_time_jam')->nullable()->after('butuh_persetujuan_khusus');
            $table->json('role_diizinkan')->nullable()->after('min_lead_time_jam');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropIndex(['identity_number']);
            $table->dropColumn([
                'phone', 'phone_verified_at', 'identity_number', 'user_type',
                'program_studi', 'unit_kerja', 'jabatan', 'angkatan',
                'poin_pelanggaran', 'pwa_prompt_dismissed_at', 'onboarding_completed_at',
            ]);
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_perolehan', 'satuan', 'kondisi', 'lokasi_penyimpanan',
                'is_consumable', 'butuh_operator', 'min_lead_time_jam', 'role_diizinkan',
            ]);
        });

        Schema::table('ruangans', function (Blueprint $table) {
            $table->dropColumn([
                'fasilitas', 'jam_buka', 'jam_tutup', 'buffer_menit', 'pengelola_unit',
                'butuh_persetujuan_khusus', 'min_lead_time_jam', 'role_diizinkan',
            ]);
        });
    }
};
