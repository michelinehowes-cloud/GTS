@extends('layouts.app')

@section('title', 'لوحة تحكم المدير')

@section('page-title', 'لوحة تحكم مسؤول النظام')

@section('content')
    <!-- Breadcrumbs -->
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم المدير', 'active' => true],
        ]
    ])

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary fw-bold mb-4">نظرة عامة على النظام</h2>
        </div>

        <!-- Charts Section -->
        <div class="col-12 mb-4">
            <div class="card-modern">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-chart-bar me-2"></i>إحصائيات تفاعلية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- مخطط توزيع المستخدمين حسب الدور -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 fw-bold">توزيع المستخدمين حسب الدور</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="usersByRoleChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- مخطط حالة الشركات -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 fw-bold">حالة الشركات</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="companiesByStatusChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- مخطط حالة التوظيف -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 fw-bold">حالة توظيف الخريجين</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="employmentStatusChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- مخطط النشاط الشهري -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 fw-bold">النشاط الشهري لطلبات التدريب</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyActivityChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 1: Core Statistics -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'الشركات',
                'value' => $companiesCount ?? 0,
                'icon' => 'fas fa-building',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي الشركات المسجلة'
            ])

            @include('components.stat-card', [
                'title' => 'المستخدمين',
                'value' => $usersCount ?? 0,
                'icon' => 'fas fa-users',
                'color' => 'primary', // Using distinct color if needed, but primary fits 'Core'
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي المستخدمين المسجلين'
            ])

            @include('components.stat-card', [
                'title' => 'فرص العمل',
                'value' => $jobOpportunitiesCount ?? 0,
                'icon' => 'fas fa-briefcase',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي فرص العمل'
            ])

            @include('components.stat-card', [
                'title' => 'برامج التدريب',
                'value' => $trainingsCount ?? 0,
                'icon' => 'fas fa-graduation-cap',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي برامج التدريب'
            ])
        </div>

        <!-- Row 2: Detailed Statistics -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'شركات معتمدة',
                'value' => $approvedCompaniesCount ?? 0,
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'الشركات التي تم اعتمادها'
            ])

            @include('components.stat-card', [
                'title' => 'شركات قيد الانتظار',
                'value' => $pendingCompaniesCount ?? 0,
                'icon' => 'fas fa-hourglass-half',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'الشركات بانتظار الموافقة'
            ])

            @include('components.stat-card', [
                'title' => 'الخريجون',
                'value' => $graduatesCount ?? 0,
                'icon' => 'fas fa-user-graduate',
                'color' => 'info',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي بيانات الخريجين'
            ])

            @include('components.stat-card', [
                'title' => 'خريجون موظفون',
                'value' => $employedGraduatesCount ?? 0,
                'icon' => 'fas fa-user-tie',
                'color' => 'success',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'الخريجون الذين تم توظيفهم'
            ])
        </div>

        <!-- Row 3: Applications and Documents -->
        <div class="row mb-4">
            @include('components.stat-card', [
                'title' => 'طلبات التدريب',
                'value' => $applicationsCount ?? 0,
                'icon' => 'fas fa-file-alt',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي طلبات التدريب'
            ])

            @include('components.stat-card', [
                'title' => 'طلبات تدريب معلقة',
                'value' => $pendingApplicationsCount ?? 0,
                'icon' => 'fas fa-clock',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'طلبات بانتظار المراجعة'
            ])

            @include('components.stat-card', [
                'title' => 'الترشيحات',
                'value' => $nominationsCount ?? 0,
                'icon' => 'fas fa-handshake',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي ترشيحات العمل'
            ])

            @include('components.stat-card', [
                'title' => 'ترشيحات معلقة',
                'value' => $pendingNominationsCount ?? 0,
                'icon' => 'fas fa-user-clock',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'ترشيحات بانتظار المراجعة'
            ])

            @include('components.stat-card', [
                'title' => 'وثائق الشراكة',
                'value' => $partnershipDocumentsCount ?? 0,
                'icon' => 'fas fa-file-contract',
                'color' => 'primary',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'إجمالي وثائق الشراكة'
            ])

            @include('components.stat-card', [
                'title' => 'وثائق شراكة معلقة',
                'value' => $pendingPartnershipDocumentsCount ?? 0,
                'icon' => 'fas fa-file-signature',
                'color' => 'warning',
                'col' => 'col-xl-3 col-md-6 mb-4',
                'description' => 'وثائق بانتظار المراجعة'
            ])
        </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-history me-2"></i>أحدث المستخدمين
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentUsers->isEmpty())
                        <p class="text-muted text-center">لا يوجد مستخدمون جدد لعرضهم.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($recentUsers as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
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
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-building me-2"></i>أحدث الشركات
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentCompanies->isEmpty())
                        <p class="text-muted text-center">لا توجد شركات جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($recentCompanies as $company)
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                            <i class="fas fa-industry fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $company->name }}</h6>
                                            <small class="text-muted">{{ $company->email }}</small>
                                        </div>
                                    </div>
                                    @if($company->is_approved)
                                        <span class="badge bg-success">معتمدة</span>
                                    @else
                                        <span class="badge bg-warning">قيد الانتظار</span>
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
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-file-alt me-2"></i>أحدث طلبات التدريب
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentTrainingApplications->isEmpty())
                        <p class="text-muted text-center">لا توجد طلبات تدريب جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($recentTrainingApplications as $application)
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $application->user->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">برنامج: {{ $application->training->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span
                                        class="badge bg-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
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
            <div class="card-modern h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-briefcase me-2"></i>أحدث فرص العمل
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentJobOpportunities->isEmpty())
                        <p class="text-muted text-center">لا توجد فرص عمل جديدة لعرضها.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($recentJobOpportunities as $job)
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                            <i class="fas fa-tag fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-truncate" style="max-width: 200px;">{{ $job->title }}</h6>
                                            <small class="text-muted">الشركة: {{ $job->company->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $job->status == 'active' ? 'success' : 'secondary' }}">
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
            <div class="card-modern">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-bolt me-2"></i>إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- إدارة الشركات -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary-modern w-100">
                                <i class="fas fa-plus me-2"></i>إضافة شركة
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.companies') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-building me-2"></i>إدارة الشركات
                            </a>
                        </div>
                        <!-- إدارة برامج التدريب -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.trainings.create') }}" class="btn btn-success-modern w-100">
                                <i class="fas fa-plus me-2"></i>إضافة برنامج تدريب
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.trainings') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-graduation-cap me-2"></i>إدارة برامج التدريب
                            </a>
                        </div>
                        <!-- إدارة الإرشاد المهني -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.career-guidance.graduates.create') }}" class="btn btn-info-modern w-100 text-white">
                                <i class="fas fa-compass me-2"></i>اضافة خريج
                            </a>
                        </div>
                        <!-- إدارة المستخدمين -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users me-2"></i>إدارة المستخدمين
                            </a>
                        </div>
                        <!-- التقارير -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>التقارير والإحصائيات
                            </a>
                        </div>
                        <!-- طلبات التدريب -->
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-info w-100">
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
    </script>
@endsection