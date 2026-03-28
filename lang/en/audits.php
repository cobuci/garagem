<?php

return [
    'events' => [
        'created'  => 'Created',
        'updated'  => 'Updated',
        'deleted'  => 'Deleted',
        'restored' => 'Restored',
    ],
    'filters' => [
        'all_events' => 'All',
        'all_models' => 'All models',
        'all_users'  => 'All users',
    ],
    'table' => [
        'date'      => 'Date',
        'event'     => 'Event',
        'model'     => 'Model',
        'user'      => 'User',
        'changes'   => 'Changes',
        'view_diff' => 'View diff',
        'system'    => 'System',
        'empty'     => 'No audit records found.',
        'fields'    => ':count field|:count fields',
    ],
    'modal' => [
        'title'      => 'Audit Details',
        'model'      => 'Model',
        'event'      => 'Event',
        'user'       => 'User',
        'date'       => 'Date',
        'field'      => 'Field',
        'old_value'  => 'Old value',
        'new_value'  => 'New value',
        'no_changes' => 'No field changes recorded for this event.',
        'close'      => 'Close',
    ],
];
