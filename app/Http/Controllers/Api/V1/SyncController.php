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
        $syncedAt = now();

        $since = $request->filled('last_synced_at')
            ? Carbon::parse($request->string('last_synced_at'))
            : null;

        $pull = $pullSyncService->handle($since);

        return $this->ok('Sync completed.', [
            'synced_at' => $syncedAt->toIso8601String(),
            'pull'      => $pull,
        ]);
    }
}
