<?php

namespace Tests\Feature\Customers;

use App\Enums\Gender;
use App\Livewire\Customers\Create;
use App\Livewire\Customers\Index;
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

it('can render customer create component', function () {
    Livewire::test(Create::class)
        ->assertStatus(200);
});

it('opens drawer when open-drawer event is dispatched', function () {
    Livewire::test(Create::class)
        ->dispatch('open-drawer', component: 'customers.create')
        ->assertSet('showDrawer', true);
});

it('validates required fields', function () {
    Livewire::test(Create::class)
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors(['form.name' => 'required']);
});

it('validates unique customer name', function () {
    Customer::factory()->create(['name' => 'John Doe']);

    Livewire::test(Create::class)
        ->set('form.name', 'John Doe')
        ->call('save')
        ->assertHasErrors(['form.name' => 'unique']);
});

it('can create a new customer', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'Jane Doe')
        ->set('form.email', 'jane@example.com')
        ->set('form.phone', '123456789')
        ->set('form.gender', Gender::Female->value)
        ->set('form.zip_code', '12345-678')
        ->set('form.street', 'Main St')
        ->set('form.neighborhood', 'Downtown')
        ->set('form.address', '123 Apt')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showDrawer', false)
        ->assertDispatched('customer:created');

    assertDatabaseHas('customers', [
        'name'  => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

it('refreshes index when customer is created', function () {
    Livewire::test(Index::class)
        ->assertSee(__('customers.empty'))
        ->dispatch('customer:created')
        ->assertStatus(200);
});
