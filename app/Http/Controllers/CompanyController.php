<?php
// ملف: app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Nomination;
use App\Models\JobOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\NotificationService;

class CompanyController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function dashboard()
    {
        $user = auth()->user();

        // التحقق من وجود شركة مرتبطة
        $company = $user->company;
        // أو طريقة أخرى للحصول على الشركة إذا كانت العلاقة مختلفة
        // مثلاً: $company = Company::where('user_id', $user->id)->first();
        if (!$company && $user->role === 'company') {
            $company = Company::where('user_id', $user->id)->first();
        }

        return view('company.dashboard', compact('company'));
    }

    public function index()
    {
        $companies = Company::latest()->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'password' => 'required|min:8|confirmed',
            'partnership_type' => 'required|in:employment,training,logistic_support,academic,training_employment',
            'partnership_status' => 'required|in:active,expired,under_review',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // إنشاء المستخدم أولاً
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'company',
        ]);

        // إنشاء الشركة
        $company = Company::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'industry' => $request->industry,
            'address' => $request->address,
            'description' => $request->description,
            'is_approved' => true, // الموافقة تلقائياً عند الإنشاء من قبل المدير
            'partnership_type' => $request->partnership_type,
            'partnership_status' => $request->partnership_status,
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('companies/logos', 'public');
            $company->logo_path = $path;
            $company->save();
        }

        // إرسال إشعارات
        try {
            // إشعار للشركة
            $this->notificationService->sendToUser(
                $user,
                'تم إنشاء حساب شركتك',
                'تم إنشاء حساب لشركتك بنجاح. يمكنك الآن الدخول وتحديث بياناتك.',
                'success'
            );

            // إشعار لمسؤول الشراكات
            $this->notificationService->sendToRole(
                'partnership_officer',
                'شركة جديدة: ' . $request->name,
                "تم إضافة شركة جديدة: {$request->name} ({$request->industry})",
                'info',
                ['model_type' => get_class($user), 'model_id' => $user->id]
            );

        } catch (\Exception $e) {
            \Log::error('Failed to send company notifications: ' . $e->getMessage());
        }

        return redirect()->route('admin.companies')
            ->with('success', 'تم إضافة الشركة بنجاح');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'partnership_type' => 'required|in:employment,training,logistic_support,academic,training_employment',
            'partnership_status' => 'required|in:active,expired,under_review',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name',
            'email',
            'phone',
            'industry',
            'address',
            'description',
            'partnership_type',
            'partnership_status',
            'partnership_notes',
            'partnership_start_date',
            'partnership_end_date',
            'contact_person',
            'contact_position',
            'contact_phone',
            'contact_email'
        ]);

        if ($request->has('partnership_status')) {
            $status = $request->partnership_status;
            $data['partnership_status'] = $status;
            $data['is_approved'] = ($status === 'active');
        } elseif ($request->has('is_approved')) {
            $isApproved = $request->boolean('is_approved');
            $data['is_approved'] = $isApproved;
            $data['partnership_status'] = $isApproved ? 'active' : 'under_review';
        }

        $company->update($data);

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo_path);
            }
            $path = $request->file('logo')->store('companies/logos', 'public');
            $company->logo_path = $path;
            $company->save();
        }

        // تحديث بيانات المستخدم المرتبط
        if ($company->user) {
            $company->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('admin.companies')
            ->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    /**
     * تبديل حالة اعتماد الشركة (معتمدة / قيد المراجعة)
     */
    public function toggleApproval($id)
    {
        $company = Company::findOrFail($id);
        $company->is_approved = !$company->is_approved;
        $company->partnership_status = $company->is_approved ? 'active' : 'under_review';
        $company->save();

        $message = $company->is_approved 
            ? "تم اعتماد وتفعيل شركة ({$company->name}) بنجاح." 
            : "تم إلغاء اعتماد شركة ({$company->name}) وتحويلها إلى قيد المراجعة.";

        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        // حذف المستخدم المرتبط أولاً
        if ($company->user) {
            $company->user->delete();
        }

        // ثم حذف الشركة
        $company->delete();

        return redirect()->route('admin.companies')
            ->with('success', 'تم حذف الشركة بنجاح');
    }

    public function profile()
    {
        $user = auth()->user();
        $company = Company::where('user_id', $user->id)->first();
        if (!$company) {
            return redirect()->route('company.dashboard')->with('error', 'الشركة غير موجودة');
        }
        return view('company.profile.edit', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $company = Company::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($company) {
            $company->update($request->only([
                'name',
                'phone',
                'industry',
                'address',
                'description'
            ]));

            if ($request->hasFile('logo')) {
                if ($company->logo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo_path);
                }
                $path = $request->file('logo')->store('companies/logos', 'public');
                $company->logo_path = $path;
                $company->save();
            }
        }

        $user->update([
            'name' => $request->name,
        ]);

        return redirect()->route('company.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    // ==========================================
    // مسارات التوظيف والمرشحين (Company ATS)
    // ==========================================

    public function nominations(Request $request)
    {
        $user = auth()->user();
        $company = Company::where('user_id', $user->id)->first();

        if (!$company) {
            return redirect()->route('company.dashboard')->with('error', 'يجب استكمال بيانات الشركة أولاً.');
        }

        // جلب الترشيحات الخاصة بوظائف هذه الشركة فقط
        $query = Nomination::with(['graduate', 'jobOpportunity'])
            ->whereHas('jobOpportunity', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            });

        // الفلاتر
        if ($request->has('opportunity_id') && $request->opportunity_id) {
            $query->where('job_opportunity_id', $request->opportunity_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('nominated_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('nominated_at', '<=', $request->to_date);
        }

        $nominations = $query->latest()->get();
        $opportunities = JobOpportunity::where('company_id', $company->id)->latest()->get();

        return view('company.nominations.index', compact('nominations', 'opportunities', 'company'));
    }

    public function showNomination($id)
    {
        $user = auth()->user();
        $company = Company::where('user_id', $user->id)->first();

        $nomination = Nomination::with(['graduate', 'jobOpportunity.company', 'nominator'])
            ->whereHas('jobOpportunity', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->findOrFail($id);

        return view('company.nominations.show', compact('nomination', 'company'));
    }

    public function updateNominationStatus(Request $request, $id)
    {
        $user = auth()->user();
        $company = Company::where('user_id', $user->id)->first();

        $nomination = Nomination::whereHas('jobOpportunity', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,under_review,interview_scheduled,accepted,rejected',
            'final_status' => 'nullable|string|in:hired,not_hired,in_progress',
            'interview_date' => 'nullable|date',
            'interview_time' => 'nullable|string|max:20',
            'interview_location' => 'nullable|string|max:255',
            'interview_notes' => 'nullable|string',
            'nomination_notes' => 'nullable|string',
        ]);

        $nomination->update([
            'status' => $request->status,
            'final_status' => $request->final_status,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'interview_location' => $request->interview_location,
            'interview_notes' => $request->interview_notes,
            'nomination_notes' => $request->nomination_notes,
            // Update timestamps based on status changes
            'company_response_at' => (in_array($request->status, ['accepted', 'rejected']) && !$nomination->company_response_at) ? now() : $nomination->company_response_at,
            'interview_at' => ($request->status === 'interview_scheduled' && !$nomination->interview_at) ? now() : $nomination->interview_at,
            'final_decision_at' => ($request->final_status && $request->final_status !== 'in_progress' && !$nomination->final_decision_at) ? now() : $nomination->final_decision_at,
        ]);

        // يمكن هنا إضافة إشعار للخريج بخصوص تغير حالته
        try {
            $this->notificationService->sendToUser(
                $nomination->graduate->user,
                'تحديث حالة طلب التوظيف',
                "قامت شركة {$company->name} بتحديث حالة طلبك للوظيفة: {$nomination->jobOpportunity->title}",
                'info',
                ['model_type' => get_class($nomination), 'model_id' => $nomination->id]
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send nomination status update notification: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'تم تحديث حالة المرشح بنجاح.');
    }
}
