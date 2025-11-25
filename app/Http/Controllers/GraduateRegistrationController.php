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

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'تمت الموافقة على الحساب بنجاح');
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
