@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الإرشاد المهني')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم مسؤول الإرشاد المهني', 'active' => true],
        ]
    ])

    <div class="row mb-4">
        <div class="col-12">
            <div class="bento-card" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9)); border-left: 4px solid var(--bento-primary);">
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 70px; height: 70px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                        <i class="fas fa-compass fa-2x"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold text-white" style="letter-spacing: -0.5px;">مرحباً بك، {{ auth()->user()->name }} 👋</h2>
                        <p class="mb-0 text-white-50 fs-5"><i class="fas fa-tasks me-2"></i>مسؤول الإرشاد المهني</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة (Bento Grid) -->
    <div class="bento-grid-large mb-4">
        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">إجمالي الخريجين</h3>
                <div class="bento-card-icon bento-icon-primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $stats['totalGraduates'] ?? 0 }}</div>
            <div class="bento-desc">إجمالي الخريجين المسجلين</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">تم توظيفهم</h3>
                <div class="bento-card-icon bento-icon-success">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $stats['hiredGraduates'] ?? 0 }}</div>
            <div class="bento-desc">الخريجين الذين حصلوا على وظائف</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">يبحثون عن عمل</h3>
                <div class="bento-card-icon bento-icon-warning">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $stats['seekingGraduates'] ?? 0 }}</div>
            <div class="bento-desc">يحتاجون إلى توجيه وترشيحات</div>
        </div>

        <div class="bento-card">
            <div class="bento-card-header">
                <h3 class="bento-card-title">الترشيحات المقبولة</h3>
                <div class="bento-card-icon bento-icon-info">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="bento-stat">{{ $stats['acceptedNominations'] ?? 0 }}</div>
            <div class="bento-desc">{{ number_format(($stats['acceptedNominations'] / max($stats['totalNominations'] ?? 1, 1)) * 100, 1) }}% نسبة النجاح</div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row mb-4">
        <!-- الخريجين الجدد -->
        <div class="col-lg-6 mb-4">
            <div class="bento-card h-100">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h3 class="bento-card-title text-white fs-5 m-0">
                        <i class="fas fa-user-graduate me-2 text-primary"></i>
                        أحدث الخريجين المسجلين
                    </h3>
                    <a href="{{ route('career-guidance.graduates') }}" class="btn-bento-outline btn-sm py-1 px-3">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentGraduates) && $recentGraduates->count() > 0)
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($recentGraduates as $graduate)
                            <li class="list-group-item bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 45px; height: 45px;">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-white">{{ $graduate->first_name . ' ' . $graduate->last_name }}</h6>
                                            <small class="text-white-50">{{ $graduate->major ?? 'غير محدد' }}</small>
                                        </div>
                                    </div>
                                    <span class="bento-badge bento-badge-{{ $graduate->status == 'employed' ? 'success' : ($graduate->status == 'seeking_employment' ? 'warning' : 'secondary') }}">
                                        {{ $graduate->status == 'employed' ? 'موظف' : ($graduate->status == 'seeking_employment' ? 'يبحث عن عمل' : 'غير محدد') }}
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-white-50 mb-3 d-block"></i>
                            <p class="text-white-50">لا توجد بيانات خريجين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الترشيحات الحديثة -->
        <div class="col-lg-6 mb-4">
            <div class="bento-card h-100">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-3 d-flex justify-content-between align-items-center">
                    <h3 class="bento-card-title text-white fs-5 m-0">
                        <i class="fas fa-paper-plane me-2 text-success"></i>
                        أحدث الترشيحات
                    </h3>
                    <a href="{{ route('career-guidance.nominations') }}" class="btn-bento-outline btn-sm py-1 px-3">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentNominations) && $recentNominations->count() > 0)
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($recentNominations as $nomination)
                            <li class="list-group-item bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold text-white">{{ $nomination->graduate->name ?? 'N/A' }}</h6>
                                    <span class="bento-badge bento-badge-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ $nomination->status_text ?? $nomination->status }}
                                    </span>
                                </div>
                                <small class="text-white-50 d-block">
                                    <i class="fas fa-briefcase me-1"></i>
                                    {{ $nomination->jobOpportunity->title ?? 'N/A' }} - {{ $nomination->jobOpportunity->company->name ?? 'N/A' }}
                                </small>
                                <small class="text-white-50 d-block mt-1">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $nomination->created_at->diffForHumans() }}
                                </small>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane fa-3x text-white-50 mb-3 d-block"></i>
                            <p class="text-white-50">لا توجد ترشيحات حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bento-card">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-4">
                    <h3 class="bento-card-title text-white fs-4">
                        <i class="fas fa-bolt me-2 text-gold"></i>
                        إجراءات سريعة
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('career-guidance.graduates') }}" class="btn-bento-outline w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                                <i class="fas fa-users fa-2x mb-2 text-primary"></i>
                                <span>إدارة الخريجين</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('career-guidance.nominations') }}" class="btn-bento-outline w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100">
                                <i class="fas fa-paper-plane fa-2x mb-2 text-success"></i>
                                <span>الترشيحات</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('career-guidance.import.graduates.create') }}" class="btn-bento w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-file-import fa-2x mb-2"></i>
                                <span>استيراد بيانات خريجين</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="btn-bento-outline w-100 d-flex flex-column justify-content-center align-items-center py-3 text-decoration-none h-100" style="border-color: var(--bento-gold); color: var(--bento-gold);">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>التقارير</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
