<?php

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * B-01 — /laporan me-render view('user.laporan') yang berkasnya tidak pernah
 * ada, sehingga rute di sidebar user selalu 500. /admin/laporan me-render Blade
 * penuh di luar Inertia.
 */
test('rute /laporan membuka halaman Inertia, bukan error 500', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/laporan')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('User/Laporan')
            ->has('peminjamans.data')
            ->has('stats')
        );
});

test('rute /admin/laporan membuka halaman Inertia', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin/laporan')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Laporan')
            ->has('peminjamans.data')
            ->has('stats')
            ->has('topRuangan')
            ->has('topBarang')
        );
});

test('daftar laporan user ter-paginasi dan menyaring status', function () {
    $user = User::factory()->create();

    Peminjaman::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => Peminjaman::STATUS_DONE,
    ]);
    Peminjaman::factory()->create([
        'user_id' => $user->id,
        'status' => Peminjaman::STATUS_PENDING,
    ]);

    $this->actingAs($user)
        ->get('/laporan?status=selesai')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('peminjamans.total', 3)
            ->has('peminjamans.links')
        );
});
