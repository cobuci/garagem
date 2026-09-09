<?php

use App\Livewire\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

use Spatie\OneTimePasswords\Models\OneTimePassword;

uses(RefreshDatabase::class);

it('renders the login page', function () {
    app()->setLocale('en');

    get(route('login'))
        ->assertOk()
        ->assertSee('Welcome back');
});

it('renders the login page in portuguese', function () {
    app()->setLocale('pt_BR');

    get(route('login'))
        ->assertOk()
        ->assertSee('Bem-vindo de volta');
});

it('validates email is required and exists', function () {
    Livewire::test(Login::class)
        ->set('email', '')
        ->call('sendOtp')
        ->assertHasErrors(['email' => 'required']);

    Livewire::test(Login::class)
        ->set('email', 'notfound@example.com')
        ->call('sendOtp')
        ->assertHasErrors(['email' => 'exists']);
});

it('sends an otp to an existing user and moves to step 2', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->call('sendOtp')
        ->assertSet('step', 2)
        ->assertHasNoErrors()
        ->assertDispatched('wireui:notification');

    assertDatabaseHas('one_time_passwords', [
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
    ]);
});

it('validates otp is required and has 6 digits', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('step', 2)
        ->set('otp', '')
        ->call('verifyOtp')
        ->assertHasErrors(['otp' => 'required']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('step', 2)
        ->set('otp', '123')
        ->call('verifyOtp')
        ->assertHasErrors(['otp' => 'min']);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('step', 2)
        ->set('otp', '1234567')
        ->call('verifyOtp')
        ->assertHasErrors(['otp' => 'max']);
});

it('authenticates user with correct otp', function () {
    $user = User::factory()->create(['email' => 'auth@example.com']);

    $otp = '123456';
    OneTimePassword::create([
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
        'password'             => $otp,
        'expires_at'           => now()->addMinutes(10),
        'origin_properties'    => [
            'ip'        => '127.0.0.1',
            'userAgent' => 'Symfony',
        ],
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('otp', $otp)
        ->call('verifyOtp')
        ->assertHasNoErrors();

    assertAuthenticatedAs($user);
});

it('authenticates user when otp is passed as argument to verifyOtp', function () {
    $user = User::factory()->create(['email' => 'arg@example.com']);

    $otp = '654321';
    OneTimePassword::create([
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
        'password'             => $otp,
        'expires_at'           => now()->addMinutes(10),
        'origin_properties'    => [
            'ip'        => '127.0.0.1',
            'userAgent' => 'Symfony',
        ],
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->call('verifyOtp', $otp)
        ->assertHasNoErrors();

    assertAuthenticatedAs($user);
});

it('authenticates user with remember me enabled', function () {
    $user = User::factory()->create(['email' => 'remember@example.com']);

    $otp = '123456';
    OneTimePassword::create([
        'authenticatable_id'   => $user->id,
        'authenticatable_type' => User::class,
        'password'             => $otp,
        'expires_at'           => now()->addMinutes(10),
        'origin_properties'    => [
            'ip'        => '127.0.0.1',
            'userAgent' => 'Symfony',
        ],
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('otp', $otp)
        ->set('rememberMe', true)
        ->call('verifyOtp')
        ->assertHasNoErrors();

    assertAuthenticatedAs($user);
});

it('redirects authenticated user to dashboard if access login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

it('fails to authenticate user with incorrect otp', function () {
    $user = User::factory()->create();
    $user->sendOneTimePassword();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('step', 2)
        ->set('otp', '000000')
        ->call('verifyOtp')
        ->assertHasErrors(['otp'])
        ->assertDispatched('wireui:notification');

    assertGuest();
});

it('can go back to email step', function () {
    Livewire::test(Login::class)
        ->set('email', 'test@example.com')
        ->set('step', 2)
        ->set('otp', '123456')
        ->call('backToEmail')
        ->assertSet('step', 1)
        ->assertSet('otp', '')
        ->assertHasNoErrors();
});
