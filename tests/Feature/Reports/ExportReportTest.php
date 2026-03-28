<?php

namespace Tests\Feature\Reports;

use App\Actions\Reports\GetSystemReportData;
use App\Enums\SaleStatus;
use App\Jobs\GenerateSystemReportJob;
use App\Livewire\Reports\ExportReport;
use App\Mail\SystemReportMail;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolesAndPermissionsSeeder::class);
});

test('it can request a system report for authorized user', function () {
    Queue::fake();
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ExportReport::class)
        ->set('startDate', '2026-01-01')
        ->set('endDate', '2026-01-31')
        ->call('export')
        ->assertHasNoErrors()
        ->assertDispatched('wireui:notification');

    Queue::assertPushed(GenerateSystemReportJob::class, function ($job) use ($user) {
        return $job->user->id === $user->id
            && $job->startDate === '2026-01-01'
            && $job->endDate === '2026-01-31';
    });
});

test('it denies requesting a system report for unauthorized user', function () {
    Queue::fake();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ExportReport::class)
        ->assertForbidden();
});

test('it can generate the report PDF', function () {
    Mail::fake();
    Storage::fake('public');

    $user = User::factory()->create();
    $sale = Sale::factory()
        ->create([
            'status'         => SaleStatus::Paid,
            'payment_method' => 'pix',
            'total_amount'   => 10000,
            'net_amount'     => 9500,
            'created_at'     => now(),
        ]);

    SaleItem::factory()->count(3)->create([
        'sale_id'    => $sale->id,
        'product_id' => Product::factory(),
        'subtotal'   => 3333,
        'quantity'   => 1,
    ]);

    Carbon::setTestNow($now = now());
    $job = new GenerateSystemReportJob($user, now()->subDay()->format('Y-m-d'), now()->addDay()->format('Y-m-d'));
    $job->handle(new GetSystemReportData);

    Mail::assertSent(SystemReportMail::class);
    Storage::disk('public')->assertExists('reports/system_report_' . $user->id . '_' . $now->timestamp . '.pdf');

    Carbon::setTestNow();
});

test('it validates the date range', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    Livewire::actingAs($user)
        ->test(ExportReport::class)
        ->set('startDate', '2026-01-31')
        ->set('endDate', '2026-01-01')
        ->call('export')
        ->assertHasErrors(['endDate']);
});

test('GetSystemReportData excludes pending sales from total revenue', function () {
    $product = Product::factory()->create(['unit_cost' => 50.00, 'sale_price' => 100.00]);

    $paidSale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 100.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
        'is_gift'         => false,
    ]);
    $paidSale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 100.00,
        'unit_cost'  => 50.00,
        'quantity'   => 1,
        'unit_price' => 100.00,
    ]);

    $pendingSale = Sale::query()->create([
        'status'          => SaleStatus::Pending,
        'total_amount'    => 50.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
        'is_gift'         => false,
    ]);
    $pendingSale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 50.00,
        'unit_cost'  => 25.00,
        'quantity'   => 1,
        'unit_price' => 50.00,
    ]);

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['totalRevenue'])->toBe(10000)
        ->and($data['netSales'])->toBe(5000);
});

test('GetSystemReportData excludes cancelled sales from total revenue', function () {
    $product = Product::factory()->create(['unit_cost' => 50.00, 'sale_price' => 100.00]);

    $paidSale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 80.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
        'is_gift'         => false,
    ]);
    $paidSale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 80.00,
        'unit_cost'  => 40.00,
        'quantity'   => 1,
        'unit_price' => 80.00,
    ]);

    $cancelledSale = Sale::query()->create([
        'status'          => SaleStatus::Cancelled,
        'total_amount'    => 30.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'created_at'      => now(),
        'is_gift'         => false,
    ]);
    $cancelledSale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 30.00,
        'unit_cost'  => 10.00,
        'quantity'   => 1,
        'unit_price' => 30.00,
    ]);

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['totalRevenue'])->toBe(8000);
});

test('GetSystemReportData deducts discounts and fees from net sales', function () {
    $product = Product::factory()->create(['unit_cost' => 50.00, 'sale_price' => 100.00]);

    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 100.00,
        'discount_amount' => 5.00,
        'fee_amount'      => 2.00,
        'payment_method'  => 'pix',
        'created_at'      => now(),
        'is_gift'         => false,
    ]);
    $sale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 100.00,
        'unit_cost'  => 40.00,
        'quantity'   => 1,
        'unit_price' => 100.00,
    ]);

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['totalDiscount'])->toBe(500)
        ->and($data['totalFees'])->toBe(200)
        ->and($data['netSales'])->toBe(5300);
});

test('GetSystemReportData returns zero totals when no paid sales exist in range', function () {
    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['totalRevenue'])->toBe(0)
        ->and($data['totalDiscount'])->toBe(0)
        ->and($data['totalFees'])->toBe(0)
        ->and($data['netSales'])->toBe(0);
});

test('GetSystemReportData excludes sales outside the requested date range', function () {
    $product = Product::factory()->create(['unit_cost' => 10.00, 'sale_price' => 20.00]);

    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 20.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'is_gift'         => false,
        'created_at'      => now()->subMonths(2),
    ]);
    $sale->items()->create([
        'product_id' => $product->id,
        'subtotal'   => 20.00,
        'unit_cost'  => 10.00,
        'quantity'   => 1,
        'unit_price' => 20.00,
    ]);

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['totalRevenue'])->toBe(0);
});

test('GetSystemReportData groups sales by payment method with correct count and amount', function () {
    $product = Product::factory()->create(['unit_cost' => 0, 'sale_price' => 100.00]);

    foreach (['pix', 'pix', 'cash'] as $method) {
        $sale = Sale::query()->create([
            'status'          => SaleStatus::Paid,
            'total_amount'    => 100.00,
            'discount_amount' => 0,
            'fee_amount'      => 0,
            'payment_method'  => $method,
            'is_gift'         => false,
            'created_at'      => now(),
        ]);
        $sale->items()->create([
            'product_id' => $product->id,
            'subtotal'   => 100.00,
            'unit_cost'  => 0,
            'quantity'   => 1,
            'unit_price' => 100.00,
        ]);
    }

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    $byMethod = $data['salesByPaymentMethod'];

    expect($byMethod->has('pix'))->toBeTrue()
        ->and($byMethod['pix']['count'])->toBe(2)
        ->and($byMethod['pix']['amount'])->toBe(20000)
        ->and($byMethod->has('cash'))->toBeTrue()
        ->and($byMethod['cash']['count'])->toBe(1)
        ->and($byMethod['cash']['amount'])->toBe(10000);
});

test('GetSystemReportData top products are ordered by total quantity descending', function () {
    $productA = Product::factory()->create(['sale_price' => 10.00, 'unit_cost' => 5.00]);
    $productB = Product::factory()->create(['sale_price' => 10.00, 'unit_cost' => 5.00]);

    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 60.00,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'is_gift'         => false,
        'created_at'      => now(),
    ]);
    $sale->items()->create(['product_id' => $productA->id, 'quantity' => 1, 'unit_price' => 10.00, 'unit_cost' => 5.00, 'subtotal' => 10.00]);
    $sale->items()->create(['product_id' => $productB->id, 'quantity' => 5, 'unit_price' => 10.00, 'unit_cost' => 5.00, 'subtotal' => 50.00]);

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['topProducts']->first()->product_id)->toBe($productB->id)
        ->and($data['topProducts']->last()->product_id)->toBe($productA->id);
});

test('GetSystemReportData top products are limited to 10 results', function () {
    $sale = Sale::query()->create([
        'status'          => SaleStatus::Paid,
        'total_amount'    => 0,
        'discount_amount' => 0,
        'fee_amount'      => 0,
        'payment_method'  => 'cash',
        'is_gift'         => false,
        'created_at'      => now(),
    ]);

    Product::factory()->count(15)->create(['sale_price' => 10.00, 'unit_cost' => 5.00])->each(function ($product) use ($sale) {
        $sale->items()->create([
            'product_id' => $product->id,
            'quantity'   => 1,
            'unit_price' => 10.00,
            'unit_cost'  => 5.00,
            'subtotal'   => 10.00,
        ]);
    });

    $data = (new GetSystemReportData)->execute(
        now()->startOfDay()->format('Y-m-d'),
        now()->endOfDay()->format('Y-m-d'),
    );

    expect($data['topProducts'])->toHaveCount(10);
});

test('GetSystemReportData returns correct startDate endDate and generatedAt in result', function () {
    $data = (new GetSystemReportData)->execute('2026-01-01', '2026-01-31');

    expect($data['startDate'])->toBe('2026-01-01')
        ->and($data['endDate'])->toBe('2026-01-31')
        ->and($data['generatedAt'])->not->toBeEmpty();
});
