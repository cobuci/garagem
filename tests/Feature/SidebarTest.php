<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertGuest;

uses(RefreshDatabase::class);

it('renders the sidebar component', function () {
    $settings = Setting::factory()->create(['store_name' => 'Custom Store']);
    config(['app.name' => 'Custom Store']);

    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('layout.sidebar')
        ->assertSee('Custom Store')
        ->assertSee(__('sidebar.dashboard'))
        ->assertSee(__('sidebar.sales'))
        ->assertSee(__('sidebar.settings'))
        ->assertSee(__('sidebar.logout'))
        ->assertSee(__('sidebar.dark_mode'));
});

it('can logout from the sidebar', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('layout.sidebar')
        ->call('logout')
        ->assertRedirect(route('login'));

    assertGuest();
});

it('shows user info in the sidebar', function () {
    $user = User::factory()->create([
        'name'  => 'John Doe',
        'email' => 'john@example.com',
    ]);
    $this->actingAs($user);

    Livewire::test('layout.sidebar')
        ->assertSee('John Doe')
        ->assertSee('john@example.com');
});
