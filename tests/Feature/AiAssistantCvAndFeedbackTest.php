<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\User;
use App\Services\Ai\AiAssistantService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AiAssistantCvAndFeedbackTest extends TestCase
{
    use DatabaseTransactions;

    private function graduate(): User
    {
        $user = User::factory()->create(['role' => 'graduate', 'is_active' => true, 'is_approved' => true]);
        RateLimiter::clear('ai-chat:minute:' . $user->id);

        return $user;
    }

    /** @test */
    public function an_attached_cv_reaches_the_model_but_only_the_short_message_is_shown_and_saved()
    {
        $user = $this->graduate();
        $captured = [];

        $this->mock(AiAssistantService::class, function ($mock) use (&$captured) {
            $mock->shouldReceive('chat')->once()->andReturnUsing(
                function ($user, $sessionId, $message, $onEvent, $modelMessage, $userMeta) use (&$captured) {
                    $captured = compact('message', 'modelMessage', 'userMeta');
                    return ['status' => 'success', 'session_id' => $sessionId, 'message_id' => 1, 'content' => 'تحليل'];
                }
            );
        });

        $this->actingAs($user)->postJson(route('ai-assistant.chat'), [
            'message' => 'حلّل سيرتي الذاتية',
            'attachment_name' => 'my-cv.pdf',
            'attachment_text' => 'مهندس برمجيات - خبرة 3 سنوات في Laravel و Vue',
        ])->assertOk();

        $this->assertSame('حلّل سيرتي الذاتية', $captured['message']);
        $this->assertStringContainsString('خبرة 3 سنوات في Laravel', $captured['modelMessage']);
        $this->assertStringContainsString('my-cv.pdf', $captured['modelMessage']);
        $this->assertSame('my-cv.pdf', $captured['userMeta']['attachment']['name']);
    }

    /** @test */
    public function the_saved_user_message_keeps_the_cv_text_out_of_the_conversation_history()
    {
        $user = $this->graduate();
        // Any configured provider "fails", so the offline engine answers and nothing leaves the machine
        Http::fake(['*' => Http::response('unavailable', 503)]);

        app(AiAssistantService::class)->chat($user, 'cv-session', 'حلّل سيرتي', null, "حلّل سيرتي\n\nنص سري طويل من السيرة", ['attachment' => ['name' => 'cv.pdf']]);

        $saved = AiChatMessage::where('session_id', 'cv-session')->where('role', 'user')->first();
        $this->assertSame('حلّل سيرتي', $saved->content);
        $this->assertSame('cv.pdf', $saved->meta_data['attachment']['name']);
    }

    /** @test */
    public function attachment_text_is_limited_in_size()
    {
        $this->actingAs($this->graduate())->postJson(route('ai-assistant.chat'), [
            'message' => 'حلل',
            'attachment_text' => str_repeat('أ', 20001),
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_can_rate_their_own_reply()
    {
        $user = $this->graduate();
        $reply = AiChatMessage::create([
            'user_id' => $user->id, 'session_id' => 's', 'role' => 'assistant', 'content' => 'رد',
            'meta_data' => ['tool_executed' => 'search_trainings'],
        ]);

        $this->actingAs($user)->postJson(route('ai-assistant.feedback'), ['message_id' => $reply->id, 'value' => 'down'])
            ->assertOk();

        $reply->refresh();
        $this->assertSame('down', $reply->meta_data['feedback']['value']);
        $this->assertSame('search_trainings', $reply->meta_data['tool_executed']); // existing data kept
    }

    /** @test */
    public function a_user_cannot_rate_someone_elses_reply()
    {
        $owner = $this->graduate();
        $reply = AiChatMessage::create(['user_id' => $owner->id, 'session_id' => 's', 'role' => 'assistant', 'content' => 'رد']);

        $this->actingAs($this->graduate())
            ->postJson(route('ai-assistant.feedback'), ['message_id' => $reply->id, 'value' => 'up'])
            ->assertNotFound();

        $this->assertNull($reply->fresh()->meta_data);
    }
}
