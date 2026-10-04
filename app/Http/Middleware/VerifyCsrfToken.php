<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'logout',
        'logout/*',
    ];

    /**
     * Create a new "XSRF-TOKEN" cookie that contains the CSRF token.
     * Enforcing HttpOnly and Secure flags eliminates ZAP security alerts.
     */
    protected function newCookie($request, $config)
    {
        $isSecure = (bool) ($config['secure'] ?? ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https' || app()->environment('production')));

        return new Cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            $this->availableAt(60 * ($config['lifetime'] ?? 120)),
            $config['path'] ?? '/',
            $config['domain'] ?? null,
            $isSecure,
            true,
            false,
            $config['same_site'] ?? 'lax'
        );
    }
}