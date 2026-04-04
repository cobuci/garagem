<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SyncRequest;
use App\Http\Responses\Api\Concerns\HasApiResponses;
use App\Services\Sync\PullSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class SyncController extends Controller
{
    use HasApiResponses;

    public function __invoke(SyncRequest $request, PullSyncService $pullSyncService): JsonResponse
    {
        $since = $request->input('last_synced_at')
            ? Carbon::parse($request->input('last_synced_at'))->setTimezone(config('app.timezone'))
            : null;

        $cursors = $request->input('cursors');

        $result = $pullSyncService->syncAll($since, $cursors);

        $syncedAt = Carbon::now()->toISOString();

        return $this->ok('Sync completed.', [
            'synced_at' => $syncedAt,
            'has_more'  => $result['has_more'],
            'cursors'   => $result['cursors'],
            'pull'      => $result['pull'],
        ]);
    }
}
