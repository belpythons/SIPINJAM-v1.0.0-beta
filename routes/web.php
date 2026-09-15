<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\LaporanPelanggaranController as AdminLaporanPelanggaranController;
use App\Http\Controllers\Admin\TataTertibController as AdminTataTertibController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanPelanggaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\TataTertibController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// ==========================================
// SOCIALITE (Google Login)
// ==========================================
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

// ==========================================
// ROUTE UNTUK USER BIASA
// ==========================================
Route::middleware(['auth', 'verified', 'blocked'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peminjaman
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:5,1')->name('bookings.store');
    Route::get('/bookings/{id}/pdf', [BookingController::class, 'generatePDF'])->middleware('throttle:20,1')->name('bookings.pdf');

    // Menu Lainnya
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/tata_tertib', [TataTertibController::class, 'index'])->name('tata_tertib.index');

    // Kalender Akademik (User)
    Route::get('/kalender', [CalendarController::class, 'index'])->name('kalender.index');
    Route::get('/kalender/export-pdf', [CalendarController::class, 'exportPdf'])->middleware('throttle:20,1')->name('kalender.export_pdf');

    // Laporan Pribadi (User)
    Route::get('/laporan', [ReportController::class, 'userIndex'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [ReportController::class, 'userExportPdf'])->middleware('throttle:20,1')->name('laporan.export_pdf');

    // Lapor Pelanggaran (User)
    Route::get('/lapor-pelanggaran', [LaporanPelanggaranController::class, 'create'])->name('lapor_pelanggaran.create');
    Route::post('/lapor-pelanggaran', [LaporanPelanggaranController::class, 'store'])->name('lapor_pelanggaran.store');

    // Profile Edit (User)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/edit', fn () => redirect('/profile'));
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
    Route::patch('/admin/kelola-peminjaman/{id}/selesai', [AdminController::class, 'selesaiPeminjaman'])->name('admin.peminjaman.selesai');

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

    // Kelola Landing Page / Banner (CRUD)
    Route::get('/admin/kelola-banner', [BannerController::class, 'index'])->name('admin.kelola_banner');
    Route::post('/admin/kelola-banner', [BannerController::class, 'store'])->name('admin.banner.store');
    Route::put('/admin/kelola-banner/{id}', [BannerController::class, 'update'])->name('admin.banner.update');
    Route::delete('/admin/kelola-banner/{id}', [BannerController::class, 'destroy'])->name('admin.banner.destroy');

    // Kalender Akademik (Admin)
    Route::get('/admin/kelola-kalender', [CalendarController::class, 'adminIndex'])->name('admin.kelola_kalender');
    Route::post('/admin/kelola-kalender', [CalendarController::class, 'store'])->name('admin.kalender.store');
    Route::patch('/admin/kelola-kalender/{id}/activate', [CalendarController::class, 'setActive'])->name('admin.kalender.activate');
    Route::delete('/admin/kelola-kalender/{id}', [CalendarController::class, 'destroy'])->name('admin.kalender.destroy');

    // Laporan Admin
    Route::get('/admin/laporan', [ReportController::class, 'adminIndex'])->name('admin.laporan');
    Route::get('/admin/laporan/export-pdf', [ReportController::class, 'adminExportPdf'])->middleware('throttle:20,1')->name('admin.laporan.export_pdf');
    Route::get('/admin/laporan/export-excel', [ReportController::class, 'adminExportExcel'])->middleware('throttle:20,1')->name('admin.laporan.export_excel');

    // Kelola Pelanggaran & Sanksi (Admin)
    Route::get('/admin/pelanggaran', [AdminLaporanPelanggaranController::class, 'index'])->name('admin.pelanggaran.index');
    Route::patch('/admin/pelanggaran/{laporan}/sanksi', [AdminLaporanPelanggaranController::class, 'putuskanSanksi'])->name('admin.pelanggaran.sanksi');
    Route::patch('/admin/pelanggaran/{laporan}/tolak', [AdminLaporanPelanggaranController::class, 'tolak'])->name('admin.pelanggaran.tolak');

    // Kelola Tata Tertib (Admin)
    Route::get('/admin/kelola-tata-tertib', [AdminTataTertibController::class, 'index'])->name('admin.tata_tertib.index');
    Route::post('/admin/kelola-tata-tertib', [AdminTataTertibController::class, 'store'])->name('admin.tata_tertib.store');

    // Profile Edit (Admin — renders Admin/ProfileEdit with AdminLayout)
    Route::get('/admin/profile', [AdminController::class, 'profileEdit'])->name('admin.profile.edit');
    Route::post('/admin/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');
});

require __DIR__.'/auth.php';
