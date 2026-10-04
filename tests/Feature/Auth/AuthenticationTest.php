<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/login');
});

test('blocked users can not authenticate and see specific error message', function () {
    $user = User::factory()->create([
        'is_blocked' => true,
        'blocked_reason' => 'Melanggar tata tertib',
        'blocked_until' => now()->addDays(5),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
    $errors = session('errors')->get('email');
    $this->assertStringContainsString('Akun Anda diblokir hingga', $errors[0]);
    $this->assertStringContainsString('Alasan: Melanggar tata tertib', $errors[0]);
});
