<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Assistant Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supported: "gemini", "openai", "mock" (auto-fallback when no API key)
    |
    */
    'provider' => env('AI_PROVIDER', 'gemini'),

    'api_key' => env('GEMINI_API_KEY', env('AI_API_KEY', '')),

    'model' => env('AI_MODEL', 'gemini-1.5-flash'),

    'system_name' => 'المساعد الذكي لمكتب تدريب وتأهيل الخريجين — جامعة طرابلس',

    'temperature' => (float) env('AI_TEMPERATURE', 0.4),

    'max_tokens' => (int) env('AI_MAX_TOKENS', 2048),
];
