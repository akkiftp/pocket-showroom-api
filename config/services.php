<?php

return [
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'openai/gpt-4o-mini'),
        'base_url' => env('OPENAI_BASE_URL'),
    ],

];
