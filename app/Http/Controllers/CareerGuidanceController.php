<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GraduateData;
use App\Models\JobOpportunity;
use App\Models\Nomination;
use App\Models\Company;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TrainingApplication;
use App\Models\Certificate;
use App\Models\JobFairRegistration;


class CareerGuidanceController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * عرض لوحة تحكم مسؤول الإرشاد المهني
     */
    public function dashboard()
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasAnyPermission(['graduates.view', 'nominations.manage']) && $user->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بالوصول إلى لوحة الإرشاد المهني');
        }

        // إحصائيات الخريجين: تعتمد على جدول المستخدمين (role=graduate) وليس فقط من أكملوا ملفاتهم
        $totalGraduateUsers = User::where('role', 'graduate')->count();
        $stats = [
            'totalGraduates'       => $totalGraduateUsers,
            'employedGraduates'    => GraduateData::where('employment_status', 'employed')->count(),
            'seekingOpportunities' => GraduateData::where('employment_status', 'seeking_opportunities')->count(),
            'totalNominations'     => Nomination::count(),
            'pendingNominations'   => Nomination::where('status', 'pending')->count(),
            'acceptedNominations'  => Nomination::where('final_status', 'hired')->count(),
            'totalOpportunities'   => JobOpportunity::where('status', 'open')->count(),
            // عدد من لم يكملوا ملفاتهم بعد
            'pendingProfileCount'  => User::where('role', 'graduate')->doesntHave('graduateData')->count(),
        ];

        $recentGraduates = User::where('role', 'graduate')
            ->with('graduateData')
            ->latest()
            ->take(5)
            ->get();
        $recentNominations = Nomination::with(['graduate', 'jobOpportunity'])
            ->latest()
            ->take(5)
            ->get();

        return view('career-guidance.dashboard', compact('stats', 'recentGraduates', 'recentNominations'));
    }

    /**
     * عرض نموذج إضافة خريج جديد
     */
    public function createGraduate()
    {
        $this->authorize('create', GraduateData::class);

        $majors = GraduateData::distinct()->pluck('major');
        $graduationYears = range(date('Y') - 5, date('Y') + 1);
        $employmentStatuses = [
            'employed' => 'موظف',
            'seeking_opportunities' => 'يبحث عن فرصة عمل',
            'unemployed' => 'عاطل عن العمل',
            'further_study' => 'يكمل دراسته'
        ];
        $degrees = [
            'بكالوريوس' => 'بكالوريوس',
            'ماجستير' => 'ماجستير',
            'دكتوراه' => 'دكتوراه',
            'دبلوم' => 'دبلوم'
        ];

        return view('career-guidance.graduates.create', compact('majors', 'graduationYears', 'employmentStatuses', 'degrees'));
    }

    /**
     * حفظ بيانات الخريج الجديد
     */
    public function storeGraduate(Request $request)
    {
        // التطبيع بين المسميات المتطابقة
        if (!$request->filled('major') && $request->filled('specialization')) {
            $request->merge(['major' => $request->input('specialization')]);
        }
        if (!$request->filled('specialization') && $request->filled('major')) {
            $request->merge(['specialization' => $request->input('major')]);
        }
        if (!$request->filled('degree') && $request->filled('qualification')) {
            $request->merge(['degree' => $request->input('qualification')]);
        }
        if (!$request->filled('qualification') && $request->filled('degree')) {
            $request->merge(['qualification' => $request->input('degree')]);
        }
        if (!$request->filled('work_experience') && $request->filled('experiences')) {
            $request->merge(['work_experience' => $request->input('experiences')]);
        }
        if (!$request->filled('employment_status')) {
            $request->merge(['employment_status' => 'seeking_opportunities']);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:graduates_data,email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'national_id' => 'nullable|string|max:50|unique:graduates_data,national_id|unique:users,national_id',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female',
                'city' => 'nullable|string|max:100',
                'university' => 'required|string|max:255',
                'sector' => 'required|string|max:100',
                'faculty' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'degree' => 'nullable|string|max:255',
                'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
                'gpa' => 'nullable|numeric|min:0|max:100',
                'employment_status' => 'required|in:employed,seeking_opportunities,unemployed,further_study',
                'current_job_title' => 'nullable|string|max:255',
                'current_company' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'experiences' => 'nullable|string',
                'education' => 'nullable|string',
                'address' => 'nullable|string|max:500',
                'notes' => 'nullable|string',
            ], [
                'name.required' => 'الاسم مطلوب',
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
                'university.required' => 'حقل الجامعة مطلوب',
                'sector.required' => 'حقل القطاع مطلوب',
                'faculty.required' => 'حقل الكلية مطلوب',
                'major.required' => 'حقل التخصص مطلوب',
                'graduation_year.required' => 'حقل سنة التخرج مطلوب',
                'employment_status.required' => 'حقل حالة التوظيف مطلوب',
            ]);

            // Ensure the user is authenticated before attempting to get Auth::id()
            if (!Auth::check()) {
                Log::error('فشل إضافة خريج جديد: المستخدم غير مصادق عليه.', [
                    'request_ip' => $request->ip(),
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'يجب أن تكون مسجل الدخول لإضافة خريج جديد.');
            }

            // إنشاء كلمة مرور مؤقتة قوية
            $temporaryPassword = 'Gr' . date('Y') . '@' . \Illuminate\Support\Str::random(6);

            $degreeValue = $request->input('degree', $request->input('qualification', 'بكالوريوس'));

            // إنشاء حساب مستخدم للخريج
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($temporaryPassword),
                'role' => 'graduate',
                'phone' => $validated['phone'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'city' => $validated['city'] ?? null,
                'address' => $validated['address'] ?? null,
                'university' => $validated['university'],
                'sector' => $validated['sector'],
                'faculty' => $validated['faculty'],
                'major' => $validated['major'],
                'specialization' => $validated['major'],
                'degree' => $degreeValue,
                'qualification' => $degreeValue,
                'graduation_year' => $validated['graduation_year'],
                'gpa' => $validated['gpa'] ?? null,
                'skills' => is_string($request->input('skills')) ? array_values(array_filter(array_map('trim', explode(',', $request->input('skills'))))) : $request->input('skills'),
                'experiences' => $validated['experiences'] ?? null,
                'is_active' => true,
                'must_change_password' => true, // إجبار تغيير كلمة المرور
                'is_approved' => true, // الموافقة التلقائية لأن مسؤول الإرشاد هو من أضافه
            ]);

            // إنشاء سجل في جدول الخريجين
            $validated['added_by'] = Auth::id();
            $validated['user_id'] = $user->id; // ربط الخريج بالمستخدم
            $validated['degree'] = $degreeValue;
            $validated['qualification'] = $degreeValue;
            $validated['specialization'] = $validated['major'];
            if (!empty($validated['skills']) && is_string($validated['skills'])) {
                $validated['skills'] = array_values(array_filter(array_map('trim', explode(',', $validated['skills']))));
            }
            if (!empty($validated['experiences'])) {
                $validated['work_experience'] = $validated['experiences'];
            }
            $graduate = GraduateData::create($validated);

            // إرسال إيميل بالبيانات
            try {
                $user->notify(new \App\Notifications\GraduateAccountCreated(
                    $validated['email'],
                    $temporaryPassword,
                    $validated['name']
                ));

                Log::info('تم إرسال بيانات الدخول للخريج عبر البريد الإلكتروني', [
                    'user_id' => $user->id,
                    'email' => $validated['email']
                ]);
            } catch (\Exception $e) {
                Log::error('فشل إرسال البريد الإلكتروني للخريج: ' . $e->getMessage());
                // نكمل العملية حتى لو فشل الإيميل
            }

            Log::info('تمت إضافة خريج جديد مع حساب مستخدم', [
                'user_id' => Auth::id(),
                'graduate_id' => $graduate->id,
                'graduate_name' => $graduate->name,
                'account_created' => true
            ]);

            return redirect()
                ->route('career-guidance.graduates.show', $graduate->id)
                ->with('success', 'تمت إضافة بيانات الخريج بنجاح وتم إرسال بيانات الدخول إلى بريده الإلكتروني');

        } catch (ValidationException $e) {
            Log::warning('فشل التحقق من صحة البيانات عند إضافة خريج جديد: ' . $e->getMessage(), [
                'errors' => $e->errors()
            ]);

            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'الرجاء التحقق من البيانات المدخلة. ربما البريد الإلكتروني أو الرقم الوطني مسجل مسبقاً.');
        } catch (\Exception $e) {
            Log::error('فشل إضافة خريج جديد: ' . $e->getMessage(), [
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ غير متوقع أثناء محاولة إضافة الخريج. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض قائمة الخريجين
     */
    public function graduates(Request $request)
    {
        $this->authorize('viewAny', GraduateData::class);

        // نعتمد على جدول users (role=graduate) كقاعدة، مع تحميل graduate_data إذا وُجدت
        $query = User::where('role', 'graduate')->with('graduateData');

        // بحث شامل (الاسم، الهاتف، البريد، الرقم الوطني)
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('graduateData', function ($gq) use ($searchTerm) {
                      $gq->where('national_id', 'like', '%' . $searchTerm . '%')
                         ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // بحث مخصص بالاسم
        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', '%' . $name . '%');
        }

        // بحث مخصص برقم الهاتف
        if ($request->filled('phone')) {
            $phone = trim($request->phone);
            $query->where(function ($q) use ($phone) {
                $q->where('phone', 'like', '%' . $phone . '%')
                  ->orWhereHas('graduateData', fn ($gq) => $gq->where('phone', 'like', '%' . $phone . '%'));
            });
        }

        // تطبيق الفلاتر على بيانات الخريج (graduateData)
        if ($request->filled('major')) {
            $query->where(function ($q) use ($request) {
                $q->where('major', 'like', '%' . $request->major . '%')
                  ->orWhereHas('graduateData', fn ($gq) => $gq->where('major', 'like', '%' . $request->major . '%'));
            });
        }

        if ($request->filled('graduation_year')) {
            $query->where(function ($q) use ($request) {
                $q->where('graduation_year', $request->graduation_year)
                  ->orWhereHas('graduateData', fn ($gq) => $gq->where('graduation_year', $request->graduation_year));
            });
        }

        if ($request->filled('employment_status')) {
            $status = $request->employment_status;
            $statuses = ($status === 'continuing_education' || $status === 'further_study')
                ? ['continuing_education', 'further_study']
                : [$status];
            $query->whereHas('graduateData', fn ($gq) => $gq->whereIn('employment_status', $statuses));
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $graduates */
        $graduates = $query->latest()->paginate(25);
        $graduates->withQueryString();

        // احسب عدد ترشيحات كل خريج (عبر GraduateData)
        $graduateDataIds = $graduates->getCollection()->pluck('graduateData.id')->filter();
        $nominationCounts = \DB::table('nominations')
            ->whereIn('graduate_id', $graduateDataIds)
            ->selectRaw('graduate_id, COUNT(*) as total, SUM(CASE WHEN final_status="hired" THEN 1 ELSE 0 END) as hired')
            ->groupBy('graduate_id')
            ->get()
            ->keyBy('graduate_id');

        $graduates->getCollection()->each(function ($user) use ($nominationCounts) {
            $gdId = optional($user->graduateData)->id;
            $counts = $gdId ? ($nominationCounts[$gdId] ?? null) : null;
            $user->nominations_count = $counts ? $counts->total : 0;
            $user->accepted_nominations_count = $counts ? $counts->hired : 0;
        });

        $majors = GraduateData::whereNotNull('major')->where('major', '!=', '')->distinct()->pluck('major');
        $graduationYears = GraduateData::whereNotNull('graduation_year')->distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

        return view('career-guidance.graduates.index', compact('graduates', 'majors', 'graduationYears'));
    }

    /**
     * عرض نموذج تعديل بيانات الخريج
     */
    public function editGraduate($id)
    {
        $graduate = GraduateData::findOrFail($id);
        $this->authorize('update', $graduate); // Authorize editing the graduate details

        $majors = GraduateData::distinct()->pluck('major');
        $graduationYears = GraduateData::distinct()->pluck('graduation_year');
        $employmentStatuses = ['employed', 'seeking_opportunities', 'unemployed', 'further_study'];
        $degrees = ['بكالوريوس', 'ماجستير', 'دكتوراه', 'دبلوم'];

        return view('career-guidance.graduates.edit', compact('graduate', 'majors', 'graduationYears', 'employmentStatuses', 'degrees'));
    }

    /**
     * تحديث بيانات الخريج
     */
    public function updateGraduate(Request $request, $id)
    {
        $graduate = GraduateData::findOrFail($id);
        $this->authorize('update', $graduate); // Authorize updating the graduate details

        $oldEmail = $graduate->email;
        $oldUserId = $graduate->user_id;

        // العثور على حساب المستخدم المرتبط قبل إجراء التعديلات
        $targetUser = null;
        if ($graduate->user_id) {
            $targetUser = User::find($graduate->user_id);
        }
        if (!$targetUser && $oldEmail) {
            $targetUser = User::where('email', $oldEmail)->first();
        }

        // التطبيع بين المسميات المتطابقة (specialization <-> major و qualification <-> degree و experiences <-> work_experience)
        if (!$request->filled('major') && $request->filled('specialization')) {
            $request->merge(['major' => $request->input('specialization')]);
        }
        if (!$request->filled('specialization') && $request->filled('major')) {
            $request->merge(['specialization' => $request->input('major')]);
        }
        if (!$request->filled('degree') && $request->filled('qualification')) {
            $request->merge(['degree' => $request->input('qualification')]);
        }
        if (!$request->filled('qualification') && $request->filled('degree')) {
            $request->merge(['qualification' => $request->input('degree')]);
        }
        if (!$request->filled('work_experience') && $request->filled('experiences')) {
            $request->merge(['work_experience' => $request->input('experiences')]);
        }
        if (!$request->filled('experiences') && $request->filled('work_experience')) {
            $request->merge(['experiences' => $request->input('work_experience')]);
        }
        if (!$request->filled('employment_status')) {
            $request->merge(['employment_status' => $graduate->employment_status ?? 'seeking_opportunities']);
        }

        $userIdToIgnore = $targetUser ? $targetUser->id : null;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('graduates_data', 'email')->ignore($graduate->id),
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($userIdToIgnore),
            ],
            'phone' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'university' => 'required|string|max:255',
            'sector' => 'required|string|max:100',
            'faculty' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'gpa' => 'nullable|numeric|min:0|max:100',
            'degree' => 'required|string|max:255',
            'skills' => 'nullable|string',
            'languages' => 'nullable|string',
            'employment_status' => 'required|in:employed,seeking_opportunities,unemployed,further_study',
            'work_experience' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:255',
            'cv' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل في حساب آخر',
            'university.required' => 'حقل الجامعة مطلوب',
            'sector.required' => 'حقل القطاع مطلوب',
            'faculty.required' => 'حقل الكلية مطلوب',
            'major.required' => 'حقل التخصص مطلوب',
            'degree.required' => 'حقل المؤهل العلمي مطلوب',
            'graduation_year.required' => 'حقل سنة التخرج مطلوب',
            'employment_status.required' => 'حقل حالة التوظيف مطلوب',
            'password.min' => 'يجب أن تتكون كلمة المرور من 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $skills = is_string($request->input('skills')) ? array_values(array_filter(array_map('trim', explode(',', $request->input('skills'))))) : $request->input('skills');
        $languages = is_string($request->input('languages')) ? array_values(array_filter(array_map('trim', explode(',', $request->input('languages'))))) : $request->input('languages');

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'city' => $request->city,
            'university' => $request->university,
            'sector' => $request->sector,
            'faculty' => $request->faculty,
            'major' => $request->major,
            'specialization' => $request->major,
            'graduation_year' => $request->graduation_year,
            'gpa' => $request->gpa,
            'degree' => $request->degree,
            'qualification' => $request->degree,
            'skills' => $skills,
            'languages' => $languages,
            'employment_status' => $request->employment_status,
            'work_experience' => $request->work_experience,
            'address' => $request->address,
            'linkedin_url' => $request->linkedin_url,
        ];

        // معالجة رفع السيرة الذاتية
        if ($request->hasFile('cv')) {
            // حذف السيرة الذاتية القديمة إن وجدت
            if ($graduate->cv_path && \Storage::exists('public/' . $graduate->cv_path)) {
                \Storage::delete('public/' . $graduate->cv_path);
            }

            // رفع السيرة الذاتية الجديدة
            $fileName = 'cv_' . $graduate->id . '_' . time() . '.pdf';
            $path = $request->file('cv')->storeAs('cvs', $fileName, 'public');
            $data['cv_path'] = $path;
        }

        // تحديث سجل الخريج في جدول graduates_data
        $graduate->update($data);

        // إذا كان هناك حساب مستخدم مرتبط أو تم العثور عليه
        if ($targetUser) {
            // ربط معرف المستخدم في سجل الخريج إذا لم يكن مرتبطاً
            if ($graduate->user_id !== $targetUser->id) {
                $graduate->updateQuietly(['user_id' => $targetUser->id]);
            }

            $userUpdateData = [
                'name' => $data['name'],
                'email' => $data['email'] ?? $targetUser->email, // مزامنة البريد الإلكتروني الجديد لحساب تسجيل الدخول
                'phone' => $data['phone'] ?? $targetUser->phone,
                'national_id' => $data['national_id'] ?? $targetUser->national_id,
                'date_of_birth' => $data['date_of_birth'] ?? $targetUser->date_of_birth,
                'gender' => $data['gender'] ?? $targetUser->gender,
                'city' => $data['city'] ?? $targetUser->city,
                'address' => $data['address'] ?? $targetUser->address,
                'university' => $data['university'],
                'sector' => $data['sector'],
                'faculty' => $data['faculty'],
                'major' => $data['major'],
                'specialization' => $data['major'],
                'degree' => $data['degree'],
                'qualification' => $data['degree'],
                'graduation_year' => $data['graduation_year'],
                'gpa' => $data['gpa'],
                'skills' => $data['skills'],
                'languages' => $data['languages'],
                'experiences' => $data['work_experience'] ?? $targetUser->experiences,
            ];

            // تحديث كلمة المرور في حساب المستخدم إن تم إدخالها
            if ($request->filled('password')) {
                $userUpdateData['password'] = Hash::make($request->password);
            }

            if (isset($data['cv_path'])) {
                $userUpdateData['cv_path'] = $data['cv_path'];
            }

            $targetUser->update($userUpdateData);
        } elseif ($request->filled('password') && !empty($data['email'])) {
            // إذا لم يكن هناك حساب مستخدم وتم إدخال كلمة مرور، يتم إنشاء حساب تسجيل الدخول وربطه فوراً
            $newUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($request->password),
                'role' => 'graduate',
                'is_active' => true,
                'phone' => $data['phone'] ?? null,
                'national_id' => $data['national_id'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'city' => $data['city'] ?? null,
                'address' => $data['address'] ?? null,
                'university' => $data['university'],
                'sector' => $data['sector'],
                'faculty' => $data['faculty'],
                'major' => $data['major'],
                'specialization' => $data['major'],
                'degree' => $data['degree'],
                'qualification' => $data['degree'],
                'graduation_year' => $data['graduation_year'],
                'gpa' => $data['gpa'],
                'skills' => $data['skills'],
                'languages' => $data['languages'],
                'experiences' => $data['work_experience'] ?? null,
                'cv_path' => $data['cv_path'] ?? null,
            ]);

            $graduate->updateQuietly(['user_id' => $newUser->id]);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات الخريج وحساب تسجيل الدخول بنجاح.');
    }

    /**
     * عرض تفاصيل الخريج
     */
    public function showGraduate($id)
    {
        $graduate = GraduateData::with(['nominations.jobOpportunity.company', 'nominations.nominator'])->findOrFail($id);
        $this->authorize('view', $graduate); // Authorize viewing the graduate details

        // البحث عن حساب المستخدم المرتبط أو ربطه بالبريد الإلكتروني
        $user = $graduate->user;
        if (!$user && $graduate->email) {
            $user = User::where('email', $graduate->email)->first();
            if ($user && !$graduate->user_id) {
                $graduate->updateQuietly(['user_id' => $user->id]);
            }
        }

        $trainingApplications = collect();
        $certificates = collect();
        $jobFairRegistrations = collect();

        if ($user) {
            $trainingApplications = TrainingApplication::where('user_id', $user->id)
                ->with(['training.company', 'attendances'])
                ->latest()
                ->get();

            $certificates = Certificate::where('user_id', $user->id)
                ->with(['training', 'company'])
                ->latest()
                ->get();

            $jobFairRegistrations = JobFairRegistration::where('user_id', $user->id)
                ->with('jobFair')
                ->latest()
                ->get();
        }

        // فرص العمل المتاحة للترشيح الفوري
        $openOpportunities = JobOpportunity::where('status', 'open')
            ->with('company')
            ->latest()
            ->get();

        return view('career-guidance.graduates.show', compact(
            'graduate',
            'user',
            'trainingApplications',
            'certificates',
            'jobFairRegistrations',
            'openOpportunities'
        ));
    }

    /**
     * إعادة تعيين كلمة مرور حساب الخريج
     */
    public function resetGraduatePassword(Request $request, $id)
    {
        $graduate = GraduateData::findOrFail($id);
        $this->authorize('update', $graduate);

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'new_password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'new_password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقين.',
        ]);

        $user = null;
        if ($graduate->user_id) {
            $user = User::find($graduate->user_id);
        }
        if (!$user && $graduate->email) {
            $user = User::where('email', $graduate->email)->first();
        }

        if (!$user) {
            if (!$graduate->email) {
                return redirect()->back()->withErrors(['password_error' => 'لا يمكن تعيين كلمة مرور لخريج لا يمتلك بريداً إلكترونياً. يرجى إضافة بريد إلكتروني للخريج أولاً.']);
            }

            $user = User::create([
                'name' => $graduate->name,
                'email' => $graduate->email,
                'password' => Hash::make($request->new_password),
                'role' => 'graduate',
                'is_active' => true,
                'phone' => $graduate->phone,
                'national_id' => $graduate->national_id,
            ]);
            $graduate->updateQuietly(['user_id' => $user->id]);
        } else {
            $updateData = [
                'password' => Hash::make($request->new_password),
            ];
            // مزامنة البريد الإلكتروني في جدول المستخدمين إذا كان مختلفاً
            if ($graduate->email && $user->email !== $graduate->email) {
                $updateData['email'] = $graduate->email;
            }
            $user->update($updateData);

            if ($graduate->user_id !== $user->id) {
                $graduate->updateQuietly(['user_id' => $user->id]);
            }
        }

        return redirect()->back()->with('success', 'تم تغيير كلمة مرور الخريج بنجاح!')->with('password_success', 'تم تغيير كلمة مرور الخريج بنجاح!');
    }

    /**
     * إنشاء حساب مستخدم للخريج إذا لم يكن موجوداً
     */
    public function createGraduateAccount(Request $request, $id)
    {
        $graduate = GraduateData::findOrFail($id);
        $this->authorize('update', $graduate);

        if ($graduate->user_id && User::where('id', $graduate->user_id)->exists()) {
            return redirect()->back()->withErrors(['error' => 'هذا الخريج يمتلك حساباً بالفعل.']);
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'كلمة المرور مطلوبة.',
            'new_password.min' => 'يجب أن تكون كلمة المرور 8 أحرف على الأقل.',
            'new_password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقين.',
        ]);

        $existingUser = User::where('email', $graduate->email)->first();
        if ($existingUser) {
            $existingUser->update([
                'password' => Hash::make($request->new_password),
            ]);
            $graduate->updateQuietly(['user_id' => $existingUser->id]);
            return redirect()->back()->with('password_success', 'تم ربط الحساب وتحديث كلمة المرور بنجاح!');
        }

        $user = User::create([
            'name' => $graduate->name,
            'email' => $graduate->email,
            'password' => Hash::make($request->new_password),
            'role' => 'graduate',
            'is_active' => true,
            'phone' => $graduate->phone,
            'national_id' => $graduate->national_id,
        ]);

        $graduate->updateQuietly(['user_id' => $user->id]);

        return redirect()->back()->with('password_success', 'تم إنشاء وربط حساب الخريج بنجاح!');
    }

    /**
     * عرض قائمة الترشيحات
     */
    public function nominations(Request $request)
    {
        $this->authorize('viewAny', Nomination::class);

        $query = Nomination::with(['graduate', 'jobOpportunity.company', 'nominator']);

        // تطبيق الفلاتر
        if ($request->has('opportunity_id') && $request->opportunity_id) {
            $query->where('job_opportunity_id', $request->opportunity_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('final_status') && $request->final_status) {
            $query->where('final_status', $request->final_status);
        }

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('nominated_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('nominated_at', '<=', $request->to_date);
        }

        $nominations = $query->latest()->get();
        $opportunities = JobOpportunity::where('status', 'open')->get();

        return view('career-guidance.nominations.index', compact('nominations', 'opportunities'));
    }

    public function showNomination($id)
    {
        $nomination = Nomination::with([
            'graduate',
            'jobOpportunity.company',
            'nominator'
        ])->findOrFail($id);

        $this->authorize('view', $nomination); // Authorize viewing the nomination

        return view('career-guidance.nominations.show', compact('nomination'));
    }

    /**
     * تحديث حالة الترشيح
     */
    public function updateNominationStatus(Request $request, $id)
    {
        $nomination = Nomination::findOrFail($id);

        // Authorize the action using the NominationPolicy
        $this->authorize('updateStatus', $nomination);

        $request->validate([
            'status' => 'required|in:pending,sent_to_company,under_review,interview_scheduled,accepted,rejected,withdrawn',
            'final_status' => 'nullable|in:hired,not_hired,in_progress',
            'nomination_notes' => 'nullable|string',
            'matching_reasons' => 'nullable|string',
            'interview_date' => 'nullable|date',
            'interview_time' => 'nullable|string',
            'interview_location' => 'nullable|string',
            'interview_notes' => 'nullable|string',
            'company_feedback' => 'nullable|string',
            'graduate_feedback' => 'nullable|string',
        ]);

        $nomination->update([
            'status' => $request->status,
            'final_status' => $request->final_status,
            'nomination_notes' => $request->nomination_notes,
            'matching_reasons' => $request->matching_reasons,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'interview_location' => $request->interview_location,
            'interview_notes' => $request->interview_notes,
            'company_feedback' => $request->company_feedback,
            'graduate_feedback' => $request->graduate_feedback,
            // Update timestamps based on status changes
            'sent_to_company_at' => ($request->status === 'sent_to_company' && !$nomination->sent_to_company_at) ? now() : $nomination->sent_to_company_at,
            'interview_at' => ($request->status === 'interview_scheduled' && !$nomination->interview_at) ? now() : $nomination->interview_at,
            'final_decision_at' => ($request->final_status && !$nomination->final_decision_at) ? now() : $nomination->final_decision_at,
        ]);

        // إرسال إشعار بتحديث حالة الترشيح
        try {
            $nomination->load(['graduate.user', 'jobOpportunity']);
            $this->notificationService->notifyNominationStatusUpdate($nomination, $request->status);
        } catch (\Exception $e) {
            \Log::error('Failed to send nomination status update notification: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الترشيح بنجاح');
    }

    /**
     * عرض نموذج ترشيح خريج جديد
     */
    public function createNomination()
    {
        $this->authorize('create', Nomination::class);

        $graduates = GraduateData::where('is_active', true)
            // ->where('employment_status', 'seeking_opportunities') // Temporarily remove this filter for debugging
            ->get();

        $opportunities = JobOpportunity::where('status', 'open')
            ->where('application_deadline', '>=', now())
            ->with('company')
            ->get();

        return view('career-guidance.nominations.create', compact('graduates', 'opportunities'));
    }

    /**
     * عرض نموذج تعديل حالة الترشيح
     */
    public function editNominationStatusForm($id)
    {
        $nomination = Nomination::with(['graduate', 'jobOpportunity.company', 'nominator'])->findOrFail($id);
        $this->authorize('updateStatus', $nomination); // Use the same policy for viewing the edit form

        return view('career-guidance.nominations.edit-status', compact('nomination'));
    }

    /**
     * تحديث حالة الترشيح من صفحة كاملة (Full Page)
     */
    public function updateNominationStatusFullPage(Request $request, $id)
    {
        $nomination = Nomination::findOrFail($id);

        // Authorize the action using the NominationPolicy
        $this->authorize('updateStatus', $nomination);

        $request->validate([
            'status' => 'required|in:pending,sent_to_company,under_review,interview_scheduled,accepted,rejected,withdrawn',
            'final_status' => 'nullable|in:hired,not_hired,in_progress',
            'nomination_notes' => 'nullable|string',
            'matching_reasons' => 'nullable|string',
            'interview_date' => 'nullable|date',
            'interview_time' => 'nullable|string',
            'interview_location' => 'nullable|string',
            'interview_notes' => 'nullable|string',
            'company_feedback' => 'nullable|string',
            'graduate_feedback' => 'nullable|string',
        ]);

        $nomination->update([
            'status' => $request->status,
            'final_status' => $request->final_status,
            'nomination_notes' => $request->nomination_notes,
            'matching_reasons' => $request->matching_reasons,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'interview_location' => $request->interview_location,
            'interview_notes' => $request->interview_notes,
            'company_feedback' => $request->company_feedback,
            'graduate_feedback' => $request->graduate_feedback,
            'sent_to_company_at' => ($request->status === 'sent_to_company' && !$nomination->sent_to_company_at) ? now() : $nomination->sent_to_company_at,
            'interview_at' => ($request->status === 'interview_scheduled' && !$nomination->interview_at) ? now() : $nomination->interview_at,
            'final_decision_at' => ($request->final_status && !$nomination->final_decision_at) ? now() : $nomination->final_decision_at,
        ]);

        // إرسال إشعار بتحديث حالة الترشيح
        try {
            $nomination->load(['graduate.user', 'jobOpportunity']);
            $this->notificationService->notifyNominationStatusUpdate($nomination, $request->status);
        } catch (\Exception $e) {
            \Log::error('Failed to send nomination status update notification: ' . $e->getMessage());
        }

        $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : (request()->routeIs('partnership.*') ? 'partnership' : 'career-guidance');
        return redirect()->route($prefix . '.nominations.edit-status', $id)
            ->with('success', 'تم تحديث حالة الترشيح بنجاح');
    }

    /**
     * ترشيح خريج لفرصة عمل
     */
    public function nominateGraduate(Request $request)
    {
        $this->authorize('create', Nomination::class); // Authorize creation of nominations

        $request->validate([
            'graduate_id' => 'required|exists:graduates_data,id',
            'job_opportunity_id' => 'required|exists:job_opportunities,id',
            'nomination_notes' => 'nullable|string',
            'matching_reasons' => 'required|string',
        ]);

        // التحقق من عدم وجود ترشيح مسبق
        $existingNomination = Nomination::where('graduate_id', $request->graduate_id)
            ->where('job_opportunity_id', $request->job_opportunity_id)
            ->first();

        if ($existingNomination) {
            return redirect()->back()->with('error', 'تم ترشيح هذا الخريج لهذه الفرصة مسبقاً');
        }

        $nomination = Nomination::create([
            'graduate_id' => $request->graduate_id,
            'job_opportunity_id' => $request->job_opportunity_id,
            'nominated_by' => Auth::id(),
            'nomination_notes' => $request->nomination_notes,
            'matching_reasons' => $request->matching_reasons,
            'status' => 'pending',
            'nominated_at' => now(),
        ]);

        // إرسال إشعار بالترشيح
        try {
            $nomination->load(['graduate.user', 'jobOpportunity']);
            $this->notificationService->notifyJobNomination($nomination, Auth::user());
        } catch (\Exception $e) {
            \Log::error('Failed to send job nomination notification: ' . $e->getMessage());
        }

        if ($request->filled('redirect_to_graduate') || $request->filled('redirect_back')) {
            return redirect()->back()->with('success', 'تم ترشيح الخريج لفرصة العمل بنجاح.');
        }

        $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : (request()->routeIs('partnership.*') ? 'partnership' : 'career-guidance');
        return redirect()->route($prefix . '.nominations')
            ->with('success', 'تم ترشيح الخريج بنجاح');
    }


    /**
     * إشعار الخريجين بفرص جديدة
     */
    public function notifyGraduates(Request $request)
    {
        $request->validate([
            'opportunity_id' => 'required|exists:job_opportunities,id',
            'graduate_ids' => 'required|array',
            'message' => 'required|string',
        ]);

        // هنا سيتم إرسال الإشعارات للخريجين
        // يمكن استخدام نظام الإشعارات في Laravel أو البريد الإلكتروني

        return redirect()->back()->with('success', 'تم إرسال الإشعارات بنجاح');
    }

    /**
     * تحميل نموذج Excel لاستيراد الخريجين
     */
    public function downloadTemplate()
    {
        $fileName = 'graduates_template.csv';

        $headers = [
            'name',
            'email',
            'phone',
            'major',
            'graduation_year',
            'gpa',
            'degree',
            'skills',
            'languages',
            'employment_status',
            'work_experience',
            'address',
            'linkedin_url'
        ];

        $sampleData = [
            [
                'أحمد محمد',
                'ahmed@example.com',
                '0912345678',
                'هندسة حاسوب',
                '2023',
                '3.75',
                'بكالوريوس',
                'برمجة,تصميم,إدارة',
                'عربية,إنجليزية',
                'seeking_opportunities',
                '2 سنوات في شركة X',
                'طرابلس - الحي القديم',
                'linkedin.com/in/ahmed'
            ],
            [
                'فاطمة علي',
                '',
                '0923456789',
                'علوم حاسوب',
                '2022',
                '',
                'بكالوريوس',
                'تحليل بيانات,ذكاء اصطناعي',
                'عربية,إنجليزية,فرنسية',
                'employed',
                'مطور برمجيات في شركة Y',
                'بنغازي - المدينة الجامعية',
                'linkedin.com/in/fatima'
            ]
        ];

        return response()->streamDownload(function () use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للحروف العربية
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // كتابة العناوين
            fputcsv($file, $headers);

            // كتابة البيانات النموذجية
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * عرض نموذج استيراد الخريجين
     */
    public function showImportForm()
    {
        $this->authorize('create', GraduateData::class);
        return view('career-guidance.graduates.import');
    }


    /**
     * استيراد الخريجين من ملف Excel/CSV
     */
    public function importGraduates(Request $request)
    {
        $this->authorize('create', GraduateData::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = $this->readCSV($path);

        $headers = array_map('trim', array_shift($data)); // Get headers and remove from data
        $expectedHeaders = [
            'name',
            'email',
            'phone',
            'major',
            'graduation_year',
            'gpa',
            'degree',
            'skills',
            'languages',
            'employment_status',
            'work_experience',
            'address',
            'linkedin_url'
        ];

        // Validate headers
        if (array_diff($expectedHeaders, $headers) || array_diff($headers, $expectedHeaders)) {
            return redirect()->back()->with('error', 'تنسيق الملف غير صحيح. يرجى استخدام النموذج المرفق.');
        }

        $importedCount = 0;
        $errors = [];

        foreach ($data as $rowNumber => $row) {
            try {
                // Map row data to an associative array using headers
                $rowData = array_combine($headers, $row);
                $this->createGraduateFromRow($rowData, $rowNumber + 2); // +2 for 0-indexed array and header row
                $importedCount++;
            } catch (\Exception $e) {
                $errors[] = "السطر " . ($rowNumber + 2) . ": " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
            return redirect()->back()->with('warning', 'تم استيراد بعض البيانات مع وجود أخطاء.');
        }

        return redirect()->back()->with('success', 'تم استيراد الخريجين بنجاح: ' . $importedCount . ' سجلات.');
    }

    private function validateChartData($chartData)
    {
        // بسيطة للتحقق من أن بيانات المخطط ليست فارغة
        foreach ($chartData as $key => $data) {
            if (empty($data['labels']) || empty($data['datasets'][0]['data'])) {
                return false;
            }
        }
        return true;
    }

    private function readCSV($path)
    {
        $data = [];
        if (($handle = fopen($path, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    private function createGraduateFromRow($row, $rowNumber)
    {
        // تنظيف البيانات
        $name = trim($row['name'] ?? '');
        $email = trim($row['email'] ?? '');
        $phone = trim($row['phone'] ?? '');
        $major = trim($row['major'] ?? '');
        $graduationYear = intval($row['graduation_year'] ?? 0);
        $gpa = !empty($row['gpa']) ? floatval($row['gpa']) : null;
        $degree = trim($row['degree'] ?? 'بكالوريوس');
        $skills = !empty($row['skills']) ? array_map('trim', explode(',', $row['skills'])) : [];
        $languages = !empty($row['languages']) ? array_map('trim', explode(',', $row['languages'])) : [];
        $employmentStatus = trim($row['employment_status'] ?? 'seeking_opportunities');
        $workExperience = trim($row['work_experience'] ?? '');
        $address = trim($row['address'] ?? '');
        $linkedinUrl = trim($row['linkedin_url'] ?? '');

        // التحقق من البيانات الأساسية (المطلوبة فقط)
        if (empty($name)) {
            throw new \Exception("الاسم الكامل مطلوب");
        }

        if (empty($major)) {
            throw new \Exception("التخصص مطلوب");
        }

        if ($graduationYear < 2000 || $graduationYear > date('Y')) {
            throw new \Exception("سنة التخرج غير صحيحة. يجب أن تكون بين 2000 و " . date('Y'));
        }

        // التحقق من صحة البريد الإلكتروني إذا تم إدخاله
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("تنسيق البريد الإلكتروني غير صحيح");
        }

        // التحقق من التكرار (البريد الإلكتروني اختياري الآن)
        if (!empty($email) && GraduateData::where('email', $email)->exists()) {
            throw new \Exception("البريد الإلكتروني مسجل مسبقاً");
        }

        // إنشاء الخريج
        GraduateData::create([
            'name' => $name,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'major' => $major,
            'graduation_year' => $graduationYear,
            'gpa' => $gpa,
            'degree' => $degree,
            'skills' => $skills,
            'languages' => $languages,
            'employment_status' => $employmentStatus,
            'work_experience' => $workExperience ?: null,
            'address' => $address ?: null,
            'linkedin_url' => $linkedinUrl ?: null,
            'added_by' => Auth::id(),
            'data_source' => 'excel_import',
            'is_active' => true,
        ]);
    }



    /**
     * التقارير المتقدمة مع المخططات البيانية
     */
    public function advancedReports(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('reports.view') && $user->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بعرض التقارير المتقدمة');
        }

        try {
            // جلب البيانات للفلاتر
            $majors = GraduateData::distinct()->pluck('major');
            $years = GraduateData::distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

            // الإحصائيات الأساسية مع تطبيق الفلاتر
            $filters = $request->only(['major', 'year', 'status']);
            $stats = $this->getAdvancedStats($filters);

            // بيانات المخططات الفعلية من قاعدة البيانات
            $chartData = $this->getChartData($filters);

            // استنتاجات ذكية مبنية على الإحصائيات الفعلية
            $insights = $this->getAIInsights($stats);

            \Log::info('Advanced Reports Loaded with real database data', [
                'graduates' => $stats['totalGraduates'],
                'charts' => count($chartData),
                'insights' => count($insights)
            ]);

            return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights', 'majors', 'years'));

        } catch (\Exception $e) {
            \Log::error('Advanced Reports Error: ' . $e->getMessage());

            $stats = $this->getAdvancedStats();
            $chartData = $this->getChartData();
            $insights = $this->getAIInsights($stats);
            $majors = [];
            $years = [];

            return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights', 'majors', 'years'))
                ->with('error', 'حدث خطأ أثناء تحميل بعض بيانات التقارير: ' . $e->getMessage());
        }
    }

    /**
     * تصدير التقرير كـ PDF
     */
    public function exportReportsPDF(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('reports.export') && $user->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بتصدير التقارير');
        }

        try {
            $type = $request->get('type', 'full');

            // تطبيق الفلاتر الحالية إذا وجدت
            $filters = $request->only(['major', 'year', 'status']);
            $stats = $this->getAdvancedStats($filters);
            $insights = $this->getAIInsights($stats);

            if ($stats['totalGraduates'] == 0) {
                return redirect()->back()
                    ->with('warning', 'لا توجد بيانات كافية لتصدير التقرير');
            }

            // معالجة النصوص العربية
            // نقوم بتحويل النصوص الثابتة المهمة أو البيانات التي ستظهر في PDF
            // ملاحظة: هذا حل مؤقت، الأفضل استخدام مكتبة ArPHP

            // مثال على استخدام Helper (إذا تم تفعيله)
            // $stats['totalGraduates'] = \App\Helpers\Arabic::reshape($stats['totalGraduates']);

            $fileName = "تقرير_الارشاد_المهني_" . date('Y-m-d') . ".pdf";

            // استخدام مكتبة PDF مع إعدادات مناسبة
            $pdf = PDF::loadView('career-guidance.reports-pdf', compact('stats', 'insights', 'type'))
                ->setPaper('a4', 'portrait')
                ->setOption('enable_remote', false)
                ->setOption('enable_php', false)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', false)
                ->setOption('defaultFont', 'dejavu sans'); // استخدام خط يدعم العربية

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تصدير التقرير: ' . $e->getMessage());
        }
    }


    /**
     * تصدير التقرير كـ Excel
     */
    public function exportReportsExcel(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->hasPermission('reports.export') && $user->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بتصدير التقارير');
        }

        try {
            // تعطيل مؤقت: المكتبة المثبتة قديمة ولا تدعم الواجهات الحديثة
            // يمكن تفعيلها بعد تحديث maatwebsite/excel إلى الإصدار 3.x

            return redirect()->back()
                ->with('info', 'تصدير Excel غير متاح حالياً. يرجى استخدام تصدير PDF بدلاً من ذلك.');

            /* الكود الأصلي - سيتم تفعيله بعد تحديث المكتبة
            $filters = $request->only(['major', 'year', 'status']);
            $stats = $this->getAdvancedStats($filters);

            if ($stats['totalGraduates'] == 0) {
                return redirect()->route('career-guidance.advanced-reports')
                    ->with('warning', 'لا توجد بيانات كافية لتصدير التقرير');
            }

            $insights = $this->getAIInsights($stats);
            return Excel::download(new \App\Exports\AdvancedReportsExport($stats, $insights), 'التقارير_المتقدمة.xlsx');
            */

        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تصدير التقرير: ' . $e->getMessage());
        }
    }

    /**
     * جمع الإحصائيات المتقدمة
     */
    private function getAdvancedStats($filters = [])
    {
        // بناء الاستعلام الأساسي مع الفلاتر
        $graduateQuery = GraduateData::query();

        if (!empty($filters['major'])) {
            $graduateQuery->where('major', $filters['major']);
        }
        if (!empty($filters['year'])) {
            $graduateQuery->where('graduation_year', $filters['year']);
        }
        if (!empty($filters['status'])) {
            $graduateQuery->where('employment_status', $filters['status']);
        }

        // حساب الإحصائيات بناءً على الفلاتر
        $totalGraduates = $graduateQuery->count();
        $employedGraduates = (clone $graduateQuery)->where('employment_status', 'employed')->count();
        $seekingOpportunities = (clone $graduateQuery)->where('employment_status', 'seeking_opportunities')->count();

        // إحصائيات الترشيحات (يمكن تطبيق فلاتر مماثلة إذا لزم الأمر)
        $nominationStats = Nomination::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN final_status = "hired" THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status NOT IN ("rejected", "withdrawn") THEN 1 ELSE 0 END) as active
        ')->first();

        return [
            'totalGraduates' => $totalGraduates,
            'employedGraduates' => $employedGraduates,
            'seekingOpportunities' => $seekingOpportunities,
            'employmentRate' => $totalGraduates > 0 ? round(($employedGraduates / $totalGraduates) * 100, 1) : 0,
            'seekingRate' => $totalGraduates > 0 ? round(($seekingOpportunities / $totalGraduates) * 100, 1) : 0,
            'activeNominations' => $nominationStats->active ?? 0,
            'successRate' => $this->calculateSuccessRate(), // يمكن تحديث هذه الدالة لتقبل فلاتر أيضاً
            'availableOpportunities' => JobOpportunity::where('status', 'open')->count(),
            'newGraduatesThisMonth' => GraduateData::whereMonth('created_at', now()->month)->count(),
            'newOpportunitiesThisWeek' => JobOpportunity::where('created_at', '>=', now()->subWeek())->count(),
            'overallSuccessRate' => $this->calculateOverallSuccessRate(),
        ];
    }

    /**
     * بيانات المخططات البيانية
     */
    private function getChartData($filters = [])
    {
        try {
            // توزيع الخريجين حسب التخصص
            $majorsDistribution = $this->getMajorsDistribution($filters);

            // حالة التوظيف
            $employmentStatus = $this->getEmploymentStatus($filters);

            // حالة الترشيحات
            $nominationsStatus = $this->getNominationsStatus(); // يمكن إضافة الفلاتر هنا أيضاً إذا لزم الأمر

            // الأداء الشهري
            $monthlyPerformance = $this->getMonthlyPerformance();

            // النجاح حسب التخصص
            $successByMajor = $this->getSuccessByMajor();

            // توزيع فرص العمل
            $opportunitiesDistribution = $this->getOpportunitiesDistribution();

            return [
                'majorsDistribution' => $majorsDistribution,
                'employmentStatus' => $employmentStatus,
                'nominationsStatus' => $nominationsStatus,
                'monthlyPerformance' => $monthlyPerformance,
                'successByMajor' => $successByMajor,
                'opportunitiesDistribution' => $opportunitiesDistribution,
            ];

        } catch (\Exception $e) {
            Log::error('Chart Data Error: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    /**
     * هيكل بيانات فارغ في حالة عدم وجود بيانات أو حدوث استثناء
     */
    private function getEmptyChartData()
    {
        return [
            'majorsDistribution' => [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'عدد الخريجين',
                        'data' => [],
                        'backgroundColor' => [],
                        'borderWidth' => 1
                    ]
                ]
            ],
            'employmentStatus' => [
                'labels' => [],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ],
            'nominationsStatus' => [
                'labels' => [],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ],
            'monthlyPerformance' => [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'إجمالي الترشيحات',
                        'data' => [],
                        'borderColor' => '#4e73df',
                        'backgroundColor' => 'rgba(78, 115, 223, 0.1)',
                        'fill' => true
                    ],
                    [
                        'label' => 'الترشيحات الناجحة',
                        'data' => [],
                        'borderColor' => '#1cc88a',
                        'backgroundColor' => 'rgba(28, 200, 138, 0.1)',
                        'fill' => true
                    ]
                ]
            ],
            'successByMajor' => [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'معدل النجاح %',
                        'data' => [],
                        'backgroundColor' => 'rgba(78, 115, 223, 0.2)',
                        'borderColor' => '#4e73df',
                    ]
                ]
            ],
            'opportunitiesDistribution' => [
                'labels' => [],
                'datasets' => [
                    [
                        'data' => [],
                        'backgroundColor' => [],
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ]
        ];
    }

    /**
     * استنتاجات ذكية
     */
    private function getAIInsights($stats)
    {
        $insights = [];

        if ($stats['employmentRate'] > 70) {
            $insights[] = [
                'icon' => 'trophy',
                'color' => 'success',
                'title' => 'معدل توظيف ممتاز',
                'description' => 'معدل التوظيف مرتفع بشكل ممتاز، استمر في هذا الأداء'
            ];
        } elseif ($stats['employmentRate'] < 30) {
            $insights[] = [
                'icon' => 'exclamation-triangle',
                'color' => 'danger',
                'title' => 'انخفاض في معدل التوظيف',
                'description' => 'معدل التوظيف منخفض، يحتاج إلى تحسين استراتيجيات التوظيف'
            ];
        }

        if ($stats['successRate'] < 30) {
            $insights[] = [
                'icon' => 'exclamation-triangle',
                'color' => 'warning',
                'title' => 'تحسين عملية الترشيح',
                'description' => 'نسبة نجاح الترشيحات منخفضة، راجع معايير الترشيح'
            ];
        } elseif ($stats['successRate'] > 60) {
            $insights[] = [
                'icon' => 'check-circle',
                'color' => 'success',
                'title' => 'كفاءة عالية في الترشيح',
                'description' => 'نسبة نجاح الترشيحات ممتازة، استمر في النهج الحالي'
            ];
        }

        if ($stats['seekingRate'] > 50) {
            $insights[] = [
                'icon' => 'people',
                'color' => 'info',
                'title' => 'فرص تحسين',
                'description' => 'هناك عدد كبير من الخريجين الباحثين عن عمل، ركز على توفير فرص مناسبة'
            ];
        }

        if ($stats['availableOpportunities'] < 10) {
            $insights[] = [
                'icon' => 'briefcase',
                'color' => 'warning',
                'title' => 'نقص في فرص العمل',
                'description' => 'عدد فرص العمل المتاحة قليل، حاول التواصل مع المزيد من الشركات'
            ];
        }

        // إذا لم تكن هناك استنتاجات، أضف رسالة تشجيعية
        if (empty($insights)) {
            $insights[] = [
                'icon' => 'info-circle',
                'color' => 'info',
                'title' => 'أداء متوازن',
                'description' => 'جميع المؤشرات ضمن المعدلات المتوقعة، استمر في المتابعة'
            ];
        }

        return $insights;
    }

    /**
     * حساب معدل النجاح
     */
    private function calculateSuccessRate()
    {
        $totalNominations = Nomination::count();
        $successfulNominations = Nomination::where('final_status', 'hired')->count();

        return $totalNominations > 0 ? round(($successfulNominations / $totalNominations) * 100, 1) : 0;
    }

    /**
     * حساب معدل النجاح الكلي
     */
    private function calculateOverallSuccessRate()
    {
        $employmentRate = GraduateData::where('employment_status', 'employed')->count() / max(GraduateData::count(), 1) * 100;
        $nominationSuccessRate = $this->calculateSuccessRate();

        return round(($employmentRate + $nominationSuccessRate) / 2, 1);
    }

    /**
     * توزيع الخريجين حسب التخصص
     */
    private function getMajorsDistribution($filters = [])
    {
        $query = GraduateData::select('major')
            ->selectRaw('COUNT(*) as count');

        if (!empty($filters['year'])) {
            $query->where('graduation_year', $filters['year']);
        }
        if (!empty($filters['status'])) {
            $query->where('employment_status', $filters['status']);
        }

        $majors = $query->groupBy('major')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get();

        $labels = $majors->pluck('major')->toArray();
        $data = $majors->pluck('count')->toArray();
        $backgroundColors = [
            '#4e73df',
            '#1cc88a',
            '#36b9cc',
            '#f6c23e',
            '#e74a3b',
            '#858796',
            '#5a5c69',
            '#6f42c1'
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'عدد الخريجين',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($data)),
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * حالة التوظيف
     */
    private function getEmploymentStatus($filters = [])
    {
        $query = GraduateData::select('employment_status')
            ->selectRaw('COUNT(*) as count');

        if (!empty($filters['major'])) {
            $query->where('major', $filters['major']);
        }
        if (!empty($filters['year'])) {
            $query->where('graduation_year', $filters['year']);
        }

        $statuses = $query->groupBy('employment_status')->get();

        $labels = [];
        $data = [];
        $statusLabels = [
            'employed' => 'موظف',
            'seeking_opportunities' => 'باحث عن عمل',
            'unemployed' => 'عاطل عن العمل',
            'further_study' => 'يكمل دراسته'
        ];
        $colors = [
            'employed' => '#1cc88a',
            'seeking_opportunities' => '#f6c23e',
            'unemployed' => '#e74a3b',
            'further_study' => '#36b9cc'
        ];
        $bgColors = [];

        foreach ($statuses as $status) {
            $labels[] = $statusLabels[$status->employment_status] ?? $status->employment_status;
            $data[] = $status->count;
            $bgColors[] = $colors[$status->employment_status] ?? '#858796';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $bgColors,
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }

    /**
     * حالة الترشيحات
     */
    private function getNominationsStatus()
    {
        $statuses = Nomination::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $statusLabels = [
            'pending' => 'قيد المراجعة',
            'sent_to_company' => 'مرسل للشركة',
            'interview_scheduled' => 'مقابلة مجدولة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'منسحب'
        ];

        foreach ($statuses as $status) {
            $labels[] = $statusLabels[$status->status] ?? $status->status;
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#f6c23e', '#36b9cc', '#858796', '#1cc88a', '#e74a3b', '#5a5c69'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }


    /**
     * الأداء الشهري للترشيحات
     */
    private function getMonthlyPerformance()
    {
        $monthlyData = Nomination::selectRaw('
            YEAR(created_at) as year, 
            MONTH(created_at) as month, 
            COUNT(*) as total,
            SUM(CASE WHEN final_status = "hired" THEN 1 ELSE 0 END) as successful
        ')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $labels = [];
        $totalData = [];
        $successData = [];

        foreach ($monthlyData as $data) {
            $labels[] = $this->getArabicMonthName($data->month) . ' ' . $data->year;
            $totalData[] = $data->total;
            $successData[] = $data->successful;
        }

        // عكس البيانات لتكون من الأقدم إلى الأحدث
        $labels = array_reverse($labels);
        $totalData = array_reverse($totalData);
        $successData = array_reverse($successData);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'إجمالي الترشيحات',
                    'data' => $totalData,
                    'borderColor' => '#4e73df',
                    'backgroundColor' => 'rgba(78, 115, 223, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'الترشيحات الناجحة',
                    'data' => $successData,
                    'borderColor' => '#1cc88a',
                    'backgroundColor' => 'rgba(28, 200, 138, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    /**
     * معدل نجاح الترشيحات حسب التخصص
     */
    private function getSuccessByMajor()
    {
        $successByMajor = Nomination::join('graduates_data', 'nominations.graduate_id', '=', 'graduates_data.id')
            ->selectRaw('
                graduates_data.major,
                COUNT(*) as total_nominations,
                SUM(CASE WHEN nominations.final_status = "hired" THEN 1 ELSE 0 END) as successful_nominations
            ')
            ->groupBy('graduates_data.major')
            ->having('total_nominations', '>=', 2) // فقط التخصصات التي لديها ترشيحين على الأقل
            ->orderBy('successful_nominations', 'desc')
            ->limit(6)
            ->get();

        $labels = $successByMajor->pluck('major')->toArray();
        $successRates = [];

        foreach ($successByMajor as $item) {
            $successRate = $item->total_nominations > 0 ?
                round(($item->successful_nominations / $item->total_nominations) * 100, 1) : 0;
            $successRates[] = $successRate;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'معدل النجاح %',
                    'data' => $successRates,
                    'backgroundColor' => 'rgba(78, 115, 223, 0.2)',
                    'borderColor' => '#4e73df',
                    'pointBackgroundColor' => '#4e73df',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => '#4e73df'
                ]
            ]
        ];
    }

    /**
     * توزيع فرص العمل
     */
    private function getOpportunitiesDistribution()
    {
        $opportunitiesByType = JobOpportunity::select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get();

        $labels = [];
        $data = [];

        $typeLabels = [
            'job' => 'وظائف',
            'training' => 'تدريبات',
            'internship' => 'تدريب عملي'
        ];

        $backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc'];

        foreach ($opportunitiesByType as $opportunity) {
            $labels[] = $typeLabels[$opportunity->type] ?? $opportunity->type;
            $data[] = $opportunity->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    private function getArabicMonthName($month)
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        return $months[$month] ?? 'غير معروف';
    }

    /**
     * مخطط سرعة الاستجابة للترشيحات
     */
    private function getResponseTimeAnalysis()
    {
        $responseTimes = Nomination::whereNotNull('company_response_at')
            ->selectRaw('TIMESTAMPDIFF(DAY, sent_to_company_at, company_response_at) as response_days')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('response_days')
            ->get();

        $labels = [];
        $data = [];

        foreach ($responseTimes as $item) {
            $labels[] = $item->response_days . ' أيام';
            $data[] = $item->count;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'سرعة استجابة الشركات',
                    'data' => $data,
                    'backgroundColor' => '#4e73df',
                ]
            ]
        ];
    }

    /**
     * تجميد/تنشيط حساب الخريج
     */
    public function toggleGraduateStatus($id)
    {
        $authUser = auth()->user();
        if (!$authUser->isAdmin() && !$authUser->hasPermission('graduates.edit') && $authUser->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بتعديل حالة حسابات الخريجين.');
        }

        // البحث إما عن طريق User id، أو GraduateData id
        $user = User::where('id', $id)->where('role', 'graduate')->first();
        if (!$user) {
            $gradData = GraduateData::find($id);
            if ($gradData) {
                $user = User::where('email', $gradData->email)->first() ?? $gradData->user;
            }
        }

        if (!$user) {
            return back()->with('error', 'لم يتم العثور على حساب مستخدم لهذا الخريج');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        if ($user->graduateData) {
            $user->graduateData->is_active = $user->is_active;
            $user->graduateData->save();
        }

        $statusWord = $user->is_active ? 'تنشيط' : 'تجميد';

        \App\Models\AuditLog::logAction('toggle_graduate_status', "تم {$statusWord} حساب الخريج: {$user->name}", 'User', $user->id);

        return back()->with('success', "تم {$statusWord} حساب الخريج [{$user->name}] بنجاح.");
    }

    /**
     * مسح وحذف سجلات وحساب الخريج نهائياً
     */
    public function destroyGraduate($id)
    {
        $authUser = auth()->user();
        if (!$authUser->isAdmin() && !$authUser->hasPermission('graduates.delete') && $authUser->role !== 'career_guidance_officer') {
            abort(403, 'غير مصرح لك بحذف حسابات وسجلات الخريجين.');
        }

        // البحث إما عن طريق User id، أو GraduateData id
        $user = User::where('id', $id)->where('role', 'graduate')->first();
        $gradData = null;
        if (!$user) {
            $gradData = GraduateData::find($id);
            if ($gradData) {
                $user = User::where('email', $gradData->email)->first() ?? $gradData->user;
            }
        }

        if (!$user && !$gradData) {
            return back()->with('error', 'لم يتم العثور على سجل الخريج المطلوب حذفه.');
        }

        $graduateName = $user ? $user->name : ($gradData ? $gradData->name : 'الخريج');
        $userId = $user ? $user->id : null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $gradData, $userId) {
            // 1. حذف الترشيحات المرتبطة وبيانات الخريج
            if ($user && $user->graduateData) {
                $user->graduateData->nominations()->delete();
                $user->graduateData->delete();
            }
            if ($gradData) {
                $gradData->nominations()->delete();
                $gradData->delete();
            }
            if ($user && $user->email) {
                GraduateData::where('email', $user->email)->delete();
            }

            // 2. حذف طلبات التقديم على التدريب
            if ($userId) {
                TrainingApplication::where('user_id', $userId)->delete();
            }

            // 3. حذف استجابات الاستبيانات إن وجدت
            if ($userId && class_exists(\App\Models\SurveyResponse::class)) {
                \App\Models\SurveyResponse::where('user_id', $userId)->delete();
            }

            // 4. حذف حساب المستخدم والصلاحيات
            if ($user) {
                $user->permissions()->detach();
                $user->delete();
            }
        });

        \App\Models\AuditLog::logAction('delete_graduate', "تم مسح حساب وسجلات الخريج: {$graduateName} نهائياً من المنظومة", 'User', $userId);

        return back()->with('success', "تم مسح الخريج [{$graduateName}] وكافة سجلاته المرتبطة بنجاح.");
    }

    /**
     * تحليل المهارات المطلوبة مقابل المتاحة
     */
    private function getSkillsAnalysis()
    {
        // المهارات المطلوبة في فرص العمل
        $requiredSkills = JobOpportunity::whereNotNull('required_skills')
            ->get()
            ->flatMap(function ($job) {
                return $job->required_skills ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        // المهارات المتاحة لدى الخريجين
        $availableSkills = GraduateData::whereNotNull('skills')
            ->get()
            ->flatMap(function ($grad) {
                return $grad->skills ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        return [
            'required' => $requiredSkills,
            'available' => $availableSkills
        ];
    }
}
