<?php

use App\Actions\Product\StockMovementAction;
use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Livewire\BillsPayable\Index as BillsPayableIndex;
use App\Livewire\Customers\Show as CustomersShow;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\RecentActivities\Index as RecentActivitiesIndex;
use App\Livewire\Sales\Index as SalesIndex;
use App\Models\AccountBalance;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    $this->actingAs($this->user);

    AccountBalance::query()->delete();
    AccountBalance::create([
        'current_balance' => 0,
        'target_balance'  => 0,
    ]);
});

function ledgerAmount(TransactionType $type, ?int $referenceId = null): ?int
{
    $query = FinancialTransaction::query()->where('type', $type);

    if ($referenceId !== null) {
        $query->where('reference_id', $referenceId);
    }

    $amount = $query->latest('id')->value('amount');

    return $amount === null ? null : (int) $amount;
}

function balanceCents(): int
{
    return (int) AccountBalance::singleton()->getRawOriginal('current_balance');
}

function ledgerSumCents(): int
{
    return (int) FinancialTransaction::query()->sum('amount');
}

function runAmountFixMigration(): void
{
    $migration = require database_path('migrations/2026_07_26_212602_fix_financial_transactions_amount_double_scale.php');
    $migration->up();
}

it('stores paid-on-create sale ledger amount equal to net_amount cents and balance', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 7.60,
    ]);

    expect(ledgerAmount(TransactionType::Sale, $sale->id))->toBe(760)
        ->and(balanceCents())->toBe(760)
        ->and(ledgerSumCents())->toBe(760);
});

it('stores mark-as-paid sale ledger amount equal to net_amount cents via sales index', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Pending,
        'net_amount' => 95.00,
    ]);

    expect(balanceCents())->toBe(0)
        ->and(FinancialTransaction::query()->count())->toBe(0);

    Livewire::test(SalesIndex::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect($sale->fresh()->status)->toBe(SaleStatus::Paid)
        ->and(ledgerAmount(TransactionType::Sale, $sale->id))->toBe(9500)
        ->and(balanceCents())->toBe(9500);
});

it('stores cancelled sale ledger as negative cents and reverts balance', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 40.00,
    ]);

    expect(balanceCents())->toBe(4000);

    Livewire::test(SalesIndex::class)
        ->call('confirmCancelSale', $sale->id)
        ->call('cancelSale');

    expect($sale->fresh()->status)->toBe(SaleStatus::Cancelled)
        ->and(ledgerAmount(TransactionType::CancelledSale, $sale->id))->toBe(-4000)
        ->and(balanceCents())->toBe(0)
        ->and(ledgerSumCents())->toBe(0);
});

it('stores gift sale ledger as zero without changing balance', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Pending,
        'net_amount' => 0,
        'is_gift'    => true,
    ]);

    Livewire::test(SalesIndex::class)
        ->call('confirmMarkAsPaid', $sale->id)
        ->call('markAsPaid');

    expect(ledgerAmount(TransactionType::Sale, $sale->id))->toBe(0)
        ->and(balanceCents())->toBe(0);
});

it('stores mark-all-as-paid ledger rows matching each sale net_amount', function () {
    $customer = Customer::factory()->create();

    $sales = Sale::factory()->count(3)->create([
        'customer_id' => $customer->id,
        'status'      => SaleStatus::Pending,
        'net_amount'  => 50.00,
    ]);

    Livewire::test(CustomersShow::class, ['customer' => $customer])
        ->set('confirmedMarkAllAsPaid', true)
        ->call('markAllAsPaid');

    expect(FinancialTransaction::query()->where('type', TransactionType::Sale)->count())->toBe(3)
        ->and(balanceCents())->toBe(15000);

    foreach ($sales as $sale) {
        expect(ledgerAmount(TransactionType::Sale, $sale->id))->toBe(5000);
    }
});

it('stores paid purchase from stock movement in cents matching balance debit', function () {
    $product = Product::factory()->create([
        'stock_quantity' => 0,
        'unit_cost'      => 0,
    ]);

    AccountBalance::singleton()->update(['current_balance' => 1000.00]);

    (new StockMovementAction)->add([
        'product_id'   => $product->id,
        'quantity'     => 2,
        'unit_cost'    => 100.00,
        'sale_price'   => 150.00,
        'payment_date' => now()->toDateString(),
        'due_date'     => now()->toDateString(),
    ]);

    $purchase = ProductPurchase::query()->latest('id')->first();

    expect($purchase)->not->toBeNull()
        ->and(ledgerAmount(TransactionType::Purchase, $purchase->id))->toBe(-20000)
        ->and(balanceCents())->toBe(80000);
});

it('stores bills payable mark-as-paid ledger amount in cents', function () {
    $product = Product::factory()->create();
    $purchase = ProductPurchase::query()->create([
        'product_id' => $product->id,
        'quantity'   => 1,
        'unit_cost'  => 500.00,
        'total_cost' => 500.00,
        'due_date'   => now()->addDays(5)->toDateString(),
        'is_paid'    => false,
    ])->fresh();

    AccountBalance::singleton()->update(['current_balance' => 1000.00]);

    Livewire::test(BillsPayableIndex::class)
        ->call('confirmPayment', $purchase->id)
        ->call('markAsPaid');

    expect(ledgerAmount(TransactionType::Purchase, $purchase->id))->toBe(-50000)
        ->and(balanceCents())->toBe(50000);
});

it('stores cancelled paid purchase ledger amount restoring balance in cents', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);
    $purchase = ProductPurchase::query()->create([
        'product_id'   => $product->id,
        'quantity'     => 5,
        'unit_cost'    => 100.00,
        'total_cost'   => 500.00,
        'payment_date' => now()->toDateString(),
        'due_date'     => now()->toDateString(),
        'is_paid'      => true,
    ])->fresh();

    AccountBalance::singleton()->update(['current_balance' => 500.00]);

    Livewire::test(BillsPayableIndex::class)
        ->call('confirmCancellation', $purchase->id)
        ->call('cancelBill');

    expect(ProductPurchase::query()->find($purchase->id))->toBeNull()
        ->and(ledgerAmount(TransactionType::Purchase))->toBe(50000)
        ->and(balanceCents())->toBe(100000);
});

it('keeps add and remove manual adjustments aligned with balance cents', function () {
    Livewire::test(RecentActivitiesIndex::class)
        ->call('openAdjustmentModal', 'add')
        ->set('amount', '10.00')
        ->set('description', 'Cash top-up')
        ->call('saveAdjustment');

    $added = FinancialTransaction::query()
        ->where('type', TransactionType::ManualAdjustment)
        ->latest('id')
        ->first();

    expect($added)->not->toBeNull()
        ->and($added->amount)->toBe(1000)
        ->and(balanceCents())->toBe(1000);

    Livewire::test(RecentActivitiesIndex::class)
        ->call('openAdjustmentModal', 'remove')
        ->set('amount', '3.50')
        ->set('description', 'Cash out')
        ->call('saveAdjustment');

    $removed = FinancialTransaction::query()
        ->where('type', TransactionType::ManualAdjustment)
        ->latest('id')
        ->first();

    expect($removed)->not->toBeNull()
        ->and($removed->amount)->toBe(-350)
        ->and(balanceCents())->toBe(650)
        ->and(ledgerSumCents())->toBe(650);
});

it('reverts balance using raw cents when cancelling a manual adjustment', function () {
    Livewire::test(RecentActivitiesIndex::class)
        ->call('openAdjustmentModal', 'add')
        ->set('amount', '25.00')
        ->set('description', 'Float')
        ->call('saveAdjustment');

    $transaction = FinancialTransaction::query()
        ->where('type', TransactionType::ManualAdjustment)
        ->latest('id')
        ->first();

    expect(balanceCents())->toBe(2500);

    Livewire::test(RecentActivitiesIndex::class)
        ->call('confirmCancelAdjustment', $transaction->id)
        ->call('cancelAdjustment');

    expect(FinancialTransaction::query()->find($transaction->id))->toBeNull()
        ->and(balanceCents())->toBe(0);
});

it('renders ledger amounts as reais without a 100x inflation on recent activities', function () {
    Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 7.60,
    ]);

    Livewire::test(RecentActivitiesIndex::class)
        ->assertSee('R$ 7,60')
        ->assertDontSee('R$ 760,00');
});

it('renders ledger amounts as reais without a 100x inflation on dashboard', function () {
    Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 12.50,
    ]);

    Livewire::test(DashboardIndex::class)
        ->assertSee('R$ 12,50')
        ->assertDontSee('R$ 1.250,00')
        ->assertDontSee('R$ 1250,00');
});

it('keeps ledger sum equal to balance after a mixed sale purchase and adjustment flow', function () {
    Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 100.00,
    ]);

    $product = Product::factory()->create([
        'stock_quantity' => 0,
        'unit_cost'      => 0,
    ]);

    (new StockMovementAction)->add([
        'product_id'   => $product->id,
        'quantity'     => 1,
        'unit_cost'    => 30.00,
        'sale_price'   => 50.00,
        'payment_date' => now()->toDateString(),
    ]);

    Livewire::test(RecentActivitiesIndex::class)
        ->call('openAdjustmentModal', 'add')
        ->set('amount', '5.00')
        ->set('description', 'Adjustment')
        ->call('saveAdjustment');

    expect(balanceCents())->toBe(7500)
        ->and(ledgerSumCents())->toBe(7500);
});

it('fixes historically double-scaled ledger rows when bug pattern is present', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 10.00,
    ]);

    DB::table('financial_transactions')
        ->where('reference_id', $sale->id)
        ->where('reference_type', Sale::class)
        ->update(['amount' => 100000]);

    DB::table('financial_transactions')->insert([
        'type'             => TransactionType::ManualAdjustment->value,
        'amount'           => 25000,
        'description'      => 'Double-scaled adjustment',
        'transaction_date' => now(),
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    runAmountFixMigration();

    expect(
        (int) DB::table('financial_transactions')
            ->where('reference_id', $sale->id)
            ->where('reference_type', Sale::class)
            ->value('amount'),
    )->toBe(1000)
        ->and((int) DB::table('financial_transactions')->where('description', 'Double-scaled adjustment')->value('amount'))->toBe(250);

    runAmountFixMigration();

    expect(
        (int) DB::table('financial_transactions')
            ->where('reference_id', $sale->id)
            ->where('reference_type', Sale::class)
            ->value('amount'),
    )->toBe(1000);
});

it('does not rescale amounts when ledger already matches sale cents', function () {
    $sale = Sale::factory()->create([
        'status'     => SaleStatus::Paid,
        'net_amount' => 12.50,
    ]);

    expect(
        (int) DB::table('financial_transactions')
            ->where('reference_id', $sale->id)
            ->where('reference_type', Sale::class)
            ->value('amount'),
    )->toBe(1250);

    runAmountFixMigration();

    expect(
        (int) DB::table('financial_transactions')
            ->where('reference_id', $sale->id)
            ->where('reference_type', Sale::class)
            ->value('amount'),
    )->toBe(1250);
});

it('detects double-scaled purchases and fixes all ledger rows', function () {
    $product = Product::factory()->create(['name' => 'Filter']);
    $purchase = ProductPurchase::query()->create([
        'product_id'   => $product->id,
        'quantity'     => 1,
        'unit_cost'    => 20.00,
        'total_cost'   => 20.00,
        'payment_date' => now()->toDateString(),
        'is_paid'      => true,
    ])->fresh();

    FinancialTransaction::query()->create([
        'type'             => TransactionType::Purchase,
        'amount'           => -2000,
        'description'      => "Purchase of {$product->name}",
        'reference_id'     => $purchase->id,
        'reference_type'   => ProductPurchase::class,
        'transaction_date' => now(),
    ]);

    DB::table('financial_transactions')
        ->where('reference_id', $purchase->id)
        ->where('reference_type', ProductPurchase::class)
        ->update(['amount' => -200000]);

    runAmountFixMigration();

    expect(
        (int) DB::table('financial_transactions')
            ->where('reference_id', $purchase->id)
            ->where('reference_type', ProductPurchase::class)
            ->value('amount'),
    )->toBe(-2000);
});
