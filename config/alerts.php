<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Failure alerts (e-mail)
    |--------------------------------------------------------------------------
    | Sent when a server error (500) happens or the AI service is unreachable.
    | ALERT_EMAIL may hold several addresses separated by commas; when empty the
    | main administrator account receives the alerts.
    */
    'enabled' => env('ALERTS_ENABLED', true),
    'email' => env('ALERT_EMAIL', ''),
    'repeat_minutes' => (int) env('ALERT_REPEAT_MINUTES', 30),
    'max_per_hour' => (int) env('ALERT_MAX_PER_HOUR', 10),
];
