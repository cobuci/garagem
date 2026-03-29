<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SyncRequest;
use App\Http\Responses\Api\Concerns\HasApiResponses;
use App\Jobs\SyncModelJob;
use App\Services\Sync\PullSyncService;
use App\Services\Sync\SyncRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    use HasApiResponses;

    public function __invoke(SyncRequest $request, SyncRegistry $registry): JsonResponse
    {
        $syncToken = Str::uuid()->toString();
        $syncedAt = now()->toIso8601String();
        $since = $request->input('last_synced_at');

        $registry->initSession($syncToken, $syncedAt, $since);

        foreach (array_keys(PullSyncService::SYNCABLE_MODELS) as $modelKey) {
            $registry->markPending($syncToken, $modelKey);

            SyncModelJob::dispatch($syncToken, $modelKey, $since);
        }

        return $this->accepted('Sync started.', [
            'sync_token' => $syncToken,
            'synced_at'  => $syncedAt,
            'models'     => array_keys(PullSyncService::SYNCABLE_MODELS),
        ]);
    }
}
