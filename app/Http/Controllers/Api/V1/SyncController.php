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

        $cursors = $request->input('cursors');

        $result = $pullSyncService->syncAll($since, $cursors);

        $syncedAt = DB::selectOne("SELECT DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') AS now")->now;

        return $this->ok('Sync completed.', [
            'synced_at' => $syncedAt,
            'has_more'  => $result['has_more'],
            'cursors'   => $result['cursors'],
            'pull'      => $result['pull'],
        ]);
    }
}
