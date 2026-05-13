<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class BookingPolicy
{
    /**
     * Admin bisa akses semua, User hanya miliknya sendiri.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        return $user->role === 'admin' || $user->id === $peminjaman->user_id;
    }

    /**
     * Hanya pemilik booking (atau Admin) yang bisa membatalkan.
     */
    public function cancel(User $user, Peminjaman $peminjaman): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $peminjaman->user_id && $peminjaman->status === 'menunggu';
    }

    /**
     * Hanya Admin yang bisa menyetujui/menolak.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Download PDF — hanya pemilik atau admin, dan status bukan menunggu.
     */
    public function downloadPdf(User $user, Peminjaman $peminjaman): bool
    {
        if ($peminjaman->status === 'menunggu') {
            return false;
        }

        return $user->role === 'admin' || $user->id === $peminjaman->user_id;
    }
}
