<?php

namespace Tests\Feature\Customers;

use App\Livewire\Customers\Index;
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

it('can render customers index page', function () {
    get(route('customers.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('can list customers', function () {
    $customers = Customer::factory()->count(3)->create();

    $test = Livewire::test(Index::class);

    foreach ($customers as $customer) {
        $test->assertSee($customer->name);
    }
});

it('shows empty message when no customers exist', function () {
    Livewire::test(Index::class)
        ->assertSee(__('customers.empty'));
});
