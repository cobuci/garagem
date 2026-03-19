<?php

namespace App\Actions\Product;

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

            ProductPurchase::create([
                'product_id'   => $product->id,
                'unit_cost'    => $data['unit_cost'],
                'total_cost'   => $data['unit_cost'] * $data['quantity'],
                'quantity'     => $data['quantity'],
                'invoice_date' => $data['invoice_date'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
                'due_date'     => $data['due_date'] ?? null,
            ]);

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
