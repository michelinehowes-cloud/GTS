<?php

// قراءة الإعدادات الديناميكية المحفوظة من لوحة التحكم في حال وجودها
$storedSecurity = [];
$settingsPath = storage_path('app/ai_settings.json');
if (file_exists($settingsPath)) {
    $storedSecurity = json_decode(@file_get_contents($settingsPath), true) ?: [];
}

$siteKey = $storedSecurity['TURNSTILE_SITE_KEY'] ?? env('TURNSTILE_SITE_KEY', '');
$secretKey = $storedSecurity['TURNSTILE_SECRET_KEY'] ?? env('TURNSTILE_SECRET_KEY', '');

// يتم تفعيل كاشف الروبوتات فقط في حال توفر المفاتيح صراحة
$turnstileExplicitlyEnabled = $storedSecurity['TURNSTILE_ENABLED'] ?? env('TURNSTILE_ENABLED', null);
$turnstileEnabled = ($turnstileExplicitlyEnabled === null) 
    ? (!empty($siteKey) && !empty($secretKey)) 
    : (bool) ($turnstileExplicitlyEnabled && !empty($siteKey) && !empty($secretKey));

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
        'enabled' => $turnstileEnabled,
        'site_key' => $siteKey,
        'secret_key' => $secretKey,
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
        'register_max_attempts' => 10,
        'register_decay_minutes' => 5,
    ],
];
