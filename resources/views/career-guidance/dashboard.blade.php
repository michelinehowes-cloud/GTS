@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الإرشاد المهني')

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم مسؤول الإرشاد المهني', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-compass me-2"></i>منظومة الإرشاد والتوجيه المهني
            </h2>
            <div class="text-muted small mt-1">مرحباً بك، <strong class="text-dark">{{ auth()->user()->name }}</strong> &bull; إدارة شؤون الخريجين والترشيحات والتوظيف</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($prefix . '.graduates.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-plus me-1"></i> إضافة خريج
            </a>
            <a href="{{ route($prefix . '.nominations.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-paper-plane me-1"></i> ترشيح جديد
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (4 Columns) -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">إجمالي الخريجين</div>
                        <div class="fs-4 fw-bold text-primary mb-0">{{ $stats['totalGraduates'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">المسجلين في النظام</div>
                    </div>
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">تم توظيفهم</div>
                        <div class="fs-4 fw-bold text-success mb-0">{{ $stats['employedGraduates'] ?? ($stats['hiredGraduates'] ?? 0) }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">الحاصلين على وظائف</div>
                    </div>
                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">يبحثون عن عمل</div>
                        <div class="fs-4 fw-bold text-warning mb-0">{{ $stats['seekingOpportunities'] ?? ($stats['seekingGraduates'] ?? 0) }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">بانتظار فرص وتوجيه</div>
                    </div>
                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-search fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">إجمالي الترشيحات</div>
                        <div class="fs-4 fw-bold text-info mb-0">{{ $stats['totalNominations'] ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.75rem;">{{ $stats['acceptedNominations'] ?? 0 }} مقبولاً وموظفاً</div>
                    </div>
                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 mb-4">
        <!-- الخريجين الجدد -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-user-graduate me-2"></i>أحدث الخريجين المسجلين
                    </h5>
                    <a href="{{ route($prefix . '.graduates') }}" class="btn btn-sm btn-outline-info-modern">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentGraduates) && $recentGraduates->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentGraduates as $graduate)
                            <div class="list-group-item bg-transparent px-4 py-3 d-flex justify-content-between align-items-center border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: 700;">
                                        {{ mb_substr($graduate->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route($prefix . '.graduates.show', $graduate->id) }}" class="text-dark fw-bold text-decoration-none d-block">
                                            {{ $graduate->name }}
                                        </a>
                                        <small class="text-muted">
                                            <i class="fas fa-graduation-cap me-1"></i>{{ $graduate->major ?? 'غير محدد' }} &bull; سنة {{ $graduate->graduation_year }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    @if($graduate->employment_status == 'employed')
                                        <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">موظف</span>
                                    @elseif($graduate->employment_status == 'seeking_opportunities')
                                        <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">باحث عن عمل</span>
                                    @elseif($graduate->employment_status == 'unemployed')
                                        <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">عاطل عن العمل</span>
                                    @else
                                        <span class="badge bg-light text-info border border-info rounded-pill px-3 py-1">مستكمل للدراسة</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-graduate display-4 text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0">لا توجد بيانات خريجين مسجلة حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الترشيحات الحديثة -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-paper-plane me-2"></i>أحدث الترشيحات لفرص العمل
                    </h5>
                    <a href="{{ route($prefix . '.nominations') }}" class="btn btn-sm btn-outline-info-modern">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentNominations) && $recentNominations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentNominations as $nomination)
                            <div class="list-group-item bg-transparent px-4 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <a href="{{ route($prefix . '.nominations.show', $nomination->id) }}" class="text-dark fw-bold text-decoration-none">
                                        {{ $nomination->graduate->name ?? 'غير محدد' }}
                                    </a>
                                    @if($nomination->status == 'accepted')
                                        <span class="badge bg-light text-success border border-success rounded-pill px-3 py-1">مقبول</span>
                                    @elseif($nomination->status == 'rejected')
                                        <span class="badge bg-light text-danger border border-danger rounded-pill px-3 py-1">مرفوض</span>
                                    @elseif($nomination->status == 'pending')
                                        <span class="badge bg-light text-warning border border-warning rounded-pill px-3 py-1">قيد المراجعة</span>
                                    @else
                                        <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">{{ $nomination->status_text ?? $nomination->status }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted mb-1">
                                    <i class="fas fa-briefcase me-1 text-primary"></i>
                                    {{ $nomination->jobOpportunity->title ?? 'فرصة غير محددة' }} &bull;
                                    <span class="text-dark">{{ $nomination->jobOpportunity->company->name ?? 'شركة غير محددة' }}</span>
                                </div>
                                <div class="small text-muted d-flex align-items-center gap-3">
                                    <span><i class="far fa-calendar-alt me-1"></i>{{ $nomination->created_at ? $nomination->created_at->format('Y-m-d') : '--' }}</span>
                                    @if($nomination->nominator)
                                        <span><i class="fas fa-user-tie me-1"></i>المرشح: {{ $nomination->nominator->name }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-paper-plane display-4 text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-0">لا توجد ترشيحات مسجلة حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Section -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-bolt me-2"></i>روابط الوصول السريع
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route($prefix . '.graduates') }}" class="btn btn-outline-info-modern w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                        <i class="fas fa-users-graduate fa-2x mb-2"></i>
                        <span class="fw-bold">إدارة بيانات الخريجين</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route($prefix . '.nominations') }}" class="btn btn-outline-primary-modern w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                        <i class="fas fa-paper-plane fa-2x mb-2"></i>
                        <span class="fw-bold">إدارة الترشيحات</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route($prefix . '.import.graduates.create') }}" class="btn btn-outline-secondary w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                        <i class="fas fa-file-excel fa-2x mb-2"></i>
                        <span class="fw-bold">استيراد من Excel</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route($prefix . '.advanced-reports') }}" class="btn btn-outline-warning-modern w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <span class="fw-bold">التقارير المتقدمة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


