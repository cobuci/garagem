<?php

namespace App\Models;

use Database\Factories\MobileSaleItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int     $id
 * @property int     $mobile_sale_id
 * @property int     $product_id
 * @property int     $unit_price_cents
 * @property int     $quantity
 * @property int     $subtotal_cents
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read MobileSale $mobileSale
 * @property-read Product    $product
 */
class MobileSaleItem extends Model
{
    /** @use HasFactory<MobileSaleItemFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function mobileSale(): BelongsTo
    {
        return $this->belongsTo(MobileSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
