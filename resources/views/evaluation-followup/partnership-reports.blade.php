@extends('layouts.app')

@section('title', 'تقارير الشراكات - تقييم ومتابعة')

@section('page-title', 'تقارير الشراكات')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>إحصائيات الشراكات وفرص العمل
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
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>توزيع فرص العمل حسب النوع</h6>
                        <canvas id="opportunityTypeChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>توزيع فرص العمل حسب الحالة</h6>
                        <canvas id="opportunityStatusChart"></canvas>
                    </div>
                </div>
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

        // Opportunity Type Chart
        var opportunityTypeCtx = document.getElementById('opportunityTypeChart').getContext('2d');
        new Chart(opportunityTypeCtx, {
            type: 'bar',
            data: {
                labels: @json($opportunityStats['byType']->pluck('type')->toArray() ?? []),
                datasets: [{
                    label: 'عدد الفرص',
                    data: @json($opportunityStats['byType']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
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
                        text: 'توزيع فرص العمل حسب النوع'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Opportunity Status Chart
        var opportunityStatusCtx = document.getElementById('opportunityStatusChart').getContext('2d');
        new Chart(opportunityStatusCtx, {
            type: 'bar',
            data: {
                labels: @json($opportunityStats['byStatus']->pluck('status')->toArray() ?? []),
                datasets: [{
                    label: 'عدد الفرص',
                    data: @json($opportunityStats['byStatus']->pluck('count')->toArray() ?? []),
                    backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
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
                        text: 'توزيع فرص العمل حسب الحالة'
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
