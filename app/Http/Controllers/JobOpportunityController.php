<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOpportunity;
use App\Models\Company;
use App\Models\Nomination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class JobOpportunityController extends Controller
{
    /**
     * عرض قائمة فرص العمل والتدريب
     */
    public function index(Request $request)
    {
        $query = JobOpportunity::with(['company', 'creator']);

        // تطبيق الفلاتر
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        $opportunities = $query->withCount(['nominations'])
            ->latest()
            ->get();

        $companies = Company::all();

        return view('job-opportunities.index', compact('opportunities', 'companies'));
    }

    /**
     * عرض نموذج إنشاء فرصة جديدة
     */
    public function create()
    {
        $companies = Company::all(); // Fetches all companies

        $specializations = [
            'هندسة برمجيات', 'علوم حاسب', 'هندسة كهربائية', 'هندسة ميكانيكية',
            'هندسة مدنية', 'هندسة معمارية', 'طب بشري', 'طب أسنان', 'صيدلة',
            'علوم طبية مساعدة', 'إدارة أعمال', 'محاسبة', 'اقتصاد', 'تسويق',
            'موارد بشرية', 'قانون', 'إعلام', 'لغات', 'آداب', 'تاريخ',
            'جغرافيا', 'علوم سياسية', 'علم نفس', 'علم اجتماع', 'تربية',
            'فنون جميلة', 'علوم بحرية', 'علوم بيئية', 'تقنية معلومات', 'شبكات حاسوب',
            'أمن سيبراني', 'ذكاء اصطناعي', 'علم البيانات', 'تصميم جرافيك', 'تصميم داخلي',
            'هندسة نفط', 'هندسة كيميائية', 'هندسة طيران', 'هندسة اتصالات', 'هندسة إلكترونية',
            'هندسة مواد', 'هندسة صناعية', 'هندسة زراعية', 'علوم أغذية', 'تغذية',
            'رياضيات', 'فيزياء', 'كيمياء', 'علوم حياة', 'جيولوجيا', 'إحصاء',
            'إدارة مشاريع', 'تحليل نظم', 'دعم فني', 'خدمة عملاء', 'مبيعات',
            'علاقات عامة', 'ترجمة', 'كتابة محتوى', 'تحرير', 'تصوير', 'مونتاج',
            'صيانة', 'فني مختبر', 'تمريض', 'علاج طبيعي', 'تكنولوجيا حيوية',
            'إدارة مستشفيات', 'إدارة فنادق', 'سياحة', 'آثار', 'مكتبات ومعلومات',
            'أرشيف', 'تخطيط حضري', '' // Add more specializations as needed
        ];

        $skills = [
            'برمجة (عام)', 'تطوير الويب (Frontend)', 'تطوير الويب (Backend)', 'تطوير تطبيقات الجوال',
            'إدارة قواعد البيانات', 'أمن الشبكات', 'تحليل البيانات', 'الذكاء الاصطناعي',
            'تعلم الآلة', 'الحوسبة السحابية (AWS, Azure, GCP)', 'إدارة المشاريع (Agile, Scrum)',
            'التفكير النقدي', 'حل المشكلات', 'التواصل الفعال', 'العمل الجماعي',
            'القيادة', 'التكيف والمرونة', 'الإبداع والابتكار', 'إدارة الوقت',
            'اتخاذ القرار', 'التفاوض', 'العرض والتقديم', 'اللغة الإنجليزية (تحدثًا وكتابة)',
            'اللغة الفرنسية', 'اللغة الألمانية', 'التصميم الجرافيكي', 'تحرير الفيديو',
            'التسويق الرقمي', 'تحسين محركات البحث (SEO)', 'التسويق عبر وسائل التواصل الاجتماعي',
            'كتابة المحتوى', 'تحليل الأعمال', 'خدمة العملاء', 'المبيعات',
            'المحاسبة المالية', 'المحاسبة الإدارية', 'التدقيق', 'التحليل المالي',
            'إدارة الموارد البشرية', 'التدريب والتطوير', 'التوظيف', 'العلاقات العامة',
            'البحث العلمي', 'التحليل الإحصائي', 'استخدام برامج Office (Word, Excel, PowerPoint)',
            'استخدام Google Workspace (Docs, Sheets, Slides)', 'التفكير التصميمي', 'التعلم الذاتي',
            'التعامل مع الضغط', 'الاهتمام بالتفاصيل', '' // Add more skills as needed
        ];

        return view('job-opportunities.create', compact('companies', 'specializations', 'skills'));
    }

    /**
     * حفظ فرصة عمل جديدة
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:job,training,internship',
            'contract_type' => 'required|in:full_time,part_time,contract,freelance',
            'company_id' => 'required|exists:companies,id',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'application_deadline' => 'required|date|after:today',
            'required_specializations' => 'nullable|array',
            'required_skills' => 'nullable|array',
            'required_experience' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|string',
            'requirements' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        JobOpportunity::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'contract_type' => $request->contract_type,
            'company_id' => $request->company_id,
            'location' => $request->location,
            'seats' => $request->seats,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'application_deadline' => $request->application_deadline,
            'required_specializations' => $request->required_specializations,
            'required_skills' => $request->required_skills,
            'required_experience' => $request->required_experience,
            'salary' => $request->salary,
            'benefits' => $request->benefits,
            'requirements' => $request->requirements,
            'status' => 'open',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('job-opportunities.index')
            ->with('success', 'تم إنشاء فرصة العمل بنجاح');
    }

    /**
     * عرض تفاصيل فرصة عمل
     */
    public function show($id)
    {
        $opportunity = JobOpportunity::with([
            'company', 
            'creator',
            'nominations.graduate'
        ])->findOrFail($id);

        $nominationsCount = [
            'total' => $opportunity->nominations->count(),
            'pending' => $opportunity->nominations->where('status', 'pending')->count(),
            'accepted' => $opportunity->nominations->where('final_status', 'hired')->count(),
            'rejected' => $opportunity->nominations->where('status', 'rejected')->count(),
        ];

        return view('job-opportunities.show', compact('opportunity', 'nominationsCount'));
    }

    /**
     * عرض نموذج تعديل فرصة عمل
     */
    public function edit($id)
    {
        $opportunity = JobOpportunity::findOrFail($id);
        $companies = Company::all();

        return view('job-opportunities.edit', compact('opportunity', 'companies'));
    }

    /**
     * تحديث فرصة عمل
     */
    public function update(Request $request, $id)
    {
        $opportunity = JobOpportunity::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:job,training,internship',
            'contract_type' => 'required|in:full_time,part_time,contract,freelance',
            'company_id' => 'required|exists:companies,id',
            'location' => 'required|string|max:255',
            'seats' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'application_deadline' => 'required|date|after:today',
            'required_specializations' => 'nullable|array',
            'required_skills' => 'nullable|array',
            'required_experience' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:new,open,closed,completed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $opportunity->update($request->all());

        return redirect()->route('job-opportunities.show', $opportunity->id)
            ->with('success', 'تم تحديث فرصة العمل بنجاح');
    }

    /**
     * حذف فرصة عمل
     */
    public function destroy($id)
    {
        $opportunity = JobOpportunity::findOrFail($id);

        // التحقق من عدم وجود ترشيحات مرتبطة
        if ($opportunity->nominations()->exists()) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الفرصة لأنها مرتبطة بترشيحات');
        }

        $opportunity->delete();

        return redirect()->route('job-opportunities.index')
            ->with('success', 'تم حذف فرصة العمل بنجاح');
    }

    /**
     * تغيير حالة الفرصة
     */
    public function updateStatus(Request $request, $id)
    {
        $opportunity = JobOpportunity::findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,open,closed,completed',
        ]);

        $opportunity->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'تم تحديث حالة الفرصة بنجاح');
    }

    /**
     * استيراد فرص عمل من ملف CSV
     */
    public function importFromExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt|max:5120',
            'company_id' => 'required|exists:companies,id',
        ]);

        try {
            $file = $request->file('excel_file');
            $importedCount = $this->importFromCSV($file, $request->company_id);

            if ($importedCount > 0) {
                return redirect()->route('job-opportunities.index')
                    ->with('success', "تم استيراد {$importedCount} فرصة عمل بنجاح");
            } else {
                return redirect()->back()
                    ->with('warning', 'لم يتم استيراد أي بيانات. تأكد من تنسيق الملف.');
            }

        } catch (\Exception $e) {
            \Log::error('Import Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * استيراد من ملف CSV
     */
    private function importFromCSV($file, $companyId)
    {
        $path = $file->getPathname();
        $handle = fopen($path, 'r');
        
        if (!$handle) {
            throw new \Exception('لا يمكن فتح الملف');
        }
        
        // تخطي الصف الأول (العناوين)
        $headers = fgetcsv($handle);
        
        $importedCount = 0;
        $rowNumber = 1;
        
        while (($row = fgetcsv($handle)) !== FALSE) {
            $rowNumber++;
            
            // تخطي الصفوف الفارغة
            if (count($row) < 3 || empty(trim($row[0]))) {
                continue;
            }
            
            try {
                $this->createJobOpportunityFromRow($row, $companyId, $rowNumber);
                $importedCount++;
            } catch (\Exception $e) {
                \Log::error("Error importing row {$rowNumber}: " . $e->getMessage());
                continue;
            }
        }
        
        fclose($handle);
        return $importedCount;
    }

    /**
     * إنشاء فرصة عمل من صف البيانات
     */
    private function createJobOpportunityFromRow($row, $companyId, $rowNumber)
    {
        // تنظيف البيانات
        $title = trim($row[0] ?? '');
        $description = trim($row[1] ?? 'لا يوجد وصف');
        $type = trim($row[2] ?? 'job');
        $contractType = trim($row[3] ?? 'full_time');
        $location = trim($row[4] ?? 'طرابلس');
        $seats = intval($row[5] ?? 1);
        $startDate = trim($row[6] ?? '');
        $endDate = trim($row[7] ?? '');
        $deadline = trim($row[8] ?? '');
        $specializations = trim($row[9] ?? '');
        $skills = trim($row[10] ?? '');
        $experience = trim($row[11] ?? 'مبتدئ');
        $salary = trim($row[12] ?? '');
        $benefits = trim($row[13] ?? '');
        $requirements = trim($row[14] ?? '');

        // التحقق من البيانات الأساسية
        if (empty($title)) {
            throw new \Exception("العنوان فارغ في الصف {$rowNumber}");
        }

        if ($seats < 1) {
            throw new \Exception("عدد المقاعد غير صحيح في الصف {$rowNumber}");
        }

        // إنشاء فرصة العمل
        JobOpportunity::create([
            'title' => $title,
            'description' => $description,
            'type' => $this->mapType($type),
            'contract_type' => $this->mapContractType($contractType),
            'company_id' => $companyId,
            'location' => $location,
            'seats' => $seats,
            'start_date' => $this->parseDate($startDate) ?? now()->addDays(30),
            'end_date' => $this->parseDate($endDate) ?? now()->addDays(60),
            'application_deadline' => $this->parseDate($deadline) ?? now()->addDays(15),
            'required_specializations' => $this->parseCsvArray($specializations),
            'required_skills' => $this->parseCsvArray($skills),
            'required_experience' => $experience,
            'salary' => $salary ? floatval($salary) : null,
            'status' => 'open',
            'benefits' => $benefits ?: null,
            'requirements' => $requirements ?: null,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * تحويل النص إلى تاريخ
     */
    private function parseDate($dateString)
    {
        if (empty(trim($dateString))) {
            return null;
        }

        try {
            // محاولة تحويل التنسيقات المختلفة
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'm/d/Y',
                'd-m-Y',
                'm-d-Y',
            ];

            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, trim($dateString));
                if ($date !== false) {
                    return $date;
                }
            }

            // إذا فشلت جميع المحاولات، حاول التحليل التلقائي
            return Carbon::parse(trim($dateString));
        } catch (\Exception $e) {
            \Log::warning("Cannot parse date: {$dateString} - " . $e->getMessage());
            return null;
        }
    }

    /**
     * تحويل نوع الفرصة
     */
    private function mapType($type)
    {
        $type = strtolower(trim($type));
        
        $typeMap = [
            'وظيفة' => 'job',
            'job' => 'job',
            'وظائف' => 'job',
            'jobs' => 'job',
            'تدريب' => 'training',
            'training' => 'training',
            'تدريبات' => 'training',
            'trainings' => 'training',
            'تدريب عملي' => 'internship',
            'internship' => 'internship',
            'تدريبات عملية' => 'internship',
            'internships' => 'internship',
        ];
        
        return $typeMap[$type] ?? 'job';
    }

    /**
     * تحويل نوع العقد
     */
    private function mapContractType($type)
    {
        $type = strtolower(trim($type));
        
        $contractMap = [
            'دوام كامل' => 'full_time',
            'full_time' => 'full_time',
            'full time' => 'full_time',
            'دوام جزئي' => 'part_time',
            'part_time' => 'part_time',
            'part time' => 'part_time',
            'عقد' => 'contract',
            'contract' => 'contract',
            'عمل حر' => 'freelance',
            'freelance' => 'freelance',
        ];
        
        return $contractMap[$type] ?? 'full_time';
    }

    /**
     * تحويل النص إلى مصفوفة
     */
    private function parseCsvArray($value)
    {
        if (empty(trim($value))) {
            return [];
        }

        $value = trim($value);
        
        // تحويل النص إلى مصفوفة باستخدام الفواصل
        $items = array_map('trim', explode(',', $value));
        
        // إزالة القيم الفارغة
        return array_filter($items);
    }

    /**
     * البحث عن فرص العمل
     */
    public function search(Request $request)
    {
        $query = JobOpportunity::with('company');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $opportunities = $query->where('status', 'open')
            ->where('application_deadline', '>=', now())
            ->latest()
            ->get();

        return view('job-opportunities.search', compact('opportunities'));
    }

    /**
     * عرض الترشيحات لفرصة محددة
     */
    public function nominations($id)
    {
        $opportunity = JobOpportunity::with(['nominations.graduate'])->findOrFail($id);
        $nominations = $opportunity->nominations()->latest()->get();

        $statuses = [
            'pending' => 'قيد المراجعة',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'hired' => 'تم التوظيف',
            'interview' => 'مقابلة',
            'offered' => 'تم تقديم عرض',
        ];

        return view('job-opportunities.nominations', compact('opportunity', 'nominations', 'statuses'));
    }

    /**
     * إحصائيات فرص العمل
     */
    public function statistics()
    {
        $stats = [
            'byType' => JobOpportunity::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            
            'byStatus' => JobOpportunity::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get(),
            
            'byCompany' => JobOpportunity::selectRaw('company_id, companies.name, count(*) as count')
                ->join('companies', 'job_opportunities.company_id', '=', 'companies.id')
                ->groupBy('company_id', 'companies.name')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        return view('job-opportunities.statistics', compact('stats'));
    }

    /**
     * نسخ فرصة عمل موجودة
     */
    public function duplicate($id)
    {
        $originalOpportunity = JobOpportunity::findOrFail($id);

        $newOpportunity = $originalOpportunity->replicate();
        $newOpportunity->title = $originalOpportunity->title . ' (نسخة)';
        $newOpportunity->status = 'new';
        $newOpportunity->created_by = Auth::id();
        $newOpportunity->save();

        return redirect()->route('job-opportunities.edit', $newOpportunity->id)
            ->with('success', 'تم نسخ الفرصة بنجاح، يمكنك الآن تعديل التفاصيل');
    }
}
