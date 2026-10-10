<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairVisit;
use App\Models\JobFairRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyJobFairController extends Controller
{
    /**
     * عرض المعارض التي تشارك فيها الشركة
     */
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;
        
        if (!$company) {
            $fairs = collect();
        } else {
            $fairs = JobFair::whereHas('companies', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->orderBy('event_date', 'desc')
            ->get();
        }

        return view('company.job-fair.index', compact('fairs'));
    }

    /**
     * واجهة ماسح رمز الاستجابة السريعة (Scanner)
     */
    public function scanner(JobFair $fair)
    {
        $company = auth()->user()->company;
        
        // التأكد من أن الشركة مشاركة في هذا المعرض
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        // تشغيل الترحيل التلقائي لضمان وجود أحدث الأعمدة
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('job_opportunities', 'job_fair_id') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {}

        // جلب الشواغر الوظيفية المتاحة للشركة في المعرض بشكل آمن
        $opportunities = collect();
        try {
            $query = \App\Models\JobOpportunity::where('company_id', $company->id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('job_opportunities', 'status')) {
                $query->whereIn('status', ['open', 'approved', 'active']);
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('job_opportunities', 'job_fair_id')) {
                $query->where(function($q) use ($fair) {
                    $q->whereNull('job_fair_id')->orWhere('job_fair_id', $fair->id);
                });
            }

            $opportunities = $query->orderBy('title')->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Error fetching scanner job opportunities: ' . $e->getMessage());
        }

        return view('company.job-fair.scanner', compact('fair', 'opportunities'));
    }

    /**
     * طباعة الباركود الخاص بجناح الشركة
     */
    public function qrBooth(JobFair $fair)
    {
        $company = auth()->user()->company;
        
        // التأكد من أن الشركة مشاركة في هذا المعرض
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        return view('company.job-fair.qr-booth', compact('fair', 'company'));
    }

    /**
     * تسجيل زيارة خريج (استلام سيرة ذاتية مع ربط الوظيفة المستهدفة)
     */
    public function storeVisit(Request $request, JobFair $fair)
    {
        $graduateId = $request->graduate_id;

        $registration = JobFairRegistration::where('user_id', $graduateId)
            ->where('job_fair_id', $fair->id)
            ->with('graduate')
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'الرمز غير صالح أو لم يسجل الخريج في هذا المعرض.'
            ]);
        }

        $graduate = $registration->graduate;
        $company = auth()->user()->company;

        if (!$company) {
            return response()->json(['success' => false, 'message' => 'بيانات الشركة غير مكتملة. يرجى التواصل مع الإدارة.']);
        }

        // التحقق من صحة الوظيفة المستهدفة إن تم تحديدها
        $jobOpportunityId = $request->job_opportunity_id;
        if ($jobOpportunityId && $jobOpportunityId !== 'general') {
            $opp = \App\Models\JobOpportunity::where('id', $jobOpportunityId)
                ->where('company_id', $company->id)
                ->first();
            $jobOpportunityId = $opp ? $opp->id : null;
        } else {
            $jobOpportunityId = null;
        }

        // company_id in job_fair_visits references users.id, not companies.id
        $companyId = auth()->id();

        // Check if already visited
        $visit = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $companyId)
            ->where('graduate_id', $graduate->id)
            ->first();

        $hasJobOppCol = \Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id');

        if ($visit) {
            if ($hasJobOppCol && $jobOpportunityId && $visit->job_opportunity_id != $jobOpportunityId) {
                $visit->update(['job_opportunity_id' => $jobOpportunityId]);
            }
            if ($hasJobOppCol) {
                $visit->load('jobOpportunity');
            }
            return response()->json([
                'success'         => true,
                'already_visited' => true,
                'graduate_name'   => $graduate->name,
                'job_title'       => ($hasJobOppCol && $visit->jobOpportunity) ? $visit->jobOpportunity->title : 'تقديم عام',
                'message'         => 'تم استلام بيانات هذا الخريج مسبقاً.' . ($jobOpportunityId ? ' (تم تحديث الوظيفة المستهدفة)' : ''),
            ]);
        }

        $visitData = [
            'job_fair_id' => $fair->id,
            'company_id'  => $companyId,
            'graduate_id' => $graduate->id,
            'notes'       => $request->notes,
        ];
        if ($hasJobOppCol) {
            $visitData['job_opportunity_id'] = $jobOpportunityId;
        }

        $visit = JobFairVisit::create($visitData);

        if ($hasJobOppCol) {
            $visit->load('jobOpportunity');
        }
            
        // جلب التدريبات التي حضرها الخريج
        $attendedTrainings = \App\Models\TrainingApplication::where('user_id', $graduate->id)
            ->whereNotNull('attended_at')
            ->with('training')
            ->get()
            ->map(function($app) {
                return [
                    'title' => $app->training->title ?? 'تدريب',
                    'date' => $app->attended_at->format('Y-m-d')
                ];
            });

        return response()->json([
            'success'            => true,
            'already_visited'    => false,
            'visit_id'           => $visit->id,
            'graduate_name'      => $graduate->name,
            'job_title'          => $visit->jobOpportunity?->title ?? 'تقديم عام',
            'job_opportunity_id' => $visit->job_opportunity_id,
            'major'              => $graduate->specialization ?? $graduate->major ?? $graduate->qualification ?? null,
            'university'         => $graduate->university,
            'graduation_year'    => $graduate->graduation_year,
            'gpa'                => $graduate->gpa ? number_format($graduate->gpa, 2) : null,
            'phone'              => $graduate->phone,
            'skills'             => $graduate->skills ?? [],
            'trainings'          => $attendedTrainings,
            'message'            => 'تم استلام بيانات الخريج بنجاح.',
        ]);
    }

    /**
     * تحديث نتيجة المقابلة لزيارة معرض
     */
    public function updateVisitOutcome(Request $request, JobFairVisit $visit)
    {
        $company = auth()->user()->company;
        if (!$company || $visit->company_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $outcome = $request->outcome;
        // Map old 'interviewed' to 'shortlisted' to prevent DB enum errors from old form resubmissions
        if ($outcome === 'interviewed') {
            $outcome = 'shortlisted';
        }

        // Validate to ensure it's in the ENUM list
        if (!in_array($outcome, ['pending', 'shortlisted', 'accepted', 'rejected'])) {
            $outcome = 'pending';
        }

        $visit->update(['status' => $outcome]);

        $labels = [
            'shortlisted' => 'مدرج في القائمة القصيرة ⭐',
            'accepted'    => 'مقبول ✅',
            'rejected'    => 'غير مناسب ❌',
            'pending'     => 'قيد الدراسة 🕐',
        ];

        // Since the user might be submitting via a standard form POST rather than AJAX, 
        // we should redirect back instead of returning JSON if it's not an AJAX request.
        if (!$request->ajax() && !$request->wantsJson()) {
            return back()->with('success', 'تم تحديث حالة الخريج بنجاح.');
        }

        return response()->json([
            'success' => true,
            'label'   => $labels[$outcome] ?? $outcome,
        ]);
    }


    /**
     * عرض السير الذاتية المستلمة (Leads) لمعرض محدد مع دعم الفلترة حسب الوظيفة
     */
    public function leads(JobFair $fair)
    {
        $company = auth()->user()->company;
        
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        // تشغيل الترحيل التلقائي إذا لم تكن الجداول محدثة
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('job_opportunities', 'job_fair_id') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {}

        $hasJobOppColumn = \Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id');

        $withRelations = [
            'graduate', 
            'graduate.graduateData',
            'graduate.trainingApplications' => function ($q) {
                $q->with(['training', 'attendances']);
            }
        ];

        if ($hasJobOppColumn) {
            $withRelations[] = 'jobOpportunity';
        }

        $query = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', auth()->id())
            ->with($withRelations);

        // تصفية حسب الوظيفة المتقدم عليها
        if ($hasJobOppColumn && request()->filled('job_opportunity_id')) {
            if (request('job_opportunity_id') === 'general') {
                $query->whereNull('job_opportunity_id');
            } else {
                $query->where('job_opportunity_id', request('job_opportunity_id'));
            }
        }

        // تصفية حسب حالة التقييم
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // بحث بالاسم، البريد، التخصص، أو الهاتف
        if (request()->filled('keyword')) {
            $keyword = request('keyword');
            $query->where(function($q) use ($keyword) {
                $q->whereHas('graduate', function($gq) use ($keyword) {
                    $gq->where('name', 'like', "%{$keyword}%")
                       ->orWhere('email', 'like', "%{$keyword}%")
                       ->orWhere('phone', 'like', "%{$keyword}%");
                })->orWhereHas('graduate.graduateData', function($gq) use ($keyword) {
                    $gq->where('major', 'like', "%{$keyword}%")
                       ->orWhere('university', 'like', "%{$keyword}%");
                });
            });
        }

        $visits = $query->latest()->paginate(15)->withQueryString();

        // قائمة الوظائف الخاصة بالشركة للمعرض
        $opportunities = collect();
        try {
            $opportunities = \App\Models\JobOpportunity::where('company_id', $company->id)
                ->orderBy('title')
                ->get();
        } catch (\Throwable $e) {}

        // إحصائيات التوزيع بحسب الوظائف
        $generalCount = 0;
        $jobCounts = collect();
        if ($hasJobOppColumn) {
            try {
                $generalCount = JobFairVisit::where('job_fair_id', $fair->id)
                    ->where('company_id', auth()->id())
                    ->whereNull('job_opportunity_id')
                    ->count();

                $jobCounts = JobFairVisit::where('job_fair_id', $fair->id)
                    ->where('company_id', auth()->id())
                    ->whereNotNull('job_opportunity_id')
                    ->selectRaw('job_opportunity_id, count(*) as total')
                    ->groupBy('job_opportunity_id')
                    ->pluck('total', 'job_opportunity_id');
            } catch (\Throwable $e) {}
        }

        return view('company.job-fair.leads', compact('fair', 'visits', 'opportunities', 'jobCounts', 'generalCount'));
    }

    /**
     * تحديث الوظيفة المتقدم عليها للخريج في المعرض
     */
    public function updateLeadJob(Request $request)
    {
        $request->validate([
            'visit_id'           => 'required|exists:job_fair_visits,id',
            'job_opportunity_id' => 'nullable',
        ]);

        $company = auth()->user()->company;
        if (!$company) abort(403);

        $visit = JobFairVisit::where('id', $request->visit_id)
            ->where('company_id', auth()->id())
            ->firstOrFail();

        $hasJobOppCol = \Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id');
        $jobTitle = 'تقديم عام';

        if ($hasJobOppCol) {
            $jobId = $request->job_opportunity_id;
            if ($jobId && $jobId !== 'general') {
                $opp = \App\Models\JobOpportunity::where('id', $jobId)
                    ->where('company_id', $company->id)
                    ->firstOrFail();
                $visit->job_opportunity_id = $opp->id;
                $jobTitle = $opp->title;
            } else {
                $visit->job_opportunity_id = null;
            }

            $visit->save();
        }

        return response()->json([
            'success'   => true,
            'message'   => 'تم تحديث الوظيفة المتقدم عليها بنجاح.',
            'job_title' => $jobTitle,
            'job_id'    => $visit->job_opportunity_id ?? null,
        ]);
    }

    /**
     * تصدير السير الذاتية المستلمة كملف CSV / Excel
     */
    public function exportLeads(JobFair $fair)
    {
        $company = auth()->user()->company;
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        $hasJobOppCol = \Illuminate\Support\Facades\Schema::hasColumn('job_fair_visits', 'job_opportunity_id');
        $withs = ['graduate.graduateData'];
        if ($hasJobOppCol) {
            $withs[] = 'jobOpportunity';
        }

        $visits = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', auth()->id())
            ->with($withs)
            ->latest()
            ->get();

        $filename = 'سير_ذاتية_' . Str::slug($fair->title) . '_' . date('Y-m-d') . '.csv';

        $statusLabels = [
            'pending'     => 'قيد الدراسة والمراجعة',
            'shortlisted' => 'القائمة القصيرة',
            'accepted'    => 'مقبول مبدئياً',
            'rejected'    => 'غير متوافق',
        ];

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($visits, $statusLabels, $hasJobOppCol) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens Arabic correctly
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'الرقم',
                'اسم الخريج',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'التخصص',
                'الجامعة',
                'سنة التخرج',
                'المعدل التراكمي (%)',
                'الوظيفة المتقدم عليها',
                'حالة التقييم',
                'ملاحظات المقابلة',
                'تاريخ ووقت الاستلام'
            ]);

            foreach ($visits as $index => $v) {
                $grad = $v->graduate;
                $gData = $grad?->graduateData;
                $gpa = $gData?->gpa ?? $grad?->gpa;

                fputcsv($file, [
                    $index + 1,
                    $grad?->name ?? '—',
                    $grad?->email ?? '—',
                    $grad?->phone ?? '—',
                    $gData?->major ?? $grad?->major ?? $grad?->specialization ?? '—',
                    $gData?->university ?? $grad?->university ?? '—',
                    $gData?->graduation_year ?? $grad?->graduation_year ?? '—',
                    $gpa ? number_format($gpa, 2) . '%' : '—',
                    ($hasJobOppCol && $v->jobOpportunity) ? $v->jobOpportunity->title : 'تقديم عام',
                    $statusLabels[$v->status] ?? $v->status,
                    $v->notes ?? '—',
                    $v->created_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function searchGraduates(Request $request, JobFair $fair)
    {
        $company = auth()->user()->company;
        
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        // Get all graduate IDs registered in this fair
        $registeredIds = JobFairRegistration::where('job_fair_id', $fair->id)
            ->pluck('user_id');

        $query = User::whereIn('id', $registeredIds)
                     ->where('role', 'graduate')
                     ->with('graduateData');

        // تطبيق الفلاتر
        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('major')) {
            $query->where('major', 'like', '%' . $request->major . '%');
        }

        if ($request->filled('gpa_min')) {
            $query->whereHas('graduateData', function($q) use ($request) {
                $q->where('gpa', '>=', $request->gpa_min);
            });
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $graduates */
        $graduates = $query->paginate(12);
        $graduates->withQueryString();

        return view('company.job-fair.search', compact('fair', 'graduates'));
    }

    /**
     * تحديث حالة التوظيف للخريج عبر الأجاكس
     */
    public function updateLeadStatus(Request $request)
    {
        $request->validate([
            'visit_id' => 'required|exists:job_fair_visits,id',
            'status'   => 'required|in:pending,shortlisted,accepted,rejected',
        ]);

        $company = auth()->user()->company;
        if (!$company) abort(403);

        $visit = JobFairVisit::where('id', $request->visit_id)
            ->where('company_id', auth()->id()) // Ensure they own this lead
            ->firstOrFail();

        $visit->status = $request->status;
        $visit->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الخريج بنجاح.',
            'status'  => $visit->status
        ]);
    }

    /**
     * تحميل الأصول الإعلامية والهوية البصرية للمعرض
     */
    public function downloadAsset(JobFair $fair, $type)
    {
        $company = auth()->user()->company;
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        switch ($type) {
            case 'fair-logo':
                if ($fair->fair_logo_path && Storage::disk('public')->exists($fair->fair_logo_path)) {
                    $ext = pathinfo($fair->fair_logo_path, PATHINFO_EXTENSION) ?: 'png';
                    return Storage::disk('public')->download($fair->fair_logo_path, 'شعار_' . Str::slug($fair->title) . '.' . $ext);
                }
                $path = public_path('images/job_fair_logo.png');
                return response()->download($path, 'شعار_معرض_التوظيف_2026.png');

            case 'fair-logo-horizontal':
                if ($fair->fair_logo_horizontal_path && Storage::disk('public')->exists($fair->fair_logo_horizontal_path)) {
                    $ext = pathinfo($fair->fair_logo_horizontal_path, PATHINFO_EXTENSION) ?: 'png';
                    return Storage::disk('public')->download($fair->fair_logo_horizontal_path, 'شعار_' . Str::slug($fair->title) . '_أفقي.' . $ext);
                }
                $path = public_path('images/job_fair_logo_horizontal.png');
                return response()->download($path, 'شعار_معرض_التوظيف_أفقي.png');

            case 'fair-logo-white':
                if ($fair->fair_logo_white_path && Storage::disk('public')->exists($fair->fair_logo_white_path)) {
                    $ext = pathinfo($fair->fair_logo_white_path, PATHINFO_EXTENSION) ?: 'png';
                    return Storage::disk('public')->download($fair->fair_logo_white_path, 'شعار_' . Str::slug($fair->title) . '_أبيض_شفاف.' . $ext);
                }
                $path = public_path('images/job_fair_logo_white.png');
                return response()->download($path, 'شعار_معرض_التوظيف_أبيض_شفاف.png');

            case 'brand-guidelines':
                if ($fair->brand_guidelines_path && Storage::disk('public')->exists($fair->brand_guidelines_path)) {
                    return Storage::disk('public')->download($fair->brand_guidelines_path, 'دليل_الهوية_البصرية_' . Str::slug($fair->title) . '.pdf');
                }
                abort(404, 'دليل الهوية غير متوفر حالياً.');

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
                    return Storage::disk('public')->download($fair->media_kit_path, 'الحقيبة_الإعلامية_' . Str::slug($fair->title) . '.' . $ext);
                }
                $path = public_path('assets/media-kit-2026.zip');
                if (!file_exists($path)) {
                    abort(404, 'الحقيبة الإعلامية غير متوفرة حالياً.');
                }
                return response()->download($path, 'الحقيبة_الإعلامية_معرض_التوظيف_2026.zip');
        }
    }
}
