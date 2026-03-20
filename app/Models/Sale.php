<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\SaleStatus;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'total_amount'         => MoneyCast::class,
            'discount_amount'      => MoneyCast::class,
            'fee_amount'           => MoneyCast::class,
            'fee_percentage'       => 'float',
            'pass_fee_to_customer' => 'boolean',
            'net_amount'           => MoneyCast::class,
            'is_gift'              => 'boolean',
            'status'               => SaleStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Sale $sale) {
            if ($sale->status === SaleStatus::Paid) {
                $netAmount = $sale->getAttributes()['net_amount'] ?? 0;
                AccountBalance::singleton()->increment('current_balance', (int) $netAmount);
            }
        });

        static::updated(function (Sale $sale) {
            if ($sale->wasChanged('status')) {
                if ($sale->status === SaleStatus::Paid) {
                    $netAmount = $sale->getAttributes()['net_amount'] ?? 0;
                    AccountBalance::singleton()->increment('current_balance', (int) $netAmount);
                }

                if ($sale->status === SaleStatus::Cancelled) {
                    DB::transaction(function () use ($sale) {
                        foreach ($sale->items as $item) {
                            $product = Product::query()->find($item->product_id);
                            if ($product instanceof Product && $product->stock_quantity !== null) {
                                $product->increment('stock_quantity', $item->quantity);
                            }
                        }

                        if ($sale->getOriginal('status') === SaleStatus::Paid) {
                            $netAmount = $sale->getAttributes()['net_amount'] ?? 0;
                            AccountBalance::singleton()->decrement('current_balance', (int) $netAmount);
                        }
                    });
                }
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function totalCost(): float
    {
        return (float) $this->items->sum(fn (SaleItem $item) => (float) $item->getRawOriginal('unit_cost') * $item->quantity) / 100;
    }

    public function profit(): float
    {
        $totalAmount = (float) $this->getRawOriginal('total_amount');

        if ($this->is_gift) {
            return -$this->totalCost();
        }

        return ($totalAmount / 100) - $this->totalCost();
    }
}
