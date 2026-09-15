<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

/**
 * B-07 — Login Google sebelumnya menerima email apa pun (termasuk Gmail
 * pribadi) dan otomatis membuatkan akun. Domain kampus kini menjadi gerbang.
 */
function fakeGoogleUser(string $email, string $id = 'google-123', string $name = 'Budi Santoso'): void
{
    $socialiteUser = (new SocialiteUser)->map([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'avatar' => 'https://example.test/avatar.png',
    ]);

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);
}

beforeEach(function () {
    config()->set('sipinjam.allowed_email_domains', ['stitek.ac.id']);
    config()->set('sipinjam.auto_provision_google_users', true);

});

test('email di luar domain kampus ditolak dan tidak membuat akun', function () {
    fakeGoogleUser('orang.luar@gmail.com');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');

    expect(session('error'))->toContain('email kampus');
    expect(User::where('email', 'orang.luar@gmail.com')->exists())->toBeFalse();
    $this->assertGuest();
});

test('email domain kampus diterima dan akun dibuat otomatis', function () {
    fakeGoogleUser('budi@stitek.ac.id');

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard'));

    $user = User::where('email', 'budi@stitek.ac.id')->first();
    expect($user)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

test('subdomain kampus juga diterima', function () {
    fakeGoogleUser('mahasiswa@mhs.stitek.ac.id');

    $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

    expect(User::where('email', 'mahasiswa@mhs.stitek.ac.id')->exists())->toBeTrue();
});

test('domain yang menyerupai tetapi bukan milik kampus ditolak', function () {
    // "bukanstitek.ac.id" berakhiran sama; pencocokan harus per-label,
    // bukan sekadar str_ends_with pada string mentah.
    fakeGoogleUser('penipu@bukanstitek.ac.id');

    $this->get('/auth/google/callback')->assertRedirect(route('login'));

    expect(User::where('email', 'penipu@bukanstitek.ac.id')->exists())->toBeFalse();
    $this->assertGuest();
});

test('auto_provision dimatikan: user baru ditolak, user lama tetap bisa masuk', function () {
    config()->set('sipinjam.auto_provision_google_users', false);

    fakeGoogleUser('barudaftar@stitek.ac.id');

    $this->get('/auth/google/callback')->assertRedirect(route('login'));

    expect(session('error'))->toContain('belum terdaftar');
    expect(User::where('email', 'barudaftar@stitek.ac.id')->exists())->toBeFalse();
    $this->assertGuest();
});

test('auto_provision dimatikan: akun yang sudah dibuat admin tetap dapat login', function () {
    config()->set('sipinjam.auto_provision_google_users', false);

    $existing = User::factory()->create(['email' => 'dosen@stitek.ac.id']);

    fakeGoogleUser('dosen@stitek.ac.id');

    $this->get('/auth/google/callback')->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($existing->fresh());
});
