<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Configuration
    |--------------------------------------------------------------------------
    |
    | Cloudflare Turnstile provides smart, invisible bot protection.
    | Official test keys:
    | Sitekey: 1x00000000000000000000AA (Always passes)
    | Secret:  1x0000000000000000000000000000000AA
    |
    */
    'turnstile' => [
        'enabled' => env('TURNSTILE_ENABLED', true),
        'site_key' => env('TURNSTILE_SITE_KEY', '1x00000000000000000000AA'),
        'secret_key' => env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'),
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
