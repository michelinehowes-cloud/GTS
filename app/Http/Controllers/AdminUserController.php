<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
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

        $status = $user->is_active ? 'تنشيط' : 'تجميد';
        return back()->with('success', "تم {$status} حساب المستخدم بنجاح");
    }
}
