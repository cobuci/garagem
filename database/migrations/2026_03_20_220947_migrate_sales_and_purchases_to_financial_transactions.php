<?php

use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use App\Models\FinancialTransaction;
use App\Models\ProductPurchase;
use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Sale::query()->where('status', SaleStatus::Paid)->each(function (Sale $sale) {
            FinancialTransaction::query()->create([
                'type'             => TransactionType::Sale,
                'amount'           => $sale->getAttributes()['net_amount'],
                'description'      => "Venda #{$sale->id}",
                'reference_id'     => $sale->id,
                'reference_type'   => Sale::class,
                'transaction_date' => $sale->created_at,
            ]);
        });

        ProductPurchase::query()->with('product')->where('is_paid', true)->each(function (ProductPurchase $purchase) {
            FinancialTransaction::query()->create([
                'type'             => TransactionType::Purchase,
                'amount'           => -$purchase->getAttributes()['total_cost'],
                'description'      => "Compra de {$purchase->product->name}",
                'reference_id'     => $purchase->id,
                'reference_type'   => ProductPurchase::class,
                'transaction_date' => $purchase->payment_date ?? $purchase->created_at,
            ]);
        });
    }

    public function down(): void
    {
        FinancialTransaction::query()->truncate();
    }
};
