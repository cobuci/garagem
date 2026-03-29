<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Contracts\Syncable;
use App\Traits\HasMobileSync;
use Database\Factories\SaleItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * @property int     $id
 * @property int     $sale_id
 * @property int     $product_id
 * @property int     $quantity
 * @property float   $unit_price
 * @property float   $unit_cost
 * @property float   $subtotal
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property-read Sale $sale
 * @property-read Product $product
 */
class SaleItem extends Model implements AuditableContract, Syncable
{
    use Auditable;
    /** @use HasFactory<SaleItemFactory> */
    use HasFactory;
    use HasMobileSync;

    protected $guarded = ['id'];

    /** @return list<string> */
    public function getSyncableFields(): array
    {
        return [
            'id',
            'sale_id',
            'product_id',
            'quantity',
            'unit_price',
            'subtotal',
            'created_at',
            'updated_at',
        ];
    }

    protected function casts(): array
    {
        return [
            'unit_price' => MoneyCast::class,
            'unit_cost'  => MoneyCast::class,
            'subtotal'   => MoneyCast::class,
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
