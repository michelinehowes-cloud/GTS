<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Ai\AiAssistantService;
use App\Models\AiChatMessage;
use Illuminate\Support\Facades\RateLimiter;

class AiAssistantController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Per-user usage limits. Returns an error message when the user is over a limit, null otherwise.
     */
    protected function checkUsageLimits($user): ?string
    {
        $perMinute = (int) config('ai.rate_limits.per_minute', 15);
        $perDay = (int) ($user->isAdmin()
            ? config('ai.rate_limits.per_day_admin', 400)
            : config('ai.rate_limits.per_day', 150));

        $minuteKey = 'ai-chat:minute:' . $user->id;
        $dayKey = 'ai-chat:day:' . $user->id . ':' . now()->format('Y-m-d');

        if (RateLimiter::tooManyAttempts($minuteKey, $perMinute)) {
            $seconds = RateLimiter::availableIn($minuteKey);
            return "أرسلت رسائل كثيرة خلال وقت قصير. يرجى الانتظار {$seconds} ثانية ثم المحاولة مرة أخرى.";
        }
        if (RateLimiter::tooManyAttempts($dayKey, $perDay)) {
            return "وصلت إلى الحد اليومي لاستخدام المساعد الذكي ({$perDay} رسالة). يمكنك المتابعة غداً.";
        }

        RateLimiter::hit($minuteKey, 60);
        RateLimiter::hit($dayKey, 86400);

        return null;
    }

    /**
     * Stream the assistant's answer as Server-Sent Events:
     *   event: status  {label}            a tool is running ("يبحث في فرص العمل…")
     *   event: delta   {text}             a piece of the answer as it is written
     *   event: replace {content}          final formatted answer (cards, confirmations)
     *   event: done    {content, action_proposal, tool_executed, session_id}
     *   event: error   {content}
     */
    public function stream(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        if ($limitMessage = $this->checkUsageLimits($user)) {
            return response()->json(['status' => 'rate_limited', 'content' => $limitMessage], 429);
        }

        $sessionId = $validated['session_id'] ?? session()->getId();
        $message = $validated['message'];

        return response()->stream(function () use ($user, $sessionId, $message) {
            // Push every event to the browser immediately (skipped under PHPUnit, which owns the output buffers)
            $live = !app()->runningUnitTests();
            if ($live) {
                @set_time_limit(180);
                while (ob_get_level() > 0) {
                    @ob_end_flush();
                }
            }

            $send = function (string $event, array $data) use ($live) {
                echo "event: {$event}\n";
                echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
                if ($live) {
                    @ob_flush();
                    @flush();
                }
            };

            // Padding so proxies that buffer small responses start forwarding immediately
            echo ':' . str_repeat(' ', 2048) . "\n\n";
            if ($live) {
                @flush();
            }

            try {
                $result = $this->aiService->chat($user, $sessionId, $message, $send);
                $send('done', $result);
            } catch (\Throwable $e) {
                \Log::error('AI Assistant Stream Error: ' . $e->getMessage(), ['user_id' => $user->id]);
                $send('error', ['content' => 'عذراً، حدث خطأ أثناء معالجة طلبك، يرجى المحاولة مرة أخرى لاحقاً.']);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Handle incoming chat message from user (non-streaming JSON; used as a fallback by the widget)
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        if ($limitMessage = $this->checkUsageLimits($user)) {
            return response()->json(['status' => 'rate_limited', 'content' => $limitMessage], 429);
        }

        $sessionId = $validated['session_id'] ?? session()->getId();

        try {
            $response = $this->aiService->chat($user, $sessionId, $validated['message']);
            return response()->json($response);
        } catch (\Throwable $e) {
            \Log::error('AI Assistant Chat Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'content' => 'عذراً، حدث خطأ أثناء معالجة طلبك، يرجى المحاولة مرة أخرى لاحقاً.',
            ], 500);
        }
    }

    /**
     * Confirm a proposed action (Draft news, draft training, etc.)
     */
    public function confirmAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string',
            'action_data' => 'required|array',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
        $sessionId = $validated['session_id'] ?? session()->getId();

        try {
            $response = $this->aiService->confirmAction(
                $user,
                $sessionId,
                $validated['action_type'],
                $validated['action_data']
            );

            return response()->json($response);
        } catch (\Throwable $e) {
            \Log::error('AI Assistant Confirm Action Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'تعذر إتمام الإجراء المطلوب في الوقت الحالي. يرجى المحاولة لاحقاً.',
            ], 500);
        }
    }

    /**
     * Retrieve chat history for the current session
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->input('session_id');

        // إذا لم يتم تمرير session_id، نبحث عن آخر جلسة محادثة للمستخدم
        if (!$sessionId) {
            $latestMsg = AiChatMessage::where('user_id', $user->id)->latest()->first();
            $sessionId = $latestMsg ? $latestMsg->session_id : session()->getId();
        }

        $messages = AiChatMessage::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get(['id', 'role', 'content', 'meta_data', 'created_at']);

        // إذا كانت الجلسة الممررة فارغة، ولكن للمستخدم رسائل حديثة أخرى
        if ($messages->isEmpty()) {
            $latestMsg = AiChatMessage::where('user_id', $user->id)->latest()->first();
            if ($latestMsg && $latestMsg->session_id) {
                $sessionId = $latestMsg->session_id;
                $messages = AiChatMessage::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->orderBy('created_at', 'asc')
                    ->take(50)
                    ->get(['id', 'role', 'content', 'meta_data', 'created_at']);
            }
        }

        return response()->json([
            'status' => 'success',
            'session_id' => $sessionId,
            'messages' => $messages,
        ]);
    }

    /**
     * Clear chat history for the current session
     */
    public function clearHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = $request->input('session_id') ?: session()->getId();

        AiChatMessage::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم مسح سجل المحادثة بنجاح.',
        ]);
    }
}
