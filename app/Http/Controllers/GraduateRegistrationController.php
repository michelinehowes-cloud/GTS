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
        // 1. فحص مصيدة الروبوتات (Honeypot)
        $securityService = app(\App\Services\SecurityService::class);
        $honeypot = $securityService->verifyHoneypot($request);
        if (!$honeypot['success']) {
            return back()->withInput()->withErrors([
                'name' => $honeypot['message'],
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

        // 3. تقييد معدل طلبات التسجيل للحماية من إنشاء الحسابات العشوائية
        $throttleKey = 'register-grad|' . $request->ip();
        $maxAttempts = config('security.rate_limits.register_max_attempts', 3);
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withInput()->withErrors([
                'email' => "تم تجاوز عدد محاولات التسجيل المسموح بها من هذا الجهاز. يرجى الانتظار {$seconds} ثانية.",
            ]);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300);

        $request->validate([
            'name' => 'required|string|max:255',
            'student_id' => 'required_without:national_id|nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
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
            'student_id.required' => 'رقم القيد الجامعي مطلوب',
            'student_id.required_without' => 'رقم القيد الجامعي مطلوب',
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

        // استخراج رقم القيد الجامعي
        $studentId = $request->input('student_id') ?: $request->input('national_id');

        // إنشاء حساب جديد بحالة غير موافق عليه
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'graduate',
            'phone' => $request->phone,
            'national_id' => $studentId, // تخزين رقم القيد الجامعي
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

        $audits = [];
        $auditSummary = [
            'valid' => 0,
            'warning' => 0,
            'invalid' => 0,
            'total' => $pendingUsers->count(),
        ];

        foreach ($pendingUsers as $u) {
            $eval = self::evaluateGraduateApplication($u);
            $audits[$u->id] = $eval;
            if (isset($auditSummary[$eval['verdict']])) {
                $auditSummary[$eval['verdict']]++;
            }
        }

        return view('career-guidance.pending-approvals', compact('pendingUsers', 'audits', 'auditSummary'));
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

        // 3. التحقق من وجود سجل مسبق في جدول graduates_data بالبريد الإلكتروني
        $existingGraduate = \App\Models\GraduateData::where('email', $user->email)->first();

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
                'national_id' => $user->national_id ?? null, // رقم القيد الجامعي
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
            \Log::error('GraduateData creation error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return [
                'status' => 'warning',
                'message' => 'تم تفعيل حساب الخريج بنجاح، مع تعذر استكمال بيانات الإرشاد تلقائياً. يرجى مراجعة سجلات النظام.'
            ];
        }
    }

    /**
     * فحص وتدقيق طلبات تسجيل الخريجين بالذكاء الاصطناعي
     */
    public function aiAudit(Request $request)
    {
        $ids = $request->input('ids', []);

        $query = User::where('role', 'graduate')->where('is_approved', false);
        if (!empty($ids) && is_array($ids)) {
            $query->whereIn('id', $ids);
        }
        $pendingUsers = $query->get();

        if ($pendingUsers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد طلبات معلقة لفحصها حالياً.'
            ], 404);
        }

        $results = [];
        $validIds = [];
        $warningIds = [];
        $invalidIds = [];

        foreach ($pendingUsers as $user) {
            $eval = self::evaluateGraduateApplication($user);
            $results[$user->id] = $eval;

            if ($eval['verdict'] === 'valid') {
                $validIds[] = $user->id;
            } elseif ($eval['verdict'] === 'warning') {
                $warningIds[] = $user->id;
            } else {
                $invalidIds[] = $user->id;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تدقيق وفحص الطلبات بنجاح بالذكاء الاصطناعي',
            'total_audited' => count($pendingUsers),
            'counts' => [
                'valid' => count($validIds),
                'warning' => count($warningIds),
                'invalid' => count($invalidIds),
            ],
            'valid_ids' => $validIds,
            'warning_ids' => $warningIds,
            'invalid_ids' => $invalidIds,
            'results' => $results,
        ]);
    }

    /**
     * محرك التدقيق الذكي لطلب تسجيل الخريج
     * يفحص صحة المدخلات بدقة دون الحاجة لرقم وطني أو سجل جامعي مسبق
     */
    public static function evaluateGraduateApplication(User $user): array
    {
        $score = 100;
        $flags = [];
        $verdict = 'valid';

        // 1. فحص واقعية وجودة الاسم الكامل (Full Name Realism)
        $name = trim($user->name ?? '');
        $nameWords = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY);
        $nameWordCount = count($nameWords);

        // كشف الكلمات التجريبية أو الوهمية
        $isMock = false;
        $mockPatterns = ['/test/i', '/demo/i', '/asdf/i', '/123/', '/تجربة/u', '/وهمي/u', '/خريج خريج/u', '/فلان/u', '/مجهول/u', '/abc/i'];
        foreach ($mockPatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                $isMock = true;
                break;
            }
        }

        // كشف الألقاب والأسماء المستعارة غير الرسمية
        $nicknames = ['برنس', 'كول', 'الملك', 'الأسطورة', 'الزعيم', 'امير', 'أبو فهد', 'بيبو', 'الجوكر', 'حمودي', 'عبودي', 'سوسو', 'البرنس'];
        $hasNickname = false;
        foreach ($nicknames as $nick) {
            if (mb_stripos($name, $nick) !== false) {
                $hasNickname = true;
                break;
            }
        }

        // كشف التكرار الشاذ للأحرف
        $hasRepeatedChars = preg_match('/(.)\1{3,}/u', $name);

        if ($isMock) {
            $score -= 60;
            $flags[] = [
                'type' => 'danger',
                'field' => 'name',
                'title' => 'اسم وهمي أو تجريبي',
                'msg' => 'الاسم يحتوي على كلمات تجريبية أو وهمية غير مقبولة.'
            ];
        } elseif ($hasNickname) {
            $score -= 35;
            $flags[] = [
                'type' => 'warning',
                'field' => 'name',
                'title' => 'لقب أو اسم مستعار',
                'msg' => 'الاسم يبدو كلقب أو اسم مستعار وليس اسماً رسمياً ثلاثياً أو رباعياً.'
            ];
        } elseif ($hasRepeatedChars) {
            $score -= 30;
            $flags[] = [
                'type' => 'warning',
                'field' => 'name',
                'title' => 'تكرار عشوائي للأحرف',
                'msg' => 'الاسم يحتوي على تكرار عشوائي للأحرف.'
            ];
        } elseif ($nameWordCount < 3) {
            $score -= 20;
            $flags[] = [
                'type' => 'warning',
                'field' => 'name',
                'title' => 'الاسم أقل من ثلاثة مقاطع',
                'msg' => 'الاسم ثنائي فقط، يُفضل الاسم الثلاثي أو الرباعي المعتمد.'
            ];
        }

        // 2. فحص الاتساق الأكاديمي (الكلية vs التخصص)
        $faculty = trim($user->faculty ?? '');
        $specialization = trim($user->specialization ?? $user->major ?? '');

        if (mb_strlen($specialization) < 3 || preg_match('/^[a-z0-9]{1,3}$/i', $specialization)) {
            $score -= 35;
            $flags[] = [
                'type' => 'danger',
                'field' => 'specialization',
                'title' => 'تخصص غير واضح',
                'msg' => 'التخصص غير محدد أو يحتوي على حروف عشوائية.'
            ];
        }

        $domainRules = [
            'it' => [
                'faculties' => ['تقنية المعلومات', 'الحاسوب', 'هندسة الحاسوب', 'it', 'computer', 'information technology'],
                'specs' => ['برمجيات', 'حاسوب', 'شبكات', 'أمن سيبراني', 'نظم معلومات', 'ذكاء اصطناعي', 'بيانات', 'انترنت', 'سوفتوير', 'it', 'cs', 'software', 'cyber', 'network', 'ai'],
                'incompatible_specs' => ['طب', 'صيدلة', 'جراحة', 'أسنان', 'قانون', 'شريعة', 'تاريخ', 'جغرافيا', 'تمريض']
            ],
            'med' => [
                'faculties' => ['الطب البشري', 'الصيدلة', 'العلوم الطبية', 'الطب', 'الأسنان', 'التمريض', 'الصحة العامة', 'medicine', 'pharmacy'],
                'specs' => ['طب', 'جراحة', 'صيدلة', 'مختبرات', 'أسنان', 'تمريض', 'تخدير', 'أشعة', 'بشري', 'علاج طبيعي', 'تغذية', 'صحة عامة'],
                'incompatible_specs' => ['برمجيات', 'هندسة ميكانيكية', 'هندسة كهربائية', 'محاسبة', 'إدارة أعمال', 'قانون', 'اقتصاد']
            ],
            'eng' => [
                'faculties' => ['الهندسة', 'engineering', 'كلية الهندسة'],
                'specs' => ['مدنية', 'ميكانيكية', 'كهربائية', 'معمارية', 'نفط', 'كيميائية', 'صناعية', 'تعدين', 'اتصالات', 'حاسوب', 'طيران', 'مساحة', 'هندسة'],
                'incompatible_specs' => ['طب بشري', 'صيدلة', 'قانون', 'محاسبة', 'لغة عربية', 'تاريخ']
            ],
            'econ' => [
                'faculties' => ['الاقتصاد', 'العلوم السياسية', 'التجارة', 'الإدارة', 'economics', 'business'],
                'specs' => ['محاسبة', 'إدارة', 'تسويق', 'تمويل', 'مصارف', 'اقتصاد', 'علوم سياسية', 'مالية', 'تأمين'],
                'incompatible_specs' => ['طب', 'صيدلة', 'جراحة', 'هندسة مدنية', 'هندسة نفطية']
            ],
            'law' => [
                'faculties' => ['القانون', 'الحقوق', 'الشريعة', 'law'],
                'specs' => ['قانون', 'حقوق', 'شريعة', 'دراسات إسلامية', 'قانون عام', 'قانون خاص', 'قضائي'],
                'incompatible_specs' => ['طب', 'هندسة', 'برمجيات', 'صيدلة']
            ]
        ];

        $matchedDomain = null;
        foreach ($domainRules as $domainKey => $rule) {
            foreach ($rule['faculties'] as $fKeyword) {
                if (mb_stripos($faculty, $fKeyword) !== false) {
                    $matchedDomain = $rule;
                    break 2;
                }
            }
        }

        if ($matchedDomain) {
            $incompatible = false;
            foreach ($matchedDomain['incompatible_specs'] as $badSpec) {
                if (mb_stripos($specialization, $badSpec) !== false) {
                    $incompatible = true;
                    break;
                }
            }
            if ($incompatible) {
                $score -= 40;
                $flags[] = [
                    'type' => 'danger',
                    'field' => 'academic',
                    'title' => 'تعارض أكاديمي صريح',
                    'msg' => "يوجد تعارض بين الكلية ({$faculty}) والتخصص ({$specialization})."
                ];
            }
        }

        // 3. فحص التسلسل الزمني وتاريخ الميلاد وسنة التخرج (Timeline Sanity)
        $dob = $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth) : null;
        $gradYear = (int) ($user->graduation_year ?? 0);
        $currentYear = (int) date('Y');

        if ($gradYear < 1960 || $gradYear > ($currentYear + 1)) {
            $score -= 30;
            $flags[] = [
                'type' => 'danger',
                'field' => 'grad_year',
                'title' => 'سنة تخرج غير منطقية',
                'msg' => "سنة التخرج ({$gradYear}) خارج النطاق المنطقي."
            ];
        }

        if ($dob) {
            $ageAtGrad = $gradYear - $dob->year;
            if ($ageAtGrad < 19) {
                $score -= 40;
                $flags[] = [
                    'type' => 'danger',
                    'field' => 'timeline',
                    'title' => 'عمر تخرج غير ممكن',
                    'msg' => "العمر عند التخرج ({$ageAtGrad} سنة) غير منطقي أكاديمياً."
                ];
            } elseif ($ageAtGrad > 65) {
                $score -= 15;
                $flags[] = [
                    'type' => 'warning',
                    'field' => 'timeline',
                    'title' => 'عمر تخرج متقدم',
                    'msg' => "العمر عند التخرج ({$ageAtGrad} سنة) غير معتاد، يرجى التحقق."
                ];
            }
        } else {
            $score -= 10;
            $flags[] = [
                'type' => 'warning',
                'field' => 'dob',
                'title' => 'تاريخ الميلاد غير مسجل',
                'msg' => 'لم يتم إدخال تاريخ الميلاد.'
            ];
        }

        // 4. فحص بيانات الاتصال (الهاتف والبريد الإلكتروني)
        $phone = preg_replace('/[^0-9]/', '', (string) ($user->phone ?? ''));
        $isLibyanPhone = preg_match('/^(09[1-5][0-9]{7}|2189[1-5][0-9]{7}|9[1-5][0-9]{7})$/', $phone);
        $isFakePhone = preg_match('/^(\d)\1{6,}$/', $phone) || in_array($phone, ['12345678', '1234567890', '0912345678', '0911111111']);

        if ($isFakePhone || strlen($phone) < 8) {
            $score -= 30;
            $flags[] = [
                'type' => 'danger',
                'field' => 'phone',
                'title' => 'رقم هاتف غير صحيح أو وهمي',
                'msg' => 'رقم الهاتف يبدو متسلسلاً أو وهمياً أو غير مكتمل.'
            ];
        } elseif (!$isLibyanPhone) {
            $score -= 10;
            $flags[] = [
                'type' => 'info',
                'field' => 'phone',
                'title' => 'تنسيق هاتف غير ليبي',
                'msg' => 'رقم الهاتف ليس بتنسيق شبكات الاتصال الليبية المعتادة.'
            ];
        }

        $email = trim($user->email ?? '');
        $disposableDomains = ['tempmail.com', '10minutemail.com', 'mailinator.com', 'trashmail.com', 'guerrillamail.com', 'sharklasers.com', 'dispostable.com', 'test.com', 'example.com'];
        $emailDomain = substr(strrchr($email, "@"), 1);
        if (in_array(strtolower($emailDomain), $disposableDomains)) {
            $score -= 50;
            $flags[] = [
                'type' => 'danger',
                'field' => 'email',
                'title' => 'بريد إلكتروني مؤقت أو وهمي',
                'msg' => 'تم استخدام مزود بريد مؤقت أو تجريبي غير موثوق.'
            ];
        }

        // 5. فحص رقم القيد الجامعي (Student University Registration ID)
        $studentId = trim((string) ($user->national_id ?? ''));
        if (empty($studentId)) {
            $score -= 20;
            $flags[] = [
                'type' => 'warning',
                'field' => 'student_id',
                'title' => 'رقم القيد غير مسجل',
                'msg' => 'لم يتم إدخال رقم القيد الجامعي للخريج.'
            ];
        } else {
            // كشف أرقام القيد الوهمية أو المتكررة
            $isFakeId = preg_match('/^(\d)\1{4,}$/', $studentId) || in_array($studentId, ['123', '1234', '12345', '123456', '0000', '00000', 'asdf', 'test', 'none']);
            if ($isFakeId || strlen($studentId) < 3) {
                $score -= 35;
                $flags[] = [
                    'type' => 'danger',
                    'field' => 'student_id',
                    'title' => 'رقم قيد غير صالح أو وهمي',
                    'msg' => 'رقم القيد الجامعي يبدو وهمياً أو غير مكتمل.'
                ];
            }
        }

        // 6. فحص المعدل التراكمي
        if (!is_null($user->gpa)) {
            $gpa = (float) $user->gpa;
            if ($gpa < 0 || $gpa > 100) {
                $score -= 25;
                $flags[] = [
                    'type' => 'danger',
                    'field' => 'gpa',
                    'title' => 'معدل تراكمي غير صحيح',
                    'msg' => "المعدل التراكمي ({$gpa}%) خارج النطاق المقبول."
                ];
            }
        }

        $score = max(0, min(100, $score));

        $dangerCount = count(array_filter($flags, fn($f) => $f['type'] === 'danger'));
        $warningCount = count(array_filter($flags, fn($f) => $f['type'] === 'warning'));

        if ($dangerCount > 0 || $score < 60) {
            $verdict = 'invalid';
            $summaryText = 'بيانات مشبوهة أو غير صحيحة';
            $recommendation = 'يُوصى برفض الطلب أو التواصل مع المتقدم لإعادة تصحيح البيانات.';
            $badgeColor = 'danger';
            $badgeText = '🔴 بيانات غير متطابقة';
        } elseif ($warningCount > 0 || $score < 85) {
            $verdict = 'warning';
            $summaryText = 'بيانات تحتاج مراجعة وتدقيق بشري';
            $recommendation = 'البيانات شبه مكتملة ولكن يُوصى بالتحقق من الملاحظات قبل الاعتماد.';
            $badgeColor = 'warning';
            $badgeText = '🟡 يحتاج تدقيق';
        } else {
            $verdict = 'valid';
            $summaryText = 'بيانات سليمة وموثوقة';
            $recommendation = 'الطلب مستوفٍ لكافة المعايير ومؤهل للاعتماد والموافقة الفورية.';
            $badgeColor = 'success';
            $badgeText = '🟢 سليم وموثوق';
        }

        return [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'score' => $score,
            'verdict' => $verdict,
            'badge_color' => $badgeColor,
            'badge_text' => $badgeText,
            'summary' => $summaryText,
            'recommendation' => $recommendation,
            'flags' => $flags,
        ];
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
