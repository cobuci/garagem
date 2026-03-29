<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

trait HasMobileSync
{
    public function scopeModifiedSince(Builder $query, ?Carbon $since): Builder
    {
        if ($since === null) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($since): void {
            $query->where('created_at', '>', $since)
                ->orWhere('updated_at', '>', $since);
        });
    }

    public function scopeDeletedSince(Builder $query, ?Carbon $since): Builder
    {
        if ($since === null || ! $this->usesSoftDeletes()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->withoutGlobalScopes()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '>', $since);
    }

    /** @return array<string, mixed> */
    public function toSyncArray(): array
    {
        $fields = $this->getSyncableFields();

        if (empty($fields)) {
            return $this->toArray();
        }

        return collect($this->getAttributes())
            ->only($fields)
            ->toArray();
    }

    /** @return list<string> */
    public function getSyncableFields(): array
    {
        return [];
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive(static::class), true);
    }
}
