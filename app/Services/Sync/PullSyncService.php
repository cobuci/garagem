<?php

namespace App\Services\Sync;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;

class PullSyncService
{
    /**
     * @return array{
     *     products: array{upsert: list<array<string, mixed>>, deleted: list<int>},
     *     categories: array{upsert: list<array<string, mixed>>, deleted: list<int>},
     *     customers: array{upsert: list<array<string, mixed>>, deleted: list<int>},
     *     sales: array{upsert: list<array<string, mixed>>, deleted: list<int>},
     *     sale_items: array{upsert: list<array<string, mixed>>, deleted: list<int>},
     * }
     */
    public function handle(?Carbon $since): array
    {
        return [
            'products'   => $this->syncModel(new Product, $since),
            'categories' => $this->syncModel(new Category, $since),
            'customers'  => $this->syncModel(new Customer, $since),
            'sales'      => $this->syncModel(new Sale, $since),
            'sale_items' => $this->syncModel(new SaleItem, $since),
        ];
    }

    /**
     * @return array{upsert: list<array<string, mixed>>, deleted: list<int>}
     */
    private function syncModel(Product|Category|Customer|Sale|SaleItem $model, ?Carbon $since): array
    {
        $upsert = $model::query()
            ->modifiedSince($since)
            ->get()
            ->map(fn ($record) => $record->toSyncArray())
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
