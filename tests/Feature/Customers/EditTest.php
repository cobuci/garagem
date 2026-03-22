<?php

namespace Tests\Feature\Customers;

use App\Enums\Gender;
use App\Livewire\Customers\Edit;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render customer edit component', function () {
    Livewire::test(Edit::class)
        ->assertStatus(200);
});

it('fills form when edit:customer event is dispatched', function () {
    $customer = Customer::factory()->create([
        'name'  => 'John Doe',
        'email' => 'john@example.com',
    ]);

    Livewire::test(Edit::class)
        ->dispatch('edit:customer', component: 'customers.edit', customer: $customer->id)
        ->assertSet('showDrawer', true)
        ->assertSet('form.name', 'John Doe')
        ->assertSet('form.email', 'john@example.com');
});

it('validates required fields on update', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Edit::class)
        ->dispatch('edit:customer', customer: $customer->id)
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors(['form.name' => 'required']);
});

it('allows keeping the same name for the same customer', function () {
    $customer = Customer::factory()->create(['name' => 'John Doe']);

    Livewire::test(Edit::class)
        ->dispatch('edit:customer', component: 'customers.edit', customer: $customer->id)
        ->call('save')
        ->assertHasNoErrors();
});

it('validates unique name for other customers', function () {
    Customer::factory()->create(['name' => 'Existing Customer']);
    $customer = Customer::factory()->create(['name' => 'John Doe']);

    Livewire::test(Edit::class)
        ->dispatch('edit:customer', component: 'customers.edit', customer: $customer->id)
        ->set('form.name', 'Existing Customer')
        ->call('save')
        ->assertHasErrors(['form.name' => 'unique']);
});

it('can update a customer', function () {
    $customer = Customer::factory()->create([
        'name'  => 'Old Name',
        'email' => 'old@example.com',
    ]);

    Livewire::test(Edit::class)
        ->dispatch('edit:customer', component: 'customers.edit', customer: $customer->id)
        ->set('form.name', 'New Name')
        ->set('form.email', 'new@example.com')
        ->set('form.phone', '987654321')
        ->set('form.gender', Gender::Male->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showDrawer', false)
        ->assertDispatched('customer:updated');

    assertDatabaseHas('customers', [
        'id'     => $customer->id,
        'name'   => 'New Name',
        'email'  => 'new@example.com',
        'phone'  => '987654321',
        'gender' => Gender::Male->value,
    ]);
});
