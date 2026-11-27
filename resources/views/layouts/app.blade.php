<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-authenticated" content="true">
        <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>@yield('title', 'نظام إدارة الخريجين')</title>

    <!-- Stylesheets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="{{ asset('css/premium-forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bento-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dark-mode.css') }}" rel="stylesheet">

    <!-- كود فوري لمنع وميض الشاشة البيضاء عند تفعيل الوضع الليلي -->
    <script>
        (function() {
            var saved = localStorage.getItem('theme') || 'light';
            if (saved === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>

    <link href="{{ asset('css/app-layout.css') }}?v={{ @filemtime(public_path('css/app-layout.css')) ?: '1' }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="bento-theme {{ request()->routeIs('graduate.dashboard') ? 'is-graduate-dashboard' : '' }}">

    <!-- شاشة الترحيب البسيطة (Splash Screen) -->
    <div id="welcome-screen">
        <div class="welcome-content">
            <img src="{{ asset('images/office_logo_white.png') }}" alt="Logo" class="splash-logo">
            <div class="splash-pulse"></div>
        </div>
    </div>

    <!-- الشريط الجانبي -->
    <nav class="sidebar" id="sidebar">
        <div class="position-sticky sidebar-content">
            @php
                $sidebarRoleLabels = [
                    'admin' => 'لوحة تحكم المدير العام',
                    'staff' => 'لوحة الموظف (صلاحيات مخصصة)',
                    'training_coordinator' => 'لوحة التدريب والتأهيل',
                    'placement_coordinator' => 'لوحة التوظيف والمتابعة',
                    'graduate' => 'منصة الخريجين',
                    'evaluation_followup' => 'لوحة تحكم التقييم والمتابعة',
                    'career_guidance_officer' => 'مسؤول الإرشاد المهني',
                    'partnership_officer' => 'مسؤول الشراكات والتوظيف',
                    'company' => 'لوحة تحكم الشركة',
                    'media_officer' => 'لوحة تحكم الميديا',
                ];
                $sidebarRoleLabel = auth()->check() ? ($sidebarRoleLabels[auth()->user()->role] ?? '') : '';
            @endphp
            <div class="sidebar-header sidebar-header-compact">
                <!-- زر إغلاق الشريط الجانبي للموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-md-none border-0 sidebar-mobile-close" onclick="window.closeMobileSidebar ? window.closeMobileSidebar() : null" title="إغلاق القائمة">
                    <i class="fas fa-times"></i>
                </button>

                <div class="sidebar-brand">
                    <div class="sidebar-brand-logo">
                        <img src="{{ asset('images/office_logo_white.png') }}" alt="شعار مكتب الخريجين"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="sidebar-brand-fallback"><i class="fas fa-graduation-cap"></i></span>
                    </div>
                    <div class="sidebar-brand-text">
                        <div class="sidebar-brand-title">مكتب تدريب الخريجين</div>
                        <div class="sidebar-brand-subtitle">جامعة طرابلس</div>
                    </div>
                </div>

                <!-- زر تصغير الشريط الجانبي إلى شريط أيقونات (الديسكتوب فقط) -->
                <button class="toggle-sidebar d-none d-md-flex" id="toggleSidebar" title="تصغير القائمة الجانبية">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            @auth
            <!-- البحث السريع في القائمة -->
            <div class="sidebar-search">
                <i class="fas fa-search sidebar-search-icon"></i>
                <input type="search" id="sidebarSearch" class="sidebar-search-input" placeholder="ابحث في القائمة..." autocomplete="off" aria-label="البحث في القائمة الجانبية">
            </div>
            <div class="sidebar-search-empty" id="sidebarSearchEmpty" hidden>لا توجد نتائج مطابقة</div>
            @endauth

            <ul class="nav flex-column mt-3">
                @auth
                    <!-- لوحة التحكم لجميع المستخدمين -->
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('*dashboard*') ? 'active' : '' }}"
                            href="{{ auth()->user()->dashboard_route }}">
                            <i class="fas fa-tachometer-alt"></i>
                            لوحة التحكم
                        </a>
                    </li>

                    <!-- إدارة التدريب (لمنسق التدريب فقط - المدير لديه القائمة الكاملة بالأسفل) -->
                    @if(auth()->user()->role == 'training_coordinator')
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('*trainings*') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.trainings') }}">
                                <i class="fas fa-graduation-cap"></i>
                                إدارة التدريب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.calendar') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.calendar') }}">
                                <i class="fas fa-calendar-alt"></i>
                                تقويم التدريبات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.applications*') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.applications') }}">
                                <i class="fas fa-users"></i>
                                طلبات التدريب
                                @php
                                    $pendingCount = \App\Models\TrainingApplication::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.trainings.create') }}">
                                <i class="fas fa-plus-circle"></i>
                                إضافة برنامج تدريب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('*trainers*') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.trainers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                إدارة المدربين
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.reports') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير والإحصائيات
                            </a>
                        </li>

                        {{-- القوائم والصلاحيات الإضافية الممنوحة لمنسق التدريب --}}
                        @include('layouts.partials.dynamic-staff-menus')
                    @endif

                    <!-- إدارة المستخدمين والموظفين والصلاحيات -->
                    @if(auth()->user()->isAdmin() || auth()->user()->hasAnyPermission(['users.view', 'users.manage', 'users.create', 'users.edit']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                                href="{{ route('admin.users') }}">
                                <i class="fas fa-users-cog"></i>
                                إدارة الموظفين والصلاحيات
                            </a>
                        </li>
                    @endif


                    <!-- الأقسام الإدارية (للمسؤول فقط) -->
                    @if(auth()->user()->role == 'admin')

                        <!-- ادارة الشراكات والتوظيف -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('admin.companies*') || request()->routeIs('job-opportunities*') || request()->routeIs('partnership.*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('partnership-employment-menu')">
                                <i class="fas fa-handshake"></i>
                                ادارة الشراكات والتوظيف
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('admin.companies*') || request()->routeIs('job-opportunities*') || request()->routeIs('partnership.*') ? 'show' : '' }}"
                                id="partnership-employment-menu">
                                <a href="{{ route('partnership.dashboard') }}"
                                    class="submenu-item {{ request()->routeIs('partnership.dashboard') ? 'active' : '' }}">
                                    لوحة الشراكات والتوظيف
                                </a>
                                <a href="{{ route('admin.companies') }}"
                                    class="submenu-item {{ request()->routeIs('admin.companies') ? 'active' : '' }}">
                                    ادارة الشركات
                                </a>
                                <a href="{{ route('admin.companies.create') }}"
                                    class="submenu-item {{ request()->routeIs('admin.companies.create') ? 'active' : '' }}">
                                    إضافة شركة جديدة
                                </a>
                                <a href="{{ route('job-opportunities.index') }}"
                                    class="submenu-item {{ request()->routeIs('job-opportunities.index') ? 'active' : '' }}">
                                    إدارة فرص العمل
                                </a>
                                <a href="{{ route('job-opportunities.create') }}"
                                    class="submenu-item {{ request()->routeIs('job-opportunities.create') ? 'active' : '' }}">
                                    إضافة فرصة عمل جديدة
                                </a>
                                <a href="{{ route('partnership.documents') }}"
                                    class="submenu-item {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}">
                                    اتفاقيات ووثائق الشراكة
                                </a>
                                <a href="{{ route('job-fair.admin.index') }}"
                                    class="submenu-item {{ (request()->routeIs('job-fair.admin*') && !request()->routeIs('job-fair.admin.visitors*')) ? 'active' : '' }}">
                                    إدارة المعارض والفعاليات
                                </a>
                                <a href="{{ route('job-fair.admin.visitors.index', 1) }}"
                                    class="submenu-item {{ request()->routeIs('job-fair.admin.visitors*') ? 'active' : '' }}">
                                    إدارة زوار وتذاكر المعرض
                                </a>
                                <a href="{{ route('partnership.reports') }}"
                                    class="submenu-item {{ request()->routeIs('partnership.reports*') ? 'active' : '' }}">
                                    تقارير الشراكات والتوظيف
                                </a>
                            </div>
                        </li>

                        <!-- ادارة برامج التدريب والتأهيل للمدير -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('admin-trainings-menu')">
                                <i class="fas fa-graduation-cap"></i>
                                إدارة برامج التدريب والتأهيل
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'show' : '' }}"
                                id="admin-trainings-menu">
                                <a href="{{ route('training-coordinator.dashboard') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.dashboard') ? 'active' : '' }}">
                                    لوحة تحكم التدريب
                                </a>
                                <a href="{{ route('training-coordinator.trainings') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.trainings') && !request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}">
                                    جميع برامج التدريب
                                </a>
                                <a href="{{ route('training-coordinator.trainings.create') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}">
                                    إضافة برنامج تدريبي جديد
                                </a>
                                <a href="{{ route('training-coordinator.applications') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.applications*') ? 'active' : '' }}">
                                    إدارة طلبات التدريب
                                </a>
                                <a href="{{ route('training-coordinator.calendar') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.calendar*') ? 'active' : '' }}">
                                    التقويم التدريبي والجدول
                                </a>
                                <a href="{{ route('training-coordinator.reports') }}"
                                    class="submenu-item {{ request()->routeIs('training-coordinator.reports*') ? 'active' : '' }}">
                                    تقارير التدريب والشهادات
                                </a>
                            </div>
                        </li>

                        <!-- ادارة الارشاد المهني للمدير -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('admin.career-guidance.*') || request()->routeIs('career-guidance.pending-approvals') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('admin-career-guidance-menu')">
                                <i class="fas fa-compass"></i>
                                إدارة الإرشاد المهني
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('admin.career-guidance.*') || request()->routeIs('career-guidance.pending-approvals') ? 'show' : '' }}"
                                id="admin-career-guidance-menu">
                                <a href="{{ route('admin.career-guidance.dashboard') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.dashboard') ? 'active' : '' }}">
                                    لوحة الإرشاد المهني
                                </a>
                                <a href="{{ route('admin.career-guidance.graduates') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.graduates') && !request()->routeIs('admin.career-guidance.graduates.create') ? 'active' : '' }}">
                                    إدارة بيانات الخريجين
                                </a>
                                <a href="{{ route('career-guidance.pending-approvals') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.pending-approvals') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                                    <span>طلبات تسجيل الخريجين</span>
                                    @if(isset($pendingGraduatesCount) && $pendingGraduatesCount > 0)
                                        <span class="badge bg-danger rounded-pill px-2"
                                            style="font-size: 0.68rem;">{{ $pendingGraduatesCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.career-guidance.graduates.create') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.graduates.create') ? 'active' : '' }}">
                                    إضافة خريج جديد
                                </a>
                                <a href="{{ route('admin.career-guidance.nominations') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.nominations') && !request()->routeIs('admin.career-guidance.nominations.create') ? 'active' : '' }}">
                                    إدارة الترشيحات
                                </a>
                                <a href="{{ route('admin.career-guidance.nominations.create') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.nominations.create') ? 'active' : '' }}">
                                    ترشيح جديد
                                </a>
                                <a href="{{ route('admin.career-guidance.import.graduates.create') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.import.graduates*') ? 'active' : '' }}">
                                    استيراد الخريجين Excel
                                </a>
                                <a href="{{ route('admin.career-guidance.advanced-reports') }}"
                                    class="submenu-item {{ request()->routeIs('admin.career-guidance.advanced-reports*') ? 'active' : '' }}">
                                    التقارير المتقدمة
                                </a>
                            </div>
                        </li>

                        <!-- أقسام التقييم والمتابعة (للمدير فقط) -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup*') ? 'active' : '' }}" href="#"
                                onclick="toggleSubmenu('admin-evaluation-menu')">
                                <i class="fas fa-chart-line"></i>
                                إدارة التقييم والمتابعة
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('evaluation-followup*') ? 'show' : '' }}"
                                id="admin-evaluation-menu">
                                <a href="{{ route('evaluation-followup.training-calendar') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.training-calendar') ? 'active' : '' }}">
                                    تقويم التدريبات
                                </a>
                                <a href="{{ route('evaluation-followup.training-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.training-reports') ? 'active' : '' }}">
                                    تقارير التدريب
                                </a>
                                <a href="{{ route('evaluation-followup.partnership-employment-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.partnership-employment-reports') ? 'active' : '' }}">
                                    تقارير الشراكات والتوظيف
                                </a>
                                <a href="{{ route('evaluation-followup.career-guidance-advanced-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.career-guidance-advanced-reports') ? 'active' : '' }}">
                                    تقارير الإرشاد المهني
                                </a>
                                <a href="{{ route('evaluation-followup.surveys.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.index') ? 'active' : '' }}">
                                    إدارة الاستبيانات
                                </a>
                                <a href="{{ route('evaluation-followup.surveys.templates.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.templates*') ? 'active' : '' }}">
                                    مكتبة قوالب ونماذج الاستبيانات
                                </a>
                                <a href="{{ route('evaluation-followup.evaluations.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.evaluations*') ? 'active' : '' }}">
                                    إدارة التقييمات
                                </a>

                                <a href="{{ route('evaluation-followup.performance-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.performance-reports') ? 'active' : '' }}">
                                    تقارير الأداء
                                </a>

                            </div>
                        </li>

                        <!-- ادارة الإعلام والتغطيات للمدير -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('media.*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('admin-media-menu')">
                                <i class="fas fa-camera"></i>
                                إدارة الإعلام والتغطيات
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('media.*') ? 'show' : '' }}"
                                id="admin-media-menu">
                                <a href="{{ route('media.dashboard') }}"
                                    class="submenu-item {{ request()->routeIs('media.dashboard') ? 'active' : '' }}">
                                    لوحة تحكم الإعلام
                                </a>
                                <a href="{{ route('media.coverage-calendar') }}"
                                    class="submenu-item {{ request()->routeIs('media.coverage-calendar*') ? 'active' : '' }}">
                                    تقويم وجدول التغطيات
                                </a>
                                <a href="{{ route('media.news.index') }}"
                                    class="submenu-item {{ request()->routeIs('media.news*') ? 'active' : '' }}">
                                    إدارة الأخبار الصحفية
                                </a>
                                <a href="{{ route('media.announcements.index') }}"
                                    class="submenu-item {{ request()->routeIs('media.announcements*') ? 'active' : '' }}">
                                    إدارة الإعلانات والتعميمات
                                </a>
                                <a href="{{ route('media.reports.coverage') }}"
                                    class="submenu-item {{ request()->routeIs('media.reports.coverage*') ? 'active' : '' }}">
                                    تقارير التغطية الإعلامية
                                </a>
                                <a href="{{ route('media.platform-stats') }}"
                                    class="submenu-item {{ request()->routeIs('media.platform-stats*') ? 'active' : '' }}">
                                    إحصائيات المنصة الرئيسية
                                </a>
                                <a href="{{ route('job-fair.public') }}" target="_blank" class="submenu-item">
                                    الصفحة العامة للمعارض والفعاليات ↗
                                </a>
                            </div>
                        </li>
                    @endif

                    <!-- أقسام الموظف مخصص الصلاحيات (Staff RBAC) -->
                    @if(auth()->user()->role == 'staff')
                        @include('layouts.partials.dynamic-staff-menus')
                    @endif

                    <!-- قسم مسؤول الإعلام والتغطيات (لمسؤول الإعلام فقط) -->
                    @if(auth()->user()->role == 'media_officer')
                        <hr class="sidebar-divider my-2">
                        <div class="sidebar-heading">
                            وحدة الإعلام والتغطيات
                        </div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('media.coverage-calendar*') ? 'active' : '' }}"
                                href="{{ route('media.coverage-calendar') }}">
                                <i class="fas fa-calendar-check text-primary"></i>
                                تقويم وجدول التغطيات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('media.news*') ? 'active' : '' }}"
                                href="{{ route('media.news.index') }}">
                                <i class="fas fa-newspaper text-success"></i>
                                إدارة الأخبار الصحفية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('media.announcements*') ? 'active' : '' }}"
                                href="{{ route('media.announcements.index') }}">
                                <i class="fas fa-bullhorn text-warning"></i>
                                إدارة الإعلانات والتعميمات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('media.reports.coverage*') ? 'active' : '' }}"
                                href="{{ route('media.reports.coverage') }}">
                                <i class="fas fa-chart-line text-info"></i>
                                تقارير التغطية الإعلامية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('media.platform-stats*') ? 'active' : '' }}"
                                href="{{ route('media.platform-stats') }}">
                                <i class="fas fa-sliders-h text-primary"></i>
                                إحصائيات المنصة الرئيسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('job-fair.public') }}" target="_blank">
                                <i class="fas fa-globe text-primary"></i>
                                الصفحة العامة للمعارض والفعاليات ↗
                            </a>
                        </li>

                        {{-- القوائم والصلاحيات الإضافية الممنوحة لمسؤول الإعلام --}}
                        @include('layouts.partials.dynamic-staff-menus')
                    @endif

                    <!-- التدريبات المتاحة (للخريج فقط) -->
                    @if(auth()->user()->role == 'graduate')
                        <!-- فاصل -->
                        <!-- فاصل -->
                        <hr class="sidebar-divider my-3">
                        <div class="sidebar-heading">
                            الملف الشخصي
                        </div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.profile') ? 'active' : '' }}"
                                href="{{ route('graduate.profile') }}">
                                <i class="fas fa-user"></i>
                                بياناتي
                            </a>
                        </li>
                        <hr class="sidebar-divider my-3">
                        <div class="sidebar-heading">
                            التدريب والتوظيف
                        </div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.trainings*') ? 'active' : '' }}"
                                href="{{ route('graduate.trainings') }}">
                                <i class="fas fa-graduation-cap"></i>
                                برامج التدريب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.certificates*') ? 'active' : '' }}"
                                href="{{ route('graduate.certificates.index') }}">
                                <i class="fas fa-certificate text-warning"></i>
                                شهاداتي المعتمدة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.job-opportunities*') ? 'active' : '' }}"
                                href="{{ route('graduate.job-opportunities.index') }}">
                                <i class="fas fa-briefcase"></i>
                                فرص العمل
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.job-fairs*') || request()->routeIs('job-fair.*') ? 'active' : '' }}"
                                href="{{ route('graduate.job-fairs.index') }}">
                                <i class="fas fa-calendar-alt"></i>
                                الفعاليات و المعارض
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.my-applications') ? 'active' : '' }}"
                                href="{{ route('graduate.my-applications') }}">
                                <i class="fas fa-clipboard-list"></i>
                                ترشيحاتي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.surveys*') ? 'active' : '' }}"
                                href="{{ route('graduate.surveys.index') }}">
                                <i class="fas fa-poll"></i>
                                الاستبيانات
                            </a>
                        </li>


                    @endif

                    <!-- أقسام الإرشاد المهني (لمستخدم الإرشاد المهني فقط) -->
                    @if(auth()->user()->role == 'career_guidance_officer')

                        <!-- إدارة بيانات الخريجين -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('career-guidance.graduates*') ? 'active' : '' }}" href="#"
                                onclick="toggleSubmenu('cg-graduates-menu')">
                                <i class="fas fa-user-graduate"></i>
                                إدارة بيانات الخريجين
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('career-guidance.graduates*') ? 'show' : '' }}"
                                id="cg-graduates-menu">
                                <a href="{{ route('career-guidance.graduates') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.graduates') && !request()->routeIs('career-guidance.graduates.create') ? 'active' : '' }}">
                                    عرض الخريجين
                                </a>
                                <a href="{{ route('career-guidance.graduates.create') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.graduates.create') ? 'active' : '' }}">
                                    إضافة خريج جديد
                                </a>
                                <a href="{{ route('career-guidance.import.graduates.create') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.import.graduates*') ? 'active' : '' }}">
                                    استيراد الخريجين Excel
                                </a>
                                <a href="{{ route('career-guidance.pending-approvals') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.pending-approvals') ? 'active' : '' }}">
                                    طلبات التسجيل
                                </a>
                            </div>
                        </li>

                        <!-- إدارة الترشيحات -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('career-guidance.nominations*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('cg-nominations-menu')">
                                <i class="fas fa-user-check"></i>
                                إدارة الترشيحات
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('career-guidance.nominations*') ? 'show' : '' }}"
                                id="cg-nominations-menu">
                                <a href="{{ route('career-guidance.nominations') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.nominations') && !request()->routeIs('career-guidance.nominations.create') ? 'active' : '' }}">
                                    عرض الترشيحات
                                </a>
                                <a href="{{ route('career-guidance.nominations.create') }}"
                                    class="submenu-item {{ request()->routeIs('career-guidance.nominations.create') ? 'active' : '' }}">
                                    ترشيح جديد
                                </a>
                            </div>
                        </li>

                        <!-- الشركات -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('career-guidance.companies*') ? 'active' : '' }}"
                                href="{{ route('career-guidance.companies') }}">
                                <i class="fas fa-building"></i>
                                الشركات
                            </a>
                        </li>

                        {{-- القوائم والصلاحيات الإضافية الممنوحة لمسؤول الإرشاد المهني (بما فيها معرض التوظيف الكامل) --}}
                        @include('layouts.partials.dynamic-staff-menus')

                        <!-- التقارير -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('career-guidance.advanced-reports') ? 'active' : '' }}"
                                href="{{ route('career-guidance.advanced-reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير المتقدمة
                            </a>
                        </li>

                    @endif



                    <!-- أقسام مسؤول الشراكات والتوظيف -->
                    @if(auth()->user()->role == 'partnership_officer')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('partnership.companies*') ? 'active' : '' }}"
                                href="{{ route('partnership.companies') }}">
                                <i class="fas fa-building"></i>
                                إدارة الشركات
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}"
                                href="{{ route('partnership.documents') }}">
                                <i class="fas fa-file-contract"></i>
                                إدارة الوثائق
                            </a>
                        </li>



                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('job-opportunities.index') ? 'active' : '' }}"
                                href="{{ route('job-opportunities.index') }}">
                                <i class="fas fa-briefcase"></i>
                                فرص العمل
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('partnership.nominations*') ? 'active' : '' }}"
                                href="{{ route('partnership.nominations') }}">
                                <i class="fas fa-user-check"></i>
                                إدارة الترشيحات
                            </a>
                        </li>
                        {{-- القوائم والصلاحيات الإضافية الممنوحة لمسؤول الشراكات (بما فيها معرض التوظيف الكامل) --}}
                        @include('layouts.partials.dynamic-staff-menus')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('partnership.reports') ? 'active' : '' }}"
                                href="{{ route('partnership.reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير
                            </a>
                        </li>
                    @endif

                    <!-- أقسام التقييم والمتابعة (لمستخدم التقييم والمتابعة فقط) -->
                    @if(auth()->user()->role == 'evaluation_followup')


                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.training-calendar') ? 'active' : '' }}"
                                href="{{ route('evaluation-followup.training-calendar') }}">
                                <i class="fas fa-calendar-alt"></i>
                                تقويم التدريبات
                            </a>
                        </li>
                        <!-- إدارة الاستبيانات -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.surveys*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('surveys-menu')">
                                <i class="fas fa-poll"></i>
                                إدارة الاستبيانات
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('evaluation-followup.surveys*') ? 'show' : '' }}"
                                id="surveys-menu">
                                <a href="{{ route('evaluation-followup.surveys.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.index') ? 'active' : '' }}">
                                    عرض الاستبيانات
                                </a>
                                <a href="{{ route('evaluation-followup.surveys.templates.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.surveys.templates*') ? 'active' : '' }}">
                                    مكتبة قوالب ونماذج الاستبيانات
                                </a>
                            </div>
                        </li>

                        <!-- إدارة التقييمات -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.evaluations*') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('evaluations-menu')">
                                <i class="fas fa-star"></i>
                                إدارة التقييمات
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('evaluation-followup.evaluations*') ? 'show' : '' }}"
                                id="evaluations-menu">
                                <a href="{{ route('evaluation-followup.evaluations.index') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.evaluations.index') ? 'active' : '' }}">
                                    عرض التقييمات
                                </a>
                                <a href="{{ route('evaluation-followup.evaluations.create') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.evaluations.create') ? 'active' : '' }}">
                                    إضافة تقييم جديد
                                </a>
                            </div>
                        </li>

                        <!-- ردود الاستبيانات -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.survey-responses*') ? 'active' : '' }}"
                                href="{{ route('evaluation-followup.survey-responses.index') }}">
                                <i class="fas fa-list"></i>
                                ردود الاستبيانات
                            </a>
                        </li>

                        <!-- التقارير -->
                        <li class="nav-item menu-group">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.*reports') ? 'active' : '' }}"
                                href="#" onclick="toggleSubmenu('reports-menu')">
                                <i class="fas fa-chart-bar"></i>
                                التقارير والإحصائيات
                                <i class="fas fa-chevron-down menu-arrow"></i>
                            </a>
                            <div class="submenu {{ request()->routeIs('evaluation-followup.*reports') ? 'show' : '' }}"
                                id="reports-menu">
                                <a href="{{ route('evaluation-followup.evaluation-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.evaluation-reports') ? 'active' : '' }}">
                                    تقارير التقييمات
                                </a>
                                <a href="{{ route('evaluation-followup.survey-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.survey-reports') ? 'active' : '' }}">
                                    تقارير الاستبيانات
                                </a>
                                <a href="{{ route('evaluation-followup.performance-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.performance-reports') ? 'active' : '' }}">
                                    تقارير الأداء
                                </a>
                                <a href="{{ route('evaluation-followup.training-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.training-reports') ? 'active' : '' }}">
                                    تقارير التدريب
                                </a>
                                <a href="{{ route('evaluation-followup.partnership-employment-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.partnership-employment-reports') ? 'active' : '' }}">
                                    تقارير الشراكات والتوظيف
                                </a>
                                <a href="{{ route('evaluation-followup.career-guidance-advanced-reports') }}"
                                    class="submenu-item {{ request()->routeIs('evaluation-followup.career-guidance-advanced-reports') ? 'active' : '' }}">
                                    تقارير الإرشاد المهني
                                </a>
                            </div>
                        </li>

                        {{-- القوائم والصلاحيات الإضافية الممنوحة لمسؤول التقييم والمتابعة (بما فيها معرض التوظيف والبرامج الأخرى) --}}
                        @include('layouts.partials.dynamic-staff-menus')
                    @endif

                    <!-- لوحة تحكم الشركة -->
                    @if(auth()->user()->role == 'company')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('company.profile') ? 'active' : '' }}"
                                href="{{ route('company.profile') }}">
                                <i class="fas fa-building"></i>
                                ملف الشركة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('job-opportunities.*') ? 'active' : '' }}"
                                href="{{ route('job-opportunities.index') }}">
                                <i class="fas fa-briefcase"></i>
                                فرص العمل
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('company.job-fairs*') ? 'active' : '' }}"
                                href="{{ route('company.job-fairs.index') }}">
                                <i class="fas fa-calendar-alt"></i>
                                المعارض والفعاليات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('company.surveys*') ? 'active' : '' }}"
                                href="{{ route('company.surveys.index') }}">
                                <i class="fas fa-poll"></i>
                                الاستبيانات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}"
                                href="{{ route('messages.index') }}">
                                <i class="fas fa-envelope"></i>
                                الرسائل
                                @php
                                    $unreadMessages = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                                @endphp
                                @if($unreadMessages > 0)
                                    <span class="badge bg-danger ms-auto">{{ $unreadMessages }}</span>
                                @endif
                            </a>
                        </li>
                    @endif



                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            تسجيل الدخول
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('graduate.register') }}">
                            <i class="fas fa-user-plus"></i>
                            تسجيل خريج جديد
                        </a>
                    </li>
                @endauth

            </ul>
        </div>

        @auth
        <!-- بطاقة المستخدم الثابتة أسفل الشريط الجانبي -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ $sidebarRoleLabel }}</div>
                </div>
            </div>
            <div class="sidebar-footer-actions">
                <button type="button" class="sidebar-footer-btn" id="darkModeMenuToggle" onclick="toggleDarkMode(event)" title="الوضع الليلي">
                    <i class="fas fa-moon"></i>
                </button>
                <button type="button" class="sidebar-footer-btn sidebar-logout-btn" title="تسجيل الخروج"
                    onclick="document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        @endauth
    </nav>

    <!-- زر إظهار القائمة الجانبية العائم عند الإخفاء (فقط في الديسكتوب) -->
    <button type="button" class="floating-sidebar-toggle btn d-none d-md-flex" id="floatingSidebarToggle" title="إظهار القائمة الجانبية">
        <i class="fas fa-bars"></i>
    </button>

    <!-- المحتوى الرئيسي -->
    <div class="main-content" id="mainContent">

        @auth
        <!-- شريط الموبايل العلوي الموحد مع زر فتح القائمة الجانبية -->
        <header class="app-mobile-topbar d-flex d-md-none align-items-center justify-content-between px-3 py-2 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <!-- زر فتح القائمة الجانبية للموبايل -->
                <button type="button" class="btn mobile-topbar-btn mobile-menu-toggle shadow-sm" onclick="window.openMobileSidebar ? window.openMobileSidebar() : (window.toggleSidebarFunc ? window.toggleSidebarFunc() : null)" aria-label="فتح القائمة الرئيسية" title="القائمة الرئيسية">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('images/office_logo_white.png') }}" alt="الشعار" class="mobile-topbar-logo" onerror="this.src='{{ asset('images/logo.jpg') }}';">
                    <div class="mobile-topbar-brand">
                        <span class="mobile-topbar-title">مكتب تدريب الخريجين</span>
                        <span class="mobile-topbar-sub">جامعة طرابلس</span>
                    </div>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2 mobile-topbar-actions">
                <!-- زر الرسائل -->
                <button type="button" class="btn mobile-topbar-btn position-relative" onclick="openMessagesDrawer()" title="الرسائل">
                    <i class="fas fa-envelope"></i>
                    @php
                        $unreadMsgsMobile = \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    @if($unreadMsgsMobile > 0)
                        <span class="mobile-topbar-badge">{{ $unreadMsgsMobile > 9 ? '9+' : $unreadMsgsMobile }}</span>
                    @endif
                </button>

                <!-- زر الإشعارات -->
                <button type="button" class="btn mobile-topbar-btn position-relative" onclick="openNotificationsDrawer()" title="الإشعارات">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadNotifsMobile = $unreadNotificationsCount ?? \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    @if($unreadNotifsMobile > 0)
                        <span class="mobile-topbar-badge">{{ $unreadNotifsMobile > 9 ? '9+' : $unreadNotifsMobile }}</span>
                    @endif
                </button>

                <!-- زر الوضع الليلي -->
                <button type="button" class="btn mobile-topbar-btn dark-mode-trigger" onclick="toggleDarkMode(event)" title="الوضع الليلي">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </header>
        @endauth


        <!-- محتوى الصفحة -->
        <main class="container-fluid px-4 py-4 content-area-padding">
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

            @yield('content')
        </main>
    </div>



    <!-- ============================================================ -->
    <!-- درج الإشعارات المنزلق (Notifications Drawer) -->
    <!-- ============================================================ -->
    @auth
    <div id="notificationsDrawer" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;pointer-events:none;">
        <!-- Backdrop -->
        <div id="notifDrawerBackdrop" onclick="closeNotificationsDrawer()" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);opacity:0;transition:opacity 0.3s ease;pointer-events:none;"></div>
        <!-- Panel -->
        <div id="notifDrawerPanel" style="position:absolute;top:0;right:0;height:100%;width:380px;max-width:95vw;background:#fff;box-shadow:-4px 0 30px rgba(0,0,0,0.15);transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);display:flex;flex-direction:column;pointer-events:all;">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#0d3882,#1565c0);color:#fff;padding:1.2rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div>
                    <h5 style="margin:0;font-weight:700;font-size:1.1rem;"><i class="fas fa-bell me-2"></i>الإشعارات</h5>
                    <p style="margin:0;font-size:0.78rem;opacity:0.8;" id="notifDrawerSubtitle">جاري التحميل...</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button onclick="markAllNotificationsRead()" class="btn btn-sm drawer-header-btn rounded-pill px-3 py-1" style="font-size:0.78rem;" id="markAllNotifBtn">
                        <i class="fas fa-check-double me-1"></i>تحديد الكل مقروء
                    </button>
                    <button onclick="closeNotificationsDrawer()" class="btn btn-sm drawer-header-btn rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- Body -->
            <div id="notifDrawerBody" style="flex:1;overflow-y:auto;padding:0;">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <p class="small">جاري تحميل الإشعارات...</p>
                </div>
            </div>
            <!-- Footer -->
            <div style="padding:0.75rem 1rem;border-top:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;">
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm w-100 rounded-3">
                    <i class="fas fa-list me-1"></i>عرض جميع الإشعارات
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- درج الرسائل المنزلق (Messages Drawer) -->
    <!-- ============================================================ -->
    <div id="messagesDrawer" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;pointer-events:none;">
        <!-- Backdrop -->
        <div id="msgDrawerBackdrop" onclick="closeMessagesDrawer()" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);opacity:0;transition:opacity 0.3s ease;pointer-events:none;"></div>
        <!-- Panel -->
        <div id="msgDrawerPanel" style="position:absolute;top:0;right:0;height:100%;width:380px;max-width:95vw;background:#fff;box-shadow:-4px 0 30px rgba(0,0,0,0.15);transform:translateX(100%);transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);display:flex;flex-direction:column;pointer-events:all;">
            <!-- Header -->
            <div style="background:linear-gradient(135deg,#059669,#047857);color:#fff;padding:1.2rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div>
                    <h5 style="margin:0;font-weight:700;font-size:1.1rem;"><i class="fas fa-envelope me-2"></i>الرسائل</h5>
                    <p style="margin:0;font-size:0.78rem;opacity:0.8;" id="msgDrawerSubtitle">جاري التحميل...</p>
                </div>
                <button onclick="closeMessagesDrawer()" class="btn btn-sm drawer-header-btn rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Body -->
            <div id="msgDrawerBody" style="flex:1;overflow-y:auto;padding:0;">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-success mb-2" role="status"></div>
                    <p class="small">جاري تحميل الرسائل...</p>
                </div>
            </div>
            <!-- Footer -->
            <div style="padding:0.75rem 1rem;border-top:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;">
                <a href="{{ route('messages.index') }}" class="btn btn-outline-success btn-sm w-100 rounded-3">
                    <i class="fas fa-inbox me-1"></i>فتح صندوق الرسائل الكامل
                </a>
            </div>
        </div>
    </div>
    @endauth

    <link href="{{ asset('css/app-layout-2.css') }}?v={{ @filemtime(public_path('css/app-layout-2.css')) ?: '1' }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}?v={{ @filemtime(public_path('css/sidebar.css')) ?: '1' }}" rel="stylesheet">

    <!-- Bootstrap 5 Bundle JS (enables modals, dropdowns, tooltips globally) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/app-layout.js') }}?v={{ @filemtime(public_path('js/app-layout.js')) ?: '1' }}"></script>

    <!-- Simple Dark Mode Script -->
    <script src="{{ asset('js/simple-dark-mode.js') }}"></script>

    @auth
    @include('components.ai-assistant-widget')
    @endauth

    @yield('scripts')
    @stack('scripts')

    <!-- ============================================================ -->
    <!-- دوال الدرج المنزلق للإشعارات والرسائل -->
    <!-- ============================================================ -->
    <script src="{{ asset('js/app-layout-2.js') }}?v={{ @filemtime(public_path('js/app-layout-2.js')) ?: '1' }}"></script>
    <script src="{{ asset('js/sidebar.js') }}?v={{ @filemtime(public_path('js/sidebar.js')) ?: '1' }}"></script>

    @include('partials.loading-wave-overlay')
</body>

</html>