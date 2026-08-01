<?php

return [
    'title'    => 'Banners',
    'subtitle' => 'Create promotional banners with AI-generated backgrounds and export in high quality.',
    'empty'    => 'No banners created yet.',

    'formats' => [
        'stories' => 'Stories (1080 × 1920)',
        'post'    => 'Post (1080 × 1350)',
        'square'  => 'Square (1080 × 1080)',
    ],

    'sections' => [
        'presets'    => 'Start from a template',
        'background' => 'AI Background',
        'logo'       => 'Logo',
        'content'    => 'Content',
        'items'      => 'Items and Prices',
        'export'     => 'Export',
        'preview'    => 'Preview',
    ],

    'hints' => [
        'workflow'    => 'Pick a template, generate the background and adjust the texts.',
        'presets'     => 'Apply a ready-made template, then customize freely.',
        'background'  => 'Pick a theme and, if you like, adjust mood and intensity.',
        'content'     => 'You can also tap the banner text directly to edit it.',
        'items'       => 'Items without a name are hidden on the final banner.',
        'tap_to_edit' => 'Tap text to edit',
    ],

    'presets' => [
        'barbecue' => 'Skewers',
        'drinks'   => 'Cold drinks',
        'snacks'   => 'Snacks',
    ],

    'themes' => [
        'barbecue' => [
            'label'       => 'Barbecue',
            'description' => 'Embers, smoke and skewer vibes',
        ],
        'cold_drinks' => [
            'label'       => 'Cold drinks',
            'description' => 'Chilled bottles and blue tones',
        ],
        'snacks' => [
            'label'       => 'Snacks',
            'description' => 'Warm golden appetizer tones',
        ],
        'daily_promo' => [
            'label'       => 'Daily promo',
            'description' => 'Dark, clean and elegant backdrop',
        ],
        'happy_hour' => [
            'label'       => 'Happy hour',
            'description' => 'Bokeh lights and festive mood',
        ],
        'brand_minimal' => [
            'label'       => 'Minimal',
            'description' => 'Blue gradient with brand identity',
        ],
    ],

    'moods' => [
        'dark'  => 'Dark',
        'light' => 'Light',
        'night' => 'Night',
    ],

    'intensities' => [
        'soft' => 'Soft',
        'bold' => 'Bold',
    ],

    'logo_positions' => [
        'top'    => 'Top',
        'bottom' => 'Bottom',
    ],

    'logo_aligns' => [
        'left'   => 'Left',
        'center' => 'Center',
        'right'  => 'Right',
    ],

    'palettes' => [
        'brand_dark'  => 'Brand (dark)',
        'brand_light' => 'Brand (light)',
    ],

    'logo_sizes' => [
        'small'  => 'Small',
        'medium' => 'Medium',
        'large'  => 'Large',
    ],

    'fields' => [
        'name'             => 'Name',
        'name_placeholder' => 'e.g. August Promo',
        'format'           => 'Format',
        'created_by'       => 'Created by',
        'updated_at'       => 'Updated',
        'title'            => 'Title',
        'subtitle'         => 'Subtitle',
        'footer'           => 'Footer',
        'background_color' => 'Background',
        'accent_color'     => 'Accent',
        'text_color'       => 'Text',
        'show_logo'        => 'Show logo on banner',
        'logo_position'    => 'Logo position',
        'logo_align'       => 'Logo alignment',
        'logo_size'        => 'Logo size',
        'brand_colors'     => 'Brand colors',
        'mood'             => 'Mood',
        'intensity'        => 'Intensity',
        'item_name'        => 'Item',
        'item_note'        => 'Note',
        'item_price'       => 'Price',
        'resolution'       => 'Resolution',
    ],

    'actions' => [
        'label'               => 'Actions',
        'create'              => 'New Banner',
        'save'                => 'Save',
        'cancel'              => 'Cancel',
        'delete'              => 'Delete',
        'back'                => 'Back',
        'add_item'            => 'Add item',
        'generate_background' => 'Generate AI background',
        'remove_background'   => 'Remove background',
        'export'              => 'Export',
        'download_png'        => 'Download PNG',
        'download_pdf'        => 'Download PDF',
    ],

    'messages' => [
        'success'               => 'Success!',
        'saved'                 => 'Banner saved.',
        'deleted'               => 'Banner deleted.',
        'delete_title'          => 'Delete banner?',
        'delete_description'    => 'This action cannot be undone.',
        'theme_required'        => 'Pick a theme to generate the background.',
        'background_generating' => 'Generating AI background, please wait...',
        'background_failed'     => 'Failed to generate background. Try again.',
        'export_generating'     => 'Exporting in high quality, please wait...',
        'export_failed'         => 'Failed to export. Try again.',
    ],
];
