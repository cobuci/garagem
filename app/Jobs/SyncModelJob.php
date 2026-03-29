<?php

namespace App\Jobs;

use App\Enums\Queue;
use App\Services\Sync\PullSyncService;
use App\Services\Sync\SyncRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class SyncModelJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $syncToken,
        public string $modelKey,
        public ?string $since,
    ) {
        $this->onQueue(Queue::Default);
    }

    public function handle(SyncRegistry $registry, PullSyncService $pullSyncService): void
    {
        $since = $this->since ? Carbon::parse($this->since) : null;

        $modelClass = PullSyncService::SYNCABLE_MODELS[$this->modelKey] ?? null;

        if ($modelClass === null) {
            $registry->markFailed($this->syncToken, $this->modelKey);

            return;
        }

        $payload = $pullSyncService->syncModel(new $modelClass, $since);

        $registry->markDone($this->syncToken, $this->modelKey, $payload);
    }

    public function failed(\Throwable $e): void
    {
        app(SyncRegistry::class)->markFailed($this->syncToken, $this->modelKey);
    }
}
