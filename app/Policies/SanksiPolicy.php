<?php

namespace App\Policies;

use App\Models\Sanksi;
use App\Models\User;

class SanksiPolicy
{
    /** Setiap orang berhak tahu sanksi yang dijatuhkan kepadanya. */
    public function view(User $user, Sanksi $sanksi): bool
    {
        return $user->id === $sanksi->user_id || $user->can('sanksi.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('sanksi.manage');
    }

    /** Pencabutan wajib disertai alasan — ditegakkan di lapisan request. */
    public function cabut(User $user, Sanksi $sanksi): bool
    {
        return $user->can('sanksi.manage') && $sanksi->dicabut_at === null;
    }
}
