@extends('layouts.app')

@section('title', 'التقارير والإحصائيات المتقدمة - منسق التدريب')
@section('page-title', 'التقارير والإحصائيات المتقدمة')

@push('styles')
<style>
    .analytics-stat-box {
        padding: 1.25rem 0.85rem;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.2s ease;
        height: 100%;
    }

    .analytics-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }

    .analytics-stat-val {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.35rem;
    }

    .analytics-stat-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.15rem;
    }

    .chart-box-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-container-wrap {
        position: relative;
        width: 100%;
        height: 240px;
        flex-grow: 1;
    }

    @media (max-width: 767.98px) {
        .analytics-stat-box {
            padding: 0.75rem 0.4rem !important;
        }

        .analytics-stat-val {
            font-size: 1.35rem !important;
        }

        .analytics-stat-title {
            font-size: 0.72rem !important;
        }

        .chart-box-card {
            padding: 1rem 0.85rem !important;
            margin-bottom: 1rem !important;
        }

        .chart-container-wrap {
            height: 210px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم منسق التدريب', 'url' => route('training-coordinator.dashboard')],
            ['label' => 'التقارير والإحصائيات المتقدمة', 'active' => true],
        ]
    ])

    {{-- رأس الصفحة --}}
    <div class="card-modern mb-4 p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-0 fs-5">التقارير ومؤشرات الأداء</h3>
                    <p class="text-muted small mb-0">تحليلات وإحصائيات البرامج التدريبية ومعدلات الإقبال والقبول</p>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap w-100 w-md-auto">
                <a href="#uploadReportSection" class="btn btn-primary-modern flex-grow-1 flex-md-grow-0 btn-sm">
                    <i class="fas fa-upload me-1"></i> رفع تقرير CSV
                </a>
                <a href="{{ route('training-coordinator.calendar') }}" class="btn btn-outline-primary flex-grow-1 flex-md-grow-0 btn-sm">
                    <i class="fas fa-calendar-alt me-1"></i> التقويم
                </a>
            </div>
        </div>
    </div>

    {{-- 📊 بطاقات الإحصائيات الأساسية (2x2 على الموبايل) --}}
    <div class="row mb-3">
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'إجمالي البرامج',
            'value' => $basicStats['totalTrainings'],
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'برامج نشطة',
            'value' => $basicStats['activeTrainings'],
            'icon' => 'fas fa-play-circle',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'إجمالي الطلبات',
            'value' => $basicStats['totalApplications'],
            'icon' => 'fas fa-users',
            'color' => 'info'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'طلبات معلقة',
            'value' => $basicStats['pendingApplications'],
            'icon' => 'fas fa-clock',
            'color' => 'warning'
        ])
    </div>

    {{-- 📈 المخططات البيانية المتقدمة --}}
    <div class="row">
        {{-- مخطط توزيع أنواع التدريبات --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="chart-box-card">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i> توزيع أنواع التدريبات
                    </h5>
                </div>
                <div class="chart-container-wrap">
                    <canvas id="trainingTypesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- مخطط حالات التدريبات --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="chart-box-card">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-chart-bar me-2 text-success"></i> حالات البرامج التدريبية
                    </h5>
                </div>
                <div class="chart-container-wrap">
                    <canvas id="trainingStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- مخطط توزيع طلبات التدريب --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="chart-box-card">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-chart-doughnut me-2 text-info"></i> توزيع طلبات التدريب
                    </h5>
                </div>
                <div class="chart-container-wrap">
                    <canvas id="applicationStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- مخطط التوجه الشهري --}}
        <div class="col-12 col-lg-6 mb-4">
            <div class="chart-box-card">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-chart-line me-2 text-warning"></i> التوجه الشهري للتدريبات
                    </h5>
                </div>
                <div class="chart-container-wrap">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- مخطط توزيع التدريبات حسب الشركات --}}
        <div class="col-12 mb-4">
            <div class="chart-box-card">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-building me-2 text-danger"></i> توزيع التدريبات حسب جهات ومكاتب التدريب
                    </h5>
                </div>
                <div class="chart-container-wrap" style="min-height: 260px;">
                    <canvas id="companyDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 🧠 التحليلات الذكية والاستنتاجات --}}
    <div class="row">
        {{-- التحليلات المتقدمة --}}
        <div class="col-12 col-lg-8 mb-4">
            <div class="card-modern p-3 p-md-4 h-100">
                <div class="border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-chart-network me-2 text-primary"></i> مؤشرات الأداء الرئيسية
                    </h5>
                </div>

                {{-- 3 مؤشرات رقمية تظهر بجانب بعضها بسلاسة --}}
                <div class="row g-2 text-center mb-4">
                    <div class="col-4">
                        <div class="analytics-stat-box">
                            <div class="analytics-stat-val text-primary">{{ $advancedAnalytics['approval_rate'] }}%</div>
                            <div class="analytics-stat-title">معدل القبول</div>
                            <small class="text-muted d-none d-sm-block" style="font-size: 0.7rem;">نسبة المقبولين</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="analytics-stat-box">
                            <div class="analytics-stat-val text-success">{{ $advancedAnalytics['avg_applicants'] }}</div>
                            <div class="analytics-stat-title">متوسط المتقدمين</div>
                            <small class="text-muted d-none d-sm-block" style="font-size: 0.7rem;">لكل برنامج</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="analytics-stat-box">
                            <div class="analytics-stat-val text-info">{{ $advancedAnalytics['overall_occupancy'] }}%</div>
                            <div class="analytics-stat-title">نسبة الإشغال</div>
                            <small class="text-muted d-none d-sm-block" style="font-size: 0.7rem;">المقاعد المحجوزة</small>
                        </div>
                    </div>
                </div>

                {{-- أكثر التدريبات طلباً --}}
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-fire me-1 text-danger"></i> أكثر البرامج التدريبية طلباً
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>اسم البرنامج</th>
                                <th>الطلبات</th>
                                <th>نسبة الإشغال</th>
                                <th>الإقبال</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advancedAnalytics['popular_trainings'] as $popularTraining)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark">{{ Str::limit($popularTraining['name'], 22) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border border-primary rounded-pill px-2 py-1">{{ $popularTraining['applications'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress mb-1" style="height: 6px; width: 80px;">
                                            <div class="progress-bar bg-{{ $popularTraining['occupancy_rate'] > 80 ? 'success' : ($popularTraining['occupancy_rate'] > 50 ? 'warning' : 'danger') }}"
                                                style="width: {{ $popularTraining['occupancy_rate'] }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted fw-bold" style="font-size: 0.72rem;">{{ $popularTraining['occupancy_rate'] }}%</small>
                                    </td>
                                    <td>
                                        @if($popularTraining['occupancy_rate'] > 80)
                                            <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size: 0.72rem;">مرتفع جداً</span>
                                        @elseif($popularTraining['occupancy_rate'] > 50)
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.72rem;">متوسط</span>
                                        @else
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.72rem;">اعتيادي</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">لا توجد بيانات كافية بعد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- الاستنتاجات الذكية --}}
        <div class="col-12 col-lg-4 mb-4">
            <div class="card-modern p-3 p-md-4 h-100">
                <div class="border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-lightbulb me-2 text-warning"></i> استنتاجات وتحليلات ذكية
                    </h5>
                </div>
                @foreach($advancedAnalytics['insights'] as $insight)
                    <div class="alert bg-light border-0 mb-3 p-3 rounded-3" style="border-right: 4px solid var(--bento-{{ $insight['type'] == 'info' ? 'primary' : $insight['type'] }}) !important;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-{{ $insight['icon'] }} text-{{ $insight['type'] }} mt-1"></i>
                            <div>
                                <h6 class="text-dark fw-bold mb-1 fs-6">{{ $insight['title'] }}</h6>
                                <p class="mb-0 text-muted small" style="font-size: 0.78rem;">{{ $insight['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 📂 قسم رفع تقارير CSV --}}
    <div class="card-modern mb-4 p-3 p-md-4" id="uploadReportSection">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <h5 class="fw-bold text-dark fs-6 mb-0">
                <i class="fas fa-file-upload me-2 text-primary"></i> استيراد ورفع تقارير CSV
            </h5>
        </div>

        <form action="{{ route('training-coordinator.reports.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <label for="report_type" class="form-label fw-medium small">نوع التقرير</label>
                    <select class="form-select" id="report_type" name="report_type" required>
                        <option value="">اختر نوع التقرير...</option>
                        <option value="training_programs">برامج التدريب</option>
                        <option value="training_applications">طلبات التدريب</option>
                        <option value="student_data">بيانات المتدربين</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 mb-3">
                    <label for="csv_file" class="form-label fw-medium small">ملف الـ CSV</label>
                    <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                </div>

                <div class="col-12 col-md-4 mb-3">
                    <label for="description" class="form-label fw-medium small">وصف اختياري</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="وصف مختصر للتقرير...">
                </div>
            </div>

            {{-- روابط تحميل القوالب الجاهزة --}}
            <div class="alert bg-light border-0 mb-3 p-3 rounded-3">
                <div class="fw-bold text-dark mb-2 small"><i class="fas fa-download me-1 text-primary"></i> تحميل قوالب CSV النموذجية:</div>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['training_programs' => 'برامج التدريب', 'training_applications' => 'طلبات التدريب', 'student_data' => 'بيانات المتدربين'] as $type => $label)
                        <a href="{{ route('training-coordinator.reports.download-template', ['type' => $type]) }}"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                            <i class="fas fa-file-csv me-1"></i> قالب {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary-modern px-4 w-100 w-md-auto" id="uploadBtn">
                    <i class="fas fa-upload me-1"></i> رفع وحفظ التقرير
                </button>
            </div>
        </form>
    </div>

    {{-- 📁 أرشيف التقارير المحفوظة --}}
    <div class="card-modern p-3 p-md-4">
        <div class="border-bottom pb-2 mb-3">
            <h5 class="fw-bold text-dark fs-6 mb-0">
                <i class="fas fa-folder-open me-2 text-success"></i> التقارير والملفات المحفوظة
            </h5>
        </div>

        @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th>اسم التقرير</th>
                            <th>النوع</th>
                            <th>الحجم</th>
                            <th>التاريخ</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-file-csv text-success fs-5"></i>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $report->report_name }}</span>
                                            @if($report->description)
                                                <small class="text-muted" style="font-size: 0.72rem;">{{ $report->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                        {{ $report->type_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ number_format($report->file_size / 1024, 1) }} KB</span>
                                </td>
                                <td>
                                    <span class="text-dark small d-block">{{ $report->created_at->format('Y-m-d') }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $report->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('training-coordinator.reports.download', $report->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="تحميل">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('training-coordinator.reports.delete', $report->id) }}"
                                            method="POST" class="d-inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"
                                                style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-file-csv fa-3x text-muted mb-2 opacity-50"></i>
                <h6 class="fw-bold text-dark">لا توجد تقارير محفوظة حتى الآن</h6>
                <p class="text-muted small mb-0">يمكنك رفع ملف CSV الأول باستخدام النموذج أعلاه</p>
            </div>
        @endif
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // خيارات المخططات الموحدة المتوافقة مع الموبايل
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { family: 'Tajawal', size: 11 }
                    }
                }
            }
        };

        // 1. مخطط توزيع أنواع التدريبات
        new Chart(document.getElementById('trainingTypesChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($advancedCharts['training_types']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($advancedCharts['training_types']['data']) !!},
                    backgroundColor: {!! json_encode($advancedCharts['training_types']['colors']) !!},
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: commonOptions
        });

        // 2. مخطط حالات التدريبات
        new Chart(document.getElementById('trainingStatusChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($advancedCharts['training_status']['labels']) !!},
                datasets: [{
                    label: 'عدد البرامج',
                    data: {!! json_encode($advancedCharts['training_status']['data']) !!},
                    backgroundColor: {!! json_encode($advancedCharts['training_status']['colors']) !!},
                    borderRadius: 6
                }]
            },
            options: {
                ...commonOptions,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // 3. مخطط توزيع طلبات التدريب
        new Chart(document.getElementById('applicationStatusChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($advancedCharts['application_status']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($advancedCharts['application_status']['data']) !!},
                    backgroundColor: {!! json_encode($advancedCharts['application_status']['colors']) !!},
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: commonOptions
        });

        // 4. مخطط التوجه الشهري
        new Chart(document.getElementById('monthlyTrendsChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($advancedCharts['monthly_trends']['labels']) !!},
                datasets: [{
                    label: 'عدد التدريبات',
                    data: {!! json_encode($advancedCharts['monthly_trends']['data']) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // 5. مخطط توزيع الشركات
        new Chart(document.getElementById('companyDistributionChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($advancedCharts['company_distribution']['labels']) !!},
                datasets: [{
                    label: 'عدد التدريبات',
                    data: {!! json_encode($advancedCharts['company_distribution']['data']) !!},
                    backgroundColor: {!! json_encode($advancedCharts['company_distribution']['colors']) !!},
                    borderRadius: 6
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });

        // مؤشر التحميل عند رفع ملف
        const uploadForm = document.getElementById('uploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function () {
                const btn = document.getElementById('uploadBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الرفع...';
                }
            });
        }
    });
</script>
@endsection