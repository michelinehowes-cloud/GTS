<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isHttps = $request->isSecure() 
            || $request->header('X-Forwarded-Proto') === 'https' 
            || app()->environment('production');

        // 1. إزالة الرؤوس التي تسرب معلومات عن الخادم وإصدار PHP
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }

        // 2. رؤوس الحماية الأساسية
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(self), microphone=(), payment=()');

        // 3. فرض اتصال آمن مشفر HSTS في بيئة الإنتاج و HTTPS
        if ($isHttps) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 4. سياسة أمان المحتوى (Content Security Policy - CSP) المتوافقة مع مكونات المنظومة
        $csp = "default-src 'self' https: data: blob:; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://code.jquery.com https://challenges.cloudflare.com; "
            . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.bunny.net https://unpkg.com; "
            . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://fonts.bunny.net; "
            . "img-src 'self' data: blob: https:; "
            . "connect-src 'self' https://challenges.cloudflare.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; "
            . "frame-src 'self' https://challenges.cloudflare.com; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "form-action 'self' https:;";

        $response->headers->set('Content-Security-Policy', $csp);

        // 5. معالجة Big Redirect لمنع تسريب أي محتوى في ردود إعادة التوجيه 3xx
        if ($response->isRedirection()) {
            $response->setContent('');
        }

        // 6. تشديد أمان ملفات تعريف الارتباط (Cookies)
        foreach ($response->headers->getCookies() as $cookie) {
            if ($isHttps && !$cookie->isSecure()) {
                $response->headers->setCookie($cookie->withSecure(true));
            }
        }

        return $response;
    }
}
