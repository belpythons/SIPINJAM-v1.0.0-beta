<?php

use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertQueued(ResetPasswordMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('reset password screen can be rendered', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertQueued(ResetPasswordMail::class, function ($mail) {
        $response = $this->get('/reset-password/'.$mail->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Mail::assertQueued(ResetPasswordMail::class, function ($mail) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $mail->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});
