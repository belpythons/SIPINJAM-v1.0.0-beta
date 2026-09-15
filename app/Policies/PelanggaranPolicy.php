<?php

namespace App\Policies;

use App\Models\Pelanggaran;
use App\Models\User;

class PelanggaranPolicy
{
    /** Pelaku berhak melihat pelanggaran yang dicatatkan atas namanya. */
    public function view(User $user, Pelanggaran $pelanggaran): bool
    {
        return $user->id === $pelanggaran->user_id
            || $user->can('pelanggaran.create');
    }

    public function create(User $user): bool
    {
        return $user->can('pelanggaran.create');
    }

    public function update(User $user, Pelanggaran $pelanggaran): bool
    {
        return $user->can('pelanggaran.create');
    }

    /** Mengajukan keberatan — hanya pelaku, dan hanya selama belum lunas. */
    public function ajukanKeberatan(User $user, Pelanggaran $pelanggaran): bool
    {
        return $user->id === $pelanggaran->user_id
            && $pelanggaran->status_tindak_lanjut !== Pelanggaran::TINDAK_LUNAS;
    }
}
