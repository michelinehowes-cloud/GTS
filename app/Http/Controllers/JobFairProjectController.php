<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobFairProjectController extends Controller
{
    /**
     * المعرض العام لمشاريع التخرج والبحث والفلترة
     * تظهر المشاريع المعتمدة والمنشورة فقط للجمهور
     */
    public function publicIndex(Request $request, $fair = null)
    {
        if ($fair) {
            if (!($fair instanceof JobFair)) {
                $fair = JobFair::find($fair);
            }
        } elseif ($request->has('fair')) {
            $fair = JobFair::find($request->query('fair'));
        }

        if (!$fair) {
            $fair = JobFair::where('status', 'published')
                           ->orderBy('event_date', 'asc')
                           ->first();

            if (!$fair) {
                $fair = JobFair::where('status', 'ongoing')->first() ?? JobFair::first();
            }
        }

        // عرض المشاريع المنشورة فقط للعامة
        $query = JobFairProject::where('status', 'published');

        // ربط بالمعرض المحدد أو أرشيف سنة معينة
        if ($request->filled('archive_fair')) {
            $query->where('job_fair_id', $request->archive_fair);
        } elseif ($fair) {
            $query->where('job_fair_id', $fair->id);
        }

        // الفلاتر
        if ($request->filled('faculty')) {
            $query->where('faculty', $request->faculty);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('year')) {
            $query->where('graduation_year', $request->year);
        }

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->filled('main_category')) {
            $query->where('main_category', $request->main_category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('summary', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('problem_statement', 'like', "%{$s}%")
                  ->orWhere('solution_statement', 'like', "%{$s}%")
                  ->orWhere('supervisor_name', 'like', "%{$s}%")
                  ->orWhere('faculty', 'like', "%{$s}%")
                  ->orWhere('department', 'like', "%{$s}%")
                  ->orWhere('team_members', 'like', "%{$s}%");
            });
        }

        $projects = $query->orderByDesc('is_featured')
                          ->orderByDesc('created_at')
                          ->get();

        // استخراج خيارات الفلترة المتاحة للمشاريع المنشورة
        $baseQuery = $fair 
            ? JobFairProject::where('job_fair_id', $fair->id)->where('status', 'published') 
            : JobFairProject::where('status', 'published');

        $faculties = (clone $baseQuery)->distinct()->pluck('faculty')->filter()->values();
        $departments = (clone $baseQuery)->distinct()->pluck('department')->filter()->values();
        $years = (clone $baseQuery)->distinct()->orderByDesc('graduation_year')->pluck('graduation_year')->filter()->values();
        $projectTypes = (clone $baseQuery)->distinct()->pluck('project_type')->filter()->values();
        $categories = (clone $baseQuery)->distinct()->pluck('main_category')->filter()->values();
        $allFairs = JobFair::orderByDesc('event_date')->select('id', 'title', 'event_date')->get();

        // إحصائيات المعرض
        $totalProjects = $projects->count();
        $totalFaculties = $projects->pluck('faculty')->unique()->count();
        $totalStudents = $projects->reduce(function($carry, $proj) {
            return $carry + count($proj->team_list);
        }, 0);
        $featuredCount = $projects->where('is_featured', true)->count();

        return view('job-fair.projects.index', compact(
            'fair',
            'projects',
            'faculties',
            'departments',
            'years',
            'projectTypes',
            'categories',
            'allFairs',
            'totalProjects',
            'totalFaculties',
            'totalStudents',
            'featuredCount'
        ));
    }

    /**
     * الصفحة المستقلة للمشروع مع البوستر ورمز QR وتفاصيل الفريق
     * (تظهر فقط الـ 22 حقلاً العامة ولا تُظهر أياً من الحقول السرية للمسؤول)
     */
    public function publicShow(Request $request, $project)
    {
        if (!($project instanceof JobFairProject)) {
            $project = JobFairProject::with('jobFair')->findOrFail($project);
        } else {
            $project->load('jobFair');
        }

        // إذا كان المشروع غير منشور، يُسمح بالمعاينة فقط للمسؤولين أو صاحب المشروع
        if ($project->status !== 'published') {
            $canPreview = auth()->check() && (
                auth()->user()->role === 'admin' ||
                auth()->user()->role === 'super_admin' ||
                (method_exists(auth()->user(), 'canManageJobFair') && auth()->user()->canManageJobFair()) ||
                $project->user_id === auth()->id()
            );

            if (!$canPreview) {
                return redirect()->route('job-fair.public.projects.index')
                    ->with('info', 'المشروع قيد المراجعة الإدارية والاعتماد أو غير متاح للعرض العام حالياً.');
            }
        }

        // زيادة عداد المشاهدات بدون تحديث timestamps
        $project->timestamps = false;
        $project->increment('views_count');
        $project->timestamps = true;

        $fair = $project->jobFair;

        // مشاريع مقترحة ذات صلة من المشاريع المنشورة فقط
        $relatedProjects = JobFairProject::where('id', '!=', $project->id)
            ->where('status', 'published')
            ->where(function($q) use ($project) {
                $q->where('faculty', $project->faculty)
                  ->orWhere('job_fair_id', $project->job_fair_id);
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('job-fair.projects.show', compact('project', 'fair', 'relatedProjects'));
    }

    /**
     * نموذج تقديم مشروع تخرج من قبل الخريجين بأنفسهم
     */
    public function createSubmission(Request $request, $fair = null)
    {
        if ($fair) {
            if (!($fair instanceof JobFair)) {
                $fair = JobFair::find($fair);
            }
        } elseif ($request->has('fair')) {
            $fair = JobFair::find($request->query('fair'));
        }

        if (!$fair) {
            $fair = JobFair::where('status', 'published')->orderBy('event_date', 'asc')->first()
                 ?? JobFair::where('status', 'ongoing')->first()
                 ?? JobFair::first();
        }

        $allFairs = JobFair::whereIn('status', ['published', 'ongoing'])
                           ->orderByDesc('event_date')
                           ->get();

        // التعبئة التلقائية إذا كان الخريج مسجلاً دخوله
        $prefill = [];
        if (auth()->check()) {
            $user = auth()->user();
            $graduate = $user->graduateProfile ?? null;
            $prefill = [
                'name'                  => $user->name,
                'contact_email'         => $user->email,
                'whatsapp_phone'        => $user->phone ?? ($graduate?->phone ?? ''),
                'student_university_id' => $user->university_id ?? ($graduate?->student_id ?? ($graduate?->national_id ?? '')),
                'faculty'               => $user->faculty ?? ($graduate?->faculty ?? ''),
                'department'            => $user->department ?? ($graduate?->specialization ?? ''),
                'graduation_year'       => $graduate?->graduation_year ?? date('Y'),
            ];
        }

        return view('job-fair.projects.submit', compact('fair', 'allFairs', 'prefill'));
    }

    /**
     * حفظ طلب تقديم مشروع التخرج المرسل من الخريج
     */
    public function storeSubmission(Request $request)
    {
        $validated = $request->validate([
            'job_fair_id'              => 'required|exists:job_fairs,id',
            'title'                    => 'required|string|max:255',
            'faculty'                  => 'required|string|max:255',
            'department'               => 'required|string|max:255',
            'graduation_year'          => 'required|integer|min:2000|max:2035',
            'academic_year'            => 'nullable|string|max:50',
            'project_type'             => 'required|string|max:100',
            'main_category'            => 'required|string|max:100',
            'supervisor_name'          => 'nullable|string|max:255',
            'supervisor_title'         => 'nullable|string|max:255',
            'description'              => 'required|string',
            'summary'                  => 'required|string',
            'problem_statement'        => 'nullable|string',
            'solution_statement'       => 'nullable|string',
            'objectives'               => 'nullable|string',
            'technical_specifications' => 'nullable|string',
            'key_outcomes'             => 'nullable|string',
            'market_viability'         => 'nullable|string',
            'project_url'              => 'nullable|url|max:255',
            'video_url'                => 'nullable|url|max:255',
            'contact_email'            => 'required|email|max:255',
            'poster_image'             => 'nullable|image|max:10240',
            'cover_image'              => 'nullable|image|max:10240',
            // الحقول الخاصة بالمسؤول فقط
            'student_university_id'    => 'required|string|max:100',
            'whatsapp_phone'           => 'required|string|max:50',
            'project_requirements'     => 'nullable|string',
            'needs_special_equipment'  => 'nullable|boolean',
            'special_equipment_details'=> 'nullable|string',
            'additional_requirements'  => 'nullable|string',
            'executive_summary'        => 'nullable|string',
            'prototype_status'         => 'nullable|string|max:100',
        ]);

        // معالجة أعضاء الفريق
        $teamMembers = [];
        if ($request->filled('team_members_raw')) {
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $request->team_members_raw)));
            foreach ($lines as $line) {
                $teamMembers[] = ['name' => $line];
            }
        } elseif ($request->has('team_names') && is_array($request->team_names)) {
            foreach ($request->team_names as $i => $name) {
                if (!empty(trim($name))) {
                    $teamMembers[] = [
                        'name'  => trim($name),
                        'role'  => $request->team_roles[$i] ?? null,
                        'email' => $request->team_emails[$i] ?? null,
                        'phone' => $request->team_phones[$i] ?? null,
                    ];
                }
            }
        }
        $validated['team_members'] = $teamMembers;

        if ($request->hasFile('poster_image')) {
            $validated['poster_image'] = $request->file('poster_image')->store('projects/posters', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $validated['status'] = 'pending'; // يبدأ دائمًا بحالة قيد المراجعة الإدارية
        $validated['is_featured'] = false;
        $validated['user_id'] = auth()->id();
        $validated['needs_special_equipment'] = $request->boolean('needs_special_equipment');

        $project = JobFairProject::create($validated);

        return redirect()->route('job-fair.public.projects.submitted', $project->id)
            ->with('success', 'تم استلام بيانات مشروع التخرج بنجاح وهو الآن قيد المراجعة والاعتماد من قبل إدارة المعرض.');
    }

    /**
     * صفحة إشعار نجاح تقديم المشروع للخريج
     */
    public function submissionSuccess($id)
    {
        $project = JobFairProject::with('jobFair')->findOrFail($id);
        $fair = $project->jobFair;

        return view('job-fair.projects.submitted', compact('project', 'fair'));
    }

    /**
     * لوحة تحكم المشرفين لإدارة مشاريع المعرض
     */
    public function adminIndex(Request $request, JobFair $fair)
    {
        $statusFilter = $request->query('status', 'all');

        $allProjects = $fair->projects()
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'     => $allProjects->count(),
            'pending'   => $allProjects->where('status', 'pending')->count(),
            'published' => $allProjects->where('status', 'published')->count(),
            'rejected'  => $allProjects->where('status', 'rejected')->count(),
            'draft'     => $allProjects->where('status', 'draft')->count(),
            'faculties' => $allProjects->pluck('faculty')->unique()->filter()->count(),
            'featured'  => $allProjects->where('is_featured', true)->count(),
            'views'     => $allProjects->sum('views_count'),
        ];

        // الفلترة حسب الحالة
        $filtered = match($statusFilter) {
            'pending'   => $allProjects->where('status', 'pending'),
            'published' => $allProjects->where('status', 'published'),
            'rejected'  => $allProjects->where('status', 'rejected'),
            'draft'     => $allProjects->where('status', 'draft'),
            default     => $allProjects,
        };

        // الفلترة الإضافية (بحث، كلية، قسم)
        if ($request->filled('faculty')) {
            $filtered = $filtered->where('faculty', $request->faculty);
        }

        if ($request->filled('department')) {
            $filtered = $filtered->where('department', $request->department);
        }

        if ($request->filled('search')) {
            $s = mb_strtolower(trim($request->search));
            $filtered = $filtered->filter(function($p) use ($s) {
                return str_contains(mb_strtolower($p->title ?? ''), $s)
                    || str_contains(mb_strtolower($p->summary ?? ''), $s)
                    || str_contains(mb_strtolower($p->supervisor_name ?? ''), $s)
                    || str_contains(mb_strtolower($p->student_university_id ?? ''), $s)
                    || str_contains(mb_strtolower($p->whatsapp_phone ?? ''), $s)
                    || str_contains(mb_strtolower(json_encode($p->team_members) ?? ''), $s);
            });
        }

        $projects = $filtered;
        $faculties = $allProjects->pluck('faculty')->unique()->filter()->values();
        $departments = $allProjects->pluck('department')->unique()->filter()->values();

        return view('job-fair.admin.projects', compact('fair', 'projects', 'stats', 'statusFilter', 'allProjects', 'faculties', 'departments'));
    }

    /**
     * اعتماد / قبول أو رفض سريع للمشروع من لوحة الإدارة
     */
    public function updateStatus(Request $request, JobFairProject $project)
    {
        $validated = $request->validate([
            'status'           => 'required|string|in:published,pending,rejected,draft,archived',
            'booth_number'     => 'nullable|string|max:50',
            'is_featured'      => 'nullable|boolean',
            'admin_notes'      => 'nullable|string',
            'rejection_reason' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($request->has('booth_number')) {
            $updateData['booth_number'] = $validated['booth_number'];
        }
        if ($request->has('is_featured')) {
            $updateData['is_featured'] = $request->boolean('is_featured');
        }
        if ($request->has('admin_notes')) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        if ($request->has('rejection_reason')) {
            $updateData['rejection_reason'] = $validated['rejection_reason'];
        }

        $project->update($updateData);

        $msg = match($validated['status']) {
            'published' => 'تمت الموافقة على المشروع بنجاح وإدراجه رسمياً في المعرض الرقمي للجمهور.',
            'rejected'  => 'تم رفض المشروع وتسجيل سبب الرفض.',
            'pending'   => 'تمت إعادة المشروع إلى قائمة الانتظار والمراجعة.',
            default     => 'تم تحديث حالة المشروع بنجاح.',
        };

        return back()->with('success', $msg);
    }

    /**
     * حفظ مشروع تخرج جديد من لوحة الإدارة
     */
    public function store(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'faculty'                  => 'required|string|max:255',
            'department'               => 'required|string|max:255',
            'graduation_year'          => 'required|integer|min:2000|max:2035',
            'academic_year'            => 'nullable|string|max:50',
            'project_type'             => 'nullable|string|max:100',
            'main_category'            => 'nullable|string|max:100',
            'supervisor_name'          => 'nullable|string|max:255',
            'supervisor_title'         => 'nullable|string|max:255',
            'summary'                  => 'nullable|string',
            'problem_statement'        => 'nullable|string',
            'solution_statement'       => 'nullable|string',
            'objectives'               => 'nullable|string',
            'description'              => 'nullable|string',
            'technical_specifications' => 'nullable|string',
            'key_outcomes'             => 'nullable|string',
            'market_viability'         => 'nullable|string',
            'booth_number'             => 'nullable|string|max:50',
            'project_url'              => 'nullable|url|max:255',
            'video_url'                => 'nullable|url|max:255',
            'contact_email'            => 'nullable|email|max:255',
            'status'                   => 'required|string|in:published,pending,rejected,draft,archived',
            'poster_image'             => 'nullable|image|max:10240',
            'cover_image'              => 'nullable|image|max:10240',
            // حقول الإدارة
            'student_university_id'    => 'nullable|string|max:100',
            'whatsapp_phone'           => 'nullable|string|max:50',
            'project_requirements'     => 'nullable|string',
            'needs_special_equipment'  => 'nullable|boolean',
            'special_equipment_details'=> 'nullable|string',
            'additional_requirements'  => 'nullable|string',
            'executive_summary'        => 'nullable|string',
            'prototype_status'         => 'nullable|string|max:100',
            'admin_notes'              => 'nullable|string',
            'rejection_reason'         => 'nullable|string',
        ]);

        // معالجة أعضاء الفريق من النموذج
        $teamMembers = [];
        if ($request->filled('team_members_raw')) {
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $request->team_members_raw)));
            foreach ($lines as $line) {
                $teamMembers[] = ['name' => $line];
            }
        } elseif ($request->has('team_names') && is_array($request->team_names)) {
            foreach ($request->team_names as $i => $name) {
                if (!empty(trim($name))) {
                    $teamMembers[] = [
                        'name'     => trim($name),
                        'role'     => $request->team_roles[$i] ?? null,
                        'email'    => $request->team_emails[$i] ?? null,
                        'phone'    => $request->team_phones[$i] ?? null,
                        'linkedin' => $request->team_linkedins[$i] ?? null,
                    ];
                }
            }
        }
        $validated['team_members'] = $teamMembers;

        if ($request->hasFile('poster_image')) {
            $validated['poster_image'] = $request->file('poster_image')->store('projects/posters', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['needs_special_equipment'] = $request->boolean('needs_special_equipment');

        $fair->projects()->create($validated);

        return back()->with('success', 'تمت إضافة مشروع التخرج بنجاح إلى المعرض والأرشيف.');
    }

    /**
     * تحديث بيانات مشروع تخرج من لوحة الإدارة
     */
    public function update(Request $request, JobFairProject $project)
    {
        $validated = $request->validate([
            'title'                    => 'required|string|max:255',
            'faculty'                  => 'required|string|max:255',
            'department'               => 'required|string|max:255',
            'graduation_year'          => 'required|integer|min:2000|max:2035',
            'academic_year'            => 'nullable|string|max:50',
            'project_type'             => 'nullable|string|max:100',
            'main_category'            => 'nullable|string|max:100',
            'supervisor_name'          => 'nullable|string|max:255',
            'supervisor_title'         => 'nullable|string|max:255',
            'summary'                  => 'nullable|string',
            'problem_statement'        => 'nullable|string',
            'solution_statement'       => 'nullable|string',
            'objectives'               => 'nullable|string',
            'description'              => 'nullable|string',
            'technical_specifications' => 'nullable|string',
            'key_outcomes'             => 'nullable|string',
            'market_viability'         => 'nullable|string',
            'booth_number'             => 'nullable|string|max:50',
            'project_url'              => 'nullable|url|max:255',
            'video_url'                => 'nullable|url|max:255',
            'contact_email'            => 'nullable|email|max:255',
            'status'                   => 'required|string|in:published,pending,rejected,draft,archived',
            'poster_image'             => 'nullable|image|max:10240',
            'cover_image'              => 'nullable|image|max:10240',
            // حقول الإدارة
            'student_university_id'    => 'nullable|string|max:100',
            'whatsapp_phone'           => 'nullable|string|max:50',
            'project_requirements'     => 'nullable|string',
            'needs_special_equipment'  => 'nullable|boolean',
            'special_equipment_details'=> 'nullable|string',
            'additional_requirements'  => 'nullable|string',
            'executive_summary'        => 'nullable|string',
            'prototype_status'         => 'nullable|string|max:100',
            'admin_notes'              => 'nullable|string',
            'rejection_reason'         => 'nullable|string',
        ]);

        if ($request->filled('team_members_raw')) {
            $teamMembers = [];
            $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $request->team_members_raw)));
            foreach ($lines as $line) {
                $teamMembers[] = ['name' => $line];
            }
            $validated['team_members'] = $teamMembers;
        }

        if ($request->hasFile('poster_image')) {
            if ($project->poster_image && Storage::disk('public')->exists($project->poster_image)) {
                Storage::disk('public')->delete($project->poster_image);
            }
            $validated['poster_image'] = $request->file('poster_image')->store('projects/posters', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image && Storage::disk('public')->exists($project->cover_image)) {
                Storage::disk('public')->delete($project->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['needs_special_equipment'] = $request->boolean('needs_special_equipment');

        $project->update($validated);

        return back()->with('success', 'تم تحديث بيانات مشروع التخرج بنجاح.');
    }

    /**
     * حذف مشروع تخرج
     */
    public function destroy(JobFairProject $project)
    {
        if ($project->poster_image && Storage::disk('public')->exists($project->poster_image)) {
            Storage::disk('public')->delete($project->poster_image);
        }
        if ($project->cover_image && Storage::disk('public')->exists($project->cover_image)) {
            Storage::disk('public')->delete($project->cover_image);
        }

        $project->delete();

        return back()->with('success', 'تم حذف مشروع التخرج بنجاح.');
    }

    /**
     * تصدير دليل مشاريع التخرج كملف CSV مهيأ لـ Excel مع دعم كامل للغة العربية
     */
    public function export(JobFair $fair): StreamedResponse
    {
        $projects = $fair->projects()->orderBy('faculty')->orderBy('department')->get();
        $fileName = 'projects_catalog_' . Str::slug($fair->title) . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($projects, $fair) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                '#',
                'عنوان المشروع',
                'الكلية',
                'القسم / التخصص',
                'سنة التخرج',
                'نوع المشروع',
                'المجال الرئيسي',
                'المشرف الأكاديمي',
                'اللقب العلمي',
                'أسماء أعضاء الفريق',
                'البريد الإلكتروني للتواصل',
                'الرقم الجامعي للممثل',
                'رقم الواتساب',
                'رقم الجناح',
                'حالة النموذج الأولي',
                'معدات خاصة',
                'الحالة',
                'المشاهدات',
            ]);

            foreach ($projects as $index => $proj) {
                $teamNames = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                fputcsv($handle, [
                    $index + 1,
                    $proj->title,
                    $proj->faculty,
                    $proj->department,
                    $proj->graduation_year,
                    $proj->project_type ?? '-',
                    $proj->main_category ?? '-',
                    $proj->supervisor_name ?? '-',
                    $proj->supervisor_title ?? '-',
                    $teamNames,
                    $proj->contact_email ?? '-',
                    $proj->student_university_id ?? '-',
                    $proj->whatsapp_phone ?? '-',
                    $proj->booth_number ?? '-',
                    $proj->prototype_status ?? '-',
                    $proj->needs_special_equipment ? 'نعم (' . ($proj->special_equipment_details ?: 'بدون تفاصيل') . ')' : 'لا',
                    $proj->status_label,
                    $proj->views_count,
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
