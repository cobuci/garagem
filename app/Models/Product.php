<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int        $id
 * @property int        $category_id
 * @property string     $name
 * @property string     $brand
 * @property string     $weight
 * @property ?string    $upc
 * @property ?int       $stock_quantity
 * @property ?MoneyCast $unit_cost
 * @property ?MoneyCast $sale_price
 * @property ?Carbon    $expiration_date
 * @property ?Carbon    $created_at
 * @property ?Carbon    $updated_at
 * @property ?Carbon    $deleted_at
 * @property Category   $category
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'datetime',
            'unit_cost'       => MoneyCast::class,
            'sale_price'      => MoneyCast::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(ProductPurchase::class);
    }
}
