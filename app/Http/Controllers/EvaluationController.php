<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\Training;
use App\Models\Trainer;
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
        $trainers = Trainer::all();
        $evaluators = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup'])->get();

        return view('evaluation-followup.evaluations.create', compact('users', 'trainings', 'trainers', 'evaluators'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'type' => 'required|in:training,employment,performance',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,completed,reviewed',

            // تقييم التجهيزات
            'facilities.*' => 'nullable|numeric|min:1|max:5',

            // تقييم المحتوى
            'content.*' => 'nullable|numeric|min:1|max:5',

            // تقييم المدرب
            'trainer.*' => 'nullable|numeric|min:1|max:5',

            // تقييم التنظيم
            'organization.*' => 'nullable|numeric|min:1|max:5',

            // تقييم الأثر
            'impact.*' => 'nullable|numeric|min:1|max:5',

            // تقييم التوظيف
            'employment.*' => 'nullable|numeric|min:1|max:5',

            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'comments' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evaluation = Evaluation::create([
            'training_id' => $request->training_id,
            'user_id' => $request->user_id,
            'evaluator_id' => auth()->id(),
            'evaluatable_type' => 'App\Models\Training',
            'evaluatable_id' => $request->training_id,
            'evaluation_type' => $request->type,
            'type' => $request->type,
            'facilities_evaluation' => $request->facilities ?? null,
            'content_evaluation' => $request->content ?? null,
            'trainer_evaluation' => $request->trainer ?? null,
            'organization_evaluation' => $request->organization ?? null,
            'impact_evaluation' => $request->impact ?? null,
            'employment_evaluation' => $request->employment ?? null,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments ?? '',
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
            'score' => 0,
        ]);

        // حساب التقييم الإجمالي
        $evaluation->overall_rating = $evaluation->overall_rating;
        $evaluation->save();

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
        $trainers = Trainer::all();
        $evaluators = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup'])->get();

        return view('evaluation-followup.evaluations.edit', compact('evaluation', 'users', 'trainings', 'trainers', 'evaluators'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'type' => 'required|in:training,employment,performance',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,completed,reviewed',

            'facilities.*' => 'nullable|numeric|min:1|max:5',
            'content.*' => 'nullable|numeric|min:1|max:5',
            'trainer.*' => 'nullable|numeric|min:1|max:5',
            'organization.*' => 'nullable|numeric|min:1|max:5',
            'impact.*' => 'nullable|numeric|min:1|max:5',
            'employment.*' => 'nullable|numeric|min:1|max:5',

            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'comments' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $evaluation->update([
            'training_id' => $request->training_id,
            'user_id' => $request->user_id,
            'type' => $request->type,
            'facilities_evaluation' => $request->facilities ?? null,
            'content_evaluation' => $request->content ?? null,
            'trainer_evaluation' => $request->trainer ?? null,
            'organization_evaluation' => $request->organization ?? null,
            'impact_evaluation' => $request->impact ?? null,
            'employment_evaluation' => $request->employment ?? null,
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
        ]);

        // إعادة حساب التقييم الإجمالي
        $evaluation->overall_rating = $evaluation->overall_rating;
        $evaluation->save();

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    /**
     * الحصول على معايير التقييم حسب النوع
     */
    public function getEvaluationCriteria($type)
    {
        $criteria = $this->getComprehensiveCriteria();

        return response()->json($criteria[$type] ?? []);
    }

    /**
     * معايير التقييم الشاملة
     */
    private function getComprehensiveCriteria()
    {
        return [
            'training' => [
                'facilities' => [
                    'label' => 'التجهيزات والمرافق',
                    'questions' => [
                        'room_quality' => 'جودة القاعة التدريبية',
                        'equipment' => 'التجهيزات والأدوات',
                        'comfort' => 'الراحة والإضاءة',
                        'cleanliness' => 'النظافة والترتيب',
                        'accessibility' => 'سهولة الوصول',
                    ]
                ],
                'content' => [
                    'label' => 'المحتوى التدريبي',
                    'questions' => [
                        'relevance' => 'ملاءمة المحتوى للأهداف',
                        'quality' => 'جودة المواد التدريبية',
                        'organization' => 'تنظيم المحتوى',
                        'practical' => 'التطبيق العملي',
                        'updated' => 'حداثة المعلومات',
                    ]
                ],
                'trainer' => [
                    'label' => 'أداء المدرب',
                    'questions' => [
                        'knowledge' => 'المعرفة والخبرة',
                        'communication' => 'مهارات التواصل',
                        'interaction' => 'التفاعل مع المتدربين',
                        'time_management' => 'إدارة الوقت',
                        'motivation' => 'القدرة على التحفيز',
                    ]
                ],
                'organization' => [
                    'label' => 'التنظيم والإدارة',
                    'questions' => [
                        'scheduling' => 'الجدول الزمني',
                        'coordination' => 'التنسيق والتنظيم',
                        'support' => 'الدعم الإداري',
                        'communication_admin' => 'التواصل الإداري',
                        'problem_solving' => 'حل المشكلات',
                    ]
                ],
                'impact' => [
                    'label' => 'الأثر والاستفادة',
                    'questions' => [
                        'skills_gained' => 'المهارات المكتسبة',
                        'knowledge_gained' => 'المعرفة المكتسبة',
                        'practical_application' => 'إمكانية التطبيق العملي',
                        'career_impact' => 'الأثر على المسار المهني',
                        'overall_satisfaction' => 'الرضا العام',
                    ]
                ],
            ],
            'employment' => [
                'work_environment' => [
                    'label' => 'بيئة العمل',
                    'questions' => [
                        'workplace_quality' => 'جودة مكان العمل',
                        'tools_equipment' => 'الأدوات والمعدات',
                        'safety' => 'الأمان والسلامة',
                        'work_culture' => 'ثقافة العمل',
                        'colleagues' => 'العلاقة مع الزملاء',
                    ]
                ],
                'supervision' => [
                    'label' => 'الإشراف والتوجيه',
                    'questions' => [
                        'supervisor_support' => 'دعم المشرف',
                        'guidance' => 'التوجيه والإرشاد',
                        'feedback' => 'التغذية الراجعة',
                        'communication' => 'التواصل',
                        'problem_resolution' => 'حل المشكلات',
                    ]
                ],
                'development' => [
                    'label' => 'فرص التطوير',
                    'questions' => [
                        'training_opportunities' => 'فرص التدريب',
                        'career_growth' => 'النمو الوظيفي',
                        'skill_development' => 'تطوير المهارات',
                        'responsibilities' => 'المسؤوليات والتحديات',
                        'learning' => 'بيئة التعلم',
                    ]
                ],
                'compensation' => [
                    'label' => 'الرواتب والمزايا',
                    'questions' => [
                        'salary' => 'الراتب',
                        'benefits' => 'المزايا',
                        'work_hours' => 'ساعات العمل',
                        'work_life_balance' => 'التوازن بين العمل والحياة',
                        'job_security' => 'الأمان الوظيفي',
                    ]
                ],
                'overall' => [
                    'label' => 'التقييم العام',
                    'questions' => [
                        'job_satisfaction' => 'الرضا الوظيفي',
                        'company_reputation' => 'سمعة الشركة',
                        'recommendation' => 'التوصية للآخرين',
                        'future_prospects' => 'التوقعات المستقبلية',
                        'overall_experience' => 'التجربة الإجمالية',
                    ]
                ],
            ],
        ];
    }
}
