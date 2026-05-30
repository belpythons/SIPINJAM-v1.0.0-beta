<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ApplySanctionPenalties extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sanction:apply';

    /**
     * The console command description.
     */
    protected $description = 'Blokir akun user yang overtime (peminjaman melewati batas waktu) dan unblock user yang masa blokirnya sudah habis.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('[Sanction] Memulai proses sanksi harian...');

        $blockedCount = $this->blockOvertimeUsers();
        $unblockedCount = $this->unblockExpiredUsers();

        $this->info("[Sanction] Selesai. Diblokir: {$blockedCount}, Di-unblock: {$unblockedCount}.");

        return self::SUCCESS;
    }

    /**
     * Blokir user yang peminjamannya overtime (waktu selesai sudah lewat).
     *
     * PENTING: Status peminjaman TIDAK diubah menjadi "selesai" — biarkan tetap
     * "sedang_dipinjam" agar barang/ruangan tetap terlacak belum dikembalikan.
     */
    private function blockOvertimeUsers(): int
    {
        $now = Carbon::now();
        $blockedCount = 0;

        // Cari semua peminjaman yang masih berstatus "sedang_dipinjam"
        // dan waktu selesainya sudah lewat
        $overtimeBookings = Peminjaman::where('status', Peminjaman::STATUS_APPROVED)
            ->where(function ($query) use ($now) {
                // Tanggal selesai sudah lewat hari ini
                $query->where('tanggal_selesai', '<', $now->toDateString())
                    // ATAU tanggal selesai = hari ini tapi jam selesai sudah lewat
                    ->orWhere(function ($q) use ($now) {
                        $q->where('tanggal_selesai', '=', $now->toDateString())
                          ->where('jam_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->with('user')
            ->get();

        foreach ($overtimeBookings as $booking) {
            $user = $booking->user;

            if (!$user) {
                continue;
            }

            // Skip jika user sudah diblokir (hindari reset timer)
            if ($user->isBlocked()) {
                continue;
            }

            $user->blockFor(30, 'Keterlambatan pengembalian barang/ruangan (Overtime otomatis oleh sistem).');
            $blockedCount++;

            $this->line("  → User #{$user->id} ({$user->name}) diblokir 30 hari (overtime booking #{$booking->id}).");
        }

        return $blockedCount;
    }

    /**
     * Unblock user yang masa blokirnya sudah habis.
     */
    private function unblockExpiredUsers(): int
    {
        $unblockedCount = 0;

        $expiredUsers = User::where('is_blocked', true)
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '<=', now())
            ->get();

        foreach ($expiredUsers as $user) {
            $user->unblock();
            $unblockedCount++;

            $this->line("  → User #{$user->id} ({$user->name}) di-unblock (masa blokir berakhir).");
        }

        return $unblockedCount;
    }
}
