<?php

return [
    // gemini | openai | anthropic | openai_compatible
    'provider' => env('LOG_ANALYZER_PROVIDER', 'gemini'),

    // Prefer LOG_ANALYZER_API_KEY; fall back to legacy GEMINI_API_KEY
    'api_key' => env('LOG_ANALYZER_API_KEY', env('GEMINI_API_KEY')),

    // Used by openai_compatible (e.g. https://api.openai.com/v1 or a proxy)
    'base_url' => env('LOG_ANALYZER_BASE_URL', 'https://api.openai.com/v1'),

    // Selected preset model id, or "custom"
    'model' => env('LOG_ANALYZER_MODEL', 'gemini-3.5-flash'),

    // Used when model === "custom"
    'custom_model' => env('LOG_ANALYZER_CUSTOM_MODEL'),

    // Legacy key kept for older installs
    'gemini_api_key' => env('GEMINI_API_KEY'),

    'providers' => [
        'gemini' => [
            'label' => 'Google Gemini',
            'models' => [
                'gemini-3.5-flash' => 'Gemini 3.5 Flash',
                'gemini-3.1-pro-preview' => 'Gemini 3.1 Pro Preview',
                'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash-Lite',
                'gemini-3-flash-preview' => 'Gemini 3 Flash Preview',
                'gemini-2.5-pro' => 'Gemini 2.5 Pro',
                'gemini-2.5-flash' => 'Gemini 2.5 Flash',
                'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash-Lite',
            ],
        ],
        'openai' => [
            'label' => 'OpenAI',
            'models' => [
                'gpt-5.6-sol' => 'GPT-5.6 Sol',
                'gpt-5.6-terra' => 'GPT-5.6 Terra',
                'gpt-5.6-luna' => 'GPT-5.6 Luna',
                'gpt-5.6' => 'GPT-5.6 (alias → Sol)',
                'gpt-5.4' => 'GPT-5.4',
                'gpt-5.4-mini' => 'GPT-5.4 Mini',
                'gpt-5.4-nano' => 'GPT-5.4 Nano',
            ],
        ],
        'anthropic' => [
            'label' => 'Anthropic',
            'models' => [
                'claude-fable-5' => 'Claude Fable 5',
                'claude-opus-4-8' => 'Claude Opus 4.8',
                'claude-sonnet-5' => 'Claude Sonnet 5',
                'claude-haiku-4-5' => 'Claude Haiku 4.5',
                'claude-sonnet-4-6' => 'Claude Sonnet 4.6',
                'claude-opus-4-7' => 'Claude Opus 4.7',
            ],
        ],
        'openai_compatible' => [
            'label' => 'OpenAI Compatible',
            'models' => [],
        ],
    ],
];
