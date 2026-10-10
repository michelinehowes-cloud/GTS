@extends('layouts.app')

@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : (request()->routeIs('partnership.*') ? 'partnership' : 'career-guidance');
    $gradName = $nomination->graduate?->name ?? 'المرشح';
    $jobTitle = $nomination->jobOpportunity?->title ?? 'الفرصة الوظيفية';
@endphp

@section('title', 'تفاصيل الترشيح - ' . $gradName)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة التحكم', 'url' => Route::has($routePrefix . '.dashboard') ? route($routePrefix . '.dashboard') : route('home')],
            ['label' => 'إدارة الترشيحات', 'url' => Route::has($routePrefix . '.nominations') ? route($routePrefix . '.nominations') : '#'],
            ['label' => 'تفاصيل ترشيح ' . $gradName, 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-paper-plane me-2"></i> تفاصيل ملف الترشيح
            </h2>
            <div class="text-muted small mt-1">
                المرشح: <strong class="text-dark">{{ $gradName }}</strong> &bull; الفرصة: <strong class="text-dark">{{ $jobTitle }}</strong>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(Route::has($routePrefix . '.nominations.edit-status'))
            <a href="{{ route($routePrefix . '.nominations.edit-status', $nomination->id) }}" class="btn btn-primary-modern">
                <i class="fas fa-edit me-1"></i> تحديث الحالة
            </a>
            @endif
            @if(Route::has($routePrefix . '.nominations'))
            <a href="{{ route($routePrefix . '.nominations') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
            </a>
            @endif
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="row g-4 mb-4">
        <!-- بيانات الخريج المرشح -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-user-graduate me-2"></i>بيانات الخريج المرشح
                    </h5>
                    @if($nomination->graduate)
                        @php
                            $gradRoute = Route::has($routePrefix . '.graduates.show')
                                ? route($routePrefix . '.graduates.show', $nomination->graduate->id)
                                : (Route::has('career-guidance.graduates.show') ? route('career-guidance.graduates.show', $nomination->graduate->id) : null);
                        @endphp
                        @if($gradRoute)
                        <a href="{{ $gradRoute }}" class="btn btn-sm btn-outline-info-modern">
                            <i class="fas fa-external-link-alt me-1"></i> الملف الكامل
                        </a>
                        @endif
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الاسم الكامل</small>
                                <span class="fw-bold text-dark">{{ $nomination->graduate?->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                                <span class="fw-bold text-dark text-break">{{ $nomination->graduate?->email ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">التخصص والكلية</small>
                                <span class="fw-bold text-primary">{{ $nomination->graduate?->major ?? 'غير محدد' }}</span>
                                <small class="text-muted d-block">{{ $nomination->graduate?->faculty ?? $nomination->graduate?->university ?? '' }}</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">سنة التخرج والمعدل</small>
                                <span class="fw-bold text-dark">{{ $nomination->graduate?->graduation_year ?? '--' }} @if($nomination->graduate?->gpa) ({{ $nomination->graduate->gpa }}%) @endif</span>
                            </div>
                        </div>
                    </div>

                    @if(!empty($nomination->graduate?->skills))
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2 fw-bold">المهارات المسجلة:</small>
                            @php
                                $skills = is_array($nomination->graduate->skills) ? $nomination->graduate->skills : explode(',', $nomination->graduate->skills);
                            @endphp
                            @foreach($skills as $skill)
                                @if(trim($skill))
                                    <span class="badge bg-light text-primary border border-primary px-2 py-1 rounded-pill me-1 mb-1">{{ trim($skill) }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- بيانات الفرصة والشركة -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-briefcase me-2"></i>بيانات الفرصة الوظيفية والشركة
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">المسمى الوظيفي</small>
                                <span class="fw-bold text-dark">{{ $nomination->jobOpportunity?->title ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الشركة</small>
                                <span class="fw-bold text-primary">{{ $nomination->jobOpportunity?->company?->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">نوع الفرصة والموقع</small>
                                <span class="fw-bold text-dark">
                                    @if(($nomination->jobOpportunity?->type ?? '') == 'job') وظيفة @elseif(($nomination->jobOpportunity?->type ?? '') == 'training') تدريب @else تدريب عملي @endif
                                    - {{ $nomination->jobOpportunity?->location ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الموعد النهائي للتقديم</small>
                                <span class="fw-bold text-danger">{{ $nomination->jobOpportunity?->application_deadline ? $nomination->jobOpportunity->application_deadline->format('Y-m-d') : 'مفتوح' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($nomination->jobOpportunity?->description)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <small class="text-muted d-block mb-1 fw-bold">وصف الفرصة:</small>
                            <p class="small text-muted mb-0">{{ Str::limit($nomination->jobOpportunity->description, 200) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- مبررات الترشيح والملاحظات -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-info-circle me-2"></i>مبررات وملاحظات الترشيح
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">تاريخ الترشيح</small>
                                <span class="fw-bold text-dark">{{ $nomination->nominated_at ? $nomination->nominated_at->format('Y-m-d') : '--' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">المرشح بواسطة</small>
                                <span class="fw-bold text-dark">{{ $nomination->nominator->name ?? 'مسؤول النظام' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">الحالة الإدارية</small>
                                <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">{{ $nomination->status_text }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark mb-2">أسباب ومبررات الترشيح:</h6>
                                <p class="mb-0 text-muted">{{ $nomination->matching_reasons ?: 'لم تسجل أسباب تفصيلية' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark mb-2">ملاحظات الترشيح:</h6>
                                <p class="mb-0 text-muted">{{ $nomination->nomination_notes ?: 'لا توجد ملاحظات إضافية' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المقابلة إن وجدت -->
    @if($nomination->interview_date)
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-calendar-alt me-2"></i>بيانات المقابلة المجدولة
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">تاريخ المقابلة</small>
                                <span class="fw-bold text-dark">{{ $nomination->interview_date ? $nomination->interview_date->format('Y-m-d') : '--' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">وقت المقابلة</small>
                                <span class="fw-bold text-dark">{{ $nomination->interview_time ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">مكان / رابط المقابلة</small>
                                <span class="fw-bold text-dark">{{ $nomination->interview_location ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        @if($nomination->interview_notes)
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">ملاحظات المقابلة</small>
                                <span class="text-dark">{{ $nomination->interview_notes }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


