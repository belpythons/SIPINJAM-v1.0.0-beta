<?php

namespace App\Console\Commands;

use App\Mail\BookingStatusUpdated;
use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AutoRejectPendingBookings extends Command
{
    protected $signature = 'booking:auto-reject';

    protected $description = 'Tolak otomatis peminjaman berstatus "menunggu" yang sudah lewat 48 jam (SLA)';

    public function handle(): int
    {
        $expiredBookings = Peminjaman::where('status', 'menunggu')
            ->where('created_at', '<=', now()->subHours(48))
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('Tidak ada peminjaman yang perlu di-reject.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expiredBookings as $peminjaman) {
            DB::transaction(function () use ($peminjaman) {
                $locked = Peminjaman::lockForUpdate()->find($peminjaman->id);

                // Guard: pastikan masih berstatus menunggu (bisa saja sudah diproses)
                if (!$locked || $locked->status !== 'menunggu') {
                    return;
                }

                $locked->update(['status' => 'ditolak']);

                // Kembalikan stok barang yang sudah dikurangi saat booking dibuat
                if ($locked->tipe === 'barang' && $locked->barang_id) {
                    Barang::lockForUpdate()
                        ->where('id', $locked->barang_id)
                        ->increment('stok_tersedia');
                }

                Mail::to($locked->user->email)->queue(
                    new BookingStatusUpdated($locked, 'ditolak')
                );
            });

            $count++;
        }

        $this->info("Selesai. {$count} peminjaman di-auto-reject karena melewati SLA 48 jam.");

        return Command::SUCCESS;
    }
}
