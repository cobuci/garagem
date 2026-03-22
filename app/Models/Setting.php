<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int     $id
 * @property ?float  $credit_card_fee
 * @property ?float  $debit_card_fee
 * @property ?string $store_name
 * @property ?string $address
 * @property ?string $city
 * @property ?string $state
 * @property ?string $zip_code
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property array   $skipped_categories
 */
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'skipped_categories' => 'array',
        ];
    }

    public static function singleton(): self
    {
        return self::first() ?? self::create([
            'credit_card_fee' => 0,
            'debit_card_fee'  => 0,
        ]);
    }
}
