<?php

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\BookingService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Buat role Spatie yang diperlukan untuk testing
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);
});

it('prevents race conditions and auto-rejects pending bookings when stock hits 0 during admin approval', function () {
    // 1. Inisialisasi Data Barang dengan stok = 1
    $barang = Barang::create([
        'nama' => 'Proyektor Epson',
        'kode' => 'B-999',
        'stok_total' => 1,
        'stok_tersedia' => 1,
        'kategori' => 'Elektronik',
        'deskripsi' => 'Proyektor HD',
        'status' => 'tersedia',
    ]);

    // 2. Buat Admin dan 2 User Mahasiswa
    $admin = User::create([
        'name' => 'Admin STITEK',
        'email' => 'admin@stitek.ac.id',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
    ]);
    $admin->assignRole('admin');

    $user1 = User::create([
        'name' => 'Mahasiswa 1',
        'email' => 'mhs1@stitek.ac.id',
        'password' => bcrypt('user123'),
        'role' => 'user',
    ]);
    $user1->assignRole('user');

    $user2 = User::create([
        'name' => 'Mahasiswa 2',
        'email' => 'mhs2@stitek.ac.id',
        'password' => bcrypt('user123'),
        'role' => 'user',
    ]);
    $user2->assignRole('user');

    // 3. Simulasikan Pembuatan Peminjaman oleh User 1 (PENDING)
    $bookingService = app(BookingService::class);
    
    $booking1Data = [
        'tipe_peminjaman' => 'barang',
        'barang_id' => $barang->id,
        'tanggal_mulai' => now()->addDay()->format('Y-m-d'),
        'tanggal_selesai' => now()->addDays(2)->format('Y-m-d'),
        'waktu_mulai' => '08:00',
        'waktu_selesai' => '10:00',
        'keterangan' => 'Rapat Himpunan',
    ];

    $peminjaman1 = $bookingService->createBooking($booking1Data, $user1);

    // 4. Simulasikan Pembuatan Peminjaman oleh User 2 (PENDING)
    $booking2Data = $booking1Data; // Meminjam barang yang sama di waktu yang sama
    $peminjaman2 = $bookingService->createBooking($booking2Data, $user2);

    // Verifikasi awal: Kedua peminjaman berstatus 'menunggu' (PENDING)
    expect($peminjaman1->status)->toBe(Peminjaman::STATUS_PENDING);
    expect($peminjaman2->status)->toBe(Peminjaman::STATUS_PENDING);

    // Verifikasi awal: Dengan Delayed Deduction, stok_tersedia tetap 1
    $barang->refresh();
    expect($barang->stok_tersedia)->toBe(1);

    // 5. Admin menyetujui Peminjaman User 1
    $bookingService->approveBooking($peminjaman1);

    // 6. Asersi Hasil
    $peminjaman1->refresh();
    $peminjaman2->refresh();
    $barang->refresh();

    // - Peminjaman 1 disetujui, approved_at terisi
    expect($peminjaman1->status)->toBe(Peminjaman::STATUS_APPROVED);
    expect($peminjaman1->approved_at)->not->toBeNull();

    // - Peminjaman 2 otomatis ditolak oleh sistem karena stok barang habis
    expect($peminjaman2->status)->toBe(Peminjaman::STATUS_REJECTED);
    expect($peminjaman2->keterangan)->toContain('Dibatalkan sistem: Stok habis');

    // - Stok barang yang tersisa harus tepat 0, tidak boleh minus (-1)
    expect($barang->stok_tersedia)->toBe(0);

    // 7. Pengujian Tambahan: Admin mencoba menyetujui Peminjaman 2 (harus gagal dengan exception)
    $this->expectException(\RuntimeException::class);
    $bookingService->approveBooking($peminjaman2);
});
