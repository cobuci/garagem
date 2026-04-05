<?php

namespace App\Jobs;

use App\Enums\Queue;
use App\Models\Sale;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

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

        if ($this->sale->invoice_path) {
            Storage::delete($this->sale->invoice_path);
        }

        if ($this->sale->invoice_png_path) {
            Storage::delete($this->sale->invoice_png_path);
        }

        $sale = $this->sale->load(['customer', 'items.product']);
        $settings = Setting::singleton();

        $pdf = Pdf::loadView('pdf.invoice', [
            'sale'     => $sale,
            'settings' => $settings,
        ]);

        $pdfFileName = "invoices/{$this->sale->id}.pdf";
        Storage::put($pdfFileName, $pdf->output());

        $html = view('pdf.invoice', ['sale' => $sale, 'settings' => $settings])->render();
        $pngFileName = "invoices/{$this->sale->id}.png";
        $pngAbsPath = Storage::path($pngFileName);

        Storage::makeDirectory('invoices');

        $browsershot = Browsershot::html($html)
            ->setNodeBinary(config('services.browsershot.node_binary'))
            ->setNpmBinary(config('services.browsershot.npm_binary'))
            ->setNodeModulePath(base_path('node_modules'))
            ->addChromiumArguments([
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--headless=new',
            ])
            ->windowSize(900, 1200)
            ->setScreenshotType('png');

        if (config('services.browsershot.no_sandbox')) {
            $browsershot->noSandbox();
        }

        $browsershot->save($pngAbsPath);

        $this->sale->update([
            'invoice_status'   => 'ready',
            'invoice_path'     => $pdfFileName,
            'invoice_png_path' => $pngFileName,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->sale->update(['invoice_status' => 'failed']);
    }
}
