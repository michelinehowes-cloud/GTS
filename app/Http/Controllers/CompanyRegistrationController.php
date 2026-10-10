<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyRegistrationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض صفحة تسجيل حساب شركة جديدة
     */
    public function showRegistrationForm()
    {
        $partnershipTypes = Company::partnershipTypeLabels();
        return view('auth.company-register', compact('partnershipTypes'));
    }

    /**
     * معالجة طلب تسجيل شركة جديدة
     */
    public function register(Request $request)
    {
        // 1. فحص مصيدة الروبوتات (Honeypot)
        $securityService = app(\App\Services\SecurityService::class);
        $honeypot = $securityService->verifyHoneypot($request);
        if (!$honeypot['success']) {
            return back()->withInput()->withErrors([
                'company_name' => $honeypot['message'],
            ]);
        }

        // 2. التحقق من كاشف الروبوتات (Cloudflare Turnstile)
        $turnstile = $securityService->verifyTurnstile($request->input('cf-turnstile-response'), $request->ip());
        if (!$turnstile['success']) {
            return back()->withInput()->withErrors([
                'cf-turnstile-response' => $turnstile['message'],
                'security' => $turnstile['message'],
            ]);
        }

        // 3. تقييد معدل طلبات تسجيل الشركات للحماية من هجمات الإغراق
        $throttleKey = 'register-company|' . $request->ip();
        $maxAttempts = config('security.rate_limits.register_max_attempts', 3);
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withInput()->withErrors([
                'email' => "تم تجاوز عدد محاولات التسجيل المسموح بها من هذا الجهاز. يرجى الانتظار {$seconds} ثانية.",
            ]);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300);

        $request->validate([
            // بيانات ممثل الشركة والحساب
            'contact_name' => 'required|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email|unique:companies,email',
            'password' => 'required|string|min:8|confirmed',

            // بيانات المؤسسة أو الشركة
            'company_name' => 'required|string|max:255|unique:companies,name',
            'phone' => 'nullable|string|max:20',
            'industry' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'partnership_types' => 'required|array|min:1',
            'partnership_types.*' => 'in:employment,training,logistic_support,academic,workshops,training_employment',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'contact_name.required' => 'اسم ممثل الشركة / مسؤول الاتصال مطلوب',
            'contact_phone.required' => 'رقم هاتف مسؤول الاتصال مطلوب',
            'email.required' => 'البريد الإلكتروني للشركة مطلوب',
            'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقاً في النظام',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'company_name.required' => 'اسم الشركة أو المؤسسة مطلوب',
            'company_name.unique' => 'اسم الشركة مسجل لدينا مسبقاً',
            'industry.required' => 'قطاع ومجال العمل مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'address.required' => 'عنوان المقر الرئيسي مطلوب',
            'partnership_types.required' => 'يرجى اختيار نوع شراكة واحد على الأقل',
            'partnership_types.min' => 'يرجى اختيار نوع شراكة واحد على الأقل',
            'logo.image' => 'يجب أن يكون الشعار ملف صورة صحيح',
            'logo.max' => 'حجم الشعار يجب ألا يتجاوز 2 ميجابايت',
        ]);

        DB::beginTransaction();

        try {
            // 1. إنشاء حساب المستخدم كشركة
            $user = User::create([
                'name' => $request->contact_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'company',
                'phone' => $request->contact_phone,
                'is_approved' => false, // قيد المراجعة
                'is_active' => false,   // معطل حتى الاعتماد
            ]);

            // 2. رفع الشعار إن وجد
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('companies/logos', 'public');
            }

            // تجميع العنوان مع المدينة
            $fullAddress = trim($request->city . ' - ' . $request->address, ' -');

            // 3. إنشاء سجل الشركة
            $primaryType = $request->partnership_types[0] ?? 'employment';

            $company = Company::create([
                'user_id' => $user->id,
                'name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone ?: $request->contact_phone,
                'industry' => $request->industry,
                'address' => $fullAddress,
                'website' => $request->website,
                'description' => $request->description,
                'logo_path' => $logoPath,
                'is_approved' => false,
                'partnership_type' => $primaryType,
                'partnership_types' => $request->partnership_types,
                'partnership_status' => 'under_review',
                'contact_person' => $request->contact_name,
                'contact_position' => $request->contact_position,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->email,
            ]);

            // 4. تسجيل النشاط
            AuditLog::logAction(
                'COMPANY_SELF_REGISTERED',
                'Company',
                $company->id,
                null,
                ['name' => $company->name, 'email' => $company->email, 'industry' => $company->industry]
            );

            // 5. إشعار المسؤولين المخولين (مسؤول الشراكات ومدير النظام)
            try {
                $this->notificationService->notifyNewCompanyRegistration($company);
            } catch (\Exception $e) {
                Log::error('فشل إرسال إشعار تسجيل شركة جديدة: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('company.register.success')->with([
                'company_name' => $company->name,
                'email' => $company->email,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ أثناء تسجيل الشركة: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ غير متوقع أثناء معالجة الطلب، يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني.'])
                ->withInput();
        }
    }

    /**
     * صفحة نجاح تقديم الطلب
     */
    public function success()
    {
        return view('auth.company-register-success');
    }
}
