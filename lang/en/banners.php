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
        'content'    => 'Content',
        'items'      => 'Items and Prices',
        'background' => 'AI Background',
        'export'     => 'Export',
        'preview'    => 'Preview',
    ],

    'fields' => [
        'name'                          => 'Name',
        'name_placeholder'              => 'e.g. August Promo',
        'format'                        => 'Format',
        'created_by'                    => 'Created by',
        'updated_at'                    => 'Updated',
        'title'                         => 'Title',
        'subtitle'                      => 'Subtitle',
        'footer'                        => 'Footer',
        'background_color'              => 'Background',
        'accent_color'                  => 'Accent',
        'text_color'                    => 'Text',
        'item_name'                     => 'Item',
        'item_note'                     => 'Note',
        'item_price'                    => 'Price',
        'background_prompt'             => 'Describe the desired background',
        'background_prompt_placeholder' => 'e.g. dark garage workshop background, blue tones, modern style',
        'resolution'                    => 'Resolution',
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
        'background_generating' => 'Generating AI background, please wait...',
        'background_failed'     => 'Failed to generate background. Try again.',
        'export_generating'     => 'Exporting in high quality, please wait...',
        'export_failed'         => 'Failed to export. Try again.',
    ],
];
