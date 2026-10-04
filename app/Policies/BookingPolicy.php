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

        return $user->id === $peminjaman->user_id && $peminjaman->status === Peminjaman::STATUS_PENDING;
    }

    /**
     * Hanya Admin yang bisa menyetujui/menolak.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Download PDF — Fix IDOR: validasi ketat bahwa peminjam hanya bisa
     * mengunduh PDF miliknya sendiri. Admin bebas mengunduh semua.
     */
    public function downloadPdf(User $user, Peminjaman $peminjaman): bool
    {
        // Status menunggu tidak boleh diunduh siapapun
        if ($peminjaman->status === Peminjaman::STATUS_PENDING) {
            return false;
        }

        // Admin bebas mengunduh semua PDF
        if ($user->role === 'admin') {
            return true;
        }

        // User biasa — WAJIB pemilik peminjaman
        return $user->id === $peminjaman->user_id;
    }
}
