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

        $query = JobFairProject::query();

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

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('summary', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('supervisor_name', 'like', "%{$s}%")
                  ->orWhere('faculty', 'like', "%{$s}%")
                  ->orWhere('department', 'like', "%{$s}%")
                  ->orWhere('team_members', 'like', "%{$s}%");
            });
        }

        $projects = $query->orderByDesc('is_featured')
                          ->orderByDesc('created_at')
                          ->get();

        // استخراج خيارات الفلترة المتاحة
        $baseQuery = $fair ? JobFairProject::where('job_fair_id', $fair->id) : JobFairProject::query();
        $faculties = (clone $baseQuery)->distinct()->pluck('faculty')->filter()->values();
        $departments = (clone $baseQuery)->distinct()->pluck('department')->filter()->values();
        $years = JobFairProject::distinct()->orderByDesc('graduation_year')->pluck('graduation_year')->filter()->values();
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
            'allFairs',
            'totalProjects',
            'totalFaculties',
            'totalStudents',
            'featuredCount'
        ));
    }

    /**
     * الصفحة المستقلة للمشروع مع البوستر ورمز QR وتفاصيل الفريق
     */
    public function publicShow(Request $request, $project)
    {
        if (!($project instanceof JobFairProject)) {
            $project = JobFairProject::with('jobFair')->findOrFail($project);
        } else {
            $project->load('jobFair');
        }

        // زيادة عداد المشاهدات بدون تحديث timestamps
        $project->timestamps = false;
        $project->increment('views_count');
        $project->timestamps = true;

        $fair = $project->jobFair;

        // مشاريع مقترحة ذات صلة
        $relatedProjects = JobFairProject::where('id', '!=', $project->id)
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
     * لوحة تحكم المشرفين لإدارة مشاريع المعرض
     */
    public function adminIndex(JobFair $fair)
    {
        $projects = $fair->projects()
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'     => $projects->count(),
            'faculties' => $projects->pluck('faculty')->unique()->count(),
            'featured'  => $projects->where('is_featured', true)->count(),
            'views'     => $projects->sum('views_count'),
        ];

        return view('job-fair.admin.projects', compact('fair', 'projects', 'stats'));
    }

    /**
     * حفظ مشروع تخرج جديد
     */
    public function store(Request $request, JobFair $fair)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'faculty'          => 'required|string|max:255',
            'department'       => 'required|string|max:255',
            'graduation_year'  => 'required|integer|min:2000|max:2035',
            'academic_year'    => 'nullable|string|max:50',
            'supervisor_name'  => 'nullable|string|max:255',
            'supervisor_title' => 'nullable|string|max:255',
            'summary'          => 'nullable|string',
            'objectives'       => 'nullable|string',
            'description'      => 'nullable|string',
            'booth_number'     => 'nullable|string|max:50',
            'project_url'      => 'nullable|url|max:255',
            'video_url'        => 'nullable|url|max:255',
            'status'           => 'required|string|in:published,draft,archived',
            'poster_image'     => 'nullable|image|max:5120',
            'cover_image'      => 'nullable|image|max:4096',
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

        $fair->projects()->create($validated);

        return back()->with('success', 'تمت إضافة مشروع التخرج بنجاح إلى المعرض والأرشيف.');
    }

    /**
     * تحديث بيانات مشروع تخرج
     */
    public function update(Request $request, JobFairProject $project)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'faculty'          => 'required|string|max:255',
            'department'       => 'required|string|max:255',
            'graduation_year'  => 'required|integer|min:2000|max:2035',
            'academic_year'    => 'nullable|string|max:50',
            'supervisor_name'  => 'nullable|string|max:255',
            'supervisor_title' => 'nullable|string|max:255',
            'summary'          => 'nullable|string',
            'objectives'       => 'nullable|string',
            'description'      => 'nullable|string',
            'booth_number'     => 'nullable|string|max:50',
            'project_url'      => 'nullable|url|max:255',
            'video_url'        => 'nullable|url|max:255',
            'status'           => 'required|string|in:published,draft,archived',
            'poster_image'     => 'nullable|image|max:5120',
            'cover_image'      => 'nullable|image|max:4096',
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
                'المشرف الأكاديمي',
                'اللقب العلمي',
                'أسماء أعضاء الفريق',
                'رقم الجناح',
                'رابط المشروع',
                'المشاهدات',
                'الحالة'
            ]);

            foreach ($projects as $index => $proj) {
                $teamNames = collect($proj->team_list)->pluck('name')->filter()->implode(' • ');
                fputcsv($handle, [
                    $index + 1,
                    $proj->title,
                    $proj->faculty,
                    $proj->department,
                    $proj->graduation_year,
                    $proj->supervisor_name ?? '-',
                    $proj->supervisor_title ?? '-',
                    $teamNames,
                    $proj->booth_number ?? '-',
                    $proj->project_url ?? '-',
                    $proj->views_count,
                    $proj->status === 'published' ? 'منشور' : ($proj->status === 'draft' ? 'مسودة' : 'مؤرشف')
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
