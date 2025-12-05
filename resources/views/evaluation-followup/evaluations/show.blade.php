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
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">التدريب</span>
                                        <span>{{ $evaluation->training->title ?? '-' }}</span>
                                    </li>
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
                                        'facilities_evaluation' => ['title' => 'التجهيزات والمرافق', 'icon' => 'building', 'color' => 'info'],
                                        'content_evaluation' => ['title' => 'المحتوى التدريبي', 'icon' => 'book-open', 'color' => 'success'],
                                        'trainer_evaluation' => ['title' => 'أداء المدرب', 'icon' => 'chalkboard-teacher', 'color' => 'warning'],
                                        'organization_evaluation' => ['title' => 'التنظيم والإدارة', 'icon' => 'tasks', 'color' => 'secondary'],
                                        'impact_evaluation' => ['title' => 'الأثر والاستفادة', 'icon' => 'chart-line', 'color' => 'primary'],
                                    ];

                                    $labels = [
                                        'room_quality' => 'جودة القاعة',
                                        'equipment' => 'التجهيزات',
                                        'comfort' => 'الراحة',
                                        'cleanliness' => 'النظافة',
                                        'relevance' => 'الملاءمة',
                                        'quality' => 'الجودة',
                                        'organization' => 'التنظيم',
                                        'practical' => 'التطبيق العملي',
                                        'updated' => 'الحداثة',
                                        'knowledge' => 'المعرفة',
                                        'communication' => 'التواصل',
                                        'interaction' => 'التفاعل',
                                        'time_management' => 'إدارة الوقت',
                                        'motivation' => 'التحفيز',
                                        'scheduling' => 'الجدول الزمني',
                                        'coordination' => 'التنسيق',
                                        'support' => 'الدعم',
                                        'communication_admin' => 'التواصل الإداري',
                                        'skills_gained' => 'المهارات المكتسبة',
                                        'knowledge_gained' => 'المعرفة المكتسبة',
                                        'practical_application' => 'إمكانية التطبيق',
                                        'career_impact' => 'الأثر المهني',
                                        'overall_satisfaction' => 'الرضا العام'
                                    ];
                                @endphp

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
                                                                <li class="mb-2 d-flex justify-content-between align-items-center">
                                                                    <span>{{ $labels[$key] ?? $key }}</span>
                                                                    <span class="badge bg-light text-dark border">
                                                                        {{ $value }}/5 <i class="fas fa-star text-warning ms-1"></i>
                                                                    </span>
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
                        @elseif($evaluation->type == 'employment')
                            <!-- تقييم التوظيف -->
                            <div class="row">
                                @php
                                    $empSections = [
                                        'employment_evaluation' => ['title' => 'بيئة العمل والإشراف', 'icon' => 'building', 'color' => 'info'],
                                    ];
                                    $empLabels = [
                                        'workplace_quality' => 'جودة مكان العمل',
                                        'tools_equipment' => 'الأدوات والمعدات',
                                        'safety' => 'الأمان والسلامة',
                                        'work_culture' => 'ثقافة العمل',
                                        'supervisor_support' => 'دعم المشرف',
                                        'guidance' => 'التوجيه والإرشاد'
                                    ];
                                @endphp

                                @foreach($empSections as $field => $config)
                                    @if($evaluation->$field)
                                        <div class="col-12 mb-4">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <div class="card-header bg-{{ $config['color'] }} text-white">
                                                    <i class="fas fa-{{ $config['icon'] }} me-2"></i> {{ $config['title'] }}
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach($evaluation->$field as $key => $value)
                                                            @if(is_numeric($value))
                                                                <div class="col-md-6 mb-2">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                                                        <span>{{ $empLabels[$key] ?? $key }}</span>
                                                                        <span class="badge bg-light text-dark border">
                                                                            {{ $value }}/5 <i class="fas fa-star text-warning ms-1"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
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
                                            <h6 class="fw-bold text-success"><i class="fas fa-plus-circle me-1"></i> نقاط القوة
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->strengths }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->weaknesses)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-danger"><i class="fas fa-minus-circle me-1"></i> نقاط الضعف
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->weaknesses }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->comments)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-primary"><i class="fas fa-comment me-1"></i> تعليقات عامة
                                            </h6>
                                            <p class="bg-light p-3 rounded">{{ $evaluation->comments }}</p>
                                        </div>
                                    @endif

                                    @if($evaluation->recommendations)
                                        <div class="col-md-6 mb-3">
                                            <h6 class="fw-bold text-info"><i class="fas fa-lightbulb me-1"></i> التوصيات</h6>
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