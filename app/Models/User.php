<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\ResetPasswordMail;
use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'is_blocked',
        'blocked_until',
        'blocked_reason',
        // Identitas sivitas (P2)
        'nickname',
        'phone',
        'phone_verified_at',
        'identity_number',
        'user_type',
        'program_studi',
        'unit_kerja',
        'jabatan',
        'angkatan',
        'onboarding_completed_at',
        'pwa_prompt_dismissed_at',
    ];

    /** Tipe sivitas yang dikenali. */
    public const TIPE = ['mahasiswa', 'dosen', 'staff'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // UU PDP: nomor telepon TIDAK pernah ikut terserialisasi secara tidak
        // sengaja. Tempat yang benar-benar butuh harus memintanya eksplisit
        // lewat User::phoneUntuk(), yang menyamarkan untuk peran tak berhak.
        'phone',
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
            'phone_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'pwa_prompt_dismissed_at' => 'datetime',
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

    // ── Domain Relations (P1) ───────────────────────────

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class);
    }

    public function sanksi(): HasMany
    {
        return $this->hasMany(Sanksi::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(UserAgreement::class);
    }

    // ── Privasi kontak (UU PDP No. 27/2022) ─────────────

    /**
     * Nomor telepon sebagaimana boleh dilihat oleh $penonton.
     *
     * Penuh hanya untuk admin & staf aset — merekalah yang perlu menghubungi
     * peminjam saat terjadi masalah di lapangan. Peran lain mendapat bentuk
     * tersamar. Pemilik nomor selalu boleh melihat nomornya sendiri.
     */
    public function phoneUntuk(?User $penonton): ?string
    {
        if ($this->phone === null) {
            return null;
        }

        $bolehPenuh = $penonton !== null && (
            $penonton->id === $this->id
            || $penonton->hasAnyRole(config('sipinjam.peran.boleh_lihat_kontak', ['admin', 'staf_aset']))
        );

        return $bolehPenuh ? $this->phone : PhoneNumber::mask($this->phone);
    }

    public function phoneTerverifikasi(): bool
    {
        return $this->phone !== null && $this->phone_verified_at !== null;
    }

    /**
     * Profil dianggap lengkap bila identitas inti dan kontak sudah terisi.
     */
    public function profilLengkap(): bool
    {
        if ($this->identity_number === null || $this->user_type === null) {
            return false;
        }

        $unit = $this->user_type === 'mahasiswa' ? $this->program_studi : $this->unit_kerja;

        return ! empty($unit);
    }

    public function sudahOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    // ── Nomor telepon ───────────────────────────────────

    /**
     * Simpan nomor telepon selalu dalam bentuk E.164 (+62...).
     *
     * Satu bentuk penyimpanan membuat unique index, pencarian, dan pengiriman
     * WhatsApp memakai nilai yang sama — apa pun format yang diketik pengguna.
     */
    protected function phone(): Attribute
    {
        return Attribute::set(fn (?string $value) => PhoneNumber::normalize($value));
    }

    // ── Sanction Methods ────────────────────────────────

    /**
     * Sanksi blokir yang sedang berlaku, bila ada.
     */
    public function sanksiAktif(): ?Sanksi
    {
        return $this->sanksi()->aktif()->latest('mulai_at')->first();
    }

    /**
     * Apakah user sedang diblokir?
     *
     * Sumber kebenarannya adalah tabel `sanksi`. Kolom is_blocked/blocked_until
     * dipertahankan sebagai CACHE dan dipakai sebagai jalur mundur, karena
     * tabel sanksi baru mulai terisi di P5 — tanpa fallback ini, setiap akun
     * yang sedang diblokir hari ini akan langsung bebas begitu P1 dirilis.
     *
     * TODO(P5): hapus fallback kolom begitu SanksiService menjadi penulis
     * satu-satunya dan data lama sudah dimigrasikan ke tabel sanksi.
     */
    public function isBlocked(): bool
    {
        if ($this->sanksiAktif() !== null) {
            return true;
        }

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
