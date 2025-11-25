<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GraduateRegistrationController extends Controller
{
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
            'gpa' => 'nullable|numeric|min:0|max:4',
        ], [
            'name.required' => 'الاسم الكامل مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمة المرور غير متطابقة',
            'phone.required' => 'رقم الهاتف مطلوب',
            'national_id.required' => 'رقم الهوية الوطنية مطلوب',
            'national_id.unique' => 'رقم الهوية مستخدم بالفعل',
            'date_of_birth.required' => 'تاريخ الميلاد مطلوب',
            'gender.required' => 'الجنس مطلوب',
            'address.required' => 'العنوان مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'qualification.required' => 'المؤهل العلمي مطلوب',
            'specialization.required' => 'التخصص مطلوب',
            'graduation_year.required' => 'سنة التخرج مطلوبة',
            'university.required' => 'الجامعة مطلوبة',
        ]);

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
            'is_approved' => false, // في انتظار الموافقة
        ]);

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
