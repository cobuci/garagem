<?php

use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Resolvers\IpAddressResolver;
use OwenIt\Auditing\Resolvers\UrlResolver;
use OwenIt\Auditing\Resolvers\UserAgentResolver;
use OwenIt\Auditing\Resolvers\UserResolver;

return [

    'enabled' => env('AUDITING_ENABLED', true),

    'implementation' => Audit::class,

    'user' => [
        'morph_prefix' => 'user',
        'guards'       => ['web', 'api'],
        'resolver'     => UserResolver::class,
    ],

    'resolvers' => [
        'ip_address' => IpAddressResolver::class,
        'user_agent' => UserAgentResolver::class,
        'url'        => UrlResolver::class,
    ],

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    'strict' => false,

    'exclude' => [],

    /*
    |--------------------------------------------------------------------------
    | Empty Values
    |--------------------------------------------------------------------------
    |
    | Discard audit records where both old_values and new_values are empty.
    | This prevents noise from no-op updates.
    |
    */

    'empty_values'         => false,
    'allowed_empty_values' => ['retrieved'],

    'allowed_array_values' => true,

    'timestamps' => false,

    'threshold' => 200,

    'driver' => 'database',

    'drivers' => [
        'database' => [
            'table'      => 'audits',
            'connection' => null,
        ],
    ],

    'queue' => [
        'enable'     => env('AUDITING_QUEUE', false),
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue'      => 'audits',
        'delay'      => 0,
    ],

    'console' => false,
];
