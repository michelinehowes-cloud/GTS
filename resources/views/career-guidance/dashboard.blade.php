@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الإرشاد المهني')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم مسؤول الإرشاد المهني', 'active' => true],
        ]
    ])

    <!-- الإحصائيات السريعة -->
    <div class="row mb-4">
        @include('components.stat-card', [
            'title' => 'إجمالي الخريجين',
            'value' => $stats['totalGraduates'] ?? 0,
            'icon' => 'fas fa-user-graduate',
            'color' => 'primary',
            'col' => 'col-6 col-md-3 mb-3',
            'description' => 'إجمالي الخريجين المسجلين'
        ])

        @include('components.stat-card', [
            'title' => 'خريجون تم توظيفهم',
            'value' => $stats['hiredGraduates'] ?? 0,
            'icon' => 'fas fa-briefcase',
            'color' => 'success',
            'col' => 'col-6 col-md-3 mb-3',
            'description' => 'الخريجين الذين حصلوا على وظائف'
        ])

        @include('components.stat-card', [
            'title' => 'يبحثون عن عمل',
            'value' => $stats['seekingGraduates'] ?? 0,
            'icon' => 'fas fa-search',
            'color' => 'warning',
            'col' => 'col-6 col-md-3 mb-3',
            'description' => 'يحتاجون إلى توجيه وترشيحات'
        ])

        @include('components.stat-card', [
            'title' => 'الترشيحات المقبولة',
            'value' => $stats['acceptedNominations'] ?? 0,
            'icon' => 'fas fa-check-circle',
            'color' => 'info',
            'col' => 'col-6 col-md-3 mb-3',
            'description' => number_format(($stats['acceptedNominations'] / max($stats['totalNominations'] ?? 1, 1)) * 100, 1) . '% نسبة النجاح'
        ])
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row mb-4">
        <!-- الخريجين الجدد -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-user-graduate me-2"></i>
                        أحدث الخريجين المسجلين
                    </h5>
                    <a href="{{ route('career-guidance.graduates') }}" class="btn btn-sm btn-outline-primary-modern">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentGraduates) && $recentGraduates->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentGraduates as $graduate)
                            <li class="list-group-item border-0 px-0">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $graduate->first_name . ' ' . $graduate->last_name }}</h6>
                                            <small class="text-muted">{{ $graduate->major ?? 'غير محدد' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $graduate->status == 'employed' ? 'success' : ($graduate->status == 'seeking_employment' ? 'warning' : 'secondary') }}">
                                        {{ $graduate->status == 'employed' ? 'موظف' : ($graduate->status == 'seeking_employment' ? 'يبحث عن عمل' : 'غير محدد') }}
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">لا توجد بيانات خريجين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الترشيحات الحديثة -->
        <div class="col-lg-6 mb-4">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        أحدث الترشيحات
                    </h5>
                    <a href="{{ route('career-guidance.nominations') }}" class="btn btn-sm btn-outline-success-modern">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentNominations) && $recentNominations->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentNominations as $nomination)
                            <li class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 font-weight-bold">{{ $nomination->graduate->name ?? 'N/A' }}</h6>
                                    <span class="badge bg-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ $nomination->status_text ?? $nomination->status }}
                                    </span>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-briefcase me-1"></i>
                                    {{ $nomination->jobOpportunity->title ?? 'N/A' }} - {{ $nomination->jobOpportunity->company->name ?? 'N/A' }}
                                </small>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $nomination->created_at->diffForHumans() }}
                                </small>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">لا توجد ترشيحات حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('career-guidance.graduates') }}" class="btn btn-outline-primary-modern w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>إدارة الخريجين</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('career-guidance.nominations') }}" class="btn btn-outline-success-modern w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-paper-plane fa-2x mb-2"></i>
                                <span>الترشيحات</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('career-guidance.import.graduates.create') }}" class="btn btn-outline-info-modern w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                                <i class="fas fa-file-import fa-2x mb-2"></i>
                                <span>استيراد بيانات خريجين</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-outline-warning-modern w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
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
