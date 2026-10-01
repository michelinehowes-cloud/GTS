@extends('layouts.app')

@section('title', 'تفاصيل التقييم - نظام إدارة الخريجين')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- رأس الصفحة -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body d-flex justify-content-between align-items-center bg-white rounded">
                        <div>
                            <h4 class="mb-1 text-primary fw-bold">
                                <i class="fas fa-file-alt me-2"></i> تفاصيل التقييم #{{ $evaluation->id }}
                            </h4>
                            <p class="text-muted mb-0">
                                تاريخ التقييم:
                                {{ $evaluation->evaluation_date ? \Carbon\Carbon::parse($evaluation->evaluation_date)->format('Y/m/d') : '-' }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('evaluation-followup.evaluations.edit', $evaluation) }}"
                                class="btn btn-outline-primary me-2">
                                <i class="fas fa-edit me-1"></i> تعديل
                            </a>
                            <a href="{{ route('evaluation-followup.evaluations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> عودة
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- المعلومات الأساسية -->
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 border-0">
                            <div class="card-header bg-gradient text-white"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات عامة</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">نوع التقييم</span>
                                        <span
                                            class="badge bg-{{ $evaluation->type == 'training' ? 'success' : ($evaluation->type == 'employment' ? 'info' : 'primary') }}">
                                            {{ $evaluation->type == 'training' ? 'تدريب' : ($evaluation->type == 'employment' ? 'توظيف' : 'أداء') }}
                                        </span>
                                    </li>
                                    @if($evaluation->training)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">البرنامج التدريبي</span>
                                            <span class="text-primary fw-semibold">{{ $evaluation->training->title }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">المدرب / المحاضر</span>
                                            <span>{{ $evaluation->training->trainer ? $evaluation->training->trainer->name : ($evaluation->training->instructor_name ?? 'غير محدد') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">القسم المنفذ</span>
                                            <span>{{ $evaluation->training->category ?? ($evaluation->training->coordinator ? $evaluation->training->coordinator->name : 'مكتب تدريب الخريجين') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">مكان التنفيذ</span>
                                            <span>{{ $evaluation->training->location ?? 'جامعة طرابلس' }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">تاريخ التنفيذ</span>
                                            <span class="small">{{ $evaluation->training->start_date ? $evaluation->training->start_date->format('Y/m/d') : '-' }}</span>
                                        </li>
                                    @endif
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">المقيم</span>
                                        <span>{{ $evaluation->evaluator->name ?? '-' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">الحالة</span>
                                        <span
                                            class="badge bg-{{ $evaluation->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ $evaluation->status == 'completed' ? 'مكتمل' : 'مسودة' }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">التقييم العام</span>
                                        <span
                                            class="fw-bold text-primary fs-5">{{ $evaluation->overall_rating ?? '-' }}/5</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- تفاصيل التقييم -->
                    <div class="col-md-8">
                        @if($evaluation->type == 'training')
                            <!-- تقييم التدريب -->
                            <div class="row">
                                @php
                                    $sections = [
                                        'content_evaluation' => ['title' => 'تقييم محتوى وتنفيذ الجلسة التدريبية', 'icon' => 'tasks', 'color' => 'primary'],
                                        'trainer_evaluation' => ['title' => 'تقييم أداء المدرب والمحاضر', 'icon' => 'chalkboard-teacher', 'color' => 'success'],
                                        'facilities_evaluation' => ['title' => 'التجهيزات والمرافق', 'icon' => 'building', 'color' => 'info'],
                                        'organization_evaluation' => ['title' => 'التنظيم والإدارة', 'icon' => 'cogs', 'color' => 'warning'],
                                        'impact_evaluation' => ['title' => 'الأثر والاستفادة', 'icon' => 'chart-line', 'color' => 'secondary'],
                                    ];

                                    $labels = [
                                        // Official Tripoli University Session Criteria
                                        'clarity_of_goals' => '1. وضوح أهداف البرنامج التدريبي',
                                        'session_sequence' => '2. تنظيم وتتابع محاور الجلسة',
                                        'presentation_attractiveness' => '3. جاذبية العرض وأساليب التقديم',
                                        'trainee_interaction' => '4. تفاعل المتدربين أثناء النشاط',
                                        'diversity_of_tools' => '5. تنوع الوسائل التدريبية المستخدمة',
                                        'achieving_outcomes' => '6. مدى تحقيق مخرجات التدريب المستهدفة',
                                        'time_commitment' => '7. مدى التزام التدريب بالوقت المحدد',
                                        'pre_post_assessment' => '8. وجود قياس قبلي/ بعدي للجلسة',
                                        'general_training_rating' => '9. التقييم العام للتدريب',
                                        'training_total_ratio' => '10. نسبة اجمالي تقييم التدريب',

                                        // Official Tripoli University Trainer Criteria
                                        'trainer_punctuality' => '1. الحضور والانضباط في الوقت',
                                        'trainer_clarity' => '2. وضوح الشرح والأسلوب',
                                        'trainer_management' => '3. القدرة على إدارة المتدربين وتحفيزهم',
                                        'trainer_interaction' => '4. التفاعل مع الأسئلة والمداخلات',
                                        'trainer_content_adherence' => '5. الالتزام بالمحتوى المتفق عليه',
                                        'trainer_professionalism' => '6. المهنية في التعامل',
                                        'trainer_methods' => '7. توظيف أساليب تدريب مناسبة',
                                        'trainer_total_ratio' => '8. نسبة اجمالي تقييم المدرب',

                                        // Facilities
                                        'room_quality' => 'جودة القاعة التدريبية',
                                        'equipment' => 'التجهيزات والأدوات',
                                        'comfort' => 'الراحة والإضاءة',
                                        'cleanliness' => 'النظافة والترتيب',
                                        'accessibility' => 'سهولة الوصول',
                                        'lighting' => 'الإضاءة',
                                        'ventilation' => 'التهوية',
                                        'noise_level' => 'مستوى الضوضاء',
                                        
                                        // Organization
                                        'scheduling' => 'الجدول الزمني',
                                        'coordination' => 'التنسيق والترتيب',
                                        'support' => 'الدعم الفني واللوجستي',
                                        'communication_admin' => 'التواصل الإداري',
                                        'problem_solving' => 'سرعة حل المشكلات',
                                        
                                        // Impact
                                        'skills_gained' => 'المهارات المكتسبة',
                                        'knowledge_gained' => 'المعرفة المكتسبة',
                                        'practical_application' => 'إمكانية التطبيق العملي',
                                        'career_impact' => 'التأثير على المسار المهني',
                                        'overall_satisfaction' => 'الرضا العام عن البرنامج',
                                        
                                        // Content
                                        'relevance' => 'ملاءمة المحتوى للأهداف',
                                        'quality' => 'جودة المواد التدريبية',
                                        'organization' => 'تنظيم المحتوى',
                                        'practical' => 'التطبيق العملي',
                                        'updated' => 'حداثة المعلومات'
                                    ];
                                @endphp

                                <!-- Other Sections -->
                                @foreach($sections as $field => $config)
                                    @if($evaluation->$field)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-header bg-{{ $config['color'] }} text-white">
                                                    <i class="fas fa-{{ $config['icon'] }} me-2"></i> {{ $config['title'] }}
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($evaluation->$field as $key => $value)
                                                            @if(is_numeric($value))
                                                                <li class="mb-3">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <span class="small">{{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                                        <span class="badge bg-light text-dark border">{{ $value }}/5</span>
                                                                    </div>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar bg-{{ $config['color'] }}" role="progressbar" style="width: {{ $value * 20 }}%" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Daily and Trainer Evaluations Row -->
                            <div class="row">
                                <!-- Daily Content Evaluation -->
                                @if($evaluation->content_evaluation)
                                    <div class="col-12 mb-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-success text-white">
                                                <i class="fas fa-book-open me-2"></i> المحتوى التدريبي (تقييم يومي)
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($evaluation->content_evaluation as $dayKey => $values)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card h-100 border-light">
                                                                <div class="card-header bg-light">
                                                                    <strong>{{ str_replace('_', ' ', $dayKey) }}</strong>
                                                                </div>
                                                                <ul class="list-group list-group-flush">
                                                                    @if(is_array($values))
                                                                        @foreach($values as $k => $v)
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <small>{{ $labels[$k] ?? $k }}</small>
                                                                                <span class="badge bg-success rounded-pill">{{ $v }}/5</span>
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Trainer Evaluations -->
                                @if($evaluation->trainerEvaluations->count() > 0)
                                    <div class="col-12 mb-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-warning text-dark">
                                                <i class="fas fa-chalkboard-teacher me-2"></i> أداء المدربين
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($evaluation->trainerEvaluations as $trainerEval)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card h-100 shadow-sm border-light">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                                        <h6 class="card-title fw-bold mb-0">
                                                                            {{ $trainerEval->trainer->name ?? 'مدرب غير معروف' }}
                                                                        </h6>
                                                                        <span class="badge bg-warning text-dark">{{ $trainerEval->rating }}/5 <i class="fas fa-star ms-1"></i></span>
                                                                    </div>
                                                                    
                                                                    @if($trainerEval->scores && is_array($trainerEval->scores))
                                                                        <div class="row g-2 mb-3 bg-light p-2 rounded">
                                                                            @php
                                                                                $trainerLabels = [
                                                                                    'knowledge' => 'المعرفة والخبرة',
                                                                                    'communication' => 'مهارات التواصل',
                                                                                    'interaction' => 'التفاعل مع المتدربين',
                                                                                    'time_management' => 'إدارة الوقت',
                                                                                    'motivation' => 'القدرة على التحفيز'
                                                                                ];
                                                                            @endphp
                                                                            @foreach($trainerEval->scores as $sKey => $sValue)
                                                                                @if(isset($trainerLabels[$sKey]) || !is_numeric($sKey))
                                                                                <div class="col-12">
                                                                                    <div class="d-flex justify-content-between small mb-1">
                                                                                        <span>{{ $trainerLabels[$sKey] ?? ucfirst(str_replace('_', ' ', $sKey)) }}</span>
                                                                                        <strong>{{ $sValue }}/5</strong>
                                                                                    </div>
                                                                                    <div class="progress" style="height: 4px;">
                                                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $sValue * 20 }}%" aria-valuenow="{{ $sValue }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                                    </div>
                                                                                </div>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    @endif

                                                                    @if($trainerEval->notes || $trainerEval->strengths || $trainerEval->weaknesses || $trainerEval->recommendations)
                                                                        <div class="mt-2 p-2 border-top small text-muted">
                                                                            @if($trainerEval->notes)
                                                                                <p class="mb-1"><i class="fas fa-comment me-1"></i> <strong>الملاحظات:</strong> {{ $trainerEval->notes }}</p>
                                                                            @endif
                                                                            @if($trainerEval->strengths)
                                                                                <p class="mb-1 text-success"><i class="fas fa-plus-circle me-1"></i> <strong>النقاط القوية:</strong> {{ $trainerEval->strengths }}</p>
                                                                            @endif
                                                                            @if($trainerEval->weaknesses)
                                                                                <p class="mb-1 text-danger"><i class="fas fa-minus-circle me-1"></i> <strong>نقاط التحسين:</strong> {{ $trainerEval->weaknesses }}</p>
                                                                            @endif
                                                                            @if($trainerEval->recommendations)
                                                                                <p class="mb-0 text-info"><i class="fas fa-lightbulb me-1"></i> <strong>التوصيات:</strong> {{ $trainerEval->recommendations }}</p>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @elseif($evaluation->type == 'employment')
                            <!-- تقييم التوظيف -->
                            <div class="row">
                                @php
                                    $employmentGroups = [
                                        'work_environment' => [
                                            'label' => 'بيئة العمل',
                                            'icon' => 'building',
                                            'color' => 'info',
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
                                            'icon' => 'user-tie',
                                            'color' => 'primary',
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
                                            'icon' => 'chart-line',
                                            'color' => 'success',
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
                                            'icon' => 'money-bill-wave',
                                            'color' => 'warning',
                                            'questions' => [
                                                'salary' => 'الراتب',
                                                'benefits' => 'المزايا',
                                                'work_hours' => 'ساعات العمل',
                                                'work_life_balance' => 'التوازن بين العمل والحياة',
                                                'job_security' => 'الأمان الوظيفي',
                                            ]
                                        ],
                                        'overall' => [
                                            'label' => 'التقييم العام للوظيفة',
                                            'icon' => 'star',
                                            'color' => 'dark',
                                            'questions' => [
                                                'job_satisfaction' => 'الرضا الوظيفي',
                                                'company_reputation' => 'سمعة الشركة',
                                                'recommendation' => 'التوصية للآخرين',
                                                'future_prospects' => 'التوقعات المستقبلية',
                                                'overall_experience' => 'التجربة الإجمالية',
                                            ]
                                        ],
                                    ];
                                @endphp

                                @foreach($employmentGroups as $groupId => $group)
                                    @if(isset($evaluation->employment_evaluation[$groupId]) && is_array($evaluation->employment_evaluation[$groupId]))
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-header bg-{{ $group['color'] }} text-{{ in_array($group['color'], ['warning', 'light']) ? 'dark' : 'white' }}">
                                                    <i class="fas fa-{{ $group['icon'] }} me-2"></i> {{ $group['label'] }}
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($group['questions'] as $qKey => $qLabel)
                                                            @if(isset($evaluation->employment_evaluation[$groupId][$qKey]))
                                                                @php $val = $evaluation->employment_evaluation[$groupId][$qKey]; @endphp
                                                                <li class="mb-3">
                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                        <span class="small">{{ $qLabel }}</span>
                                                                        <span class="badge bg-light text-dark border">{{ $val }}/5</span>
                                                                    </div>
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar bg-{{ $group['color'] }}" role="progressbar" style="width: {{ $val * 20 }}%" aria-valuenow="{{ $val }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- التعليقات والتوصيات -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-dark text-white">
                                <i class="fas fa-comments me-2"></i> الملاحظات والتوصيات
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($evaluation->strengths)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-success"><i class="fas fa-plus-circle me-1"></i> نقاط
                                                القوة
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->strengths }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->weaknesses)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-danger"><i class="fas fa-minus-circle me-1"></i> نقاط
                                                الضعف
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->weaknesses }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->comments)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-primary"><i class="fas fa-comment me-1"></i> تعليقات
                                                عامة
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->comments }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->recommendations)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-info"><i class="fas fa-lightbulb me-1"></i> التوصيات
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->recommendations }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection