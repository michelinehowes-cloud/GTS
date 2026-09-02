<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * عرض قائمة الموظفين والمستخدمين الإداريين مع الإحصائيات والفلاتر
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        // فحص الصلاحية الأمنية
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.view') && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى إدارة الموظفين.');
        }

        $query = User::with('permissions')
            ->whereNotIn('role', ['graduate', 'company']);

        // فلترة بالبحث (الاسم، البريد، الهاتف)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة بالدور
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // فلترة بالحالة (نشط / معطل)
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // إحصائيات لوحة الموظفين
        $statistics = [
            'total_staff' => User::whereNotIn('role', ['graduate', 'company'])->count(),
            'active_staff' => User::whereNotIn('role', ['graduate', 'company'])->where('is_active', true)->count(),
            'custom_staff' => User::where('role', 'staff')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        $availableRoles = User::getAvailableRoles();
        $groupedPermissions = Permission::getGrouped();

        return view('admin.users.index', compact('users', 'statistics', 'availableRoles', 'groupedPermissions'));
    }

    /**
     * نموذج إضافة موظف جديد
     */
    public function create()
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بإضافة موظفين جدد.');
        }

        $availableRoles = User::getAvailableRoles();
        // استبعاد الأدوار غير الإدارية من قائمة الموظفين
        unset($availableRoles['graduate'], $availableRoles['company']);

        $groupedPermissions = Permission::getGrouped();

        return view('admin.users.create', compact('availableRoles', 'groupedPermissions'));
    }

    /**
     * حفظ موظف جديد وتعيين صلاحياته
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بإنشاء موظفين.');
        }

        $validRoles = ['admin', 'staff', 'training_coordinator', 'partnership_officer', 'career_guidance_officer', 'evaluation_followup', 'media_officer'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $validRoles),
            'phone' => 'nullable|string|max:20',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'يرجى كتابة اسم الموظف بالكامل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً في النظام.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات لأسباب أمنية.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.required' => 'يرجى تحديد الدور الوظيفي للمستخدم.',
        ]);

        // حماية فائقة: لا يمكن لغير المدير العام تعيين مستخدم كمدير عام (منع تصعيد الصلاحيات)
        if ($request->role === 'admin' && !$currentUser->isAdmin()) {
            abort(403, 'لا تملك الصلاحية لتعيين مدير نظام عام.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // تعيين الصلاحيات المختارة
        if ($request->has('permissions') && is_array($request->permissions)) {
            $user->permissions()->sync($request->permissions);
        }

        // توثيق أمني للعملية في سجل الرقابة
        AuditLog::logAction(
            'STAFF_CREATED',
            'User',
            $user->id,
            null,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'granted_permissions_count' => count($request->permissions ?? []),
                'creator' => $currentUser->email,
            ]
        );

        return redirect()->route('admin.users')->with('success', "تم إضافة الموظف ({$user->name}) وتعيين صلاحياته بنجاح.");
    }

    /**
     * نموذج تعديل بيانات وصلاحيات موظف
     */
    public function edit($id)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بتعديل بيانات الموظفين.');
        }

        $user = User::with('permissions')->findOrFail($id);

        $availableRoles = User::getAvailableRoles();
        unset($availableRoles['graduate'], $availableRoles['company']);

        $groupedPermissions = Permission::getGrouped();
        $userPermissionIds = $user->permissions->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'availableRoles', 'groupedPermissions', 'userPermissionIds'));
    }

    /**
     * تحديث بيانات وصلاحيات الموظف
     */
    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بتعديل الموظفين.');
        }

        $user = User::with('permissions')->findOrFail($id);

        $validRoles = ['admin', 'staff', 'training_coordinator', 'partnership_officer', 'career_guidance_officer', 'evaluation_followup', 'media_officer'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', $validRoles),
            'phone' => 'nullable|string|max:20',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'اسم الموظف مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 خانات على الأقل.',
            'role.required' => 'الدور الوظيفي مطلوب.',
        ]);

        // حماية فائقة: منع تغيير دور أو تجميد مدير النظام الأساسي المحمي
        if ($user->isProtectedSuperAdmin() && $request->role !== 'admin') {
            return back()->with('error', 'إجراء محظور: لا يمكن تجريد حساب مدير النظام الأساسي من رتبة المدير العام (حماية أمنية مشددة).');
        }

        // حماية فائقة: لا يمكن لغير المدير العام ترقية أحد لرتبة مدير عام
        if ($request->role === 'admin' && !$currentUser->isAdmin()) {
            abort(403, 'لا يمكنك ترقية مستخدم لرتبة مدير نظام عام.');
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->permissions->pluck('name')->toArray(),
        ];

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // تحديث الصلاحيات المخصصة
        $newPermissions = $request->permissions ?? [];
        $user->permissions()->sync($newPermissions);

        // توثيق أمني للعملية
        AuditLog::logAction(
            'STAFF_UPDATED',
            'User',
            $user->id,
            $oldValues,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions_count' => count($newPermissions),
                'password_changed' => $request->filled('password'),
                'updated_by' => $currentUser->email,
            ]
        );

        return redirect()->route('admin.users')->with('success', "تم تحديث بيانات وصلاحيات الموظف ({$user->name}) بنجاح.");
    }

    /**
     * حذف حساب الموظف مع الحماية الأمنية
     */
    public function destroy($id)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin()) {
            abort(403, 'عذراً، حذف حسابات الموظفين مقتصر حصراً على مدير النظام العام.');
        }

        $user = User::findOrFail($id);

        // حماية أمنية فائقة
        if ($user->isProtectedSuperAdmin()) {
            return redirect()->route('admin.users')->with('error', 'محاولة محظورة: لا يمكن حذف حساب مدير النظام الأساسي نهائياً.');
        }

        if ($user->id === $currentUser->id) {
            return redirect()->route('admin.users')->with('error', 'لا يمكنك حذف حسابك الشخصي وأنت قيد تسجيل الدخول.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // توثيق الحذف قبل التنفيذ
        AuditLog::logAction(
            'STAFF_DELETED',
            'User',
            $user->id,
            [
                'name' => $userName,
                'email' => $userEmail,
                'role' => $user->role,
            ],
            [
                'deleted_by' => $currentUser->email,
            ]
        );

        $user->permissions()->detach();
        $user->delete();

        return redirect()->route('admin.users')->with('success', "تم حذف حساب الموظف ({$userName}) نهائياً.");
    }

    /**
     * تجميد أو تنشيط حساب الموظف
     */
    public function toggleStatus($id)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بتغيير حالة حسابات الموظفين.');
        }

        $user = User::findOrFail($id);

        if ($user->isProtectedSuperAdmin()) {
            return back()->with('error', 'حماية أمنية مشددة: لا يمكن تجميد حساب مدير النظام الأساسي.');
        }

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'لا يمكن تجميد حسابك الشخصي.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $actionText = $user->is_active ? 'تنشيط' : 'تجميد';

        AuditLog::logAction(
            $user->is_active ? 'STAFF_ACTIVATED' : 'STAFF_SUSPENDED',
            'User',
            $user->id,
            ['is_active' => !$user->is_active],
            ['is_active' => $user->is_active, 'by' => $currentUser->email]
        );

        return back()->with('success', "تم {$actionText} حساب الموظف ({$user->name}) بنجاح.");
    }

    /**
     * تغيير كلمة مرور موظف
     */
    public function changePassword(Request $request, $id)
    {
        $currentUser = auth()->user();
        if (!$currentUser->isAdmin() && !$currentUser->hasPermission('users.manage')) {
            abort(403, 'غير مصرح لك بتغيير كلمات المرور.');
        }

        $user = User::findOrFail($id);

        if ($user->isProtectedSuperAdmin() && !$currentUser->isAdmin()) {
            abort(403, 'لا يمكن تعديل كلمة مرور مدير النظام الأساسي إلا من قبل مدير النظام نفسه.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::logAction(
            'PASSWORD_RESET_BY_ADMIN',
            'User',
            $user->id,
            null,
            ['target_user' => $user->email, 'changed_by' => $currentUser->email]
        );

        return back()->with('success', "تم تغيير كلمة المرور للموظف ({$user->name}) بنجاح.");
    }
}
