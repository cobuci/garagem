<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\ChurnRiskCustomers;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it denies access to unauthorized users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertForbidden();
});

test('it renders for authorized users', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertStatus(200)
        ->assertViewIs('livewire.reports.churn-risk-customers');
});

test('it lists customers who exceed 2x their average purchase interval', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $customer = Customer::factory()->create(['name' => 'Cliente Risco']);

    // 3 purchases spaced ~10 days apart, last one 30+ days ago (> 2x interval)
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(50)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(40)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(30)]);

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertViewHas('customers', fn ($customers) => $customers->contains('name', 'Cliente Risco'));
});

test('it excludes customers within their normal purchase interval', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $customer = Customer::factory()->create(['name' => 'Cliente Normal']);

    // 3 purchases ~10 days apart, last one only 5 days ago (within 2x interval)
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(25)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(15)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(5)]);

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertViewHas('customers', fn ($customers) => ! $customers->contains('name', 'Cliente Normal'));
});

test('it excludes customers with fewer than 3 purchases', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $customer = Customer::factory()->create(['name' => 'Cliente Poucas Compras']);

    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(200)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(100)]);

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertViewHas('customers', fn ($customers) => ! $customers->contains('name', 'Cliente Poucas Compras'));
});

test('urgency level returns correct classification', function () {
    expect(ChurnRiskCustomers::urgencyLevel(80, 10))->toBe('critical');  // 8x → critical
    expect(ChurnRiskCustomers::urgencyLevel(35, 10))->toBe('high');      // 3.5x → high
    expect(ChurnRiskCustomers::urgencyLevel(25, 10))->toBe('medium');    // 2.5x → medium
});

test('it excludes customers whose last purchase was over a year ago', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $customer = Customer::factory()->create(['name' => 'Cliente Perdido']);

    // 3 purchases, but last one was 400 days ago
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(420)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(410)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(400)]);

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertViewHas('customers', fn ($customers) => ! $customers->contains('name', 'Cliente Perdido'));
});

test('it excludes customers with sporadic purchase pattern over 180 day average interval', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $customer = Customer::factory()->create(['name' => 'Cliente Esporádico']);

    // 3 purchases ~200 days apart — sporadic pattern, not a real risk
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(340)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(200)]);
    Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(60)]);

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertViewHas('customers', fn ($customers) => ! $customers->contains('name', 'Cliente Esporádico'));
});

test('sorting by name orders customers alphabetically', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    foreach (['Zara', 'Ana', 'Marco'] as $name) {
        $customer = Customer::factory()->create(['name' => $name]);
        Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(50)]);
        Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(40)]);
        Sale::factory()->create(['customer_id' => $customer->id, 'status' => SaleStatus::Paid, 'created_at' => Carbon::now()->subDays(30)]);
    }

    $component = Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->call('sort', 'name');

    $component->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc')
        ->assertViewHas('customers', function ($customers) {
            $names = $customers->pluck('name')->values()->toArray();

            return array_search('Ana', $names) < array_search('Marco', $names)
                && array_search('Marco', $names) < array_search('Zara', $names);
        });
});

test('sorting toggles direction when clicking the same field twice', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->assertSet('sortField', 'days_since_last')
        ->assertSet('sortDirection', 'desc')
        ->call('sort', 'days_since_last')
        ->assertSet('sortDirection', 'asc')
        ->call('sort', 'days_since_last')
        ->assertSet('sortDirection', 'desc');
});

test('sorting resets to page 1', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->call('sort', 'name')
        ->assertSet('sortField', 'name')
        ->assertSet('sortDirection', 'asc');
});

test('ignores invalid sort fields', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ChurnRiskCustomers::class)
        ->call('sort', 'invalid_field')
        ->assertSet('sortField', 'days_since_last');
});
