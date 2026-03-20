<?php

namespace App\Models;

use App\Casts\MoneyCast;
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
