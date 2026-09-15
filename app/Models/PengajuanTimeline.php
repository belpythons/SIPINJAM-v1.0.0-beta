<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jejak audit append-only.
 *
 * Sengaja tanpa updated_at: baris timeline tidak pernah diubah maupun dihapus.
 * Satu-satunya penulis adalah PengajuanStateMachine.
 */
class PengajuanTimeline extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_timeline';

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
