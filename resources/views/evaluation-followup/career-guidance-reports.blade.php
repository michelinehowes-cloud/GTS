@extends('layouts.app')

@section('title', 'تقارير الإرشاد المهني - تقييم ومتابعة')

@section('page-title', 'تقارير الإرشاد المهني')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>إحصائيات الإرشاد المهني الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-info text-center">
                            <h4 class="alert-heading">{{ $stats['totalGraduates'] ?? 0 }}</h4>
                            <p class="mb-0">إجمالي الخريجين</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-success text-center">
                            <h4 class="alert-heading">{{ $stats['employedGraduates'] ?? 0 }}</h4>
                            <p class="mb-0">خريجون موظفون</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-warning text-center">
                            <h4 class="alert-heading">{{ $stats['seekingOpportunities'] ?? 0 }}</h4>
                            <p class="mb-0">باحثون عن فرص</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-primary text-center">
                            <h4 class="alert-heading">{{ $stats['employmentRate'] ?? 0 }}%</h4>
                            <p class="mb-0">معدل التوظيف</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-secondary text-center">
                            <h4 class="alert-heading">{{ $stats['activeNominations'] ?? 0 }}</h4>
                            <p class="mb-0">ترشيحات نشطة</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-info text-center">
                            <h4 class="alert-heading">{{ $stats['successRate'] ?? 0 }}%</h4>
                            <p class="mb-0">معدل نجاح الترشيح</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-success text-center">
                            <h4 class="alert-heading">{{ $stats['availableOpportunities'] ?? 0 }}</h4>
                            <p class="mb-0">فرص عمل متاحة</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="alert alert-warning text-center">
                            <h4 class="alert-heading">{{ $stats['overallSuccessRate'] ?? 0 }}%</h4>
                            <p class="mb-0">معدل النجاح الكلي</p>
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
                    <i class="fas fa-chart-pie me-2"></i>توزيع الخريجين حسب التخصص
                </h5>
            </div>
            <div class="card-body">
                <canvas id="majorsDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>حالة التوظيف للخريجين
                </h5>
            </div>
            <div class="card-body">
                <canvas id="employmentStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2"></i>حالة الترشيحات
                </h5>
            </div>
            <div class="card-body">
                <canvas id="nominationsStatusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>الأداء الشهري للترشيحات
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyPerformanceChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-percent me-2"></i>معدل نجاح الترشيحات حسب التخصص
                </h5>
            </div>
            <div class="card-body">
                <canvas id="successByMajorChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-briefcase me-2"></i>توزيع فرص العمل
                </h5>
            </div>
            <div class="card-body">
                <canvas id="opportunitiesDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>استنتاجات ذكية
                </h5>
            </div>
            <div class="card-body">
                @forelse($insights ?? [] as $insight)
                    <div class="alert alert-{{ $insight['color'] }}">
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
                    <a href="{{ route('career-guidance.export-reports.excel', ['type' => 'advanced']) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>تصدير إلى Excel
                    </a>
                    <a href="{{ route('career-guidance.export-reports.pdf', ['type' => 'advanced']) }}" class="btn btn-danger">
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
        // Majors Distribution Chart
        var majorsDistributionCtx = document.getElementById('majorsDistributionChart').getContext('2d');
        new Chart(majorsDistributionCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['majorsDistribution']['labels'] ?? []),
                datasets: @json($chartData['majorsDistribution']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                        text: 'توزيع الخريجين حسب التخصص'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Employment Status Chart
        var employmentStatusCtx = document.getElementById('employmentStatusChart').getContext('2d');
        new Chart(employmentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['employmentStatus']['labels'] ?? []),
                datasets: @json($chartData['employmentStatus']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'حالة التوظيف للخريجين'
                    }
                }
            }
        });

        // Nominations Status Chart
        var nominationsStatusCtx = document.getElementById('nominationsStatusChart').getContext('2d');
        new Chart(nominationsStatusCtx, {
            type: 'pie',
            data: {
                labels: @json($chartData['nominationsStatus']['labels'] ?? []),
                datasets: @json($chartData['nominationsStatus']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'حالة الترشيحات'
                    }
                }
            }
        });

        // Monthly Performance Chart
        var monthlyPerformanceCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
        new Chart(monthlyPerformanceCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['monthlyPerformance']['labels'] ?? []),
                datasets: @json($chartData['monthlyPerformance']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'الأداء الشهري للترشيحات'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Success By Major Chart
        var successByMajorCtx = document.getElementById('successByMajorChart').getContext('2d');
        new Chart(successByMajorCtx, {
            type: 'radar',
            data: {
                labels: @json($chartData['successByMajor']['labels'] ?? []),
                datasets: @json($chartData['successByMajor']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'معدل نجاح الترشيحات حسب التخصص'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Opportunities Distribution Chart
        var opportunitiesDistributionCtx = document.getElementById('opportunitiesDistributionChart').getContext('2d');
        new Chart(opportunitiesDistributionCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['opportunitiesDistribution']['labels'] ?? []),
                datasets: @json($chartData['opportunitiesDistribution']['datasets'] ?? [])
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                        text: 'توزيع فرص العمل'
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
