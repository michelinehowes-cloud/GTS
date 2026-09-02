@extends('layouts.app')

@section('title', 'ترشيحاتي ومقابلاتي')

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="ترشيحاتي ومقابلاتي الوظيفية"
        subtitle="متابعة حالة طلبات التوظيف، نتائج الفرز، ومواعيد المقابلات الشخصية مع الشركات"
        icon="fas fa-clipboard-list"
        :breadcrumbs="[
            ['label' => 'لوحة التحكم', 'url' => route('graduate.dashboard')],
            ['label' => 'ترشيحاتي ومقابلاتي']
        ]"
        :badge="$nominations->count() > 0 ? $nominations->count() . ' ترشيح وظيفي' : null"
        badgeIcon="fas fa-briefcase"
    >
        <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-search fs-6"></i>
            <span>تصفح فرص العمل</span>
        </a>
        <a href="{{ route('graduate.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
    </x-page-hero>

    <!-- Quick Stats Cards (4 Columns) -->
    @if($graduateData && $nominations->count() > 0)
        <div class="row g-2 g-md-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">قيد الفرز</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-warning mb-1">{{ $nominations->where('status', 'pending')->count() }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">طلبات بانتظار المراجعة</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">مقابلات مجدولة</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-info mb-1">{{ $nominations->where('status', 'interview_scheduled')->count() }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">مواعيد مقابلات قادمة</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">تم القبول</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-success mb-1">{{ $nominations->where('status', 'accepted')->count() }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">ترشيحات مقبولة بنجاح</div>
                </div>
            </div>

            <div class="col-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.78rem;">غير مكتمل</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(100, 116, 139, 0.1); color: #64748b;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="fs-3 fw-bolder text-secondary mb-1">{{ $nominations->whereIn('status', ['rejected', 'withdrawn'])->count() }}</div>
                    <div class="text-muted small" style="font-size: 0.72rem;">مرفوضة أو ملغاة</div>
                </div>
            </div>
        </div>
    @endif

    @if(!$graduateData)
        <div class="alert alert-warning alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لم يتم العثور على بياناتك الأكاديمية في النظام. يرجى التواصل مع مسؤول الإرشاد والتوجيه المهني.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif($nominations->count() > 0)
        <div class="row g-3 g-md-4 mb-4">
            @foreach($nominations as $nomination)
                @php
                    $company = $nomination->jobOpportunity->company ?? null;
                    $companyName = $company->name ?? 'شركة معتمدة';
                    $companyLogo = $company->logo_path ?? null;

                    $step = 1;
                    if ($nomination->status == 'pending') $step = 2;
                    elseif ($nomination->status == 'interview_scheduled') $step = 3;
                    elseif (in_array($nomination->status, ['accepted', 'rejected', 'withdrawn'])) $step = 4;

                    $progressPercent = ($step - 1) * 33.33;
                    $progressColor = '#2563eb';
                    if ($step == 2) $progressColor = '#f59e0b';
                    elseif ($step == 3) $progressColor = '#0ea5e9';
                    elseif ($step == 4) $progressColor = ($nomination->status == 'accepted' ? '#10b981' : '#ef4444');
                @endphp
                <div class="col-12 col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100 d-flex flex-column transition-hover" style="background: #ffffff;">
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                @if($nomination->status == 'accepted')
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #f0fdf4; color: #15803d; border: 1.5px solid #22c55e; box-shadow: none !important; font-size: 0.78rem;">
                                        <i class="fas fa-check-circle me-1"></i>مقبول
                                    </span>
                                @elseif($nomination->status == 'interview_scheduled')
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #eff6ff; color: #1d4ed8; border: 1.5px solid #3b82f6; box-shadow: none !important; font-size: 0.78rem;">
                                        <i class="fas fa-calendar-check me-1"></i>مقابلة مجدولة
                                    </span>
                                @elseif($nomination->status == 'rejected' || $nomination->status == 'withdrawn')
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #fef2f2; color: #b91c1c; border: 1.5px solid #ef4444; box-shadow: none !important; font-size: 0.78rem;">
                                        <i class="fas fa-times-circle me-1"></i>غير مكتمل
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="background: #fffbeb; color: #b45309; border: 1.5px solid #f59e0b; box-shadow: none !important; font-size: 0.78rem;">
                                        <i class="fas fa-hourglass-half me-1"></i>قيد المراجعة
                                    </span>
                                @endif
                            </div>

                            @if($nomination->nomination_type == 'self')
                                <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; box-shadow: none !important; font-size: 0.7rem;">
                                    <i class="fas fa-user me-1"></i>تقديم مباشر
                                </span>
                            @else
                                <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; box-shadow: none !important; font-size: 0.7rem;">
                                    <i class="fas fa-star me-1"></i>ترشيح المكتب
                                </span>
                            @endif
                        </div>

                        <div class="card-body p-4 flex-grow-1">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if($companyLogo)
                                    <img src="{{ asset('storage/' . $companyLogo) }}" alt="{{ $companyName }}" class="rounded-3 shadow-sm object-fit-cover flex-shrink-0" style="width: 52px; height: 52px; border: 1px solid #e2e8f0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="rounded-3 bg-light text-primary fw-bold shadow-sm align-items-center justify-content-center flex-shrink-0 border" style="width: 52px; height: 52px; font-size: 1.3rem; display: none;">
                                        {{ mb_substr($companyName, 0, 1) }}
                                    </div>
                                @else
                                    <div class="rounded-3 bg-light text-primary fw-bold shadow-sm d-flex align-items-center justify-content-center flex-shrink-0 border" style="width: 52px; height: 52px; font-size: 1.3rem;">
                                        {{ mb_substr($companyName, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h5 class="fw-bold text-dark mb-1 fs-6">{{ $nomination->jobOpportunity->title }}</h5>
                                    <div class="text-muted small d-flex align-items-center gap-1">
                                        <i class="fas fa-building text-primary"></i> {{ $companyName }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 text-muted small mb-3 flex-wrap" style="font-size: 0.78rem;">
                                <span><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $nomination->jobOpportunity->location ?? 'ليبيا' }}</span>
                                <span><i class="far fa-calendar-alt text-secondary me-1"></i>{{ $nomination->created_at->format('Y/m/d') }}</span>
                            </div>

                            <!-- Stepper Progress Tracker with Connecting Line -->
                            <div class="p-3 bg-light rounded-3 mb-3">
                                <div class="position-relative py-1">
                                    <!-- Connecting Line Background -->
                                    <div class="position-absolute" style="top: 12px; right: 12.5%; left: 12.5%; height: 2px; background: #cbd5e1; z-index: 1;">
                                        <div class="h-100" style="width: {{ $progressPercent }}%; background: {{ $progressColor }}; transition: width 0.4s ease;"></div>
                                    </div>

                                    <div class="row text-center g-0 position-relative" style="z-index: 2;">
                                        <div class="col-3">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1 text-white shadow-sm" style="width: 24px; height: 24px; font-size: 0.65rem; background: #2563eb; border: 2px solid #ffffff;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <div class="small fw-bold text-primary" style="font-size: 0.7rem;">تقديم</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1 text-white shadow-sm" style="width: 24px; height: 24px; font-size: 0.65rem; background: {{ $step >= 2 ? ($nomination->status == 'pending' ? '#f59e0b' : '#2563eb') : '#cbd5e1' }}; border: 2px solid #ffffff;">
                                                    <i class="fas {{ $step > 2 ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                                                </div>
                                                <div class="small fw-bold {{ $step >= 2 ? ($nomination->status == 'pending' ? 'text-warning' : 'text-primary') : 'text-muted' }}" style="font-size: 0.7rem;">فرز</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1 text-white shadow-sm" style="width: 24px; height: 24px; font-size: 0.65rem; background: {{ $step >= 3 ? ($nomination->status == 'interview_scheduled' ? '#0ea5e9' : '#2563eb') : '#cbd5e1' }}; border: 2px solid #ffffff;">
                                                    <i class="fas {{ $step > 3 ? 'fa-check' : 'fa-calendar-alt' }}"></i>
                                                </div>
                                                <div class="small fw-bold {{ $step >= 3 ? ($nomination->status == 'interview_scheduled' ? 'text-info' : 'text-primary') : 'text-muted' }}" style="font-size: 0.7rem;">مقابلة</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1 text-white shadow-sm" style="width: 24px; height: 24px; font-size: 0.65rem; background: {{ $step >= 4 ? ($nomination->status == 'accepted' ? '#10b981' : '#ef4444') : '#cbd5e1' }}; border: 2px solid #ffffff;">
                                                    <i class="fas {{ $nomination->status == 'accepted' ? 'fa-check' : 'fa-flag' }}"></i>
                                                </div>
                                                <div class="small fw-bold {{ $step >= 4 ? ($nomination->status == 'accepted' ? 'text-success' : 'text-danger') : 'text-muted' }}" style="font-size: 0.7rem;">قرار</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Interview Details Alert (if scheduled) -->
                            @if($nomination->interview_date)
                                <div class="p-2.5 px-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25 mb-3 text-dark small" style="font-size: 0.78rem;">
                                    <div class="fw-bold text-info mb-1">
                                        <i class="fas fa-calendar-check me-1"></i> موعد المقابلة الشخصية
                                    </div>
                                    <div><strong>التاريخ:</strong> {{ $nomination->interview_date->format('Y-m-d') }} @if($nomination->interview_time) ({{ $nomination->interview_time }}) @endif</div>
                                    @if($nomination->interview_location)
                                        <div><strong>المكان:</strong> {{ $nomination->interview_location }}</div>
                                    @endif
                                </div>
                            @endif

                            @if($nomination->notes)
                                <div class="small text-muted p-2 bg-light rounded-3 mb-2" style="font-size: 0.75rem;">
                                    <strong>ملاحظات:</strong> {{ $nomination->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white p-3 px-4 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($nomination->created_at)->locale('ar')->diffForHumans() }}
                            </small>
                            <a href="{{ route('graduate.job-opportunities.show', $nomination->job_opportunity_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 rounded-4 shadow-sm text-center py-5" style="background: #ffffff;">
            <div class="card-body py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد ترشيحات وظيفية حالياً</h4>
                <p class="text-muted mb-4 small">يمكنك تصفح فرص العمل المتاحة والتقديم المباشر على الفرص المناسبة لتخصصك ومؤهلاتك</p>
                <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold">
                    <i class="fas fa-search me-1"></i> استكشاف فرص العمل
                </a>
            </div>
        </div>
    @endif
</div>
</div>
@endsection