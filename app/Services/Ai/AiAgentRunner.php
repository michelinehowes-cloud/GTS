<?php

namespace App\Services\Ai;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Agent loop for the AI assistant.
 *
 * The model can call several tools in a row and sees every tool result before deciding the next step,
 * then writes its own answer (analysis, comparison, recommendations) instead of a fixed template.
 * Text is streamed from the provider and forwarded through $onEvent as it arrives.
 *
 * Safety: any tool that returns a "proposal" (an action that changes data) stops the loop and is
 * shown to the user for confirmation, exactly as before.
 */
class AiAgentRunner
{
    /** Tools whose formatted result contains interactive cards (apply buttons etc.) */
    public const STRUCTURED_UI_TOOLS = [
        'search_job_opportunities',
        'search_trainings',
        'get_my_applications',
        'get_my_profile',
        'search_partner_companies',
    ];

    /** Short Arabic labels shown while a tool runs ("🔍 يبحث في فرص العمل…") */
    public const TOOL_LABELS = [
        'search_job_opportunities' => 'يبحث في فرص العمل',
        'search_trainings' => 'يبحث في البرامج التدريبية',
        'search_graduates_advanced' => 'يبحث في بيانات الخريجين',
        'get_graduates_statistics' => 'يجمع إحصائيات الخريجين',
        'get_trainings_statistics' => 'يجمع إحصائيات التدريب',
        'get_jobs_statistics' => 'يجمع إحصائيات الوظائف',
        'get_system_overview' => 'يراجع نظرة عامة على المنظومة',
        'get_partner_companies' => 'يراجع الشركات الشريكة',
        'search_partner_companies' => 'يبحث في الشركات الشريكة',
        'get_employment_analytics' => 'يحلل بيانات التوظيف',
        'get_my_profile' => 'يراجع ملفك الشخصي',
        'get_my_applications' => 'يراجع طلباتك',
        'get_latest_news' => 'يراجع آخر الأخبار',
        'get_active_announcements' => 'يراجع الإعلانات',
    ];

    protected AiAssistantService $service;

    /** @var callable|null */
    protected $onEvent;

    protected bool $emittedText = false;

    /** The current question (used to tailor the instructions) */
    protected string $currentMessage = '';

    public function __construct(AiAssistantService $service, ?callable $onEvent = null)
    {
        $this->service = $service;
        $this->onEvent = $onEvent;
    }

    /**
     * @param array $history  previous messages: [['role' => 'user'|'assistant', 'content' => string], ...]
     * @return array|null     response array, or null when the provider failed before producing anything
     */
    public function run(string $provider, string $apiKey, User $user, array $history, string $userMessage, array $tools): ?array
    {
        $this->emittedText = false;
        $this->currentMessage = $userMessage;

        // Send only the tools relevant to this question: keeps requests within free-tier token limits
        $tools = AiToolSelector::select($tools, $userMessage, $history, (int) config('ai.agent.max_tools', 12));

        try {
            return $provider === 'gemini'
                ? $this->runGemini($apiKey, $user, $history, $userMessage, $tools)
                : $this->runGroq($apiKey, $user, $history, $userMessage, $tools);
        } catch (\Throwable $e) {
            Log::error("AI agent ({$provider}) exception: " . $e->getMessage());
            if ($this->emittedText) {
                return [
                    'content' => "\n\n⚠️ انقطع الاتصال بخدمة الذكاء الاصطناعي قبل اكتمال الرد. يرجى إعادة المحاولة.",
                    'tool_calls' => null,
                    'tool_results' => null,
                ];
            }
            return null;
        }
    }

    // ------------------------------------------------------------------ Gemini

    protected function runGemini(string $apiKey, User $user, array $history, string $userMessage, array $tools): ?array
    {
        $model = config('ai.gemini_model', 'gemini-3.6-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?alt=sse&key={$apiKey}";

        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $declarations = array_map(fn ($t) => [
            'name' => $t['name'],
            'description' => $t['description'],
            'parameters' => $t['parameters'],
        ], $tools);

        $state = new AgentState();
        $maxSteps = max(1, (int) config('ai.agent.max_steps', 5));

        for ($step = 1; $step <= $maxSteps; $step++) {
            $lastStep = $step === $maxSteps;

            $payload = [
                'systemInstruction' => ['parts' => [['text' => $this->systemInstruction($user)]]],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => config('ai.temperature', 0.3),
                    'maxOutputTokens' => config('ai.max_tokens', 2048),
                ],
            ];
            // Gemini 3 "thinks" before answering; a low level keeps replies fast (set AI_GEMINI_THINKING=high for harder questions)
            if ($thinking = config('ai.agent.gemini_thinking_level')) {
                $payload['generationConfig']['thinkingConfig'] = ['thinkingLevel' => $thinking];
            }
            if (!empty($declarations)) {
                $payload['tools'] = [['functionDeclarations' => $declarations]];
                if ($lastStep) {
                    $payload['toolConfig'] = ['functionCallingConfig' => ['mode' => 'NONE']];
                }
            }

            $textParts = '';
            $firstTextPart = null;
            $callParts = [];
            $ok = $this->streamSse($url, $payload, null, function (array $chunk) use (&$textParts, &$firstTextPart, &$callParts) {
                foreach ($chunk['candidates'][0]['content']['parts'] ?? [] as $part) {
                    if (isset($part['functionCall'])) {
                        // Kept as-is (includes any thought signature the model requires back).
                        // JSON decoding turns "args": {} into [], which Gemini rejects; restore it as an object.
                        if (empty($part['functionCall']['args'])) {
                            $part['functionCall']['args'] = new \stdClass();
                        }
                        $callParts[] = $part;
                    } elseif (isset($part['text']) && empty($part['thought'])) {
                        $firstTextPart ??= $part;
                        $textParts .= $part['text'];
                        $this->emitText($part['text']);
                    }
                }
            });

            if (!$ok) {
                return $state->isEmpty() ? null : $this->finish($state, $user);
            }

            $state->addText($textParts);

            if (empty($callParts)) {
                if (trim($textParts) === '') {
                    Log::warning('AI agent: empty answer from gemini', [
                        'model' => $model,
                        'step' => $step,
                        'tools_run' => array_column($state->tools, 'name'),
                    ]);
                }
                break;
            }

            // Record the model turn exactly as returned, then answer every call
            $modelParts = [];
            if ($textParts !== '') {
                $modelParts[] = array_merge($firstTextPart ?? [], ['text' => $textParts]);
            }
            $contents[] = ['role' => 'model', 'parts' => array_merge($modelParts, $callParts)];

            $responseParts = [];
            foreach ($callParts as $part) {
                $name = $part['functionCall']['name'] ?? '';
                $args = (array) ($part['functionCall']['args'] ?? []);
                $result = $this->runTool($user, $name, $args, $state);
                $responseParts[] = ['functionResponse' => ['name' => $name, 'response' => ['result' => $this->compactResult($result)]]];

                if ($state->proposal) {
                    return $this->finish($state, $user);
                }
            }
            $contents[] = ['role' => 'user', 'parts' => $responseParts];
        }

        return $this->finish($state, $user);
    }

    // ------------------------------------------------------------------ Groq (OpenAI-compatible)

    protected function runGroq(string $apiKey, User $user, array $history, string $userMessage, array $tools): ?array
    {
        $model = config('ai.groq_model', 'openai/gpt-oss-120b');
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $messages = [['role' => 'system', 'content' => $this->systemInstruction($user)]];
        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'] === 'assistant' ? 'assistant' : 'user', 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $groqTools = array_map(fn ($t) => [
            'type' => 'function',
            'function' => [
                'name' => $t['name'],
                'description' => $t['description'],
                'parameters' => (object) ($t['parameters'] ?? new \stdClass()),
            ],
        ], $tools);

        $state = new AgentState();
        $maxSteps = max(1, (int) config('ai.agent.max_steps', 5));

        // Reasoning models (gpt-oss, qwen3...) "think" before answering and that thinking counts against
        // max_tokens; with a small budget they can spend it all and return an empty answer.
        $isReasoningModel = (bool) preg_match('/gpt-oss|qwen3|deepseek-r1|reason/i', $model);

        for ($step = 1; $step <= $maxSteps; $step++) {
            $payload = [
                'model' => $model,
                'messages' => $messages,
                'temperature' => config('ai.temperature', 0.3),
                'max_tokens' => $isReasoningModel
                    ? max((int) config('ai.max_tokens', 2048), (int) config('ai.agent.reasoning_min_tokens', 4096))
                    : (int) config('ai.max_tokens', 2048),
                'stream' => true,
            ];
            if ($isReasoningModel && str_contains($model, 'gpt-oss')) {
                $payload['reasoning_effort'] = config('ai.agent.groq_reasoning_effort', 'low');
            }
            if (!empty($groqTools)) {
                $payload['tools'] = $groqTools;
                $payload['tool_choice'] = $step === $maxSteps ? 'none' : 'auto';
            }

            $text = '';
            $calls = [];
            $finishReason = null;
            $ok = $this->streamSse($url, $payload, $apiKey, function (array $chunk) use (&$text, &$calls, &$finishReason) {
                $finishReason = $chunk['choices'][0]['finish_reason'] ?? $finishReason;
                $delta = $chunk['choices'][0]['delta'] ?? [];
                if (isset($delta['content']) && $delta['content'] !== '') {
                    $text .= $delta['content'];
                    $this->emitText($delta['content']);
                }
                foreach ($delta['tool_calls'] ?? [] as $tc) {
                    $i = $tc['index'] ?? 0;
                    $calls[$i]['id'] = $tc['id'] ?? ($calls[$i]['id'] ?? 'call_' . $i);
                    $calls[$i]['name'] = ($calls[$i]['name'] ?? '') . ($tc['function']['name'] ?? '');
                    $calls[$i]['arguments'] = ($calls[$i]['arguments'] ?? '') . ($tc['function']['arguments'] ?? '');
                }
            }, $step === 1 ? $model : null);

            if (!$ok) {
                return $state->isEmpty() ? null : $this->finish($state, $user);
            }

            $state->addText($text);

            if (empty($calls)) {
                if (trim($text) === '') {
                    Log::warning('AI agent: empty answer from groq', [
                        'model' => $model,
                        'step' => $step,
                        'finish_reason' => $finishReason,
                        'tools_run' => array_column($state->tools, 'name'),
                    ]);
                }
                break;
            }

            $messages[] = [
                'role' => 'assistant',
                'content' => $text,
                'tool_calls' => array_values(array_map(fn ($c) => [
                    'id' => $c['id'],
                    'type' => 'function',
                    'function' => ['name' => $c['name'], 'arguments' => $c['arguments'] ?: '{}'],
                ], $calls)),
            ];

            foreach ($calls as $c) {
                $args = json_decode($c['arguments'] ?: '{}', true);
                $result = $this->runTool($user, $c['name'], is_array($args) ? $args : [], $state);
                $messages[] = [
                    'role' => 'tool',
                    'tool_call_id' => $c['id'],
                    'content' => json_encode($this->compactResult($result), JSON_UNESCAPED_UNICODE),
                ];

                if ($state->proposal) {
                    return $this->finish($state, $user);
                }
            }
        }

        return $this->finish($state, $user);
    }

    // ------------------------------------------------------------------ shared helpers

    protected function runTool(User $user, string $name, array $args, AgentState $state): array
    {
        $this->emit('status', [
            'tool' => $name,
            'label' => (self::TOOL_LABELS[$name] ?? 'ينفّذ: ' . $name) . '…',
        ]);

        $result = AiToolRegistry::executeTool($user, $name, $args);
        $state->addTool($name, $args, $result);

        if (($result['status'] ?? null) === 'proposal') {
            $state->proposal = $result;
            $state->proposalTool = $name;
        }

        return $result;
    }

    /** Build the final response array from what the loop collected. */
    protected function finish(AgentState $state, User $user): ?array
    {
        $content = trim($state->text);

        // A write action waits for the user's confirmation; show it with its usual card text.
        if ($state->proposal) {
            $fallback = $this->service->formatToolResultFallback($state->proposalTool, $state->proposal, $user);
            $content = $content !== '' ? $content . "\n\n" . $fallback : $fallback;
            $this->emitReplace($content);
        } elseif (count($state->tools) === 1
            && in_array($state->tools[0]['name'], self::STRUCTURED_UI_TOOLS, true)
            && ($state->tools[0]['result']['status'] ?? null) === 'success') {
            // Single search with interactive cards: the model's summary + the cards with their buttons
            $cards = $this->service->formatToolResultFallback($state->tools[0]['name'], $state->tools[0]['result'], $user);
            $content = $content !== '' ? $content . "\n\n" . $cards : $cards;
            $this->emitReplace($content);
        } elseif ($content === '') {
            if (empty($state->tools)) {
                return null;
            }
            $last = end($state->tools);
            $content = $this->service->formatToolResultFallback($last['name'], $last['result'], $user);
            $this->emitReplace($content);
        }

        $names = array_values(array_unique(array_column($state->tools, 'name')));

        return [
            'content' => $content,
            'tool_calls' => $names ? array_map(fn ($t) => ['name' => $t['name'], 'args' => $t['args']], $state->tools) : null,
            'tool_results' => $names ? array_map(fn ($t) => ['tool' => $t['name'], 'result' => $this->compactResult($t['result'])], $state->tools) : null,
            'tool_executed' => $names ? implode('، ', $names) : null,
            'action_proposal' => $state->proposal,
        ];
    }

    protected function systemInstruction(User $user): string
    {
        return $this->service->buildSystemInstruction($user, $this->currentMessage) . "\n" .
            "قواعد التفكير والأدوات:\n" .
            "• يمكنك استدعاء عدة أدوات متتالية قبل الإجابة، وسترى نتيجة كل أداة. اجمع ما تحتاجه ثم حلّل وقارن وقدّم توصيات واضحة مبنية على الأرقام الفعلية.\n" .
            "• لا تخترع أرقاماً أو أسماء غير موجودة في نتائج الأدوات. إذا كانت البيانات ناقصة فقل ذلك بوضوح.\n" .
            "• عند البحث عن وظائف أو دورات تُعرض البطاقات التفاعلية تلقائياً أسفل ردك، فاكتب ملخصاً قصيراً ولا تكرر قائمة العناصر كاملة.\n" .
            "• الإجراءات التي تغيّر البيانات تُعرض على المستخدم للتأكيد قبل تنفيذها؛ لا تقل إنها نُفذت.\n";
    }

    /** Keep tool results small enough for the model (large lists are trimmed). */
    protected function compactResult(array $result): array
    {
        $limit = (int) config('ai.agent.max_tool_result_chars', 12000);
        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
        if ($json === false || mb_strlen($json) <= $limit) {
            return $result;
        }

        return [
            'status' => $result['status'] ?? 'success',
            'note' => 'تم اختصار النتيجة لطولها؛ هذا جزء منها فقط.',
            'partial_json' => mb_substr($json, 0, $limit),
        ];
    }

    /**
     * POST a streaming request and feed every SSE "data:" JSON chunk to $onChunk.
     * Returns false when the request failed before any data arrived.
     */
    protected function streamSse(string $url, array $payload, ?string $bearer, callable $onChunk, ?string $groqModelForFallback = null): bool
    {
        $request = Http::withOptions(['stream' => true])
            ->connectTimeout(10)
            ->timeout((int) config('ai.agent.request_timeout', 90))
            ->acceptJson();
        if ($bearer) {
            $request = $request->withToken($bearer);
        }

        $response = $request->post($url, $payload);

        // Groq: retired/unknown model -> try known alternatives once
        if ($response->status() === 404 && $groqModelForFallback) {
            foreach (['openai/gpt-oss-120b', 'llama-3.3-70b-versatile', 'openai/gpt-oss-20b', 'llama-3.1-8b-instant'] as $alt) {
                if ($alt === $groqModelForFallback) {
                    continue;
                }
                $payload['model'] = $alt;
                $response = $request->post($url, $payload);
                if ($response->successful()) {
                    Log::info("Groq fell back to model {$alt}");
                    break;
                }
            }
        }

        // Per-minute token limit: wait briefly and retry once when the provider says it is a short wait
        if ($response->status() === 429) {
            $wait = null;
            if (preg_match('/try again in ([\d.]+)s/i', (string) $response->body(), $m)) {
                $wait = (float) $m[1];
            } elseif ($response->header('retry-after')) {
                $wait = (float) $response->header('retry-after');
            }
            if ($wait !== null && $wait <= (float) config('ai.agent.max_rate_limit_wait', 12)) {
                $this->emit('status', ['tool' => null, 'label' => 'الخدمة مشغولة لحظياً، إعادة المحاولة…']);
                if (!app()->runningUnitTests()) {
                    usleep((int) (($wait + 0.5) * 1000000));
                }
                $response = $request->post($url, $payload);
            }
        }

        if (!$response->successful()) {
            Log::warning('AI provider error (' . $response->status() . '): ' . mb_substr((string) $response->body(), 0, 500));
            return false;
        }

        $body = $response->toPsrResponse()->getBody();
        $buffer = '';
        $handleLine = function (string $line) use ($onChunk) {
            $line = trim($line);
            if (!str_starts_with($line, 'data:')) {
                return;
            }
            $data = trim(substr($line, 5));
            if ($data === '' || $data === '[DONE]') {
                return;
            }
            $json = json_decode($data, true);
            if (is_array($json)) {
                $onChunk($json);
            }
        };

        while (!$body->eof()) {
            $buffer .= $body->read(2048);
            while (($pos = strpos($buffer, "\n")) !== false) {
                $handleLine(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);
            }
        }
        if ($buffer !== '') {
            $handleLine($buffer);
        }

        return true;
    }

    protected function emitText(string $text): void
    {
        if ($text === '') {
            return;
        }
        $this->emittedText = true;
        $this->emit('delta', ['text' => $text]);
    }

    /** Tell the client to replace what it streamed with the final formatted content. */
    protected function emitReplace(string $content): void
    {
        $this->emit('replace', ['content' => $content]);
    }

    protected function emit(string $type, array $data): void
    {
        if ($this->onEvent) {
            ($this->onEvent)($type, $data);
        }
    }
}

