<?php

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int     $id
 * @property string  $name
 * @property ?Gender $gender
 * @property ?string $phone
 * @property ?string $email
 * @property ?string $zip_code
 * @property ?string $street
 * @property ?string $neighborhood
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 */
class Customer extends Model
{
    /* @use HasFactory<CustomerFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'gender' => Gender::class,
    ];
}
