<?php

namespace App\Models;

use App\Enums\MobileSaleStatus;
use Database\Factories\MobileSaleFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int              $id
 * @property string           $local_id
 * @property ?int             $customer_id
 * @property ?string          $customer_name
 * @property int              $total_amount_cents
 * @property MobileSaleStatus $status
 * @property Carbon           $device_created_at
 * @property ?Carbon          $created_at
 * @property ?Carbon          $updated_at
 * @property-read ?Customer    $customer
 * @property-read Collection<int, MobileSaleItem> $items
 */
class MobileSale extends Model
{
    /** @use HasFactory<MobileSaleFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status'            => MobileSaleStatus::class,
            'device_created_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MobileSaleItem::class);
    }
}
