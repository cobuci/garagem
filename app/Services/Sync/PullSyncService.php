<?php

namespace App\Services\Sync;

use App\Contracts\Syncable;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PullSyncService
{
    /**
     * Ordered map of sync key → model class for all syncable entities.
     *
     * @var array<string, class-string<Model&Syncable>>
     */
    public const array SYNCABLE_MODELS = [
        'categories' => Category::class,
        'products'   => Product::class,
        'customers'  => Customer::class,
        'sales'      => Sale::class,
        'sale_items' => SaleItem::class,
    ];

    /**
     * @return array{upsert: list<array<string, mixed>>, deleted: list<int>}
     */
    public function syncModel(Category|Customer|Product|Sale|SaleItem $model, ?Carbon $since): array
    {
        $upsert = $model::query()
            ->modifiedSince($since)
            ->get()
            ->map(fn (Category|Customer|Product|Sale|SaleItem $record) => $record->toSyncArray())
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
