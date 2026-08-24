<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairVisit;
use App\Models\JobFairRegistration;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CompanyJobFairController extends Controller
{
    /**
     * عرض المعارض التي تشارك فيها الشركة
     */
    public function index()
    {
        $companyId = auth()->id();
        
        $fairs = JobFair::whereHas('companies', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->orderBy('event_date', 'desc')
        ->get();

        return view('company.job-fair.index', compact('fairs'));
    }

    /**
     * واجهة ماسح رمز الاستجابة السريعة (Scanner)
     */
    public function scanner(JobFair $fair)
    {
        // التأكد من أن الشركة مشاركة في هذا المعرض
        if (!$fair->companies()->where('company_id', auth()->id())->exists()) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        return view('company.job-fair.scanner', compact('fair'));
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
            return response()->json(['success' => false, 'message' => 'رمز غير صالح أو لم يسجل الخريج في هذا المعرض.']);
        }

        $graduate = $registration->graduate;
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

        return response()->json([
            'success' => true,
            'already_visited' => false,
            'graduate_name' => $graduate->name,
            'major' => $graduate->major,
            'message' => 'تم استلام بيانات الخريج بنجاح.'
        ]);
    }

    /**
     * عرض السير الذاتية المستلمة (Leads) لمعرض محدد
     */
    public function leads(JobFair $fair)
    {
        if (!$fair->companies()->where('company_id', auth()->id())->exists()) {
            abort(403);
        }

        $visits = JobFairVisit::where('job_fair_id', $fair->id)
            ->where('company_id', auth()->id())
            ->with(['graduate', 'graduate.graduateData'])
            ->latest()
            ->paginate(15);

        return view('company.job-fair.leads', compact('fair', 'visits'));
    }
}
