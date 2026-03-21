<?php

namespace Tests\Feature\Dashboard;

use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Livewire\Dashboard\Index;
use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAsAdmin();
});

it('can render dashboard index page', function () {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

it('displays the correct total balance', function () {
    AccountBalance::singleton()->update(['current_balance' => 1500.50]);

    Livewire::test(Index::class)
        ->assertSet('totalBalance', 1500.50);
});

it('calculates daily metrics correctly', function () {
    $product = Product::factory()->create([
        'unit_cost'  => 10.00,
        'sale_price' => 20.00,
    ]);

    $saleToday = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now(),
        'total_amount'   => 40.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $saleToday->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 20.00,
        'unit_cost'  => 10.00,
        'subtotal'   => 40.00,
    ]);

    $saleYesterday = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now()->subDay(),
        'total_amount'   => 20.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $saleYesterday->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 20.00,
        'unit_cost'  => 10.00,
        'subtotal'   => 20.00,
    ]);

    Livewire::test(Index::class)
        ->assertSet('dailyMetrics.sales', 40.00)
        ->assertSet('dailyMetrics.previous_sales', 20.00)
        ->assertSet('dailyMetrics.profit', 20.00)
        ->assertSet('dailyMetrics.previous_profit', 10.00)
        ->assertSet('dailyMetrics.percent', 100.0);
});

it('does not count unpaid sales in daily metrics', function () {
    $product = Product::factory()->create([
        'unit_cost'  => 10.00,
        'sale_price' => 20.00,
    ]);

    $salePending = Sale::query()->create([
        'status'         => SaleStatus::Pending,
        'created_at'     => now(),
        'total_amount'   => 40.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $salePending->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 20.00,
        'unit_cost'  => 10.00,
        'subtotal'   => 40.00,
    ]);

    Livewire::test(Index::class)
        ->assertSet('dailyMetrics.sales', 0.0)
        ->assertSet('dailyMetrics.profit', 0.0);
});

it('calculates monthly metrics correctly', function () {
    $product = Product::factory()->create([
        'unit_cost'  => 50.00,
        'sale_price' => 100.00,
    ]);

    $saleThisMonth = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now()->startOfMonth(),
        'total_amount'   => 500.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $saleThisMonth->items()->create([
        'product_id' => $product->id,
        'quantity'   => 5,
        'unit_price' => 100.00,
        'unit_cost'  => 50.00,
        'subtotal'   => 500.00,
    ]);

    $saleLastMonth = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now()->subMonth()->startOfMonth(),
        'total_amount'   => 200.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $saleLastMonth->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 100.00,
        'unit_cost'  => 50.00,
        'subtotal'   => 200.00,
    ]);

    Livewire::test(Index::class)
        ->assertSet('monthlyMetrics.sales', 500.00)
        ->assertSet('monthlyMetrics.previous_sales', 200.00)
        ->assertSet('monthlyMetrics.profit', 250.00)
        ->assertSet('monthlyMetrics.previous_profit', 100.00)
        ->assertSet('monthlyMetrics.percent', 150.0);
});

it('provides correct chart data for the last 6 months', function () {
    $product = Product::factory()->create(['unit_cost' => 10, 'sale_price' => 20]);

    for ($i = 0; $i < 6; $i++) {
        $date = now()->subMonths($i);
        $sale = Sale::query()->create([
            'status'         => SaleStatus::Paid,
            'created_at'     => $date,
            'total_amount'   => 20,
            'payment_method' => 'cash',
            'is_gift'        => false,
        ]);
        $sale->items()->create([
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 20,
            'unit_cost'  => 10,
            'subtotal'   => 20,
        ]);
    }

    $component = Livewire::test(Index::class);
    $chartData = $component->get('chartData');

    expect($chartData['labels'])->toHaveCount(6)
        ->and($chartData['sales'])->toHaveCount(6)
        ->and($chartData['profit'])->toHaveCount(6)
        ->and($chartData['sales'][5])->toEqual(20.0);
});

it('calculates goal metrics correctly', function () {
    AccountBalance::singleton()->update(['target_balance' => 1000.00]);

    $product = Product::factory()->create(['unit_cost' => 0, 'sale_price' => 100]);
    $sale = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now(),
        'total_amount'   => 400,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $sale->items()->create([
        'product_id' => $product->id,
        'quantity'   => 4,
        'unit_price' => 100,
        'unit_cost'  => 0,
        'subtotal'   => 400,
    ]);

    Livewire::test(Index::class)
        ->assertSet('goalMetrics.target', 1000.00)
        ->assertSet('goalMetrics.percent', 40.0)
        ->assertSet('goalMetrics.remaining', 600.0)
        ->assertSet('goalMetrics.reached', false);
});

it('marks goal as reached when sales exceed target', function () {
    AccountBalance::singleton()->update(['target_balance' => 500.00]);

    $product = Product::factory()->create(['unit_cost' => 0, 'sale_price' => 100]);
    $sale = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now(),
        'total_amount'   => 600,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $sale->items()->create([
        'product_id' => $product->id,
        'quantity'   => 6,
        'unit_price' => 100,
        'unit_cost'  => 0,
        'subtotal'   => 600,
    ]);

    Livewire::test(Index::class)
        ->assertSet('goalMetrics.percent', 100.0)
        ->assertSet('goalMetrics.reached', true);
});

it('can update target balance', function () {
    Livewire::test(Index::class)
        ->set('targetBalance', 2500.00)
        ->call('updateTargetBalance');

    expect(AccountBalance::singleton()->target_balance)->toEqual(2500.00);
});

it('lists the 5 most recent activities', function () {
    FinancialTransaction::query()->create([
        'type'             => TransactionType::Sale,
        'amount'           => 1000,
        'description'      => 'Test Activity',
        'transaction_date' => now(),
    ]);

    for ($i = 0; $i < 9; $i++) {
        FinancialTransaction::query()->create([
            'type'             => TransactionType::ManualAdjustment,
            'amount'           => 100,
            'description'      => "Adjustment $i",
            'transaction_date' => now()->subMinutes($i + 1),
        ]);
    }

    $component = Livewire::test(Index::class);
    $recentActivities = $component->get('recentActivities');

    expect($recentActivities)->toHaveCount(5);
});

it('handles zero sales gracefully in percentage calculations', function () {
    Livewire::test(Index::class)
        ->assertSet('dailyMetrics.percent', 0.0)
        ->assertSet('monthlyMetrics.percent', 0.0);
});

it('shows 100% growth when previous period had zero sales', function () {
    $product = Product::factory()->create(['unit_cost' => 10, 'sale_price' => 20]);
    $sale = Sale::query()->create([
        'status'         => SaleStatus::Paid,
        'created_at'     => now(),
        'total_amount'   => 20,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);
    $sale->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 20,
        'unit_cost'  => 10,
        'subtotal'   => 20,
    ]);

    Livewire::test(Index::class)
        ->assertSet('dailyMetrics.percent', 100.0);
});
