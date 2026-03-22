<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\TopProducts;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it can load top products for the last 30 days for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    Sale::query()->delete();
    Product::query()->delete();

    $productA = Product::factory()->create(['name' => 'Product A']);
    $productB = Product::factory()->create(['name' => 'Product B']);

    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 1000.0,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
    ]);

    $sale->items()->createMany([
        [
            'product_id' => $productA->id,
            'quantity'   => 5,
            'unit_price' => 100.0,
            'unit_cost'  => 50.0,
            'subtotal'   => 500.0,
        ],
        [
            'product_id' => $productB->id,
            'quantity'   => 10,
            'unit_price' => 50.0,
            'unit_cost'  => 25.0,
            'subtotal'   => 500.0,
        ],
    ]);

    Livewire::test(TopProducts::class)
        ->assertSet('period', 'last_30_days')
        ->assertSet('chartDataArray.labels', ['Product B', 'Product A'])
        ->assertSet('chartDataArray.quantity', [10, 5])
        ->assertSet('chartDataArray.revenue', [500.0, 500.0]);
});

test('it filters top products by period for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    Sale::query()->delete();
    Product::query()->delete();

    $product = Product::factory()->create(['name' => 'Exclusive Today']);

    $saleToday = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 200.0,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
    ]);
    $saleToday->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 100.0,
        'unit_cost'  => 50.0,
        'subtotal'   => 200.0,
    ]);

    // Sale 10 days ago
    $saleOld = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 500.0,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => Carbon::now()->subDays(10),
    ]);
    $saleOld->items()->create([
        'product_id' => $product->id,
        'quantity'   => 5,
        'unit_price' => 100.0,
        'unit_cost'  => 50.0,
        'subtotal'   => 500.0,
    ]);

    Livewire::test(TopProducts::class)
        ->set('period', 'today')
        ->assertSet('chartDataArray.quantity', [2])
        ->set('period', 'last_30_days')
        ->assertSet('chartDataArray.quantity', [7]); // 2 + 5
});

test('it denies access to top products for unauthorized user', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(TopProducts::class)
        ->assertForbidden();
});
