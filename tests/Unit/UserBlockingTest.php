<?php

use App\Models\Sanksi;
use App\Models\User;

/**
 * K-2 — `isBlocked()` bersandar pada tabel `sanksi`, dengan jalur mundur ke
 * kolom cache selama tabel itu belum menjadi penulis utama (baru di P5).
 *
 * Test ini mengunci KEDUA jalur sekaligus, supaya penghapusan fallback di P5
 * tidak diam-diam membebaskan akun yang seharusnya masih terblokir.
 */
test('jalur baru: sanksi blokir aktif membuat user terblokir', function () {
    $user = User::factory()->create(['is_blocked' => false, 'blocked_until' => null]);

    Sanksi::create([
        'user_id' => $user->id,
        'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now()->subDay(),
        'sampai_at' => now()->addDays(7),
        'alasan' => 'Uji sanksi aktif',
    ]);

    expect($user->fresh()->isBlocked())->toBeTrue();
});

test('jalur baru: sanksi tanpa batas akhir (sampai ganti rugi lunas) tetap memblokir', function () {
    $user = User::factory()->create(['is_blocked' => false, 'blocked_until' => null]);

    Sanksi::create([
        'user_id' => $user->id,
        'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now()->subDay(),
        'sampai_at' => null,
        'alasan' => 'Menunggu ganti rugi',
    ]);

    expect($user->fresh()->isBlocked())->toBeTrue();
});

test('jalur baru: sanksi yang sudah dicabut tidak memblokir', function () {
    $user = User::factory()->create(['is_blocked' => false, 'blocked_until' => null]);

    Sanksi::create([
        'user_id' => $user->id,
        'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now()->subDays(3),
        'sampai_at' => now()->addDays(7),
        'dicabut_at' => now()->subDay(),
        'alasan_pencabutan' => 'Keberatan diterima',
    ]);

    expect($user->fresh()->isBlocked())->toBeFalse();
});

test('jalur baru: sanksi yang sudah berakhir tidak memblokir', function () {
    $user = User::factory()->create(['is_blocked' => false, 'blocked_until' => null]);

    Sanksi::create([
        'user_id' => $user->id,
        'jenis' => Sanksi::JENIS_BLOKIR,
        'mulai_at' => now()->subDays(30),
        'sampai_at' => now()->subDay(),
        'alasan' => 'Sudah lewat',
    ]);

    expect($user->fresh()->isBlocked())->toBeFalse();
});

test('jalur mundur: kolom cache masih dihormati selama tabel sanksi kosong', function () {
    $user = User::factory()->create([
        'is_blocked' => true,
        'blocked_until' => now()->addDays(5),
    ]);

    expect(Sanksi::count())->toBe(0)
        ->and($user->isBlocked())->toBeTrue();
});

test('jalur mundur: perilaku lama dipertahankan persis — blocked_until kosong berarti TIDAK terblokir', function () {
    // Ini yang terjadi pada dua akun "terblokir" bawaan UserSeeder: kolom
    // blocked_until tidak pernah diisi, sehingga isBlocked() sudah false jauh
    // sebelum P1. Dikunci di sini agar perubahan perilakunya ketahuan.
    $user = User::factory()->create([
        'is_blocked' => true,
        'blocked_until' => null,
    ]);

    expect($user->isBlocked())->toBeFalse();
});

test('jalur mundur: masa blokir kolom yang sudah lewat tidak lagi memblokir', function () {
    $user = User::factory()->create([
        'is_blocked' => true,
        'blocked_until' => now()->subDay(),
    ]);

    expect($user->isBlocked())->toBeFalse();
});
