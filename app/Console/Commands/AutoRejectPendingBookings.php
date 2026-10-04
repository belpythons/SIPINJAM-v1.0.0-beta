<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoRejectPendingBookings extends Command
{
    protected $signature = 'booking:auto-reject';

    protected $description = 'Tolak otomatis peminjaman berstatus "menunggu" yang sudah lewat 48 jam (SLA)';

    public function handle(): int
    {
        $expiredBookings = Peminjaman::where('status', Peminjaman::STATUS_PENDING)
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
                if (!$locked || $locked->status !== Peminjaman::STATUS_PENDING) {
                    return;
                }

                $locked->update([
                    'status'     => Peminjaman::STATUS_REJECTED,
                    'keterangan' => 'Dibatalkan sistem: Melewati SLA 48 jam',
                ]);

                // ⛔ TIDAK ada increment stok — karena Delayed Deduction
                //    (stok tidak pernah dideduct saat status PENDING).
                // ⛔ Email notification DISABLED for MVP.
            });

            $count++;
        }

        $this->info("Selesai. {$count} peminjaman di-auto-reject karena melewati SLA 48 jam.");

        return Command::SUCCESS;
    }
}
