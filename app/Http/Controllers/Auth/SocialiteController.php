<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user ke halaman consent Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google.
     *
     * B-07: sebelumnya email APA PUN diterima (termasuk Gmail pribadi) dan
     * langsung dibuatkan akun. Sekarang domain diperiksa lebih dulu, dan
     * pembuatan akun otomatis dapat dimatikan lewat konfigurasi.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = (string) $googleUser->getEmail();

            if (! $this->emailDomainDiizinkan($email)) {
                return redirect()->route('login')->with(
                    'error',
                    'Login hanya diizinkan untuk email kampus ('.$this->daftarDomainTerbaca().'). '
                    .'Silakan masuk memakai akun kampus Anda.'
                );
            }

            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                // Update google_id & avatar jika belum terisi
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {
                if (! config('sipinjam.auto_provision_google_users')) {
                    return redirect()->route('login')->with(
                        'error',
                        'Akun Anda belum terdaftar di SiPinjam. Silakan hubungi admin untuk pendaftaran akun.'
                    );
                }

                // Buat akun baru otomatis dengan role 'user'
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                ]);

                // Peran bawaan dari config — P1 mengganti 'user' menjadi 'mahasiswa'.
                $user->assignRole(config('sipinjam.peran.default_google', 'mahasiswa'));
            }

            Auth::login($user, true);

            // Redirect berdasarkan Spatie role
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));

        } catch (\Throwable $e) {
            // Tanpa log, kegagalan login Google mustahil didiagnosis.
            Log::error('Login Google gagal', [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }
    }

    /**
     * Apakah domain email termasuk daftar yang diizinkan?
     *
     * Daftar kosong berarti semua domain diterima (tidak disarankan untuk
     * produksi, tapi berguna untuk lingkungan uji coba).
     */
    private function emailDomainDiizinkan(string $email): bool
    {
        $domains = (array) config('sipinjam.allowed_email_domains', []);

        if ($domains === []) {
            return true;
        }

        $emailDomain = Str::lower(Str::after($email, '@'));

        if ($emailDomain === '' || ! str_contains($email, '@')) {
            return false;
        }

        foreach ($domains as $domain) {
            $domain = Str::lower(trim((string) $domain));

            if ($domain === '') {
                continue;
            }

            // Cocok persis, atau subdomain darinya (mis. mhs.stitek.ac.id).
            if ($emailDomain === $domain || str_ends_with($emailDomain, '.'.$domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Daftar domain untuk ditampilkan pada pesan galat.
     */
    private function daftarDomainTerbaca(): string
    {
        $domains = array_map(
            fn ($d) => '@'.trim((string) $d),
            (array) config('sipinjam.allowed_email_domains', [])
        );

        return implode(', ', $domains);
    }
}
