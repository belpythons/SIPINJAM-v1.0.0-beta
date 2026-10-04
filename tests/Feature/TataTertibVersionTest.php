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
test('user dapat membuka halaman tata tertib resmi dan regulasi peminjaman', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/tata_tertib')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('User/TataTertib')
        );
});
