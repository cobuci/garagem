<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

use Spatie\OneTimePasswords\Models\OneTimePassword;

uses(RefreshDatabase::class);

// ─── send ──────────────────────────────────────────────────────────────────

it('returns 422 when email is missing on send', function () {
    postJson(route('api.v1.auth.otp.send'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when email does not exist on send', function () {
    postJson(route('api.v1.auth.otp.send'), ['email' => 'notfound@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('sends otp and returns 202 with success envelope', function () {
    $user = User::factory()->create();

    postJson(route('api.v1.auth.otp.send'), ['email' => $user->email])
        ->assertStatus(202)
        ->assertJson([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);

    assertDatabaseHas('one_time_passwords', [
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
    ]);
});

// ─── verify ────────────────────────────────────────────────────────────────

it('returns 422 when email is missing on verify', function () {
    postJson(route('api.v1.auth.otp.verify'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when code is missing on verify', function () {
    $user = User::factory()->create();

    postJson(route('api.v1.auth.otp.verify'), ['email' => $user->email])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

it('returns 422 when code is not 6 digits on verify', function () {
    $user = User::factory()->create();

    postJson(route('api.v1.auth.otp.verify'), ['email' => $user->email, 'code' => '123'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

it('returns 422 error envelope for invalid otp code', function () {
    $user = User::factory()->create();
    $user->sendOneTimePassword();

    postJson(route('api.v1.auth.otp.verify'), ['email' => $user->email, 'code' => '000000'])
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'message' => 'Invalid or expired OTP.',
        ]);
});

it('returns 200 success envelope with token and user data for valid otp', function () {
    $user = User::factory()->create();

    $code = '123456';
    OneTimePassword::create([
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
        'password'             => $code,
        'expires_at'           => now()->addMinutes(10),
        'origin_properties'    => [
            'ip'        => '127.0.0.1',
            'userAgent' => 'Symfony',
        ],
    ]);

    postJson(route('api.v1.auth.otp.verify'), ['email' => $user->email, 'code' => $code])
        ->assertOk()
        ->assertJson(['success' => true, 'message' => 'Authenticated successfully.'])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'user' => ['id', 'name', 'email'],
            ],
        ])
        ->assertJsonPath('data.user.email', $user->email);
});
