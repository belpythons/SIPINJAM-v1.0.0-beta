<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Dokumen;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    /**
     * Buat peminjaman baru dengan pessimistic locking pada jadwal ruangan.
     *
     * PENTING (Delayed Deduction):
     * Stok barang TIDAK dikurangi saat booking dibuat (status PENDING).
     * Stok hanya dikurangi saat Admin meng-approve booking.
     */
    public function createBooking(array $data, User $user): Peminjaman
    {
        if ($user->is_blocked) {
            throw new \RuntimeException('Akun Anda diblokir: '.$user->blocked_reason);
        }

        return DB::transaction(function () use ($data, $user) {
            $jumlah = $data['jumlah'] ?? 1;

            if ($data['tipe_peminjaman'] === 'barang') {
                // Lock record barang untuk validasi stok (read-only, no decrement)
                $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

                if ($barang->stok_tersedia < $jumlah) {
                    throw new \RuntimeException('Stok barang "'.$barang->nama.'" tidak mencukupi.');
                }

                // ⛔ TIDAK ada $barang->decrement() di sini.
                // Stok hanya dikurangi saat Admin approve (lihat approveBooking).

                $peminjaman = $this->buildPeminjaman($data, $user, [
                    'tipe' => 'barang',
                    'barang_id' => $barang->id,
                    'nama_item' => $barang->nama,
                    'jumlah' => $jumlah,
                ]);
            } else {
                // Lock record ruangan lalu cek apakah jadwal bentrok
                $ruangan = Ruangan::lockForUpdate()->findOrFail($data['ruangan_id']);

                [$mulai, $selesai] = $this->slotFromRequest($data);

                // Saat mengajukan, pengajuan yang masih MENUNGGU pun dianggap
                // memesan slot — supaya dua orang tidak mengantre di slot sama.
                $this->assertNoScheduleConflict(
                    $ruangan,
                    $mulai,
                    $selesai,
                    [Peminjaman::STATUS_PENDING, Peminjaman::STATUS_APPROVED]
                );

                $peminjaman = $this->buildPeminjaman($data, $user, [
                    'tipe' => 'ruangan',
                    'ruangan_id' => $ruangan->id,
                    'nama_item' => $ruangan->nama,
                    'jumlah' => $jumlah,
                ]);
            }

            // ⛔ Email notification DISABLED for MVP — relying on UI only.

            return $peminjaman;
        });
    }

    /**
     * Setujui peminjaman — deduct stock dan auto-reject jika habis.
     *
     * Flow:
     * 1. Lock baris peminjaman + barang (pessimistic locking).
     * 2. Ruangan: pastikan slot belum diambil peminjaman lain yang SUDAH disetujui.
     * 3. Barang: validasi stok cukup, lalu deduct stok_tersedia.
     * 4. Auto-reject pengajuan PENDING lain yang jadwalnya BERIRISAN bila stok habis.
     * 5. Update status → APPROVED, set approved_at, terbitkan nomor surat.
     */
    public function approveBooking(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            // Lock baris peminjaman agar tidak diproses ganda oleh admin lain
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            // Guard: hanya bisa approve dari status PENDING
            if ($peminjaman->status !== Peminjaman::STATUS_PENDING) {
                throw new \RuntimeException('Peminjaman ini sudah diproses sebelumnya.');
            }

            [$mulai, $selesai] = $this->slotOf($peminjaman);

            if ($peminjaman->tipe === 'ruangan' && $peminjaman->ruangan_id) {
                // ── B-03: bentrok ruangan WAJIB dicek ulang saat menyetujui ──
                // Sebelumnya hanya dicek saat membuat, sehingga dua pengajuan
                // "menunggu" pada slot yang sama bisa dua-duanya disetujui.
                //
                // Di sini HANYA status APPROVED yang dianggap bentrok. Bila
                // status PENDING ikut diperiksa, menyetujui pengajuan pertama
                // justru gagal gara-gara pengajuan kedua yang masih antre —
                // kebalikan dari yang kita inginkan.
                $ruangan = Ruangan::where('id', $peminjaman->ruangan_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->assertNoScheduleConflict(
                    $ruangan,
                    $mulai,
                    $selesai,
                    [Peminjaman::STATUS_APPROVED],
                    $peminjaman->id
                );
            }

            if ($peminjaman->tipe === 'barang' && $peminjaman->barang_id) {
                // ── CRITICAL: Pessimistic lock pada barang ─────────
                $barang = Barang::where('id', $peminjaman->barang_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $jumlah = $peminjaman->jumlah ?? 1;
                $newStock = $barang->stok_tersedia - $jumlah;

                if ($newStock < 0) {
                    throw new \RuntimeException(
                        'Stok barang "'.$barang->nama.'" tidak mencukupi untuk disetujui.'
                    );
                }

                // Deduct stock
                $barang->update(['stok_tersedia' => $newStock]);

                // ── B-02: auto-reject HANYA yang jadwalnya beririsan ──
                // Stok habis hari ini tidak berarti habis bulan depan;
                // pengajuan yang jadwalnya tidak beririsan dibiarkan menunggu.
                if ($newStock === 0 && $mulai && $selesai) {
                    Peminjaman::where('barang_id', $barang->id)
                        ->where('id', '!=', $peminjaman->id)
                        ->where('status', Peminjaman::STATUS_PENDING)
                        ->where('mulai_at', '<', $selesai)
                        ->where('selesai_at', '>', $mulai)
                        ->update([
                            'status' => Peminjaman::STATUS_REJECTED,
                            // B-06: tulis ke alasan_sistem, JANGAN timpa
                            // `keterangan` milik pemohon.
                            'alasan_sistem' => 'Dibatalkan sistem: stok habis pada rentang waktu yang diminta.',
                        ]);
                }
            }

            // Update status to APPROVED + stamp approval time + generate nomor_surat
            $peminjaman->update([
                'status' => Peminjaman::STATUS_APPROVED,
                'approved_at' => now(),
                'nomor_surat' => $peminjaman->nomor_surat
                    ?: app(NomorSuratService::class)->terbitkan(Dokumen::JENIS_SURAT_IZIN),
            ]);

            // ⛔ Email notification DISABLED for MVP — relying on UI only.

            return $peminjaman;
        });
    }

    /**
     * Tolak peminjaman.
     *
     * Karena Delayed Deduction: stok TIDAK perlu dikembalikan
     * (tidak pernah dikurangi saat status PENDING).
     */
    public function rejectBooking(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            // Guard: hanya bisa reject dari status PENDING
            if ($peminjaman->status !== Peminjaman::STATUS_PENDING) {
                throw new \RuntimeException('Peminjaman ini sudah diproses sebelumnya.');
            }

            $peminjaman->update(['status' => Peminjaman::STATUS_REJECTED]);

            // ⛔ TIDAK ada increment stok — karena stok tidak pernah dideduct saat PENDING.
            // ⛔ Email notification DISABLED for MVP — relying on UI only.

            return $peminjaman;
        });
    }

    /**
     * Cek apakah ruangan sudah dibooking pada rentang waktu yang sama.
     *
     * B-15: memakai kolom datetime tunggal `mulai_at`/`selesai_at`, sehingga
     * kueri dapat memakai indeks komposit dan berlaku sama di semua driver
     * database (tidak ada lagi percabangan CONCAT vs ||).
     *
     * @param  array  $statuses  Status yang dianggap memesan slot
     * @param  int|null  $exceptId  Baris yang sedang diproses, dikecualikan
     */
    private function assertNoScheduleConflict(
        Ruangan $ruangan,
        ?Carbon $mulai,
        ?Carbon $selesai,
        array $statuses,
        ?int $exceptId = null
    ): void {
        if ($mulai === null || $selesai === null) {
            return;
        }

        $conflict = Peminjaman::query()
            ->where('ruangan_id', $ruangan->id)
            ->whereIn('status', $statuses)
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            // Dua rentang beririsan bila yang satu mulai sebelum yang lain
            // berakhir, dan berakhir setelah yang lain mulai.
            ->where('mulai_at', '<', $selesai)
            ->where('selesai_at', '>', $mulai)
            ->first();

        if ($conflict) {
            throw new \RuntimeException(sprintf(
                'Ruangan "%s" sudah dipakai pada %s s/d %s. Silakan pilih jadwal lain.',
                $ruangan->nama,
                optional($conflict->mulai_at)->translatedFormat('d M Y H:i'),
                optional($conflict->selesai_at)->translatedFormat('d M Y H:i')
            ));
        }
    }

    /**
     * Selesaikan peminjaman (validasi admin bahwa barang/ruangan telah dikembalikan).
     *
     * Flow:
     * 1. Lock baris peminjaman (pessimistic locking).
     * 2. Validasi status harus APPROVED (sedang_dipinjam).
     * 3. Jika tipe barang → kembalikan stok.
     * 4. Update status → DONE, set completed_at.
     */
    public function completeBooking(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

            if ($peminjaman->status !== Peminjaman::STATUS_APPROVED) {
                throw new \RuntimeException('Hanya peminjaman berstatus "Sedang Dipinjam" yang bisa diselesaikan.');
            }

            // Kembalikan stok barang jika tipe = barang
            if ($peminjaman->tipe === 'barang' && $peminjaman->barang_id) {
                $barang = Barang::where('id', $peminjaman->barang_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $jumlah = $peminjaman->jumlah ?? 1;

                $barang->update([
                    'stok_tersedia' => $barang->stok_tersedia + $jumlah,
                ]);
            }

            $peminjaman->update([
                'status' => Peminjaman::STATUS_DONE,
                'completed_at' => now(),
            ]);

            return $peminjaman;
        });
    }

    /**
     * Rentang waktu dari data form.
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function slotFromRequest(array $data): array
    {
        return [
            Peminjaman::combineDateTime($data['tanggal_mulai'] ?? null, $data['waktu_mulai'] ?? null, '00:00:00'),
            Peminjaman::combineDateTime($data['tanggal_selesai'] ?? null, $data['waktu_selesai'] ?? null, '23:59:59'),
        ];
    }

    /**
     * Rentang waktu dari sebuah peminjaman yang sudah tersimpan.
     *
     * Mengutamakan kolom baru; jatuh ke kombinasi tanggal+jam hanya bila baris
     * lama belum ter-backfill.
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function slotOf(Peminjaman $peminjaman): array
    {
        return [
            $peminjaman->mulai_at
                ?: Peminjaman::combineDateTime($peminjaman->tanggal_mulai, $peminjaman->jam_mulai, '00:00:00'),
            $peminjaman->selesai_at
                ?: Peminjaman::combineDateTime($peminjaman->tanggal_selesai, $peminjaman->jam_selesai, '23:59:59'),
        ];
    }

    /**
     * Helper: buat record Peminjaman dari data form.
     */
    private function buildPeminjaman(array $data, User $user, array $extra): Peminjaman
    {
        $keterangan = $data['keterangan'];
        if (! empty($data['catatan'])) {
            $keterangan .= "\nCatatan: ".$data['catatan'];
        }

        [$mulai, $selesai] = $this->slotFromRequest($data);

        return Peminjaman::create(array_merge([
            'user_id' => $user->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'jam_mulai' => $data['waktu_mulai'],
            'jam_selesai' => $data['waktu_selesai'],
            // B-15: kolom inti pengecekan bentrok — wajib ikut terisi.
            'mulai_at' => $mulai,
            'selesai_at' => $selesai,
            'keterangan' => $keterangan,
            'status' => Peminjaman::STATUS_PENDING,
        ], $extra));
    }
}
