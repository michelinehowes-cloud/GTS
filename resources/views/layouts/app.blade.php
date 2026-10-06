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
            background-color: #f1f5f9;
        }

        html[data-theme='dark'] body,
        body.dark-mode {
            background-color: #090d16 !important;
            color: #cbd5e1 !important;
        }

        .sidebar {
            background: linear-gradient(180deg, #2374c9 0%, #287ed4 50%, #2b82d8 100%) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }

        /* القوائم الفرعية المنسدلة للشريط الجانبي (قابلة للطي والانسدال السلس - نظام أكورديون) */
        .sidebar .submenu {
            display: none !important;
            padding-right: 16px !important;
            padding-left: 8px !important;
            margin: 4px 8px !important;
            background: rgba(0, 0, 0, 0.18) !important;
            border-radius: 8px !important;
            border-right: 2.5px solid rgba(238, 202, 62, 0.45) !important;
            transition: all 0.25s ease-in-out !important;
        }

        .sidebar .submenu.show {
            display: block !important;
            animation: fadeInSubmenu 0.25s ease-out forwards;
        }

        @keyframes fadeInSubmenu {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar .menu-group .menu-arrow {
            transition: transform 0.25s ease !important;
            margin-right: auto !important;
            font-size: 0.78rem !important;
            opacity: 0.8;
        }

        .sidebar .menu-group.active .menu-arrow,
        .sidebar .menu-group:has(.submenu.show) .menu-arrow {
            transform: rotate(180deg) !important;
            opacity: 1 !important;
            color: #eeca3e !important;
        }

        .sidebar .submenu-item {
            padding: 8px 12px !important;
            color: rgba(255, 255, 255, 0.85) !important;
            text-decoration: none !important;
            display: block !important;
            font-size: 0.86rem !important;
            border-radius: 6px !important;
            margin-bottom: 2px !important;
            transition: all 0.2s ease !important;
        }

        .sidebar .submenu-item:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12) !important;
            padding-right: 16px !important;
        }

        .sidebar .submenu-item.active {
            color: #ffffff !important;
            background: rgba(238, 202, 62, 0.22) !important;
            border-right: 3px solid #eeca3e !important;
            font-weight: 700 !important;
        }


        .main-content {
            padding-top: 0 !important;
            padding-bottom: 40px !important;
            min-height: 100vh;
        }

        /* تنسيق جرس الإشعارات والقائمة المنسدلة */
        #notificationsDropdown {
            cursor: pointer;
            padding: 8px;
            border-radius: 10px;
            transition: background-color 0.2s ease;
        }

        #notificationsDropdown:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        #notification-badge {
            animation: pulseNotification 2s infinite;
            font-size: 0.68rem;
            padding: 0.35em 0.55em;
        }

        @keyframes pulseNotification {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.15);
            }
        }

        .notification-item {
            transition: background-color 0.2s ease;
            border-right: 3px solid transparent;
        }

        .notification-item.unread {
            background-color: rgba(59, 130, 246, 0.05);
            border-right-color: #3b82f6;
        }

        .notification-item:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }

        /* زر إظهار القائمة الجانبية العائم عند إخفاء الشريط الجانبي (ديسكتوب فقط) */
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

        @media (min-width: 768.01px) {
            body.sidebar-is-collapsed .floating-sidebar-toggle {
                display: flex !important;
            }
        }

        /* أزرار رأس درجي الإشعارات والرسائل الزجاجية الأنيقة */
        .drawer-header-btn {
            background: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.35) !important;
            backdrop-filter: blur(6px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.2s ease !important;
        }

        .drawer-header-btn:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        }

        .drawer-header-btn:active {
            transform: scale(0.96) !important;
        }

        /* إلغاء جميع الحركات والنبض التلقائي للأزرار والشارات واعتماد حركة هادئة عند تمرير الماوس فقط */
        .badge,
        .badge.rounded-pill,
        a.badge,
        .btn,
        .btn-modern,
        .btn-primary,
        .btn-primary-modern,
        .btn-warning,
        .btn-info,
        .btn-light,
        .btn-outline-primary,
        .btn-outline-secondary {
            animation: none !important;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, filter 0.2s ease !important;
        }

        /* إلغاء الظلال الحمراء والمزعجة للشارات نهائياً وتصميمها بستايل بسيط ونظيف */
        .badge,
        .badge.rounded-pill,
        span.badge {
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
        }

        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-success,
        .btn-outline-danger,
        .btn-outline-warning,
        .btn-outline-info {
            box-shadow: none !important;
        }

        .btn:hover,
        .btn-modern:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-secondary:hover {
            box-shadow: none !important;
        }

        .btn:active,
        .btn-modern:active,
        a.badge:active,
        button.badge:active {
            transform: translateY(0) scale(0.98) !important;
            box-shadow: none !important;
        }

        /* منع أي ارتعاش أو اهتزاز في صفوف الجداول أو البطاقات الحاوية للجداول */
        .table,
        .table tbody tr,
        .table tbody tr:hover,
        .table-responsive,
        .table-responsive table {
            transform: none !important;
        }

        .card:has(.table),
        .card:has(table),
        .card-modern:has(.table),
        .card-modern:has(table) {
            transition: box-shadow 0.2s ease !important;
        }

        .card:has(.table):hover,
        .card:has(table):hover,
        .card-modern:has(.table):hover,
        .card-modern:has(table):hover {
            transform: none !important;
        }

        @media (max-width: 768px) {
            #toggleSidebar {
                display: none !important;
            }

            .floating-sidebar-toggle {
                display: none !important;
            }

            .sidebar-mobile-close {
                position: absolute;
                top: 14px;
                left: 14px;
                width: 38px;
                height: 38px;
                background: rgba(255, 255, 255, 0.25) !important;
                color: #fff !important;
                border: 1px solid rgba(255, 255, 255, 0.4) !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                z-index: 1070;
                cursor: pointer;
                backdrop-filter: blur(6px);
                transition: all 0.2s ease;
                font-size: 1.1rem;
            }

            .sidebar-mobile-close:hover {
                background: rgba(255, 255, 255, 0.4) !important;
                color: #fff !important;
                transform: scale(1.08);
            }

            .main-content {
                padding-top: 0 !important;
                padding-bottom: 85px !important;
                margin-right: 0 !important;
                width: 100% !important;
                overflow-y: visible !important;
            }

            .sidebar {
                transform: translateX(100%) !important;
                transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                height: 100vh !important;
                z-index: 1060 !important;
                width: 290px !important;
                max-width: 85vw !important;
                box-shadow: -6px 0 35px rgba(0, 0, 0, 0.35) !important;
                display: block !important;
            }

            .sidebar.active-mobile {
                transform: translateX(0) !important;
            }
        }

        /* ── شريط الموبايل العلوي الموحد (Universal Mobile App Header) ── */
        .app-mobile-topbar {
            position: sticky;
            top: 0;
            z-index: 1040;
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%) !important;
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            min-height: 54px;
            box-shadow: 0 4px 20px rgba(13, 56, 130, 0.15);
            margin: 0;
        }

        html[data-theme='dark'] .app-mobile-topbar,
        body.dark-mode .app-mobile-topbar {
            background: #0f172a !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .mobile-topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px !important;
        }

        .mobile-topbar-btn {
            width: 38px;
            height: 38px;
            min-width: 38px;
            min-height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        /* منع تشوه الأزرار الدائرية على الهواتف والشاشات الصغيرة */
        .btn.rounded-circle,
        .action-btn-circle {
            aspect-ratio: 1 / 1 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            border-radius: 50% !important;
        }

        .mobile-topbar-btn:active {
            transform: scale(0.92);
            background: rgba(255, 255, 255, 0.3) !important;
        }

        .mobile-topbar-btn.mobile-menu-toggle {
            background: #eeca3e !important;
            color: #0d3882 !important;
            border-color: #eeca3e !important;
            font-size: 1.15rem;
            box-shadow: 0 2px 10px rgba(238, 202, 62, 0.4);
        }

        .mobile-topbar-btn.mobile-menu-toggle:hover {
            background: #f5d76e !important;
            color: #0d3882 !important;
        }

        .mobile-topbar-logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
            border-radius: 50%;
            border: 1.5px solid rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-topbar-brand {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            text-align: right;
        }

        .mobile-topbar-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
        }

        .mobile-topbar-sub {
            font-size: 0.68rem;
            color: #eeca3e;
            font-weight: 600;
        }

        .mobile-topbar-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.62rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            line-height: 1;
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
                <!-- زر إغلاق الشريط الجانبي للموبايل -->
                <button type="button" class="btn btn-sm text-white rounded-circle d-md-none border-0 sidebar-mobile-close" onclick="window.closeMobileSidebar ? window.closeMobileSidebar() : null" title="إغلاق القائمة">
                    <i class="fas fa-times"></i>
                </button>

                <!-- زر تصغير الشريط الجانبي للديسكتوب فقط -->
                <button class="toggle-sidebar d-none d-md-flex" id="toggleSidebar" title="تصغير القائمة الجانبية">
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
                                <i class="fas fa-calendar-star"></i>
                                المعارض والفعاليات
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
                        $unreadNotifsMobile = auth()->user()->unreadNotifications->count() ?? 0;
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

    <style>
        /* Dark mode support for drawers */
        html[data-theme='dark'] #notifDrawerPanel,
        body.dark-mode #notifDrawerPanel,
        html[data-theme='dark'] #msgDrawerPanel,
        body.dark-mode #msgDrawerPanel {
            background: #1e293b !important;
            color: #cbd5e1 !important;
        }
        html[data-theme='dark'] #notifDrawerPanel div[style*="background:#f8fafc"],
        body.dark-mode #notifDrawerPanel div[style*="background:#f8fafc"],
        html[data-theme='dark'] #msgDrawerPanel div[style*="background:#f8fafc"],
        body.dark-mode #msgDrawerPanel div[style*="background:#f8fafc"] {
            background: #0f172a !important;
        }
    </style>

    <!-- Bootstrap 5 Bundle JS (enables modals, dropdowns, tooltips globally) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                const isMobile = window.innerWidth < 768;
                const isCollapsed = sidebar.classList.contains('collapsed');
                if (toggleSidebar) {
                    const icon = toggleSidebar.querySelector('i');
                    if (icon) icon.className = isCollapsed ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
                }
                const floatingBtn = document.getElementById('floatingSidebarToggle');
                if (floatingBtn) {
                    floatingBtn.style.display = (!isMobile && isCollapsed) ? 'flex' : 'none';
                }
                if (!isMobile && isCollapsed) {
                    document.body.classList.add('sidebar-is-collapsed');
                } else {
                    document.body.classList.remove('sidebar-is-collapsed');
                }
            }

            window.openMobileSidebar = openMobileSidebar;
            window.closeMobileSidebar = closeMobileSidebar;

            window.addEventListener('resize', function () {
                updateToggleIcon();
            });

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
                    updateToggleIcon();
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

            // التعامل مع القوائم الفرعية - نظام أكورديون سلس (إغلاق القوائم الأخرى لتفادي الطول الزائد)
            window.toggleSubmenu = function (menuId, event) {
                const evt = event || window.event;
                if (evt) {
                    try { evt.preventDefault(); } catch (e) { }
                    try { evt.stopPropagation(); } catch (e) { }
                }
                const submenu = document.getElementById(menuId);
                if (!submenu) return;
                const menuGroup = submenu.closest('.menu-group');
                const isAlreadyOpen = submenu.classList.contains('show');

                // إغلاق كل القوائم الفرعية المفتوحة الأخرى تلقائياً لتقليص طول الشريط الجانبي (Accordion Mode)
                document.querySelectorAll('.sidebar .submenu.show').forEach(s => {
                    if (s !== submenu) {
                        s.classList.remove('show');
                        const mg = s.closest('.menu-group');
                        if (mg) mg.classList.remove('active');
                    }
                });

                if (isAlreadyOpen) {
                    submenu.classList.remove('show');
                    if (menuGroup) menuGroup.classList.remove('active');
                } else {
                    submenu.classList.add('show');
                    if (menuGroup) menuGroup.classList.add('active');
                }
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

            // ==========================================
            // نظام إدارة الإشعارات الفوري (AJAX & Realtime)
            // ==========================================
            const notifDropdownBtn = document.getElementById('notificationsDropdown');
            const notifList = document.getElementById('notifications-list');
            const notifHeaderCount = document.getElementById('notification-header-count');
            const markAllBtn = document.getElementById('markAllNotificationsReadBtn');
            const clearReadBtn = document.getElementById('clearReadNotificationsBtn');
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

            // دالة تحديث عداد الإشعارات في الواجهة
            function updateUnreadBadge(count) {
                const badge = document.getElementById('notification-badge');
                if (count > 0) {
                    if (badge) {
                        badge.innerText = count;
                        badge.style.display = 'inline-block';
                    } else if (notifDropdownBtn) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notification-badge';
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.innerText = count;
                        newBadge.innerHTML = count + '<span class="visually-hidden">unread messages</span>';
                        notifDropdownBtn.appendChild(newBadge);
                    }
                    if (notifHeaderCount) {
                        notifHeaderCount.innerText = count + ' غير مقروء';
                        notifHeaderCount.style.display = 'inline-block';
                    }
                } else {
                    if (badge) badge.remove();
                    if (notifHeaderCount) {
                        notifHeaderCount.innerText = '0 غير مقروء';
                        notifHeaderCount.style.display = 'none';
                    }
                }
            }

            // دالة جلب وعرض الإشعارات في القائمة المنسدلة
            function fetchNotificationsList() {
                if (!notifList) return;
                notifList.innerHTML = `
                    <li class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div>
                        <span class="small">جاري تحميل الإشعارات...</span>
                    </li>
                `;

                fetch('/notifications/api/notifications', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        const notifications = data.notifications || [];
                        const unreadCount = data.unread_count ?? 0;
                        updateUnreadBadge(unreadCount);

                        if (notifications.length === 0) {
                            notifList.innerHTML = `
                            <li class="text-center py-4 text-muted">
                                <i class="fas fa-bell-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                <span class="small">لا توجد إشعارات حتى الآن</span>
                            </li>
                        `;
                            return;
                        }

                        let html = '';
                        notifications.forEach(n => {
                            const isUnread = !n.is_read;
                            const iconClass = n.type === 'success' ? 'check-circle text-success' :
                                (n.type === 'danger' ? 'exclamation-circle text-danger' :
                                    (n.type === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-primary'));
                            const bgClass = isUnread ? 'bg-primary-subtle bg-opacity-10' : 'bg-transparent';
                            const timeString = n.created_at ? new Date(n.created_at).toLocaleDateString('ar-LY', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

                            html += `
                            <li class="border-bottom notification-item-row ${bgClass}">
                                <a href="/notifications/${n.id}" class="d-flex align-items-start p-3 text-decoration-none text-dark notification-link" style="transition: background 0.15s ease;">
                                    <div class="me-3 mt-1 flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(15, 23, 42, 0.06);">
                                            <i class="fas fa-${iconClass} fs-6"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                                            <h6 class="mb-0 fw-bold fs-6 text-truncate text-slate-800" style="max-width: 210px;">${n.title || 'إشعار جديد'}</h6>
                                            <small class="text-muted text-nowrap ms-2" style="font-size: 0.7rem;">${timeString}</small>
                                        </div>
                                        <p class="mb-0 text-muted small text-truncate" style="max-width: 250px;">${n.message || ''}</p>
                                    </div>
                                    ${isUnread ? '<span class="badge bg-primary rounded-circle p-1 ms-2 mt-2" style="width: 8px; height: 8px;" title="غير مقروء"></span>' : ''}
                                </a>
                            </li>
                        `;
                        });
                        notifList.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Error fetching notifications:', err);
                        notifList.innerHTML = `
                        <li class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                            <span class="small">تعذر جلب الإشعارات حالياً</span>
                        </li>
                    `;
                    });
            }

            // الاستماع لفتح القائمة المنسدلة - نقل القائمة إلى الـ body لتجنب overflow:hidden
            if (notifDropdownBtn) {
                const dropdownMenu = notifDropdownBtn.closest('.dropdown')?.querySelector('.dropdown-menu');
                const originalParent = dropdownMenu?.parentElement;

                notifDropdownBtn.addEventListener('show.bs.dropdown', function () {
                    fetchNotificationsList();

                    // نقل القائمة إلى الـ body لتجاوز overflow:hidden
                    if (dropdownMenu && originalParent) {
                        const rect = notifDropdownBtn.getBoundingClientRect();
                        dropdownMenu.style.position = 'fixed';
                        dropdownMenu.style.top = (rect.bottom + 6) + 'px';
                        dropdownMenu.style.right = 'auto';
                        dropdownMenu.style.left = rect.left + 'px';
                        dropdownMenu.style.zIndex = '9999';
                        dropdownMenu.style.display = 'block';
                        document.body.appendChild(dropdownMenu);
                    }
                });

                notifDropdownBtn.addEventListener('hide.bs.dropdown', function () {
                    // إعادة القائمة لمكانها الأصلي
                    if (dropdownMenu && originalParent && dropdownMenu.parentElement === document.body) {
                        dropdownMenu.style.position = '';
                        dropdownMenu.style.top = '';
                        dropdownMenu.style.left = '';
                        dropdownMenu.style.zIndex = '';
                        dropdownMenu.style.display = '';
                        originalParent.appendChild(dropdownMenu);
                    }
                });
            }

            // زر تحديد كافة الإشعارات كمقروءة
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري التحديد...';

                    fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(() => {
                            updateUnreadBadge(0);
                            fetchNotificationsList();
                            markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تم التحديد';
                            setTimeout(() => {
                                markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء';
                            }, 2500);
                        })
                        .catch(err => {
                            console.error('Error marking all as read:', err);
                            markAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> تحديد الكل كمقروء';
                        });
                });
            }

            // زر مسح الإشعارات المقروءة
            if (clearReadBtn) {
                clearReadBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!confirm('هل تريد مسح جميع الإشعارات المقروءة؟')) return;

                    clearReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري المسح...';

                    fetch('/notifications/read/delete', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(() => {
                            fetchNotificationsList();
                            clearReadBtn.innerHTML = '<i class="fas fa-trash-alt text-danger me-1"></i> تنظيف المقروء';
                        })
                        .catch(err => {
                            console.error('Error clearing read notifications:', err);
                            clearReadBtn.innerHTML = '<i class="fas fa-trash-alt text-danger me-1"></i> تنظيف المقروء';
                        });
                });
            }

            // فحص دوري للإشعارات في الخلفية كل 60 ثانية لتحديث العداد تلقائياً
            setInterval(() => {
                fetch('/notifications/api/notifications', {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data && typeof data.unread_count !== 'undefined') {
                            updateUnreadBadge(data.unread_count);
                        }
                    })
                    .catch(() => { });
            }, 60000);

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
    <script src="{{ asset('js/simple-dark-mode.js') }}"></script>

    @auth
    @include('components.ai-assistant-widget')
    @endauth

    @yield('scripts')
    @stack('scripts')

    <!-- ============================================================ -->
    <!-- دوال الدرج المنزلق للإشعارات والرسائل -->
    <!-- ============================================================ -->
    <script>
    // ==========================================
    // Notifications Drawer Functions
    // ==========================================
    function openNotificationsDrawer() {
        const drawer = document.getElementById('notificationsDrawer');
        const panel = document.getElementById('notifDrawerPanel');
        const backdrop = document.getElementById('notifDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        drawer.style.pointerEvents = 'all';
        backdrop.style.pointerEvents = 'all';
        backdrop.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        // جلب الإشعارات
        loadNotificationsInDrawer();
    }

    function closeNotificationsDrawer() {
        const drawer = document.getElementById('notificationsDrawer');
        const panel = document.getElementById('notifDrawerPanel');
        const backdrop = document.getElementById('notifDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        panel.style.transform = 'translateX(100%)';
        backdrop.style.opacity = '0';
        backdrop.style.pointerEvents = 'none';
        setTimeout(() => {
            drawer.style.pointerEvents = 'none';
        }, 350);
        document.body.style.overflow = '';
    }

    function loadNotificationsInDrawer() {
        const body = document.getElementById('notifDrawerBody');
        const subtitle = document.getElementById('notifDrawerSubtitle');
        if (!body) return;

        body.innerHTML = `<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div><p class="small">جاري تحميل الإشعارات...</p></div>`;

        fetch('/notifications/api/notifications', { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                const notifications = data.notifications || [];
                const unreadCount = data.unread_count ?? 0;
                if (subtitle) subtitle.textContent = unreadCount > 0 ? `${unreadCount} غير مقروء` : 'لا توجد إشعارات جديدة';

                if (notifications.length === 0) {
                    body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-bell-slash fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد إشعارات حتى الآن</p></div>`;
                    return;
                }

                let html = '';
                notifications.forEach(n => {
                    const isUnread = !n.is_read;
                    const iconMap = { success: 'check-circle text-success', danger: 'exclamation-circle text-danger', warning: 'exclamation-triangle text-warning' };
                    const iconClass = iconMap[n.type] || 'info-circle text-primary';
                    const timeStr = n.created_at ? new Date(n.created_at).toLocaleDateString('ar-LY', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
                    html += `
                    <a href="/notifications/${n.id}" class="d-flex align-items-start p-3 text-decoration-none border-bottom ${isUnread ? 'bg-primary bg-opacity-5' : ''}" style="color:inherit;transition:background 0.15s;">
                        <div class="me-3 mt-1 flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:rgba(15,23,42,0.06);">
                            <i class="fas fa-${iconClass} fs-6"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width:200px;font-size:0.88rem;">${n.title || 'إشعار جديد'}</h6>
                                <small class="text-muted text-nowrap ms-2" style="font-size:0.7rem;">${timeStr}</small>
                            </div>
                            <p class="mb-0 text-muted text-truncate small" style="max-width:250px;">${n.message || ''}</p>
                        </div>
                        ${isUnread ? '<span class="rounded-circle bg-primary ms-2 mt-2 flex-shrink-0" style="width:8px;height:8px;min-width:8px;"></span>' : ''}
                    </a>`;
                });
                body.innerHTML = html;
            })
            .catch(() => {
                body.innerHTML = `<div class="text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i><p class="small">تعذر تحميل الإشعارات</p></div>`;
            });
    }

    function markAllNotificationsRead() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        }).then(() => loadNotificationsInDrawer()).catch(() => {});
    }

    // ==========================================
    // Messages Drawer Functions
    // ==========================================
    function openMessagesDrawer() {
        const drawer = document.getElementById('messagesDrawer');
        const panel = document.getElementById('msgDrawerPanel');
        const backdrop = document.getElementById('msgDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        drawer.style.pointerEvents = 'all';
        backdrop.style.pointerEvents = 'all';
        backdrop.style.opacity = '1';
        panel.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';

        loadMessagesInDrawer();
    }

    function closeMessagesDrawer() {
        const drawer = document.getElementById('messagesDrawer');
        const panel = document.getElementById('msgDrawerPanel');
        const backdrop = document.getElementById('msgDrawerBackdrop');
        if (!drawer || !panel || !backdrop) return;

        panel.style.transform = 'translateX(100%)';
        backdrop.style.opacity = '0';
        backdrop.style.pointerEvents = 'none';
        setTimeout(() => {
            drawer.style.pointerEvents = 'none';
        }, 350);
        document.body.style.overflow = '';
    }

    function loadMessagesInDrawer() {
        const body = document.getElementById('msgDrawerBody');
        const subtitle = document.getElementById('msgDrawerSubtitle');
        if (!body) return;

        body.innerHTML = `<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-success mb-2" role="status"></div><p class="small">جاري تحميل الرسائل...</p></div>`;

        fetch('/messages/api/recent', { headers: { 'Accept': 'application/json' } })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                // API returns: { status, unread_total, conversations: [{user_id, name, last_message, last_message_time, unread_count, initial, avatar}] }
                const conversations = data.conversations || [];
                const unreadTotal = data.unread_total ?? 0;
                if (subtitle) subtitle.textContent = unreadTotal > 0 ? `${unreadTotal} رسالة غير مقروءة` : 'لا توجد رسائل جديدة';

                if (conversations.length === 0) {
                    body.innerHTML = `<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 opacity-25 d-block"></i><p class="small">لا توجد رسائل حتى الآن</p></div>`;
                    return;
                }

                let html = '';
                conversations.forEach(conv => {
                    const hasUnread = conv.unread_count > 0;
                    const initial = conv.initial || (conv.name || 'م').charAt(0);
                    const avatarHtml = conv.avatar
                        ? `<img src="${conv.avatar}" alt="${conv.name}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">`
                        : `<div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width:40px;height:40px;background:linear-gradient(135deg,#059669,#047857);font-size:1rem;">${initial}</div>`;
                    html += `
                    <a href="/messages/${conv.user_id}" class="d-flex align-items-start p-3 text-decoration-none border-bottom ${hasUnread ? 'bg-success bg-opacity-5' : ''}" style="color:inherit;transition:background 0.15s;">
                        <div class="me-3 mt-1 flex-shrink-0">${avatarHtml}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width:190px;font-size:0.88rem;">${conv.name || 'مجهول'}</h6>
                                <small class="text-muted text-nowrap ms-2" style="font-size:0.7rem;">${conv.last_message_time || ''}</small>
                            </div>
                            <p class="mb-0 text-muted text-truncate small" style="max-width:240px;">${conv.last_message || '...'}</p>
                        </div>
                        ${hasUnread ? `<span class="badge bg-success rounded-pill ms-2 mt-2" style="font-size:0.65rem;">${conv.unread_count}</span>` : ''}
                    </a>`;
                });
                body.innerHTML = html;
            })
            .catch(() => {
                // الرسائل API غير متاحة، إعادة التوجيه لصفحة الرسائل
                body.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-envelope fa-3x mb-3 opacity-25 d-block"></i>
                    <p class="small mb-3">لعرض الرسائل، انقر على الزر أدناه</p>
                    <a href="/messages" class="btn btn-sm btn-outline-success rounded-pill px-4">
                        <i class="fas fa-inbox me-1"></i>فتح صندوق الرسائل
                    </a>
                </div>`;
                if (subtitle) subtitle.textContent = 'صندوق الرسائل';
            });
    }

    // إغلاق الدرجين عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotificationsDrawer();
            closeMessagesDrawer();
        }
    });

    // ربط الدوال بالكائن العام window لضمان إمكانية استدعائها من أي مكان
    window.openNotificationsDrawer = openNotificationsDrawer;
    window.closeNotificationsDrawer = closeNotificationsDrawer;
    window.openMessagesDrawer = openMessagesDrawer;
    window.closeMessagesDrawer = closeMessagesDrawer;
    window.loadNotificationsInDrawer = loadNotificationsInDrawer;
    window.loadMessagesInDrawer = loadMessagesInDrawer;
    </script>

</body>

</html>