<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @param  string  $permissions  الصلاحية أو الصلاحيات مفصولة بعلامة |
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permissions)
    {
        // 1. التحقق من تسجيل الدخول
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى هذه الصفحة.');
        }

        $user = auth()->user();

        // 2. التحقق من نشاط الحساب
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'تم تجميد هذا الحساب من قبل الإدارة. يرجى مراجعة مدير النظام.');
        }

        // 3. مدير النظام العام (Super Admin) يملك كافة الصلاحيات بدون قيد
        if ($user->isAdmin()) {
            return $next($request);
        }

        // 4. تقسيم الصلاحيات في حال تمرير أكثر من صلاحية (منطق OR)
        $permList = explode('|', $permissions);
        $hasAccess = false;

        foreach ($permList as $perm) {
            $perm = trim($perm);
            if ($user->hasPermission($perm)) {
                $hasAccess = true;
                break;
            }
        }

        // 5. إذا كان يملك الصلاحية، السماح بالمرور
        if ($hasAccess) {
            return $next($request);
        }

        // 6. في حال عدم امتلاك الصلاحية: توثيق أمني لمحاولة الوصول الممنوع
        AuditLog::logAction(
            'UNAUTHORIZED_ACCESS_BLOCKED',
            'Security',
            $user->id,
            null,
            [
                'required_permission' => $permissions,
                'user_role' => $user->role,
                'target_url' => $request->fullUrl(),
                'method' => $request->method(),
            ]
        );

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'status' => 'error',
                'message' => 'عذراً، لا تمتلك الصلاحية الكافية لتنفيذ هذا الإجراء.',
                'required' => $permissions,
            ], 403);
        }

        // عرض رسالة حظر مؤدبة واحترافية أو توجيه للوحة التحكم المناسبة
        return response()->view('errors.403-permission', [
            'requiredPermission' => $permissions,
            'user' => $user,
        ], 403);
    }
}
