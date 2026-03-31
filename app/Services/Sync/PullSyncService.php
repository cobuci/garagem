<?php

namespace App\Services\Sync;

use App\Contracts\Syncable;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PullSyncService
{
    /**
     * @var array<string, class-string<Model&Syncable>>
     */
    public const array SYNCABLE_MODELS = [
        'categories' => Category::class,
        'products'   => Product::class,
        'customers'  => Customer::class,
    ];

    /**
     * @return array<string, array{upsert: list<array<string, mixed>>, deleted: list<int>}>
     */
    public function syncAll(?Carbon $since): array
    {
        $payload = [];

        foreach (self::SYNCABLE_MODELS as $key => $modelClass) {
            $payload[$key] = $this->syncModel(new $modelClass, $since);
        }

        return $payload;
    }

    /**
     * @return array{upsert: list<array<string, mixed>>, deleted: list<int>}
     */
    public function syncModel(Category|Customer|Product $model, ?Carbon $since): array
    {
        $upsert = $model::query()
            ->modifiedSince($since)
            ->get()
            ->map(fn (Category|Customer|Product $record) => $record->toSyncArray())
            ->values()
            ->all();

        $deleted = $model::query()
            ->deletedSince($since)
            ->pluck('id')
            ->values()
            ->all();

        return [
            'upsert'  => $upsert,
            'deleted' => $deleted,
        ];
    }
}
