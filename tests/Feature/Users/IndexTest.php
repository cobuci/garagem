<?php

use App\Livewire\Users\Index;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    actingAs($this->user);
});

it('can render the user management tab', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee(__('admin.users.title'));
});

it('can list users', function () {
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);

    Livewire::test(Index::class)
        ->assertSee('User One')
        ->assertSee('User Two');
});

it('can create a user', function () {
    Livewire::test(Index::class)
        ->call('create')
        ->set('form.name', 'New User')
        ->set('form.email', 'new@example.com')
        ->set('form.password', 'password123')
        ->set('form.roles', ['admin'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showDrawer', false);

    expect(User::whereEmail('new@example.com')->exists())->toBeTrue();
    $newUser = User::whereEmail('new@example.com')->first();
    expect($newUser->hasRole('admin'))->toBeTrue();
});

it('can edit a user', function () {
    $user = User::factory()->create(['name' => 'Old Name']);
    $user->assignRole('manager');

    Livewire::test(Index::class)
        ->call('edit', $user->id)
        ->assertSet('form.name', 'Old Name')
        ->set('form.name', 'Updated Name')
        ->set('form.roles', ['admin'])
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('Updated Name');
    expect($user->fresh()->hasRole('admin'))->toBeTrue();
});

it('can delete a user', function () {
    $user = User::factory()->create();

    Livewire::test(Index::class)
        ->call('delete', $user->id)
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull();
});

it('cannot delete self', function () {
    Livewire::test(Index::class)
        ->call('delete', $this->user->id)
        ->assertHasNoErrors();

    expect(User::find($this->user->id))->not->toBeNull();
});

it('validates required fields when creating', function () {
    Livewire::test(Index::class)
        ->call('create')
        ->call('save')
        ->assertHasErrors([
            'form.name'     => 'required',
            'form.email'    => 'required',
            'form.password' => 'required',
            'form.roles'    => 'required',
        ]);
});

it('denies access to unauthorized user', function () {
    $unauthorized = User::factory()->create();
    actingAs($unauthorized);

    Livewire::test(Index::class)
        ->assertForbidden();
});
