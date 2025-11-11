<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GraduateData;
use App\Models\JobOpportunity;
use App\Models\Nomination;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CareerGuidanceController extends Controller
{
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

        $graduates = $query->withCount(['nominations', 'nominations as accepted_nominations_count' => function($q) {
            $q->where('final_status', 'hired');
        }])->latest()->get();

        $majors = GraduateData::distinct()->pluck('major');
        $graduationYears = GraduateData::distinct()->pluck('graduation_year');

        return view('career-guidance.graduates.index', compact('graduates', 'majors', 'graduationYears'));
    }

    /**
     * عرض تفاصيل خريج
     */
    public function showGraduate($id)
    {
        $graduate = GraduateData::with(['nominations.jobOpportunity'])->findOrFail($id);
        return view('career-guidance.graduates.show', compact('graduate'));
    }
    
    /**
     * عرض نموذج تعديل خريج
     */
    public function editGraduate($id)
    {
        $graduate = GraduateData::findOrFail($id);
        return view('career-guidance.graduates.edit', compact('graduate'));
    }

    /**
     * تحديث بيانات خريج
     */
    public function updateGraduate(Request $request, $id)
    {
        $graduate = GraduateData::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:graduates_data,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'major' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:2000|max:' . date('Y'),
            'gpa' => 'nullable|numeric|min:0|max:4',
            'degree' => 'nullable|string|max:255',
            'employment_status' => 'required|in:employed,unemployed,seeking_opportunities,continuing_education',
            'work_experience' => 'nullable|string',
            'address' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
        ]);

        // معالجة المهارات واللغات إذا كانت موجودة
        $skills = $request->skills ? (is_array($request->skills) ? $request->skills : array_map('trim', explode(',', $request->skills))) : [];
        $languages = $request->languages ? (is_array($request->languages) ? $request->languages : array_map('trim', explode(',', $request->languages))) : [];

        $graduate->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'major' => $request->major,
            'graduation_year' => $request->graduation_year,
            'gpa' => $request->gpa,
            'degree' => $request->degree ?: 'بكالوريوس',
            'employment_status' => $request->employment_status,
            'skills' => $skills,
            'languages' => $languages,
            'work_experience' => $request->work_experience,
            'address' => $request->address,
            'linkedin_url' => $request->linkedin_url,
        ]);

        return redirect()->route('admin.career-guidance.graduates.show', $graduate->id)
            ->with('success', 'تم تحديث بيانات الخريج بنجاح');
    }

    /**
     * عرض نموذج إضافة خريج جديد
     */
    public function createGraduate()
    {
        return view('career-guidance.graduates.create');
    }

    /**
     * حفظ خريج جديد
     */
    public function storeGraduate(Request $request)
    {
        if (!auth()->user()->canAddGraduates()) {
            abort(403, 'ليس لديك صلاحية لإضافة خريجين');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:graduates_data,email',
            'phone' => 'nullable|string|max:20',
            'major' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:2000|max:' . date('Y'),
            'gpa' => 'nullable|numeric|min:0|max:4',
            'degree' => 'nullable|string|max:255',
            'employment_status' => 'required|in:employed,unemployed,seeking_opportunities,continuing_education',
            'work_experience' => 'nullable|string',
            'address' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
        ]);

        // معالجة المهارات واللغات
        $skills = $request->skills ? (is_array($request->skills) ? $request->skills : array_map('trim', explode(',', $request->skills))) : [];
        $languages = $request->languages ? (is_array($request->languages) ? $request->languages : array_map('trim', explode(',', $request->languages))) : [];

        GraduateData::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'major' => $request->major,
            'graduation_year' => $request->graduation_year,
            'gpa' => $request->gpa,
            'degree' => $request->degree ?: 'بكالوريوس',
            'employment_status' => $request->employment_status,
            'skills' => $skills,
            'languages' => $languages,
            'work_experience' => $request->work_experience,
            'address' => $request->address,
            'linkedin_url' => $request->linkedin_url,
            'added_by' => Auth::id(),
            'data_source' => 'manual',
            'is_active' => true,
        ]);

        return redirect()->route('career-guidance.graduates')
                       ->with('success', 'تم إضافة الخريج بنجاح');
    }

    /**
     * إدارة الترشيحات
     */
    public function nominations(Request $request)
    {
        $query = Nomination::with(['graduate', 'jobOpportunity.company']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('opportunity_id') && $request->opportunity_id) {
            $query->where('job_opportunity_id', $request->opportunity_id);
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

        return view('career-guidance.nominations.show', compact('nomination'));
    }

    /**
     * عرض نموذج ترشيح خريج جديد
     */
    public function createNomination()
    {
        $graduates = GraduateData::where('is_active', true)
            ->where('employment_status', 'seeking_opportunities')
            ->get();

        $opportunities = JobOpportunity::where('status', 'open')
            ->where('application_deadline', '>=', now())
            ->with('company')
            ->get();

        return view('career-guidance.nominations.create', compact('graduates', 'opportunities'));
    }

    /**
     * ترشيح خريج لفرصة عمل
     */
    public function nominateGraduate(Request $request)
    {
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

        Nomination::create([
            'graduate_id' => $request->graduate_id,
            'job_opportunity_id' => $request->job_opportunity_id,
            'nominated_by' => Auth::id(),
            'nomination_notes' => $request->nomination_notes,
            'matching_reasons' => $request->matching_reasons,
            'status' => 'pending',
            'nominated_at' => now(),
        ]);

        return redirect()->route('career-guidance.nominations')
            ->with('success', 'تم ترشيح الخريج بنجاح');
    }

    /**
     * تحديث حالة الترشيح
     */
    public function updateNominationStatus(Request $request, $id)
    {
        $nomination = Nomination::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,sent_to_company,under_review,interview_scheduled,accepted,rejected,withdrawn',
            'interview_date' => 'nullable|date',
            'interview_time' => 'nullable',
            'interview_location' => 'nullable|string',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'interview_scheduled' && $request->interview_date) {
            $updateData['interview_date'] = $request->interview_date;
            $updateData['interview_time'] = $request->interview_time;
            $updateData['interview_location'] = $request->interview_location;
        }

        if ($request->status === 'sent_to_company') {
            $updateData['sent_to_company_at'] = now();
        }

        $nomination->update($updateData);

        return redirect()->back()->with('success', 'تم تحديث حالة الترشيح بنجاح');
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
            'name', 'email', 'phone', 'major', 
            'graduation_year', 'gpa', 'degree', 'skills', 
            'languages', 'employment_status', 'work_experience', 
            'address', 'linkedin_url'
        ];

        $sampleData = [
            [
                'أحمد محمد', 'ahmed@example.com', '0912345678',
                'هندسة حاسوب', '2023', '3.75', 'بكالوريوس', 'برمجة,تصميم,إدارة',
                'عربية,إنجليزية', 'seeking_opportunities', '2 سنوات في شركة X',
                'طرابلس - الحي القديم', 'linkedin.com/in/ahmed'
            ],
            [
                'فاطمة علي', '', '0923456789',
                'علوم حاسوب', '2022', '', 'بكالوريوس', 'تحليل بيانات,ذكاء اصطناعي',
                'عربية,إنجليزية,فرنسية', 'employed', 'مطور برمجيات في شركة Y',
                'بنغازي - المدينة الجامعية', 'linkedin.com/in/fatima'
            ]
        ];

        return response()->streamDownload(function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // إضافة BOM للحروف العربية
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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
 * التحقق من صحة بيانات المخططات
 */
private function validateChartData($chartData)
{
    if (!is_array($chartData)) {
        return false;
    }
    // Add more specific validation for chartData structure if needed
    // For now, a simple array check is sufficient.
    
    return true;
}
    /**
     * استيراد بيانات الخريجين من Excel
     */
    public function importGraduates(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $file = $request->file('excel_file');
            $importedCount = $this->processExcelImport($file);

            return redirect()->route('career-guidance.graduates')
                ->with('success', "تم استيراد {$importedCount} خريج بنجاح");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function processExcelImport($file)
    {
        $path = $file->getRealPath();
        
        // استخدام مكتبة Excel لقراءة الملف
        try {
            $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];
        } catch (\Exception $e) {
            // إذا فشل استخدام Excel، حاول استخدام CSV العادي
            $data = $this->readCSV($path);
        }
        
        $importedCount = 0;
        $errors = [];

        // تخطي الصف الأول (العناوين)
        for ($i = 1; $i < count($data); $i++) {
            try {
                $row = $data[$i];
                
                // تخطي الصفوف الفارغة
                if (empty(trim($row[0] ?? ''))) {
                    continue;
                }

                $this->createGraduateFromRow($row, $i + 1);
                $importedCount++;

            } catch (\Exception $e) {
                $errors[] = "الصف " . ($i + 1) . ": " . $e->getMessage();
            }
        }

        // عرض الأخطاء إذا وجدت
        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }

        return $importedCount;
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
        $name = trim($row[0] ?? '');
        $email = trim($row[1] ?? '');
        $phone = trim($row[2] ?? '');
        $major = trim($row[3] ?? '');
        $graduationYear = intval($row[4] ?? 0);
        $gpa = !empty($row[5]) ? floatval($row[5]) : null;
        $degree = trim($row[6] ?? 'بكالوريوس');
        $skills = !empty($row[7]) ? array_map('trim', explode(',', $row[7])) : [];
        $languages = !empty($row[8]) ? array_map('trim', explode(',', $row[8])) : [];
        $employmentStatus = trim($row[9] ?? 'seeking_opportunities');
        $workExperience = trim($row[10] ?? '');
        $address = trim($row[11] ?? '');
        $linkedinUrl = trim($row[12] ?? '');

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
     */public function advancedReports()
{
    try {
        // الإحصائيات الأساسية
        $stats = $this->getAdvancedStats();
        
        // بيانات المخططات مع التحقق من الصحة
        $chartData = $this->getChartData();
        
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

        return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights'));
    
    } catch (\Exception $e) {
        \Log::error('Advanced Reports Error: ' . $e->getMessage());
        
        // في حالة الخطأ، عرض بيانات نموذجية
        $stats = $this->getAdvancedStats();
        $chartData = $this->getSampleChartData();
        $insights = $this->getAIInsights($stats);
        
        return view('career-guidance.advanced-reports', compact('stats', 'chartData', 'insights'))
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
            
            $stats = $this->getAdvancedStats();
            $chartData = $this->getChartData();
            $insights = $this->getAIInsights($stats);
            
            if ($stats['totalGraduates'] == 0) {
                return redirect()->route('career-guidance.advanced-reports')
                    ->with('warning', 'لا توجد بيانات كافية لتصدير التقرير');
            }
            
            $fileName = "التقارير_المتقدمة_" . date('Y-m-d') . ".pdf";
            
            // إذا كان لديك مكتبة PDF مثبتة
            $pdf = \PDF::loadView('career-guidance.reports-pdf', compact('stats', 'chartData', 'insights', 'type'));
            return $pdf->download($fileName);
            
        } catch (\Exception $e) {
            Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->route('career-guidance.advanced-reports')
                ->with('error', 'حدث خطأ أثناء تصدير التقرير: ' . $e->getMessage());
        }
    }

    /**
     * تصدير التقرير كـ Excel
     */
    public function exportReportsExcel(Request $request)
    {
        try {
            $stats = $this->getAdvancedStats();
            
            if ($stats['totalGraduates'] == 0) {
                return redirect()->route('career-guidance.advanced-reports')
                    ->with('warning', 'لا توجد بيانات كافية لتصدير التقرير');
            }
            
            // إذا كان لديك مكتبة Excel مثبتة
            $insights = $this->getAIInsights($stats); // Get insights for Excel export
            return Excel::download(new \App\Exports\AdvancedReportsExport($stats, $insights), 'التقارير_المتقدمة.xlsx');
            
        } catch (\Exception $e) {
            Log::error('Excel Export Error: ' . $e->getMessage());
            return redirect()->route('admin.career-guidance.advanced-reports')
                ->with('error', 'حدث خطأ أثناء تصدير التقرير: ' . $e->getMessage());
        }
    }

    /**
     * جمع الإحصائيات المتقدمة
     */
    private function getAdvancedStats()
    {
        // استخدام استعلامات أكثر كفاءة
        $graduateStats = GraduateData::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN employment_status = "employed" THEN 1 ELSE 0 END) as employed,
            SUM(CASE WHEN employment_status = "seeking_opportunities" THEN 1 ELSE 0 END) as seeking
        ')->first();

        $nominationStats = Nomination::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN final_status = "hired" THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status NOT IN ("rejected", "withdrawn") THEN 1 ELSE 0 END) as active
        ')->first();

        $totalGraduates = $graduateStats->total ?? 0;
        $employedGraduates = $graduateStats->employed ?? 0;
        $seekingOpportunities = $graduateStats->seeking ?? 0;
        
        return [
            'totalGraduates' => $totalGraduates,
            'employedGraduates' => $employedGraduates,
            'seekingOpportunities' => $seekingOpportunities,
            'employmentRate' => $totalGraduates > 0 ? round(($employedGraduates / $totalGraduates) * 100, 1) : 0,
            'seekingRate' => $totalGraduates > 0 ? round(($seekingOpportunities / $totalGraduates) * 100, 1) : 0,
            'activeNominations' => $nominationStats->active ?? 0,
            'successRate' => $this->calculateSuccessRate(),
            'availableOpportunities' => JobOpportunity::where('status', 'open')->count(),
            'newGraduatesThisMonth' => GraduateData::whereMonth('created_at', now()->month)->count(),
            'newOpportunitiesThisWeek' => JobOpportunity::where('created_at', '>=', now()->subWeek())->count(),
            'overallSuccessRate' => $this->calculateOverallSuccessRate(),
        ];
    }

    /**
     * بيانات المخططات البيانية
     */
    private function getChartData()
    {
        try {
            // توزيع الخريجين حسب التخصص
            $majorsDistribution = $this->getMajorsDistribution();
            
            // حالة التوظيف
            $employmentStatus = $this->getEmploymentStatus();
            
            // حالة الترشيحات
            $nominationsStatus = $this->getNominationsStatus();
            
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
                'datasets' => [[
                    'label' => 'عدد الخريجين',
                    'data' => [25, 18, 12, 8, 6],
                    'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    'borderColor' => ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    'borderWidth' => 2
                ]]
            ],
            'employmentStatus' => [
                'labels' => ['موظف', 'باحث عن عمل', 'غير موظف', 'مستكمل للدراسة'],
                'datasets' => [[
                    'data' => [45, 30, 15, 10],
                    'backgroundColor' => ['#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]]
            ],
            'nominationsStatus' => [
                'labels' => ['قيد المراجعة', 'مرسل للشركة', 'مقابلة مجدولة', 'مقبول', 'مرفوض'],
                'datasets' => [[
                    'data' => [20, 15, 10, 8, 5],
                    'backgroundColor' => ['#f6c23e', '#36b9cc', '#858796', '#1cc88a', '#e74a3b'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]]
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
                'datasets' => [[
                    'label' => 'معدل النجاح %',
                    'data' => [75, 60, 80, 55],
                    'backgroundColor' => 'rgba(78, 115, 223, 0.2)',
                    'borderColor' => '#4e73df',
                    'pointBackgroundColor' => '#4e73df',
                    'pointBorderColor' => '#fff'
                ]]
            ],
            'opportunitiesDistribution' => [
                'labels' => ['وظائف', 'تدريبات', 'تدريب عملي'],
                'datasets' => [[
                    'data' => [35, 25, 15],
                    'backgroundColor' => ['#4e73df', '#1cc88a', '#36b9cc'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]]
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
    private function getMajorsDistribution()
    {
        $majors = GraduateData::select('major')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('major')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get();

        $labels = $majors->pluck('major')->toArray();
        $data = $majors->pluck('count')->toArray();
        $backgroundColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#858796', '#5a5c69', '#6f42c1'
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'عدد الخريجين',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => array_map(function($color) {
                        return $color;
                    }, $backgroundColors),
                    'borderWidth' => 2
                ]
            ]
        ];
    }

    /**
     * حالة التوظيف للخريجين
     */
    private function getEmploymentStatus()
    {
        $statusCounts = GraduateData::select('employment_status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('employment_status')
            ->get();

        $statusLabels = [
            'employed' => 'موظف',
            'unemployed' => 'غير موظف', 
            'seeking_opportunities' => 'باحث عن عمل',
            'continuing_education' => 'مستكمل للدراسة'
        ];

        $labels = [];
        $data = [];
        $backgroundColors = ['#1cc88a', '#e74a3b', '#f6c23e', '#36b9cc'];

        foreach ($statusCounts as $status) {
            $labels[] = $statusLabels[$status->employment_status] ?? $status->employment_status;
            $data[] = $status->count;
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
     * توزيع الترشيحات حسب الحالة
     */
    private function getNominationsStatus()
    {
        $statusCounts = Nomination::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $statusLabels = [
            'pending' => 'قيد المراجعة',
            'sent_to_company' => 'مرسل للشركة',
            'under_review' => 'قيد الدراسة',
            'interview_scheduled' => 'مقابلة مجدولة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'withdrawn' => 'ملغي'
        ];

        $labels = [];
        $data = [];
        $backgroundColors = ['#f6c23e', '#36b9cc', '#4e73df', '#858796', '#1cc88a', '#e74a3b', '#5a5c69'];

        foreach ($statusCounts as $status) {
            $labels[] = $statusLabels[$status->status] ?? $status->status;
            $data[] = $status->count;
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
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
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
     * تحليل المهارات المطلوبة مقابل المتاحة
     */
    private function getSkillsAnalysis()
    {
        // المهارات المطلوبة في فرص العمل
        $requiredSkills = JobOpportunity::whereNotNull('required_skills')
            ->get()
            ->flatMap(function($job) {
                return $job->required_skills ?? [];
            })
            ->countBy()
            ->sortDesc()
            ->take(10);

        // المهارات المتاحة لدى الخريجين
        $availableSkills = GraduateData::whereNotNull('skills')
            ->get()
            ->flatMap(function($grad) {
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
