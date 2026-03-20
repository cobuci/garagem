<?php

use App\Enums\SaleStatus;
use App\Livewire\Sales\Index;
use App\Models\AccountBalance;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    AccountBalance::query()->delete();
    AccountBalance::create([
        'current_balance' => 0,
        'target_balance'  => 0,
    ]);
});

test('can filter sales by status', function () {
    $user = User::factory()->create();
    Sale::factory()->create(['status' => SaleStatus::Paid]);
    Sale::factory()->create(['status' => SaleStatus::Pending]);

    actingAs($user);

    Livewire::test(Index::class)
        ->assertSet('status', 'pending')
        ->call('filterByStatus', 'paid')
        ->assertSet('status', 'paid')
        ->assertSet('sales', function ($sales) {
            return $sales->count() === 1 && $sales->first()->status === SaleStatus::Paid;
        })
        ->call('filterByStatus', 'pending')
        ->assertSet('status', 'pending')
        ->assertSet('sales', function ($sales) {
            return $sales->count() === 1 && $sales->first()->status === SaleStatus::Pending;
        });
});

test('can show sale details', function () {
    $user = User::factory()->create();
    $sale = Sale::factory()->create();

    actingAs($user);

    Livewire::test(Index::class)
        ->call('showDetails', $sale->id)
        ->assertSet('selectedSaleId', $sale->id)
        ->assertSet('showDetailsModal', true)
        ->assertSet('selectedSale.id', $sale->id);
});

test('cannot mark an already paid sale as paid', function () {
    $user = User::factory()->create();
    $sale = Sale::factory()->create([
        'status' => SaleStatus::Paid,
    ]);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid')
        ->assertHasNoErrors()
        ->assertNotSet('showConfirmPaymentModal', false);

    expect($sale->fresh()->status)->toBe(SaleStatus::Paid);
});

test('cannot cancel an already cancelled sale', function () {
    $user = User::factory()->create();
    $sale = Sale::factory()->create([
        'status' => SaleStatus::Cancelled,
    ]);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale')
        ->assertHasNoErrors();

    expect($sale->fresh()->status)->toBe(SaleStatus::Cancelled);
});

test('can mark a pending sale as paid and update balance', function () {
    $user = User::factory()->create();
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Pending,
        'net_amount' => 100.00,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->assertSet('selectedSaleId', $sale->id)
        ->assertSet('showConfirmPaymentModal', true)
        ->call('markAsPaid')
        ->assertHasNoErrors()
        ->assertSet('showConfirmPaymentModal', false);

    $sale->refresh();
    expect($sale->status)->toBe(SaleStatus::Paid)
        ->and(AccountBalance::singleton()->current_balance)->toEqual(100.0);
});

test('mark as paid correctly adds net_amount to balance (considering fees)', function () {
    $user = User::factory()->create();

    $sale = Sale::factory()->create([
        'status'               => SaleStatus::Pending,
        'total_amount'         => 100.00,
        'fee_amount'           => 5.00,
        'pass_fee_to_customer' => false,
        'net_amount'           => 95.00,
        'is_gift'              => false,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect(AccountBalance::singleton()->current_balance)->toEqual(95.00);
});

test('mark as paid with gift sale adds zero to balance', function () {
    $user = User::factory()->create();

    $sale = Sale::factory()->create([
        'status'       => SaleStatus::Pending,
        'total_amount' => 0,
        'net_amount'   => 0,
        'is_gift'      => true,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);
});

test('displays total pending amount', function () {
    $user = User::factory()->create();
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 50.00]);
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 30.00]);
    Sale::factory()->create(['status' => SaleStatus::Paid, 'net_amount' => 100.00]);

    actingAs($user);

    Livewire::test(Index::class)
        ->assertSet('totalPendingAmount', 80.00);
});

test('calculates sale profit correctly', function () {
    $sale = Sale::create([
        'total_amount'    => 100.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'net_amount'      => 100.00,
        'is_gift'         => false,
        'status'          => SaleStatus::Pending,
        'payment_method'  => 'cash',
    ]);

    $sale->items()->create([
        'product_id' => Product::factory()->create()->id,
        'quantity'   => 1,
        'unit_price' => 100.00,
        'unit_cost'  => 60.00,
        'subtotal'   => 100.00,
    ]);

    expect($sale->fresh()->profit())->toEqual(40.00);
});

test('calculates profit for gift sale as negative cost', function () {
    $sale = Sale::create([
        'total_amount'    => 0,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'net_amount'      => 0,
        'is_gift'         => true,
        'status'          => SaleStatus::Pending,
        'payment_method'  => 'cash',
    ]);

    $sale->items()->create([
        'product_id' => Product::factory()->create()->id,
        'quantity'   => 1,
        'unit_price' => 50.00,
        'unit_cost'  => 30.00,
        'subtotal'   => 50.00,
    ]);

    expect($sale->fresh()->profit())->toEqual(-30.00);
});

test('profit calculation considers fees absorbed by company', function () {
    $sale = Sale::create([
        'total_amount'         => 100.00,
        'fee_amount'           => 5.00,
        'pass_fee_to_customer' => false,
        'net_amount'           => 95.00,
        'is_gift'              => false,
        'status'               => SaleStatus::Pending,
        'payment_method'       => 'credit_card',
    ]);

    $sale->items()->create([
        'product_id' => Product::factory()->create()->id,
        'quantity'   => 1,
        'unit_price' => 100.00,
        'unit_cost'  => 60.00,
        'subtotal'   => 100.00,
    ]);

    expect($sale->fresh()->profit())->toEqual(40.00);
});

test('profit calculation considers fees passed to customer', function () {
    $sale = Sale::create([
        'total_amount'         => 105.00,
        'fee_amount'           => 5.00,
        'pass_fee_to_customer' => true,
        'net_amount'           => 105.00,
        'is_gift'              => false,
        'status'               => SaleStatus::Pending,
        'payment_method'       => 'credit_card',
    ]);

    $sale->items()->create([
        'product_id' => Product::factory()->create()->id,
        'quantity'   => 1,
        'unit_price' => 100.00,
        'unit_cost'  => 60.00,
        'subtotal'   => 100.00,
    ]);

    expect($sale->fresh()->profit())->toEqual(45.00);
});

test('can cancel a pending sale and restore stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $sale = Sale::create([
        'status'         => SaleStatus::Pending,
        'total_amount'   => 100.00,
        'net_amount'     => 100.00,
        'payment_method' => 'cash',
        'is_gift'        => false,
    ]);

    $sale->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 50.00,
        'unit_cost'  => 30.00,
        'subtotal'   => 100.00,
    ]);

    $product->decrement('stock_quantity', 2);
    expect($product->fresh()->stock_quantity)->toBe(8);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmCancelSale', $sale->id)
        ->assertSet('selectedSaleId', $sale->id)
        ->assertSet('showConfirmCancelModal', true)
        ->call('cancelSale')
        ->assertHasNoErrors()
        ->assertSet('showConfirmCancelModal', false);

    $sale->refresh();
    expect($sale->status)->toBe(SaleStatus::Cancelled);
    expect($product->fresh()->stock_quantity)->toBe(10);
});

test('can cancel a paid sale, restore stock and decrement balance', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $sale = Sale::create([
        'status'         => SaleStatus::Paid,
        'net_amount'     => 100.00,
        'total_amount'   => 100.00,
        'payment_method' => 'cash',
    ]);

    $sale->items()->create([
        'product_id' => $product->id,
        'quantity'   => 2,
        'unit_price' => 50.00,
        'unit_cost'  => 30.00,
        'subtotal'   => 100.00,
    ]);

    $product->decrement('stock_quantity', 2);

    expect(AccountBalance::singleton()->current_balance)->toEqual(100.00)
        ->and($product->fresh()->stock_quantity)->toBe(8);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale');

    $sale->refresh();
    expect($sale->status)->toBe(SaleStatus::Cancelled)
        ->and($product->fresh()->stock_quantity)->toBe(10)
        ->and(AccountBalance::singleton()->current_balance)->toEqual(0.00);
});
