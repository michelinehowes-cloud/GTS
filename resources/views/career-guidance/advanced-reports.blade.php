@extends('layouts.app')

@section('title', 'التقارير المتقدمة والإحصائيات')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-color: #5a5c69;
            --light-bg: #f8f9fc;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Tajawal', sans-serif;
        }

        .page-header {
            background: #fff;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-right: 5px solid var(--primary-color);
        }

        .stat-card {
            border-radius: 15px;
            border: none;
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        .stat-trend {
            font-size: 0.85rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
        }

        .chart-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 2rem;
        }

        .chart-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.5rem;
            border-radius: 15px 15px 0 0;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .insight-card {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .insight-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-right: 3px solid rgba(255, 255, 255, 0.5);
            transition: background 0.2s;
        }

        .insight-item:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-action {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- رأس الصفحة -->
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <h1 class="h3 mb-1 text-gray-800 fw-bold">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i>
                    لوحة القيادة والتقارير المتقدمة
                </h1>
                <p class="mb-0 text-muted">نظرة شاملة وتحليلات دقيقة لأداء نظام الإرشاد المهني</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('career-guidance.export-reports.pdf') }}" target="_blank"
                    class="btn btn-danger btn-action shadow-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>تصدير PDF
                </a>
                <a href="{{ route('career-guidance.export-reports.excel') }}" target="_blank"
                    class="btn btn-success btn-action shadow-sm">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i>تصدير Excel
                </a>
                <button class="btn btn-primary btn-action shadow-sm" id="refreshBtn" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i>تحديث
                </button>
            </div>
        </div>

        <!-- نموذج التصفية (Interactive Filters) -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-funnel-fill me-2"></i>تصفية البيانات
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('evaluation-followup.career-guidance-advanced-reports') }}" method="GET"
                    id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="major" class="form-label small text-muted fw-bold">التخصص</label>
                            <select class="form-select" name="major" id="major">
                                <option value="">كل التخصصات</option>
                                @foreach($majors ?? [] as $major)
                                    <option value="{{ $major }}" {{ request('major') == $major ? 'selected' : '' }}>{{ $major }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label small text-muted fw-bold">سنة التخرج</label>
                            <select class="form-select" name="year" id="year">
                                <option value="">كل السنوات</option>
                                @foreach($years ?? [] as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label small text-muted fw-bold">حالة التوظيف</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">الكل</option>
                                <option value="employed" {{ request('status') == 'employed' ? 'selected' : '' }}>موظف</option>
                                <option value="seeking_opportunities" {{ request('status') == 'seeking_opportunities' ? 'selected' : '' }}>باحث عن عمل</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-2"></i>تطبيق الفلاتر
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- رسالة ترحيب عند عدم وجود بيانات -->
        @if($stats['totalGraduates'] == 0)
            <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1">مرحباً بك في النظام!</h5>
                        <p class="mb-0">
                            يبدو أن قاعدة البيانات فارغة حالياً. يمكنك
                            <a href="{{ route('career-guidance.graduates.create') }}"
                                class="alert-link fw-bold text-decoration-underline">إضافة خريج جديد</a>
                            أو
                            <a href="{{ route('career-guidance.import.graduates') }}"
                                class="alert-link fw-bold text-decoration-underline">استيراد بيانات الخريجين</a>
                            للبدء.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- مؤشرات الأداء الرئيسية (KPIs) -->
        <div class="row mb-4">
            <!-- إجمالي الخريجين -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="card-title text-primary">إجمالي الخريجين</div>
                                <div class="card-value">{{ number_format($stats['totalGraduates']) }}</div>
                                <div class="stat-trend text-success">
                                    <i class="bi bi-arrow-up me-1"></i>
                                    <span>{{ $stats['newGraduatesThisMonth'] }}</span>
                                    <span class="text-muted ms-1">هذا الشهر</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الخريجين الموظفين -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-success border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="card-title text-success">تم توظيفهم</div>
                                <div class="card-value">{{ number_format($stats['employedGraduates']) }}</div>
                                <div class="stat-trend text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    <span>{{ number_format($stats['employmentRate'], 1) }}%</span>
                                    <span class="text-muted ms-1">معدل التوظيف</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-briefcase-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الباحثين عن عمل -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="card-title text-warning">باحثين عن عمل</div>
                                <div class="card-value">{{ number_format($stats['seekingOpportunities']) }}</div>
                                <div class="stat-trend text-warning">
                                    <i class="bi bi-search me-1"></i>
                                    <span>{{ number_format($stats['seekingRate'], 1) }}%</span>
                                    <span class="text-muted ms-1">من الإجمالي</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-person-lines-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الفرص المتاحة -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="card-title text-danger">فرص متاحة</div>
                                <div class="card-value">{{ number_format($stats['availableOpportunities']) }}</div>
                                <div class="stat-trend text-info">
                                    <i class="bi bi-plus-circle-fill me-1"></i>
                                    <span>{{ $stats['newOpportunitiesThisWeek'] }}</span>
                                    <span class="text-muted ms-1">جديدة هذا الأسبوع</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-building-add"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسوم البيانية -->
        <div class="row">
            <!-- رسم بياني 1: توزيع الخريجين -->
            <div class="col-lg-8 mb-4">
                <div class="card chart-card h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-pie-chart-fill me-2"></i>توزيع حالة الخريجين
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="graduatesStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- رسم بياني 2: نسبة النجاح والتوظيف -->
            <div class="col-lg-4 mb-4">
                <div class="card chart-card h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">
                            <i class="bi bi-bar-chart-fill me-2"></i>مؤشرات الأداء
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                        <hr>
                        <div class="text-center small mt-3">
                            <span class="me-2">
                                <i class="bi bi-circle-fill text-success"></i> نسبة التوظيف
                            </span>
                            <span class="me-2">
                                <i class="bi bi-circle-fill text-info"></i> نسبة النجاح
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الاستنتاجات والتحليلات -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card insight-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-lightbulb-fill me-2 text-warning"></i>
                                استنتاجات وتحليلات ذكية
                            </h5>
                            <span class="badge bg-white text-primary fs-6 rounded-pill px-3">{{ count($insights) }}
                                تحليل</span>
                        </div>
                        <div class="row">
                            @forelse($insights as $insight)
                                <div class="col-md-6 col-lg-4">
                                    <div class="insight-item h-100">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-{{ $insight['icon'] }} text-white fs-4 opacity-75"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="text-white mb-2 fw-bold">{{ $insight['title'] }}</h6>
                                                <p class="text-white mb-0 small opacity-75">{{ $insight['description'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="bi bi-clipboard-data text-white opacity-50 display-1 mb-3"></i>
                                    <p class="text-white h5 opacity-75">جاري جمع البيانات لتحليلها...</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- استدعاء مكتبة Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // إعدادات عامة للرسوم البيانية
        Chart.defaults.font.family = "'Tajawal', sans-serif";
        Chart.defaults.color = '#858796';

        // بيانات تجريبية في حالة عدم وجود بيانات حقيقية (للعرض فقط)
        // في الواقع، يجب تمرير هذه البيانات من الـ Controller عبر المتغير $chartData
        const chartData = @json($chartData ?? []);
        const stats = @json($stats);

        // 1. رسم بياني لتوزيع حالة الخريجين (Pie Chart)
        const ctxStatus = document.getElementById('graduatesStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['موظفون', 'باحثون عن عمل', 'غير محدد'],
                datasets: [{
                    data: [
                        stats.employedGraduates,
                        stats.seekingOpportunities,
                        stats.totalGraduates - (stats.employedGraduates + stats.seekingOpportunities)
                    ],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#858796'],
                    hoverBackgroundColor: ['#17a673', '#dda20a', '#60616f'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                },
                cutout: '70%',
            },
        });

        // 2. رسم بياني لمؤشرات الأداء (Bar Chart)
        const ctxPerformance = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctxPerformance, {
            type: 'bar',
            data: {
                labels: ['معدل التوظيف', 'معدل النجاح', 'معدل البحث'],
                datasets: [{
                    label: 'النسبة المئوية',
                    data: [
                        stats.employmentRate,
                        stats.successRate,
                        stats.seekingRate
                    ],
                    backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#17a673', '#2c9faf', '#dda20a'],
                    borderColor: "#4e73df",
                    borderRadius: 5,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function (value, index, values) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                }
            },
        });
    </script>
@endsection