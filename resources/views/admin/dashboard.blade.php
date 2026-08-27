@extends('layouts.app')

@section('title', 'لوحة تحكم المدير')
@section('page-title', 'لوحة تحكم مسؤول النظام')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumbs -->
        @include('components.breadcrumbs', [
            'items' => [
                ['label' => 'الرئيسية', 'url' => route('home')],
                ['label' => 'لوحة تحكم المدير', 'active' => true],
            ]
        ])

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white fw-bold mb-4" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">نظرة عامة على النظام</h2>
            </div>
        </div>

        <!-- Row 1: Core Statistics (Bento Grid) -->
        <div class="bento-grid-large mb-4">
            <!-- الشركات -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">الشركات</h3>
                    <div class="bento-card-icon bento-icon-primary">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat" id="stat-companies">{{ $companiesCount ?? 0 }}</div>
                    <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-2">
                        مستمر <i class="fas fa-arrow-trend-up ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">إجمالي الشركات المسجلة بالمعرض والنظام</div>
            </div>

            <!-- المستخدمين -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">المستخدمين</h3>
                    <div class="bento-card-icon bento-icon-success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat" id="stat-users">{{ $usersCount ?? 0 }}</div>
                    <div class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 mb-2">
                        نشط <i class="fas fa-bolt ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">إجمالي الخريجين والمشرفين</div>
            </div>

            <!-- التدريبات المعتمدة -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">التدريبات المعتمدة</h3>
                    <div class="bento-card-icon bento-icon-gold">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat" id="stat-trainings">{{ $approvedTrainingsCount ?? 0 }}</div>
                    <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 mb-2" style="color: #d97706 !important;">
                        متاح <i class="fas fa-check-circle ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">جاهزة ومتاحة لتسجيل الخريجين</div>
            </div>
            
            <!-- طلبات التدريب -->
            <div class="bento-card">
                <div class="bento-card-header">
                    <h3 class="bento-card-title">طلبات التدريب</h3>
                    <div class="bento-card-icon bento-icon-danger">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-1">
                    <div class="bento-stat" id="stat-applications">{{ $applicationsCount ?? 0 }}</div>
                    <div class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 mb-2">
                        جديد <i class="fas fa-fire ms-1"></i>
                    </div>
                </div>
                <div class="bento-desc mt-2">طلبات مسجلة في البرامج التدريبية</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bento-card">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-4">
                        <h3 class="bento-card-title text-white fs-4">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>الإحصائيات التفاعلية
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="row">
                            <!-- مخطط توزيع المستخدمين حسب الدور -->
                            <div class="col-lg-6 mb-4">
                                <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                                    <h5 class="text-white mb-3 fw-bold">توزيع المستخدمين حسب الدور</h5>
                                    <canvas id="usersByRoleChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>

                            <!-- مخطط حالة الشركات -->
                            <div class="col-lg-6 mb-4">
                                <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                                    <h5 class="text-white mb-3 fw-bold">حالة الشركات</h5>
                                    <canvas id="companiesByStatusChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>

                            <!-- مخطط حالة التوظيف -->
                            <div class="col-lg-6 mb-4">
                                <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                                    <h5 class="text-white mb-3 fw-bold">حالة توظيف الخريجين</h5>
                                    <canvas id="employmentStatusChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>

                            <!-- مخطط النشاط الشهري -->
                            <div class="col-lg-6 mb-4">
                                <div class="p-3 h-100" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px;">
                                    <h5 class="text-white mb-3 fw-bold">النشاط الشهري لطلبات التدريب</h5>
                                    <canvas id="monthlyActivityChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="bento-card h-100">
                    <div class="bento-card-header border-bottom border-secondary pb-3 mb-3">
                        <h3 class="bento-card-title text-white fs-5">
                            <i class="fas fa-history me-2 text-primary"></i>أحدث المستخدمين
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($recentUsers->isEmpty())
                            <p class="text-white-50 text-center py-4">لا يوجد مستخدمون جدد لعرضهم.</p>
                        @else
                            <ul class="list-group list-group-flush bg-transparent">
                                @foreach($recentUsers as $user)
                                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 45px; height: 45px;">
                                                <i class="fas fa-user-circle fa-lg"></i>
                                            </div>
                                            <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="bento-card h-100">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-3">
                    <h3 class="bento-card-title text-white fs-5">
                        <i class="fas fa-building me-2 text-info"></i>أحدث الشركات
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($recentCompanies->isEmpty())
                        <p class="text-white-50 text-center py-4">لا توجد شركات جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($recentCompanies as $company)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 45px; height: 45px;">
                                            <i class="fas fa-industry fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-white">{{ $company->name }}</h6>
                                            <small class="text-white-50">{{ $company->email }}</small>
                                        </div>
                                    </div>
                                    @if($company->is_approved)
                                        <span class="bento-badge bento-badge-success">معتمدة</span>
                                    @else
                                        <span class="bento-badge bento-badge-warning">قيد الانتظار</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="bento-card h-100">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-3">
                    <h3 class="bento-card-title text-white fs-5">
                        <i class="fas fa-file-alt me-2 text-warning"></i>أحدث طلبات التدريب
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($recentTrainingApplications->isEmpty())
                        <p class="text-white-50 text-center py-4">لا توجد طلبات تدريب جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($recentTrainingApplications as $application)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 45px; height: 45px;">
                                            <i class="fas fa-user-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-white">{{ $application->user->name ?? 'N/A' }}</h6>
                                            <small class="text-white-50">برنامج: {{ $application->training->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="bento-badge bento-badge-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
                                        {{ $application->status == 'pending' ? 'معلق' : ($application->status == 'approved' ? 'موافق عليه' : 'مرفوض') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="bento-card h-100">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-3">
                    <h3 class="bento-card-title text-white fs-5">
                        <i class="fas fa-briefcase me-2 text-success"></i>أحدث فرص العمل
                    </h3>
                </div>
                <div class="card-body p-0">
                    @if($recentJobOpportunities->isEmpty())
                        <p class="text-white-50 text-center py-4">لا توجد فرص عمل جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush bg-transparent">
                            @foreach($recentJobOpportunities as $job)
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0 mb-2" style="border-bottom: 1px solid rgba(255,255,255,0.05) !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 45px; height: 45px;">
                                            <i class="fas fa-tag fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-white text-truncate" style="max-width: 200px;">{{ $job->title }}</h6>
                                            <small class="text-white-50">الشركة: {{ $job->company->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="bento-badge bento-badge-{{ $job->status == 'active' ? 'success' : 'primary' }}">
                                        {{ $job->status == 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="bento-card">
                <div class="bento-card-header border-bottom border-secondary pb-3 mb-4">
                    <h3 class="bento-card-title text-white fs-4">
                        <i class="fas fa-bolt me-2 text-gold"></i>إجراءات سريعة
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="row g-3">
                        <!-- إدارة الشركات -->
                        <div class="col-md-3">
                            <a href="{{ route('admin.companies.create') }}" class="btn-bento w-100 d-block text-center text-decoration-none">
                                <i class="fas fa-plus me-2"></i>إضافة شركة
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.companies') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                <i class="fas fa-building me-2"></i>إدارة الشركات
                            </a>
                        </div>
                        <!-- إدارة برامج التدريب -->
                        <div class="col-md-3">
                            <a href="{{ route('admin.trainings.create') }}" class="btn-bento w-100 d-block text-center text-decoration-none" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-plus me-2"></i>إضافة تدريب
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.trainings') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                <i class="fas fa-graduation-cap me-2"></i>برامج التدريب
                            </a>
                        </div>
                        <!-- إدارة المستخدمين -->
                        <div class="col-md-3">
                            <a href="{{ route('admin.users') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                <i class="fas fa-users me-2"></i>إدارة المستخدمين
                            </a>
                        </div>
                        <!-- التقارير -->
                        <div class="col-md-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none" style="border-color: var(--bento-gold); color: var(--bento-gold);">
                                <i class="fas fa-chart-bar me-2"></i>التقارير
                            </a>
                        </div>
                        <!-- طلبات التدريب -->
                        <div class="col-md-3">
                            <a href="{{ route('admin.applications.index') }}" class="btn-bento-outline w-100 d-block text-center text-decoration-none">
                                <i class="fas fa-file-alt me-2"></i>طلبات التدريب
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // بيانات المخططات من PHP
            const chartData = @json($chartData);

            // تهيئة المخططات
            initDashboardCharts(chartData);
        });

        function initDashboardCharts(chartData) {
            // مخطط توزيع المستخدمين حسب الدور
            const usersByRoleCtx = document.getElementById('usersByRoleChart');
            if (usersByRoleCtx && chartData.usersByRole) {
                new Chart(usersByRoleCtx, {
                    type: 'bar',
                    data: chartData.usersByRole,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'عدد المستخدمين'
                                }
                            }
                        }
                    }
                });
            }

            // مخطط حالة الشركات
            const companiesByStatusCtx = document.getElementById('companiesByStatusChart');
            if (companiesByStatusCtx && chartData.companiesByStatus) {
                new Chart(companiesByStatusCtx, {
                    type: 'doughnut',
                    data: chartData.companiesByStatus,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // مخطط حالة التوظيف
            const employmentStatusCtx = document.getElementById('employmentStatusChart');
            if (employmentStatusCtx && chartData.employmentStatus) {
                new Chart(employmentStatusCtx, {
                    type: 'pie',
                    data: chartData.employmentStatus,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }

            // مخطط النشاط الشهري
            const monthlyActivityCtx = document.getElementById('monthlyActivityChart');
            if (monthlyActivityCtx && chartData.monthlyActivity) {
                new Chart(monthlyActivityCtx, {
                    type: 'line',
                    data: chartData.monthlyActivity,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'عدد الطلبات'
                                }
                            }
                        }
                    }
                });
            }
        }

        // Live Dashboard Updates
        setInterval(function() {
            fetch('{{ route('admin.dashboard.live-stats') }}')
                .then(response => response.json())
                .then(data => {
                    // Update stats cards with smooth animation if value changed
                    updateStatWithAnimation('stat-companies', data.companiesCount);
                    updateStatWithAnimation('stat-users', data.usersCount);
                    updateStatWithAnimation('stat-trainings', data.approvedTrainingsCount);
                    updateStatWithAnimation('stat-applications', data.applicationsCount);
                })
                .catch(error => console.error('Error fetching live stats:', error));
        }, 10000); // Poll every 10 seconds

        function updateStatWithAnimation(elementId, newValue) {
            const el = document.getElementById(elementId);
            if (el && el.innerText != newValue) {
                el.style.opacity = '0';
                setTimeout(() => {
                    el.innerText = newValue;
                    el.style.opacity = '1';
                }, 300);
            }
        }
    </script>
@endsection
