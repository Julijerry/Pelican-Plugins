<?php

return [
    // API key for Google Generative Language
    'gemini_api_key' => env('GEMINI_API_KEY'),

    // Default model to use for analysis. Can be overridden in the UI per-request.
    'model' => env('LOG_ANALYZER_MODEL', 'gemini-2.5-flash'),

    // Available models shown in the dropdown. Adjust as needed for your subscription.
    'available_models' => [
        'gemini-3-pro-preview',
        'gemini-3-flash-preview',
        'gemini-2.5-pro',
        'gemini-2.5-flash',
        'gemini-2.0-flash',
    ],
];
