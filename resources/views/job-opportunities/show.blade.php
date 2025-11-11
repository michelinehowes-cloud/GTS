@extends('layouts.app')

@section('title', 'تفاصيل فرصة العمل - ' . $opportunity->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- بطاقة العنوان الرئيسية -->
            <div class="card shadow-lg border-0 mb-4" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                        <i class="fas fa-briefcase fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-4">
                                    <h1 class="h2 mb-2 fw-bold">{{ $opportunity->title }}</h1>
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <span class="badge bg-white bg-opacity-20 text-white fs-6 border-0">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $opportunity->company->name }}
                                        </span>
                                        <span class="badge bg-white bg-opacity-20 text-white fs-6 border-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $opportunity->location }}
                                        </span>
                                        <span class="badge bg-white bg-opacity-20 text-white fs-6 border-0">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $opportunity->seats }} مقاعد
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group-vertical w-100">
                                <a href="{{ route('job-opportunities.nominations', $opportunity->id) }}" 
                                   class="btn btn-light btn-lg mb-2 text-primary fw-bold">
                                    <i class="fas fa-users me-2"></i>الترشيحات ({{ $nominationsCount['total'] }})
                                </a>
                                <a href="{{ route('job-opportunities.edit', $opportunity->id) }}" 
                                   class="btn btn-warning btn-lg mb-2 fw-bold">
                                    <i class="fas fa-edit me-2"></i>تعديل الفرصة
                                </a>
                                <a href="{{ route('job-opportunities.index') }}" 
                                   class="btn btn-outline-light">
                                    <i class="fas fa-arrow-right me-2"></i>رجوع للقائمة
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">تم بنجاح!</h5>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <div class="row">
                <!-- العمود الأيسر - المعلومات الرئيسية -->
                <div class="col-lg-8">
                    <!-- بطاقة الوصف -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h4 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-file-alt me-2"></i>وصف الفرصة
                            </h4>
                        </div>
                        <div class="card-body">
                            <p class="lead mb-0 text-dark">{{ $opportunity->description }}</p>
                        </div>
                    </div>

                    <!-- بطاقة المتطلبات -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h4 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-tasks me-2"></i>المتطلبات والمهارات
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($opportunity->required_specializations)
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3 fw-semibold">
                                        <i class="fas fa-graduation-cap me-2"></i>التخصصات المطلوبة
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($opportunity->required_specializations as $specialization)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3 fw-semibold">
                                                {{ $specialization }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($opportunity->required_skills)
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3 fw-semibold">
                                        <i class="fas fa-cogs me-2"></i>المهارات المطلوبة
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($opportunity->required_skills as $skill)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 py-2 px-3 fw-semibold">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($opportunity->required_experience)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-muted mb-2 fw-semibold">
                                        <i class="fas fa-briefcase me-2"></i>الخبرة المطلوبة
                                    </h6>
                                    <p class="mb-0 text-dark">{{ $opportunity->required_experience }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- بطاقة المتطلبات العامة -->
                    @if($opportunity->requirements)
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h4 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-clipboard-list me-2"></i>المتطلبات العامة
                            </h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-dark">{{ $opportunity->requirements }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- العمود الأيمن - المعلومات الجانبية -->
                <div class="col-lg-4">
                    <!-- بطاقة التواريخ -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-calendar-alt me-2"></i>التواريخ المهمة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-success fw-semibold">تاريخ البدء</h6>
                                        <p class="mb-0 text-dark">{{ $opportunity->start_date->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-info fw-semibold">تاريخ الانتهاء</h6>
                                        <p class="mb-0 text-dark">{{ $opportunity->end_date->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-danger fw-semibold">آخر موعد للتقديم</h6>
                                        <p class="mb-0 text-dark">{{ $opportunity->application_deadline->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة الإحصائيات -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-chart-pie me-2"></i>إحصائيات الترشيحات
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="stats-grid">
                                <div class="stat-item text-center p-3 bg-primary bg-opacity-10 rounded-3 mb-3">
                                    <h3 class="text-primary mb-1 fw-bold">{{ $nominationsCount['total'] }}</h3>
                                    <small class="text-muted fw-semibold">إجمالي الترشيحات</small>
                                </div>
                                <div class="stat-item text-center p-3 bg-success bg-opacity-10 rounded-3 mb-3">
                                    <h3 class="text-success mb-1 fw-bold">{{ $nominationsCount['accepted'] }}</h3>
                                    <small class="text-muted fw-semibold">مقبولة</small>
                                </div>
                                <div class="stat-item text-center p-3 bg-warning bg-opacity-10 rounded-3 mb-3">
                                    <h3 class="text-warning mb-1 fw-bold">{{ $nominationsCount['pending'] }}</h3>
                                    <small class="text-muted fw-semibold">قيد المراجعة</small>
                                </div>
                                <div class="stat-item text-center p-3 bg-danger bg-opacity-10 rounded-3">
                                    <h3 class="text-danger mb-1 fw-bold">{{ $nominationsCount['rejected'] }}</h3>
                                    <small class="text-muted fw-semibold">مرفوضة</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بطاقة المزايا -->
                    @if($opportunity->salary || $opportunity->benefits)
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-transparent border-0 py-3">
                            <h5 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-gift me-2"></i>المزايا والعوائد
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($opportunity->salary)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-success bg-opacity-10 rounded-3">
                                <span class="text-success fw-semibold">
                                    <i class="fas fa-money-bill-wave me-2"></i>الراتب
                                </span>
                                <strong class="text-success fs-5">{{ number_format($opportunity->salary) }} د.ل</strong>
                            </div>
                            @endif

                            @if($opportunity->benefits)
                            <div class="p-3 bg-info bg-opacity-10 rounded-3">
                                <h6 class="text-info mb-2 fw-semibold">
                                    <i class="fas fa-star me-2"></i>المزايا الإضافية
                                </h6>
                                <p class="mb-0 text-dark">{{ $opportunity->benefits }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #2c5aa0;
    --primary-dark: #1e3a8a;
    --secondary-color: #f59e0b;
    --success-color: #10b981;
    --info-color: #3b82f6;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-bottom: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.card {
    border-radius: 15px;
    border: none;
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.badge {
    border-radius: 8px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 12px;
    border: none;
}

.bg-primary { background-color: var(--primary-color) !important; }
.bg-success { background-color: var(--success-color) !important; }
.bg-info { background-color: var(--info-color) !important; }
.bg-warning { background-color: var(--warning-color) !important; }
.bg-danger { background-color: var(--danger-color) !important; }

.text-primary { color: var(--primary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-info { color: var(--info-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }

.btn-primary { 
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

.shadow-lg {
    box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
}

.shadow-sm {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}

.fw-semibold {
    font-weight: 600;
}
</style>
@endsection