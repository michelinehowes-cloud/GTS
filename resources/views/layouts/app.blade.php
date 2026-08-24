<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        /* Prevent horizontal scroll */
        html,
        body {
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-authenticated" content="true">
        <meta name="user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>@yield('title', 'نظام إدارة الخريجين')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <!-- شاشة الترحيب البسيطة (Splash Screen) -->
    <div id="welcome-screen">
        <div class="welcome-content">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="splash-logo">
            <div class="splash-pulse"></div>
        </div>
    </div>

    <style>
        /* شاشة الترحيب البسيطة */
        #welcome-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            z-index: 100000;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease-out;
        }

        .welcome-content {
            text-align: center;
            position: relative;
        }

        .splash-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
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

    <!-- الشريط الجانبي -->
    <nav class="sidebar" id="sidebar">
        <div class="position-sticky sidebar-content">
            <div class="sidebar-header">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="logo-container">
                    <div
                        class="logo-img-placeholder university-logo d-flex align-items-center justify-content-center mx-auto">
                        <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" class="logo-img"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-none align-items-center justify-content-center w-100 h-100">
                            <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #1e3a8a;"></i>
                        </div>
                    </div>
                    <div class="logo-text">مكتب تدريب الخريجين</div>
                    <div class="logo-subtext">جامعة طرابلس</div>
                </div>

                <!-- عرض نوع لوحة التحكم -->
                <small class="text-light opacity-85 mt-2 d-block">
                    @auth
                        @if(auth()->user()->role == 'admin')
                            لوحة تحكم المدير
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
                                    href="{{ 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        auth()->user()->role == 'graduate' ? route('graduate.dashboard') :
                    (auth()->user()->role == 'training_coordinator' ? route('training-coordinator.dashboard') :
                        (auth()->user()->role == 'evaluation_followup' ? route('evaluation-followup.dashboard') :
                            (auth()->user()->role == 'career_guidance_officer' ? route('career-guidance.dashboard') :
                                (auth()->user()->role == 'partnership_officer' ? route('partnership.dashboard') :
                                    (auth()->user()->role == 'company' ? route('company.dashboard') :
                                        route('admin.dashboard')))))) 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    لوحة التحكم
                                </a>
                            </li>

                            <!-- إدارة التدريب (للمسؤول ومنسق التدريب) -->
                            @if(in_array(auth()->user()->role, ['admin', 'training_coordinator']))
                                <li class="nav-item">
                                    <a class="nav-link {{ Request::is('*trainings*') ? 'active' : '' }}"
                                        href="{{ auth()->user()->role == 'training_coordinator' ? route('training-coordinator.trainings') : route('admin.trainings') }}">
                                        <i class="fas fa-graduation-cap"></i>
                                        إدارة التدريب
                                    </a>
                                </li>
                                @if(auth()->user()->role == 'training_coordinator')
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
                            @endif

                            <!-- الأقسام الإدارية (للمسؤول فقط) -->
                            @if(auth()->user()->role == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                                        href="{{ route('admin.users') }}">
                                        <i class="fas fa-users-cog"></i>
                                        إدارة المستخدمين
                                    </a>
                                </li>


                                <!-- ادارة الشراكات والتوظيف -->
                                <li class="nav-item menu-group">
                                    <a class="nav-link {{ request()->routeIs('admin.companies*') || request()->routeIs('job-opportunities*') ? 'active' : '' }}"
                                        href="#" onclick="toggleSubmenu('partnership-employment-menu')">
                                        <i class="fas fa-handshake"></i>
                                        ادارة الشراكات والتوظيف
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </a>
                                    <div class="submenu {{ request()->routeIs('admin.companies*') || request()->routeIs('job-opportunities*') ? 'show' : '' }}"
                                        id="partnership-employment-menu">
                                        <a href="{{ route('admin.companies') }}"
                                            class="submenu-item {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
                                            إدارة الشركات
                                        </a>
                                        <a href="{{ route('job-opportunities.index') }}"
                                            class="submenu-item {{ request()->routeIs('job-opportunities*') ? 'active' : '' }}">
                                            إدارة فرص العمل
                                        </a>
                                    </div>
                                </li>

                                <!-- ادارة الارشاد المهني -->
                                <li class="nav-item menu-group">
                                    <a class="nav-link {{ request()->routeIs('admin.career-guidance.graduates.create') || request()->routeIs('job-opportunities.create') ? 'active' : '' }}"
                                        href="#" onclick="toggleSubmenu('quick-actions-menu')">
                                        <i class="fas fa-bolt"></i>
                                        ادارة الارشاد المهني
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </a>
                                    <div class="submenu {{ request()->routeIs('admin.career-guidance.graduates.create') || request()->routeIs('job-opportunities.create') ? 'show' : '' }}"
                                        id="quick-actions-menu">
                                        <a href="{{ route('admin.career-guidance.graduates') }}"
                                            class="submenu-item {{ request()->routeIs('admin.career-guidance.graduates') ? 'active' : '' }}">
                                            إدارة بيانات الخريجين
                                        </a>
                                        <a href="{{ route('admin.career-guidance.graduates.create') }}"
                                            class="submenu-item {{ request()->routeIs('admin.career-guidance.graduates.create') ? 'active' : '' }}">
                                            إضافة خريج
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
                                        معرض التوظيف 2026
                                        <span class="badge ms-1 py-1 px-2 rounded-pill" style="background:linear-gradient(135deg,#F59E0B,#F97316);font-size:0.62rem">جديد</span>
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
                                        <div class="submenu {{ request()->routeIs('media.*') && !request()->routeIs('media.trainings*') && !request()->routeIs('media.news*') && !request()->routeIs('media.announcements*') && !request()->routeIs('media.reports*') ? 'show' : '' }}"
                                            id="media-menu">
                                            <a href="{{ route('media.gallery') }}"
                                                class="submenu-item {{ request()->routeIs('media.gallery') ? 'active' : '' }}">
                                                معرض الوسائط
                                            </a>
                                            <a href="{{ route('media.upload.form') }}"
                                                class="submenu-item {{ request()->routeIs('media.upload.form') ? 'active' : '' }}">
                                                رفع الوسائط
                                            </a>
                                        </div>
                                </li>

                                <!-- إدارة المحتوى -->
                                <li class="nav-item menu-group">
                                    <a class="nav-link {{ request()->routeIs('media.news*') || request()->routeIs('media.announcements*') ? 'active' : '' }}"
                                        href="#" onclick="toggleSubmenu('content-menu')">
                                        <i class="fas fa-newspaper"></i>
                                        إدارة المحتوى
                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                    </a>
                                    <div class="submenu {{ request()->routeIs('media.news*') || request()->routeIs('media.announcements*') ? 'show' : '' }}"
                                        id="content-menu">
                                        <a href="{{ route('media.news.index') }}"
                                            class="submenu-item {{ request()->routeIs('media.news.index') ? 'active' : '' }}">
                                            الأخبار
                                        </a>
                                        <a href="{{ route('media.announcements.index') }}"
                                            class="submenu-item {{ request()->routeIs('media.announcements.index') ? 'active' : '' }}">
                                            الإعلانات
                                        </a>
                                    </div>
                                </li>

                                <!-- التقارير -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('media.reports*') ? 'active' : '' }}"
                                        href="{{ route('media.reports.coverage') }}">
                                        <i class="fas fa-chart-bar"></i>
                                        التقارير
                                    </a>
                                </li>
                                <!-- تسجيل الخروج -->
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

    <!-- المحتوى الرئيسي -->
    <div class="main-content" id="mainContent">

        <!-- الشريط العلوي -->
        <nav class="navbar navbar-expand-lg navbar-main" id="navbarMain">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center">
                    <button class="toggle-sidebar-main" id="toggleSidebarMain">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="navbar-brand mb-0 ms-3">
                        <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين"
                            style="height: 40px; margin-left: 10px; display: inline-block;"
                            onerror="this.style.display='none'">
                        <i class="fas fa-graduation-cap me-2"></i>
                        @yield('page-title', 'لوحة التحكم الرئيسية')
                    </h4>
                </div>

                <div class="d-flex align-items-center">
                    <div class="me-3 text-end">
                        <div class="fw-bold text-dark fs-6">{{ auth()->user()->name ?? 'مستخدم' }}</div>
                        <small class="text-muted">
                            @auth
                                {{ auth()->user()->role_name }}
                            @endauth
                        </small>
                    </div>

                    <!-- Notifications Dropdown -->
                    @auth
                        <div class="dropdown me-3">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationsDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg text-secondary"></i>
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
                            <i class="fas fa-moon fa-lg text-secondary"></i>
                        </button>
                    @endauth
                    @auth
                        @php
                            $roleIcons = [
                                'admin' => 'fas fa-user-shield',
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
                background: rgba(0, 0, 0, 0.5); z-index: 1035; display: none; opacity: 0; transition: opacity 0.3s ease;
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
                setTimeout(() => sidebarOverlay.style.opacity = '1', 10);
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('active-mobile');
                sidebar.classList.add('collapsed');
                sidebarOverlay.style.opacity = '0';
                setTimeout(() => sidebarOverlay.style.display = 'none', 300);
            }

            function updateToggleIcon() {
                if (toggleSidebar) {
                    const icon = toggleSidebar.querySelector('i');
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    if (icon) icon.className = isCollapsed ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
                }
            }

            // ربط الأزرار بالدالة
            if (toggleSidebar) toggleSidebar.addEventListener('click', toggleSidebarFunc);
            if (toggleSidebarMain) toggleSidebarMain.addEventListener('click', toggleSidebarFunc);

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
                    .sidebar {
                        transform: translateX(100%);
                        transition: transform 0.3s ease-in-out;
                        position: fixed !important; top: 0; right: 0; height: 100vh; z-index: 1040; width: 280px !important;
                    }
                    .sidebar.active-mobile { transform: translateX(0) !important; }
                    .sidebar.collapsed { transform: translateX(100%) !important; }
                    .main-content, .navbar-main { margin-right: 0 !important; width: 100% !important; }
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

    @yield('scripts')
    @stack('scripts')

</body>

</html>