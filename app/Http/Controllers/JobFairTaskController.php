<?php

namespace App\Http\Controllers;

use App\Models\JobFair;
use App\Models\JobFairTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobFairTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض قائمة المهام ونموذج المتابعة لمشروع معرض التوظيف 2026
     */
    public function index(Request $request)
    {
        $jobFair = JobFair::whereYear('event_date', 2026)->first() ?? JobFair::latest()->first();

        $query = JobFairTask::with(['user', 'supervisor', 'jobFair']);

        if ($request->filled('team_name')) {
            $query->where('team_name', $request->team_name);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest('due_date')->paginate(20);

        // احصائيات سريعة
        $totalTasks = JobFairTask::count();
        $completedTasks = JobFairTask::where('status', 'completed')->count();
        $inProgressTasks = JobFairTask::where('status', 'in_progress')->count();
        $delayedTasks = JobFairTask::where('status', 'delayed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // قائمة الموظفين للاختيار
        $staffUsers = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup', 'partner_coordinator'])
            ->orderBy('name')
            ->get();

        $teams = [
            'اللجنة التنظيمية الرئيسية',
            'لجنة التنسيق والبروتوكول',
            'لجنة العلاقات والشراكات',
            'اللجنة الإعلامية والتوثيق',
            'لجنة التجهيز والدعم اللوجستي',
            'لجنة الاستقبال والتوجيه',
            'فريق العمليات والتنظيم الميداني',
        ];

        return view('job-fair.tasks.index', compact(
            'tasks',
            'jobFair',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'delayedTasks',
            'completionRate',
            'staffUsers',
            'teams'
        ));
    }

    /**
     * حفظ مهمة جديدة وفق النموذج الرسمي
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'team_name' => 'required|string|max:255',
            'task_description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'evaluation_score' => 'nullable|string|max:50',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'notes' => 'nullable|string',
            'job_fair_id' => 'nullable|exists:job_fairs,id',
        ]);

        $task = JobFairTask::create([
            'job_fair_id' => $validated['job_fair_id'] ?? JobFair::first()?->id,
            'user_id' => $validated['user_id'],
            'supervisor_id' => Auth::id(),
            'team_name' => $validated['team_name'],
            'task_description' => $validated['task_description'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'evaluation_score' => $validated['evaluation_score'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'signature_name' => Auth::user()->name,
            'signed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'تمت إضافة المهمة إلى جدول تنظيم ومتابعة معرض التوظيف 2026 بنجاح.');
    }

    /**
     * تحديث مهمة قائمة
     */
    public function update(Request $request, JobFairTask $task)
    {
        $validated = $request->validate([
            'task_description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'evaluation_score' => 'nullable|string|max:50',
            'status' => 'required|in:pending,in_progress,completed,delayed',
            'notes' => 'nullable|string',
        ]);

        $task->update($validated);

        return redirect()->back()->with('success', 'تم تحديث بيانات المهمة والتقييم بنجاح.');
    }

    /**
     * حذف مهمة
     */
    public function destroy(JobFairTask $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'تم حذف المهمة بنجاح.');
    }

    /**
     * طباعة النموذج الرسمي المعتمد لجامعة طرابلس (مشروع سنة 2026 - نموذج المهام)
     */
    public function print(Request $request)
    {
        $userId = $request->query('user_id');
        $teamName = $request->query('team_name');

        $query = JobFairTask::with(['user', 'supervisor']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($teamName) {
            $query->where('team_name', $teamName);
        }

        $tasks = $query->orderBy('start_date')->get();
        $employee = $userId ? User::find($userId) : ($tasks->first()?->user ?? Auth::user());
        $supervisor = $tasks->first()?->supervisor ?? Auth::user();
        $assignedTeam = $teamName ?? ($tasks->first()?->team_name ?? 'اللجنة التنظيمية لمعرض التوظيف 2026');

        return view('job-fair.tasks.print', compact('tasks', 'employee', 'supervisor', 'assignedTeam'));
    }
}
