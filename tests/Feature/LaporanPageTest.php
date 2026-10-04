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
test('rute /laporan dialihkan ke riwayat peminjaman /bookings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/laporan')
        ->assertRedirect(route('bookings.index'));
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

test('daftar riwayat peminjaman user ter-paginasi dan menyaring status', function () {
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
        ->get('/bookings?status=selesai')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('bookings.total', 3)
            ->has('bookings.links')
        );
});
