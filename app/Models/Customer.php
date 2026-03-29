<?php

namespace App\Models;

use App\Contracts\Syncable;
use App\Enums\Gender;
use App\Traits\HasMobileSync;
use App\Traits\HasSearch;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

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
 * @property-read Collection<int, Sale> $sales
 *
 * @method static Builder<static> filters(array $filters)
 * @method        Builder<static> scopeFilters(Builder<static> $query, array $filters)
 */
class Customer extends Model implements AuditableContract, Syncable
{
    /* @use HasFactory<CustomerFactory> */
    use Auditable;
    use HasFactory;
    use HasMobileSync;
    use HasSearch;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected array $searchable = ['name', 'email', 'phone'];

    /** @return list<string> */
    public function getSyncableFields(): array
    {
        return [
            'id',
            'name',
            'gender',
            'phone',
            'email',
            'zip_code',
            'street',
            'neighborhood',
            'created_at',
            'updated_at',
        ];
    }

    protected $casts = [
        'gender' => Gender::class,
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
