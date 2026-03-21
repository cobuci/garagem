<?php

namespace Tests\Feature\Roles;

use App\Livewire\Roles\Index;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('it can access the roles management page', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)
        ->get(route('roles.index'))
        ->assertStatus(200);
});

test('it can see roles and permissions', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Role::findOrCreate('manager');
    Permission::findOrCreate('view test');

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Admin')
        ->assertSee('Manager')
        ->assertSee('test');
});

test('it can toggle permissions for a role', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $manager = Role::findOrCreate('manager');
    $permission = Permission::findOrCreate('edit test');

    $manager->syncPermissions([]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('selectRole', $manager->id)
        ->set('rolePermissions', [$permission->name])
        ->call('savePermissions')
        ->assertDispatched('wireui:notification');

    expect($manager->fresh()->hasPermissionTo($permission))->toBeTrue();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('selectRole', $manager->id)
        ->set('rolePermissions', [])
        ->call('savePermissions')
        ->assertDispatched('wireui:notification');

    expect($manager->fresh()->hasPermissionTo($permission))->toBeFalse();
});

test('it cannot toggle permissions for admin role', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $admin = Role::findByName('admin');
    $permission = Permission::first();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('selectRole', $admin->id)
        ->set('rolePermissions', [])
        ->call('savePermissions')
        ->assertDispatched('wireui:notification');

    expect($admin->fresh()->hasPermissionTo($permission))->toBeTrue();
});

test('it can create a new role', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('form.name', 'Accountant')
        ->call('saveRole')
        ->assertHasNoErrors()
        ->assertSet('showDrawer', false)
        ->assertSet('form.name', '')
        ->assertDispatched('wireui:notification');

    expect(Role::where('name', 'Accountant')->exists())->toBeTrue();
});

test('it updates permission list when switching roles', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $roleA = Role::create(['name' => 'Role A']);
    $roleB = Role::create(['name' => 'Role B']);

    $permission = Permission::findOrCreate('view test');

    $roleA->givePermissionTo($permission);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('selectRole', $roleA->id)
        ->assertSet('rolePermissions', [$permission->name])
        ->call('selectRole', $roleB->id)
        ->assertSet('rolePermissions', []);
});
