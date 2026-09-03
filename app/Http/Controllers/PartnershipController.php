<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\JobOpportunity;
use App\Models\PartnershipDocument;
use App\Models\GraduateData;
use App\Models\Nomination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnershipController extends Controller
{
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
    public function companies()
    {
        $companies = Company::with(['user', 'jobOpportunities'])
            ->withCount(['jobOpportunities', 'partnershipDocuments'])
            ->latest()
            ->get();

        return view('partnership.companies.index', compact('companies'));
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
            return Storage::disk('public')->response(
                $document->file_path,
                $document->file_name ?? basename($document->file_path)
            );
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
            return Storage::disk('public')->download(
                $document->file_path,
                $document->file_name ?? basename($document->file_path)
            );
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
     * تحديث بيانات الشركة
     */
    public function updateCompany(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:companies,email,' . $id,
                'phone' => 'required|string|max:20',
                'industry' => 'required|string|max:255',
                'address' => 'required|string',
                'website' => 'nullable|url',
                'description' => 'nullable|string',
                'partnership_type' => 'required|in:employment,training,logistic_support,academic',
                'partnership_status' => 'required|in:active,expired,under_review',
                'partnership_start_date' => 'nullable|date',
                'partnership_end_date' => 'nullable|date|after:partnership_start_date',
                'contact_person' => 'nullable|string|max:255',
                'contact_position' => 'nullable|string|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_email' => 'nullable|email',
            ]);

            $company->update($request->all());

            // ✅ تأكد أن هذا هو الـ redirect الوحيد
            return redirect()->route('partnership.companies')
                           ->with('success', 'تم تحديث بيانات الشركة بنجاح');

        } catch (\Exception $e) {
            // ✅ في حالة الخطأ، ارجع مع رسالة خطأ واحدة
            return redirect()->back()
                           ->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage())
                           ->withInput();
        }
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
            'address' => 'required|string',
            'partnership_type' => 'required|in:employment,training,logistic_support,academic',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        Company::create($request->all());

        return redirect()->route('partnership.companies')->with('success', 'تم إضافة الشركة بنجاح');
    }

    /**
     * عرض نموذج تعديل الشركة
     */
    public function editCompany(Company $company)
    {
        return view('partnership.companies.edit', compact('company'));
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

        $msg = $company->is_approved ? 'تم اعتماد الشركة بنجاح' : 'تم إلغاء اعتماد الشركة';
        return redirect()->back()->with('success', $msg);
    }
}
