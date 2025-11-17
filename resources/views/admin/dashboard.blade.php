@extends('layouts.app')

@section('title', 'لوحة تحكم المدير')

@section('page-title', 'لوحة تحكم مسؤول النظام')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="text-primary fw-bold mb-4">نظرة عامة على النظام</h2>
    </div>

    <!-- Charts Section -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>إحصائيات تفاعلية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- مخطط توزيع المستخدمين حسب الدور -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0">توزيع المستخدمين حسب الدور</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="usersByRoleChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- مخطط حالة الشركات -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0">حالة الشركات</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="companiesByStatusChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- مخطط حالة التوظيف -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0">حالة توظيف الخريجين</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="employmentStatusChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- مخطط النشاط الشهري -->
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0">النشاط الشهري لطلبات التدريب</h6>
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
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">الشركات</h5>
                        <h3 class="mb-0">{{ $companiesCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي الشركات المسجلة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">المستخدمين</h5>
                        <h3 class="mb-0">{{ $usersCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي المستخدمين المسجلين</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">فرص العمل</h5>
                        <h3 class="mb-0">{{ $jobOpportunitiesCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي فرص العمل</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">برامج التدريب</h5>
                        <h3 class="mb-0">{{ $trainingsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي برامج التدريب</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Detailed Statistics -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-success">شركات معتمدة</h5>
                        <h3 class="mb-0">{{ $approvedCompaniesCount ?? 0 }}</h3>
                        <small class="text-muted">الشركات التي تم اعتمادها</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-warning">شركات قيد الانتظار</h5>
                        <h3 class="mb-0">{{ $pendingCompaniesCount ?? 0 }}</h3>
                        <small class="text-muted">الشركات بانتظار الموافقة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-info">الخريجون</h5>
                        <h3 class="mb-0">{{ $graduatesCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي بيانات الخريجين</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-info">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-success">خريجون موظفون</h5>
                        <h3 class="mb-0">{{ $employedGraduatesCount ?? 0 }}</h3>
                        <small class="text-muted">الخريجون الذين تم توظيفهم</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-success">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Applications and Documents -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">طلبات التدريب</h5>
                        <h3 class="mb-0">{{ $applicationsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي طلبات التدريب</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-warning">طلبات تدريب معلقة</h5>
                        <h3 class="mb-0">{{ $pendingApplicationsCount ?? 0 }}</h3>
                        <small class="text-muted">طلبات بانتظار المراجعة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">الترشيحات</h5>
                        <h3 class="mb-0">{{ $nominationsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي ترشيحات العمل</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-warning">ترشيحات معلقة</h5>
                        <h3 class="mb-0">{{ $pendingNominationsCount ?? 0 }}</h3>
                        <small class="text-muted">ترشيحات بانتظار المراجعة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-primary">وثائق الشراكة</h5>
                        <h3 class="mb-0">{{ $partnershipDocumentsCount ?? 0 }}</h3>
                        <small class="text-muted">إجمالي وثائق الشراكة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon">
                            <i class="fas fa-file-contract"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title text-warning">وثائق شراكة معلقة</h5>
                        <h3 class="mb-0">{{ $pendingPartnershipDocumentsCount ?? 0 }}</h3>
                        <small class="text-muted">وثائق بانتظار المراجعة</small>
                    </div>
                    <div class="col-4 text-end">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-file-signature"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>أحدث المستخدمين
                </h5>
            </div>
            <div class="card-body">
                @if($recentUsers->isEmpty())
                    <p class="text-muted text-center">لا يوجد مستخدمون جدد لعرضهم.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-circle me-2 text-primary"></i>
                                    {{ $user->name }}
                                    <small class="text-muted d-block">{{ $user->email }}</small>
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
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>أحدث الشركات
                </h5>
            </div>
            <div class="card-body">
                @if($recentCompanies->isEmpty())
                    <p class="text-muted text-center">لا توجد شركات جديدة لعرضها.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentCompanies as $company)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-industry me-2 text-primary"></i>
                                    {{ $company->name }}
                                    <small class="text-muted d-block">{{ $company->email }}</small>
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
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>أحدث طلبات التدريب
                </h5>
            </div>
            <div class="card-body">
                @if($recentTrainingApplications->isEmpty())
                    <p class="text-muted text-center">لا توجد طلبات تدريب جديدة لعرضها.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentTrainingApplications as $application)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-check me-2 text-primary"></i>
                                    {{ $application->user->name ?? 'N/A' }}
                                    <small class="text-muted d-block">برنامج: {{ $application->training->name ?? 'N/A' }}</small>
                                </div>
                                <span class="badge bg-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'approved' ? 'success' : 'danger') }}">
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
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-briefcase me-2"></i>أحدث فرص العمل
                </h5>
            </div>
            <div class="card-body">
                @if($recentJobOpportunities->isEmpty())
                    <p class="text-muted text-center">لا توجد فرص عمل جديدة لعرضها.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentJobOpportunities as $job)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    {{ $job->title }}
                                    <small class="text-muted d-block">الشركة: {{ $job->company->name ?? 'N/A' }}</small>
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
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- إدارة الشركات -->
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary w-100">
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
                        <a href="{{ route('admin.trainings.create') }}" class="btn btn-success w-100">
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
                        <a href="{{ route('admin.career-guidance.graduates.create') }}"" class="btn btn-info w-100">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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
