<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Enums\TransactionType;
use Carbon\Carbon;
use Database\Factories\FinancialTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int             $id
 * @property TransactionType $type
 * @property int             $amount
 * @property ?string         $description
 * @property ?int            $reference_id
 * @property ?string         $reference_type
 * @property Carbon          $transaction_date
 */
class FinancialTransaction extends Model
{
    /** @use HasFactory<FinancialTransactionFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type'             => TransactionType::class,
            'amount'           => MoneyCast::class,
            'transaction_date' => 'datetime',
        ];
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
