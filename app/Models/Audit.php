<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;

/**
 * @property-read ?User $user
 */
class Audit extends \OwenIt\Auditing\Models\Audit
{
    public function user(): MorphTo
    {
        $morphPrefix = Config::get('audit.user.morph_prefix', 'user');

        return $this->morphTo(__FUNCTION__, $morphPrefix . '_type', $morphPrefix . '_id');
    }
}
