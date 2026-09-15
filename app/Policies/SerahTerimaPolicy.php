<?php

namespace App\Policies;

use App\Models\SerahTerima;
use App\Models\User;

class SerahTerimaPolicy
{
    /** Peminjam boleh melihat berita acara miliknya sendiri. */
    public function view(User $user, SerahTerima $serahTerima): bool
    {
        return $user->can('serahterima.create')
            || $user->id === $serahTerima->item?->pengajuan?->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('serahterima.create');
    }

    /**
     * Berita acara tidak boleh disunting setelah ditandatangani — ia adalah
     * bukti. Koreksi dilakukan dengan menerbitkan berita acara baru.
     */
    public function update(User $user, SerahTerima $serahTerima): bool
    {
        return false;
    }
}
