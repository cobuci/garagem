<?php

namespace Tests\Feature\Customers;

use App\Enums\SaleStatus;
use App\Livewire\Customers\Index;
use App\Models\Customer;
use App\Models\Sale;
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

it('shows the total due amount for each customer', function () {
    $customer1 = Customer::factory()->create();
    $customer2 = Customer::factory()->create();

    $sale1 = Sale::factory()->create([
        'customer_id' => $customer1->id,
        'status'      => SaleStatus::Pending,
    ]);
    $sale1->update(['total_amount' => 100.00]);

    $sale2 = Sale::factory()->create([
        'customer_id' => $customer2->id,
        'status'      => SaleStatus::Paid,
    ]);
    $sale2->update(['total_amount' => 200.00]);

    Livewire::test(Index::class)
        ->assertSee('100,00')
        ->assertSee('0,00');
});
