<?php

namespace App\Actions\Product;

use App\Enums\TransactionType;
use App\Models\AccountBalance;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\ProductPurchase;
use Illuminate\Support\Facades\DB;
use Throwable;

class StockMovementAction
{
    /**
     * @throws Throwable
     */
    public function add(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $purchase = ProductPurchase::create([
                'product_id'   => $product->id,
                'unit_cost'    => $data['unit_cost'],
                'total_cost'   => $data['unit_cost'] * $data['quantity'],
                'quantity'     => $data['quantity'],
                'invoice_date' => $data['invoice_date'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
                'due_date'     => $data['due_date'] ?? null,
                'is_paid'      => ($data['payment_date'] ?? null) !== null,
            ]);

            if ($purchase->is_paid) {
                FinancialTransaction::query()->create([
                    'type'             => TransactionType::Purchase,
                    'amount'           => -(int) $purchase->getRawOriginal('total_cost'),
                    'description'      => "Purchase of {$product->name}",
                    'reference_id'     => $purchase->id,
                    'reference_type'   => ProductPurchase::class,
                    'transaction_date' => now(),
                ]);

                AccountBalance::singleton()->decrement('current_balance', $purchase->getRawOriginal('total_cost'));
            }

            $currentStock = $product->stock_quantity ?? 0;
            $currentUnitCost = $product->unit_cost ?? 0;

            $newQuantity = $data['quantity'];
            $newUnitCost = $data['unit_cost'];

            $totalQuantity = $currentStock + $newQuantity;

            $newAverageCost = $newUnitCost;

            if ($totalQuantity > 0) {
                $newAverageCost = (($currentStock * $currentUnitCost) + ($newQuantity * $newUnitCost)) / $totalQuantity;
            }

            $product->update([
                'stock_quantity'  => $totalQuantity,
                'unit_cost'       => $newAverageCost,
                'sale_price'      => $data['sale_price'],
                'expiration_date' => $data['expiration_date'] ?? $product->expiration_date,
            ]);

            return $product;
        });
    }

    /**
     * @throws Throwable
     */
    public function remove(int $productId, int $quantity): Product
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            $product->decrement('stock_quantity', $quantity);

            return $product;
        });
    }
}
