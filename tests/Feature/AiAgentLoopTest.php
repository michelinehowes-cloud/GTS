<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\User;
use App\Services\Ai\AiAgentRunner;
use App\Services\Ai\AiAssistantService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * The AI providers are faked: no request ever leaves the machine and no API credit is used.
 */
class AiAgentLoopTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true, 'is_approved' => true]);
    }

    /** Build a fake Server-Sent-Events body from JSON chunks. */
    private function sse(array ...$chunks): string
    {
        $body = '';
        foreach ($chunks as $chunk) {
            $body .= 'data: ' . json_encode($chunk, JSON_UNESCAPED_UNICODE) . "\n\n";
        }
        return $body . "data: [DONE]\n\n";
    }

    private function collectEvents(array &$events): callable
    {
        return function (string $type, array $data) use (&$events) {
            $events[] = [$type, $data];
        };
    }

    /** @test */
    public function gemini_calls_a_tool_sees_the_result_and_writes_its_own_answer()
    {
        Http::fakeSequence('generativelanguage.googleapis.com/*')
            ->push($this->sse(['candidates' => [['content' => ['parts' => [
                ['functionCall' => ['name' => 'get_trainings_statistics', 'args' => new \stdClass()], 'thoughtSignature' => 'sig-1'],
            ]]]]]))
            ->push($this->sse(
                ['candidates' => [['content' => ['parts' => [['text' => 'لديكم برامج تدريبية نشطة، ']]]]]],
                ['candidates' => [['content' => ['parts' => [['text' => 'وأنصح بزيادة المقاعد.']]]]]]
            ));

        $events = [];
        $runner = new AiAgentRunner(app(AiAssistantService::class), $this->collectEvents($events));
        $result = $runner->run('gemini', 'test-key', $this->admin(), [], 'حلل وضع التدريب', [
            ['name' => 'get_trainings_statistics', 'description' => 'stats', 'parameters' => ['type' => 'object', 'properties' => new \stdClass()]],
        ]);

        $this->assertSame('لديكم برامج تدريبية نشطة، وأنصح بزيادة المقاعد.', $result['content']);
        $this->assertSame('get_trainings_statistics', $result['tool_executed']);

        // The second request sent the tool result back to the model, with the model's own turn unchanged
        Http::assertSentCount(2);
        $recorded = Http::recorded();
        $second = $recorded[1][0]->data();
        $last = end($second['contents']);
        $this->assertArrayHasKey('functionResponse', $last['parts'][0]);
        $this->assertSame('success', $last['parts'][0]['functionResponse']['response']['result']['status']);
        $modelTurn = $second['contents'][count($second['contents']) - 2];
        $this->assertSame('sig-1', $modelTurn['parts'][0]['thoughtSignature']);
        // Empty arguments must go back as an object ({}), never a list ([]) — Gemini rejects lists here
        $this->assertStringContainsString('"args":{}', $recorded[1][0]->body());
        $this->assertStringNotContainsString('"args":[]', $recorded[1][0]->body());

        // Live events: tool status, then the text as it streamed
        $types = array_column($events, 0);
        $this->assertSame('status', $types[0]);
        $this->assertContains('delta', $types);
    }

    /** @test */
    public function groq_streams_tool_call_arguments_in_pieces_and_runs_several_steps()
    {
        Http::fakeSequence('api.groq.com/*')
            ->push($this->sse(
                ['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'id' => 'call_1', 'function' => ['name' => 'get_trainings_statistics', 'arguments' => '{']]]]]]],
                ['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'function' => ['arguments' => '}']]]]]]]
            ))
            ->push($this->sse(
                ['choices' => [['delta' => ['tool_calls' => [['index' => 0, 'id' => 'call_2', 'function' => ['name' => 'get_jobs_statistics', 'arguments' => '{}']]]]]]]
            ))
            ->push($this->sse(
                ['choices' => [['delta' => ['content' => 'مقارنة: ']]]],
                ['choices' => [['delta' => ['content' => 'التدريب أنشط من التوظيف.']]]]
            ));

        $runner = new AiAgentRunner(app(AiAssistantService::class));
        $result = $runner->run('groq', 'test-key', $this->admin(), [], 'قارن التدريب بالتوظيف', [
            ['name' => 'get_trainings_statistics', 'description' => 'a', 'parameters' => ['type' => 'object', 'properties' => new \stdClass()]],
            ['name' => 'get_jobs_statistics', 'description' => 'b', 'parameters' => ['type' => 'object', 'properties' => new \stdClass()]],
        ]);

        $this->assertSame('مقارنة: التدريب أنشط من التوظيف.', $result['content']);
        $this->assertSame('get_trainings_statistics، get_jobs_statistics', $result['tool_executed']);
        Http::assertSentCount(3);

        $third = Http::recorded()[2][0]->data();
        $toolMessages = array_values(array_filter($third['messages'], fn ($m) => $m['role'] === 'tool'));
        $this->assertCount(2, $toolMessages);
        $this->assertSame('call_1', $toolMessages[0]['tool_call_id']);
    }

    /** @test */
    public function an_action_that_changes_data_stops_the_loop_and_waits_for_confirmation()
    {
        Http::fakeSequence('api.groq.com/*')
            ->push($this->sse(['choices' => [['delta' => ['tool_calls' => [[
                'index' => 0, 'id' => 'call_1',
                'function' => ['name' => 'draft_announcement', 'arguments' => '{"title":"إعلان","content":"نص الإعلان"}'],
            ]]]]]]))
            ->push($this->sse(['choices' => [['delta' => ['content' => 'should never be requested']]]]));

        $runner = new AiAgentRunner(app(AiAssistantService::class));
        $result = $runner->run('groq', 'test-key', $this->admin(), [], 'انشر إعلاناً', [
            ['name' => 'draft_announcement', 'description' => 'x', 'parameters' => ['type' => 'object', 'properties' => new \stdClass()]],
        ]);

        Http::assertSentCount(1);
        $this->assertSame('proposal', $result['action_proposal']['status']);
    }

    /** @test */
    public function provider_failure_returns_null_so_the_next_provider_or_offline_engine_is_used()
    {
        Http::fake(['api.groq.com/*' => Http::response(['error' => 'down'], 500)]);

        $runner = new AiAgentRunner(app(AiAssistantService::class));
        $this->assertNull($runner->run('groq', 'test-key', $this->admin(), [], 'مرحبا', []));
    }

    /** @test */
    public function the_conversation_history_is_sent_once_without_duplicating_the_new_message()
    {
        Http::fake(['api.groq.com/*' => Http::response($this->sse(['choices' => [['delta' => ['content' => 'أهلاً']]]]), 200)]);

        $runner = new AiAgentRunner(app(AiAssistantService::class));
        $runner->run('groq', 'test-key', $this->admin(), [
            ['role' => 'user', 'content' => 'سؤال سابق'],
            ['role' => 'assistant', 'content' => 'جواب سابق'],
        ], 'سؤال جديد', []);

        Http::assertSent(function (Request $request) {
            $contents = array_column($request->data()['messages'], 'content');
            return count(array_keys($contents, 'سؤال جديد', true)) === 1
                && in_array('سؤال سابق', $contents, true)
                && in_array('جواب سابق', $contents, true);
        });
    }

    /** @test */
    public function stream_endpoint_sends_live_events_and_saves_the_conversation()
    {
        $user = $this->admin();
        RateLimiter::clear('ai-chat:minute:' . $user->id);

        $this->mock(AiAssistantService::class, function ($mock) {
            $mock->shouldReceive('chat')->once()->andReturnUsing(function ($user, $sessionId, $message, $onEvent) {
                $onEvent('status', ['label' => 'يجمع إحصائيات التدريب…']);
                $onEvent('delta', ['text' => 'جزء أول']);
                return ['status' => 'success', 'session_id' => $sessionId, 'content' => 'الرد الكامل', 'action_proposal' => null, 'tool_executed' => null];
            });
        });

        $response = $this->actingAs($user)->post(route('ai-assistant.stream'), ['message' => 'مرحبا', 'session_id' => 's-1']);
        $response->assertOk();
        $this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));

        ob_start();
        $response->baseResponse->sendContent();
        $body = ob_get_clean();

        $this->assertStringContainsString("event: status", $body);
        $this->assertStringContainsString("event: delta", $body);
        $this->assertStringContainsString("event: done", $body);
        $this->assertStringContainsString('الرد الكامل', $body);
    }

    /** @test */
    public function usage_is_limited_per_minute()
    {
        $user = User::factory()->create(['role' => 'graduate', 'is_active' => true, 'is_approved' => true]);
        config(['ai.rate_limits.per_minute' => 2]);
        RateLimiter::clear('ai-chat:minute:' . $user->id);

        $this->mock(AiAssistantService::class, function ($mock) {
            $mock->shouldReceive('chat')->andReturn(['status' => 'success', 'session_id' => 's', 'content' => 'ok']);
        });

        $this->actingAs($user)->postJson(route('ai-assistant.chat'), ['message' => 'a'])->assertOk();
        $this->actingAs($user)->postJson(route('ai-assistant.chat'), ['message' => 'b'])->assertOk();
        $this->actingAs($user)->postJson(route('ai-assistant.chat'), ['message' => 'c'])
            ->assertStatus(429)
            ->assertJson(['status' => 'rate_limited']);

        RateLimiter::clear('ai-chat:minute:' . $user->id);
    }
}
