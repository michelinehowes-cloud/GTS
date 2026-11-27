<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdaptiveChallengeTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Adaptive#Pass123';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'security.turnstile.enabled' => true,
            'security.turnstile.force_testing' => true,
            'security.turnstile.site_key' => '0x4AAAAAAtest_site_key',
            'security.turnstile.secret_key' => '0x4AAAAAAtest_secret_key',
        ]);

        // A real request to Cloudflare must never happen in tests
        Http::fake([
            'https://challenges.cloudflare.com/*' => Http::response(['success' => true], 200),
        ]);
    }

    private function makeUser(): User
    {
        return User::factory()->create([
            'email' => 'adaptive-' . uniqid() . '@example.com',
            'role' => 'admin',
            'is_active' => true,
            'is_approved' => true,
            'password' => Hash::make(self::PASSWORD),
        ]);
    }

    /** Fields a real browser sends from the login form (honeypot time token filled 5s ago). */
    private function humanFields(array $extra = []): array
    {
        return array_merge([
            '_hp_time_token' => encrypt(microtime(true) - 5),
        ], $extra);
    }

    private function clearCounters(string $email): void
    {
        RateLimiter::clear('security-challenge:login:ip:' . sha1('127.0.0.1'));
        RateLimiter::clear('security-challenge:login:id:' . sha1($email));
        RateLimiter::clear(strtolower($email) . '|127.0.0.1');
    }

    /** @test */
    public function normal_login_needs_no_cloudflare_check()
    {
        $user = $this->makeUser();
        $this->clearCounters($user->email);

        $this->post('/login', $this->humanFields([
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]))->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
        Http::assertNothingSent();
    }

    /** @test */
    public function cloudflare_check_is_required_after_three_failed_logins()
    {
        $user = $this->makeUser();
        $this->clearCounters($user->email);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', $this->humanFields([
                'email' => $user->email,
                'password' => 'wrong-password',
            ]))->assertSessionHasErrors('email');
        }

        // Correct password but no Cloudflare token: rejected, widget now required
        $this->post('/login', $this->humanFields([
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]))->assertSessionHasErrors('cf-turnstile-response');
        $this->assertGuest();

        // The login form now renders the Cloudflare widget
        $this->get('/')->assertSee('cf-turnstile', false);

        // With a valid Cloudflare token the login succeeds
        $this->post('/login', $this->humanFields([
            'email' => $user->email,
            'password' => self::PASSWORD,
            'cf-turnstile-response' => 'valid-token-from-widget',
        ]))->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->clearCounters($user->email);
    }

    /** @test */
    public function request_without_honeypot_time_token_must_pass_cloudflare()
    {
        $user = $this->makeUser();
        $this->clearCounters($user->email);

        $this->post('/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    /** @test */
    public function widget_is_not_rendered_for_normal_visitors()
    {
        RateLimiter::clear('security-challenge:login:ip:' . sha1('127.0.0.1'));
        RateLimiter::clear('security-challenge:forgot_password:ip:' . sha1('127.0.0.1'));
        RateLimiter::clear('security-challenge:register_graduate:ip:' . sha1('127.0.0.1'));

        $this->get('/')
            ->assertOk()
            ->assertDontSee('challenges.cloudflare.com/turnstile', false);
    }
}
