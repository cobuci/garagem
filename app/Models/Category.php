<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int       $id
 * @property string    $name
 * @property ?string   $icon
 * @property ?Carbon   $created_at
 * @property ?Carbon   $updated_at
 * @property ?Carbon   $deleted_at
 * @property Product[] $products
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
