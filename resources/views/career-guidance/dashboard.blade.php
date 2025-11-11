@extends('layouts.app')

@section('title', 'لوحة تحكم مسؤول الإرشاد المهني')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-4">
                <h1 class="page-title">
                    <i class="fas fa-chart-line me-2"></i>
                    لوحة تحكم مسؤول الإرشاد المهني
                </h1>
                <div class="page-subtitle">نظرة عامة على إحصائيات الخريجين والترشيحات</div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات السريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">إجمالي الخريجين</h5>
                            <h2 class="card-value">{{ $stats['totalGraduates'] }}</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            محدث الآن
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">خريجين موظفين</h5>
                            <h2 class="card-value">{{ $stats['employedGraduates'] }}</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-success">\
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            {{ number_format(($stats['employedGraduates'] / max($stats['totalGraduates'], 1)) * 100, 1) }}% من إجمالي الخريجين
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">باحثين عن فرص</h5>
                            <h2 class="card-value">{{ $stats['seekingOpportunities'] }}</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            يحتاجون إلى توجيه وترشيحات
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title">ترشيحات مقبولة</h5>
                            <h2 class="card-value">{{ $stats['acceptedNominations'] }}</h2>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">
                            {{ number_format(($stats['acceptedNominations'] / max($stats['totalNominations'], 1)) * 100, 1) }}% نجاح
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row">
        <!-- الخريجين الجدد -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user-graduate me-2"></i>
                        أحدث الخريجين المسجلين
                    </h5>
                    <a href="{{ route('career-guidance.graduates') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recentGraduates->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentGraduates as $graduate)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $graduate->name }}</h6>
                                    <small class="text-muted">
                                        {{ $graduate->major }} - {{ $graduate->graduation_year }}
                                    </small>
                                </div>
                                <span class="badge bg-{{ $graduate->employment_status == 'employed' ? 'success' : ($graduate->employment_status == 'seeking_opportunities' ? 'warning' : 'secondary') }}">
                                    {{ $graduate->employment_status == 'employed' ? 'موظف' : ($graduate->employment_status == 'seeking_opportunities' ? 'باحث عن عمل' : 'غير موظف') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد بيانات خريجين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الترشيحات الحديثة -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-paper-plane me-2"></i>
                        أحدث الترشيحات
                    </h5>
<a href="{{ route('career-guidance.nominations') }}" class="btn btn-outline-success w-100">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    @if($recentNominations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentNominations as $nomination)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $nomination->graduate->name }}</h6>
                                    <span class="badge bg-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'pending' ? 'warning' : 'secondary') }}">
                                        {{ $nomination->status_text }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ $nomination->jobOpportunity->title }} - {{ $nomination->jobOpportunity->company->name }}
                                </small>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        {{ $nomination->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد ترشيحات حديثة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- إجراءات سريعة -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-bolt me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">\
                            <a href="{{ route('career-guidance.graduates') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users me-2"></i>
                                إدارة الخريجين
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
<a href="{{ route('career-guidance.nominations') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>
                                الترشيحات
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
<a href="{{ route('career-guidance.nominations') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>
                                الترشيحات
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>
                                التقارير
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
