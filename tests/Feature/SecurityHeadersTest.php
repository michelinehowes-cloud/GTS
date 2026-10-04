<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * التحقق من وجود رؤوس الأمان (CSP, X-Content-Type-Options, etc.) في كافة الردود
     */
    public function test_security_headers_present_on_all_responses()
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'geolocation=(), camera=(self), microphone=(), payment=()');
        
        // التحقق من وجود سياسة أمان المحتوى CSP
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("script-src", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);

        // التحقق من إخفاء معلومات إصدار السيرفر و PHP
        $this->assertFalse($response->headers->has('X-Powered-By'));
    }

    /**
     * التحقق من تفعيل رأس HSTS على اتصالات HTTPS
     */
    public function test_hsts_is_present_on_https_requests()
    {
        $response = $this->get('/', [
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);

        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    /**
     * التحقق من تفريغ محتوى ردود التحويل (3xx) لحل تنبيه Big Redirect
     */
    public function test_big_redirect_body_is_empty()
    {
        $response = $this->get('/home');

        $this->assertTrue($response->isRedirection());
        $this->assertEmpty($response->getContent());
    }

    /**
     * التحقق من أن صفحات الخطأ (404) تحتوي على رؤوس الأمان ولا تسرب أي معلومات للنظام
     */
    public function test_custom_error_pages_rendered_with_security_headers_and_no_disclosure()
    {
        $response = $this->get('/non-existent-route-for-testing-404');

        $response->assertStatus(404);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));

        // التأكد من عدم تسريب معلومات بيئة الخادم أو ملفات النظام
        $content = $response->getContent();
        $this->assertStringContainsString('404', $content);
        $this->assertStringContainsString('الصفحة غير موجودة', $content);
        $this->assertStringNotContainsString('Stack trace:', $content);
        $this->assertStringNotContainsString('APP_KEY', $content);
        $this->assertStringNotContainsString('DB_PASSWORD', $content);
    }

    /**
     * التحقق من أن كوكي XSRF-TOKEN يحمل علامة HttpOnly
     */
    public function test_xsrf_cookie_has_httponly_flag()
    {
        $response = $this->get('/');

        $foundXsrf = false;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'XSRF-TOKEN') {
                $foundXsrf = true;
                $this->assertTrue($cookie->isHttpOnly(), 'XSRF-TOKEN cookie must have HttpOnly flag set to true');
            }
        }

        $this->assertTrue($foundXsrf, 'XSRF-TOKEN cookie should be set in response');
    }
}
