<?php

namespace Tests\Feature\Customers;

use App\Livewire\Customers\Show;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render customer show page', function () {
    $customer = Customer::factory()->create();

    get(route('customers.show', $customer))
        ->assertOk()
        ->assertSeeLivewire(Show::class);
});

it('shows customer name and fake purchase statistics', function () {
    $customer = Customer::factory()->create(['name' => 'John Doe']);

    Livewire::test(Show::class, ['customer' => $customer])
        ->assertSee('John Doe')
        ->assertSee('R$ 1.500,00')
        ->assertSee('R$ 750,00')
        ->assertSee('5');
});

it('lists fake customer orders', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Show::class, ['customer' => $customer])
        ->assertSee('R$ 100,00')
        ->assertSee('R$ 500,00');
});

it('dispatches open-drawer event when edit button is clicked', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Show::class, ['customer' => $customer])
        ->call('edit')
        ->assertDispatched('open-drawer');
});
