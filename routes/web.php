<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TataTertibController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// ==========================================
// SOCIALITE (Google Login)
// ==========================================
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

// ==========================================
// ROUTE UNTUK USER BIASA
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peminjaman
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}/pdf', [BookingController::class, 'generatePDF'])->name('bookings.pdf');

    // Menu Lainnya
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/tata_tertib', [TataTertibController::class, 'index'])->name('tata_tertib.index');

    // Kalender Akademik (User)
    Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender.index');
    Route::get('/kalender/export-pdf', [CalendarController::class, 'exportPdf'])->name('kalender.export_pdf');

    // Laporan Pribadi (User)
    Route::get('/laporan', [ReportController::class, 'userIndex'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [ReportController::class, 'userExportPdf'])->name('laporan.export_pdf');
});

// ==========================================
// ROUTE KHUSUS ADMIN
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Kelola User (CRUD)
    Route::get('/admin/kelola-user', [AdminController::class, 'kelolaUser'])->name('admin.kelola_user');
    Route::post('/admin/kelola-user', [AdminController::class, 'storeUser'])->name('admin.user.store');
    Route::put('/admin/kelola-user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/admin/kelola-user/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.destroy');

    // Kelola Peminjaman
    Route::get('/admin/kelola-peminjaman', [AdminController::class, 'kelolaPeminjaman'])->name('admin.kelola_peminjaman');
    Route::patch('/admin/kelola-peminjaman/{id}/setujui', [AdminController::class, 'setujuiPeminjaman'])->name('admin.peminjaman.setujui');
    Route::patch('/admin/kelola-peminjaman/{id}/tolak', [AdminController::class, 'tolakPeminjaman'])->name('admin.peminjaman.tolak');

    // Kelola Ruangan (CRUD)
    Route::get('/admin/kelola-ruangan', [AdminController::class, 'kelolaRuangan'])->name('admin.kelola_ruangan');
    Route::post('/admin/kelola-ruangan', [AdminController::class, 'storeRuangan'])->name('admin.ruangan.store');
    Route::put('/admin/kelola-ruangan/{id}', [AdminController::class, 'updateRuangan'])->name('admin.ruangan.update');
    Route::delete('/admin/kelola-ruangan/{id}', [AdminController::class, 'destroyRuangan'])->name('admin.ruangan.destroy');

    // Kelola Barang (CRUD)
    Route::get('/admin/kelola-barang', [AdminController::class, 'kelolaBarang'])->name('admin.kelola_barang');
    Route::post('/admin/kelola-barang', [AdminController::class, 'storeBarang'])->name('admin.barang.store');
    Route::put('/admin/kelola-barang/{id}', [AdminController::class, 'updateBarang'])->name('admin.barang.update');
    Route::delete('/admin/kelola-barang/{id}', [AdminController::class, 'destroyBarang'])->name('admin.barang.destroy');

    // Kalender Akademik (Admin)
    Route::get('/admin/kelola-kalender', [CalendarController::class, 'adminIndex'])->name('admin.kelola_kalender');
    Route::post('/admin/kelola-kalender', [CalendarController::class, 'store'])->name('admin.kalender.store');
    Route::patch('/admin/kelola-kalender/{id}/activate', [CalendarController::class, 'setActive'])->name('admin.kalender.activate');
    Route::delete('/admin/kelola-kalender/{id}', [CalendarController::class, 'destroy'])->name('admin.kalender.destroy');

    // Laporan Admin
    Route::get('/admin/laporan', [ReportController::class, 'adminIndex'])->name('admin.laporan');
    Route::get('/admin/laporan/export-pdf', [ReportController::class, 'adminExportPdf'])->name('admin.laporan.export_pdf');
    Route::get('/admin/laporan/export-excel', [ReportController::class, 'adminExportExcel'])->name('admin.laporan.export_excel');
});

require __DIR__.'/auth.php';