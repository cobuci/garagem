<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Support\Facades\Storage;

class SaleObserver
{
    public function deleting(Sale $sale): void
    {
        if ($sale->invoice_path) {
            Storage::delete($sale->invoice_path);
        }

        if ($sale->invoice_png_path) {
            Storage::delete($sale->invoice_png_path);
        }
    }
}
