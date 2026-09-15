<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Penomoran dokumen resmi, per jenis dokumen per tahun.
 *
 * Menggantikan Peminjaman::generateNomorSurat() yang menghitung nomor dengan
 * COUNT(*) + LIKE (T-02). Metode itu punya dua cacat:
 *
 *   1. Penghapusan satu baris membuat nomor berikutnya MENGULANG nomor yang
 *      sudah pernah terbit.
 *   2. Dua approve bersamaan sama-sama membaca hitungan yang sama, lalu
 *      menerbitkan nomor kembar.
 *
 * Di sini nomor diambil dari tabel penghitung `nomor_counters` yang naik
 * monoton — tidak pernah mundur meski dokumen dihapus.
 *
 * CATATAN KEJUJURAN soal konkurensi: lockForUpdate() TIDAK berefek di SQLite
 * (lihat T-21), sehingga suite tidak dapat membuktikan keamanan balapan.
 * Jaminan yang benar-benar berlaku di semua driver adalah unique index pada
 * `nomor_counters (jenis, tahun)` dan pada `dokumen.nomor` — pada balapan
 * sungguhan, salah satu penulis gagal alih-alih menerbitkan nomor kembar.
 */
class NomorSuratService
{
    /**
     * Terbitkan nomor berikutnya, mis. "007/INT/SIPINJAM/2026".
     *
     * @param  string  $jenis  Jenis dokumen (lihat App\Models\Dokumen::JENIS)
     */
    public function terbitkan(string $jenis, ?int $tahun = null): string
    {
        $tahun ??= (int) now()->year;

        $urut = $this->urutBerikutnya($jenis, $tahun);

        return $this->format($urut, $tahun);
    }

    /**
     * Naikkan penghitung dan kembalikan nilai barunya.
     *
     * Dijalankan dalam transaksi dengan lockForUpdate agar dua proses tidak
     * membaca nilai yang sama. Baris penghitung dibuat sekali per (jenis, tahun).
     */
    public function urutBerikutnya(string $jenis, int $tahun): int
    {
        return DB::transaction(function () use ($jenis, $tahun) {
            $baris = DB::table('nomor_counters')
                ->where('jenis', $jenis)
                ->where('tahun', $tahun)
                ->lockForUpdate()
                ->first();

            if ($baris === null) {
                DB::table('nomor_counters')->insert([
                    'jenis' => $jenis,
                    'tahun' => $tahun,
                    'terakhir' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            }

            $berikutnya = (int) $baris->terakhir + 1;

            DB::table('nomor_counters')
                ->where('id', $baris->id)
                ->update(['terakhir' => $berikutnya, 'updated_at' => now()]);

            return $berikutnya;
        });
    }

    /**
     * Susun nomor sesuai config('sipinjam.surat.format_nomor').
     *
     * Penanda yang dikenali: {urut} (3 digit, nol di depan) dan {tahun}.
     */
    public function format(int $urut, int $tahun): string
    {
        $pola = (string) config('sipinjam.surat.format_nomor', '{urut}/INT/SIPINJAM/{tahun}');

        return strtr($pola, [
            '{urut}' => str_pad((string) $urut, 3, '0', STR_PAD_LEFT),
            '{tahun}' => (string) $tahun,
        ]);
    }

    /**
     * Nilai penghitung saat ini tanpa menaikkannya — untuk pratinjau/laporan.
     */
    public function urutTerakhir(string $jenis, ?int $tahun = null): int
    {
        $tahun ??= (int) now()->year;

        return (int) (DB::table('nomor_counters')
            ->where('jenis', $jenis)
            ->where('tahun', $tahun)
            ->value('terakhir') ?? 0);
    }
}
