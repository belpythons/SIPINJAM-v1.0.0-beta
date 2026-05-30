<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\ResetPasswordMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'is_blocked',
        'blocked_until',
        'blocked_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_blocked' => 'boolean',
            'blocked_until' => 'datetime',
        ];
    }

    // ── Custom Password Reset Notification ─────────────

    /**
     * Send the password reset notification using our custom Mailable.
     */
    public function sendPasswordResetNotification($token): void
    {
        Mail::to($this->email)->send(new ResetPasswordMail($this, $token));
    }

    // ── Sanction Methods ────────────────────────────────

    /**
     * Check if user is currently blocked.
     */
    public function isBlocked(): bool
    {
        return $this->is_blocked && $this->blocked_until && now()->lt($this->blocked_until);
    }

    /**
     * Block user for a given number of days.
     */
    public function blockFor(int $days = 30, ?string $reason = null): void
    {
        $this->update([
            'is_blocked' => true,
            'blocked_until' => now()->addDays($days),
            'blocked_reason' => $reason,
        ]);
    }

    /**
     * Unblock user.
     */
    public function unblock(): void
    {
        $this->update([
            'is_blocked' => false,
            'blocked_until' => null,
            'blocked_reason' => null,
        ]);
    }
}
