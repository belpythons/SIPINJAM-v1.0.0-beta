<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * B-15 — Pengecekan bentrok jadwal sebelumnya menggabungkan kolom `tanggal_*`
 * (date) dan `jam_*` (time) lewat CONCAT/|| di dalam WHERE. Akibatnya kueri
 * tidak dapat memakai indeks dan harus bercabang per driver database, sehingga
 * perilakunya berbeda antara test dan produksi.
 *
 * Migrasi ini menambahkan dua kolom datetime tunggal beserta indeks komposit,
 * lalu mengisinya dari data lama. Kolom lama sengaja dipertahankan demi
 * kompatibilitas — akan dibereskan pada fase P1.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dateTime('mulai_at')->nullable()->after('jam_selesai');
            $table->dateTime('selesai_at')->nullable()->after('mulai_at');

            // Inti pengecekan bentrok: dipakai oleh
            // BookingService::assertNoScheduleConflict().
            $table->index(['ruangan_id', 'mulai_at', 'selesai_at'], 'peminjamans_ruangan_slot_index');
            $table->index(['barang_id', 'mulai_at', 'selesai_at'], 'peminjamans_barang_slot_index');
        });

        $this->backfill();
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropIndex('peminjamans_ruangan_slot_index');
            $table->dropIndex('peminjamans_barang_slot_index');
            $table->dropColumn(['mulai_at', 'selesai_at']);
        });
    }

    /**
     * Isi kolom baru dari kombinasi tanggal + jam yang ada.
     *
     * Dikerjakan di PHP (bukan SQL mentah) supaya portabel lintas driver —
     * MySQL memakai CONCAT sedangkan SQLite memakai ||, dan itulah tepatnya
     * percabangan yang sedang kita hapus.
     */
    private function backfill(): void
    {
        DB::table('peminjamans')
            ->select(['id', 'tanggal_mulai', 'tanggal_selesai', 'jam_mulai', 'jam_selesai'])
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $mulai = $this->combine($row->tanggal_mulai, $row->jam_mulai, '00:00:00');
                    $selesai = $this->combine($row->tanggal_selesai, $row->jam_selesai, '23:59:59');

                    if ($mulai === null && $selesai === null) {
                        continue;
                    }

                    DB::table('peminjamans')
                        ->where('id', $row->id)
                        ->update([
                            'mulai_at' => $mulai,
                            'selesai_at' => $selesai,
                        ]);
                }
            });
    }

    private function combine(?string $tanggal, ?string $jam, string $jamDefault): ?string
    {
        if (empty($tanggal)) {
            return null;
        }

        try {
            $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
            $jam = ! empty($jam) ? Carbon::parse($jam)->format('H:i:s') : $jamDefault;

            return Carbon::parse($tanggal.' '.$jam)->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return null;
        }
    }
};
