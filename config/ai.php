<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Assistant Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Supported: "auto", "groq", "gemini", "openai", "local"
    | When "auto": will automatically use Groq if GROQ_API_KEY is present,
    | otherwise Gemini if GEMINI_API_KEY is present, otherwise intelligent local engine.
    |
    */
    'provider' => env('AI_PROVIDER', 'auto'),

    // API Keys
    'api_key' => env('AI_API_KEY', ''),
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'groq_api_key' => env('GROQ_API_KEY', ''),

    // Models
    'gemini_model' => env('GEMINI_MODEL', env('AI_MODEL', 'gemini-3.6-flash')),
    'groq_model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'), // LLaMA 3.3 70B Versatile on Groq

    'system_name' => 'المساعد الذكي لمكتب تدريب وتأهيل الخريجين — جامعة طرابلس',

    'temperature' => (float) env('AI_TEMPERATURE', 0.3),

    'max_tokens' => (int) env('AI_MAX_TOKENS', 2048),
];

