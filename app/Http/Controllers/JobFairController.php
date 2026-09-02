<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JobFairController extends Controller
{
    // ==================== الصفحة العامة للمعرض ====================

    /**
     * صفحة المعرض العامة (للزوار والخريجين)
     */
    public function publicShow()
    {
        // نجلب المعرض المنشور الأحدث أو القادم
        $fair = JobFair::where('status', 'published')
                       ->orderBy('event_date', 'asc')
                       ->first();

        if (!$fair) {
            $fair = JobFair::where('status', 'ongoing')->first();
        }

        $myRegistration = null;
        $favoriteCompanyIds = [];
        if (Auth::check() && $fair) {
            $myRegistration = JobFairRegistration::where('job_fair_id', $fair->id)
                ->where('user_id', Auth::id())
                ->first();
                
            if (Auth::user()->role === 'graduate') {
                $favoriteCompanyIds = \App\Models\Favorite::where('graduate_id', Auth::id())
                    ->pluck('company_id')
                    ->toArray();
            }
        }

        $companies = $fair ? $fair->companies()->with('company')->where('status', 'confirmed')->get() : collect();
        $events = $fair ? $fair->events()->orderBy('start_time', 'asc')->get() : collect();
        $recentJobs = \App\Models\JobOpportunity::where('status', 'open')->with('company')->latest()->take(6)->get();
        $stats = $this->getFairStats($fair);

        return view('job-fair.public', compact('fair', 'myRegistration', 'companies', 'events', 'recentJobs', 'stats', 'favoriteCompanyIds'));
    }

    /**
     * تسجيل خريج في المعرض
     */
    public function register(Request $request, JobFair $fair)
    {
        if (!$fair->can_register) {
            return back()->with('error', 'التسجيل غير متاح حالياً.');
        }

        if (Auth::user()->role !== 'graduate') {
            return back()->with('error', 'التسجيل مخصص للخريجين فقط.');
        }

        // تحقق من عدم التسجيل مسبقاً
        $exists = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return back()->with('warning', 'أنت مسجل بالفعل في هذا المعرض.');
        }

        $registration = JobFairRegistration::create([
            'job_fair_id'         => $fair->id,
            'user_id'             => Auth::id(),
            'qr_code'             => JobFairRegistration::generateQrCode($fair->id, Auth::id()),
            'registration_number' => JobFairRegistration::generateRegistrationNumber($fair->id),
            'interests'           => $request->interests,
            'status'              => 'registered',
        ]);

        return redirect()->route('job-fair.my-ticket', $registration->id)
                         ->with('success', 'تم التسجيل بنجاح! يمكنك الآن تنزيل بطاقتك.');
    }

    /**
     * بطاقة الخريج مع QR
     */
    public function myTicket(JobFairRegistration $registration)
    {
        if ($registration->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        $registration->load(['jobFair', 'graduate']);
        return view('job-fair.ticket', compact('registration'));
    }

    /**
     * مسح QR وتسجيل الحضور (للأدمن في يوم المعرض)
     */
    public function checkIn(Request $request)
    {
        $graduateId = $request->graduate_id;
        $jobFairId = $request->job_fair_id;

        $registration = JobFairRegistration::where('user_id', $graduateId)
            ->where('job_fair_id', $jobFairId)
            ->with(['graduate', 'jobFair'])
            ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'رمز QR غير صالح.'], 404);
        }

        if ($registration->attended) {
            return response()->json([
                'success'      => true,
                'already_in'   => true,
                'graduate'     => $registration->graduate->name,
                'check_in_at'  => $registration->check_in_at->format('H:i'),
                'message'      => 'تم تسجيل الحضور مسبقاً',
            ]);
        }

        $registration->update([
            'attended'    => true,
            'check_in_at' => Carbon::now(),
            'status'      => 'attended',
        ]);

        return response()->json([
            'success'    => true,
            'already_in' => false,
            'graduate'   => $registration->graduate->name,
            'major'      => $registration->graduate->major,
            'faculty'    => $registration->graduate->faculty,
            'message'    => 'تم تسجيل الحضور بنجاح ✓',
        ]);
    }

    // ==================== لوحة تحكم الأدمن ====================

    /**
     * قائمة المعارض (أدمن)
     */
    public function index()
    {
        $fairs = JobFair::withCount(['registrations', 'companies'])
                        ->orderBy('event_date', 'desc')
                        ->get();
        return view('job-fair.admin.index', compact('fairs'));
    }

    /**
     * إنشاء معرض جديد
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $surveys = \App\Models\Survey::where('type', 'job_fair')->orWhereNull('type')->orderBy('title')->get();
        return view('job-fair.admin.create', compact('companies', 'surveys'));
    }

    /**
     * حفظ معرض جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'location'   => 'required|string|max:255',
            'survey_id'  => 'nullable|exists:surveys,id',
        ]);

        $data = $request->except(['_token', 'companies']);
        $data['created_by'] = Auth::id();

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('job-fair/banners', 'public');
        }

        $fair = JobFair::create($data);

        // إضافة الشركات المختارة
        if ($request->companies) {
            foreach ($request->companies as $companyData) {
                if (!empty($companyData['company_id'])) {
                    JobFairCompany::create([
                        'job_fair_id'  => $fair->id,
                        'company_id'   => $companyData['company_id'],
                        'booth_number' => $companyData['booth_number'] ?? null,
                        'status'       => 'confirmed',
                    ]);
                }
            }
        }

        \App\Models\AuditLog::logAction('create_job_fair', "تم إنشاء معرض التوظيف: {$fair->title}", 'JobFair', $fair->id);

        return redirect()->route('job-fair.admin.show', $fair->id)
                         ->with('success', 'تم إنشاء المعرض بنجاح!');
    }

    /**
     * تفاصيل المعرض (أدمن) مع الإحصائيات
     */
    public function show(JobFair $fair)
    {
        $fair->load(['companies.company', 'registrations.graduate']);
        $stats = $this->getFairStats($fair);

        $registrations = $fair->registrations()
                               ->with('graduate')
                               ->orderBy('created_at', 'desc')
                               ->paginate(20);

        return view('job-fair.admin.show', compact('fair', 'stats', 'registrations'));
    }

    /**
     * لوحة الإحصائيات اللحظية (Live Dashboard)
     */
    public function liveDashboard(JobFair $fair)
    {
        $stats = $this->getFairStats($fair);
        
        // جلب أحدث الحضور
        $recentCheckins = JobFairRegistration::where('job_fair_id', $fair->id)
            ->where('status', 'attended')
            ->with('graduate')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
            
        // جلب أكثر التخصصات حضورا
        $topMajors = \DB::table('job_fair_registrations')
            ->join('users', 'job_fair_registrations.user_id', '=', 'users.id')
            ->select('users.major', \DB::raw('count(*) as total'))
            ->where('job_fair_registrations.job_fair_id', $fair->id)
            ->where('job_fair_registrations.status', 'attended')
            ->whereNotNull('users.major')
            ->groupBy('users.major')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('job-fair.admin.live_dashboard', compact('fair', 'stats', 'recentCheckins', 'topMajors'));
    }

    public function resetAttendance(JobFair $fair)
    {
        JobFairRegistration::where('job_fair_id', $fair->id)
            ->update([
                'attended' => false,
                'status' => 'registered'
            ]);

        \App\Models\AuditLog::logAction('reset_attendance', "تمت إعادة تهيئة حضور المعرض: {$fair->title}", 'JobFair', $fair->id);

        return back()->with('success', 'تم إعادة تهيئة الحضور ومسح السجلات بنجاح لهذا المعرض.');
    }

    /**
     * طباعة البطاقة الذكية (Smart ID Card) للخريج
     * يدعم الأدمن لعرض أي بطاقة عبر ?reg=ID
     */
    public function printTicket(JobFair $fair)
    {
        $user = auth()->user();

        // الأدمن يمكنه عرض بطاقة أي خريج عبر ?reg=ID
        if (in_array($user->role, ['admin', 'partnership_officer']) && request()->has('reg')) {
            $registration = JobFairRegistration::where('job_fair_id', $fair->id)
                ->where('id', request('reg'))
                ->with('graduate')
                ->firstOrFail();
        } else {
            $registration = JobFairRegistration::where('job_fair_id', $fair->id)
                ->where('user_id', $user->id)
                ->with('graduate')
                ->firstOrFail();
        }

        return view('job-fair.ticket-print', compact('registration', 'fair'));
    }

    /**
     * تعديل معرض
     */
    public function edit(JobFair $fair)
    {
        $companies = Company::orderBy('name')->get();
        $surveys = \App\Models\Survey::where('type', 'job_fair')->orWhereNull('type')->orderBy('title')->get();
        $fair->load('companies');
        return view('job-fair.admin.edit', compact('fair', 'companies', 'surveys'));
    }

    /**
     * تحديث معرض
     */
    public function update(Request $request, JobFair $fair)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'location'   => 'required|string|max:255',
            'survey_id'  => 'nullable|exists:surveys,id',
        ]);

        $data = $request->except(['_token', '_method', 'companies']);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('job-fair/banners', 'public');
        }

        $fair->update($data);

        \App\Models\AuditLog::logAction('update_job_fair', "تم تحديث بيانات المعرض: {$fair->title}", 'JobFair', $fair->id);

        return redirect()->route('job-fair.admin.show', $fair->id)
                         ->with('success', 'تم تحديث المعرض بنجاح.');
    }

    /**
     * تغيير حالة المعرض
     */
    public function updateStatus(Request $request, JobFair $fair)
    {
        $fair->update(['status' => $request->status]);

        \App\Models\AuditLog::logAction('update_status', "تم تغيير حالة المعرض {$fair->title} إلى {$request->status}", 'JobFair', $fair->id);

        return back()->with('success', 'تم تحديث حالة المعرض.');
    }

    /**
     * إضافة شركة للمعرض
     */
    public function addCompany(Request $request, JobFair $fair)
    {
        $request->validate(['company_id' => 'required|exists:companies,id']);

        JobFairCompany::updateOrCreate(
            ['job_fair_id' => $fair->id, 'company_id' => $request->company_id],
            [
                'booth_number'   => $request->booth_number,
                'booth_location' => $request->booth_location,
                'available_positions' => $request->available_positions,
                'requirements'   => $request->requirements,
                'status'         => 'confirmed',
            ]
        );

        return back()->with('success', 'تمت إضافة الشركة بنجاح.');
    }

    /**
     * حذف شركة من المعرض
     */
    public function removeCompany(JobFair $fair, Company $company)
    {
        JobFairCompany::where('job_fair_id', $fair->id)
                      ->where('company_id', $company->id)
                      ->delete();

        return back()->with('success', 'تمت إزالة الشركة من المعرض.');
    }

    /**
     * تصدير قائمة الخريجين PDF أو Excel
     */
    public function exportRegistrations(JobFair $fair)
    {
        $registrations = $fair->registrations()->with('graduate')->get();
        return view('job-fair.admin.export', compact('fair', 'registrations'));
    }

    /**
     * صفحة تسجيل الحضور (QR Scanner)
     */
    public function attendancePage(JobFair $fair)
    {
        $stats = $this->getFairStats($fair);
        return view('job-fair.admin.attendance', compact('fair', 'stats'));
    }

    // ==================== مساعد ====================

    private function getFairStats(?JobFair $fair): array
    {
        if (!$fair) return [];

        return [
            'total_registered'  => $fair->registrations()->count(),
            'total_attended'    => $fair->registrations()->where('attended', true)->count(),
            'total_companies'   => $fair->companies()->where('status', 'confirmed')->count(),
            'days_remaining'    => $fair->days_remaining,
        ];
    }
}
