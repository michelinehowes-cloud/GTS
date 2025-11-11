@extends('layouts.app')

@section('title', 'التقارير المتقدمة والإحصائيات')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- مؤشر التحميل -->
    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">جاري تحميل البيانات...</span>
        </div>
        <p class="mt-2 text-muted">جاري تحميل البيانات والمخططات...</p>
    </div>

    <!-- رأس الصفحة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header text-center">
                <div class="row align-items-center">
                    <div class="col-md-8 text-md-start text-center">
                        <h1 class="h2 text-primary mb-2">
                            <i class="bi bi-graph-up-arrow me-3"></i>
                            التقارير المتقدمة والإحصائيات
                        </h1>
                        <p class="lead text-muted mb-0">تحليلات شاملة ومخططات بيانية تفاعلية لنظام الإرشاد المهني</p>
                    </div>
                    <div class="btn-group">
    <button class="btn btn-outline-primary" onclick="showExportOptions()">
        <i class="bi bi-download me-2"></i>تصدير تقرير
    </button>
    <button class="btn btn-primary" id="refreshBtn">
        <i class="bi bi-arrow-clockwise me-2"></i>تحديث البيانات
    </button>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- رسالة ترحيب عند عدم وجود بيانات -->
    @if($stats['totalGraduates'] == 0)
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>مرحباً!</strong> يبدو أن النظام جديد. 
        <a href="{{ route('admin.career-guidance.graduates.create') }}" class="alert-link">ابدأ بإضافة الخريجين</a> 
        أو 
        <a href="{{ route('admin.career-guidance.import.graduates') }}" class="alert-link">استورد بيانات الخريجين</a>.
    </div>
    @endif

    <!-- بطاقات الإحصائيات السريعة -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">إجمالي الخريجين</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['totalGraduates']) }}</h3>
                            <div class="stat-trend text-success">
                                <i class="bi bi-arrow-up-circle me-1"></i>
                                {{ $stats['newGraduatesThisMonth'] }} جديد هذا الشهر
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">الترشيحات النشطة</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['activeNominations']) }}</h3>
                            <div class="stat-trend text-info">
                                <i class="bi bi-graph-up me-1"></i>
                                {{ number_format($stats['successRate'], 1) }}% نسبة النجاح
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-success bg-opacity-10 text-success">
                                <i class="bi bi-send-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">خريجين موظفين</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['employedGraduates']) }}</h3>
                            <div class="stat-trend text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ number_format($stats['employmentRate'], 1) }}% معدل التوظيف
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-info bg-opacity-10 text-info">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">باحثين عن عمل</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['seekingOpportunities']) }}</h3>
                            <div class="stat-trend text-primary">
                                <i class="bi bi-search me-1"></i>
                                {{ number_format($stats['seekingRate'], 1) }}% من الخريجين
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-search-heart"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">فرص متاحة</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['availableOpportunities']) }}</h3>
                            <div class="stat-trend text-info">
                                <i class="bi bi-plus-circle me-1"></i>
                                {{ $stats['newOpportunitiesThisWeek'] }} جديدة هذا الأسبوع
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-briefcase"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card border-start border-start-4 border-start-secondary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title text-muted small fw-bold mb-2">معدل النجاح</h6>
                            <h3 class="card-value text-dark mb-2">{{ number_format($stats['overallSuccessRate'], 1) }}%</h3>
                            <div class="stat-trend text-success">
                                <i class="bi bi-trophy me-1"></i>
                                أداء النظام
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="icon-circle bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-award-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الصف الأول من المخططات -->
    <div class="row mb-4">
        <!-- توزيع الخريجين حسب التخصص -->
        <div class="col-xl-6 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-mortarboard-fill me-2 text-primary"></i>
                        توزيع الخريجين حسب التخصص
                    </h5>
                    <div class="chart-actions">
                        <div class="dropdown filter-dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel me-1"></i>عرض حسب
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="updateChart('majors', 'count')">عدد الخريجين</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateChart('majors', 'employment')">معدل التوظيف</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('majorsChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="majorsChart"></canvas>
                        <div id="majorsFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-bar-chart fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حالة التوظيف للخريجين -->
        <div class="col-xl-6 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-briefcase-fill me-2 text-success"></i>
                        حالة التوظيف للخريجين
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('employmentChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="employmentChart"></canvas>
                        <div id="employmentFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-pie-chart fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الصف الثاني من المخططات -->
    <div class="row mb-4">
        <!-- توزيع الترشيحات حسب الحالة -->
        <div class="col-xl-6 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-send-check me-2 text-info"></i>
                        توزيع الترشيحات حسب الحالة
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('nominationsChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="nominationsChart"></canvas>
                        <div id="nominationsFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-pie-chart fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أداء الترشيحات الشهري -->
        <div class="col-xl-6 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-bar-chart-fill me-2 text-warning"></i>
                        أداء الترشيحات الشهري
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('monthlyPerformanceChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyPerformanceChart"></canvas>
                        <div id="monthlyPerformanceFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-graph-up fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الصف الثالث: مخططات متقدمة -->
    <div class="row mb-4">
        <!-- معدل نجاح الترشيحات حسب التخصص -->
        <div class="col-xl-8 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-speedometer2 me-2 text-danger"></i>
                        معدل نجاح الترشيحات حسب التخصص
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('successByMajorChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="successByMajorChart"></canvas>
                        <div id="successByMajorFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-radar fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع فرص العمل -->
        <div class="col-xl-4 mb-4">
            <div class="card stat-card chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-building me-2 text-secondary"></i>
                        توزيع فرص العمل
                    </h5>
                    <div class="chart-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('opportunitiesChart')">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="opportunitiesChart"></canvas>
                        <div id="opportunitiesFallback" class="fallback-message" style="display: none;">
                            <div>
                                <i class="bi bi-pie-chart fs-1 text-muted mb-3"></i>
                                <p class="mb-0">لا توجد بيانات كافية لعرض المخطط</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- استنتاجات وتحليلات -->
    <div class="row">
        <div class="col-12">
            <div class="card insight-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white mb-0 fw-bold">
                            <i class="bi bi-lightbulb-fill me-2"></i>
                            استنتاجات وتحليلات ذكية
                        </h5>
                        <span class="badge bg-white text-primary fs-6">{{ count($insights) }} تحليل</span>
                    </div>
                    <div class="row">
                        @forelse($insights as $insight)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="bg-white bg-opacity-10 rounded p-3 h-100">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-{{ $insight['icon'] }} text-{{ $insight['color'] }} fs-3"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-white mb-2 fw-bold">{{ $insight['title'] }}</h6>
                                        <p class="text-white-50 mb-0 small lh-base">{{ $insight['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-lightbulb text-white-50 fs-1 mb-3"></i>
                            <p class="text-white-50 mb-0">لا توجد استنتاجات متاحة حالياً</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم تحقق من الأخطاء -->
    @if(empty($chartData) || !is_array($chartData))
    <div class="alert alert-warning mt-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>تنبيه:</strong> لا توجد بيانات كافية لعرض الرسوم البيانية. 
        تأكد من وجود بيانات الخريجين والترشيحات في النظام.
    </div>
    @endif

    <!-- قسم تصحيح الأخطاء (للطور فقط) -->
    @if(app()->environment('local'))
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h6 class="mb-0">
                <i class="bi bi-bug me-2"></i>
                معلومات التصحيح (للطور فقط)
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>إحصائيات البيانات:</h6>
                    <ul class="list-unstyled">
                        <li><strong>عدد الخريجين:</strong> {{ $stats['totalGraduates'] ?? 0 }}</li>
                        <li><strong>عدد الترشيحات:</strong> {{ $stats['activeNominations'] ?? 0 }}</li>
                        <li><strong>عدد فرص العمل:</strong> {{ $stats['availableOpportunities'] ?? 0 }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>حالة المخططات:</h6>
                    <ul class="list-unstyled">
                        @foreach($chartData as $key => $data)
                        <li><strong>{{ $key }}:</strong> {{ isset($data['labels']) ? count($data['labels']) : 0 }} عنصر</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    window.routes = {
        pdf: "{{ route('admin.career-guidance.export-reports.pdf') }}",
        excel: "{{ route('admin.career-guidance.export-reports.excel') }}"
    };
    window.chartData = @json($chartData);
</script>
@endpush
