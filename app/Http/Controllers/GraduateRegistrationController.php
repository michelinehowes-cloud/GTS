<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class GraduateRegistrationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض صفحة التسجيل
     */
    public function showRegistrationForm()
    {
        return view('auth.graduate-register');
    }

    /**
     * معالجة طلب التسجيل
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:users,national_id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'qualification' => 'required|string|max:100',
            'specialization' => 'required|string|max:100',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'university' => 'required|string|max:255',
            'sector' => 'required|string|max:100',
            'faculty' => 'required|string|max:255',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'experiences' => 'nullable|string',
            'skills' => 'nullable|string',
            'languages' => 'nullable|string',
        ], [
            'name.required' => 'الاسم الكامل مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'phone.required' => 'رقم الهاتف مطلوب',
            'national_id.required' => 'رقم القيد مطلوب',
            'national_id.unique' => 'رقم القيد موجود بالفعل',
            'date_of_birth.required' => 'تاريخ الميلاد مطلوب',
            'gender.required' => 'الجنس مطلوب',
            'address.required' => 'العنوان مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'qualification.required' => 'المؤهل العلمي مطلوب',
            'specialization.required' => 'التخصص مطلوب',
            'graduation_year.required' => 'سنة التخرج مطلوبة',
            'university.required' => 'الجامعة مطلوبة',
            'sector.required' => 'القطاع مطلوب',
            'faculty.required' => 'الكلية مطلوبة',
        ]);

        // معالجة المهارات واللغات (تحويل النص إلى مصفوفة)
        $skills = $request->filled('skills') ? array_filter(array_map('trim', explode(',', $request->skills))) : [];
        $languages = $request->filled('languages') ? array_filter(array_map('trim', explode(',', $request->languages))) : [];

        // إنشاء حساب جديد بحالة غير موافق عليه
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'graduate',
            'phone' => $request->phone,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'qualification' => $request->qualification,
            'specialization' => $request->specialization,
            'graduation_year' => $request->graduation_year,
            'university' => $request->university,
            'gpa' => $request->gpa,
            'experiences' => $request->experiences,
            'skills' => $skills,
            'languages' => $languages,
            'is_approved' => false, // في انتظار الموافقة
        ]);

        // إشعار لمسؤول الإرشاد المهني ومدير النظام
        try {
            $this->notificationService->sendToRoles(
                ['career_guidance_officer', 'admin'],
                'طلب تسجيل خريج جديد',
                "قام {$user->name} بالتسجيل وينتظر الموافقة.",
                'info',
                ['model_type' => get_class($user), 'model_id' => $user->id]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send registration notification: ' . $e->getMessage());
        }

        return redirect()->route('login')
            ->with('success', 'تم إرسال طلب التسجيل بنجاح! سيتم مراجعته من قبل الإدارة قريباً.');
    }

    /**
     * عرض قائمة طلبات التسجيل (للإرشاد المهني)
     */
    public function pendingApprovals()
    {
        $pendingUsers = User::where('role', 'graduate')
            ->where('is_approved', false)
            ->latest()
            ->paginate(20);

        return view('career-guidance.pending-approvals', compact('pendingUsers'));
    }

    /**
     * الموافقة على طلب تسجيل
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'graduate') {
            return back()->withErrors(['error' => 'هذا المستخدم ليس خريجاً']);
        }

        // التحقق من عدم وجود سجل مكرر في graduates_data
        $existingGraduate = \App\Models\GraduateData::where('email', $user->email)
            ->orWhere(function ($query) use ($user) {
                if ($user->national_id) {
                    $query->where('national_id', $user->national_id);
                }
            })
            ->first();

        if ($existingGraduate) {
            // الموافقة على الحساب فقط بدون إنشاء سجل جديد
            $user->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            return back()->with('success', 'تمت الموافقة على الحساب بنجاح (الخريج موجود مسبقاً في قاعدة البيانات)');
        }

        // الموافقة على الحساب
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // إشعار للخريج
        try {
            $this->notificationService->sendToUser(
                $user,
                'تمت الموافقة على حسابك',
                'تمت الموافقة على حسابك بنجاح. يمكنك الآن الدخول إلى النظام.',
                'success'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send approval notification: ' . $e->getMessage());
        }

        // إنشاء سجل في جدول graduates_data تلقائياً
        try {
            \App\Models\GraduateData::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'national_id' => $user->national_id ?? null,
                'major' => $user->specialization ?? 'غير محدد',
                'university' => $user->university ?? 'جامعة طرابلس',
                'graduation_year' => $user->graduation_year ?? date('Y'),
                'gpa' => $user->gpa ?? null,
                'degree' => $user->qualification ?? 'بكالوريوس',
                'address' => $user->address ?? null,
                'skills' => $user->skills ?? [],
                'languages' => $user->languages ?? [],
                'work_experience' => $user->experiences ?? null,
                'employment_status' => 'seeking_opportunities',
                'added_by' => auth()->id(),
                'data_source' => 'system_sync',
                'is_active' => true,
                'notes' => 'تم الإنشاء تلقائياً عند الموافقة على طلب التسجيل',
            ]);

            return back()->with('success', 'تمت الموافقة على الحساب بنجاح وتم إضافة الخريج إلى قاعدة البيانات');
        } catch (\Exception $e) {
            return back()->with('warning', 'تمت الموافقة على الحساب بنجاح ولكن حدث خطأ أثناء إضافة الخريج إلى قاعدة البيانات: ' . $e->getMessage());
        }
    }

    /**
     * رفض طلب تسجيل
     */
    public function reject($id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'graduate') {
            return back()->withErrors(['error' => 'هذا المستخدم ليس خريجاً']);
        }

        // حذف الحساب
        $user->delete();

        return back()->with('success', 'تم رفض الطلب وحذف الحساب');
    }
}
