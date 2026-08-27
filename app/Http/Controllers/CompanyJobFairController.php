<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairVisit;
use App\Models\JobFairRegistration;
use App\Models\User;
use Illuminate\Http\Request;
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

        return view('company.job-fair.scanner', compact('fair'));
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
     * تسجيل زيارة خريج (استلام سيرة ذاتية)
     */
    public function storeVisit(Request $request, JobFair $fair)
    {
        $qrCode = trim($request->qr_code);
        $cleanNumber = ltrim($qrCode, '#');

        $registration = JobFairRegistration::where(function($query) use ($qrCode, $cleanNumber) {
                $query->where('qr_code', $qrCode)
                      ->orWhere('registration_number', $qrCode)
                      ->orWhere('registration_number', $cleanNumber);
            })
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

        // company_id in job_fair_visits references users.id, not companies.id
        $companyId = auth()->id();

        // Check if already visited
        $visit = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $companyId)
            ->where('graduate_id', $graduate->id)
            ->first();

        if ($visit) {
            return response()->json([
                'success' => true,
                'already_visited' => true,
                'graduate_name' => $graduate->name,
                'message' => 'تم استلام بيانات هذا الخريج مسبقاً.'
            ]);
        }

        JobFairVisit::create([
            'job_fair_id' => $fair->id,
            'company_id'  => $companyId,
            'graduate_id' => $graduate->id,
            'notes'       => $request->notes,
        ]);

        // reload with the id
        $visit = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', $companyId)
            ->where('graduate_id', $graduate->id)
            ->first();

        return response()->json([
            'success'        => true,
            'already_visited'=> false,
            'visit_id'       => $visit->id,
            'graduate_name'  => $graduate->name,
            'major'          => $graduate->specialization ?? $graduate->major ?? $graduate->qualification ?? null,
            'university'     => $graduate->university,
            'graduation_year'=> $graduate->graduation_year,
            'gpa'            => $graduate->gpa ? number_format($graduate->gpa, 2) : null,
            'phone'          => $graduate->phone,
            'skills'         => $graduate->skills ?? [],
            'message'        => 'تم استلام بيانات الخريج بنجاح.',
        ]);
    }

    /**
     * تحديث نتيجة المقابلة لزيارة معرض
     */
    public function updateVisitOutcome(Request $request, JobFairVisit $visit)
    {
        $company = auth()->user()->company;
        if (!$company || $visit->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح.'], 403);
        }

        $visit->update(['status' => $request->outcome]);

        $labels = [
            'shortlisted' => 'مدرج في القائمة القصيرة ⭐',
            'accepted'    => 'مقبول ✅',
            'rejected'    => 'غير مناسب ❌',
            'pending'     => 'قيد الدراسة 🕐',
        ];

        return response()->json([
            'success' => true,
            'label'   => $labels[$request->outcome] ?? $request->outcome,
        ]);
    }


    /**
     * عرض السير الذاتية المستلمة (Leads) لمعرض محدد
     */
    public function leads(JobFair $fair)
    {
        $company = auth()->user()->company;
        
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        $visits = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', auth()->id())
            ->with(['graduate', 'graduate.graduateData'])
            ->latest()
            ->paginate(15);

        return view('company.job-fair.leads', compact('fair', 'visits'));
    }
    public function searchGraduates(Request $request, JobFair $fair)
    {
        $company = auth()->user()->company;
        
        if (!$company || !$fair->companies()->where('company_id', $company->id)->exists()) {
            abort(403);
        }

        // Get all graduate IDs registered in this fair
        $registeredIds = \App\Models\JobFairRegistration::where('job_fair_id', $fair->id)
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

        $graduates = $query->paginate(12)->withQueryString();

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
            ->where('company_id', $company->id) // Ensure they own this lead
            ->firstOrFail();

        $visit->status = $request->status;
        $visit->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الخريج بنجاح.',
            'status'  => $visit->status
        ]);
    }
}
