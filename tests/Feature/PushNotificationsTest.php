<?php

namespace Tests\Feature;

use App\Models\DeviceToken;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PushService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Firebase is faked: no request leaves the machine. A throwaway RSA key stands in for the service account.
 */
class PushNotificationsTest extends TestCase
{
    use DatabaseTransactions;

    private string $credentialsPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Throwaway key used only by these tests (not a real Firebase key)
        $pem = file_get_contents(base_path('tests/fixtures/fcm-test-private-key.pem'));
        $this->credentialsPath = sys_get_temp_dir() . '/fcm-test-' . uniqid() . '.json';
        file_put_contents($this->credentialsPath, json_encode([
            'project_id' => 'gto-test',
            'client_email' => 'push@gto-test.iam.gserviceaccount.com',
            'private_key' => $pem,
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]));
        config(['push.credentials' => $this->credentialsPath, 'push.enabled' => true]);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        @unlink($this->credentialsPath);
        parent::tearDown();
    }

    private function graduate(): User
    {
        return User::factory()->create(['role' => 'graduate', 'is_active' => true, 'is_approved' => true]);
    }

    /** @test */
    public function the_app_registers_a_phone_and_logout_unlinks_it()
    {
        $user = $this->graduate();
        $token = str_repeat('fcm-token-abc', 5);

        $this->actingAs($user)->postJson(route('device-tokens.store'), ['token' => $token])->assertOk();
        $this->assertDatabaseHas('device_tokens', ['user_id' => $user->id, 'token_hash' => DeviceToken::hashOf($token)]);

        $this->actingAs($user)->post(route('logout'));
        $this->assertDatabaseMissing('device_tokens', ['token_hash' => DeviceToken::hashOf($token)]);
    }

    /** @test */
    public function a_phone_moves_to_the_user_who_logs_in_on_it()
    {
        $token = str_repeat('shared-phone-', 5);
        DeviceToken::register($this->graduate(), $token);
        $second = $this->graduate();

        $this->actingAs($second)->postJson(route('device-tokens.store'), ['token' => $token])->assertOk();

        $this->assertSame(1, DeviceToken::where('token_hash', DeviceToken::hashOf($token))->count());
        $this->assertSame($second->id, (int) DeviceToken::where('token_hash', DeviceToken::hashOf($token))->value('user_id'));
    }

    /** @test */
    public function push_is_sent_with_a_signed_firebase_token_and_dead_phones_are_removed()
    {
        $user = $this->graduate();
        $alive = DeviceToken::register($user, str_repeat('alive-token-', 5));
        $dead = DeviceToken::register($user, str_repeat('dead-token-x', 5));

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'ya29.test', 'expires_in' => 3600]),
            'fcm.googleapis.com/*' => function (Request $request) {
                return str_contains($request->body(), 'dead-token-x')
                    ? Http::response(['error' => ['status' => 'NOT_FOUND', 'details' => [['errorCode' => 'UNREGISTERED']]]], 404)
                    : Http::response(['name' => 'projects/gto-test/messages/1']);
            },
        ]);

        $delivered = PushService::sendNow([$user->id], 'عنوان', 'نص الإشعار', ['url' => '/notifications/5']);

        $this->assertSame(1, $delivered);
        $this->assertNotNull(DeviceToken::find($alive->id));
        $this->assertNull(DeviceToken::find($dead->id));

        Http::assertSent(function (Request $r) {
            if (!str_contains($r->url(), 'oauth2.googleapis.com')) return false;
            // A JWT signed with RS256 was exchanged for the access token
            return $r['grant_type'] === 'urn:ietf:params:oauth:grant-type:jwt-bearer' && substr_count($r['assertion'], '.') === 2;
        });
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'projects/gto-test/messages:send')
            && $r->hasHeader('Authorization', 'Bearer ya29.test')
            && json_decode($r->body(), true)['message']['data']['url'] === '/notifications/5');
    }

    /** @test */
    public function push_stays_off_without_the_firebase_file()
    {
        config(['push.credentials' => '/no/such/file.json']);
        $this->assertFalse(PushService::enabled());
    }

    /** @test */
    public function site_notifications_also_go_to_the_phone()
    {
        $user = $this->graduate();
        DeviceToken::register($user, str_repeat('phone-token-', 5));
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'ya29.test']),
            'fcm.googleapis.com/*' => Http::response(['name' => 'ok']),
        ]);

        app(NotificationService::class)->sendToUser($user, 'تم قبول طلبك', 'تفاصيل');

        // Push is sent after the response; in tests the deferred job runs when the app terminates
        $this->app->terminate();

        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'messages:send')
            && json_decode($r->body(), true)['message']['notification']['title'] === 'تم قبول طلبك');
    }
}
