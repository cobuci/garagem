<?php

return [

    'api_key' => env('GEMINI_API_KEY'),

    'base_url' => env('GEMINI_BASE_URL'),

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 120),

    'banner_image_model' => env('GEMINI_BANNER_IMAGE_MODEL', 'gemini-2.5-flash-image'),

];
