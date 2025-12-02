<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class AdminUserController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request)
    {
        $query = User::query();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // التصفية حسب الدور
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // التصفية حسب الحالة
        if ($request->filled('status')) {
            $isActive = $request->status == 'active';
            $query->where('is_active', $isActive);
        }

        $users = $query->latest()->paginate(15);
        $roles = User::getAvailableRoles();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * تجميد/تنشيط حساب المستخدم
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // منع تجميد النفس
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك تجميد حسابك الشخصي');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        // إرسال إشعار للمستخدم
        try {
            $statusMsg = $user->is_active ? 'تنشيط' : 'تجميد';
            $this->notificationService->sendToUser(
                $user,
                "تحديث حالة الحساب", // Changed to a generic title
                "تم {$statusMsg} حسابك من قبل إدارة النظام.", // Dynamic message as body
                $user->is_active ? 'success' : 'warning'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send user status notification: ' . $e->getMessage());
        }

        $status = $user->is_active ? 'تنشيط' : 'تجميد';
        return back()->with('success', "تم {$status} حساب المستخدم بنجاح");
    }
}
