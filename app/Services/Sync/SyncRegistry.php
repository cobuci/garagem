<?php

namespace App\Services\Sync;

use Illuminate\Support\Facades\Cache;

class SyncRegistry
{
    private const TTL_SECONDS = 300;

    private const STATUS_PENDING = 'pending';

    private const STATUS_DONE = 'done';

    private const STATUS_FAILED = 'failed';

    public function initSession(string $syncToken, string $syncedAt, ?string $since = null): void
    {
        Cache::put(
            $this->metaKey($syncToken),
            ['synced_at' => $syncedAt, 'since' => $since],
            self::TTL_SECONDS,
        );
    }

    public function markPending(string $syncToken, string $modelKey): void
    {
        Cache::put($this->statusKey($syncToken, $modelKey), self::STATUS_PENDING, self::TTL_SECONDS);
    }

    public function markDone(string $syncToken, string $modelKey, array $payload): void
    {
        Cache::put($this->payloadKey($syncToken, $modelKey), $payload, self::TTL_SECONDS);
        Cache::put($this->statusKey($syncToken, $modelKey), self::STATUS_DONE, self::TTL_SECONDS);
    }

    public function markFailed(string $syncToken, string $modelKey): void
    {
        Cache::put($this->statusKey($syncToken, $modelKey), self::STATUS_FAILED, self::TTL_SECONDS);
    }

    /**
     * @param  list<string>                                                                                                                        $modelKeys
     * @return array{synced_at: string, since: string|null, models: array<string, array{status: string, payload: array<string, mixed>|null}>}|null
     */
    public function getSession(string $syncToken, array $modelKeys): ?array
    {
        $meta = Cache::get($this->metaKey($syncToken));

        if ($meta === null) {
            return null;
        }

        $models = [];

        foreach ($modelKeys as $key) {
            $status = Cache::get($this->statusKey($syncToken, $key), self::STATUS_PENDING);
            $payload = $status === self::STATUS_DONE
                ? Cache::get($this->payloadKey($syncToken, $key))
                : null;

            $models[$key] = [
                'status'  => $status,
                'payload' => $payload,
            ];
        }

        return [
            'synced_at' => $meta['synced_at'],
            'since'     => $meta['since'],
            'models'    => $models,
        ];
    }

    public function sessionExists(string $syncToken): bool
    {
        return Cache::has($this->metaKey($syncToken));
    }

    private function metaKey(string $syncToken): string
    {
        return "sync:{$syncToken}:meta";
    }

    private function statusKey(string $syncToken, string $modelKey): string
    {
        return "sync:{$syncToken}:{$modelKey}:status";
    }

    private function payloadKey(string $syncToken, string $modelKey): string
    {
        return "sync:{$syncToken}:{$modelKey}:payload";
    }
}
