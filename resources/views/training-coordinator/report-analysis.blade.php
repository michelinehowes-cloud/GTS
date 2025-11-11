@extends('layouts.training-coordinator')

@section('title', 'تحليل التقرير - ' . $report->report_name)
@section('page-title', 'تحليل البيانات - ' . $report->report_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        تحليل التقرير: {{ $report->report_name }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('training-coordinator.reports.preview', $report->id) }}" 
                           class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i>معاينة
                        </a>
                        <a href="{{ route('training-coordinator.reports') }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>رجوع
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- ملخص التحليل -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white text-center">
                                <div class="card-body py-3">
                                    <i class="fas fa-database fa-2x mb-2"></i>
                                    <h3>{{ number_format($analysis['total_records']) }}</h3>
                                    <p class="mb-0">إجمالي السجلات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body py-3">
                                    <i class="fas fa-columns fa-2x mb-2"></i>
                                    <h3>{{ count($analysis['headers']) }}</h3>
                                    <p class="mb-0">عدد الحقول</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body py-3">
                                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                    <h3>{{ $analysis['analysis_type'] }}</h3>
                                    <p class="mb-0">نوع التحليل</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white text-center">
                                <div class="card-body py-3">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h3>{{ $analysis['processing_time'] }}s</h3>
                                    <p class="mb-0">وقت المعالجة</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الإحصائيات التفصيلية -->
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- الرسوم البيانية -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        الرسوم البيانية
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if(isset($analysis['charts']['column_chart']))
                                        <div class="col-md-6 mb-4">
                                            <h6 class="text-center mb-3">توزيع البيانات</h6>
                                            <div class="chart-container" style="height: 250px;">
                                                <canvas id="columnChart"></canvas>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if(isset($analysis['charts']['pie_chart']))
                                        <div class="col-md-6 mb-4">
                                            <h6 class="text-center mb-3">التوزيع النسبي</h6>
                                            <div class="chart-container" style="height: 250px;">
                                                <canvas id="pieChart"></canvas>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- تحليل الحقول -->
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-table me-2"></i>
                                        تحليل الحقول
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>اسم الحقل</th>
                                                    <th>نوع البيانات</th>
                                                    <th>القيم الفريدة</th>
                                                    <th>القيم الفارغة</th>
                                                    <th>النسبة %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($analysis['field_analysis'] as $field => $stats)
                                                <tr>
                                                    <td><strong>{{ $field }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $stats['data_type_color'] }}">
                                                            {{ $stats['data_type'] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $stats['unique_values'] }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $stats['empty_count'] > 0 ? 'warning' : 'success' }}">
                                                            {{ $stats['empty_count'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-{{ $stats['completeness_color'] }}" 
                                                                 style="width: {{ $stats['completeness_percentage'] }}%">
                                                                {{ $stats['completeness_percentage'] }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- الإحصائيات السريعة -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        إحصائيات سريعة
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($analysis['quick_stats'] as $stat)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <span class="fw-bold">{{ $stat['label'] }}:</span>
                                        <span class="badge bg-{{ $stat['color'] }} fs-6">
                                            {{ $stat['value'] }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- التوصيات -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        التوصيات
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach($analysis['recommendations'] as $recommendation)
                                    <div class="alert alert-{{ $recommendation['type'] }} d-flex align-items-start">
                                        <i class="fas fa-{{ $recommendation['icon'] }} me-2 mt-1"></i>
                                        <div class="flex-grow-1">
                                            <strong>{{ $recommendation['title'] }}</strong>
                                            <p class="mb-0 small">{{ $recommendation['description'] }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- تصدير النتائج -->
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-download me-2"></i>
                                        تصدير النتائج
                                    </h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="btn-group-vertical w-100">
                                        <button class="btn btn-outline-primary mb-2" onclick="exportAnalysis('pdf')">
                                            <i class="fas fa-file-pdf me-2"></i>PDF تقرير
                                        </button>
                                        <button class="btn btn-outline-success mb-2" onclick="exportAnalysis('excel')">
                                            <i class="fas fa-file-excel me-2"></i>Excel بيانات
                                        </button>
                                        <button class="btn btn-outline-warning mb-2" onclick="exportAnalysis('json')">
                                            <i class="fas fa-file-code me-2"></i>JSON بيانات
                                        </button>
                                        <button class="btn btn-outline-dark" onclick="printAnalysis()">
                                            <i class="fas fa-print me-2"></i>طباعة التقرير
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- عينة من البيانات -->
                    @if(isset($analysis['sample_data']) && count($analysis['sample_data']) > 0)
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>
                                عينة من البيانات (أول 5 سجلات)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            @foreach($analysis['headers'] as $header)
                                            <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analysis['sample_data'] as $row)
                                        <tr>
                                            @foreach($row as $cell)
                                            <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- إضافة Chart.js للرسوم البيانية -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    margin-bottom: 1rem;
}

.chart-container {
    position: relative;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    font-weight: 600;
}

.alert {
    border-radius: 8px;
    border: none;
}

.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
}
</style>

<script>
// تهيئة الرسوم البيانية
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($analysis['charts']['column_chart']))
    initColumnChart();
    @endif

    @if(isset($analysis['charts']['pie_chart']))
    initPieChart();
    @endif
});

function initColumnChart() {
    const ctx = document.getElementById('columnChart').getContext('2d');
    const chartData = @json($analysis['charts']['column_chart'] ?? []);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels || [],
            datasets: [{
                label: chartData.label || 'البيانات',
                data: chartData.data || [],
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initPieChart() {
    const ctx = document.getElementById('pieChart').getContext('2d');
    const chartData = @json($analysis['charts']['pie_chart'] ?? []);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels || [],
            datasets: [{
                data: chartData.data || [],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// تصدير النتائج
function exportAnalysis(format) {
    const reportId = {{ $report->id }};
    
    switch(format) {
        case 'pdf':
            window.open(`/coordinator/reports/export/${reportId}/pdf`, '_blank');
            break;
        case 'excel':
            window.open(`/coordinator/reports/export/${reportId}/excel`, '_blank');
            break;
        case 'json':
            window.open(`/coordinator/reports/export/${reportId}/json`, '_blank');
            break;
    }
}

function printAnalysis() {
    window.print();
}

// نسخ ملخص التحليل
function copyAnalysisSummary() {
    const summary = `
ملخص تحليل التقرير:
- إجمالي السجلات: {{ number_format($analysis['total_records']) }}
- عدد الحقول: {{ count($analysis['headers']) }}
- نوع التقرير: {{ $report->type_label }}
- تاريخ التحليل: {{ date('Y-m-d H:i') }}
    `.trim();
    
    navigator.clipboard.writeText(summary).then(function() {
        alert('تم نسخ ملخص التحليل');
    });
}
</script>
@endsection