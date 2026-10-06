<?php

// قراءة الإعدادات الديناميكية المحفوظة من لوحة التحكم في حال وجودها
$storedAi = [];
$aiSettingsPath = storage_path('app/ai_settings.json');
if (file_exists($aiSettingsPath)) {
    $storedAi = json_decode(@file_get_contents($aiSettingsPath), true) ?: [];
}

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
    'provider' => $storedAi['AI_PROVIDER'] ?? env('AI_PROVIDER', 'auto'),

    // API Keys
    'api_key' => $storedAi['AI_API_KEY'] ?? env('AI_API_KEY', ''),
    'gemini_api_key' => $storedAi['GEMINI_API_KEY'] ?? env('GEMINI_API_KEY', ''),
    'groq_api_key' => $storedAi['GROQ_API_KEY'] ?? env('GROQ_API_KEY', ''),

    // Models
    'gemini_model' => $storedAi['GEMINI_MODEL'] ?? env('GEMINI_MODEL', env('AI_MODEL', 'gemini-3.6-flash')),
    'groq_model' => $storedAi['GROQ_MODEL'] ?? env('GROQ_MODEL', 'openai/gpt-oss-120b'),

    'system_name' => 'المساعد الذكي لمكتب تدريب الخريجين — جامعة طرابلس',

    'temperature' => (float) ($storedAi['AI_TEMPERATURE'] ?? env('AI_TEMPERATURE', 0.3)),

    'max_tokens' => (int) ($storedAi['AI_MAX_TOKENS'] ?? env('AI_MAX_TOKENS', 2048)),
];

