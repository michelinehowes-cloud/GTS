<?php

namespace App\Http\Controllers;

use App\Models\JobOpportunity;
use App\Models\Nomination;
use App\Models\GraduateData;
use Illuminate\Http\Request;

class GraduateJobController extends Controller
{
    /**
     * عرض فرص العمل المتاحة للخريج
     */
    public function index(Request $request)
    {
        $query = JobOpportunity::where('status', 'open')
            ->where('application_deadline', '>=', now())
            ->with(['company']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        // التصفية حسب نوع الوظيفة
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // التصفية حسب الموقع
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $jobOpportunities = $query->latest()->paginate(12);

        // الحصول على بيانات الخريج
        $graduateData = GraduateData::where('email', auth()->user()->email)->first();

        // الحصول على الترشيحات الخاصة بالخريج
        $myNominations = [];
        if ($graduateData) {
            $myNominations = Nomination::where('graduate_id', $graduateData->id)
                ->pluck('job_opportunity_id')
                ->toArray();
        }

        return view('graduate.job-opportunities.index', compact('jobOpportunities', 'myNominations', 'graduateData'));
    }

    /**
     * عرض تفاصيل فرصة عمل
     */
    public function show($id)
    {
        $jobOpportunity = JobOpportunity::with(['company'])->findOrFail($id);

        // الحصول على بيانات الخريج
        $graduateData = GraduateData::where('email', auth()->user()->email)->first();

        // التحقق من وجود ترشيح سابق
        $nomination = null;
        if ($graduateData) {
            $nomination = Nomination::where('graduate_id', $graduateData->id)
                ->where('job_opportunity_id', $id)
                ->first();
        }

        return view('graduate.job-opportunities.show', compact('jobOpportunity', 'nomination', 'graduateData'));
    }

    /**
     * التقديم على فرصة عمل (ترشيح ذاتي)
     */
    public function apply(Request $request, $id)
    {
        $jobOpportunity = JobOpportunity::findOrFail($id);

        // التحقق من أن الفرصة لا تزال متاحة
        if ($jobOpportunity->status !== 'open' || $jobOpportunity->application_deadline < now()) {
            return back()->withErrors(['error' => 'عذراً، هذه الفرصة لم تعد متاحة']);
        }

        // الحصول على بيانات الخريج
        $graduateData = GraduateData::where('email', auth()->user()->email)->first();

        if (!$graduateData) {
            return back()->withErrors(['error' => 'يجب إكمال بياناتك الشخصية أولاً']);
        }

        // التحقق من عدم وجود ترشيح سابق
        $existingNomination = Nomination::where('graduate_id', $graduateData->id)
            ->where('job_opportunity_id', $id)
            ->first();

        if ($existingNomination) {
            return back()->withErrors(['error' => 'لقد تقدمت لهذه الفرصة مسبقاً']);
        }

        // إنشاء ترشيح جديد
        // إنشاء ترشيح جديد
        Nomination::create([
            'graduate_id' => $graduateData->id,
            'job_opportunity_id' => $id,
            'nominated_by' => auth()->id(), // الخريج رشح نفسه
            'nomination_type' => 'self', // ترشيح ذاتي
            'status' => 'pending',
            'nomination_notes' => $request->notes ?? 'ترشيح ذاتي من الخريج',
        ]);

        return back()->with('success', 'تم تقديم طلبك بنجاح! سيتم مراجعته قريباً.');
    }

    /**
     * عرض ترشيحاتي ومقابلاتي
     */
    public function myApplications()
    {
        // الحصول على بيانات الخريج
        $graduateData = GraduateData::where('email', auth()->user()->email)->first();

        if (!$graduateData) {
            return view('graduate.job-opportunities.my-applications', [
                'nominations' => collect([]),
                'graduateData' => null
            ]);
        }

        // الحصول على جميع الترشيحات
        $nominations = Nomination::where('graduate_id', $graduateData->id)
            ->with(['jobOpportunity.company', 'nominatedBy'])
            ->latest()
            ->get();

        return view('graduate.job-opportunities.my-applications', compact('nominations', 'graduateData'));
    }

    /**
     * إلغاء الترشيح
     */
    public function cancelApplication($id)
    {
        $graduateData = GraduateData::where('email', auth()->user()->email)->first();

        if (!$graduateData) {
            return back()->withErrors(['error' => 'لم يتم العثور على بياناتك']);
        }

        $nomination = Nomination::where('id', $id)
            ->where('graduate_id', $graduateData->id)
            ->firstOrFail();

        // يمكن إلغاء الترشيح فقط إذا كان في حالة pending
        if ($nomination->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن إلغاء هذا الترشيح']);
        }

        $nomination->delete();

        return back()->with('success', 'تم إلغاء الترشيح بنجاح');
    }
}
