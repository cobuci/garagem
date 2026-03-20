<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property int     $product_id
 * @property int     $quantity
 * @property int     $unit_cost
 * @property int     $total_cost
 * @property ?Carbon $invoice_date
 * @property ?Carbon $payment_date
 * @property ?Carbon $due_date
 * @property bool    $is_paid
 */
class ProductPurchase extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'unit_cost'    => MoneyCast::class,
            'total_cost'   => MoneyCast::class,
            'invoice_date' => 'date',
            'payment_date' => 'date',
            'due_date'     => 'date',
            'is_paid'      => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (ProductPurchase $purchase) {
            if ($purchase->wasChanged('is_paid') && $purchase->is_paid && ! $purchase->wasRecentlyCreated) {
                $totalCost = $purchase->getAttributes()['total_cost'] ?? 0;
                AccountBalance::singleton()->decrement('current_balance', (int) $totalCost);

                FinancialTransaction::query()->create([
                    'type'             => TransactionType::Purchase,
                    'amount'           => -(int) $totalCost,
                    'description'      => "Purchase of {$purchase->product->name}",
                    'reference_id'     => $purchase->id,
                    'reference_type'   => ProductPurchase::class,
                    'transaction_date' => now(),
                ]);
            }
        });

        static::deleted(function (ProductPurchase $purchase) {
            if ($purchase->is_paid) {
                $totalCost = $purchase->getAttributes()['total_cost'] ?? 0;
                AccountBalance::singleton()->increment('current_balance', (int) $totalCost);

                FinancialTransaction::query()->create([
                    'type'             => TransactionType::Purchase,
                    'amount'           => (int) $totalCost,
                    'description'      => "Cancelled purchase: {$purchase->product->name}",
                    'reference_id'     => $purchase->id,
                    'reference_type'   => ProductPurchase::class,
                    'transaction_date' => now(),
                ]);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
