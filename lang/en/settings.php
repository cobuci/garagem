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
            'title'                          => 'General Information',
            'description'                    => 'Basic information about your business.',
            'store_name'                     => 'Store Name',
            'store_name_placeholder'         => 'E.g.: Drinks Garage',
            'skipped_categories'             => 'Skipped Categories',
            'skipped_categories_placeholder' => 'Select the categories that will not be included in calculations',
        ],
        'preferences' => [
            'title'       => 'Preferences',
            'description' => 'Personalize your system experience.',
            'language'    => 'Language',
            'name'        => 'Your Name',
        ],
    ],
    'actions' => [
        'save'    => 'Save',
        'saving'  => 'Saving...',
        'success' => 'Settings updated!',
    ],
    'import_legacy_data'         => 'Import Legacy Data',
    'import_legacy_description'  => 'Upload a SQL file from the old system to import categories and products.',
    'sql_file'                   => 'SQL File',
    'sql_file_hint'              => 'Select the sql file exported from the old database.',
    'import_now'                 => 'Import Now',
    'import_notice'              => 'The import is processed in the background. Completion time depends on the file size.',
    'import_started'             => 'Import Started',
    'import_started_description' => 'The file was received and is being processed. Data will appear in the system soon.',
    'upload_error_title'         => 'Upload Error',
    'upload_error_description'   => 'Could not upload the file. Please check the file size and try again.',
];
