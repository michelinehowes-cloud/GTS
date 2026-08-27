@extends('layouts.app')

@section('title', 'تفاصيل فرصة العمل - ' . $opportunity->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-12">
            <!-- بطاقة العنوان الرئيسية -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-2" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-4 p-md-5 text-white position-relative">
                    <div class="position-absolute top-0 end-0 opacity-10 p-4" style="pointer-events: none;">
                        <i class="fas fa-briefcase fa-8x" style="transform: rotate(-15deg);"></i>
                    </div>
                    
                    <div class="row align-items-center position-relative z-index-1">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                                        <i class="fas fa-briefcase fa-3x text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-4 me-4">
                                    <h1 class="h2 mb-3 fw-bold text-white">{{ $opportunity->title }}</h1>
                                    <div class="d-flex flex-wrap gap-2 gap-md-3">
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $opportunity->company->name }}
                                        </span>
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $opportunity->location }}
                                        </span>
                                        <span class="badge rounded-pill" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); padding: 8px 16px; font-size: 0.9rem; font-weight: 500;">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $opportunity->seats }} مقاعد
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" class="btn btn-light text-primary fw-bold rounded-pill py-2 shadow-sm">
                                    <i class="fas fa-users me-2"></i>الترشيحات ({{ $nominationsCount['total'] ?? 0 }})
                                </a>
                                @if(auth()->user()->role === 'partnership_officer')
                                <a href="{{ route('partnership.nominations') }}" class="btn rounded-pill py-2 shadow-sm" style="background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);">
                                    <i class="fas fa-list me-2"></i>جميع الترشيحات
                                </a>
                                @endif
                                <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" class="btn btn-warning fw-bold rounded-pill py-2 shadow-sm">
                                    <i class="fas fa-edit me-2"></i>تعديل الفرصة
                                </a>
                                <a href="{{ route('job-opportunities.index') }}" class="btn rounded-pill py-2" style="color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.2);">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 border-0 mb-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-success">تم بنجاح!</h6>
                            <p class="mb-0 text-dark">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- العمود الأيمن - المعلومات الرئيسية -->
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-4">
                
                <!-- وصف الفرصة -->
                <div class="card shadow-sm border-0 rounded-4 h-100 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            وصف الفرصة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary mb-0" style="line-height: 1.8; font-size: 1.05rem;">
                            {{ $opportunity->description }}
                        </p>
                    </div>
                </div>

                <!-- المتطلبات والمهارات -->
                <div class="card shadow-sm border-0 rounded-4 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-tasks"></i>
                            </div>
                            المتطلبات والمهارات
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            @if($opportunity->required_specializations && count($opportunity->required_specializations) > 0)
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-graduation-cap me-2"></i>التخصصات المطلوبة</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($opportunity->required_specializations as $specialization)
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                            {{ $specialization }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($opportunity->required_skills && count($opportunity->required_skills) > 0)
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-cogs me-2"></i>المهارات المطلوبة</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($opportunity->required_skills as $skill)
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($opportunity->required_experience)
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-muted mb-2"><i class="fas fa-briefcase me-2"></i>الخبرة المطلوبة</h6>
                                <p class="text-dark mb-0 bg-light p-3 rounded-3">{{ $opportunity->required_experience }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- المتطلبات العامة -->
                @if($opportunity->requirements)
                <div class="card shadow-sm border-0 rounded-4 transition-all hover-shadow">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            المتطلبات العامة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-secondary mb-0 bg-light p-3 rounded-3" style="line-height: 1.7;">{{ $opportunity->requirements }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- العمود الأيسر - التواريخ والإحصائيات -->
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">
                
                <!-- التواريخ -->
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            التواريخ المهمة
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="bg-white rounded-circle p-2 me-3 text-success shadow-sm">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div>
                                    <div class="text-success fw-bold small mb-1">تاريخ البدء</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->start_date ? $opportunity->start_date->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                                <div class="bg-white rounded-circle p-2 me-3 text-primary shadow-sm">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <div>
                                    <div class="text-primary fw-bold small mb-1">تاريخ الانتهاء</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->end_date ? $opportunity->end_date->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <div class="bg-white rounded-circle p-2 me-3 text-danger shadow-sm">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="text-danger fw-bold small mb-1">آخر موعد للتقديم</div>
                                    <div class="text-dark fw-bold">{{ $opportunity->application_deadline ? $opportunity->application_deadline->format('Y-m-d') : 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإحصائيات -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            إحصائيات الترشيحات
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-primary fw-bold mb-1">{{ $nominationsCount['total'] ?? 0 }}</h3>
                                    <span class="text-muted small">إجمالي المتقدمين</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-success fw-bold mb-1">{{ $nominationsCount['accepted'] ?? 0 }}</h3>
                                    <span class="text-muted small">المقبولين</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-warning fw-bold mb-1">{{ $nominationsCount['pending'] ?? 0 }}</h3>
                                    <span class="text-muted small">قيد المراجعة</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center h-100 border border-light transition-all hover-shadow-sm">
                                    <h3 class="text-danger fw-bold mb-1">{{ $nominationsCount['rejected'] ?? 0 }}</h3>
                                    <span class="text-muted small">المرفوضين</span>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" class="btn btn-outline-primary w-100 mt-4 rounded-pill fw-bold">
                            عرض قائمة المتقدمين بالكامل
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
    }
    .hover-shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,.05)!important;
    }
</style>
@endsection