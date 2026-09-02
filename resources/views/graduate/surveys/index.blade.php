@extends('layouts.app')

@section('title', 'الاستبيانات المتاحة')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'الاستبيانات المتاحة', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-poll me-2"></i>الاستبيانات واستطلاعات الرأي
            </h2>
            <div class="text-muted small mt-1">شاركنا رأيك وساعدنا في تحسين وتطوير البرامج والخدمات المقدمة للخريجين</div>
        </div>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($surveys->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($surveys as $survey)
                <div class="col-md-6 col-lg-4">
                    <div class="card-modern h-100 d-flex flex-column">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">
                                @if($survey->type == 'general') استبيان عام
                                @elseif($survey->type == 'training') برنامج تدريبي
                                @elseif($survey->type == 'job_opportunity') فرصة عمل
                                @else نشاط وفعالية @endif
                            </span>
                            <small class="text-muted">
                                <i class="far fa-clock me-1 text-warning"></i> ينتهي: {{ $survey->end_date->format('Y-m-d') }}
                            </small>
                        </div>

                        <div class="card-body p-4 flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2">{{ $survey->title }}</h5>
                            <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($survey->description, 120) }}
                            </p>

                            @if($survey->type == 'training' && $survey->training)
                                <div class="p-2 bg-light rounded-3 small mb-2 text-muted">
                                    <i class="fas fa-chalkboard-teacher text-info me-1"></i>
                                    مرتبط بـ: <strong class="text-dark">{{ $survey->training->title }}</strong>
                                </div>
                            @elseif($survey->type == 'job_opportunity' && $survey->jobOpportunity)
                                <div class="p-2 bg-light rounded-3 small mb-2 text-muted">
                                    <i class="fas fa-briefcase text-success me-1"></i>
                                    مرتبط بـ: <strong class="text-dark">{{ $survey->jobOpportunity->title }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white p-3 border-top">
                            @if(in_array($survey->id, $answeredSurveyIds))
                                @if(now()->isBefore($survey->end_date))
                                    <a href="{{ route('graduate.surveys.show', $survey->id) }}" class="btn btn-outline-warning-modern btn-sm w-100">
                                        <i class="fas fa-edit me-1"></i> تعديل الإجابات
                                    </a>
                                    <small class="text-success d-block mt-2 text-center small fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> تم تقديم إجاباتك مسبقاً
                                    </small>
                                @else
                                    <button class="btn btn-light border btn-sm w-100 text-muted" disabled>
                                        <i class="fas fa-check-circle me-1 text-success"></i> تم الإجابة (انتهى وقت التعديل)
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('graduate.surveys.show', $survey->id) }}" class="btn btn-primary-modern btn-sm w-100">
                                    <i class="fas fa-pen me-1"></i> بدء تعبئة الاستبيان
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-modern text-center py-5">
            <div class="card-body py-5">
                <i class="fas fa-poll display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد استبيانات متاحة حالياً</h4>
                <p class="text-muted mb-0">شكراً لاهتمامك، سيتم إشعارك فور إتاحة استبيانات جديدة تناسب نشاطك</p>
            </div>
        </div>
    @endif
</div>
@endsection