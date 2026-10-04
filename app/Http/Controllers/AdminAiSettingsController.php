<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\AiChatMessage;
use App\Models\AuditLog;
use App\Services\Ai\AiToolRegistry;

class AdminAiSettingsController extends Controller
{
    /**
     * Get active AI settings from storage/app/ai_settings.json or fallback to env/config
     */
    public static function getAiSettings(): array
    {
        $file = storage_path('app/ai_settings.json');
        $stored = [];
        if (file_exists($file)) {
            $json = json_decode(@file_get_contents($file), true);
            if (is_array($json)) {
                $stored = $json;
            }
        }

        return [
            'gemini_api_key'       => $stored['GEMINI_API_KEY'] ?? env('GEMINI_API_KEY', config('ai.gemini_api_key', '')),
            'groq_api_key'         => $stored['GROQ_API_KEY'] ?? env('GROQ_API_KEY', config('ai.groq_api_key', '')),
            'provider'             => $stored['AI_PROVIDER'] ?? env('AI_PROVIDER', config('ai.provider', 'auto')),
            'gemini_model'         => $stored['GEMINI_MODEL'] ?? env('GEMINI_MODEL', config('ai.gemini_model', 'gemini-3.6-flash')),
            'groq_model'           => $stored['GROQ_MODEL'] ?? env('GROQ_MODEL', config('ai.groq_model', 'llama-3.3-70b-versatile')),
            'temperature'          => (float) ($stored['AI_TEMPERATURE'] ?? env('AI_TEMPERATURE', config('ai.temperature', 0.3))),
            'max_tokens'           => (int) ($stored['AI_MAX_TOKENS'] ?? env('AI_MAX_TOKENS', config('ai.max_tokens', 2048))),
            'turnstile_enabled'    => (bool) ($stored['TURNSTILE_ENABLED'] ?? env('TURNSTILE_ENABLED', true)),
            'turnstile_site_key'   => $stored['TURNSTILE_SITE_KEY'] ?? env('TURNSTILE_SITE_KEY', config('security.turnstile.site_key', '')),
            'turnstile_secret_key' => $stored['TURNSTILE_SECRET_KEY'] ?? env('TURNSTILE_SECRET_KEY', config('security.turnstile.secret_key', '')),
        ];
    }

    /**
     * Show the AI & API Settings Dashboard
     */
    public function index()
    {
        $settings = self::getAiSettings();
        $rawGeminiKey = $settings['gemini_api_key'];
        $rawGroqKey = $settings['groq_api_key'];
        $provider = $settings['provider'];

        $geminiModel = $settings['gemini_model'];
        $groqModel = $settings['groq_model'];

        $temperature = $settings['temperature'];
        $maxTokens = $settings['max_tokens'];

        $turnstileEnabled = $settings['turnstile_enabled'];
        $turnstileSiteKey = $settings['turnstile_site_key'];
        $turnstileSecretKey = $settings['turnstile_secret_key'];
        $maskedTurnstileSecretKey = $this->maskKey($turnstileSecretKey);

        // Mask keys for security
        $maskedGeminiKey = $this->maskKey($rawGeminiKey);
        $maskedGroqKey = $this->maskKey($rawGroqKey);

        // Stats
        $stats = [
            'total_chats' => AiChatMessage::count(),
            'user_messages' => AiChatMessage::where('role', 'user')->count(),
            'actions_confirmed' => AiChatMessage::where('meta_data->confirmed', true)->count(),
            'tools_count' => count(AiToolRegistry::getAuthorizedTools(auth()->user())),
            'gemini_configured' => !empty($rawGeminiKey),
            'groq_configured' => !empty($rawGroqKey),
            'turnstile_configured' => !empty($turnstileSiteKey) && !empty($turnstileSecretKey) && $turnstileSiteKey !== '1x00000000000000000000AA',
        ];

        return view('admin.settings.ai', compact(
            'provider',
            'rawGeminiKey',
            'rawGroqKey',
            'maskedGeminiKey',
            'maskedGroqKey',
            'geminiModel',
            'groqModel',
            'temperature',
            'maxTokens',
            'turnstileEnabled',
            'turnstileSiteKey',
            'turnstileSecretKey',
            'maskedTurnstileSecretKey',
            'stats'
        ));
    }

    /**
     * Update AI and API settings in .env
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ai_provider'          => 'required|in:auto,gemini,groq,local',
            'gemini_api_key'       => 'nullable|string|max:500',
            'gemini_model'         => 'required|string|max:100',
            'groq_api_key'         => 'nullable|string|max:500',
            'groq_model'           => 'required|string|max:100',
            'ai_temperature'       => 'nullable|numeric|between:0,1',
            'ai_max_tokens'        => 'nullable|integer|between:256,8192',
            'clear_gemini_key'     => 'nullable|boolean',
            'clear_groq_key'       => 'nullable|boolean',
            'turnstile_enabled'    => 'nullable',
            'turnstile_site_key'   => 'nullable|string|max:200',
            'turnstile_secret_key' => 'nullable|string|max:200',
        ]);

        $envUpdates = [
            'AI_PROVIDER'       => $validated['ai_provider'],
            'GEMINI_MODEL'      => $validated['gemini_model'],
            'GROQ_MODEL'        => $validated['groq_model'],
            'AI_TEMPERATURE'    => (string) ($validated['ai_temperature'] ?? '0.3'),
            'AI_MAX_TOKENS'     => (string) ($validated['ai_max_tokens'] ?? '2048'),
            'TURNSTILE_ENABLED' => $request->has('turnstile_enabled') ? 'true' : 'false',
        ];

        // Handle Turnstile Site Key
        if ($request->filled('turnstile_site_key')) {
            $envUpdates['TURNSTILE_SITE_KEY'] = trim($request->input('turnstile_site_key'));
        }

        // Handle Turnstile Secret Key
        if ($request->filled('turnstile_secret_key')) {
            $rawTurnstileSecret = trim($request->input('turnstile_secret_key'));
            if (!str_contains($rawTurnstileSecret, '••••') && !str_contains($rawTurnstileSecret, '****')) {
                $envUpdates['TURNSTILE_SECRET_KEY'] = $rawTurnstileSecret;
            }
        }

        // Handle Gemini API Key
        if ($request->boolean('clear_gemini_key')) {
            $envUpdates['GEMINI_API_KEY'] = '';
        } elseif (!empty($validated['gemini_api_key'])) {
            $trimmedKey = trim($validated['gemini_api_key']);
            // Only update if not the masked placeholder
            if (!str_contains($trimmedKey, '••••') && !str_contains($trimmedKey, '****')) {
                $envUpdates['GEMINI_API_KEY'] = $trimmedKey;
            }
        }

        // Handle Groq API Key
        if ($request->boolean('clear_groq_key')) {
            $envUpdates['GROQ_API_KEY'] = '';
        } elseif (!empty($validated['groq_api_key'])) {
            $trimmedKey = trim($validated['groq_api_key']);
            if (!str_contains($trimmedKey, '••••') && !str_contains($trimmedKey, '****')) {
                $envUpdates['GROQ_API_KEY'] = $trimmedKey;
            }
        }

        $success = $this->updateEnvironmentFile($envUpdates);

        if ($success) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'ADMIN_UPDATE_AI_SETTINGS',
                'entity' => 'AiConfiguration',
                'entity_id' => 0,
                'new_values' => [
                    'provider' => $validated['ai_provider'],
                    'gemini_model' => $validated['gemini_model'],
                    'groq_model' => $validated['groq_model'],
                    'turnstile_site_key' => $envUpdates['TURNSTILE_SITE_KEY'] ?? null,
                ],
                'ip_address' => $request->ip() ?? '127.0.0.1',
                'user_agent' => substr($request->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            return redirect()->route('admin.settings.ai')
                ->with('success', 'تم حفظ وتحديث إعدادات الـ API والأمان بنجاح!');
        }

        return redirect()->route('admin.settings.ai')
            ->with('error', 'تعذر تحديث الإعدادات. يرجى التحقق من أذونات التخزين.');
    }

    /**
     * AJAX Endpoint to test live connection with Gemini, Groq, or Turnstile
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|in:gemini,groq,turnstile',
            'api_key' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        $provider = $validated['provider'];
        $model = $validated['model'] ?? null;
        $inputKey = trim($validated['api_key'] ?? '');
        $startTime = microtime(true);

        // Turnstile testing
        if ($provider === 'turnstile') {
            $turnstileSecret = ($inputKey && !str_contains($inputKey, '••••') && !str_contains($inputKey, '****'))
                ? $inputKey
                : (self::getAiSettings()['turnstile_secret_key'] ?? '');

            if (empty($turnstileSecret)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم إدخال المفتاح السري (Secret Key) لـ Cloudflare.'
                ], 422);
            }

            try {
                $res = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $turnstileSecret,
                    'response' => 'test_dummy_token',
                ]);
                $duration = round((microtime(true) - $startTime) * 1000);
                $json = $res->json();
                $errors = $json['error-codes'] ?? [];

                if (in_array('invalid-input-secret', $errors)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'المفتاح السري (Secret Key) غير صالح أو تم رفضه من سيرفرات Cloudflare.'
                    ], 400);
                }

                return response()->json([
                    'success' => true,
                    'message' => "تم الاتصال بسيرفرات Cloudflare بنجاح! المفتاح السري صحيح وموثّق ({$duration}ms).",
                    'duration_ms' => $duration,
                ]);
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'تعذر الاتصال بسيرفرات Cloudflare: ' . $e->getMessage()
                ], 500);
            }
        }

        // If key input is masked or empty, read existing from env
        if (empty($inputKey) || str_contains($inputKey, '••••') || str_contains($inputKey, '****')) {
            $apiKey = ($provider === 'gemini')
                ? env('GEMINI_API_KEY', config('ai.gemini_api_key', ''))
                : env('GROQ_API_KEY', config('ai.groq_api_key', ''));
        } else {
            $apiKey = $inputKey;
        }

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم إدخال مفتاح API. يرجى إدخال المفتاح أولاً ثم إعادة الاختبار.'
            ], 422);
        }

        // Quick prefix check to prevent mixing up keys
        if ($provider === 'groq' && (str_starts_with($apiKey, 'AIza') || str_starts_with($apiKey, 'AQ.'))) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'المفتاح المدخل يبدو أنه خاص بـ Google Gemini وليس Groq! مفاتيح Groq تبدأ دائماً بـ (gsk_...). يرجى استخراج مفتاح مجاني من console.groq.com ولصقه في هذه الخانة.'
            ], 400);
        }

        if ($provider === 'gemini' && str_starts_with($apiKey, 'gsk_')) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'المفتاح المدخل يبدو أنه خاص بـ Groq وليس Google Gemini! مفتاح Groq يجب وضعه في خانة Groq أدناه.'
            ], 400);
        }

        $startTime = microtime(true);

        try {
            if ($provider === 'gemini') {
                $targetModel = $model ?: env('GEMINI_MODEL', config('ai.gemini_model', 'gemini-3.6-flash'));
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$targetModel}:generateContent?key={$apiKey}";

                $response = Http::timeout(15)->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => 'أجب بكلمة واحدة فقط باللغة العربية: جاهز']]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 30,
                        'temperature' => 0.1,
                    ]
                ]);

                $duration = round((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    $json = $response->json();
                    $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'جاهز';
                    return response()->json([
                        'success' => true,
                        'provider' => 'Google Gemini',
                        'model' => $targetModel,
                        'latency_ms' => $duration,
                        'reply' => trim($reply),
                        'message' => "تم الاتصال بنجاح بنموذج ({$targetModel}) خلال {$duration}ms! رد النموذج: '{$reply}'"
                    ]);
                }

                $errorData = $response->json();
                $errMessage = $errorData['error']['message'] ?? $response->body();
                return response()->json([
                    'success' => false,
                    'status_code' => $response->status(),
                    'message' => "فشل الاتصال بـ Google Gemini (رمز {$response->status()}): {$errMessage}"
                ], 400);

            } else {
                // Groq Test: Check available models on this specific account
                $modelsUrl = 'https://api.groq.com/openai/v1/models';
                $modelsResp = Http::timeout(10)->withToken($apiKey)->get($modelsUrl);

                if ($modelsResp->status() === 401) {
                    return response()->json([
                        'success' => false,
                        'status_code' => 401,
                        'message' => 'فشل الاتصال بـ Groq (رمز 401): مفتاح API غير صالح. تأكد من نسخه كاملاً من console.groq.com ويبدأ بـ (gsk_).'
                    ], 400);
                }

                $availableModels = [];
                if ($modelsResp->successful()) {
                    $modelsJson = $modelsResp->json();
                    if (!empty($modelsJson['data']) && is_array($modelsJson['data'])) {
                        foreach ($modelsJson['data'] as $m) {
                            $mId = $m['id'] ?? '';
                            if ($mId && !str_contains($mId, 'whisper') && !str_contains($mId, 'orpheus') && !str_contains($mId, 'guard')) {
                                $availableModels[] = $mId;
                            }
                        }
                    }
                }

                $requestedModel = $model ?: env('GROQ_MODEL', config('ai.groq_model', 'llama-3.3-70b-versatile'));
                $targetModel = $requestedModel;

                // If the requested model is not found in the account's active models, pick an available one
                if (!empty($availableModels) && !in_array($requestedModel, $availableModels)) {
                    // Try to prefer general chat models
                    $preferred = ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'openai/gpt-oss-120b', 'openai/gpt-oss-20b', 'mixtral-8x7b-32768'];
                    $picked = null;
                    foreach ($preferred as $pref) {
                        if (in_array($pref, $availableModels)) {
                            $picked = $pref;
                            break;
                        }
                    }
                    $targetModel = $picked ?: $availableModels[0];
                }

                $url = 'https://api.groq.com/openai/v1/chat/completions';
                $response = Http::timeout(15)->withToken($apiKey)->post($url, [
                    'model' => $targetModel,
                    'messages' => [
                        ['role' => 'user', 'content' => 'أجب بكلمة واحدة فقط باللغة العربية: جاهز']
                    ],
                    'max_tokens' => 30,
                    'temperature' => 0.1,
                ]);

                $duration = round((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    $json = $response->json();
                    $reply = $json['choices'][0]['message']['content'] ?? 'جاهز';
                    $msg = "تم الاتصال بنجاح بنموذج ({$targetModel}) خلال {$duration}ms! رد النموذج: '{$reply}'";
                    if ($targetModel !== $requestedModel) {
                        $msg .= " (ملاحظة: النموذج المطلوب {$requestedModel} غير متاح في حسابك، وتم الاعتماد تلقائياً على {$targetModel}).";
                    }

                    return response()->json([
                        'success' => true,
                        'provider' => 'Groq Cloud',
                        'model' => $targetModel,
                        'suggested_model' => $targetModel,
                        'available_models' => $availableModels,
                        'latency_ms' => $duration,
                        'reply' => trim($reply),
                        'message' => $msg
                    ]);
                }

                $errorData = $response->json();
                $errMessage = $errorData['error']['message'] ?? $response->body();
                $extraHelp = '';
                if (!empty($availableModels)) {
                    $extraHelp = "\nالنماذج المتاحة في حسابك هي: " . implode(' ، ', array_slice($availableModels, 0, 5));
                }

                return response()->json([
                    'success' => false,
                    'status_code' => $response->status(),
                    'available_models' => $availableModels,
                    'message' => "فشل الاتصال بـ Groq (رمز {$response->status()}): {$errMessage}{$extraHelp}"
                ], 400);
            }
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000);
            Log::error('AI Test Connection Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث استثناء أثناء محاولة الاتصال: ' . $e->getMessage(),
                'latency_ms' => $duration,
            ], 500);
        }
    }

    /**
     * Safely update settings persistently (in storage/app/ai_settings.json and optionally .env)
     */
    protected function updateEnvironmentFile(array $data): bool
    {
        // 1. Always save to storage/app/ai_settings.json (guaranteed writable)
        try {
            $storageDir = storage_path('app');
            if (!is_dir($storageDir)) {
                @mkdir($storageDir, 0775, true);
            }
            $file = $storageDir . '/ai_settings.json';
            $current = [];
            if (file_exists($file)) {
                $current = json_decode(@file_get_contents($file), true) ?: [];
            }
            $merged = array_merge($current, $data);
            @file_put_contents($file, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            Log::error('Failed to write ai_settings.json: ' . $e->getMessage());
        }

        // 2. Best-effort update of .env file if it exists and is writable
        $envPath = base_path('.env');
        if (file_exists($envPath) && is_writable($envPath)) {
            try {
                $content = file_get_contents($envPath);

                foreach ($data as $key => $value) {
                    $value = trim($value);
                    if ((str_contains($value, ' ') || str_contains($value, '#')) && !str_starts_with($value, '"')) {
                        $value = '"' . addcslashes($value, '"') . '"';
                    }

                    $pattern = "/^{$key}=.*$/m";

                    if (preg_match($pattern, $content)) {
                        $content = preg_replace($pattern, "{$key}={$value}", $content);
                    } else {
                        $content = rtrim($content) . "\n{$key}={$value}\n";
                    }
                }

                @file_put_contents($envPath, $content);
            } catch (\Throwable $e) {
                Log::warning('Failed to update .env: ' . $e->getMessage());
            }
        }

        try {
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
            // Ignore if config:clear is not permitted
        }

        return true;
    }

    /**
     * Helper to mask an API key for display
     */
    protected function maskKey(?string $key): string
    {
        if (empty($key)) {
            return '';
        }
        $len = strlen($key);
        if ($len <= 10) {
            return '••••••••';
        }
        return substr($key, 0, 7) . '••••••••••••' . substr($key, -4);
    }
}
