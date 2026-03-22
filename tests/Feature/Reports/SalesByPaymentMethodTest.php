<?php

namespace Tests\Feature\Reports;

use App\Enums\SaleStatus;
use App\Livewire\Reports\SalesByPaymentMethod;
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

test('it can load sales by payment method for the last 30 days for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    Sale::query()->delete();

    $product = Product::factory()->create();

    $sale1 = Sale::factory()->create([
        'status'          => SaleStatus::Paid,
        'payment_method'  => 'pix',
        'discount_amount' => 0,
        'created_at'      => now(),
    ]);
    $sale1->items()->delete();
    $sale1->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 15000,
        'unit_cost'  => 10000,
        'subtotal'   => 15000,
    ]);
    $sale1->update(['total_amount' => 15000]);

    $sale2 = Sale::factory()->create([
        'status'          => SaleStatus::Paid,
        'payment_method'  => 'credit_card',
        'discount_amount' => 0,
        'created_at'      => now(),
    ]);
    $sale2->items()->delete();
    $sale2->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 20000,
        'unit_cost'  => 10000,
        'subtotal'   => 20000,
    ]);
    $sale2->update(['total_amount' => 20000]);

    Livewire::test(SalesByPaymentMethod::class)
        ->assertSet('period', 'last_30_days')
        ->assertSet('chartData.series', [200.0, 150.0])
        ->assertSet('chartData.labels', [
            __('reports.payment_methods.credit_card'),
            __('reports.payment_methods.pix'),
        ]);
});

test('it filters by period for payment methods for authorized user', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    Sale::query()->delete();

    $product = Product::factory()->create();

    // Sale today
    $sale1 = Sale::factory()->create([
        'status'          => SaleStatus::Paid,
        'payment_method'  => 'pix',
        'discount_amount' => 0,
        'created_at'      => now(),
    ]);
    $sale1->items()->delete();
    $sale1->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 10000,
        'unit_cost'  => 5000,
        'subtotal'   => 10000,
    ]);
    $sale1->update(['total_amount' => 10000]);

    // Sale 10 days ago
    $sale2 = Sale::factory()->create([
        'status'          => SaleStatus::Paid,
        'payment_method'  => 'cash',
        'discount_amount' => 0,
        'created_at'      => Carbon::now()->subDays(10),
    ]);
    $sale2->items()->delete();
    $sale2->items()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_price' => 5000,
        'unit_cost'  => 2500,
        'subtotal'   => 5000,
    ]);
    $sale2->update(['total_amount' => 5000]);

    Livewire::test(SalesByPaymentMethod::class)
        ->set('period', 'today')
        ->assertSet('chartData.series', [100.0])
        ->set('period', 'last_30_days')
        ->assertSet('chartData.series', [50.0, 100.0]);
});

test('it supports uppercase PIX payment method', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    actingAs($user);

    Sale::query()->delete();

    Sale::factory()->create([
        'status'         => SaleStatus::Paid,
        'payment_method' => 'PIX',
        'total_amount'   => 10000,
        'net_amount'     => 10000,
    ]);

    Livewire::test(SalesByPaymentMethod::class)
        ->assertSee(__('reports.payment_methods.pix'));
});

test('it denies access to sales by payment method for unauthorized user', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(SalesByPaymentMethod::class)
        ->assertForbidden();
});
