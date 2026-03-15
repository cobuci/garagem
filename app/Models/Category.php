<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property string  $name
 * @property ?string $icon
 */
class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
}
