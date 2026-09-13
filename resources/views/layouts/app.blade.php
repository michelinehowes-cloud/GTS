<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-authenticated" content="true">
        <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>@yield('title', 'نظام إدارة الخريجين')</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="{{ asset('css/premium-forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bento-dashboard.css') }}" rel="stylesheet">

    <style>
        /* Mobile & Desktop Scroll Optimization */
        html {
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-text-size-adjust: 100%;
            height: auto;
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            touch-action: auto;
            width: 100%;
            min-height: 100vh;
            position: static;
        }

        .main-content {
            overflow-y: visible !important;
            min-height: 100vh;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Theme & Layout Customizations */
        body {
            background-color: #f1f5f9 !important;
        }

        .sidebar {
            background: linear-gradient(180deg, #0d47a1 0%, #1976d2 50%, #2563eb 100%) !important;
        }

        /* اعتماد شريط البانر الموحد العريض في جميع الصفحات وإخفاء الشريط العلوي المزدوج */
        .navbar-main {
            display: none !important;
        }

        .main-content {
            padding-top: 24px !important;
            padding-bottom: 40px !important;
            min-height: 100vh;
        }

        /* زر إظهار القائمة الجانبية العائم عند إخفاء الشريط الجانبي */
        .floating-sidebar-toggle {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 1045;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #ffffff;
            color: #1565c0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12) !important;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.15rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .floating-sidebar-toggle:hover {
            background: #1565c0;
            color: #ffffff;
            transform: scale(1.06);
            box-shadow: 0 6px 22px rgba(21, 101, 192, 0.35) !important;
        }
        body.sidebar-is-collapsed .floating-sidebar-toggle {
            display: flex !important;
        }

        /* إلغاء جميع الحركات والنبض التلقائي للأزرار والشارات واعتماد حركة هادئة عند تمرير الماوس فقط */
        .badge, .badge.rounded-pill, a.badge, .btn, .btn-modern, .btn-primary, .btn-primary-modern, .btn-warning, .btn-info, .btn-light, .btn-outline-primary, .btn-outline-secondary {
            animation: none !important;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, filter 0.2s ease !important;
        }

        /* إلغاء الظلال الحمراء والمزعجة للشارات نهائياً وتصميمها بستايل بسيط ونظيف */
        .badge, .badge.rounded-pill, span.badge {
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
        }

        .btn-outline-primary, .btn-outline-secondary, .btn-outline-success, .btn-outline-danger, .btn-outline-warning, .btn-outline-info {
            box-shadow: none !important;
        }

        .btn:hover, .btn-modern:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .btn-outline-primary:hover, .btn-outline-secondary:hover {
            box-shadow: none !important;
        }

        .btn:active, .btn-modern:active, a.badge:active, button.badge:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: none !important;
        }

        @media (max-width: 768px) {
            .main-content {
                padding-top: 10px !important;
                padding-bottom: 85px !important;
                margin-right: 0 !important;
                width: 100% !important;
                overflow-y: visible !important;
            }
            .sidebar {
                transform: translateX(100%) !important;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100vh !important;
                z-index: 1050 !important;
                width: 280px !important;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2) !important;
            }
            .sidebar.active-mobile {
                transform: translateX(0) !important;
            }
        }

        /* شاشة الترحيب البسيطة */
        #welcome-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #045db0 0%, #3b82f6 100%);
            z-index: 100000;
            display: none;
            pointer-events: none;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease-out;
        }

        #welcome-screen.active-splash {
            display: flex;
            pointer-events: auto;
        }

        .welcome-content {
            text-align: center;
            position: relative;
        }

        .splash-logo {
            width: 120px;
            height: auto;
            object-fit: contain;
            box-shadow: none;
            animation: fadeInScale 0.6s ease-out;
            position: relative;
            z-index: 2;
        }

        .splash-pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            animation: pulse 1.5s ease-out infinite;
            z-index: 1;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.8;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
        }

        .welcome-hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
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
            <div class="sidebar-header">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="logo-container">
                    <div class="university-logo d-flex align-items-center justify-content-center mx-auto">
                        <img src="{{ asset('images/office_logo_white.png') }}" alt="شعار مكتب الخريجين" class="logo-img"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-none align-items-center justify-content-center w-100 h-100">
                            <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #045db0;"></i>
                        </div>
                    </div>
                    <div class="logo-text">مكتب تدريب الخريجين</div>
                    <div class="logo-subtext">جامعة طرابلس</div>
                </div>

                <!-- عرض نوع لوحة التحكم -->
                <small class="text-light opacity-85 mt-2 d-block">
                    @auth
                        @if(auth()->user()->role == 'admin')
                            لوحة تحكم المدير العام
                        @elseif(auth()->user()->role == 'staff')
                            لوحة الموظف (صلاحيات مخصصة)
                        @elseif(auth()->user()->role == 'training_coordinator')
                            لوحة التدريب والتأهيل
                        @elseif(auth()->user()->role == 'placement_coordinator')
                            لوحة التوظيف والمتابعة
                        @elseif(auth()->user()->role == 'graduate')
                            منصة الخريجين
                        @elseif(auth()->user()->role == 'evaluation_followup')
                            لوحة تحكم التقييم والمتابعة
                        @elseif(auth()->user()->role == 'career_guidance_officer')
                            مسؤول الإرشاد المهني
                        @elseif(auth()->user()->role == 'partnership_officer')
                            مسؤول الشراكات والتوظيف
                        @elseif(auth()->user()->role == 'company')
                            لوحة تحكم الشركة
                        @elseif(auth()->user()->role == 'media_officer')
                            لوحة تحكم الميديا
                        @endif
                    @endauth
                </small>
            </div>

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
                                            دليل الشركات الشريكة
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
                                            class="submenu-item {{ request()->routeIs('job-fair.admin*') ? 'active' : '' }}">
                                            إدارة معرض التوظيف
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
                                                <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.68rem;">{{ $pendingGraduatesCount }}</span>
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
                                            class="submenu-item {{ request()->routeIs('evaluation-followup.surveys*') ? 'active' : '' }}">
                                            إدارة الاستبيانات
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

                                <!-- ادارة الإعلام والتغطيات والبث للمدير -->
                                <li class="nav-item menu-group">
                                    <a class="nav-link {{ request()->routeIs('media.*') || request()->routeIs('job-fair.live*') ? 'active' : '' }}"
                                        href="#" onclick="toggleSubmenu('admin-media-menu')">
                                        <i class="fas fa-camera"></i>
                                        إدارة الإعلام والتغطيات والبث
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </a>
                                    <div class="submenu {{ request()->routeIs('media.*') || request()->routeIs('job-fair.live*') ? 'show' : '' }}"
                                        id="admin-media-menu">
                                        <a href="{{ route('media.dashboard') }}"
                                            class="submenu-item {{ request()->routeIs('media.dashboard') ? 'active' : '' }}">
                                            لوحة تحكم الإعلام
                                        </a>
                                        <a href="{{ route('media.live-studio') }}"
                                            class="submenu-item {{ request()->routeIs('media.live-studio') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                                            <span>استوديو البث والكاميرات</span>
                                            @php
                                                $sbBroadcast = \App\Models\LiveBroadcastSetting::first();
                                            @endphp
                                            @if($sbBroadcast && $sbBroadcast->is_live_now)
                                                <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">LIVE</span>
                                            @endif
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
                                        <a href="{{ route('job-fair.live-stream') }}" target="_blank"
                                            class="submenu-item">
                                            شاشة البث المباشر (الجمهور) ↗
                                        </a>
                                        <a href="{{ route('job-fair.public') }}" target="_blank"
                                            class="submenu-item">
                                            صفحة المعرض (الزرقاء) ↗
                                        </a>
                                    </div>
                                </li>
                            @endif

                            <!-- أقسام الموظف مخصص الصلاحيات (Staff RBAC) -->
                            @if(auth()->user()->role == 'staff')
                                <!-- إدارة التدريب والتأهيل -->
                                @if(auth()->user()->hasAnyPermission(['trainings.view', 'trainings.create', 'trainings.applications', 'trainings.attendance', 'trainings.trainers']))
                                    <li class="nav-item menu-group">
                                        <a class="nav-link {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'active' : '' }}" href="#"
                                            onclick="toggleSubmenu('staff-trainings-menu')">
                                            <i class="fas fa-graduation-cap"></i>
                                            برامج التدريب والتأهيل
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </a>
                                        <div class="submenu {{ request()->routeIs('training-coordinator.*') || request()->routeIs('admin.trainings*') ? 'show' : '' }}" id="staff-trainings-menu">
                                            @if(auth()->user()->hasPermission('trainings.view'))
                                                <a href="{{ route('training-coordinator.trainings') }}" class="submenu-item {{ request()->routeIs('training-coordinator.trainings') ? 'active' : '' }}">
                                                    عرض البرامج
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('trainings.create'))
                                                <a href="{{ route('training-coordinator.trainings.create') }}" class="submenu-item {{ request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}">
                                                    إضافة برنامج تدريبي
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('trainings.applications'))
                                                <a href="{{ route('training-coordinator.applications') }}" class="submenu-item {{ request()->routeIs('training-coordinator.applications*') ? 'active' : '' }}">
                                                    طلبات التدريب
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('trainings.attendance'))
                                                <a href="{{ route('training-coordinator.calendar') }}" class="submenu-item {{ request()->routeIs('training-coordinator.calendar') ? 'active' : '' }}">
                                                    تقويم التدريبات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('trainings.trainers'))
                                                <a href="{{ route('training-coordinator.trainers.index') }}" class="submenu-item {{ request()->routeIs('training-coordinator.trainers*') ? 'active' : '' }}">
                                                    إدارة المدربين
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('reports.view'))
                                                <a href="{{ route('training-coordinator.reports') }}" class="submenu-item {{ request()->routeIs('training-coordinator.reports') ? 'active' : '' }}">
                                                    تقارير التدريب
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                <!-- الشراكات وفرص العمل ومعرض التوظيف -->
                                @if(auth()->user()->hasAnyPermission(['companies.view', 'companies.create', 'jobs.view', 'jobs.manage', 'job_fair.view', 'job_fair.manage', 'partnerships.documents']))
                                    <li class="nav-item menu-group">
                                        <a class="nav-link {{ request()->routeIs('partnership.*') || request()->routeIs('job-opportunities*') || request()->routeIs('job-fair.*') ? 'active' : '' }}" href="#"
                                            onclick="toggleSubmenu('staff-partnerships-menu')">
                                            <i class="fas fa-handshake"></i>
                                            الشراكات والتوظيف
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </a>
                                        <div class="submenu {{ request()->routeIs('partnership.*') || request()->routeIs('job-opportunities*') || request()->routeIs('job-fair.*') ? 'show' : '' }}" id="staff-partnerships-menu">
                                            @if(auth()->user()->hasPermission('companies.view'))
                                                <a href="{{ route('partnership.companies') }}" class="submenu-item {{ request()->routeIs('partnership.companies*') ? 'active' : '' }}">
                                                    دليل الشركات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('jobs.view'))
                                                <a href="{{ route('job-opportunities.index') }}" class="submenu-item {{ request()->routeIs('job-opportunities*') ? 'active' : '' }}">
                                                    فرص العمل
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('job_fair.view'))
                                                <a href="{{ route('job-fair.admin.index') }}" class="submenu-item {{ request()->routeIs('job-fair.*') ? 'active' : '' }}">
                                                    معرض التوظيف
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('partnerships.documents'))
                                                <a href="{{ route('partnership.documents') }}" class="submenu-item {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}">
                                                    إدارة الوثائق
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('reports.view'))
                                                <a href="{{ route('partnership.reports') }}" class="submenu-item {{ request()->routeIs('partnership.reports') ? 'active' : '' }}">
                                                    تقارير الشراكات
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                <!-- الإرشاد المهني والخريجون -->
                                @if(auth()->user()->hasAnyPermission(['graduates.view', 'graduates.create', 'graduates.approve', 'nominations.manage', 'graduates.import_export']))
                                    <li class="nav-item menu-group">
                                        <a class="nav-link {{ request()->routeIs('career-guidance.*') ? 'active' : '' }}" href="#"
                                            onclick="toggleSubmenu('staff-career-menu')">
                                            <i class="fas fa-compass"></i>
                                            الإرشاد المهني والخريجون
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </a>
                                        <div class="submenu {{ request()->routeIs('career-guidance.*') ? 'show' : '' }}" id="staff-career-menu">
                                            @if(auth()->user()->hasPermission('graduates.view'))
                                                <a href="{{ route('career-guidance.graduates') }}" class="submenu-item {{ request()->routeIs('career-guidance.graduates') ? 'active' : '' }}">
                                                    بيانات الخريجين
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('graduates.approve'))
                                                <a href="{{ route('career-guidance.pending-approvals') }}" class="submenu-item {{ request()->routeIs('career-guidance.pending-approvals') ? 'active' : '' }}">
                                                    طلبات التسجيل
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('graduates.create'))
                                                <a href="{{ route('career-guidance.graduates.create') }}" class="submenu-item {{ request()->routeIs('career-guidance.graduates.create') ? 'active' : '' }}">
                                                    إضافة خريج جديد
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('nominations.manage'))
                                                <a href="{{ route('career-guidance.nominations') }}" class="submenu-item {{ request()->routeIs('career-guidance.nominations*') ? 'active' : '' }}">
                                                    إدارة الترشيحات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('graduates.import_export'))
                                                <a href="{{ route('career-guidance.import.graduates.create') }}" class="submenu-item {{ request()->routeIs('career-guidance.import.graduates*') ? 'active' : '' }}">
                                                    استيراد Excel
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('reports.view'))
                                                <a href="{{ route('career-guidance.advanced-reports') }}" class="submenu-item {{ request()->routeIs('career-guidance.advanced-reports*') ? 'active' : '' }}">
                                                    التقارير المتقدمة
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                <!-- التقييم والمتابعة الاستبيانات -->
                                @if(auth()->user()->hasAnyPermission(['surveys.manage', 'evaluations.manage', 'reports.view']))
                                    <li class="nav-item menu-group">
                                        <a class="nav-link {{ request()->routeIs('evaluation-followup.*') ? 'active' : '' }}" href="#"
                                            onclick="toggleSubmenu('staff-eval-menu')">
                                            <i class="fas fa-poll-h"></i>
                                            التقييم والمتابعة
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </a>
                                        <div class="submenu {{ request()->routeIs('evaluation-followup.*') ? 'show' : '' }}" id="staff-eval-menu">
                                            @if(auth()->user()->hasPermission('surveys.manage'))
                                                <a href="{{ route('evaluation-followup.surveys.index') }}" class="submenu-item {{ request()->routeIs('evaluation-followup.surveys*') ? 'active' : '' }}">
                                                    إدارة الاستبيانات
                                                </a>
                                                <a href="{{ route('evaluation-followup.survey-responses.index') }}" class="submenu-item {{ request()->routeIs('evaluation-followup.survey-responses*') ? 'active' : '' }}">
                                                    ردود الاستبيانات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('evaluations.manage'))
                                                <a href="{{ route('evaluation-followup.evaluations.index') }}" class="submenu-item {{ request()->routeIs('evaluation-followup.evaluations*') ? 'active' : '' }}">
                                                    إدارة التقييمات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('reports.view'))
                                                <a href="{{ route('evaluation-followup.performance-reports') }}" class="submenu-item {{ request()->routeIs('evaluation-followup.performance-reports') ? 'active' : '' }}">
                                                    تقارير الأداء
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif

                                <!-- الإعلام والتغطيات للموظف مخصص الصلاحيات -->
                                @if(auth()->user()->hasAnyPermission(['media.manage', 'news.manage']))
                                    <li class="nav-item menu-group">
                                        <a class="nav-link {{ request()->routeIs('media.*') || request()->routeIs('job-fair.live*') ? 'active' : '' }}" href="#"
                                            onclick="toggleSubmenu('staff-media-menu')">
                                            <i class="fas fa-camera"></i>
                                            الإعلام والتغطيات
                                            <i class="fas fa-chevron-down menu-arrow"></i>
                                        </a>
                                        <div class="submenu {{ request()->routeIs('media.*') || request()->routeIs('job-fair.live*') ? 'show' : '' }}" id="staff-media-menu">
                                            @if(auth()->user()->hasPermission('media.manage'))
                                                <a href="{{ route('media.live-studio') }}" class="submenu-item {{ request()->routeIs('media.live-studio') ? 'active' : '' }}">
                                                    استوديو البث والكاميرات
                                                </a>
                                                <a href="{{ route('media.coverage-calendar') }}" class="submenu-item {{ request()->routeIs('media.coverage-calendar*') ? 'active' : '' }}">
                                                    تقويم وجدول التغطيات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('news.manage'))
                                                <a href="{{ route('media.news.index') }}" class="submenu-item {{ request()->routeIs('media.news*') ? 'active' : '' }}">
                                                    إدارة الأخبار الصحفية
                                                </a>
                                                <a href="{{ route('media.announcements.index') }}" class="submenu-item {{ request()->routeIs('media.announcements*') ? 'active' : '' }}">
                                                    إدارة الإعلانات والتعميمات
                                                </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('media.manage'))
                                                <a href="{{ route('media.reports.coverage') }}" class="submenu-item {{ request()->routeIs('media.reports.coverage*') ? 'active' : '' }}">
                                                    تقارير التغطية الإعلامية
                                                </a>
                                                <a href="{{ route('media.platform-stats') }}" class="submenu-item {{ request()->routeIs('media.platform-stats*') ? 'active' : '' }}">
                                                    إحصائيات المنصة الرئيسية
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endif
                            @endif

                            <!-- قسم مسؤول الميديا والإعلام والبث الذكي (لمسؤول الميديا فقط) -->
                            @if(auth()->user()->role == 'media_officer')
                                <hr class="sidebar-divider my-2">
                                <div class="sidebar-heading">
                                    وحدة الإعلام والبث
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('media.live-studio') ? 'active' : '' }}"
                                        href="{{ route('media.live-studio') }}">
                                        <i class="fas fa-satellite-dish text-danger"></i>
                                        استوديو البث والكاميرات
                                        @php
                                            $sbBroadcast = \App\Models\LiveBroadcastSetting::first();
                                        @endphp
                                        @if($sbBroadcast && $sbBroadcast->is_live_now)
                                            <span class="badge bg-danger rounded-pill ms-auto small" style="font-size: 0.65rem;">LIVE</span>
                                        @endif
                                    </a>
                                </li>
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
                                    <a class="nav-link" href="{{ route('job-fair.live-stream') }}" target="_blank">
                                        <i class="fas fa-tv text-secondary"></i>
                                        شاشة البث المباشر (الجمهور) ↗
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('job-fair.public') }}" target="_blank">
                                        <i class="fas fa-globe text-primary"></i>
                                        صفحة المعرض (الزرقاء) ↗
                                    </a>
                                </li>
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
                                    <a class="nav-link {{ request()->routeIs('graduate.job-opportunities*') ? 'active' : '' }}"
                                        href="{{ route('graduate.job-opportunities.index') }}">
                                        <i class="fas fa-briefcase"></i>
                                        فرص العمل
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
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('job-fair.*') ? 'active' : '' }}"
                                        href="{{ route('job-fair.admin.index') }}">
                                        <i class="fas fa-store"></i>
                                        معرض التوظيف
                                        <span class="badge ms-1 py-1 px-2 rounded-pill" style="background:linear-gradient(135deg,#eeca3e,#F97316);font-size:0.62rem">السنوي</span>
                                    </a>
                                </li>
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
                                        <i class="fas fa-store"></i>
                                        معارض التوظيف
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



                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0)" onclick="toggleDarkMode(event)"
                                    id="darkModeMenuToggle" style="cursor: pointer;">
                                    <i class="fas fa-moon"></i>
                                    الوضع الليلي
                                </a>
                            </li>
                            <li class="nav-item mt-2 pt-2 border-top border-light">
                                <a class="nav-link text-warning fw-bold" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i>
                                    تسجيل الخروج
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
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
    </nav>

    <!-- زر إظهار القائمة الجانبية العائم عند الإخفاء -->
    <button type="button" class="floating-sidebar-toggle btn" id="floatingSidebarToggle" title="إظهار القائمة الجانبية">
        <i class="fas fa-bars"></i>
    </button>

    <!-- المحتوى الرئيسي -->
    <div class="main-content" id="mainContent">

        <!-- الشريط العلوي -->
        <nav class="navbar navbar-expand-lg navbar-main" id="navbarMain">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center">
                    <button class="toggle-sidebar-main btn d-flex align-items-center justify-content-center p-0 me-2" id="toggleSidebarMain" style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #ffffff;">
                        <i class="fas fa-bars fa-lg text-white"></i>
                    </button>
                    <h4 class="navbar-brand mb-0 ms-3 d-flex align-items-center">
                        @if(request()->is('admin/job-fair*') || request()->is('job-fair*') || request()->routeIs('job-fair.*'))
                            <img src="{{ asset('images/job_fair_logo.png') }}" alt="شعار المعرض"
                                style="height: 38px; margin-left: 10px; display: inline-block; object-fit: contain;"
                                onerror="this.src='{{ asset('images/job_fair_logo_white.png') }}'">
                        @else
                            <img src="{{ asset('images/gto_logo.jpg') }}" alt="مكتب الخريجين"
                                style="height: 38px; border-radius: 8px; margin-left: 10px; display: inline-block; object-fit: contain; border: 1.5px solid rgba(255,255,255,0.4);"
                                onerror="this.src='{{ asset('images/office_logo_white.png') }}'">
                        @endif
                        <span class="text-white fw-bold fs-5">@yield('page-title', View::getSection('title') ?? 'لوحة التحكم')</span>
                    </h4>
                </div>

                <div class="d-flex align-items-center">
                    <div class="me-3 text-end">
                        <div class="fw-bold text-white fs-6">{{ auth()->user()->name ?? 'مستخدم' }}</div>
                        <small class="text-white opacity-75">
                            @auth
                                {{ auth()->user()->role_name }}
                            @endauth
                        </small>
                    </div>

                    <!-- Notifications Dropdown -->
                    @auth
                        <div class="dropdown me-3">
                            <a class="nav-link dropdown-toggle position-relative text-white" href="#" id="notificationsDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg text-white"></i>
                                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        id="notification-badge">
                                        {{ $unreadNotificationsCount }}
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                                aria-labelledby="notificationsDropdown"
                                style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li>
                                    <h6 class="dropdown-header bg-light text-dark fw-bold py-2 px-3 border-bottom">
                                        مركز الإشعارات</h6>
                                </li>
                                <div id="notifications-list">
                                    <!-- Notifications will be loaded here via AJAX -->
                                    <li class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        </div> جاري التحميل...
                                    </li>
                                </div>
                                <li>
                                    <hr class="dropdown-divider my-0">
                                </li>
                                <li class="d-flex justify-content-between px-2 py-1">
                                    <a class="dropdown-item text-center small text-primary fw-bold py-2 flex-grow-1"
                                        href="{{ route('notifications.index') }}">عرض كل الإشعارات</a>
                                    <button id="clearAllNotifications" class="btn btn-sm btn-outline-danger py-1 px-2"
                                        style="font-size: 0.75rem;">
                                        <i class="fas fa-trash-alt"></i> مسح الكل
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button id="darkModeToggle"
                            style="background: none; border: none; padding: 0; cursor: pointer; margin-right: 1rem; display: inline-flex; align-items: center;"
                            title="تبديل الوضع الليلي" aria-label="تبديل الوضع الليلي">
                            <i class="fas fa-moon fa-lg text-white"></i>
                        </button>
                    @endauth
                    @auth
                        @php
                            $roleIcons = [
                                'admin' => 'fas fa-user-shield',
                                'staff' => 'fas fa-user-cog',
                                'training_coordinator' => 'fas fa-chalkboard-teacher',
                                'placement_coordinator' => 'fas fa-briefcase',
                                'company' => 'fas fa-building',
                                'graduate' => 'fas fa-user-graduate',
                                'partnership_officer' => 'fas fa-handshake',
                                'career_guidance_officer' => 'fas fa-compass',
                                'evaluation_followup' => 'fas fa-chart-line',
                                'media_officer' => 'fas fa-camera',
                            ];
                            $userIcon = $roleIcons[auth()->user()->role] ?? 'fas fa-user';
                        @endphp
                        <div class="user-avatar">
                            <i class="{{ $userIcon }}"></i>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

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



    <!-- Bootstrap CDN removed to avoid conflict with Vite build -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing UI scripts...');

            // === شاشة الترحيب Splash Screen Logic ===
            // تظهر فقط في التطبيق (Native App) وليس في المتصفح
            // أو عند حدوث أخطاء في السيرفر

            const welcomeScreen = document.getElementById('welcome-screen');

            // التحقق من أن التطبيق يعمل في بيئة Capacitor (Native App)
            const isNativeApp = window.Capacitor && window.Capacitor.isNativePlatform && window.Capacitor.isNativePlatform();

            // دالة لإخفاء شاشة الترحيب
            window.hideSplashScreen = function () {
                if (welcomeScreen) {
                    welcomeScreen.classList.add('welcome-hidden');
                    setTimeout(() => {
                        welcomeScreen.style.display = 'none';
                    }, 800);
                }
            };

            // دالة لإظهار شاشة الترحيب (للأخطاء)
            window.showSplashScreen = function (message) {
                if (welcomeScreen && isNativeApp) {
                    welcomeScreen.style.display = 'flex';
                    welcomeScreen.classList.remove('welcome-hidden');
                    if (message) {
                        console.log('Splash Screen Message:', message);
                    }
                }
            };

            // إذا كان تطبيق Native، أظهر الشاشة ثم أخفها بعد ثانية واحدة
            if (isNativeApp) {
                if (welcomeScreen) {
                    welcomeScreen.style.display = 'flex';
                    setTimeout(() => {
                        window.hideSplashScreen();
                    }, 1000); // ثانية واحدة فقط
                }
            } else {
                // في المتصفح، أخفها فوراً
                if (welcomeScreen) {
                    welcomeScreen.style.display = 'none';
                }
            }


            // معالج الأخطاء - فقط للتسجيل في Console
            window.addEventListener('error', function (e) {
                if (isNativeApp) {
                    console.error('JavaScript Error:', e.error);
                }
            });

            // معالج الأخطاء غير المعالجة
            window.addEventListener('unhandledrejection', function (event) {
                if (isNativeApp) {
                    console.error('Unhandled Promise Rejection:', event.reason);
                }
            });

            // معالج أخطاء الشبكة
            window.addEventListener('offline', function () {
                if (isNativeApp) {
                    console.warn('No internet connection');
                }
            });

            // معالج أخطاء HTTP (للأخطاء من السيرفر)
            if (isNativeApp && window.fetch) {
                const originalFetch = window.fetch;
                window.fetch = function (...args) {
                    return originalFetch.apply(this, args)
                        .then(response => {
                            // التحقق من أخطاء HTTP
                            if (!response.ok) {
                                if (response.status >= 500) {
                                    showErrorSplash('خطأ في السيرفر');
                                } else if (response.status === 404) {
                                    showErrorSplash('الصفحة غير موجودة');
                                } else if (response.status === 403) {
                                    showErrorSplash('غير مصرح لك بالوصول');
                                } else if (response.status === 401) {
                                    showErrorSplash('يرجى تسجيل الدخول');
                                }
                            }
                            return response;
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            showErrorSplash('خطأ في الاتصال بالسيرفر');
                            throw error;
                        });
                };
            }

            // تعريف العناصر
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const navbarMain = document.getElementById('navbarMain');
            const toggleSidebar = document.getElementById('toggleSidebar');
            const toggleSidebarMain = document.getElementById('toggleSidebarMain');

            // إنشاء طبقة الـ Overlay للموبايل
            const sidebarOverlay = document.createElement('div');
            sidebarOverlay.className = 'sidebar-overlay';
            sidebarOverlay.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0, 0, 0, 0.5); z-index: 1035; display: none; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
            `;
            document.body.appendChild(sidebarOverlay);

            // دالة التبديل (Toggle Function)
            window.toggleSidebarFunc = function () {
                if (!sidebar) return;
                const isMobile = window.innerWidth < 768;

                if (isMobile) {
                    // منطق الموبايل
                    const isActive = sidebar.classList.contains('active-mobile');
                    if (isActive) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    // منطق الديسكتوب
                    sidebar.classList.toggle('collapsed');
                    mainContent?.classList.toggle('expanded');
                    navbarMain?.classList.toggle('expanded');

                    // تحديث الأيقونة
                    updateToggleIcon();

                    // حفظ الحالة
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            };

            function openMobileSidebar() {
                sidebar.classList.add('active-mobile');
                sidebar.classList.remove('collapsed');
                sidebarOverlay.style.display = 'block';
                sidebarOverlay.style.pointerEvents = 'auto';
                setTimeout(() => sidebarOverlay.style.opacity = '1', 10);
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('active-mobile');
                sidebar.classList.add('collapsed');
                sidebarOverlay.style.opacity = '0';
                sidebarOverlay.style.pointerEvents = 'none';
                setTimeout(() => {
                    sidebarOverlay.style.display = 'none';
                }, 300);
            }

            function updateToggleIcon() {
                const isCollapsed = sidebar.classList.contains('collapsed');
                if (toggleSidebar) {
                    const icon = toggleSidebar.querySelector('i');
                    if (icon) icon.className = isCollapsed ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
                }
                const floatingBtn = document.getElementById('floatingSidebarToggle');
                if (floatingBtn) {
                    floatingBtn.style.display = isCollapsed ? 'flex' : 'none';
                }
                if (isCollapsed) {
                    document.body.classList.add('sidebar-is-collapsed');
                } else {
                    document.body.classList.remove('sidebar-is-collapsed');
                }
            }

            // ربط الأزرار بالدالة
            if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarFunc);
            if (toggleSidebarMain) toggleSidebarMain.addEventListener('click', toggleSidebarFunc);
            const floatingToggleBtn = document.getElementById('floatingSidebarToggle');
            if (floatingToggleBtn) floatingToggleBtn.addEventListener('click', toggleSidebarFunc);

            // النقر على Overlay يغلق القائمة
            sidebarOverlay.addEventListener('click', closeMobileSidebar);

            // استعادة الحالة عند التحميل
            function restoreSidebarState() {
                const isMobile = window.innerWidth < 768;
                const savedState = localStorage.getItem('sidebarCollapsed');

                if (isMobile) {
                    sidebar.classList.add('collapsed');
                    sidebar.classList.remove('active-mobile');
                    mainContent?.classList.add('expanded');
                    navbarMain?.classList.add('expanded');
                } else {
                    // الديسكتوب
                    if (savedState === 'true') {
                        sidebar.classList.add('collapsed');
                        mainContent?.classList.add('expanded');
                        navbarMain?.classList.add('expanded');
                    } else {
                        sidebar.classList.remove('collapsed');
                        mainContent?.classList.remove('expanded');
                        navbarMain?.classList.remove('expanded');
                    }
                    updateToggleIcon();
                }
            }

            // التعامل مع القوائم الفرعية
            window.toggleSubmenu = function (menuId) {
                const submenu = document.getElementById(menuId);
                if (!submenu) return;
                const menuGroup = submenu.closest('.menu-group');
                submenu.classList.toggle('show');
                if (menuGroup) menuGroup.classList.toggle('active');
            };

            // تفعيل الروابط النشطة
            const currentPath = window.location.pathname;
            document.querySelectorAll('.submenu-item').forEach(item => {
                if (item.href && currentPath.includes(new URL(item.href).pathname)) {
                    item.classList.add('active');
                    const submenu = item.closest('.submenu');
                    const menuGroup = item.closest('.menu-group');
                    if (submenu) submenu.classList.add('show');
                    if (menuGroup) menuGroup.classList.add('active');
                }
            });

            // إغلاق القائمة عند النقر على روابط (موبايل فقط)
            const allLinks = document.querySelectorAll('#sidebar a');
            allLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 768) {
                        const isToggle = this.classList.contains('dropdown-toggle') || this.getAttribute('onclick');
                        if (!isToggle) closeMobileSidebar();
                    }
                });
            });

            // إضافة CSS الخاص بالموبايل
            const mobileStyle = document.createElement('style');
            mobileStyle.innerHTML = `
                @media (max-width: 768px) {
                    html, body {
                        overflow-x: hidden !important;
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch !important;
                        position: static !important;
                        height: auto !important;
                    }
                    .sidebar {
                        transform: translateX(100%);
                        transition: transform 0.3s ease-in-out;
                        position: fixed !important; top: 0; right: 0; height: 100vh; z-index: 1040; width: 280px !important;
                    }
                    .sidebar.active-mobile { transform: translateX(0) !important; }
                    .sidebar.collapsed { transform: translateX(100%) !important; }
                    .main-content, .navbar-main { margin-right: 0 !important; width: 100% !important; overflow-y: visible !important; }
                }
            `;
            document.head.appendChild(mobileStyle);

            restoreSidebarState();

            // التعامل مع تغيير حجم الشاشة
            window.addEventListener('resize', () => {
                const isMobile = window.innerWidth < 768;
                if (!isMobile) {
                    // إزالة آثار الموبايل عند التكبير
                    sidebarOverlay.style.display = 'none';
                    sidebar.classList.remove('active-mobile');
                    // استعادة حالة الديسكتوب
                    restoreSidebarState();
                }
            });
        });
    </script> <!-- Closing for the main script block -->

    <!-- Simple Dark Mode Script -->
    <!-- Simple Dark Mode Script -->
    <script src="{{ asset('js/simple-dark-mode.js') }}"></script>
    <script type="text/plain">
        (function () {
            // Get theme from localStorage or default to light
            let currentTheme = localStorage.getItem('theme') || 'light';

            // ==========================================
            // DARK MODE SCRIPT (Global Scope)
            // ==========================================
 // 1. Get Theme
            var currentTheme = localStorage.getItem('theme') || 'light';

            // 2. Apply Theme Function
            function applyTheme(theme) {
                const root = document.documentElement;

           // Set Attribute
                if (theme === 'dark') {
                    root.setAttribute('data-theme', 'dark');
                } else {
                    root.removeAttribute('data-theme');
                }

                // Update Icons (Any element with these IDs or inside them)
                const buttons = [
                    document.getElementById('darkModeToggle'),
                    document.getElementById('darkModeMenuToggle')
                ];

                buttons.forEach(btn => {
                    if (btn) {
                        const icon = btn.querySelector('i');
                        if (icon) {
                            // Remove old classes first to be safe
                            icon.classList.remove('fa-moon', 'fa-sun');

                            if (theme === 'dark') {
                                icon.classList.add('fa-sun');
                            } else {
                                icon.classList.add('fa-moon');
                            }
                        }
                    }
                });

                // Save
                localStorage.setItem('theme', theme);
                currentTheme = theme;
            }

            // 3. Toggle Function (Exposed Globally)
            window.toggleThemeGlobal = function (e) {
                if (e) {
                    e.preventDefault();
                    if (e.stopPropagation) e.stopPropagation();
                }

                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                applyTheme(newTheme);
                console.log('Theme toggled to:', newTheme);
            };

            // 4. Initialize
            applyTheme(currentTheme);

            // 5. Re-apply on DOMContentLoaded (to catch buttons not yet rendered)
            document.addEventListener('DOMContentLoaded', function () {
                applyTheme(currentTheme);
            });

            // 6. Keyboard Shortcut
            document.addEventListener('keydown', function (e) {
                if ((e.altKey || e.metaKey) && e.key === 'd') {
                    e.preventDefault();
                    window.toggleThemeGlobal();
                }
            });

    </script>

    @auth
        @include('components.ai-assistant-widget')
    @endauth

    @yield('scripts')
    @stack('scripts')

</body>

</html>
