<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Registrasi dinonaktifkan — user baru hanya melalui Google Login atau panel Admin.

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Verifikasi email & konfirmasi kata sandi (perancah Breeze) DIHAPUS —
    // lihat T-22/T-23 di docs/TEMUAN-TAMBAHAN.md. Controllernya memanggil view
    // Blade yang sudah tidak ada sejak migrasi ke Inertia, sementara User tidak
    // meng-implement MustVerifyEmail sehingga middleware `verified` tidak aktif.
    // Alur verifikasi akan dibangun ulang sebagai halaman Inertia pada P2,
    // berbarengan dengan verifikasi WhatsApp.

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
