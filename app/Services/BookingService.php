<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
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
        return DB::transaction(function () use ($data, $user) {
            if ($data['tipe_peminjaman'] === 'barang') {
                // Lock record barang untuk validasi stok (read-only, no decrement)
                $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

                if ($barang->stok_tersedia < 1) {
                    throw new \RuntimeException('Stok barang "' . $barang->nama . '" sudah habis.');
                }

                // ⛔ TIDAK ada $barang->decrement() di sini.
                // Stok hanya dikurangi saat Admin approve (lihat approveBooking).

                $peminjaman = $this->buildPeminjaman($data, $user, [
                    'tipe'       => 'barang',
                    'barang_id'  => $barang->id,
                    'nama_item'  => $barang->nama,
                ]);
            } else {
                // Lock record ruangan lalu cek apakah jadwal bentrok
                $ruangan = Ruangan::lockForUpdate()->findOrFail($data['ruangan_id']);

                $this->assertNoScheduleConflict($ruangan, $data);

                $peminjaman = $this->buildPeminjaman($data, $user, [
                    'tipe'       => 'ruangan',
                    'ruangan_id' => $ruangan->id,
                    'nama_item'  => $ruangan->nama,
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
     * 2. Validasi stok cukup (>= 1 untuk barang).
     * 3. Update status → APPROVED, set approved_at.
     * 4. Deduct stok_tersedia (hanya untuk tipe barang).
     * 5. Auto-reject semua PENDING bookings lain jika stok menjadi 0.
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

            if ($peminjaman->tipe === 'barang' && $peminjaman->barang_id) {
                // ── CRITICAL: Pessimistic lock pada barang ─────────
                $barang = Barang::where('id', $peminjaman->barang_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $newStock = $barang->stok_tersedia - 1;

                if ($newStock < 0) {
                    throw new \RuntimeException(
                        'Stok barang "' . $barang->nama . '" tidak mencukupi untuk disetujui.'
                    );
                }

                // Deduct stock
                $barang->update(['stok_tersedia' => $newStock]);

                // ── AUTO-REJECT: jika stok habis, reject semua PENDING lainnya ──
                if ($newStock === 0) {
                    Peminjaman::where('barang_id', $barang->id)
                        ->where('id', '!=', $peminjaman->id)
                        ->where('status', Peminjaman::STATUS_PENDING)
                        ->update([
                            'status'     => Peminjaman::STATUS_REJECTED,
                            'keterangan' => 'Dibatalkan sistem: Stok habis',
                        ]);
                }
            }

            // Update status to APPROVED + stamp approval time
            $peminjaman->update([
                'status'      => Peminjaman::STATUS_APPROVED,
                'approved_at' => now(),
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
     */
    private function assertNoScheduleConflict(Ruangan $ruangan, array $data): void
    {
        $conflict = Peminjaman::where('ruangan_id', $ruangan->id)
            ->whereNotIn('status', [Peminjaman::STATUS_REJECTED, Peminjaman::STATUS_DONE])
            ->where(function ($q) use ($data) {
                // Overlap: mulai_A < selesai_B DAN mulai_B < selesai_A
                $q->where('tanggal_mulai', '<=', $data['tanggal_selesai'])
                  ->where('tanggal_selesai', '>=', $data['tanggal_mulai']);
            })
            ->where(function ($q) use ($data) {
                // Cek overlap jam pada hari yang beririsan
                $q->where('jam_mulai', '<', $data['waktu_selesai'])
                  ->where('jam_selesai', '>', $data['waktu_mulai']);
            })
            ->exists();

        if ($conflict) {
            throw new \RuntimeException(
                'Ruangan "' . $ruangan->nama . '" sudah dibooking pada jadwal tersebut.'
            );
        }
    }

    /**
     * Helper: buat record Peminjaman dari data form.
     */
    private function buildPeminjaman(array $data, User $user, array $extra): Peminjaman
    {
        $keterangan = $data['keterangan'];
        if (!empty($data['catatan'])) {
            $keterangan .= "\nCatatan: " . $data['catatan'];
        }

        return Peminjaman::create(array_merge([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'jam_mulai'       => $data['waktu_mulai'],
            'jam_selesai'     => $data['waktu_selesai'],
            'keterangan'      => $keterangan,
            'status'          => Peminjaman::STATUS_PENDING,
        ], $extra));
    }
}
