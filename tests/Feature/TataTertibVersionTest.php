<?php

use App\Models\TataTertibVersion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * R5 — TataTertibController sebelumnya me-render halaman tanpa props sama
 * sekali; isinya hardcoded di Vue dan tidak pernah terhubung ke
 * TataTertibVersion. Sekarang admin membuat versi baru, user langsung
 * melihatnya tanpa deploy ulang.
 */
test('user melihat versi tata tertib terbaru yang berlaku', function () {
    TataTertibVersion::create([
        'versi' => '1.0',
        'konten' => 'Isi versi lama.',
        'berlaku_sejak' => now()->subDays(10),
    ]);
    TataTertibVersion::create([
        'versi' => '2.0',
        'konten' => 'Isi versi terbaru.',
        'berlaku_sejak' => now()->subDay(),
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/tata_tertib')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('User/TataTertib')
            ->where('versi.versi', '2.0')
            ->where('versi.konten', 'Isi versi terbaru.')
        );
});

test('admin membuat versi baru tata tertib tanpa mengubah versi lama', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post('/admin/kelola-tata-tertib', [
            'versi' => '1.0',
            'konten' => 'Isi tata tertib pertama.',
        ])
        ->assertRedirect();

    expect(TataTertibVersion::count())->toBe(1);

    $this->actingAs($admin)
        ->post('/admin/kelola-tata-tertib', [
            'versi' => '1.1',
            'konten' => 'Revisi tata tertib.',
        ])
        ->assertRedirect();

    // Append, bukan edit in-place — kedua versi tetap ada.
    expect(TataTertibVersion::count())->toBe(2);
    expect(TataTertibVersion::where('versi', '1.0')->first()->konten)->toBe('Isi tata tertib pertama.');
});
