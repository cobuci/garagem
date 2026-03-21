<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\TopProducts;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('it can load top products for the last 30 days', function () {
    $user = User::factory()->create();
    actingAs($user);

    Sale::query()->delete();
    Product::query()->delete();

    $productA = Product::factory()->create(['name' => 'Product A']);
    $productB = Product::factory()->create(['name' => 'Product B']);

    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 100000,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
    ]);

    $sale->items()->createMany([
        [
            'product_id' => $productA->id,
            'quantity'   => 5,
            'unit_price' => 10000,
            'unit_cost'  => 5000,
            'subtotal'   => 50000,
        ],
        [
            'product_id' => $productB->id,
            'quantity'   => 10,
            'unit_price' => 5000,
            'unit_cost'  => 2500,
            'subtotal'   => 50000,
        ],
    ]);

    Livewire::test(TopProducts::class)
        ->assertSet('period', 'last_30_days')
        ->assertSet('chartData.labels', ['Product B', 'Product A'])
        ->assertSet('chartData.quantity', [10, 5])
        ->assertSet('chartData.revenue', [50000.0, 50000.0]);
});

test('it filters top products by period', function () {
    $user = User::factory()->create();
    actingAs($user);

    Sale::query()->delete();
    Product::query()->delete();

    $product = Product::factory()->create(['name' => 'Exclusive Today']);

    $saleToday = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 20000,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
    ]);
    $saleToday->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 10000,
        'unit_cost'  => 5000,
        'subtotal'   => 20000,
    ]);

    // Sale 10 days ago
    $saleOld = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 50000,
        'discount_amount' => 0,
        'payment_method'  => 'cash',
        'created_at'      => Carbon::now()->subDays(10),
    ]);
    $saleOld->items()->create([
        'product_id' => $product->id,
        'quantity'   => 5,
        'unit_price' => 10000,
        'unit_cost'  => 5000,
        'subtotal'   => 50000,
    ]);

    Livewire::test(TopProducts::class)
        ->set('period', 'today')
        ->assertSet('chartData.quantity', [2])
        ->set('period', 'last_30_days')
        ->assertSet('chartData.quantity', [7]); // 2 + 5
});
