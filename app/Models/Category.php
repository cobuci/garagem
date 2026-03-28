<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * @property int       $id
 * @property string    $name
 * @property ?string   $icon
 * @property ?int      $sort_order
 * @property ?Carbon   $created_at
 * @property ?Carbon   $updated_at
 * @property ?Carbon   $deleted_at
 * @property Product[] $products
 */
class Category extends Model implements AuditableContract
{
    use Auditable;
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
