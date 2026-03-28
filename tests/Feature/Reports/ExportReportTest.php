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
