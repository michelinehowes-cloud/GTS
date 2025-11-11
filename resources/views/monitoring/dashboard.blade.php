@extends('layouts.app')

@section('title', 'لوحة تحكم التقييم والمتابعة')
@section('page-title', 'لوحة تحكم التقييم والمتابعة')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h3 class="text-primary">مرحباً بك، {{ auth()->user()->name }}</h3>
                <p class="lead">نظام متابعة وتقييم أداء الخريجين والبرامج</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            معدل التوظيف
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $kpis['employment_rate'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            معدل إكمال التدريب
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $kpis['training_completion_rate'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            رضا الشركات
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $kpis['company_satisfaction_rate'] ?? 0 }}%
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-smile"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="text-primary text-uppercase small fw-bold">
                            متوسط وقت التوظيف
                        </div>
                        <div class="h4 mb-0 fw-bold text-dark">
                            {{ $kpis['average_training_to_employment_days'] ?? 0 }} يوم
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">الاستبيانات النشطة</h5>
            </div>
            <div class="card-body">
                @if(isset($activeSurveys) && $activeSurveys->count() > 0)
                    <div class="list-group">
                        @foreach($activeSurveys as $survey)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $survey->title }}</h6>
                                    <p class="mb-1 text-muted">{{ $survey->description }}</p>
                                    <small class="text-muted">ينتهي في: {{ $survey->end_date }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $survey->target_audience }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">لا توجد استبيانات نشطة حالياً</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection