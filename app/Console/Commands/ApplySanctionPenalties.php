<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * B-04 — perintah ini TIDAK LAGI MENJATUHKAN SANKSI.
 *
 * Versi lama memblokir akun 30 hari hanya karena statusnya masih
 * `sedang_dipinjam` melewati toleransi 12 jam. Tiga hal yang salah:
 *
 *   1. Barang mungkin SUDAH dikembalikan — admin hanya belum sempat menekan
 *      "Selesai". Yang dihukum adalah kelambatan admin, bukan kelalaian
 *      peminjam.
 *   2. Tidak ada notifikasi apa pun. Pengguna baru sadar saat mencoba memakai
 *      aplikasi dan tertolak tanpa penjelasan.
 *   3. Tidak ada bukti yang tertaut, dan tidak ada jalur keberatan.
 *
 * Sanksi hanya sah bila bertumpu pada catatan pemeriksaan yang menyebut pelaku
 * secara pasti. Catatan itu (`SerahTerima`, `Pelanggaran`, `SanksiService`)
 * dibangun di P5. Sampai saat itu perintah ini hanya MENANDAI dan MELAPORKAN,
 * sehingga petugas dapat menindaklanjuti secara manual.
 *
 * Pembebasan blokir yang sudah lewat masanya tetap dijalankan — itu hanya
 * menguntungkan pengguna dan tidak butuh bukti apa pun.
 */
class ApplySanctionPenalties extends Command
{
    protected $signature = 'sanction:apply';

    protected $description = 'Tandai peminjaman yang melewati jatuh tempo dan bebaskan blokir yang sudah berakhir';

    /** Toleransi sebelum sebuah peminjaman dianggap terlambat. */
    private const GRACE_JAM = 12;

    public function handle(): int
    {
        $this->info('[Sanksi] Memulai pemeriksaan harian...');

        $terlambat = $this->tandaiYangTerlambat();
        $dibebaskan = $this->bebaskanBlokirKedaluwarsa();

        $this->newLine();
        $this->info("[Sanksi] Selesai. Terlambat: {$terlambat}, blokir dibebaskan: {$dibebaskan}.");

        if ($terlambat > 0) {
            $this->warn(
                "{$terlambat} peminjaman melewati jatuh tempo dan menunggu pemeriksaan petugas. "
                .'Sanksi TIDAK dijatuhkan otomatis — lihat B-04/P5.'
            );
        }

        return self::SUCCESS;
    }

    /**
     * Laporkan peminjaman yang melewati jatuh tempo + toleransi.
     *
     * Memakai chunkById: memuat seluruh booking aktif ke memori adalah cacat
     * asli yang ikut diperbaiki di sini.
     */
    private function tandaiYangTerlambat(): int
    {
        $now = Carbon::now();
        $jumlah = 0;

        Peminjaman::query()
            ->where('status', Peminjaman::STATUS_APPROVED)
            ->with('user:id,name,email')
            ->orderBy('id')
            ->chunkById(200, function ($bookings) use ($now, &$jumlah) {
                foreach ($bookings as $booking) {
                    $jatuhTempo = $this->jatuhTempo($booking);

                    if ($jatuhTempo === null) {
                        continue;
                    }

                    if ($now->lessThan($jatuhTempo->copy()->addHours(self::GRACE_JAM))) {
                        continue;
                    }

                    $jumlah++;

                    $this->line(sprintf(
                        '  → Peminjaman #%d (%s) lewat jatuh tempo %s — menunggu pemeriksaan.',
                        $booking->id,
                        $booking->user?->name ?? 'tanpa pemilik',
                        $jatuhTempo->translatedFormat('d M Y H:i'),
                    ));
                }
            });

        return $jumlah;
    }

    /**
     * Waktu jatuh tempo peminjaman.
     *
     * Mengutamakan `selesai_at` (B-15). Versi lama hanya merakit
     * `tanggal_selesai + jam_selesai` dan menelan kegagalannya lewat
     * `catch { continue; }` tanpa log — sehingga baris yang jamnya kosong lolos
     * dari pemeriksaan secara senyap. Sekarang kegagalannya dicatat.
     */
    private function jatuhTempo(Peminjaman $booking): ?Carbon
    {
        if ($booking->selesai_at !== null) {
            return $booking->selesai_at;
        }

        $gabungan = Peminjaman::combineDateTime(
            $booking->tanggal_selesai,
            $booking->jam_selesai,
            '23:59:59',
        );

        if ($gabungan === null) {
            Log::warning('Peminjaman tanpa waktu selesai yang dapat dibaca; dilewati saat pemeriksaan sanksi.', [
                'peminjaman_id' => $booking->id,
                'tanggal_selesai' => $booking->tanggal_selesai,
                'jam_selesai' => $booking->jam_selesai,
            ]);
        }

        return $gabungan;
    }

    /**
     * Bebaskan akun yang masa blokirnya sudah lewat.
     */
    private function bebaskanBlokirKedaluwarsa(): int
    {
        $jumlah = 0;

        User::query()
            ->where('is_blocked', true)
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '<=', now())
            ->orderBy('id')
            ->chunkById(200, function ($users) use (&$jumlah) {
                foreach ($users as $user) {
                    $user->unblock();
                    $jumlah++;

                    $this->line("  → {$user->name} dibebaskan (masa blokir berakhir).");
                }
            });

        return $jumlah;
    }
}
