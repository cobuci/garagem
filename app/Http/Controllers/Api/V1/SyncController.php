<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SyncRequest;
use App\Http\Responses\Api\Concerns\HasApiResponses;
use App\Services\Sync\PullSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    use HasApiResponses;

    public function __invoke(SyncRequest $request, PullSyncService $pullSyncService): JsonResponse
    {
        $since = $request->input('last_synced_at')
            ? Carbon::parse($request->input('last_synced_at'))
            : null;

        $syncedAt = DB::selectOne('SELECT NOW(3) AS now')->now;

        $pull = $pullSyncService->syncAll($since);

        return $this->ok('Sync completed.', [
            'synced_at' => $syncedAt,
            'pull'      => $pull,
        ]);
    }
}
