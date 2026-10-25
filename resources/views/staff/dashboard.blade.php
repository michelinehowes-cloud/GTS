@extends('layouts.app')

@section('title', auth()->user()->name . ' - لوحة التحكم')

@push('styles')
<style>
    .module-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        overflow: hidden;
    }
    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(3, 72, 138, 0.12);
        border-color: rgba(3, 72, 138, 0.3);
    }
    .module-header {
        padding: 1.25rem 1.25rem 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .module-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .module-body {
        padding: 0 1.25rem 1rem;
        flex-grow: 1;
    }
    .module-footer {
        padding: 0.85rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .perm-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 50rem;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="{{ auth()->user()->name }}"
        subtitle="لوحة تحكم الموظف (صلاحيات مخصصة) — متابعة وإدارة الأقسام والعمليات المتاحة لك وفق الصلاحيات الممنوحة"
        icon="fas fa-user-shield"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => auth()->user()->name]
        ]"
        secondaryBadge="موظف مخصص الصلاحيات"
        secondaryBadgeIcon="fas fa-id-badge"
    >
        <span class="text-white-50 small d-none d-md-inline me-2"><i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}</span>
    </x-page-hero>

    <!-- شريط الصلاحيات المفعلة -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-key text-warning fs-5"></i>
                <span class="fw-bold text-dark small">الوحدات الإدارية المتاحة لحسابك:</span>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-1.5">
                @if(!empty($modules['trainings']))
                    <span class="perm-pill"><i class="fas fa-graduation-cap"></i> برامج التدريب</span>
                @endif
                @if(!empty($modules['jobs']))
                    <span class="perm-pill"><i class="fas fa-briefcase"></i> الفرص الوظيفية</span>
                @endif
                @if(!empty($modules['companies']))
                    <span class="perm-pill"><i class="fas fa-building"></i> الشركات والشراكات</span>
                @endif
                @if(!empty($modules['job_fair']))
                    <span class="perm-pill"><i class="fas fa-store"></i> معرض التوظيف</span>
                @endif
                @if(!empty($modules['graduates']))
                    <span class="perm-pill"><i class="fas fa-user-graduate"></i> شؤون الخريجين</span>
                @endif
                @if(!empty($modules['evaluations']))
                    <span class="perm-pill"><i class="fas fa-poll"></i> الاستبيانات والتقييمات</span>
                @endif
                @if(!empty($modules['users']))
                    <span class="perm-pill"><i class="fas fa-users-cog"></i> إدارة المستخدمين</span>
                @endif
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات السريعة للوحدات الممنوحة -->
    <div class="row g-3 mb-4">
        @if(!empty($modules['trainings']))
            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'إجمالي البرامج التدريبية',
                'value' => $stats['trainings_total'] ?? 0,
                'icon' => 'fas fa-graduation-cap',
                'color' => 'primary',
                'description' => 'البرامج المسجلة بالمنظومة',
                'link' => route('training-coordinator.trainings')
            ])

            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'طلبات التدريب المعلقة',
                'value' => $stats['training_applications_pending'] ?? 0,
                'icon' => 'fas fa-clock',
                'color' => 'warning',
                'description' => 'طلبات بحاجة لمراجعة واعتماد',
                'link' => route('training-coordinator.applications')
            ])
        @endif

        @if(!empty($modules['jobs']))
            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'الفرص الوظيفية النشطة',
                'value' => $stats['jobs_open'] ?? 0,
                'icon' => 'fas fa-briefcase',
                'color' => 'success',
                'description' => 'فرص عمل مفتوحة للتقديم',
                'link' => route('job-opportunities.index')
            ])
        @endif

        @if(!empty($modules['companies']))
            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'المؤسسات والشركات الشريكة',
                'value' => $stats['companies_total'] ?? 0,
                'icon' => 'fas fa-building',
                'color' => 'info',
                'description' => 'المؤسسات المعتمدة بالمنظومة',
                'link' => route('partnership.companies')
            ])
        @endif

        @if(!empty($modules['job_fair']))
            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'مسجلو معرض التوظيف',
                'value' => $stats['job_fair_visitors'] ?? 0,
                'icon' => 'fas fa-store',
                'color' => 'warning',
                'description' => $stats['job_fair'] ? $stats['job_fair']->title : 'المعرض الحالي',
                'link' => route('job-fair.admin.index')
            ])
        @endif

        @if(!empty($modules['graduates']))
            @include('components.stat-card', [
                'col' => 'col-xl-3 col-md-6',
                'title' => 'قاعدة بيانات الخريجين',
                'value' => $stats['graduates_total'] ?? 0,
                'icon' => 'fas fa-user-graduate',
                'color' => 'secondary',
                'description' => 'الخريجون المسجلون بالمنظومة',
                'link' => route('career-guidance.graduates.index')
            ])
        @endif
    </div>

    <!-- شبكة بطاقات الوحدات الإدارية المتاحة (Fast Action Hub) -->
    <h5 class="fw-bold text-dark mb-3">
        <i class="fas fa-th-large text-primary me-2"></i>بوابات العمل والوحدات المتاحة
    </h5>

    <div class="row g-3 mb-4">
        {{-- 1. وحدة التدريب والتأهيل --}}
        @if(!empty($modules['trainings']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">برامج التدريب والتأهيل</h6>
                        <small class="text-muted">إدارة الدورات ومتابعة الحضور والطلبات</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">يمكنك استعراض الدورات، تسجيل حضور المتدربين، ومراجعة طلبات الالتحاق بحسب الصلاحيات الممنوحة لك.</p>
                </div>
                <div class="module-footer">
                    @if(auth()->user()->hasPermission('trainings.view') || auth()->user()->isAdmin())
                        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-list me-1"></i> البرامج
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.attendance') || auth()->user()->isAdmin())
                        <a href="{{ route('training-coordinator.calendar') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-calendar-alt me-1"></i> التقويم
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('trainings.applications') || auth()->user()->isAdmin())
                        <a href="{{ route('training-coordinator.applications') }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="fas fa-envelope-open-text me-1"></i> الطلبات
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 2. وحدة الفرص الوظيفية والترشيحات --}}
        @if(!empty($modules['jobs']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">الفرص الوظيفية والترشيحات</h6>
                        <small class="text-muted">إدارة الوظائف الشاغرة والترشيح</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">متابعة الفرص الوظيفية المقدمة من الشركات الشريكة وإدارة طلبات التوظيف والترشيحات.</p>
                </div>
                <div class="module-footer">
                    @if(auth()->user()->hasPermission('jobs.view') || auth()->user()->isAdmin())
                        <a href="{{ route('job-opportunities.index') }}" class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="fas fa-briefcase me-1"></i> فرص العمل
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('nominations.manage') || auth()->user()->isAdmin())
                        <a href="{{ route('career-guidance.nominations.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-paper-plane me-1"></i> الترشيحات
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 3. وحدة الشركات والمؤسسات الشريكة --}}
        @if(!empty($modules['companies']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">الشركات والشراكات</h6>
                        <small class="text-muted">دليل المؤسسات واتفاقيات التعاون</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">استعراض المؤسسات الشريكة المعتمدة، تحديث بياناتها، ومتابعة وثائق الشراكة الاستراتيجية.</p>
                </div>
                <div class="module-footer">
                    @if(auth()->user()->hasPermission('companies.view') || auth()->user()->isAdmin())
                        <a href="{{ route('partnership.companies') }}" class="btn btn-sm btn-outline-info rounded-pill">
                            <i class="fas fa-building me-1"></i> دليل الشركات
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('partnerships.documents') || auth()->user()->isAdmin())
                        <a href="{{ route('partnership.documents') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-file-contract me-1"></i> الوثائق
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 4. وحدة معرض التوظيف 2026 --}}
        @if(!empty($modules['job_fair']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">معرض التوظيف والفعاليات</h6>
                        <small class="text-muted">الملتقيات والمعارض السنوية</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">استعراض تفاصيل المعرض، الشركات المشاركة، ومشاريع التخرج بحسب مستوى صلاحياتك المعتمدة.</p>
                </div>
                <div class="module-footer">
                    <a href="{{ route('job-fair.admin.index') }}" class="btn btn-sm btn-outline-warning rounded-pill">
                        <i class="fas fa-store me-1"></i> إدارة المعارض
                    </a>
                    <a href="{{ route('job-fair.public') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="fas fa-external-link-alt me-1"></i> الصفحة العامة
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- 5. وحدة الخريجين والإرشاد المهني --}}
        @if(!empty($modules['graduates']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-secondary bg-opacity-10 text-secondary">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">شؤون وتأهيل الخريجين</h6>
                        <small class="text-muted">قاعدة بيانات وتأهيل الخريجين</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">الاطلاع على سجلات الخريجين، بياناتهم الأكاديمية ومهاراتهم المهنية والترشيحات الوظيفية.</p>
                </div>
                <div class="module-footer">
                    @if(auth()->user()->hasPermission('graduates.view') || auth()->user()->isAdmin())
                        <a href="{{ route('career-guidance.graduates.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="fas fa-users me-1"></i> قائمة الخريجين
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 6. وحدة الاستبيانات والتقييمات --}}
        @if(!empty($modules['evaluations']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-poll"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">الاستبيانات والتقييمات</h6>
                        <small class="text-muted">قياس الأثر واستطلاعات الرأي</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">استعراض نماذج الاستبيانات، تقييمات البرامج التدريبية، وتحليل استجابات المتدربين والشركات.</p>
                </div>
                <div class="module-footer">
                    @if(auth()->user()->hasPermission('surveys.manage') || auth()->user()->isAdmin())
                        <a href="{{ route('evaluation-followup.surveys') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="fas fa-poll me-1"></i> الاستبيانات
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- 7. وحدة إدارة المستخدمين (إذا منحت) --}}
        @if(!empty($modules['users']))
        <div class="col-md-6 col-lg-4">
            <div class="module-card">
                <div class="module-header">
                    <div class="module-icon-box bg-dark bg-opacity-10 text-dark">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">إدارة الحسابات والمستخدمين</h6>
                        <small class="text-muted">متابعة حسابات النظام</small>
                    </div>
                </div>
                <div class="module-body">
                    <p class="small text-muted mb-0">إدارة حسابات المستخدمين وصلاحياتهم وفق الضوابط والسياسات المحددة.</p>
                </div>
                <div class="module-footer">
                    <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-dark rounded-pill">
                        <i class="fas fa-users me-1"></i> المستخدمين
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
