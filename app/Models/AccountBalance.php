<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $current_balance
 * @property int $target_balance
 */
class AccountBalance extends Model
{
    protected $guarded = ['id'];

    public static function singleton(): self
    {
        return self::first() ?? self::create([
            'current_balance' => 0,
            'target_balance'  => 0,
        ]);
    }

    protected function casts(): array
    {
        return [
            'current_balance' => MoneyCast::class,
            'target_balance'  => MoneyCast::class,
        ];
    }
}
