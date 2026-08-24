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
        if (Auth::check() && $fair) {
            $myRegistration = JobFairRegistration::where('job_fair_id', $fair->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        $companies = $fair ? $fair->companies()->with('company')->where('status', 'confirmed')->get() : collect();
        $stats = $this->getFairStats($fair);

        return view('job-fair.public', compact('fair', 'myRegistration', 'companies', 'stats'));
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
        $qrCode = $request->qr_code;
        $registration = JobFairRegistration::where('qr_code', $qrCode)
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
        return view('job-fair.admin.create', compact('companies'));
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
     * تعديل معرض
     */
    public function edit(JobFair $fair)
    {
        $companies = Company::orderBy('name')->get();
        $fair->load('companies');
        return view('job-fair.admin.edit', compact('fair', 'companies'));
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
        ]);

        $data = $request->except(['_token', '_method', 'companies']);

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('job-fair/banners', 'public');
        }

        $fair->update($data);

        return redirect()->route('job-fair.admin.show', $fair->id)
                         ->with('success', 'تم تحديث المعرض بنجاح.');
    }

    /**
     * تغيير حالة المعرض
     */
    public function updateStatus(Request $request, JobFair $fair)
    {
        $fair->update(['status' => $request->status]);
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
