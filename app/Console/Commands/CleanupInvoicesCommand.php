<?php

namespace App\Console\Commands;

use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupInvoicesCommand extends Command
{
    protected $signature = 'invoices:cleanup';

    public function handle(): void
    {
        Sale::where('invoice_status', 'ready')
            ->where('updated_at', '<', now()->subDays(7))
            ->each(function (Sale $sale) {
                if ($sale->invoice_path) {
                    Storage::delete($sale->invoice_path);
                }

                if ($sale->invoice_png_path) {
                    Storage::delete($sale->invoice_png_path);
                }

                $sale->update([
                    'invoice_status'   => 'none',
                    'invoice_path'     => null,
                    'invoice_png_path' => null,
                ]);
            });
    }
}
