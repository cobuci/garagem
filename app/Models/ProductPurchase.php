<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
