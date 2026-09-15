<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile/Edit', [
            // `phone` disembunyikan pada model demi UU PDP, jadi dikirim
            // eksplisit — pemilik selalu boleh melihat nomornya sendiri.
            'user' => array_merge($user->toArray(), [
                'phone' => $user->phone,
                'phone_verified' => $user->phoneTerverifikasi(),
            ]),
            'tipeSivitas' => User::TIPE,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->nickname = $validated['nickname'] ?? null;

        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }

        if (! empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        // ── Identitas sivitas (P2) ──
        foreach (['identity_number', 'user_type', 'program_studi', 'unit_kerja', 'jabatan', 'angkatan'] as $kolom) {
            if (array_key_exists($kolom, $validated)) {
                $user->{$kolom} = $validated[$kolom] ?: null;
            }
        }

        // Mengganti nomor membatalkan verifikasi sebelumnya — nomor baru harus
        // dibuktikan lagi lewat OTP, kalau tidak verifikasi kehilangan artinya.
        if (array_key_exists('phone', $validated)) {
            $nomorBaru = $validated['phone'] ?: null;

            if ($nomorBaru !== $user->phone) {
                $user->phone = $nomorBaru;
                $user->phone_verified_at = null;
            }
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists in storage
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/'.$path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login');
    }
}
