<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\SecurityService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TurnstileEnforcementTest extends TestCase
{
    protected function fakePassword(): string
    {
        return 'Sec_' . Str::random(12) . '1!';
    }

    /** @test */
    public function empty_or_bypass_turnstile_token_is_strictly_rejected_when_keys_are_configured()
    {
        config([
            'security.turnstile.enabled' => true,
            'security.turnstile.force_testing' => true,
            'security.turnstile.site_key' => '0x4AAAAAAtest_site_key',
            'security.turnstile.secret_key' => '0x4AAAAAAtest_secret_key',
        ]);

        $service = new SecurityService();

        // 1. Empty token
        $resEmpty = $service->verifyTurnstile(null, '127.0.0.1');
        $this->assertFalse($resEmpty['success']);

        // 2. Bypass token
        $resBypass = $service->verifyTurnstile('BYPASS_TIMEOUT', '127.0.0.1');
        $this->assertFalse($resBypass['success']);
    }

    /** @test */
    public function failed_cloudflare_api_verification_rejects_login()
    {
        config([
            'security.turnstile.enabled' => true,
            'security.turnstile.force_testing' => true,
            'security.turnstile.site_key' => '0x4AAAAAAtest_site_key',
            'security.turnstile.secret_key' => '0x4AAAAAAtest_secret_key',
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $service = new SecurityService();
        $result = $service->verifyTurnstile('invalid_client_token', '127.0.0.1');

        $this->assertFalse($result['success']);
    }

    /** @test */
    public function successful_cloudflare_api_verification_passes()
    {
        config([
            'security.turnstile.enabled' => true,
            'security.turnstile.force_testing' => true,
            'security.turnstile.site_key' => '0x4AAAAAAtest_site_key',
            'security.turnstile.secret_key' => '0x4AAAAAAtest_secret_key',
        ]);

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toISOString(),
            ], 200),
        ]);

        $service = new SecurityService();
        $result = $service->verifyTurnstile('valid_client_token', '127.0.0.1');

        $this->assertTrue($result['success']);
    }
}
