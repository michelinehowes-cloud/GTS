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

    /*
    |--------------------------------------------------------------------------
    | Agent loop
    |--------------------------------------------------------------------------
    | The model may call several tools in a row (up to max_steps) and sees each
    | result before answering. history_messages = how much of the conversation
    | it remembers.
    */
    'agent' => [
        'max_steps' => (int) env('AI_AGENT_MAX_STEPS', 5),
        'history_messages' => (int) env('AI_HISTORY_MESSAGES', 20),
        'history_message_chars' => 2000,
        'max_tool_result_chars' => 12000,
        'request_timeout' => 90,
        // Gemini 3 thinking depth: low (fast) | high (slower, deeper). Empty = provider default.
        'gemini_thinking_level' => env('AI_GEMINI_THINKING', 'low'),
        // Groq gpt-oss reasoning effort (low | medium | high) and the minimum answer budget for reasoning models
        'groq_reasoning_effort' => env('AI_GROQ_REASONING', 'low'),
        'reasoning_min_tokens' => 4096,
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage limits (protect the API budget)
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'per_minute' => (int) env('AI_RATE_PER_MINUTE', 15),
        'per_day' => (int) env('AI_RATE_PER_DAY', 150),
        'per_day_admin' => (int) env('AI_RATE_PER_DAY_ADMIN', 400),
    ],
];

