<?php

namespace App\Jobs;

use App\Enums\Queue;
use App\Models\Sale;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Sale $sale)
    {
        $this->onQueue(Queue::Default);
    }

    public function handle(): void
    {
        $this->sale->update(['invoice_status' => 'generating']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'sale'     => $this->sale->load(['customer', 'items.product']),
            'settings' => Setting::singleton(),
        ]);

        $fileName = "invoices/{$this->sale->id}.pdf";
        Storage::put($fileName, $pdf->output());

        $this->sale->update([
            'invoice_status' => 'ready',
            'invoice_path'   => $fileName,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->sale->update(['invoice_status' => 'failed']);
    }
}
