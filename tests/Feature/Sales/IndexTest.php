<?php

use App\Enums\Permission;
use App\Enums\SaleStatus;
use App\Jobs\GenerateInvoiceJob;
use App\Livewire\Sales\Index;
use App\Models\AccountBalance;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo([
        Permission::ViewSale->value,
        Permission::EditSale->value,
    ]);

    AccountBalance::query()->delete();
    AccountBalance::create([
        'current_balance' => 0,
        'target_balance'  => 0,
    ]);
});

test('it returns 403 when viewing sales without permission', function () {
    $userWithoutPermission = User::factory()->create();

    actingAs($userWithoutPermission)
        ->get(route('sales.index'))
        ->assertForbidden();

    Livewire::actingAs($userWithoutPermission)
        ->test(Index::class)
        ->assertForbidden();
});

test('can filter sales by status', function () {
    Sale::factory()->create(['status' => SaleStatus::Paid]);
    Sale::factory()->create(['status' => SaleStatus::Pending]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
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
    $sale = Sale::factory()->create(['status' => SaleStatus::Pending]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showDetails', $sale->id)
        ->assertSet('selectedSaleId', $sale->id)
        ->assertSet('showDetailsModal', true)
        ->assertSet('selectedSale.id', $sale->id);
});

test('it returns 403 when marking as paid without permission', function () {
    $userWithoutPermission = User::factory()->create();
    $userWithoutPermission->givePermissionTo(Permission::ViewSale->value);
    $sale = Sale::factory()->create(['status' => SaleStatus::Pending]);

    Livewire::actingAs($userWithoutPermission)
        ->test(Index::class)
        ->call('markAsPaid')
        ->assertForbidden();
});

test('it returns 403 when cancelling sale without permission', function () {
    $userWithoutPermission = User::factory()->create();
    $userWithoutPermission->givePermissionTo(Permission::ViewSale->value);
    $sale = Sale::factory()->create(['status' => SaleStatus::Pending]);

    Livewire::actingAs($userWithoutPermission)
        ->test(Index::class)
        ->call('cancelSale')
        ->assertForbidden();
});

test('cannot mark an already paid sale as paid', function () {
    $sale = Sale::factory()->create([
        'status' => SaleStatus::Paid,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid')
        ->assertHasNoErrors()
        ->assertNotSet('showConfirmPaymentModal', false);

    expect($sale->fresh()->status)->toBe(SaleStatus::Paid);
});

test('cannot cancel an already cancelled sale', function () {
    $sale = Sale::factory()->create([
        'status' => SaleStatus::Cancelled,
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale')
        ->assertHasNoErrors();

    expect($sale->fresh()->status)->toBe(SaleStatus::Cancelled);
});

test('can mark a pending sale as paid and update balance', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Pending,
        'net_amount' => 100.00,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    Livewire::actingAs($this->user)
        ->test(Index::class)
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
    $sale = Sale::factory()->create([
        'status'               => SaleStatus::Pending,
        'total_amount'         => 100.00,
        'fee_amount'           => 5.00,
        'pass_fee_to_customer' => false,
        'net_amount'           => 95.00,
        'is_gift'              => false,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect(AccountBalance::singleton()->current_balance)->toEqual(95.00);
});

test('mark as paid with gift sale adds zero to balance', function () {
    $sale = Sale::factory()->create([
        'status'       => SaleStatus::Pending,
        'total_amount' => 0,
        'net_amount'   => 0,
        'is_gift'      => true,
    ]);

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect(AccountBalance::singleton()->current_balance)->toEqual(0);
});

test('displays total pending amount', function () {
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 50.00]);
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 30.00]);
    Sale::factory()->create(['status' => SaleStatus::Paid, 'net_amount' => 100.00]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
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

    Livewire::actingAs($this->user)
        ->test(Index::class)
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

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale');

    $sale->refresh();
    expect($sale->status)->toBe(SaleStatus::Cancelled)
        ->and($product->fresh()->stock_quantity)->toBe(10)
        ->and(AccountBalance::singleton()->current_balance)->toEqual(0.00);
});

test('can dispatch invoice generation job', function () {
    Queue::fake();
    $sale = Sale::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('downloadInvoice', $sale->id);

    Queue::assertPushed(GenerateInvoiceJob::class, function ($job) use ($sale) {
        return $job->sale->id === $sale->id;
    });

    expect($sale->fresh()->invoice_status)->toBe('generating');
});

test('can handle invoice generation failure', function () {
    $sale = Sale::factory()->create(['invoice_status' => 'failed']);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showDetails', $sale->id)
        ->assertSee(__('sales.invoice_failed_retry'));
});

test('can download invoice when it is ready', function () {
    Storage::fake();
    $sale = Sale::factory()->create([
        'invoice_status' => 'ready',
        'invoice_path'   => 'invoices/ready.pdf',
    ]);

    Storage::put('invoices/ready.pdf', 'dummy content');

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('downloadInvoice', $sale->id)
        ->assertFileDownloaded("{$sale->id}.pdf");
});

test('can download invoice png when it is ready', function () {
    Storage::fake();
    $sale = Sale::factory()->create([
        'invoice_status'   => 'ready',
        'invoice_path'     => 'invoices/ready.pdf',
        'invoice_png_path' => 'invoices/ready.png',
    ]);

    Storage::put('invoices/ready.png', 'dummy png content');

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('downloadInvoicePng', $sale->id)
        ->assertFileDownloaded("{$sale->id}.png");
});

test('invoice download button defaults to png', function () {
    $sale = Sale::factory()->create([
        'invoice_status'   => 'ready',
        'invoice_path'     => 'invoices/ready.pdf',
        'invoice_png_path' => 'invoices/ready.png',
    ]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('showDetails', $sale->id)
        ->assertSeeHtml('wire:click="downloadInvoicePng(' . $sale->id . ')"');
});

test('dispatches invoice job when png requested but invoice not yet generated', function () {
    Queue::fake();
    $sale = Sale::factory()->create();

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('downloadInvoicePng', $sale->id);

    Queue::assertPushed(GenerateInvoiceJob::class, function ($job) use ($sale) {
        return $job->sale->id === $sale->id;
    });

    expect($sale->fresh()->invoice_status)->toBe('generating');
});

test('can filter sales by cancelled and all statuses', function () {
    Sale::factory()->create(['status' => SaleStatus::Paid]);
    Sale::factory()->create(['status' => SaleStatus::Pending]);
    Sale::factory()->create(['status' => SaleStatus::Cancelled]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('filterByStatus', 'cancelled')
        ->assertSet('status', 'cancelled')
        ->assertSet('sales', function ($sales) {
            return $sales->count() === 1 && $sales->first()->status === SaleStatus::Cancelled;
        })
        ->call('filterByStatus', null)
        ->assertSet('status', null)
        ->assertSet('sales', function ($sales) {
            return $sales->count() === 3;
        });
});

test('can search sales by customer name and id', function () {
    $customerA = Customer::factory()->create(['name' => 'Alpha Customer']);
    $customerB = Customer::factory()->create(['name' => 'Beta Customer']);

    $sale1 = Sale::factory()->create(['customer_id' => $customerA->id, 'status' => SaleStatus::Pending]);
    $sale2 = Sale::factory()->create(['customer_id' => $customerB->id, 'status' => SaleStatus::Pending]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->set('search', 'Alpha')
        ->assertSet('sales', function ($sales) use ($sale1) {
            return $sales->count() === 1 && $sales->first()->id === $sale1->id;
        })
        ->set('search', (string) $sale2->id)
        ->assertSet('sales', function ($sales) use ($sale2) {
            return $sales->count() === 1 && $sales->first()->id === $sale2->id;
        });
});

test('computes status counts and totals accurately', function () {
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 50.00]);
    Sale::factory()->create(['status' => SaleStatus::Pending, 'net_amount' => 30.00]);
    Sale::factory()->create(['status' => SaleStatus::Paid, 'net_amount' => 120.00]);
    Sale::factory()->create(['status' => SaleStatus::Cancelled, 'net_amount' => 80.00]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSet('pendingCount', 2)
        ->assertSet('paidCount', 1)
        ->assertSet('cancelledCount', 1)
        ->assertSet('allCount', 4)
        ->assertSet('totalPendingAmount', 80.00)
        ->assertSet('totalPaidAmount', 120.00)
        ->assertSet('totalAllAmount', 200.00);
});
