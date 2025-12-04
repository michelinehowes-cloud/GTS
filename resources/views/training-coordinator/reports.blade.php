@extends('layouts.app')

@section('title', 'التقارير والإحصائيات المتقدمة - منسق التدريب')
@section('page-title', 'التقارير والإحصائيات المتقدمة')

@section('content')
    <div class="container-fluid">
        <!-- الإحصائيات السريعة -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">إجمالي البرامج</h5>
                                <h2 class="card-value">{{ $basicStats['totalTrainings'] }}</h2>
                                <small>برنامج تدريبي</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-graduation-cap fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">برامج نشطة</h5>
                                <h2 class="card-value">{{ $basicStats['activeTrainings'] }}</h2>
                                <small>قيد التنفيذ</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-play-circle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">إجمالي الطلبات</h5>
                                <h2 class="card-value">{{ $basicStats['totalApplications'] }}</h2>
                                <small>طلب تدريب</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">طلبات معلقة</h5>
                                <h2 class="card-value">{{ $basicStats['pendingApplications'] }}</h2>
                                <small>تنتظر المراجعة</small>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المخططات البيانية المتقدمة -->
        <div class="row">
            <!-- مخطط توزيع أنواع التدريبات -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            توزيع أنواع التدريبات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trainingTypesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مخطط توزيع حالات التدريبات -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            حالات البرامج التدريبية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trainingStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مخطط توزيع طلبات التدريب -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-doughnut me-2"></i>
                            توزيع طلبات التدريب
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="applicationStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مخطط التوجه الشهري -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            التوجه الشهري للتدريبات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyTrendsChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- مخطط توزيع الشركات -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>
                            توزيع التدريبات حسب الشركات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="companyDistributionChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- التحليلات المتقدمة -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-network me-2"></i>
                            التحليلات المتقدمة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="analytics-item">
                                    <div class="analytics-value text-primary">{{ $advancedAnalytics['approval_rate'] }}%
                                    </div>
                                    <div class="analytics-label">معدل القبول</div>
                                    <small class="text-muted">نسبة الطلبات المقبولة</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="analytics-item">
                                    <div class="analytics-value text-success">{{ $advancedAnalytics['avg_applicants'] }}
                                    </div>
                                    <div class="analytics-label">متوسط المتقدمين</div>
                                    <small class="text-muted">لكل برنامج تدريبي</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="analytics-item">
                                    <div class="analytics-value text-info">{{ $advancedAnalytics['overall_occupancy'] }}%
                                    </div>
                                    <div class="analytics-label">نسبة الإشغال</div>
                                    <small class="text-muted">إجمالي المقاعد المشغولة</small>
                                </div>
                            </div>
                        </div>

                        <!-- أكثر التدريبات طلباً -->
                        <h6 class="mt-4 mb-3">أكثر البرامج التدريبية طلباً</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>اسم البرنامج</th>
                                        <th>عدد الطلبات</th>
                                        <th>نسبة الإشغال</th>
                                        <th>مستوى الشعبية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($advancedAnalytics['popular_trainings'] as $training)
                                        <tr>
                                            <td>{{ $training['name'] }}</td>
                                            <td>{{ $training['applications'] }}</td>
                                            <td>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $training['occupancy_rate'] > 80 ? 'success' : ($training['occupancy_rate'] > 50 ? 'warning' : 'danger') }}"
                                                        style="width: {{ $training['occupancy_rate'] }}%">
                                                    </div>
                                                </div>
                                                <small>{{ $training['occupancy_rate'] }}%</small>
                                            </td>
                                            <td>
                                                @if($training['occupancy_rate'] > 80)
                                                    <span class="badge bg-success">عالية جداً</span>
                                                @elseif($training['occupancy_rate'] > 50)
                                                    <span class="badge bg-warning">متوسطة</span>
                                                @else
                                                    <span class="badge bg-danger">منخفضة</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الاستنتاجات الذكية -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            استنتاجات وتحليلات
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($advancedAnalytics['insights'] as $insight)
                            <div class="alert alert-{{ $insight['type'] }} alert-dismissible fade show mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-{{ $insight['icon'] }} fa-lg mt-1"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="alert-heading">{{ $insight['title'] }}</h6>
                                        <p class="mb-0 small">{{ $insight['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- قسم رفع التقارير -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-upload me-2"></i>
                            رفع تقارير CSV
                        </h5>
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse"
                            data-bs-target="#uploadSection">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse show" id="uploadSection">
                        <div class="card-body">
                            <form action="{{ route('training-coordinator.reports.upload') }}" method="POST"
                                enctype="multipart/form-data" id="uploadForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="report_type" class="form-label">نوع التقرير</label>
                                        <select class="form-select" id="report_type" name="report_type" required>
                                            <option value="">اختر نوع التقرير</option>
                                            <option value="training_programs">برامج التدريب</option>
                                            <option value="training_applications">طلبات التدريب</option>
                                            <option value="student_data">بيانات الطلاب</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="csv_file" class="form-label">ملف CSV</label>
                                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv"
                                            required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="description" class="form-label">وصف التقرير</label>
                                        <input type="text" class="form-control" id="description" name="description"
                                            placeholder="وصف مختصر...">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h6><i class="fas fa-download me-2"></i>تحميل نماذج CSV</h6>
                                    <div class="btn-group">
                                        @foreach(['training_programs', 'training_applications', 'student_data'] as $type)
                                            <a href="{{ route('training-coordinator.reports.download-template', ['type' => $type]) }}"
                                                class="btn btn-sm btn-outline-primary me-2">
                                                <i class="fas fa-file-csv me-1"></i>
                                                {{ $type == 'training_programs' ? 'برامج التدريب' : ($type == 'training_applications' ? 'طلبات التدريب' : 'بيانات الطلاب') }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success" id="uploadBtn">
                                        <i class="fas fa-upload me-2"></i>رفع التقرير
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- التقارير المحفوظة -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            التقارير المحفوظة
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($reports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>اسم التقرير</th>
                                            <th>النوع</th>
                                            <th>الحجم</th>
                                            <th>التاريخ</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                            <tr>
                                                <td>
                                                    <strong>{{ $report->report_name }}</strong>
                                                    @if($report->description)
                                                        <br>
                                                        <small class="text-muted">{{ $report->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $report->type_color }}">
                                                        {{ $report->type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ number_format($report->file_size / 1024, 1) }} KB</small>
                                                </td>
                                                <td>
                                                    <small>{{ $report->created_at->format('Y-m-d') }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('training-coordinator.reports.download', $report->id) }}"
                                                            class="btn btn-outline-primary" title="تحميل">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('training-coordinator.reports.delete', $report->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')"
                                                                title="حذف">
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
                                <i class="fas fa-file-csv fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">لا توجد تقارير محفوظة</h5>
                                <p class="text-muted">يمكنك البدء برفع أول تقرير CSV</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إضافة Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .card-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .analytics-item {
            padding: 1rem;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 1rem;
        }

        .analytics-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .analytics-label {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>

    <script>
        // تهيئة جميع المخططات البيانية
        document.addEventListener('DOMContentLoaded', function () {
            // 1. مخطط توزيع أنواع التدريبات (دائري)
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
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true
                        },
                        title: {
                            display: true,
                            text: 'توزيع أنواع البرامج التدريبية'
                        }
                    }
                }
            });

            // 2. مخطط حالات التدريبات (أعمدة)
            new Chart(document.getElementById('trainingStatusChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($advancedCharts['training_status']['labels']) !!},
                    datasets: [{
                        label: 'عدد البرامج',
                        data: {!! json_encode($advancedCharts['training_status']['data']) !!},
                        backgroundColor: {!! json_encode($advancedCharts['training_status']['colors']) !!},
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'حالات البرامج التدريبية'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // 3. مخطط توزيع طلبات التدريب (دونات)
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
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true
                        },
                        title: {
                            display: true,
                            text: 'توزيع طلبات التدريب'
                        }
                    }
                }
            });

            // 4. مخطط التوجه الشهري (خط)
            new Chart(document.getElementById('monthlyTrendsChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($advancedCharts['monthly_trends']['labels']) !!},
                    datasets: [{
                        label: 'عدد التدريبات',
                        data: {!! json_encode($advancedCharts['monthly_trends']['data']) !!},
                        borderColor: {!! json_encode($advancedCharts['monthly_trends']['color']) !!},
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'التوجه الشهري للتدريبات'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // 5. مخطط توزيع الشركات (أفقية)
            new Chart(document.getElementById('companyDistributionChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($advancedCharts['company_distribution']['labels']) !!},
                    datasets: [{
                        label: 'عدد التدريبات',
                        data: {!! json_encode($advancedCharts['company_distribution']['data']) !!},
                        backgroundColor: {!! json_encode($advancedCharts['company_distribution']['colors']) !!},
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'توزيع التدريبات حسب الشركات'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        // إدارة رفع الملفات
        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            const uploadBtn = document.getElementById('uploadBtn');
            const originalText = uploadBtn.innerHTML;

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الرفع...';
        });

        // التحقق من صحة الملف
        document.getElementById('csv_file').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const fileName = file.name.toLowerCase();

                if (!fileName.endsWith('.csv')) {
                    alert('يجب اختيار ملف بصيغة CSV فقط');
                    this.value = '';
                    return;
                }

                const fileSizeMB = file.size / 1024 / 1024;
                if (fileSizeMB > 10) {
                    alert('حجم الملف يجب أن لا يتجاوز 10MB');
                    this.value = '';
                    return;
                }
            }
        });
    </script>
@endsection