<?php

namespace App\Services;

use App\Mail\BookingCreatedNotification;
use App\Mail\BookingStatusUpdated;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingService
{
    /**
     * Buat peminjaman baru dengan pessimistic locking pada stok/jadwal.
     */
    public function createBooking(array $data, User $user): Peminjaman
    {
        return DB::transaction(function () use ($data, $user) {
            if ($data['tipe_peminjaman'] === 'barang') {
                // Lock record barang untuk cegah race condition pada stok
                $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

                if ($barang->stok_tersedia < 1) {
                    throw new \RuntimeException('Stok barang "' . $barang->nama . '" sudah habis.');
                }

                $barang->decrement('stok_tersedia');

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

            // Kirim notifikasi ke Admin via queue agar tidak blocking
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Mail::to($admin->email)->queue(new BookingCreatedNotification($peminjaman));
            }

            return $peminjaman;
        });
    }

    /**
     * Setujui peminjaman — ubah status dengan locking.
     */
    public function approveBooking(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            // Lock baris peminjaman agar tidak diproses ganda oleh admin lain
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);
            $peminjaman->update(['status' => Peminjaman::STATUS_APPROVED]);

            Mail::to($peminjaman->user->email)->queue(
                new BookingStatusUpdated($peminjaman, Peminjaman::STATUS_APPROVED)
            );

            return $peminjaman;
        });
    }

    /**
     * Tolak peminjaman — kembalikan stok barang jika perlu.
     */
    public function rejectBooking(Peminjaman $peminjaman): Peminjaman
    {
        return DB::transaction(function () use ($peminjaman) {
            $peminjaman = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);
            $peminjaman->update(['status' => Peminjaman::STATUS_REJECTED]);

            // Kembalikan stok barang yang sudah dikurangi saat booking dibuat
            if ($peminjaman->tipe === 'barang' && $peminjaman->barang_id) {
                $barang = Barang::lockForUpdate()->findOrFail($peminjaman->barang_id);
                $barang->increment('stok_tersedia');
            }

            Mail::to($peminjaman->user->email)->queue(
                new BookingStatusUpdated($peminjaman, Peminjaman::STATUS_REJECTED)
            );

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
