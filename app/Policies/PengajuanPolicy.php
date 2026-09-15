<?php

namespace App\Policies;

use App\Models\Pengajuan;
use App\Models\User;

/**
 * Dinamai PengajuanPolicy agar auto-discovery Laravel 11 menemukannya untuk
 * model App\Models\Pengajuan — pelajaran dari BookingPolicy lama, yang tidak
 * pernah terpanggil karena namanya tidak cocok dengan modelnya.
 */
class PengajuanPolicy
{
    /** Pemohon melihat miliknya; petugas & pimpinan melihat semua. */
    public function view(User $user, Pengajuan $pengajuan): bool
    {
        return $user->id === $pengajuan->user_id
            || $user->canAny(['pengajuan.verify', 'pengajuan.approve']);
    }

    public function create(User $user): bool
    {
        return $user->can('pengajuan.create');
    }

    /** Selama masih draf, pemohon boleh menyunting. */
    public function update(User $user, Pengajuan $pengajuan): bool
    {
        return $user->id === $pengajuan->user_id
            && $pengajuan->status === Pengajuan::STATUS_DRAFT;
    }

    /** Membatalkan hanya boleh sebelum pengajuan tuntas. */
    public function cancel(User $user, Pengajuan $pengajuan): bool
    {
        return $user->id === $pengajuan->user_id && ! $pengajuan->sudahFinal();
    }

    /** Verifikasi ketersediaan — kewenangan staf aset, terpisah dari persetujuan. */
    public function verify(User $user, Pengajuan $pengajuan): bool
    {
        return $user->can('pengajuan.verify')
            && $pengajuan->status === Pengajuan::STATUS_DIAJUKAN;
    }

    /** Persetujuan — kewenangan pimpinan. */
    public function approve(User $user, Pengajuan $pengajuan): bool
    {
        return $user->can('pengajuan.approve')
            && $pengajuan->status === Pengajuan::STATUS_DIVERIFIKASI;
    }

    public function reject(User $user, Pengajuan $pengajuan): bool
    {
        return $user->can('pengajuan.reject') && ! $pengajuan->sudahFinal();
    }

    public function downloadDokumen(User $user, Pengajuan $pengajuan): bool
    {
        return $this->view($user, $pengajuan);
    }
}
