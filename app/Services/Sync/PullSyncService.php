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

    public const int PAGE_SIZE = 200;

    /**
     * @param  array<string, int>|null                                                                                                                     $cursors
     * @return array{pull: array<string, array{upsert: list<array<string, mixed>>, deleted: list<int>}>, has_more: bool, cursors: array<string, int>|null}
     */
    public function syncAll(?Carbon $since, ?array $cursors): array
    {
        $payload = [];
        $hasMore = false;
        $nextCursors = [];

        foreach (self::SYNCABLE_MODELS as $key => $modelClass) {
            $afterId = $cursors[$key] ?? null;
            $modelData = $this->syncModel(new $modelClass, $since, $afterId);

            $isFullSync = $since === null;
            $hasUpsert = ! empty($modelData['upsert']);
            $hasDeleted = ! empty($modelData['deleted']);

            if ($isFullSync || $hasUpsert || $hasDeleted) {
                if ($isFullSync && $cursors !== null) {
                    if (! array_key_exists($key, $cursors)) {
                        continue;
                    }
                }

                $payload[$key] = [
                    'upsert'  => $modelData['upsert'],
                    'deleted' => $modelData['deleted'],
                ];

                if ($modelData['has_more']) {
                    $hasMore = true;
                    $nextCursors[$key] = end($modelData['upsert'])['id'];
                }
            }
        }

        return [
            'pull'     => $payload,
            'has_more' => $hasMore,
            'cursors'  => $hasMore ? $nextCursors : null,
        ];
    }

    /**
     * @return array{upsert: list<array<string, mixed>>, deleted: list<int>, has_more: bool}
     */
    public function syncModel(Category|Customer|Product $model, ?Carbon $since, ?int $afterId): array
    {
        $fields = $model->getSyncableFields();
        $isFullSync = $since === null;
        $limit = $isFullSync ? self::PAGE_SIZE + 1 : null;

        $query = $model::query()
            ->when(! empty($fields), fn ($query) => $query->select($fields))
            ->modifiedSince($since);

        if ($isFullSync) {
            $query->forFullSyncPage($afterId, $limit);
        }

        $records = $query->get();
        $hasMore = $isFullSync && $records->count() > self::PAGE_SIZE;

        if ($hasMore) {
            $records = $records->slice(0, self::PAGE_SIZE);
        }

        $upsert = $records
            ->map(fn (Category|Customer|Product $record) => $record->toSyncArray())
            ->values()
            ->all();

        $deleted = $model::query()
            ->deletedSince($since)
            ->pluck('id')
            ->values()
            ->all();

        return [
            'upsert'   => $upsert,
            'deleted'  => $deleted,
            'has_more' => $hasMore,
        ];
    }
}
