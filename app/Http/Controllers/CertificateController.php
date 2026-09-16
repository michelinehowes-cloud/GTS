<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * عرض قائمة شهادات الخريج
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Certificate::where('user_id', $user->id)
            ->where('status', 'valid');

        // تصفية حسب النوع
        if ($request->filled('type') && in_array($request->type, ['training_attendance', 'workshop_attendance', 'cooperative_attendance'])) {
            $query->where('type', $request->type);
        }

        // بحث بالعنوان أو الكود
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('certificate_code', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $certificates = $query->orderBy('issue_date', 'desc')->paginate(9);

        // إحصائيات سريعة للخريج
        $stats = [
            'total' => Certificate::where('user_id', $user->id)->where('status', 'valid')->count(),
            'training' => Certificate::where('user_id', $user->id)->where('type', 'training_attendance')->where('status', 'valid')->count(),
            'workshop' => Certificate::where('user_id', $user->id)->where('type', 'workshop_attendance')->where('status', 'valid')->count(),
            'cooperative' => Certificate::where('user_id', $user->id)->where('type', 'cooperative_attendance')->where('status', 'valid')->count(),
            'total_hours' => Certificate::where('user_id', $user->id)->where('status', 'valid')->sum('hours'),
        ];

        return view('graduate.certificates.index', compact('certificates', 'stats'));
    }

    /**
     * عرض وتنزيل الشهادة المعتمدة (قالب رسمي عالي الدقة مع إمكانية الطباعة والحفظ PDF)
     */
    public function show(Certificate $certificate)
    {
        // التحقق من الصلاحية: الخريج نفسه أو مسؤول النظام
        if (Auth::user()->role === 'graduate' && $certificate->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذه الشهادة.');
        }

        return view('graduate.certificates.show', compact('certificate'));
    }

    /**
     * التحقق العام من صحة الشهادة (عبر رمز QR أو الكود)
     */
    public function verify($code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['user', 'company', 'training'])
            ->first();

        return view('certificates.verify', compact('certificate', 'code'));
    }
}
