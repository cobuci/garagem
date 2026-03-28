<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Casts\PaymentMethodCast;
use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Enums\TransactionType;
use Database\Factories\SaleFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * @property int           $id
 * @property ?int          $customer_id
 * @property PaymentMethod $payment_method
 * @property float         $total_amount
 * @property float         $discount_amount
 * @property float         $fee_amount
 * @property float         $fee_percentage
 * @property bool          $pass_fee_to_customer
 * @property float         $net_amount
 * @property bool          $is_gift
 * @property SaleStatus    $status
 * @property string        $invoice_status
 * @property ?string       $invoice_path
 * @property ?string       $invoice_png_path
 * @property ?Carbon       $created_at
 * @property ?Carbon       $updated_at
 * @property-read ?Customer $customer
 * @property-read Collection<int, SaleItem> $items
 */
class Sale extends Model implements AuditableContract
{
    use Auditable;
    /** @use HasFactory<SaleFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /** @var list<string> */
    protected array $auditExclude = [
        'invoice_path',
        'invoice_png_path',
        'invoice_status',
    ];

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
            'payment_method'       => PaymentMethodCast::class,
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Sale $sale) {
            if ($sale->status === SaleStatus::Paid) {
                $netAmount = $sale->getAttributes()['net_amount'] ?? 0;
                AccountBalance::singleton()->increment('current_balance', (int) $netAmount);

                FinancialTransaction::query()->create([
                    'type'             => TransactionType::Sale,
                    'amount'           => $netAmount,
                    'description'      => "Sale #{$sale->id}",
                    'reference_id'     => $sale->id,
                    'reference_type'   => Sale::class,
                    'transaction_date' => now(),
                ]);
            }
        });

        static::updated(function (Sale $sale) {
            if ($sale->wasChanged('status')) {
                if ($sale->status === SaleStatus::Paid) {
                    $netAmount = $sale->getAttributes()['net_amount'] ?? 0;
                    AccountBalance::singleton()->increment('current_balance', (int) $netAmount);

                    FinancialTransaction::query()->create([
                        'type'             => TransactionType::Sale,
                        'amount'           => $netAmount,
                        'description'      => "Sale #{$sale->id}",
                        'reference_id'     => $sale->id,
                        'reference_type'   => Sale::class,
                        'transaction_date' => now(),
                    ]);
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

                            FinancialTransaction::query()->create([
                                'type'             => TransactionType::CancelledSale,
                                'amount'           => -(int) $netAmount,
                                'description'      => "Sale #{$sale->id} cancelled",
                                'reference_id'     => $sale->id,
                                'reference_type'   => Sale::class,
                                'transaction_date' => now(),
                            ]);
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
        return (float) $this->items->sum(fn (SaleItem $item): float => (float) $item->getRawOriginal('unit_cost') * $item->quantity) / 100;
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
