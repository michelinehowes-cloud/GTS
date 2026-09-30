@extends('layouts.app')

@section('title', 'لوحة تحكم المدير')
@section('page-title', 'لوحة تحكم مسؤول النظام')

@section('content')
<div class="container-fluid">
    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="لوحة تحكم مسؤول النظام"
        subtitle="نظرة شاملة ومؤشرات أداء المنظومة، الخريجين، والشركات ومعارض التوظيف"
        icon="fas fa-user-shield"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم المدير']
        ]"
        secondaryBadge="مدير النظام العام"
        secondaryBadgeIcon="fas fa-shield-alt"
    >
        <a href="{{ route('job-fair.admin.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-calendar-alt fs-6"></i>
            <span>فعاليات</span>
        </a>
        <a href="{{ route('admin.companies') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-building fs-6"></i>
            <span>الشركات</span>
        </a>
        <a href="{{ route('admin.settings.ai') }}" class="btn btn-outline-light text-white border-white border-opacity-50 fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-robot text-warning fs-6"></i>
            <span>إعدادات المساعد الذكي</span>
        </a>
    </x-page-hero>

    @if(isset($pendingGraduatesCount) && $pendingGraduatesCount > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning bg-opacity-25 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                <i class="fas fa-user-clock fa-lg"></i>
            </div>
            <div>
                <strong class="d-block text-dark fs-6">
                    @if($pendingGraduatesCount == 1)
                        يوجد طلب تسجيل خريج جديد بانتظار الاعتماد والموافقة
                    @elseif($pendingGraduatesCount == 2)
                        يوجد طلبان لتسجيل خريجين بانتظار الاعتماد والموافقة
                    @elseif($pendingGraduatesCount >= 3 && $pendingGraduatesCount <= 10)
                        يوجد {{ $pendingGraduatesCount }} طلبات تسجيل خريجين جديدة بانتظار الاعتماد والموافقة
                    @else
                        يوجد {{ $pendingGraduatesCount }} طلباً لتسجيل خريجين بانتظار الاعتماد والموافقة
                    @endif
                </strong>
                <span class="text-muted small">يمكنك مراجعة وتدقيق بيانات الخريجين وتفعيل حساباتهم فوراً ليتمكنوا من تسجيل الدخول واستخدام المنظومة.</span>
            </div>
        </div>
        <a href="{{ route('career-guidance.pending-approvals') }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
            <i class="fas fa-check-circle me-1"></i> مراجعة واعتماد الطلبات
        </a>
    </div>
    @endif

    <!-- 📊 الإحصائيات الرئيسية (2x2 على الموبايل) -->
    <div class="row mb-3">
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'الشركات المسجلة',
            'value' => $companiesCount ?? 0,
            'icon' => 'fas fa-building',
            'color' => 'primary',
            'link' => route('admin.companies')
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'المستخدمين والخريجين',
            'value' => $usersCount ?? 0,
            'icon' => 'fas fa-users',
            'color' => 'success',
            'link' => route('admin.users')
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'التدريبات المعتمدة',
            'value' => $approvedTrainingsCount ?? 0,
            'icon' => 'fas fa-graduation-cap',
            'color' => 'warning',
            'link' => route('admin.trainings')
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'طلبات التدريب',
            'value' => $applicationsCount ?? 0,
            'icon' => 'fas fa-file-alt',
            'color' => 'info',
            'link' => route('admin.applications.index')
        ])
    </div>

    <!-- 📈 المخططات البيانية التفاعلية -->
    <div class="row mb-4">
        <!-- مخطط توزيع المستخدمين حسب الدور -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark mb-3 fs-6">
                    <i class="fas fa-chart-pie me-2 text-primary"></i> توزيع المستخدمين حسب الدور
                </h5>
                <div style="position: relative; height: 360px; width: 100%;">
                    <canvas id="usersByRoleChart"></canvas>
                </div>
            </div>
        </div>

        <!-- مخطط حالة الشركات -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark mb-3 fs-6">
                    <i class="fas fa-chart-bar me-2 text-success"></i> حالة الشركات
                </h5>
                <div style="position: relative; height: 360px; width: 100%;">
                    <canvas id="companiesByStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- مخطط حالة التوظيف -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark mb-3 fs-6">
                    <i class="fas fa-chart-doughnut me-2 text-info"></i> حالة توظيف الخريجين
                </h5>
                <div style="position: relative; height: 360px; width: 100%;">
                    <canvas id="employmentStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- مخطط النشاط الشهري -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark mb-3 fs-6">
                    <i class="fas fa-chart-line me-2 text-warning"></i> النشاط الشهري لطلبات التدريب
                </h5>
                <div style="position: relative; height: 360px; width: 100%;">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 📋 القوائم السريعة والأنشطة الحديثة -->
    <div class="row mb-4">
        <!-- أحدث المستخدمين -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-user-plus me-2 text-primary"></i> أحدث المستخدمين
                    </h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-link text-primary p-0 text-decoration-none">عرض الكل</a>
                </div>
                @if($recentUsers->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 small">لا يوجد مستخدمون جدد لعرضهم</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentUsers as $user)
                            <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.85rem;">{{ $user->name }}</h6>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $user->email }}</small>
                                    </div>
                                </div>
                                <span class="badge bg-light text-primary border border-primary rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                    {{ $user->role_name ?? $user->role }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- أحدث الشركات -->
        <div class="col-12 col-lg-6 mb-3 mb-lg-4">
            <div class="card-modern h-100 p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold text-dark fs-6 mb-0">
                        <i class="fas fa-building me-2 text-info"></i> أحدث الشركات
                    </h5>
                    <a href="{{ route('admin.companies') }}" class="btn btn-sm btn-link text-info p-0 text-decoration-none">عرض الكل</a>
                </div>
                @if($recentCompanies->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 small">لا توجد شركات جديدة لعرضها</p>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentCompanies as $company)
                            <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light text-info d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.9rem;">
                                        <i class="fas fa-industry"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.85rem;">{{ $company->name }}</h6>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $company->email }}</small>
                                    </div>
                                </div>
                                <span class="badge rounded-pill px-2 py-1 {{ $company->is_approved ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                    {{ $company->is_approved ? 'معتمدة' : 'قيد الانتظار' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ⚡ إجراءات سريعة للمدير -->
    <div class="card-modern mb-4 p-3 p-md-4">
        <div class="border-bottom pb-2 mb-3">
            <h5 class="fw-bold text-dark fs-6 mb-0">
                <i class="fas fa-bolt me-2 text-warning"></i> إجراءات سريعة
            </h5>
        </div>
        <div class="row g-2">
            <div class="col-6 col-md">
                <a href="{{ route('admin.companies.create') }}" class="btn btn-outline-primary w-100 rounded-3 py-2 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fas fa-plus me-1"></i> إضافة شركة
                </a>
            </div>
            <div class="col-6 col-md">
                <a href="{{ route('admin.trainings.create') }}" class="btn btn-outline-success w-100 rounded-3 py-2 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fas fa-plus-circle me-1"></i> إضافة تدريب
                </a>
            </div>
            <div class="col-6 col-md">
                <a href="{{ route('job-fair.admin.create') }}" class="btn btn-outline-warning w-100 rounded-3 py-2 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fas fa-store me-1"></i> إنشاء معرض
                </a>
            </div>
            <div class="col-6 col-md">
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info w-100 rounded-3 py-2 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fas fa-chart-bar me-1"></i> تقارير النظام
                </a>
            </div>
            <div class="col-12 col-md">
                <a href="{{ route('admin.settings.ai') }}" class="btn btn-outline-dark w-100 rounded-3 py-2 text-nowrap" style="font-size: 0.8rem;">
                    <i class="fas fa-robot text-warning me-1"></i> إعدادات المساعد الذكي
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commonChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    bottom: 12
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    labels: {
                        boxWidth: 14,
                        padding: 12,
                        font: { family: 'Tajawal', size: 12, weight: '500' }
                    }
                },
                tooltip: {
                    bodyFont: { family: 'Tajawal', size: 12 },
                    titleFont: { family: 'Tajawal', size: 13, weight: 'bold' },
                    padding: 10,
                    cornerRadius: 8
                }
            }
        };

        // 1. Users by Role (من قاعدة البيانات الفعلية)
        new Chart(document.getElementById('usersByRoleChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartsData['usersByRole']['labels'] ?? []) !!},
                datasets: [{
                    data: {!! json_encode($chartsData['usersByRole']['data'] ?? []) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                ...commonChartOptions,
                cutout: '58%'
            }
        });

        // 2. Companies by Status (من قاعدة البيانات الفعلية)
        new Chart(document.getElementById('companiesByStatusChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($chartsData['companiesByStatus']['labels'] ?? []) !!},
                datasets: [{
                    data: {!! json_encode($chartsData['companiesByStatus']['data'] ?? []) !!},
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: commonChartOptions
        });

        // 3. Employment Status (من قاعدة البيانات الفعلية)
        new Chart(document.getElementById('employmentStatusChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartsData['employmentStatus']['labels'] ?? []) !!},
                datasets: [{
                    label: 'العدد',
                    data: {!! json_encode($chartsData['employmentStatus']['data'] ?? []) !!},
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#06b6d4', '#ef4444'],
                    borderRadius: 6
                }]
            },
            options: {
                ...commonChartOptions,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
            }
        });

        // 4. Monthly Activity (من قاعدة البيانات الفعلية)
        new Chart(document.getElementById('monthlyActivityChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartsData['monthlyActivity']['labels'] ?? []) !!},
                datasets: [{
                    label: 'طلبات التدريب',
                    data: {!! json_encode($chartsData['monthlyActivity']['data'] ?? []) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                ...commonChartOptions,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
            }
        });
    });
</script>
@endsection
