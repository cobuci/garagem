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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('it can request a system report', function () {
    Queue::fake();
    $user = User::factory()->create();

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

    Livewire::actingAs($user)
        ->test(ExportReport::class)
        ->set('startDate', '2026-01-31')
        ->set('endDate', '2026-01-01')
        ->call('export')
        ->assertHasErrors(['endDate']);
});
