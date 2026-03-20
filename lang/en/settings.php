<?php

return [
    'title'    => 'General Settings',
    'settings' => 'Settings',
    'subtitle' => 'Manage your establishment\'s settings and information.',
    'sections' => [
        'fees' => [
            'title'       => 'Card Fees',
            'description' => 'Configure the fees charged by card operators.',
            'credit_fee'  => 'Credit Fee (%)',
            'debit_fee'   => 'Debit Fee (%)',
        ],
        'address' => [
            'title'                => 'Establishment Address',
            'description'          => 'This information will be used for location and weather services.',
            'street'               => 'Address',
            'street_placeholder'   => 'Street, number, neighborhood...',
            'city'                 => 'City',
            'state'                => 'State',
            'zip_code'             => 'ZIP Code',
            'zip_code_placeholder' => '00000-000',
        ],
        'general' => [
            'title'                  => 'General Information',
            'description'            => 'Basic information about your business.',
            'store_name'             => 'Store Name',
            'store_name_placeholder' => 'E.g.: Drinks Garage',
        ],
        'preferences' => [
            'title'       => 'Preferences',
            'description' => 'Personalize your system experience.',
            'language'    => 'Language',
        ],
    ],
    'actions' => [
        'save'    => 'Save',
        'saving'  => 'Saving...',
        'success' => 'Settings updated!',
    ],
];
