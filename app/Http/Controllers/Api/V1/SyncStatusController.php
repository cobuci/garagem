<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\Concerns\HasApiResponses;
use App\Services\Sync\PullSyncService;
use App\Services\Sync\SyncRegistry;
use Illuminate\Http\JsonResponse;

class SyncStatusController extends Controller
{
    use HasApiResponses;

    public function __invoke(string $syncToken, SyncRegistry $registry): JsonResponse
    {
        $modelKeys = array_keys(PullSyncService::SYNCABLE_MODELS);
        $session = $registry->getSession($syncToken, $modelKeys);

        if ($session === null) {
            return $this->notFound('Sync session not found or expired.');
        }

        $statuses = array_column($session['models'], 'status');
        $isDone = ! in_array('pending', $statuses, true);
        $hasFailed = in_array('failed', $statuses, true);

        $pull = [];

        foreach ($session['models'] as $key => $model) {
            if ($model['status'] === 'done') {
                $pull[$key] = $model['payload'];
            }
        }

        return $this->ok('Sync status retrieved.', [
            'sync_token' => $syncToken,
            'synced_at'  => $session['synced_at'],
            'status'     => match (true) {
                $hasFailed => 'failed',
                $isDone    => 'completed',
                default    => 'pending',
            },
            'models' => $session['models'],
            'pull'   => $pull,
        ]);
    }
}
