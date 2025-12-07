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
        $stats = [
            'totalGraduates' => GraduateData::count(),
            'employedGraduates' => GraduateData::where('employment_status', 'employed')->count(),
            'seekingOpportunities' => GraduateData::where('employment_status', 'seeking_opportunities')->count(),
            'totalNominations' => Nomination::count(),
            'pendingNominations' => Nomination::where('status', 'pending')->count(),
            'acceptedNominations' => Nomination::where('final_status', 'hired')->count(),
            'totalOpportunities' => JobOpportunity::where('status', 'open')->count(),
        ];

        $recentGraduates = GraduateData::latest()->take(5)->get();
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
        $this->authorize('create', GraduateData::class);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:graduates_data,email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'national_id' => 'nullable|string|max:50|unique:graduates_data,national_id|unique:users,national_id',
                'university' => 'required|string|max:255',
                'sector' => 'required|string|max:100',
                'faculty' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'graduation_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'gpa' => 'nullable|numeric|min:0|max:4',
                'employment_status' => 'required|in:employed,seeking_opportunities,unemployed,further_study',
                'current_job_title' => 'nullable|string|max:255',
                'current_company' => 'nullable|string|max:255',
                'skills' => 'nullable|string',
                'experiences' => 'nullable|string',
                'education' => 'nullable|string',
                'address' => 'nullable|string|max:500',
                'notes' => 'nullable|string',
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

            // إنشاء حساب مستخدم للخريج
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($temporaryPassword),
                'role' => 'graduate',
                'phone' => $validated['phone'] ?? null,
                'national_id' => $validated['national_id'] ?? null,
                'major' => $validated['major'],
                'graduation_year' => $validated['graduation_year'],
                'gpa' => $validated['gpa'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => true,
                'must_change_password' => true, // إجبار تغيير كلمة المرور
                'is_approved' => true, // الموافقة التلقائية لأن مسؤول الإرشاد هو من أضافه
            ]);

            // إنشاء سجل في جدول الخريجين
            $validated['added_by'] = Auth::id();
            $validated['user_id'] = $user->id; // ربط الخريج بالمستخدم
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
        $query = GraduateData::query();

        // تطبيق الفلاتر
        if ($request->has('major') && $request->major) {
            $query->where('major', 'like', '%' . $request->major . '%');
        }

        if ($request->has('graduation_year') && $request->graduation_year) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->has('employment_status') && $request->employment_status) {
            $query->where('employment_status', $request->employment_status);
        }

        $graduates = $query->withCount([
            'nominations',
            'nominations as accepted_nominations_count' => function ($q) {
                $q->where('final_status', 'hired');
            }
        ])->latest()->get();

        $majors = GraduateData::distinct()->pluck('major');
        $graduationYears = GraduateData::distinct()->pluck('graduation_year');

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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:graduates_data,email,' . $graduate->id,
            'phone' => 'nullable|string|max:255',
            'university' => 'required|string|max:255',
            'sector' => 'required|string|max:100',
            'faculty' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:2000|max:' . date('Y'),
            'gpa' => 'nullable|numeric|min:0|max:4',
            'degree' => 'required|string|max:255',
            'skills' => 'nullable|string', // Will be converted to array
            'languages' => 'nullable|string', // Will be converted to array
            'employment_status' => 'required|in:employed,seeking_opportunities,unemployed,further_study',
            'work_experience' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'cv' => 'nullable|file|mimes:pdf|max:5120', // 5MB max
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'university' => $request->university,
            'sector' => $request->sector,
            'faculty' => $request->faculty,
            'major' => $request->major,
            'graduation_year' => $request->graduation_year,
            'gpa' => $request->gpa,
            'degree' => $request->degree,
            'skills' => $request->skills ? array_map('trim', explode(',', $request->skills)) : null,
            'languages' => $request->languages ? array_map('trim', explode(',', $request->languages)) : null,
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

        $graduate->update($data);

        // تحديث بيانات المستخدم المقابل إذا وجد (Reverse Sync)
        if ($graduate->email) {
            $user = \App\Models\User::where('email', $graduate->email)->first();
            if ($user) {
                $userData = [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'major' => $data['major'],
                    'specialization' => $data['major'],
                    'graduation_year' => $data['graduation_year'],
                    'gpa' => $data['gpa'],
                    'degree' => $data['degree'],
                    'qualification' => $data['degree'],
                    'skills' => $data['skills'],
                    'languages' => $data['languages'],
                    'address' => $data['address'],
                ];

                if (isset($data['cv_path'])) {
                    $userData['cv_path'] = $data['cv_path'];
                }

                $user->update($userData);
            }
        }

        return redirect()->route('career-guidance.graduates.show', $graduate->id)
            ->with('success', 'تم تحديث بيانات الخريج بنجاح.');
    }

    /**
     * عرض تفاصيل الخريج
     */
    public function showGraduate($id)
    {
        $graduate = GraduateData::with(['nominations.jobOpportunity.company', 'nominations.nominator'])->findOrFail($id);
        $this->authorize('view', $graduate); // Authorize viewing the graduate details

        return view('career-guidance.graduates.show', compact('graduate'));
    }

    /**
     * عرض قائمة الترشيحات
     */
    public function nominations(Request $request)
    {
        $query = Nomination::query();

        // تطبيق الفلاتر
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
            $nomination->load(['user', 'jobOpportunity']);
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
        $nomination = Nomination::with(['graduate', 'jobOpportunity.company'])->findOrFail($id);
        $this->authorize('updateStatus', $nomination); // Use the same policy for viewing the edit form

        return view('career-guidance.nominations.edit-status', compact('nomination'));
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
            $nomination->load(['user', 'jobOpportunity']);
            $this->notificationService->notifyJobNomination($nomination, Auth::user());
        } catch (\Exception $e) {
            \Log::error('Failed to send job nomination notification: ' . $e->getMessage());
        }

        return redirect()->route('career-guidance.nominations')
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
        return view('career-guidance.graduates.import');
    }


    /**
     * استيراد الخريجين من ملف Excel/CSV
     */
    public function importGraduates(Request $request)
    {
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
        try {
            // جلب البيانات للفلاتر
            $majors = GraduateData::distinct()->pluck('major');
            $years = GraduateData::distinct()->orderBy('graduation_year', 'desc')->pluck('graduation_year');

            // الإحصائيات الأساسية مع تطبيق الفلاتر
            $filters = $request->only(['major', 'year', 'status']);
            $stats = $this->getAdvancedStats($filters);

            // بيانات المخططات مع التحقق من الصحة
            $chartData = $this->getChartData($filters);

            // إذا لم تكن هناك بيانات كافية، استخدم بيانات نموذجية
            if ($stats['totalGraduates'] == 0 || !$this->validateChartData($chartData)) {
                $chartData = $this->getSampleChartData();
                \Log::info('Using sample chart data - no real data available');
            }

            // استنتاجات ذكية
            $insights = $this->getAIInsights($stats);

            \Log::info('Advanced Reports Loaded', [
                'graduates' => $stats['totalGraduates'],
                'charts' => count($chartData),
                'insights' => count($insights)
            ]);

            return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights', 'majors', 'years'));

        } catch (\Exception $e) {
            \Log::error('Advanced Reports Error: ' . $e->getMessage());

            // في حالة الخطأ، عرض بيانات نموذجية
            $stats = $this->getAdvancedStats();
            $chartData = $this->getSampleChartData();
            $insights = $this->getAIInsights($stats);
            $majors = [];
            $years = [];

            return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights', 'majors', 'years'))
                ->with('error', 'تم تحميل التقارير ببيانات نموذجية بسبب وجود خطأ في البيانات الفعلية');
        }
    }

    /**
     * تصدير التقرير كـ PDF
     */
    public function exportReportsPDF(Request $request)
    {
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
            $pdf = \PDF::loadView('career-guidance.reports-pdf', compact('stats', 'insights', 'type'))
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
            return $this->getSampleChartData();
        }
    }

    /**
     * بيانات نموذجية في حالة وجود خطأ
     */
    private function getSampleChartData()
    {
        return [
            'majorsDistribution' => [
                'labels' => ['هندسة حاسوب', 'إدارة أعمال', 'طب', 'هندسة مدنية', 'صيدلة'],
                'datasets' => [
                    [
                        'label' => 'عدد الخريجين',
                        'data' => [25, 18, 12, 8, 6],
                        'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        'borderColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        'borderWidth' => 2
                    ]
                ]
            ],
            'employmentStatus' => [
                'labels' => ['موظف', 'باحث عن عمل', 'غير موظف', 'مستكمل للدراسة'],
                'datasets' => [
                    [
                        'data' => [45, 30, 15, 10],
                        'backgroundColor' => ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc'],
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ],
            'nominationsStatus' => [
                'labels' => ['قيد المراجعة', 'مرسل للشركة', 'مقابلة مجدولة', 'مقبول', 'مرفوض'],
                'datasets' => [
                    [
                        'data' => [20, 15, 10, 8, 5],
                        'backgroundColor' => ['#f6c23e', '#36b9cc', '#858796', '#1cc88a', '#e74a3b'],
                        'borderWidth' => 2,
                        'borderColor' => '#fff'
                    ]
                ]
            ],
            'monthlyPerformance' => [
                'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                'datasets' => [
                    [
                        'label' => 'إجمالي الترشيحات',
                        'data' => [12, 19, 15, 22, 18, 25],
                        'borderColor' => '#4e73df',
                        'backgroundColor' => 'rgba(78, 115, 223, 0.1)',
                        'fill' => true
                    ],
                    [
                        'label' => 'الترشيحات الناجحة',
                        'data' => [5, 8, 6, 12, 9, 15],
                        'borderColor' => '#1cc88a',
                        'backgroundColor' => 'rgba(28, 200, 138, 0.1)',
                        'fill' => true
                    ]
                ]
            ],
            'successByMajor' => [
                'labels' => ['هندسة حاسوب', 'إدارة أعمال', 'طب', 'هندسة مدنية'],
                'datasets' => [
                    [
                        'label' => 'معدل النجاح %',
                        'data' => [75, 60, 80, 55],
                        'backgroundColor' => 'rgba(78, 115, 223, 0.2)',
                        'borderColor' => '#4e73df',
                        'pointBackgroundColor' => '#4e73df',
                        'pointBorderColor' => '#fff'
                    ]
                ]
            ],
            'opportunitiesDistribution' => [
                'labels' => ['وظائف', 'تدريبات', 'تدريب عملي'],
                'datasets' => [
                    [
                        'data' => [35, 25, 15],
                        'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc'],
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
        $graduate = GraduateData::findOrFail($id);

        // البحث عن حساب المستخدم المرتبط
        $user = \App\Models\User::where('email', $graduate->email)->first();

        if (!$user) {
            return back()->with('error', 'لم يتم العثور على حساب مستخدم لهذا الخريج');
        }

        // التأكد من أن المستخدم خريج فقط
        if ($user->role !== 'graduate') {
            return back()->with('error', 'لا يمكنك تعديل حالة هذا المستخدم');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'تنشيط' : 'تجميد';
        return back()->with('success', "تم {$status} حساب الخريج بنجاح");
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
