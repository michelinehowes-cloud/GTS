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
     * Show the AI & API Settings Dashboard
     */
    public function index()
    {
        $rawGeminiKey = env('GEMINI_API_KEY', config('ai.gemini_api_key', ''));
        $rawGroqKey = env('GROQ_API_KEY', config('ai.groq_api_key', ''));
        $provider = env('AI_PROVIDER', config('ai.provider', 'auto'));

        $geminiModel = env('GEMINI_MODEL', config('ai.gemini_model', 'gemini-3.6-flash'));
        $groqModel = env('GROQ_MODEL', config('ai.groq_model', 'llama-3.3-70b-versatile'));

        $temperature = env('AI_TEMPERATURE', config('ai.temperature', 0.3));
        $maxTokens = env('AI_MAX_TOKENS', config('ai.max_tokens', 2048));

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
            'stats'
        ));
    }

    /**
     * Update AI and API settings in .env
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ai_provider' => 'required|in:auto,gemini,groq,local',
            'gemini_api_key' => 'nullable|string|max:500',
            'gemini_model' => 'required|string|max:100',
            'groq_api_key' => 'nullable|string|max:500',
            'groq_model' => 'required|string|max:100',
            'ai_temperature' => 'nullable|numeric|between:0,1',
            'ai_max_tokens' => 'nullable|integer|between:256,8192',
            'clear_gemini_key' => 'nullable|boolean',
            'clear_groq_key' => 'nullable|boolean',
        ]);

        $envUpdates = [
            'AI_PROVIDER' => $validated['ai_provider'],
            'GEMINI_MODEL' => $validated['gemini_model'],
            'GROQ_MODEL' => $validated['groq_model'],
            'AI_TEMPERATURE' => (string) ($validated['ai_temperature'] ?? '0.3'),
            'AI_MAX_TOKENS' => (string) ($validated['ai_max_tokens'] ?? '2048'),
        ];

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
                ],
                'ip_address' => $request->ip() ?? '127.0.0.1',
                'user_agent' => substr($request->userAgent() ?? 'System', 0, 255),
                'timestamp' => now(),
            ]);

            return redirect()->route('admin.settings.ai')
                ->with('success', 'تم حفظ وتحديث إعدادات الـ API ومحرك الذكاء الاصطناعي بنجاح!');
        }

        return redirect()->route('admin.settings.ai')
            ->with('error', 'تعذر تحديث ملف البيئة (.env). يرجى التحقق من أذونات الملف.');
    }

    /**
     * AJAX Endpoint to test live connection with Gemini or Groq API
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => 'required|in:gemini,groq',
            'api_key' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        $provider = $validated['provider'];
        $model = $validated['model'] ?? null;
        $inputKey = trim($validated['api_key'] ?? '');

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
                // Groq Test
                $targetModel = $model ?: env('GROQ_MODEL', config('ai.groq_model', 'llama-3.3-70b-versatile'));
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
                    return response()->json([
                        'success' => true,
                        'provider' => 'Groq Cloud',
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
                    'message' => "فشل الاتصال بـ Groq (رمز {$response->status()}): {$errMessage}"
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
     * Safely update .env file keys
     */
    protected function updateEnvironmentFile(array $data): bool
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return false;
        }

        try {
            $content = file_get_contents($envPath);

            foreach ($data as $key => $value) {
                $value = trim($value);
                // Wrap in quotes if it contains spaces or hash
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

            file_put_contents($envPath, $content);

            // Clear configuration cache so changes take effect immediately
            Artisan::call('config:clear');

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to update .env: ' . $e->getMessage());
            return false;
        }
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
