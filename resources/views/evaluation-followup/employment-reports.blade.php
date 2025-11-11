@extends('layouts.app')

@section('title', 'تقارير التوظيف - تقييم ومتابعة')

@section('page-title', 'تقارير التوظيف')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>إحصائيات فرص العمل والتوظيف
                </h5>
            </div>
            <div class="card-body">
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
        // Opportunity Type Chart
        var opportunityTypeCtx = document.getElementById('opportunityTypeChart').getContext('2d');
        new Chart(opportunityTypeCtx, {
            type: 'pie',
            data: {
                labels: @json($stats['byType']->pluck('type')->toArray() ?? []),
                datasets: [{
                    data: @json($stats['byType']->pluck('count')->toArray() ?? []),
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

        // Opportunity Status Chart
        var opportunityStatusCtx = document.getElementById('opportunityStatusChart').getContext('2d');
        new Chart(opportunityStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($stats['byStatus']->pluck('status')->toArray() ?? []),
                datasets: [{
                    data: @json($stats['byStatus']->pluck('count')->toArray() ?? []),
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
                labels: @json($stats['byCompany']->pluck('name')->toArray() ?? []),
                datasets: [{
                    label: 'عدد الفرص',
                    data: @json($stats['byCompany']->pluck('count')->toArray() ?? []),
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
    });
</script>
@endpush
