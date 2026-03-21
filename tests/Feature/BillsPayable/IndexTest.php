<?php

use App\Actions\Product\StockMovementAction;
use App\Enums\Permission as PermissionEnum;
use App\Livewire\BillsPayable\Index;
use App\Models\AccountBalance;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');

    AccountBalance::query()->delete();
    $this->balance = AccountBalance::create([
        'current_balance' => 1000.00,
        'target_balance'  => 0,
    ]);
});

it('renders the bills payable page for authorized user', function () {
    $this->actingAs($this->user);

    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee(__('bills_payable.title'));
});

it('denies access to bills payable page for unauthorized user', function () {
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser);

    Livewire::test(Index::class)
        ->assertStatus(403);
});

it('creates a pending purchase when payment date is null', function () {
    $product = Product::factory()->create();
    $action = new StockMovementAction;

    $action->add([
        'product_id'   => $product->id,
        'quantity'     => 2,
        'unit_cost'    => 100.00,
        'sale_price'   => 150.00,
        'payment_date' => null,
        'due_date'     => now()->addDays(10)->toDateString(),
    ]);

    $purchase = ProductPurchase::first();
    expect($purchase->is_paid)->toBeFalse();

    $this->balance->refresh();
    expect((float) $this->balance->current_balance)->toBe(1000.00);
});

it('creates a paid purchase when payment date is provided and decrements balance', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 0,
        'unit_cost'      => 0,
    ]);
    $action = new StockMovementAction;

    $action->add([
        'product_id'   => $product->id,
        'quantity'     => 2,
        'unit_cost'    => 100.00,
        'sale_price'   => 150.00,
        'payment_date' => now()->toDateString(),
        'due_date'     => now()->toDateString(),
    ]);

    $purchase = ProductPurchase::latest('id')->first();
    expect($purchase->is_paid)->toBeTruthy();

    $this->balance->refresh();
    expect($this->balance->getRawOriginal('current_balance'))->toBe(80000);
});

it('can mark a pending bill as paid for authorized user', function () {
    $this->actingAs($this->user);
    $product = Product::factory()->create();
    $purchase = ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 500.00,
        'total_cost' => 500.00,
        'due_date'   => now()->addDays(5)->toDateString(),
        'is_paid'    => false,
    ]);

    Livewire::test(Index::class)
        ->call('confirmPayment', $purchase->id)
        ->assertSet('selectedBill.id', $purchase->id)
        ->call('markAsPaid')
        ->assertHasNoErrors()
        ->assertSet('selectedBill', null);

    $purchase->refresh();
    expect($purchase->is_paid)->toBeTruthy()
        ->and($purchase->payment_date)->not->toBeNull();

    $this->balance->refresh();
    expect($this->balance->getRawOriginal('current_balance'))->toBe(50000);
});

it('denies marking a bill as paid for unauthorized user', function () {
    $product = Product::factory()->create();
    $purchase = ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 500.00,
        'total_cost' => 500.00,
        'due_date'   => now()->addDays(5)->toDateString(),
        'is_paid'    => false,
    ]);

    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser->givePermissionTo(PermissionEnum::ViewProductPurchase->value);
    $this->actingAs($unauthorizedUser);

    Livewire::test(Index::class)
        ->call('confirmPayment', $purchase->id)
        ->call('markAsPaid')
        ->assertStatus(403);
});

it('shows correct summary values', function () {
    $this->actingAs($this->user);
    $product = Product::factory()->create();

    ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 100.00,
        'total_cost' => 100.00,
        'due_date'   => now()->subDays(5)->toDateString(),
        'is_paid'    => false,
    ]);

    ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 200.00,
        'total_cost' => 200.00,
        'due_date'   => now()->addMonth()->toDateString(),
        'is_paid'    => false,
    ]);

    Livewire::test(Index::class)
        ->assertSet('summary.total_due', 300.00)
        ->assertSet('summary.overdue', 100.00)
        ->assertSet('summary.next_month', 200.00);
});

it('can cancel a pending bill for authorized user', function () {
    $this->actingAs($this->user);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $purchase = ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 5,
        'unit_cost'  => 100.00,
        'total_cost' => 500.00,
        'due_date'   => now()->addDays(5)->toDateString(),
        'is_paid'    => false,
    ]);

    Livewire::test(Index::class)
        ->call('confirmCancellation', $purchase->id)
        ->assertSet('selectedBill.id', $purchase->id)
        ->call('cancelBill')
        ->assertHasNoErrors()
        ->assertSet('selectedBill', null);

    expect(ProductPurchase::find($purchase->id))->toBeNull();
    $product->refresh();
    expect($product->stock_quantity)->toBe(5);

    $this->balance->refresh();
    expect($this->balance->getRawOriginal('current_balance'))->toBe(100000); // 1000.00 unchanged
});

it('denies cancelling a bill for unauthorized user', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $purchase = ProductPurchase::create([
        'product_id' => $product->id,
        'quantity'   => 5,
        'unit_cost'  => 100.00,
        'total_cost' => 500.00,
        'due_date'   => now()->addDays(5)->toDateString(),
        'is_paid'    => false,
    ]);

    $unauthorizedUser = User::factory()->create();
    $unauthorizedUser->givePermissionTo(PermissionEnum::ViewProductPurchase->value);
    $this->actingAs($unauthorizedUser);

    Livewire::test(Index::class)
        ->call('confirmCancellation', $purchase->id)
        ->call('cancelBill')
        ->assertStatus(403);
});

it('can cancel a paid bill, decrement stock and restore balance', function () {
    $this->actingAs($this->user);
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $purchase = ProductPurchase::create([
        'product_id'   => $product->id,
        'quantity'     => 5,
        'unit_cost'    => 100.00,
        'total_cost'   => 500.00,
        'payment_date' => now()->toDateString(),
        'due_date'     => now()->toDateString(),
        'is_paid'      => true,
    ]);

    Livewire::test(Index::class)
        ->call('confirmCancellation', $purchase->id)
        ->call('cancelBill')
        ->assertHasNoErrors();

    expect(ProductPurchase::find($purchase->id))->toBeNull();
    $product->refresh();
    expect($product->stock_quantity)->toBe(5);

    $this->balance->refresh();
    expect($this->balance->getRawOriginal('current_balance'))->toBe(150000); // 1000.00 + 500.00
});
