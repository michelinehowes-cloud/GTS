@extends('layouts.app')

@section('title', 'معاينة التقرير - ' . $report->report_name)
@section('page-title', 'معاينة التقرير')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>
                            معاينة التقرير: {{ $report->report_name }}
                        </h5>
                        <div class="btn-group">
                            <a href="{{ route('training-coordinator.reports.download', $report->id) }}"
                                class="btn btn-light btn-sm">
                                <i class="fas fa-download me-1"></i>تحميل
                            </a>
                            <a href="{{ route('training-coordinator.reports') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right me-1"></i>رجوع للتقارير
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- معلومات التقرير -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>معلومات التقرير</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%"><i class="fas fa-file-alt me-2"></i>اسم التقرير:</th>
                                                <td>{{ $report->report_name }}</td>
                                            </tr>
                                            <tr>
                                                <th><i class="fas fa-tag me-2"></i>نوع التقرير:</th>
                                                <td>
                                                    <span class="badge bg-{{ $report->type_color }}">
                                                        {{ $report->type_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><i class="fas fa-calendar me-2"></i>تاريخ الرفع:</th>
                                                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-file-csv me-2"></i>معلومات الملف</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%"><i class="fas fa-file me-2"></i>اسم الملف:</th>
                                                <td class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $report->file_name }}">
                                                    {{ $report->file_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><i class="fas fa-weight me-2"></i>حجم الملف:</th>
                                                <td>{{ number_format($report->file_size / 1024, 2) }} KB</td>
                                            </tr>
                                            <tr>
                                                <th><i class="fas fa-align-left me-2"></i>الوصف:</th>
                                                <td>{{ $report->description ?: 'لا يوجد وصف' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معاينة البيانات -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-table me-2"></i>
                                    معاينة البيانات
                                    @if(isset($data['total_rows']))
                                        <small class="opacity-75">(عرض {{ count($data['rows'] ?? []) }} من أصل
                                            {{ $data['total_rows'] }} سجل)</small>
                                    @endif
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(!empty($data['headers']) && !empty($data['rows']))
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    @foreach($data['headers'] as $header)
                                                        <th class="text-center">{{ $header }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['rows'] as $index => $row)
                                                    <tr>
                                                        @foreach($row as $cell)
                                                            <td class="text-center">{{ $cell }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- تنقل الصفحات -->
                                    @if(($data['total_rows'] ?? 0) > 10)
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                عرض 1 إلى {{ count($data['rows']) }} من أصل {{ $data['total_rows'] }} سجل
                                            </div>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-primary btn-sm" disabled>
                                                    <i class="fas fa-chevron-right me-1"></i>السابق
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm">
                                                    التالي <i class="fas fa-chevron-left me-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <h5 class="text-warning">لا توجد بيانات للمعاينة</h5>
                                        <p class="text-muted">قد يكون الملف فارغاً أو غير قابل للقراءة</p>
                                        <a href="{{ route('training-coordinator.reports.download', $report->id) }}"
                                            class="btn btn-primary mt-2">
                                            <i class="fas fa-download me-2"></i>تحميل الملف للتحقق
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- إجراءات سريعة -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-bolt me-2"></i>
                                            إجراءات سريعة
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('training-coordinator.reports.download', $report->id) }}"
                                                    class="btn btn-outline-primary w-100">
                                                    <i class="fas fa-download fa-2x mb-2"></i>
                                                    <br>
                                                    تحميل التقرير
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('training-coordinator.reports.analyze', $report->id) }}"
                                                    class="btn btn-outline-success w-100">
                                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                    <br>
                                                    تحليل البيانات
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <a href="{{ route('training-coordinator.reports') }}"
                                                    class="btn btn-outline-info w-100">
                                                    <i class="fas fa-list fa-2x mb-2"></i>
                                                    <br>
                                                    كل التقارير
                                                </a>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <form
                                                    action="{{ route('training-coordinator.reports.delete', $report->id) }}"
                                                    method="POST" class="d-inline w-100">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger w-100"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                                                        <i class="fas fa-trash fa-2x mb-2"></i>
                                                        <br>
                                                        حذف التقرير
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }

        .table th {
            font-weight: 600;
            background: #f8f9fa;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    </style>

    <script>
        function analyzeReport(reportId) {
            // يمكن إضافة تحليل متقدم هنا
            alert('خاصية تحليل البيانات قيد التطوير');

            // أو توجيه لصفحة التحليل إذا كانت موجودة
            // window.location.href = "{{ url('coordinator/reports/analyze') }}/" + reportId;
        }

        // طباعة التقرير
        function printReport() {
            window.print();
        }

        // نسخ معلومات التقرير
        function copyReportInfo() {
            const reportInfo = `
    التقرير: {{ $report->report_name }}
    النوع: {{ $report->type_label }}
    التاريخ: {{ $report->created_at->format('Y-m-d H:i') }}
    الملف: {{ $report->file_name }}
        `.trim();

            navigator.clipboard.writeText(reportInfo).then(function () {
                alert('تم نسخ معلومات التقرير');
            }, function (err) {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endsection