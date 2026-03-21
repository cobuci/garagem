<?php

namespace Tests\Feature\Customers;

use App\Livewire\Customers\Show;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('common user can view customers list if has permission', function () {
    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user)
        ->get(route('customers.index'))
        ->assertStatus(200);
});

test('user without permission cannot view customers list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.index'))
        ->assertStatus(403);
});

test('common user can view customer profile if has permission', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $customer = Customer::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.show', $customer))
        ->assertStatus(200);
});

test('user without permission cannot view customer profile', function () {
    $user = User::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($user)
        ->get(route('customers.show', $customer))
        ->assertStatus(403);
});

test('only users with DeleteCustomer permission can delete a customer', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create();
    $user->assignRole('user');

    $customer = Customer::factory()->create();

    Livewire::actingAs($user)
        ->test(Show::class, ['customer' => $customer])
        ->call('delete')
        ->assertStatus(403);

    expect(Customer::where('id', $customer->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(Show::class, ['customer' => $customer])
        ->call('delete')
        ->assertRedirect(route('customers.index'));

    expect(Customer::where('id', $customer->id)->exists())->toBeFalse();
});
