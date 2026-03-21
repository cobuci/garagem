<?php

namespace Tests\Feature\Customers;

use App\Enums\SaleStatus;
use App\Livewire\Customers\Index;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
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

it('lists customers in alphabetical order', function () {
    Customer::factory()->create(['name' => 'Zebra']);
    Customer::factory()->create(['name' => 'Abelha']);
    Customer::factory()->create(['name' => 'Baleia']);

    Livewire::test(Index::class)
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            $names = $customers->pluck('name')->toArray();
            expect($names)->toBe(['Abelha', 'Baleia', 'Zebra']);
        });
});

it('can search customers by name', function () {
    Customer::factory()->create(['name' => 'John Doe']);
    Customer::factory()->create(['name' => 'Jane Smith']);

    Livewire::test(Index::class)
        ->set('search', 'John')
        ->assertViewHas('search', 'John')
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            expect($customers->count())->toBe(1)
                ->and($customers->first()->name)->toBe('John Doe');
        });

    Livewire::test(Index::class)
        ->set('search', 'Jane')
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            expect($customers->count())->toBe(1)
                ->and($customers->first()->name)->toBe('Jane Smith');
        });

    Livewire::test(Index::class)
        ->set('search', 'Nonexistent')
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            expect($customers->count())->toBe(0);
        });
});

it('can search customers by email', function () {
    Customer::factory()->create(['name' => 'John', 'email' => 'john@example.com']);
    Customer::factory()->create(['name' => 'Jane', 'email' => 'jane@other.com']);

    Livewire::test(Index::class)
        ->set('search', 'john@example.com')
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            expect($customers->count())->toBe(1)
                ->and($customers->first()->name)->toBe('John');
        });
});

it('can search customers by phone', function () {
    Customer::factory()->create(['name' => 'John', 'phone' => '11999999999']);
    Customer::factory()->create(['name' => 'Jane', 'phone' => '11888888888']);

    Livewire::test(Index::class)
        ->set('search', '99999')
        ->instance()
        ->customers
        ->pipe(function ($customers) {
            expect($customers->count())->toBe(1)
                ->and($customers->first()->name)->toBe('John');
        });
});
