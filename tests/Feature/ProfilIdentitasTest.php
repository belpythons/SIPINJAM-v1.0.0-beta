<?php

use App\Models\User;

/**
 * P2 Tahap 1 — identitas sivitas & nomor WhatsApp pada profil.
 */
function dataProfil(array $ubah = []): array
{
    return array_merge([
        'name' => 'Budi Santoso',
        'email' => 'budi@stitek.ac.id',
    ], $ubah);
}

test('identitas sivitas tersimpan lewat halaman profil', function () {
    $user = User::factory()->peminjam()->create();

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil([
            'identity_number' => '202312001',
            'user_type' => 'mahasiswa',
            'program_studi' => 'Teknik Informatika',
            'angkatan' => '2023',
        ]))
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->identity_number)->toBe('202312001')
        ->and($user->user_type)->toBe('mahasiswa')
        ->and($user->program_studi)->toBe('Teknik Informatika')
        ->and($user->angkatan)->toBe('2023');
});

test('nomor WhatsApp disimpan ternormalisasi E.164 apa pun format masukannya', function () {
    $format = [
        '081234567890' => '+6281234567890',
        '6281234567891' => '+6281234567891',
        '+62 812-3456-7892' => '+6281234567892',
        '81234567893' => '+6281234567893',
    ];

    foreach ($format as $masukan => $harapan) {
        $user = User::factory()->peminjam()->create();

        $this->actingAs($user)
            ->post('/profile/edit', dataProfil([
                'email' => fake()->unique()->safeEmail(),
                'phone' => $masukan,
            ]))
            ->assertSessionHasNoErrors();

        expect($user->fresh()->phone)->toBe($harapan, "masukan {$masukan}");
    }
});

test('nomor tidak valid ditolak dengan pesan Indonesia', function () {
    $user = User::factory()->peminjam()->create();

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil(['phone' => 'bukan-nomor']))
        ->assertSessionHasErrors('phone');

    expect($user->fresh()->phone)->toBeNull();
});

test('nomor yang sudah dipakai akun lain ditolak', function () {
    User::factory()->peminjam()->create(['phone' => '+6281234567890']);
    $user = User::factory()->peminjam()->create();

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil(['phone' => '081234567890']))
        ->assertSessionHasErrors('phone');
});

test('mengganti nomor membatalkan verifikasi sebelumnya', function () {
    $user = User::factory()->peminjam()->create([
        'phone' => '+6281234567890',
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil(['phone' => '081298765432']))
        ->assertSessionHasNoErrors();

    $user->refresh();

    // Nomor baru harus dibuktikan lagi lewat OTP — kalau tidak, verifikasi
    // kehilangan artinya sama sekali.
    expect($user->phone)->toBe('+6281298765432')
        ->and($user->phone_verified_at)->toBeNull();
});

test('menyimpan nomor yang sama TIDAK membatalkan verifikasi', function () {
    $user = User::factory()->peminjam()->create([
        'phone' => '+6281234567890',
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil(['phone' => '081234567890']))
        ->assertSessionHasNoErrors();

    expect($user->fresh()->phone_verified_at)->not->toBeNull();
});

test('tipe sivitas di luar daftar ditolak', function () {
    $user = User::factory()->peminjam()->create();

    $this->actingAs($user)
        ->post('/profile/edit', dataProfil(['user_type' => 'rektor']))
        ->assertSessionHasErrors('user_type');
});

// ── Privasi (UU PDP) ─────────────────────────────────────────────────────────

test('nomor telepon tidak ikut terserialisasi secara tidak sengaja', function () {
    $user = User::factory()->peminjam()->create(['phone' => '+6281234567890']);

    // $hidden membuatnya fail-safe: tempat yang butuh harus meminta eksplisit.
    expect($user->toArray())->not->toHaveKey('phone')
        ->and(json_decode($user->toJson(), true))->not->toHaveKey('phone');
});

test('admin dan staf aset melihat nomor penuh, peran lain melihat yang tersamar', function () {
    $pemilik = User::factory()->peminjam()->create(['phone' => '+6281234567890']);

    expect($pemilik->phoneUntuk(User::factory()->admin()->create()))->toBe('+6281234567890')
        ->and($pemilik->phoneUntuk(User::factory()->berperan('staf_aset')->create()))->toBe('+6281234567890')
        ->and($pemilik->phoneUntuk(User::factory()->berperan('pimpinan')->create()))->toBe('+62812****7890')
        ->and($pemilik->phoneUntuk(User::factory()->peminjam()->create()))->toBe('+62812****7890')
        ->and($pemilik->phoneUntuk(null))->toBe('+62812****7890');
});

test('pemilik selalu boleh melihat nomornya sendiri', function () {
    $pemilik = User::factory()->peminjam()->create(['phone' => '+6281234567890']);

    expect($pemilik->phoneUntuk($pemilik))->toBe('+6281234567890');
});

test('halaman profil mengirim nomor pemiliknya secara eksplisit', function () {
    $user = User::factory()->peminjam()->create([
        'phone' => '+6281234567890',
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('user.phone', '+6281234567890')
            ->where('user.phone_verified', true)
        );
});

// ── Helper kelengkapan profil ────────────────────────────────────────────────

test('profilLengkap menuntut identitas inti sesuai tipe sivitas', function () {
    $kosong = User::factory()->peminjam()->create();
    expect($kosong->profilLengkap())->toBeFalse();

    $mahasiswaTanpaProdi = User::factory()->peminjam()->create([
        'identity_number' => '202312001', 'user_type' => 'mahasiswa',
    ]);
    expect($mahasiswaTanpaProdi->profilLengkap())->toBeFalse();

    $mahasiswa = User::factory()->peminjam()->create([
        'identity_number' => '202312002', 'user_type' => 'mahasiswa', 'program_studi' => 'TI',
    ]);
    expect($mahasiswa->profilLengkap())->toBeTrue();

    // Staf memakai unit_kerja, bukan program_studi.
    $staf = User::factory()->berperan('staff')->create([
        'identity_number' => '19900101', 'user_type' => 'staff', 'unit_kerja' => 'BAUK',
    ]);
    expect($staf->profilLengkap())->toBeTrue();
});
