<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\SalesByPeriod;
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

test('it can load sales data for the last 30 days for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    $product = Product::factory()->create();

    Sale::query()->delete();

    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => now(),
    ]);

    $sale->items()->delete();

    $sale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 100, // 100.00
        'unit_cost'  => 60, // 60.00
        'quantity'   => 1,
        'unit_price' => 100,
    ]);

    Livewire::test(SalesByPeriod::class)
        ->assertSet('period', 'last_30_days')
        ->assertSee('100') // Total Sales (100.00)
        ->assertSee('40'); // Total Profit (100.00 - 60.00 = 40.00)
});

test('it can change the period and update data for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    $product = Product::factory()->create();

    Sale::query()->delete();

    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'created_at' => Carbon::now()->subDays(10)->startOfDay(),
    ]);

    $sale->items()->delete();

    $sale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 50.0,
        'unit_cost'  => 30.0,
        'quantity'   => 1,
        'unit_price' => 50.0,
    ]);

    Livewire::test(SalesByPeriod::class)
        ->set('period', 'today')
        ->assertSet('chartDataArray.sales', [])
        ->set('period', 'last_30_days')
        ->assertSet('chartDataArray.sales', [50.0]);
});

test('it denies access to sales by period for unauthorized user', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(SalesByPeriod::class)
        ->assertForbidden();
});
