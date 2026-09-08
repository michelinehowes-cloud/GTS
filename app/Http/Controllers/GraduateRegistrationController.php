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
     * عرض صفحة التسجيل (تحويلها للنافذة المنبثقة بالرئيسية)
     */
    public function showRegistrationForm()
    {
        return redirect('/?open_register=1');
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
            'national_id' => 'nullable|string|max:50',
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
            'gpa' => 'nullable|numeric|min:0|max:100',
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
            'gpa.numeric' => 'المعدل التراكمي يجب أن يكون رقماً',
            'gpa.min' => 'المعدل التراكمي لا يمكن أن يكون أقل من 0',
            'gpa.max' => 'المعدل التراكمي كنسبة مئوية لا يمكن أن يتجاوز 100%',
        ]);

        // معالجة المهارات واللغات (تحويل النص إلى مصفوفة)
        $rawSkills = $request->input('skills');
        $skills = !empty($rawSkills) ? (is_array($rawSkills) ? $rawSkills : array_filter(array_map('trim', explode(',', (string)$rawSkills)))) : [];

        $rawLanguages = $request->input('languages');
        $languages = !empty($rawLanguages) ? (is_array($rawLanguages) ? $rawLanguages : array_filter(array_map('trim', explode(',', (string)$rawLanguages)))) : [];

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
            'sector' => $request->sector,
            'faculty' => $request->faculty,
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

        $res = $this->processGraduateApproval($user);

        return back()->with($res['status'], $res['message']);
    }

    /**
     * الموافقة الجماعية على طلبات التسجيل المحددة أو الكل
     */
    public function bulkApprove(Request $request)
    {
        if ($request->boolean('approve_all')) {
            $users = User::where('role', 'graduate')->where('is_approved', false)->get();
        } else {
            $ids = $request->input('ids', []);
            if (empty($ids) || !is_array($ids)) {
                return back()->withErrors(['error' => 'يرجى تحديد خريج واحد على الأقل للموافقة عليه']);
            }
            $users = User::where('role', 'graduate')->where('is_approved', false)->whereIn('id', $ids)->get();
        }

        if ($users->isEmpty()) {
            return back()->with('info', 'لا توجد طلبات معلقة للموافقة عليها');
        }

        $count = 0;
        foreach ($users as $user) {
            $this->processGraduateApproval($user);
            $count++;
        }

        return back()->with('success', "تمت الموافقة بنجاح على {$count} من طلبات تسجيل الخريجين وتفعيل حساباتهم.");
    }

    /**
     * الرفض الجماعي لطلبات التسجيل المحددة
     */
    public function bulkReject(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->withErrors(['error' => 'يرجى تحديد خريج واحد على الأقل لرفضه']);
        }

        $users = User::where('role', 'graduate')->where('is_approved', false)->whereIn('id', $ids)->get();
        $count = 0;
        foreach ($users as $user) {
            $user->delete();
            $count++;
        }

        return back()->with('success', "تم رفض {$count} طلب تسجيل بنجاح.");
    }

    /**
     * معالجة الموافقة وتفعيل حساب الخريج ومزامنته مع قاعدة بيانات الإرشاد المهني
     */
    private function processGraduateApproval(User $user)
    {
        // 1. تفعيل الحساب
        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id() ?? 1,
        ]);

        // 2. إشعار للخريج
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

        // 3. التحقق من وجود سجل مسبق في جدول graduates_data
        $existingGraduate = \App\Models\GraduateData::where('email', $user->email)
            ->orWhere(function ($query) use ($user) {
                if ($user->national_id) {
                    $query->where('national_id', $user->national_id);
                }
            })
            ->first();

        if ($existingGraduate) {
            if ($existingGraduate->user_id !== $user->id) {
                $existingGraduate->update(['user_id' => $user->id]);
            }
            return [
                'status' => 'success',
                'message' => 'تمت الموافقة على الحساب وتفعيله بنجاح (الخريج مسجل مسبقاً في قاعدة بيانات التوظيف).'
            ];
        }

        // 4. إنشاء سجل في جدول graduates_data تلقائياً
        try {
            \App\Models\GraduateData::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'national_id' => $user->national_id ?? null,
                'major' => $user->specialization ?? $user->major ?? 'غير محدد',
                'faculty' => $user->faculty ?? null,
                'sector' => $user->sector ?? null,
                'university' => $user->university ?? 'جامعة طرابلس',
                'graduation_year' => $user->graduation_year ?? date('Y'),
                'gpa' => $user->gpa ?? null,
                'degree' => $user->qualification ?? $user->degree ?? 'بكالوريوس',
                'address' => $user->address ?? null,
                'skills' => is_array($user->skills) ? $user->skills : (is_string($user->skills) ? (json_decode((string)$user->skills, true) ?? []) : []),
                'languages' => is_array($user->languages) ? $user->languages : (is_string($user->languages) ? (json_decode((string)$user->languages, true) ?? []) : []),
                'work_experience' => $user->experiences ?? null,
                'employment_status' => 'seeking_opportunities',
                'added_by' => auth()->id() ?? 1,
                'data_source' => 'system_sync',
                'is_active' => true,
                'notes' => 'تم الإنشاء تلقائياً عند الموافقة على طلب التسجيل',
            ]);

            return [
                'status' => 'success',
                'message' => 'تمت الموافقة على الحساب بنجاح وتمت إضافة الخريج إلى قاعدة بيانات الإرشاد والتوظيف.'
            ];
        } catch (\Exception $e) {
            \Log::error('GraduateData creation error: ' . $e->getMessage());
            return [
                'status' => 'warning',
                'message' => 'تم تفعيل حساب الخريج بنجاح، مع تنبيه: ' . $e->getMessage()
            ];
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
