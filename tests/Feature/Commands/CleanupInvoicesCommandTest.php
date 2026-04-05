<?php

use App\Models\Sale;
use Illuminate\Support\Facades\Storage;

it('cleans up invoices older than 7 days', function () {
    Storage::fake();

    $recentSale = Sale::factory()->create([
        'invoice_status' => 'ready',
        'invoice_path'   => 'invoices/recent.pdf',
        'updated_at'     => now(),
    ]);
    Storage::put('invoices/recent.pdf', 'content');

    $oldSale = Sale::factory()->create([
        'invoice_status' => 'ready',
        'invoice_path'   => 'invoices/old.pdf',
    ]);
    $oldSale->update(['updated_at' => now()->subDays(8)]);
    Storage::put('invoices/old.pdf', 'content');

    $this->artisan('invoices:cleanup')->assertSuccessful();

    $recentSale->refresh();
    expect($recentSale->invoice_status)->toBe('ready');
    Storage::assertExists('invoices/recent.pdf');

    $oldSale->refresh();
    expect($oldSale->invoice_status)->toBe('none')
        ->and($oldSale->invoice_path)->toBeNull();
    Storage::assertMissing('invoices/old.pdf');
});
