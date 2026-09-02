@extends('layouts.app')

@section('title', 'التقارير المتقدمة والإحصائيات')

@php
    $prefix = request()->routeIs('admin.*') ? 'admin.career-guidance' : 'career-guidance';
@endphp

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة الإرشاد المهني', 'url' => route($prefix . '.dashboard')],
            ['label' => 'التقارير المتقدمة والإحصائيات', 'active' => true],
        ]
    ])

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="text-primary fw-bold mb-0">
                <i class="fas fa-chart-line me-2"></i>لوحة القيادة والتقارير المتقدمة
            </h2>
            <div class="text-muted small mt-1">نظرة شاملة وتحليلات دقيقة لأداء نظام الإرشاد المهني والترشيحات</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route($prefix . '.export-reports.pdf', request()->all()) }}" target="_blank"
                class="btn btn-outline-danger-modern">
                <i class="fas fa-file-pdf me-1"></i> تصدير PDF
            </a>
            <a href="{{ route($prefix . '.export-reports.excel', request()->all()) }}" target="_blank"
                class="btn btn-outline-success">
                <i class="fas fa-file-excel me-1"></i> تصدير Excel
            </a>
            <button class="btn btn-outline-primary-modern" id="refreshBtn" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i> تحديث
            </button>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-filter me-2"></i>تصفية البيانات
            </h5>
        </div>
        <div class="card-body p-3">
            <form action="{{ auth()->user()->role == 'career_guidance_officer' ? route('career-guidance.advanced-reports') : (request()->routeIs('admin.*') ? route('admin.career-guidance.advanced-reports') : route('evaluation-followup.career-guidance-advanced-reports')) }}"
                method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="major" class="form-label-modern">التخصص</label>
                        <select class="form-select-modern" name="major" id="major">
                            <option value="">كل التخصصات</option>
                            @foreach($majors ?? [] as $major)
                                <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>{{ $major }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label-modern">سنة التخرج</label>
                        <select class="form-select-modern" name="year" id="year">
                            <option value="">كل السنوات</option>
                            @foreach($years ?? [] as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label-modern">حالة التوظيف</label>
                        <select class="form-select-modern" name="status" id="status">
                            <option value="">الكل</option>
                            <option value="employed" {{ request('status') == 'employed' ? 'selected' : '' }}>موظف</option>
                            <option value="seeking_opportunities" {{ request('status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary-modern w-100">
                            <i class="fas fa-search me-1"></i> تطبيق الفلاتر
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Empty alert if no data -->
    @if($stats['totalGraduates'] == 0)
        <div class="alert alert-info alert-dismissible fade show border-0 rounded-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">مرحباً بك في النظام!</h6>
                    <p class="mb-0 small">
                        يبدو أن قاعدة البيانات فارغة حالياً. يمكنك
                        <a href="{{ route($prefix . '.graduates.create') }}" class="alert-link fw-bold text-decoration-underline">إضافة خريج جديد</a>
                        أو
                        <a href="{{ route($prefix . '.import.graduates.create') }}" class="alert-link fw-bold text-decoration-underline">استيراد بيانات الخريجين</a>
                        للبدء.
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Key Performance Indicators (4 Columns) -->
    <div class="row g-3 mb-4">
        <!-- إجمالي الخريجين -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">إجمالي الخريجين</div>
                        <div class="fs-4 fw-bold text-primary mb-0">{{ number_format($stats['totalGraduates']) }}</div>
                        <div class="text-success small" style="font-size: 0.75rem;">
                            <i class="fas fa-arrow-up me-1"></i>+{{ $stats['newGraduatesThisMonth'] }} هذا الشهر
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الخريجين الموظفين -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">تم توظيفهم</div>
                        <div class="fs-4 fw-bold text-success mb-0">{{ number_format($stats['employedGraduates']) }}</div>
                        <div class="text-success small" style="font-size: 0.75rem;">
                            <i class="fas fa-check-circle me-1"></i>{{ number_format($stats['employmentRate'], 1) }}% معدل التوظيف
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-briefcase fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الباحثين عن عمل -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">باحثين عن عمل</div>
                        <div class="fs-4 fw-bold text-warning mb-0">{{ number_format($stats['seekingOpportunities']) }}</div>
                        <div class="text-warning small" style="font-size: 0.75rem;">
                            <i class="fas fa-search me-1"></i>{{ number_format($stats['seekingRate'], 1) }}% من الإجمالي
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-user-clock fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الفرص المتاحة -->
        <div class="col-xl-3 col-sm-6">
            <div class="card-modern p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small mb-1">فرص متاحة</div>
                        <div class="fs-4 fw-bold text-info mb-0">{{ number_format($stats['availableOpportunities']) }}</div>
                        <div class="text-info small" style="font-size: 0.75rem;">
                            <i class="fas fa-plus-circle me-1"></i>+{{ $stats['newOpportunitiesThisWeek'] }} جديدة هذا الأسبوع
                        </div>
                    </div>
                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-building fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Status Distribution -->
        <div class="col-lg-8">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-chart-pie me-2"></i>توزيع حالة الخريجين
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 320px;">
                        <canvas id="graduatesStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Performance Rate -->
        <div class="col-lg-4">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-primary fw-bold">
                        <i class="fas fa-chart-bar me-2"></i>مؤشرات الأداء
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 320px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Insights Card -->
    <div class="card-modern mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary fw-bold">
                <i class="fas fa-lightbulb me-2 text-warning"></i>استنتاجات وتحليلات ذكية
            </h5>
            <span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-1">
                {{ count($insights) }} تحليل
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @forelse($insights as $insight)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 bg-light rounded-3 h-100 border border-light">
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="fas fa-{{ $insight['icon'] ?? 'info-circle' }}"></i>
                                </div>
                                <div>
                                    <h6 class="text-dark mb-1 fw-bold">{{ $insight['title'] }}</h6>
                                    <p class="text-muted mb-0 small">{{ $insight['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-chart-pie display-4 text-muted mb-3 opacity-50"></i>
                        <p class="text-muted mb-0">جاري جمع البيانات لتحليلها...</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Tajawal', sans-serif";
    Chart.defaults.color = '#858796';

    const stats = @json($stats);

    // 1. Doughnut Chart
    const ctxStatus = document.getElementById('graduatesStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['موظفون', 'باحثون عن عمل', 'غير محدد'],
            datasets: [{
                data: [
                    stats.employedGraduates,
                    stats.seekingOpportunities,
                    Math.max(0, stats.totalGraduates - (stats.employedGraduates + stats.seekingOpportunities))
                ],
                backgroundColor: ['#1cc88a', '#f6c23e', '#858796'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#60616f'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                }
            },
            cutout: '75%',
        },
    });

    // 2. Bar Chart
    const ctxPerformance = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctxPerformance, {
        type: 'bar',
        data: {
            labels: ['معدل التوظيف', 'نسبة النجاح'],
            datasets: [{
                label: 'النسبة المئوية',
                data: [stats.employmentRate, stats.successRate],
                backgroundColor: ['#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#17a673', '#2c9faf'],
                borderRadius: 8,
                maxBarThickness: 45,
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function (value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        },
    });
</script>
@endsection