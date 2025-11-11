@extends('layouts.app')

@section('title', 'تقارير الشراكات والتوظيف المتقدمة - تقييم ومتابعة')

@section('page-title', 'تقارير الشراكات والتوظيف المتقدمة')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon me-3">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">{{ $totalPartnershipsCount }}</h5>
                    <p class="card-text text-muted mb-0">إجمالي الشراكات</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon me-3">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">{{ $totalCompaniesCount }}</h5>
                    <p class="card-text text-muted mb-0">إجمالي الشركات</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon me-3">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0">{{ $totalJobOpportunitiesCount }}</h5>
                    <p class="card-text text-muted mb-0">إجمالي فرص العمل</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>إحصائيات الشراكات
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>توزيع الشركات حسب نوع الشراكة</h6>
                        <canvas id="partnershipTypeChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>توزيع الشركات حسب حالة الشراكة</h6>
                        <canvas id="partnershipStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-briefcase me-2"></i>إحصائيات فرص العمل والتوظيف
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>توزيع فرص العمل حسب النوع</h6>
                        <canvas id="employmentOpportunityTypeChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>توزيع فرص العمل حسب الحالة</h6>
                        <canvas id="employmentOpportunityStatusChart"></canvas>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 mb-3">
                        <h6>أكثر الشركات توفيراً للفرص</h6>
                        <canvas id="opportunitiesByCompanyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>تطور فرص العمل الشهرية
                </h5>
            </div>
            <div class="card-body">
                <canvas id="jobOpportunityTrendsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-export me-2"></i>تصدير التقارير المدمجة
                </h5>
            </div>
            <div class="card-body">
                <p>يمكنك تصدير تقارير الشراكات والتوظيف المدمجة بصيغ مختلفة:</p>
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
        // Partnership Type Chart
        var partnershipTypeCtx = document.getElementById('partnershipTypeChart').getContext('2d');
        new Chart(partnershipTypeCtx, {
            type: 'pie',
            data: {
                labels: @json($partnershipStats['byType']->pluck('partnership_type')->toArray() ?? []),
                datasets: [{
                    data: @json($partnershipStats['byType']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
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
                        text: 'توزيع الشركات حسب نوع الشراكة'
                    }
                }
            }
        });

        // Partnership Status Chart
        var partnershipStatusCtx = document.getElementById('partnershipStatusChart').getContext('2d');
        new Chart(partnershipStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($partnershipStats['byStatus']->pluck('partnership_status')->toArray() ?? []),
                datasets: [{
                    data: @json($partnershipStats['byStatus']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
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
                        text: 'توزيع الشركات حسب حالة الشراكة'
                    }
                }
            }
        });

        // Employment Opportunity Type Chart
        var employmentOpportunityTypeCtx = document.getElementById('employmentOpportunityTypeChart').getContext('2d');
        new Chart(employmentOpportunityTypeCtx, {
            type: 'pie',
            data: {
                labels: @json($employmentStats['byType']->pluck('type')->toArray() ?? []),
                datasets: [{
                    data: @json($employmentStats['byType']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
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
                        text: 'توزيع فرص العمل حسب النوع'
                    }
                }
            }
        });

        // Employment Opportunity Status Chart
        var employmentOpportunityStatusCtx = document.getElementById('employmentOpportunityStatusChart').getContext('2d');
        new Chart(employmentOpportunityStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($employmentStats['byStatus']->pluck('status')->toArray() ?? []),
                datasets: [{
                    data: @json($employmentStats['byStatus']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e', '#858796'],
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
                        text: 'توزيع فرص العمل حسب الحالة'
                    }
                }
            }
        });

        // Opportunities By Company Chart
        var opportunitiesByCompanyCtx = document.getElementById('opportunitiesByCompanyChart').getContext('2d');
        new Chart(opportunitiesByCompanyCtx, {
            type: 'bar',
            data: {
                labels: @json($employmentStats['byCompany']->pluck('name')->toArray() ?? []),
                datasets: [{
                    label: 'عدد الفرص',
                    data: @json($employmentStats['byCompany']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
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
                        text: 'أكثر الشركات توفيراً للفرص'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Job Opportunity Trends Chart
        var jobOpportunityTrendsCtx = document.getElementById('jobOpportunityTrendsChart').getContext('2d');
        new Chart(jobOpportunityTrendsCtx, {
            type: 'line',
            data: {
                labels: @json($jobOpportunityTrends->pluck('month')->toArray() ?? []),
                datasets: [{
                    label: 'فرص العمل الجديدة',
                    data: @json($jobOpportunityTrends->pluck('count')->toArray() ?? []),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    fill: true,
                    tension: 0.4
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
                        text: 'تطور فرص العمل الشهرية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'عدد الفرص'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'الشهر'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
