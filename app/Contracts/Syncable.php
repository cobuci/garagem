<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * @method static Builder modifiedSince(?Carbon $since)
 * @method static Builder deletedSince(?Carbon $since)
 */
interface Syncable
{
    /** @return array<string, mixed> */
    public function toSyncArray(): array;

    /** @return list<string> */
    public function getSyncableFields(): array;
}
