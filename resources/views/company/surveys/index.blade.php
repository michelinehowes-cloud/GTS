@extends('layouts.app')

@section('title', 'استبيانات واستطلاعات الرأي للشركات')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- الشريط الترحيبي المعتمد -->
    <x-page-hero
        title="الاستبيانات واستطلاعات الرأي"
        subtitle="شاركنا تقييمكم للخدمات، المعارض، والبرامج المشتركة لمساعدتنا على تعزيز جودة المخرجات وتلبية متطلبات سوق العمل"
        icon="fas fa-poll"
        :breadcrumbs="[
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'الاستبيانات']
        ]"
        :badge="$surveys->count() > 0 ? $surveys->count() . ' استبيان متاح' : null"
        badgeIcon="fas fa-clipboard-check"
    >
        <a href="{{ route('company.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="{{ route('company.job-fairs.index') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 text-nowrap" style="font-size: 0.88rem;">
            <i class="fas fa-calendar-alt fs-6"></i>
            <span>المعارض والفعاليات</span>
        </a>
    </x-page-hero>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($surveys->count() > 0)
        <div class="row g-4 mb-4">
            @foreach($surveys as $survey)
                @php
                    $isAnswered = in_array($survey->id, $answeredSurveyIds);
                    $canEdit = !$survey->end_date || now()->isBefore($survey->end_date);
                @endphp
                <div class="col-md-6 col-xl-4 d-flex">
                    <div class="card-modern w-100 d-flex flex-column border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; border: 1px solid rgba(13, 56, 130, 0.15); font-size: 0.75rem;">
                                @if($survey->type == 'job_fair')
                                    <i class="fas fa-calendar-check text-warning me-1"></i> تقييم ملتقى التوظيف
                                @elseif($survey->type == 'general')
                                    <i class="fas fa-poll-h text-primary me-1"></i> استبيان عام للشركاء
                                @else
                                    <i class="fas fa-file-alt text-info me-1"></i> استطلاع رأي
                                @endif
                            </span>

                            <small class="text-muted fw-bold" style="font-size: 0.78rem;">
                                <i class="far fa-clock text-warning me-1"></i> 
                                {{ $survey->end_date ? 'ينتهي: ' . $survey->end_date->format('Y-m-d') : 'متاح باستمرار' }}
                            </small>
                        </div>

                        <div class="card-body p-4 flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem; line-height: 1.5;">{{ $survey->title }}</h5>
                            <p class="text-muted small mb-3 lh-base" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 60px;">
                                {{ Str::limit(strip_tags($survey->description ?: 'يسرنا استقبال آرائكم وملاحظاتكم القيمة حول التعاون المشترك وسبل تطوير البرامج والخدمات.'), 140) }}
                            </p>

                            @if($survey->type == 'job_fair' && $survey->jobFair)
                                <div class="p-2.5 bg-light rounded-3 small mb-2 text-muted border">
                                    <i class="fas fa-university text-primary me-1"></i>
                                    خاص بمعرض: <strong class="text-dark">{{ $survey->jobFair->title }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-light bg-opacity-50 p-3 border-top">
                            @if($isAnswered)
                                @if($canEdit)
                                    <a href="{{ route('company.surveys.show', $survey->id) }}" class="btn btn-outline-warning text-dark fw-bold rounded-3 btn-sm w-100 py-2">
                                        <i class="fas fa-edit me-1 text-warning"></i> تعديل الإجابات المسجلة
                                    </a>
                                    <small class="text-success d-block mt-2 text-center small fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> تم تسجيل إجابات شركتكم بنجاح
                                    </small>
                                @else
                                    <button class="btn btn-light border btn-sm w-100 text-muted fw-bold py-2" disabled>
                                        <i class="fas fa-check-circle me-1 text-success"></i> تم التقديم (انتهت فترة الاستبيان)
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('company.surveys.show', $survey->id) }}" class="btn btn-primary fw-bold rounded-3 btn-sm w-100 py-2 shadow-sm" style="background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%); border: none;">
                                    <i class="fas fa-pen me-1"></i> بدء تعبئة الاستبيان
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-modern text-center py-5 rounded-4 border-0 shadow-sm bg-white">
            <div class="card-body py-5">
                <i class="fas fa-poll display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد استبيانات جديدة متاحة لشركتكم حالياً</h4>
                <p class="text-muted mb-0">نشكر لكم تعاونكم المستمر، سيتم إشعاركم فور إطلاق أي استبيان أو استطلاع رأي مخصص لشركاء التوظيف والتدريب.</p>
            </div>
        </div>
    @endif
</div>
@endsection
