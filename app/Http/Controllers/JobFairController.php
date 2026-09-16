<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobFair;
use App\Models\JobFairCompany;
use App\Models\JobFairRegistration;
use App\Models\Company;
use App\Models\User;
use App\Models\JobFairSponsor;
use App\Models\JobFairEvent;
use App\Models\JobFairEventAttendee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobFairController extends Controller
{
    // ==================== الصفحة العامة للمعرض ====================

    /**
     * صفحة المعرض / الفعالية العامة (للزوار والخريجين)
     */
    public function publicShow(Request $request, $fair = null)
    {
        // 1. إذا تم تحديد الفعالية عبر الرابط أو المعامل ?fair=ID
        if ($fair) {
            if (!($fair instanceof JobFair)) {
                $fair = JobFair::find($fair);
            }
        } elseif ($request->has('fair')) {
            $fair = JobFair::find($request->query('fair'));
        }

        // 2. إذا لم تُحدد، نجلب الفعالية المنشورة الأحدث أو القادمة افتراضياً
        if (!$fair) {
            $fair = JobFair::where('status', 'published')
                           ->orderBy('event_date', 'asc')
                           ->first();

            if (!$fair) {
                $fair = JobFair::where('status', 'ongoing')->first();
            }
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
        $sponsors = $fair ? $fair->sponsors()->where('is_active', true)->orderBy('display_order')->get() : collect();
        $events = $fair ? $fair->events()->withCount('attendees')->orderBy('start_time', 'asc')->get() : collect();
        $recentJobs = \App\Models\JobOpportunity::where('status', 'open')->with('company')->latest()->take(6)->get();
        $stats = $this->getFairStats($fair);

        return view('job-fair.public', compact('fair', 'myRegistration', 'companies', 'sponsors', 'events', 'recentJobs', 'stats', 'favoriteCompanyIds'));
    }

    /**
     * دليل الشركات والمؤسسات المشاركة في المعرض (صفحة عامة مخصصة وفق هوية المعرض)
     */
    public function publicCompanies(Request $request, $fair = null)
    {
        // 1. إذا تم تحديد الفعالية عبر الرابط أو المعامل ?fair=ID
        if ($fair) {
            if (!($fair instanceof JobFair)) {
                $fair = JobFair::find($fair);
            }
        } elseif ($request->has('fair')) {
            $fair = JobFair::find($request->query('fair'));
        }

        // 2. إذا لم تُحدد، نجلب الفعالية المنشورة الأحدث أو الجارية افتراضياً
        if (!$fair) {
            $fair = JobFair::where('status', 'published')
                           ->orderBy('event_date', 'asc')
                           ->first();

            if (!$fair) {
                $fair = JobFair::where('status', 'ongoing')->first();
            }
        }

        $companies = $fair 
            ? $fair->companies()
                ->with(['company.jobOpportunities' => function($q) {
                    $q->where('status', 'open');
                }])
                ->where('status', 'confirmed')
                ->orderBy('booth_number', 'asc')
                ->get() 
            : collect();

        // تجميع القطاعات الفريدة لتوليد فلاتر التصنيف السريع
        $industries = $companies->map(function ($fc) {
            return $fc->company->industry ?? null;
        })->filter()->map(fn($i) => trim($i))->unique()->values();

        $totalCompanies = $companies->count();
        $totalPositions = $companies->sum(function ($fc) {
            $positions = (int) ($fc->available_positions ?? 0);
            if ($positions > 0) {
                return $positions;
            }
            return $fc->company && $fc->company->jobOpportunities ? $fc->company->jobOpportunities->count() : 0;
        });
        $totalBooths = $companies->filter(fn($fc) => !empty($fc->booth_number))->count();

        $favoriteCompanyIds = [];
        if (Auth::check() && Auth::user()->role === 'graduate') {
            $favoriteCompanyIds = \App\Models\Favorite::where('graduate_id', Auth::id())
                ->pluck('company_id')
                ->toArray();
        }

        return view('job-fair.companies', compact(
            'fair',
            'companies',
            'industries',
            'totalCompanies',
            'totalPositions',
            'totalBooths',
            'favoriteCompanyIds'
        ));
    }

    /**
     * الصفحة العامة للبرنامج العلمي وجدول الفعاليات (Masterclass، ورش عمل، جلسات حوارية)
     */
    public function publicProgram(Request $request, $fair = null)
    {
        if ($fair) {
            if (!($fair instanceof JobFair)) {
                $fair = JobFair::find($fair);
            }
        } elseif ($request->has('fair')) {
            $fair = JobFair::find($request->query('fair'));
        }

        if (!$fair) {
            $fair = JobFair::where('status', 'published')
                           ->orderBy('event_date', 'asc')
                           ->first();

            if (!$fair) {
                $fair = JobFair::where('status', 'ongoing')->first();
            }
        }

        $events = $fair 
            ? $fair->events()
                ->withCount('attendees')
                ->orderBy('start_time', 'asc')
                ->get() 
            : collect();

        $totalEvents = $events->count();
        $totalCapacity = $events->sum(fn($e) => $e->capacity ?: 0);
        $masterclassCount = $events->where('type', 'masterclass')->count();
        $workshopsCount = $events->where('type', 'workshop')->count();
        $panelsCount = $events->where('type', 'panel_discussion')->count();
        $speakersCount = $events->pluck('speaker_name')->filter()->unique()->count();

        $registeredEventIds = [];
        if (Auth::check() && Auth::user()->role === 'graduate') {
            $registeredEventIds = JobFairEventAttendee::where('graduate_id', Auth::id())
                ->pluck('job_fair_event_id')
                ->toArray();
        }

        return view('job-fair.program', compact(
            'fair',
            'events',
            'totalEvents',
            'totalCapacity',
            'masterclassCount',
            'workshopsCount',
            'panelsCount',
            'speakersCount',
            'registeredEventIds'
        ));
    }

    /**
     * الصفحة المستقلة للفعالية العلمية مع رمز QR وتفاصيل المحاور والمتحدث
     */
    public function publicEventShow(Request $request, $event)
    {
        if (!($event instanceof JobFairEvent)) {
            $event = JobFairEvent::with(['jobFair', 'attendees.graduate'])->findOrFail($event);
        } else {
            $event->load(['jobFair', 'attendees.graduate']);
        }

        $fair = $event->jobFair;

        $isRegistered = false;
        if (Auth::check() && Auth::user()->role === 'graduate') {
            $isRegistered = JobFairEventAttendee::where('job_fair_event_id', $event->id)
                ->where('graduate_id', Auth::id())
                ->exists();
        }

        $relatedEvents = $fair 
            ? $fair->events()
                ->where('id', '!=', $event->id)
                ->withCount('attendees')
                ->orderBy('start_time', 'asc')
                ->take(3)
                ->get() 
            : collect();

        return view('job-fair.event-show', compact('event', 'fair', 'isRegistered', 'relatedEvents'));
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

        if ($request->hasFile('fair_logo_path')) {
            $data['fair_logo_path'] = $request->file('fair_logo_path')->store('job-fair/logos', 'public');
        }

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
        $fair->load(['companies.company', 'registrations.graduate', 'sponsors']);
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

        if ($request->hasFile('fair_logo_path')) {
            $data['fair_logo_path'] = $request->file('fair_logo_path')->store('job-fair/logos', 'public');
        }

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

    // ==================== إدارة الجهات الراعية ====================

    /**
     * إضافة جهة راعية للمعرض
     */
    public function storeSponsor(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tier' => 'required|in:diamond,platinum,gold,silver',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('sponsors', 'public');
        }

        $fair->sponsors()->create([
            'name' => $validated['name'],
            'tier' => $validated['tier'],
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
            'logo_path' => $logoPath,
        ]);

        return back()->with('success', 'تمت إضافة جهة الرعاية بنجاح.');
    }

    /**
     * تحديث بيانات جهة راعية
     */
    public function updateSponsor(Request $request, JobFairSponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tier' => 'required|in:diamond,platinum,gold,silver',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'display_order' => 'nullable|integer|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'tier' => $validated['tier'],
            'website' => $validated['website'] ?? null,
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        if ($request->hasFile('logo')) {
            if ($sponsor->logo_path && \Storage::disk('public')->exists($sponsor->logo_path)) {
                \Storage::disk('public')->delete($sponsor->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('sponsors', 'public');
        }

        $sponsor->update($data);

        return back()->with('success', 'تم تحديث بيانات جهة الرعاية بنجاح.');
    }

    /**
     * حذف جهة راعية
     */
    public function destroySponsor(JobFairSponsor $sponsor)
    {
        if ($sponsor->logo_path && \Storage::disk('public')->exists($sponsor->logo_path)) {
            \Storage::disk('public')->delete($sponsor->logo_path);
        }
        $sponsor->delete();

        return back()->with('success', 'تم حذف جهة الرعاية بنجاح.');
    }

    /**
     * تحديث الهوية البصرية والأصول الإعلامية للمعرض
     */
    public function updateBrandIdentity(Request $request, JobFair $fair)
    {
        $request->validate([
            'fair_logo'              => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'fair_logo_white'        => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'fair_logo_horizontal'   => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'brand_guidelines'       => 'nullable|file|mimes:pdf|max:25600',
            'media_kit_zip'          => 'nullable|file|mimes:zip,rar,7z|max:61440',
            'media_kit_description'  => 'nullable|string|max:4000',
        ]);

        $data = [];

        if ($request->has('media_kit_description')) {
            $data['media_kit_description'] = $request->media_kit_description;
        }

        if ($request->hasFile('fair_logo')) {
            if ($fair->fair_logo_path && Storage::disk('public')->exists($fair->fair_logo_path)) {
                Storage::disk('public')->delete($fair->fair_logo_path);
            }
            $data['fair_logo_path'] = $request->file('fair_logo')->store('job-fairs/brand', 'public');
        }

        if ($request->hasFile('fair_logo_white')) {
            if ($fair->fair_logo_white_path && Storage::disk('public')->exists($fair->fair_logo_white_path)) {
                Storage::disk('public')->delete($fair->fair_logo_white_path);
            }
            $data['fair_logo_white_path'] = $request->file('fair_logo_white')->store('job-fairs/brand', 'public');
        }

        if ($request->hasFile('fair_logo_horizontal')) {
            if ($fair->fair_logo_horizontal_path && Storage::disk('public')->exists($fair->fair_logo_horizontal_path)) {
                Storage::disk('public')->delete($fair->fair_logo_horizontal_path);
            }
            $data['fair_logo_horizontal_path'] = $request->file('fair_logo_horizontal')->store('job-fairs/brand', 'public');
        }

        if ($request->hasFile('brand_guidelines')) {
            if ($fair->brand_guidelines_path && Storage::disk('public')->exists($fair->brand_guidelines_path)) {
                Storage::disk('public')->delete($fair->brand_guidelines_path);
            }
            $data['brand_guidelines_path'] = $request->file('brand_guidelines')->store('job-fairs/brand', 'public');
        }

        if ($request->hasFile('media_kit_zip')) {
            if ($fair->media_kit_path && Storage::disk('public')->exists($fair->media_kit_path)) {
                Storage::disk('public')->delete($fair->media_kit_path);
            }
            $data['media_kit_path'] = $request->file('media_kit_zip')->store('job-fairs/brand', 'public');
        }

        $fair->update($data);

        \App\Models\AuditLog::logAction('update_job_fair_brand', "تم تحديث الهوية البصرية والأصول الإعلامية لمعرض: {$fair->title}", 'JobFair', $fair->id);

        return back()->with('success', 'تم حفظ وتحديث الهوية البصرية والأصول الإعلامية للمعرض بنجاح.');
    }

    /**
     * حذف أصل محدد من الهوية البصرية
     */
    public function deleteBrandAsset(JobFair $fair, $asset)
    {
        $allowed = [
            'fair_logo'            => 'fair_logo_path',
            'fair_logo_white'      => 'fair_logo_white_path',
            'fair_logo_horizontal' => 'fair_logo_horizontal_path',
            'brand_guidelines'     => 'brand_guidelines_path',
            'media_kit_zip'        => 'media_kit_path',
        ];

        if (!isset($allowed[$asset])) {
            return back()->with('error', 'الأصل المطلوب حذفه غير صالح.');
        }

        $column = $allowed[$asset];
        if ($fair->$column && Storage::disk('public')->exists($fair->$column)) {
            Storage::disk('public')->delete($fair->$column);
        }

        $fair->update([$column => null]);

        return back()->with('success', 'تم حذف الملف بنجاح، وستستخدم الصفحة النسخة الافتراضية للنظام.');
    }

    /**
     * تحميل أصل محدد من الهوية البصرية (للأدمن)
     */
    public function downloadBrandAsset(JobFair $fair, $type)
    {
        switch ($type) {
            case 'fair-logo':
                if ($fair->fair_logo_path && Storage::disk('public')->exists($fair->fair_logo_path)) {
                    $ext = pathinfo($fair->fair_logo_path, PATHINFO_EXTENSION) ?: 'png';
                    return response()->download(Storage::disk('public')->path($fair->fair_logo_path), 'شعار_' . Str::slug($fair->title) . '.' . $ext);
                }
                $path = public_path('images/job_fair_logo.png');
                return response()->download($path, 'شعار_معرض_التوظيف_الافتراضي.png');

            case 'fair-logo-white':
                if ($fair->fair_logo_white_path && Storage::disk('public')->exists($fair->fair_logo_white_path)) {
                    $ext = pathinfo($fair->fair_logo_white_path, PATHINFO_EXTENSION) ?: 'png';
                    return response()->download(Storage::disk('public')->path($fair->fair_logo_white_path), 'شعار_' . Str::slug($fair->title) . '_أبيض_شفاف.' . $ext);
                }
                $path = public_path('images/job_fair_logo_white.png');
                return response()->download($path, 'شعار_معرض_التوظيف_أبيض_شفاف.png');

            case 'fair-logo-horizontal':
                if ($fair->fair_logo_horizontal_path && Storage::disk('public')->exists($fair->fair_logo_horizontal_path)) {
                    $ext = pathinfo($fair->fair_logo_horizontal_path, PATHINFO_EXTENSION) ?: 'png';
                    return response()->download(Storage::disk('public')->path($fair->fair_logo_horizontal_path), 'شعار_' . Str::slug($fair->title) . '_أفقي.' . $ext);
                }
                $path = public_path('images/job_fair_logo_horizontal.png');
                return response()->download($path, 'شعار_معرض_التوظيف_أفقي.png');

            case 'brand-guidelines':
                if ($fair->brand_guidelines_path && Storage::disk('public')->exists($fair->brand_guidelines_path)) {
                    return response()->download(Storage::disk('public')->path($fair->brand_guidelines_path), 'دليل_الهوية_البصرية_' . Str::slug($fair->title) . '.pdf');
                }
                return back()->with('error', 'دليل الهوية البصرية غير متوفر لهذا المعرض بعد.');

            case 'office-logo':
                $path = public_path('images/logo.jpg');
                return response()->download($path, 'شعار_مكتب_تدريب_الخريجين.jpg');

            case 'university-logo':
                $path = public_path('images/uni_logo_white.png');
                return response()->download($path, 'شعار_جامعة_طرابلس.png');

            case 'media-kit':
            default:
                if ($fair->media_kit_path && Storage::disk('public')->exists($fair->media_kit_path)) {
                    $ext = pathinfo($fair->media_kit_path, PATHINFO_EXTENSION) ?: 'zip';
                    return response()->download(Storage::disk('public')->path($fair->media_kit_path), 'الحقيبة_الإعلامية_' . Str::slug($fair->title) . '.' . $ext);
                }
                $path = public_path('assets/media-kit-2026.zip');
                if (file_exists($path)) {
                    return response()->download($path, 'الحقيبة_الإعلامية_معرض_التوظيف_2026.zip');
                }
                return back()->with('error', 'الحقيبة الإعلامية غير متوفرة بعد.');
        }
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
