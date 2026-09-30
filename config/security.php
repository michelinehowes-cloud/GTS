<?php

// قراءة الإعدادات الديناميكية المحفوظة من لوحة التحكم في حال وجودها
$storedSecurity = [];
$settingsPath = storage_path('app/ai_settings.json');
if (file_exists($settingsPath)) {
    $storedSecurity = json_decode(@file_get_contents($settingsPath), true) ?: [];
}

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Configuration
    |--------------------------------------------------------------------------
    |
    | Cloudflare Turnstile provides smart, invisible bot protection.
    |
    */
    'turnstile' => [
        'enabled' => $storedSecurity['TURNSTILE_ENABLED'] ?? env('TURNSTILE_ENABLED', true),
        'site_key' => $storedSecurity['TURNSTILE_SITE_KEY'] ?? env('TURNSTILE_SITE_KEY', '0x4AAAAAAE90uR_z3hQgBnzU'),
        'secret_key' => $storedSecurity['TURNSTILE_SECRET_KEY'] ?? env('TURNSTILE_SECRET_KEY', '0x4AAAAAAE90uVPOzkse-syBFQqqEtsWNjo'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Honeypot Bot Trap Configuration
    |--------------------------------------------------------------------------
    |
    | Invisible fields to trap automated scripts and spam bots instantly.
    |
    */
    'honeypot' => [
        'enabled' => env('HONEYPOT_ENABLED', true),
        'field_name' => '_hp_security_check',
        'time_field' => '_hp_time_token',
        'min_seconds' => 1.0, // Minimum time in seconds to fill a human form
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'login_max_attempts' => 5,
        'login_decay_minutes' => 1,
        'register_max_attempts' => 3,
        'register_decay_minutes' => 5,
    ],
];
