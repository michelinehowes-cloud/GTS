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
        // $users = User::whereIn('role', ['graduate', 'training_coordinator', 'company'])->get(); // Removed as per requirement
        $trainings = Training::where('status', 'active')->with('trainer')->get();
        $trainers = Trainer::all();
        // $evaluators = User::whereIn('role', ['admin', 'training_coordinator', 'evaluation_followup'])->get(); // Usually auth user is evaluator

        return view('evaluation-followup.evaluations.create', compact('trainings', 'trainers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'type' => 'required|in:training,employment,performance',
            'evaluation_date' => 'required|date|before_or_equal:today',
            'status' => 'required|in:draft,completed,reviewed',

            // General Training Evaluations
            'facilities' => 'nullable|array',
            'organization' => 'nullable|array',
            'impact' => 'nullable|array',
            'employment' => 'nullable|array', // For employment type

            // Daily Content Evaluations (Dynamic Keys e.g. content_day_1, content_day_2)
            'daily_content' => 'nullable|array',

            // Multiple Instructors Evaluation
            'instructors' => 'nullable|array',
            'instructors.*.id' => 'required|exists:trainers,id',
            'instructors.*.rating' => 'required|numeric|min:1|max:5',

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

        // Calculate simplified overall score (average of provided scores)
        // detailed scoring logic can be complex, sticking to simple average for now
        $score = 0;

        $evaluation = Evaluation::create([
            'training_id' => $request->training_id,
            'user_id' => null, // Requirement to remove user field
            'evaluator_id' => auth()->id(),
            'evaluatable_type' => 'App\Models\Training',
            'evaluatable_id' => $request->training_id,
            'evaluation_type' => $request->type,
            'type' => $request->type,
            'facilities_evaluation' => $request->facilities ?? [],
            'content_evaluation' => $request->daily_content ?? [], // Storing daily axes here
            'organization_evaluation' => $request->organization ?? [],
            'impact_evaluation' => $request->impact ?? [],
            'employment_evaluation' => $request->employment ?? [],
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments ?? '',
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
            'score' => 0, // Will update if needed
        ]);

        // Handle Instructors Evaluations
        if ($request->has('instructors')) {
            foreach ($request->instructors as $instructorData) {
                if (isset($instructorData['id'])) {
                    // Create TrainerEvaluation linked to this Evaluation
                    \App\Models\TrainerEvaluation::create([
                        'evaluation_id' => $evaluation->id,
                        'training_id' => $request->training_id,
                        'evaluator_id' => auth()->id(),
                        'trainer_id' => $instructorData['id'],
                        'scores' => $instructorData['scores'] ?? [], // Store detailed sub-scores if any
                        'rating' => $instructorData['rating'] ?? 0,
                        'notes' => $instructorData['comments'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('evaluation-followup.evaluations.index')
            ->with('success', 'تم إنشاء التقييم الشامل بنجاح');
    }

    public function show(Evaluation $evaluation)
    {
        $evaluation->load(['user', 'evaluator', 'training', 'trainerEvaluations.trainer']);

        return view('evaluation-followup.evaluations.show', compact('evaluation'));
    }

    public function edit(Evaluation $evaluation)
    {
        $evaluation->load(['trainerEvaluations.trainer', 'training']);
        $users = User::whereIn('role', ['graduate', 'training_coordinator', 'company'])->get();
        // Removed eager loading of trainer for all trainings to reduce load, will assume training is selected 
        // OR eager load if needed for JS data attributes
        $trainings = Training::where('status', 'active')->with('trainer')->get();
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

            'facilities' => 'nullable|array',
            'daily_content' => 'nullable|array', // Updated name to match store
            'organization' => 'nullable|array',
            'impact' => 'nullable|array',
            'employment' => 'nullable|array',

            'instructors' => 'nullable|array',
            'instructors.*.id' => 'required|exists:trainers,id',
            'instructors.*.rating' => 'required|numeric|min:1|max:5',

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
            // 'user_id' => $request->user_id, // Removed as per requirement
            'type' => $request->type,
            'facilities_evaluation' => $request->facilities ?? [],
            'content_evaluation' => $request->daily_content ?? [], // Using daily_content
            'organization_evaluation' => $request->organization ?? [],
            'impact_evaluation' => $request->impact ?? [],
            'employment_evaluation' => $request->employment ?? [],
            'strengths' => $request->strengths,
            'weaknesses' => $request->weaknesses,
            'comments' => $request->comments,
            'recommendations' => $request->recommendations,
            'evaluation_date' => $request->evaluation_date,
            'status' => $request->status,
        ]);

        // Handle Instructors Evaluations Sync
        if ($request->has('instructors')) {
            $submittedTrainerIds = [];
            foreach ($request->instructors as $instructorData) {
                if (isset($instructorData['id'])) {
                    $submittedTrainerIds[] = $instructorData['id'];

                    \App\Models\TrainerEvaluation::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                            'trainer_id' => $instructorData['id'],
                        ],
                        [
                            'training_id' => $request->training_id,
                            'evaluator_id' => auth()->id(),
                            'scores' => $instructorData['scores'] ?? [],
                            'rating' => $instructorData['rating'] ?? 0,
                            'notes' => $instructorData['comments'] ?? null,
                        ]
                    );
                }
            }

            // Delete removed trainers
            \App\Models\TrainerEvaluation::where('evaluation_id', $evaluation->id)
                ->whereNotIn('trainer_id', $submittedTrainerIds)
                ->delete();
        } else {
            // If no instructors provided, delete all? Or maybe just keep generic? 
            // Assuming if section is hidden or empty, we might want to clear them if type is training
            if ($request->type === 'training') {
                $evaluation->trainerEvaluations()->delete();
            }
        }

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
