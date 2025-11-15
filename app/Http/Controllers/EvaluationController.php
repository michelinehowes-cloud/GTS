<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $evaluations = Evaluation::with(['user', 'evaluator', 'training'])
            ->latest()
            ->paginate(15);

        return view('evaluation-followup.evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['graduate', 'training_coordinator', 'company'])->get();
        $trainings = Training::where('status', 'active')->get();
        $evaluators = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup'])->get();


        return view('evaluation-followup.evaluations.create', compact('users', 'trainings', 'evaluators'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'evaluator_id' => 'required|exists:users,id',
            'training_id' => 'nullable|exists:trainings,id',
            'type' => 'required|in:performance,training,company',
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:1|max:5',
            'comments' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,completed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Evaluation::create([
            'user_id' => $request->user_id,
            'evaluator_id' => $request->evaluator_id,
            'training_id' => $request->training_id,
            'type' => $request->type,
            'scores' => $request->scores,
            'comments' => $request->comments,
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
        ]);

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم إنشاء التقييم بنجاح');
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['user', 'evaluator', 'training']);

        return view('evaluation-followup.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        $users = User::whereIn('role', ['graduate', 'training_coordinator', 'company'])->get();
        $trainings = Training::where('status', 'active')->get();
        $evaluators = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup'])->get();


        return view('evaluation-followup.evaluations.edit', compact('evaluation', 'users', 'trainings', 'evaluators'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'evaluator_id' => 'required|exists:users,id',
            'training_id' => 'nullable|exists:trainings,id',
            'type' => 'required|in:performance,training,company',
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:1|max:5',
            'comments' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,completed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evaluation->update([
            'user_id' => $request->user_id,
            'evaluator_id' => $request->evaluator_id,
            'training_id' => $request->training_id,
            'type' => $request->type,
            'scores' => $request->scores,
            'comments' => $request->comments,
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
        ]);

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    public function getEvaluationCriteria($type)
    {
        $criteria = [
            'performance' => [
                'التحصيل الأكاديمي' => 'academic_performance',
                'المهارات المهنية' => 'professional_skills',
                'السلوك والانضباط' => 'behavior_discipline',
                'التعاون والعمل الجماعي' => 'teamwork_cooperation',
                'الحضور والالتزام' => 'attendance_commitment',
            ],
            'training' => [
                'جودة المحتوى' => 'content_quality',
                'فعالية المدرب' => 'trainer_effectiveness',
                'التنظيم والإدارة' => 'organization_management',
                'الأثر على المهارات' => 'skill_impact',
                'الرضا العام' => 'overall_satisfaction',
            ],
            'company' => [
                'البيئة العملية' => 'work_environment',
                'الإشراف والتوجيه' => 'supervision_guidance',
                'فرص التطوير' => 'development_opportunities',
                'الرواتب والمزايا' => 'salary_benefits',
                'الرضا عن الشركة' => 'company_satisfaction',
            ],
        ];

        return $criteria[$type] ?? [];
    }
}
