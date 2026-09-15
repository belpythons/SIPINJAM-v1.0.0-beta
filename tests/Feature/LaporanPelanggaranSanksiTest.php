<?php

use App\Models\Laporan;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * R4 — Alur inti: sivitas melapor pelanggaran atas sebuah peminjaman,
 * admin memutuskan sanksi (bekukan akun N hari), pelaku (bukan pelapor)
 * langsung tidak bisa login, lalu otomatis pulih setelah blocked_until lewat.
 */
test('lapor pelanggaran lalu admin putuskan sanksi memblokir pelaku', function () {
    $pelapor = User::factory()->create();
    $pelaku = User::factory()->create(['password' => bcrypt('password123')]);
    $admin = User::factory()->admin()->create();

    $peminjaman = Peminjaman::factory()->create([
        'user_id' => $pelaku->id,
        'status' => Peminjaman::STATUS_APPROVED,
    ]);

    $this->actingAs($pelapor)
        ->post('/lapor-pelanggaran', [
            'peminjaman_id' => $peminjaman->id,
            'jenis' => 'belum_kembali',
            'deskripsi' => 'Barang belum dikembalikan setelah 3 hari.',
        ])
        ->assertRedirect(route('dashboard'));

    $laporan = Laporan::first();
    expect($laporan)->not->toBeNull()
        ->and($laporan->peminjaman_id)->toBe($peminjaman->id)
        ->and($laporan->status)->toBe(Laporan::STATUS_MENUNGGU);

    $this->actingAs($admin)
        ->patch("/admin/pelanggaran/{$laporan->id}/sanksi", [
            'hari' => 7,
            'alasan' => 'Barang belum dikembalikan lebih dari 3 hari.',
        ])
        ->assertRedirect();

    $laporan->refresh();
    $pelaku->refresh();

    expect($laporan->status)->toBe(Laporan::STATUS_DITINDAK)
        ->and($pelaku->isBlocked())->toBeTrue()
        ->and($pelaku->is_blocked)->toBeTrue();

    // Pelaku gagal login selagi diblokir (logout dulu dari sesi admin).
    $this->post('/logout');
    $this->post('/login', [
        'email' => $pelaku->email,
        'password' => 'password123',
    ])->assertSessionHasErrors('email');

    // Setelah blocked_until lewat, sanction:apply membebaskan otomatis.
    $pelaku->forceFill(['blocked_until' => now()->subMinute()])->saveQuietly();
    $this->artisan('sanction:apply')->assertSuccessful();

    expect($pelaku->fresh()->is_blocked)->toBeFalse();
});
