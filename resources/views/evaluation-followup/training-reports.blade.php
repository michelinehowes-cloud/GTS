@extends('layouts.app')

@section('title', 'تقارير التدريب - تقييم ومتابعة')

@section('page-title', 'تقارير التدريب')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>إحصائيات التدريب الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-info text-center">
                            <h4 class="alert-heading">{{ $basicStats['totalTrainings'] ?? 0 }}</h4>
                            <p class="mb-0">إجمالي برامج التدريب</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-success text-center">
                            <h4 class="alert-heading">{{ $basicStats['activeTrainings'] ?? 0 }}</h4>
                            <p class="mb-0">برامج تدريب نشطة</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-primary text-center">
                            <h4 class="alert-heading">{{ $basicStats['totalApplications'] ?? 0 }}</h4>
                            <p class="mb-0">إجمالي طلبات التدريب</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-warning text-center">
                            <h4 class="alert-heading">{{ $basicStats['pendingApplications'] ?? 0 }}</h4>
                            <p class="mb-0">طلبات قيد الانتظار</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>توزيع أنواع التدريبات
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trainingTypesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>حالات التدريبات
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trainingStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2"></i>حالات طلبات التدريب
                </h5>
            </div>
            <div class="card-body">
                <canvas id="applicationStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>اتجاهات التدريبات الشهرية
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrainingsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-percent me-2"></i>معدلات الإشغال
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($advancedCharts['occupancy_rates'] ?? [] as $rate)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $rate['training'] }}
                            <span class="badge bg-info rounded-pill">{{ round($rate['rate'], 1) }}%</span>
                        </li>
                    @empty
                        <li class="list-group-item">لا توجد بيانات لمعدلات الإشغال.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>توزيع الشركات حسب التدريبات
                </h5>
            </div>
            <div class="card-body">
                <canvas id="companyDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>تحليلات واستنتاجات متقدمة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-secondary text-center">
                            <h4 class="alert-heading">{{ $advancedAnalytics['approval_rate'] ?? 0 }}%</h4>
                            <p class="mb-0">معدل القبول</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-secondary text-center">
                            <h4 class="alert-heading">{{ $advancedAnalytics['avg_applicants'] ?? 0 }}</h4>
                            <p class="mb-0">متوسط المتقدمين لكل تدريب</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-secondary text-center">
                            <h4 class="alert-heading">{{ $advancedAnalytics['overall_occupancy'] ?? 0 }}%</h4>
                            <p class="mb-0">نسبة الإشغال الإجمالية</p>
                        </div>
                    </div>
                </div>
                <h6>أكثر التدريبات طلباً:</h6>
                <ul class="list-group mb-3">
                    @forelse($advancedAnalytics['popular_trainings'] ?? [] as $training)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $training['name'] }} (طلبات: {{ $training['applications'] }}, إشغال: {{ $training['occupancy_rate'] }}%)
                        </li>
                    @empty
                        <li class="list-group-item">لا توجد تدريبات شائعة.</li>
                    @endforelse
                </ul>
                <h6>استنتاجات:</h6>
                @forelse($advancedAnalytics['insights'] ?? [] as $insight)
                    <div class="alert alert-{{ $insight['type'] }}">
                        <i class="fas fa-{{ $insight['icon'] }} me-2"></i>
                        <strong>{{ $insight['title'] }}:</strong> {{ $insight['description'] }}
                    </div>
                @empty
                    <div class="alert alert-info">لا توجد استنتاجات متاحة.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-export me-2"></i>تصدير التقارير
                </h5>
            </div>
            <div class="card-body">
                <p>يمكنك تصدير التقارير المتاحة بصيغ مختلفة:</p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>تصدير إلى Excel
                    </a>
                    <a href="#" class="btn btn-danger">
                        <i class="fas fa-file-pdf me-2"></i>تصدير إلى PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Training Types Chart
        var trainingTypesCtx = document.getElementById('trainingTypesChart').getContext('2d');
        new Chart(trainingTypesCtx, {
            type: 'pie',
            data: {
                labels: @json($advancedCharts['training_types']['labels'] ?? []),
                datasets: [{
                    data: @json($advancedCharts['training_types']['data'] ?? []),
                    backgroundColor: @json($advancedCharts['training_types']['colors'] ?? []),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'توزيع أنواع التدريبات'
                    }
                }
            }
        });

        // Training Status Chart
        var trainingStatusCtx = document.getElementById('trainingStatusChart').getContext('2d');
        new Chart(trainingStatusCtx, {
            type: 'bar',
            data: {
                labels: @json($advancedCharts['training_status']['labels'] ?? []),
                datasets: [{
                    label: 'عدد التدريبات',
                    data: @json($advancedCharts['training_status']['data'] ?? []),
                    backgroundColor: @json($advancedCharts['training_status']['colors'] ?? []),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                        text: 'حالات التدريبات'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Application Status Chart
        var applicationStatusCtx = document.getElementById('applicationStatusChart').getContext('2d');
        new Chart(applicationStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($advancedCharts['application_status']['labels'] ?? []),
                datasets: [{
                    data: @json($advancedCharts['application_status']['data'] ?? []),
                    backgroundColor: @json($advancedCharts['application_status']['colors'] ?? []),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'حالات طلبات التدريب'
                    }
                }
            }
        });

        // Monthly Trainings Chart
        var monthlyTrainingsCtx = document.getElementById('monthlyTrainingsChart').getContext('2d');
        new Chart(monthlyTrainingsCtx, {
            type: 'line',
            data: {
                labels: @json($advancedCharts['monthly_trends']['labels'] ?? []),
                datasets: [{
                    label: 'عدد التدريبات',
                    data: @json($advancedCharts['monthly_trends']['data'] ?? []),
                    borderColor: @json($advancedCharts['monthly_trends']['color'] ?? '#8b5cf6'),
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                        text: 'اتجاهات التدريبات الشهرية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Company Distribution Chart
        var companyDistributionCtx = document.getElementById('companyDistributionChart').getContext('2d');
        new Chart(companyDistributionCtx, {
            type: 'bar',
            data: {
                labels: @json($advancedCharts['company_distribution']['labels'] ?? []),
                datasets: [{
                    label: 'عدد التدريبات',
                    data: @json($advancedCharts['company_distribution']['data'] ?? []),
                    backgroundColor: @json($advancedCharts['company_distribution']['colors'] ?? []),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                        text: 'توزيع الشركات حسب التدريبات'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
