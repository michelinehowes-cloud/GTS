<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض قائمة الإشعارات
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = Notification::forUser($user->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc');

        // فلترة حسب النوع
        if ($request->has('type') && $request->type !== '') {
            $query->ofType($request->type);
        }

        // فلترة حسب حالة القراءة
        if ($request->has('read')) {
            if ($request->read === 'unread') {
                $query->unread();
            } elseif ($request->read === 'read') {
                $query->where('is_read', true);
            }
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * عرض إشعار واحد
     */
    /**
     * عرض إشعار واحد والتوجيه
     */
    public function show(Notification $notification)
    {
        // التحقق من الصلاحية
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        // تحديث حالة القراءة
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // منطق التوجيه بناءً على نوع النموذج والدور
        $redirectUrl = null;
        $user = auth()->user();

        if ($notification->model_type && $notification->model_id) {
            // 1. تسجيل خريج جديد
            if ($notification->model_type === 'App\Models\User' || $notification->model_type === 'App\Models\GraduateData') {
                if ($user->role === 'career_guidance_officer') {
                    $redirectUrl = route('career-guidance.pending-approvals');
                } elseif ($user->role === 'admin') {
                    $redirectUrl = route('admin.career-guidance.graduates.show', $notification->model_id);
                }
            }
            // 2. تدريب جديد
            elseif (str_contains($notification->model_type, 'Training') && !str_contains($notification->model_type, 'Application')) {
                if ($user->role === 'graduate') {
                    $redirectUrl = route('graduate.trainings.show', $notification->model_id);
                } elseif ($user->role === 'admin') {
                    $redirectUrl = route('admin.trainings.show', $notification->model_id);
                } elseif ($user->role === 'training_coordinator') {
                    $redirectUrl = route('training-coordinator.trainings.show', $notification->model_id);
                }
            }
            // 2.5. طلب تدريب جديد
            elseif (str_contains($notification->model_type, 'TrainingApplication')) {
                if ($user->role === 'training_coordinator') {
                    $redirectUrl = route('training-coordinator.applications');
                } elseif ($user->role === 'admin') {
                    $redirectUrl = route('admin.trainings.applications');
                }
            }
            // 3. فرصة عمل جديدة
            elseif (str_contains($notification->model_type, 'JobOpportunity')) {
                if ($user->role === 'graduate') {
                    $redirectUrl = route('graduate.job-opportunities.show', $notification->model_id);
                } elseif ($user->role === 'partnership_officer') {
                    $redirectUrl = route('job-opportunities.show', $notification->model_id);
                }
            }
            // 4. شركة جديدة
            elseif (str_contains($notification->model_type, 'Company')) {
                if ($user->role === 'partnership_officer') {
                    $redirectUrl = route('partnership.companies.show', $notification->model_id);
                } elseif ($user->role === 'admin') {
                    $redirectUrl = route('admin.companies.edit', $notification->model_id);
                }
            }
        }

        // إذا تم تحديد رابط توجيه، قم بالتوجيه إليه
        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        // وإلا اعرض صفحة التفاصيل الافتراضية
        return view('notifications.show', compact('notification'));
    }

    /**
     * الحصول على الإشعارات عبر AJAX
     */
    public function getNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();

        $notifications = Notification::forUser($user->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::forUser($user->id)->unread()->count(),
        ]);
    }

    /**
     * تحديث حالة القراءة
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        // التحقق من الصلاحية
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحديث حالة قراءة جميع الإشعارات
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = auth()->user();

        Notification::forUser($user->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * حذف إشعار
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // التحقق من الصلاحية
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * حذف جميع الإشعارات المقروءة
     */
    public function destroyRead(Request $request): JsonResponse
    {
        $user = auth()->user();

        Notification::forUser($user->id)
            ->where('is_read', true)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * إرسال إشعار تجريبي (للإدارة فقط)
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $targetUser = $request->user_id ? User::find($request->user_id) : $user;

        $notification = $this->notificationService->sendToUser(
            $targetUser,
            $request->title,
            $request->message,
            $request->type,
            ['sender_id' => $user->id]
        );

        return response()->json([
            'success' => true,
            'notification' => $notification,
        ]);
    }

    /**
     * إحصائيات الإشعارات
     */
    public function stats(): JsonResponse
    {
        return response()->json($this->notificationService->getStats());
    }
}
