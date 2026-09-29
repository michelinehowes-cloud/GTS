<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Ai\AiAssistantService;
use App\Models\AiChatMessage;

class AiAssistantController extends Controller
{
    protected AiAssistantService $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle incoming chat message from user
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $user = $request->user();
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
                'content' => 'عذراً، حدث خطأ أثناء معالجة طلبك: ' . $e->getMessage(),
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
            \Log::error('AI Assistant Confirm Action Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'تعذر إتمام الإجراء المطلوب: ' . $e->getMessage(),
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
