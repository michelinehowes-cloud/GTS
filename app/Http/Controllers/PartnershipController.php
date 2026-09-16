<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\JobOpportunity;
use App\Models\PartnershipDocument;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnershipController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * عرض لوحة تحكم مسؤول الشراكات
     */
    public function dashboard()
    {
        $stats = [
            'totalCompanies' => Company::count(),
            'activePartnerships' => Company::where('partnership_status', 'active')->count(),
            'totalOpportunities' => JobOpportunity::count(),
            'openOpportunities' => JobOpportunity::where('status', 'open')->count(),
            'totalGraduates' => GraduateData::count(),
            'activeNominations' => Nomination::active()->count(),
            'totalDocuments' => PartnershipDocument::count(),
            'expiringDocuments' => PartnershipDocument::where('expiry_date', '<=', now()->addDays(30))->count(),
        ];

        $recentOpportunities = JobOpportunity::with('company')
            ->latest()
            ->take(5)
            ->get();

        $recentDocuments = PartnershipDocument::with('company')
            ->latest()
            ->take(5)
            ->get();

        return view('partnership.dashboard', compact('stats', 'recentOpportunities', 'recentDocuments'));
    }

    /**
     * عرض قائمة الشركات الشريكة
     */
    public function companies(Request $request)
    {
        $query = Company::with(['user', 'jobOpportunities'])
            ->withCount(['jobOpportunities', 'partnershipDocuments']);

        if ($request->filled('partnership_status')) {
            $query->where('partnership_status', $request->partnership_status);
        }

        if ($request->has('approval_status')) {
            if ($request->approval_status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->approval_status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('partnership_type')) {
            $type = $request->partnership_type;
            $query->where(function($q) use ($type) {
                $q->where('partnership_type', $type)
                  ->orWhereJsonContains('partnership_types', $type);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $companies = $query->latest()->get();
        $pendingCount = Company::where('is_approved', false)->count();

        return view('partnership.companies.index', compact('companies', 'pendingCount'));
    }

    /**
     * عرض تفاصيل شركة
     */
    public function showCompany($id)
    {
        $company = Company::with([
            'user', 
            'jobOpportunities' => function($query) {
                $query->latest();
            },
            'partnershipDocuments' => function($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        return view('partnership.companies.show', compact('company'));
    }

    /**
     * عرض قائمة وثائق الشراكة
     */
    public function documents()
    {
        $stats = [
            'total' => PartnershipDocument::count(),
            'active' => PartnershipDocument::where('document_status', 'active')->count(),
            'expired' => PartnershipDocument::where('document_status', 'expired')->count(),
            'companies_count' => PartnershipDocument::distinct('company_id')->count('company_id'),
        ];
        $documents = PartnershipDocument::with('company')->latest()->paginate(15);
        return view('partnership.documents.index', compact('documents', 'stats'));
    }

    /**
     * عرض نموذج إضافة وثيقة شراكة جديدة
     */
    public function createDocument()
    {
        $companies = Company::all(); // Fetch all companies to associate with the document
        return view('partnership.documents.create', compact('companies'));
    }

    /**
     * حفظ وثيقة شراكة جديدة
     */
    public function storeDocument(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:mou,contract,agreement,amendment,renewal,termination,other',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB Max
            'document_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('document_file');
        $filePath = $file->store('partnership-documents', 'public');

        PartnershipDocument::create([
            'company_id' => $request->company_id,
            'uploaded_by' => Auth::id(),
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'document_date' => $request->document_date,
            'effective_date' => $request->effective_date,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->route('partnership.documents')->with('success', 'تم إضافة الوثيقة بنجاح.');
    }

    /**
     * عرض أو معاينة وثيقة شراكة
     */
    public function showDocument($id)
    {
        $document = PartnershipDocument::findOrFail($id);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $filePath = storage_path('app/public/' . $document->file_path);
            $fileName = $document->file_name ?? basename($document->file_path);
            return response()->file($filePath, [
                'Content-Disposition' => 'inline; filename="' . $fileName . '"'
            ]);
        }

        return redirect()->route('partnership.documents')->with('error', 'ملف الوثيقة غير متوفر حالياً على الخادم.');
    }

    /**
     * تنزيل وثيقة شراكة
     */
    public function downloadDocument($id)
    {
        $document = PartnershipDocument::findOrFail($id);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            $filePath = storage_path('app/public/' . $document->file_path);
            $fileName = $document->file_name ?? basename($document->file_path);
            return response()->download($filePath, $fileName);
        }

        return redirect()->route('partnership.documents')->with('error', 'ملف الوثيقة غير متوفر حالياً على الخادم.');
    }

    /**
     * عرض نموذج تعديل وثيقة شراكة
     */
    public function editDocument(PartnershipDocument $document)
    {
        $companies = Company::all();
        return view('partnership.documents.edit', compact('document', 'companies'));
    }

    /**
     * تحديث وثيقة شراكة
     */
    public function updateDocument(Request $request, PartnershipDocument $document)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:mou,contract,agreement,amendment,renewal,termination,other',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // Optional file re-upload
            'document_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('document_file');

        if ($request->hasFile('document_file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file_path);

            // Upload new file
            $file = $request->file('document_file');
            $filePath = $file->store('partnership-documents', 'public');
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $document->update($data);

        return redirect()->route('partnership.documents')->with('success', 'تم تحديث الوثيقة بنجاح.');
    }



    /**
     * رفع وثيقة شراكة
     */
    public function uploadDocument(Request $request, $companyId)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:mou,contract,agreement,amendment,renewal,termination,other',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'description' => 'nullable|string',
        ]);

        $company = Company::findOrFail($companyId);
        $file = $request->file('document_file');

        // حفظ الملف
        $filePath = $file->store('partnership-documents', 'public');

        PartnershipDocument::create([
            'company_id' => $companyId,
            'uploaded_by' => Auth::id(),
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'document_date' => $request->document_date,
            'effective_date' => $request->effective_date,
            'expiry_date' => $request->expiry_date,
        ]);

        return redirect()->back()->with('success', 'تم رفع الوثيقة بنجاح');
    }

    /**
     * حذف وثيقة شراكة
     */
    public function deleteDocument($id)
    {
        $document = PartnershipDocument::findOrFail($id);

        // حذف الملف من التخزين
        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        return redirect()->back()->with('success', 'تم حذف الوثيقة بنجاح');
    }

    /**
     * استيراد بيانات الخريجين من Excel
     */
    public function importGraduates(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        // هنا سيتم معالجة ملف Excel
        // يمكن استخدام package مثل Maatwebsite/Laravel-Excel

        return redirect()->back()->with('success', 'تم استيراد بيانات الخريجين بنجاح');
    }

    /**
     * عرض تقارير الشراكات
     */
    public function reports()
    {
        $partnershipStats = [
            'byType' => Company::selectRaw('partnership_type, count(*) as count')
                ->whereNotNull('partnership_type')
                ->groupBy('partnership_type')
                ->get(),
            'byStatus' => Company::selectRaw('partnership_status, count(*) as count')
                ->whereNotNull('partnership_status')
                ->groupBy('partnership_status')
                ->get(),
            'byIndustry' => Company::selectRaw('industry, count(*) as count')
                ->whereNotNull('industry')
                ->groupBy('industry')
                ->orderByDesc('count')
                ->take(6)
                ->get(),
        ];

        $opportunityStats = [
            'byType' => JobOpportunity::selectRaw('type, count(*) as count')
                ->whereNotNull('type')
                ->groupBy('type')
                ->get(),
            'byStatus' => JobOpportunity::selectRaw('status, count(*) as count')
                ->whereNotNull('status')
                ->groupBy('status')
                ->get(),
        ];

        $counts = [
            'totalCompanies' => Company::count(),
            'activePartnerships' => Company::where('partnership_status', 'active')->count(),
            'totalOpportunities' => JobOpportunity::count(),
            'openOpportunities' => JobOpportunity::where('status', 'open')->count(),
            'totalDocuments' => PartnershipDocument::count(),
            'approvedCompanies' => Company::where('is_approved', true)->count(),
        ];

        $recentCompanies = Company::latest()->take(6)->get();

        return view('partnership.reports', compact('partnershipStats', 'opportunityStats', 'counts', 'recentCompanies'));
    }

    /**
     * عرض نموذج إضافة شركة جديدة
     */
    public function createCompany()
    {
        return view('partnership.companies.create');
    }

    /**
     * حفظ شركة جديدة
     */
    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'partnership_types' => 'nullable|array|min:1',
            'partnership_types.*' => 'in:employment,training,logistic_support,academic,workshops,training_employment',
            'partnership_type' => 'nullable|string',
            'partnership_status' => 'required|in:active,expired,under_review',
            'partnership_start_date' => 'nullable|date',
            'partnership_end_date' => 'nullable|date|after_or_equal:partnership_start_date',
            'partnership_notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $types = $request->input('partnership_types', []);
        if (empty($types) && $request->filled('partnership_type')) {
            $types = [$request->input('partnership_type')];
        }
        $primaryType = !empty($types) ? $types[0] : ($request->input('partnership_type') ?: 'employment');

        // إنشاء المستخدم إذا تم إدخال كلمة مرور أو إنشاء مستخدم تلقائي
        $user = null;
        if ($request->filled('password')) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'company',
                'is_approved' => ($request->partnership_status === 'active'),
                'is_active' => ($request->partnership_status === 'active'),
            ]);
        }

        $company = Company::create([
            'user_id' => $user ? $user->id : null,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'industry' => $request->industry,
            'address' => $request->address,
            'city' => $request->city ?? 'طرابلس',
            'website' => $request->website,
            'description' => $request->description,
            'contact_person' => $request->contact_person,
            'contact_position' => $request->contact_position,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'is_approved' => ($request->partnership_status === 'active'),
            'partnership_type' => $primaryType,
            'partnership_types' => $types,
            'partnership_status' => $request->partnership_status,
            'partnership_start_date' => $request->partnership_start_date,
            'partnership_end_date' => $request->partnership_end_date,
            'partnership_notes' => $request->partnership_notes,
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('companies/logos', 'public');
            $company->logo_path = $path;
            $company->save();
        }

        // تسجيل في سجل الرقابة
        try {
            AuditLog::logAction(
                'COMPANY_CREATED',
                'Company',
                $company->id,
                null,
                ['name' => $company->name, 'email' => $company->email, 'industry' => $company->industry]
            );
        } catch (\Throwable $e) {
            //
        }

        return redirect()->route('partnership.companies')
            ->with('success', 'تم إضافة الشركة بنجاح');
    }

    /**
     * عرض نموذج تعديل الشركة
     */
    public function editCompany($company)
    {
        $company = $company instanceof Company ? $company : Company::findOrFail($company);
        return view('partnership.companies.edit', compact('company'));
    }

    /**
     * تحديث بيانات الشركة
     */
    public function updateCompany(Request $request, $company)
    {
        $company = $company instanceof Company ? $company : Company::findOrFail($company);
        $oldData = $company->only(['name', 'email', 'phone', 'industry', 'partnership_status', 'is_approved']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'partnership_types' => 'nullable|array|min:1',
            'partnership_types.*' => 'in:employment,training,logistic_support,academic,workshops,training_employment',
            'partnership_type' => 'nullable|string',
            'partnership_status' => 'required|in:active,expired,under_review',
            'partnership_start_date' => 'nullable|date',
            'partnership_end_date' => 'nullable|date',
            'partnership_notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $types = $request->input('partnership_types', []);
        if (empty($types) && $request->filled('partnership_type')) {
            $types = [$request->input('partnership_type')];
        }
        $primaryType = !empty($types) ? $types[0] : ($request->input('partnership_type') ?: 'employment');

        $data = $request->only([
            'name',
            'email',
            'phone',
            'industry',
            'address',
            'city',
            'website',
            'description',
            'partnership_status',
            'partnership_notes',
            'partnership_start_date',
            'partnership_end_date',
            'contact_person',
            'contact_position',
            'contact_phone',
            'contact_email'
        ]);

        $data['partnership_types'] = $types;
        $data['partnership_type'] = $primaryType;
        $data['is_approved'] = ($request->partnership_status === 'active');

        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($data);

        // مزامنة مع حساب المستخدم إن وجد
        if ($company->user) {
            $company->user->name = $company->name;
            $company->user->email = $company->email;
            $company->user->is_approved = $company->is_approved;
            $company->user->is_active = $company->is_approved;
            $company->user->save();
        }

        try {
            AuditLog::logAction(
                'COMPANY_UPDATED',
                'Company',
                $company->id,
                $oldData,
                $company->only(['name', 'email', 'phone', 'industry', 'partnership_status', 'is_approved'])
            );
        } catch (\Throwable $e) {
            //
        }

        return redirect()->route('partnership.companies')
            ->with('success', 'تم تحديث بيانات الشركة بنجاح');
    }

    /**
     * حذف شركة
     */
    public function destroyCompany(Company $company)
    {
        $company->delete();
        return redirect()->route('partnership.companies')->with('success', 'تم حذف الشركة بنجاح');
    }

    /**
     * تبديل اعتماد الشركة
     */
    public function toggleApproval($id)
    {
        $company = Company::findOrFail($id);
        $company->is_approved = !$company->is_approved;
        if ($company->is_approved && $company->partnership_status === 'under_review') {
            $company->partnership_status = 'active';
        }
        $company->save();

        if ($company->user) {
            $company->user->is_approved = $company->is_approved;
            $company->user->is_active = $company->is_approved;
            $company->user->save();
        }

        if ($company->is_approved) {
            try {
                $this->notificationService->notifyCompanyApproved($company);
            } catch (\Exception $e) {
                //
            }
        }

        $msg = $company->is_approved ? 'تم اعتماد وتفعيل الشركة بنجاح' : 'تم إلغاء اعتماد الشركة';
        return redirect()->back()->with('success', $msg);
    }

    /**
     * اعتماد شركة رسمياً
     */
    public function approveCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->is_approved = true;
        $company->partnership_status = 'active';
        $company->rejection_notes = null;
        $company->save();

        if ($company->user) {
            $company->user->is_approved = true;
            $company->user->is_active = true;
            $company->user->save();
        }

        try {
            $this->notificationService->notifyCompanyApproved($company);
        } catch (\Exception $e) {
            \Log::error('فشل إرسال إشعار اعتماد الشركة: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "تم اعتماد وتفعيل حساب شركة ({$company->name}) بنجاح وإشعارها.");
    }

    /**
     * رفض طلب تسجيل شركة
     */
    public function rejectCompany(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->is_approved = false;
        $company->partnership_status = 'under_review';
        $company->rejection_notes = $request->input('rejection_notes');
        $company->save();

        if ($company->user) {
            $company->user->is_approved = false;
            $company->user->is_active = false;
            $company->user->save();
        }

        try {
            $this->notificationService->notifyCompanyRejected($company, $company->rejection_notes);
        } catch (\Exception $e) {
            \Log::error('فشل إرسال إشعار رفض الشركة: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "تم رفض طلب تسجيل شركة ({$company->name}) وإشعارها بالسبب.");
    }
}
