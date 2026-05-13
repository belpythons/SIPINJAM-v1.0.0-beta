<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TataTertibController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// ==========================================
// ROUTE UNTUK USER BIASA
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Ini yang error kemarin, sekarang sudah aman!
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Peminjaman
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}/pdf', [BookingController::class, 'generatePDF'])->name('bookings.pdf');

    // Menu Lainnya
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/tata_tertib', [TataTertibController::class, 'index'])->name('tata_tertib.index');
});

// ==========================================
// ROUTE KHUSUS ADMIN
// ==========================================
Route::middleware(['auth', 'admin'])->group(function () {
    // Route untuk Dashboard Admin (hanya satu ini saja, URL-nya /admin/dashboard)
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Route untuk Kelola User
    Route::get('/admin/kelola-user', [AdminController::class, 'kelolaUser'])->name('admin.kelola_user');

    // BACKEND: Tambahkan rute POST ini untuk menyimpan user baru
    Route::post('/admin/kelola-user', [AdminController::class, 'storeUser'])->name('admin.user.store');

    // Route untuk Kelola Peminjaman
    Route::get('/admin/kelola-peminjaman', [AdminController::class, 'kelolaPeminjaman'])->name('admin.kelola_peminjaman');
    Route::get('/admin/kelola-ruangan', [AdminController::class, 'kelolaRuangan'])->name('admin.kelola_ruangan');
    Route::get('/admin/kelola-barang', [AdminController::class, 'kelolaBarang'])->name('admin.kelola_barang');

    // Tambahkan 2 baris ini di dalam middleware admin
    Route::patch('/admin/kelola-peminjaman/{id}/setujui', [AdminController::class, 'setujuiPeminjaman'])->name('admin.peminjaman.setujui');
    Route::patch('/admin/kelola-peminjaman/{id}/tolak', [AdminController::class, 'tolakPeminjaman'])->name('admin.peminjaman.tolak');

});

require __DIR__.'/auth.php';