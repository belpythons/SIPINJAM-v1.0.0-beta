<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    public const KANAL_WA = 'wa';

    public const KANAL_EMAIL = 'email';

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_TERKIRIM = 'terkirim';

    public const STATUS_GAGAL = 'gagal';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
