@extends('layouts.app')

@section('title', 'لوحة تحكم الخريج')

@section('content')
    <div class="container-fluid">
        <!-- رسائل التنبيه -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- بطاقات الإحصائيات -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    التدريبات المتاحة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTrainings }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="card-icon bg-gradient-primary">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    طلباتي
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $myApplications }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="card-icon bg-gradient-success">
                                    <i class="fas fa-paper-plane fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    قيد المراجعة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingApplications }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="card-icon bg-gradient-warning">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    مقبولة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedApplications }}</div>
                            </div>
                            <div class="col-auto">
                                <div class="card-icon bg-gradient-info">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- التقويم التفاعلي -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>
                            تقويم التدريبات
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- الجانب الأيسر: الرسم البياني والإشعارات -->
            <div class="col-lg-4 mb-4">
                <!-- رسم بياني لحالة التقديمات -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie me-2"></i>
                            حالة التقديمات
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationStatusChart" style="height: 250px;"></canvas>
                    </div>
                </div>

                <!-- إشعارات ذكية -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bell me-2"></i>
                            الإشعارات
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">لا توجد إشعارات جديدة</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تتبع التقدم في التدريبات الحالية -->
    @if($activeTrainings->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tasks me-2"></i>
                            التدريبات الحالية والتقدم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($activeTrainings as $app)
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded bg-light">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="fw-bold">{{ $app->training->title }}</span>
                                            <span class="badge bg-info">{{ $app->progress }}% مكتمل</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $app->progress }}%" aria-valuenow="{{ $app->progress }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="mt-2 d-flex justify-content-between text-muted small">
                                            <span><i class="fas fa-calendar-start me-1"></i> البداية:
                                                {{ $app->training->start_date->format('Y-m-d') }}</span>
                                            <span><i class="fas fa-calendar-check me-1"></i> النهاية:
                                                {{ $app->training->end_date ? $app->training->end_date->format('Y-m-d') : 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- حالة الترشيحات -->
    @if($nominations->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-check me-2"></i>
                            حالة الترشيحات الوظيفية
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>الشركة</th>
                                        <th>الوظيفة</th>
                                        <th>تاريخ الترشيح</th>
                                        <th>الحالة</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nominations as $nomination)
                                        <tr>
                                            <td>{{ $nomination->jobOpportunity->company->name ?? 'غير محدد' }}</td>
                                            <td>{{ $nomination->jobOpportunity->title ?? 'غير محدد' }}</td>
                                            <td>{{ $nomination->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $nomination->status == 'accepted' ? 'success' : ($nomination->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ $nomination->status_text }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($nomination->nomination_notes, 50) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- الصف السفلي: التدريبات الموصى بها وطلباتي الأخيرة -->
    <div class="row">
        <!-- التدريبات الموصى بها -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2 text-warning"></i>
                        التدريبات الموصى بها
                    </h6>
                    <a href="{{ route('graduate.trainings') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($recommendedTrainings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recommendedTrainings as $training)
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="training-icon bg-light rounded p-2">
                                                <i class="fas fa-graduation-cap text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $training->title }}</h6>
                                            <p class="text-muted small mb-2">{{ Str::limit($training->description, 70) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $training->start_date->format('Y-m-d') }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-primary">
                                                    {{ $training->type_arabic }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد تدريبات موصى بها حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- طلباتي الأخيرة -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2 text-info"></i>
                        طلباتي الأخيرة
                    </h6>
                    <span class="badge bg-primary">{{ $myApplications }}</span>
                </div>
                <div class="card-body">
                    @if($myRecentApplications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myRecentApplications as $application)
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $application->training->title }}</h6>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="badge bg-{{ $application->status == 'approved' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }} me-2">
                                                    @if($application->status == 'pending') قيد المراجعة
                                                    @elseif($application->status == 'approved') مقبول
                                                    @elseif($application->status == 'rejected') مرفوض
                                                    @endif
                                                </span>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $application->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">لا توجد طلبات سابقة</p>
                            <a href="{{ route('graduate.trainings') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-paper-plane me-2"></i>تقديم طلب جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-right: 4px solid var(--university-gold);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--university-blue), #3b82f6);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .training-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Calendar Styling */
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            font-family: 'Tajawal', sans-serif;
        }

        .fc-event {
            cursor: pointer;
        }

        /* ===== تحسينات الموبايل ===== */
        @media (max-width: 768px) {

            /* تقليل الهوامش العامة */
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
            }

            /* بطاقات الإحصائيات */
            .stat-card {
                margin-bottom: 15px;
            }

            .stat-card .card-body {
                padding: 15px !important;
            }

            .card-icon {
                width: 50px;
                height: 50px;
            }

            .card-icon i {
                font-size: 1.5rem !important;
            }

            .text-xs {
                font-size: 0.75rem !important;
            }

            .h5 {
                font-size: 1.1rem !important;
            }

            /* البطاقات العامة */
            .card {
                margin-bottom: 15px;
            }

            .card-header {
                padding: 12px 15px !important;
            }

            .card-header h6 {
                font-size: 0.95rem !important;
            }

            .card-body {
                padding: 15px !important;
            }

            /* التقويم */
            #calendar {
                font-size: 0.85rem;
            }

            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc .fc-toolbar-title {
                font-size: 1rem !important;
                margin: 5px 0;
            }

            .fc .fc-button {
                padding: 5px 10px !important;
                font-size: 0.8rem !important;
            }

            .fc .fc-col-header-cell {
                font-size: 0.75rem !important;
                padding: 5px 2px !important;
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.8rem !important;
            }

            .fc .fc-event {
                font-size: 0.7rem !important;
                padding: 2px 4px !important;
            }

            /* الرسم البياني */
            #applicationStatusChart {
                height: 200px !important;
            }

            /* الجداول */
            .table-responsive {
                font-size: 0.85rem;
            }

            .table td,
            .table th {
                padding: 8px 5px !important;
            }

            /* الأزرار */
            .btn {
                font-size: 0.85rem;
                padding: 6px 12px;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            /* القوائم */
            .list-group-item {
                padding: 10px !important;
                font-size: 0.9rem;
            }

            /* شريط التقدم */
            .progress {
                height: 8px !important;
            }

            /* الشارات */
            .badge {
                font-size: 0.75rem;
                padding: 4px 8px;
            }

            /* تحسين عرض الصفوف */
            .row {
                margin-left: -5px;
                margin-right: -5px;
            }

            .row>[class*="col-"] {
                padding-left: 5px;
                padding-right: 5px;
            }

            /* إخفاء بعض العناصر غير الضرورية في الموبايل */
            .d-none-mobile {
                display: none !important;
            }
        }

        /* شاشات صغيرة جداً (أقل من 576px) */
        @media (max-width: 576px) {
            .card-header h6 {
                font-size: 0.85rem !important;
            }

            .h5 {
                font-size: 1rem !important;
            }

            .fc .fc-toolbar-title {
                font-size: 0.9rem !important;
            }

            .fc .fc-button {
                padding: 4px 8px !important;
                font-size: 0.75rem !important;
            }
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Chart.js: Application Status ---
                var ctx = document.getElementById('applicationStatusChart').getContext('2d');
                var applicationStats = @json($applicationStats);

                var labels = {
                    'pending': 'قيد المراجعة',
                    'approved': 'مقبول',
                    'rejected': 'مرفوض',
                    'withdrawn': 'منسحب'
                };

                var colors = {
                    'pending': '#f6c23e',
                    'approved': '#1cc88a',
                    'rejected': '#e74a3b',
                    'withdrawn': '#858796'
                };

                var chartLabels = Object.keys(applicationStats).map(key => labels[key] || key);
                var chartData = Object.values(applicationStats);
                var chartColors = Object.keys(applicationStats).map(key => colors[key] || '#4e73df');

                if (chartData.length === 0) {
                    // Show empty state or placeholder if no data
                    chartLabels = ['لا توجد بيانات'];
                    chartData = [1];
                    chartColors = ['#eaecf4'];
                }

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: chartColors,
                            hoverBackgroundColor: chartColors,
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
                                fontFamily: 'Tajawal'
                            }
                        },
                        cutoutPercentage: 80,
                    },
                });

                // --- FullCalendar ---
                // --- FullCalendar ---
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    // إجبار التقويم على أخذ ارتفاع مناسب للموبايل
                    if (window.innerWidth < 768) {
                        calendarEl.style.minHeight = "400px";
                    }

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'ar',
                        direction: 'rtl',
                        height: 'auto', // ارتفاع تلقائي مرن
                        contentHeight: 'auto',
                        headerToolbar: {
                            left: 'prev,next',
                            center: 'title',
                            right: 'dayGridMonth,listMonth' // تبسيط الأزرار للموبايل
                        },
                        buttonText: {
                            today: 'اليوم',
                            month: 'شهر',
                            week: 'أسبوع',
                            list: 'قائمة'
                        },
                        events: @json($calendarTrainings),
                        eventClick: function (info) {
                            if (info.event.url) {
                                window.location.href = info.event.url;
                                info.jsEvent.preventDefault();
                            }
                        }
                    });

                    // تأخير بسيط للعرض لضمان تحميل العنصر
                    setTimeout(function () {
                        calendar.render();
                        calendar.updateSize();
                    }, 100);
                }
            });
        </script>
    @endpush
@endsection