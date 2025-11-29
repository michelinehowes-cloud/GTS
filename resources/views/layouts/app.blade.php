<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة الخريجين')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container-fluid">
        <div class="row g-0">
            <!-- الشريط الجانبي -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="position-sticky sidebar-content">
                    <div class="sidebar-header">
                        <button class="toggle-sidebar" id="toggleSidebar">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="logo-container">
                            <div
                                class="logo-img-placeholder university-logo d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين"
                                    class="logo-img"
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
                                                    <a class="nav-link {{ request()->routeIs('evaluation-followup*') ? 'active' : '' }}"
                                                        href="#" onclick="toggleSubmenu('admin-evaluation-menu')">
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
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}"
                                                        href="{{ route('notifications.index') }}">
                                                        <i class="fas fa-bell"></i>
                                                        الإشعارات
                                                    </a>
                                                </li>
                                            @endif

                                            <!-- أقسام الإرشاد المهني (لمستخدم الإرشاد المهني فقط) -->
                                            @if(auth()->user()->role == 'career_guidance_officer')

                                                <!-- إدارة بيانات الخريجين -->
                                                <li class="nav-item menu-group">
                                                    <a class="nav-link {{ request()->routeIs('career-guidance.graduates*') ? 'active' : '' }}"
                                                        href="#" onclick="toggleSubmenu('cg-graduates-menu')">
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
                                                    <a class="nav-link {{ request()->routeIs('company.job-opportunities*') ? 'active' : '' }}"
                                                        href="{{ route('company.job-opportunities') }}">
                                                        <i class="fas fa-briefcase"></i>
                                                        فرص العمل
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('company.applications*') ? 'active' : '' }}"
                                                        href="{{ route('company.applications') }}">
                                                        <i class="fas fa-file-alt"></i>
                                                        الطلبات
                                                    </a>
                                                </li>
                                            @endif

                                            <!-- مسؤول الميديا -->
                                            @if(auth()->user()->role == 'media_officer')


                                                <!-- فهرس التدريبات للميديا -->
                                                <li class="nav-item">
                                                    <a class="nav-link {{ request()->routeIs('evaluation-followup.training-calendar') ? 'active' : '' }}"
                                                        href="{{ route('evaluation-followup.training-calendar') }}">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        تقويم التدريبات
                                                    </a>
                                                </li>

                                                <!-- إدارة الوسائط -->
                                                <li class="nav-item menu-group">
                                                    <a class="nav-link {{ request()->routeIs('media.*') && !request()->routeIs('media.trainings*') && !request()->routeIs('media.news*') && !request()->routeIs('media.announcements*') && !request()->routeIs('media.reports*') ? 'active' : '' }}"
                                                        href="#" onclick="toggleSubmenu('media-menu')">
                                                        <i class="fas fa-images"></i>
                                                        إدارة الوسائط
                                                        <i class="fas fa-chevron-down menu-arrow"></i>
                                                    </a>
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
                                            <li class="nav-item mt-4 pt-3 border-top border-light">
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
            <div class="col-md-9 ms-sm-auto col-lg-10 main-content" id="mainContent">

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
                                    <a class="nav-link dropdown-toggle position-relative" href="#"
                                        id="notificationsDropdown" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
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
                                        <li><a class="dropdown-item text-center small text-primary fw-bold py-2"
                                                href="{{ route('notifications.index') }}">عرض كل الإشعارات</a></li>
                                    </ul>
                                </div>
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
                                    $userIcon = $roleIcons[auth()->user()->role] ?? 'fas fa-user'; // Default icon
                                @endphp
                                <div class="user-avatar pulse-animation">
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
        </div>
    </div>

    <!-- Bootstrap CDN removed to avoid conflict with Vite build -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        console.log('Script started.');

        // التحكم في إظهار/إخفاء الشريط الجانبي
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const navbarMain = document.getElementById('navbarMain'); // Added navbarMain
        const toggleSidebar = document.getElementById('toggleSidebar');
        const toggleSidebarMain = document.getElementById('toggleSidebarMain');

        console.log('Elements found:', { sidebar, mainContent, navbarMain, toggleSidebar, toggleSidebarMain });

        function toggleSidebarFunc() {
            console.log('toggleSidebarFunc called.');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbarMain.classList.toggle('expanded'); // Toggle navbarMain as well

            // تغيير الأيقونة
            const icon = toggleSidebar.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('collapsed')) {
                    icon.className = 'fas fa-chevron-left';
                } else {
                    icon.className = 'fas fa-chevron-right';
                }
            }
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            console.log('Sidebar collapsed state saved:', sidebar.classList.contains('collapsed'));
        }

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', toggleSidebarFunc);
            console.log('toggleSidebar event listener attached.');
        }

        if (toggleSidebarMain) {
            toggleSidebarMain.addEventListener('click', toggleSidebarFunc);
            console.log('toggleSidebarMain event listener attached.');
        }

        // تبديل القوائم الفرعية
        function toggleSubmenu(menuId) {
            console.log('toggleSubmenu called for:', menuId);
            const submenu = document.getElementById(menuId);
            const menuGroup = submenu.closest('.menu-group');

            submenu.classList.toggle('show');
            menuGroup.classList.toggle('active');
        }

        // إغلاق الشريط الجانبي تلقائياً على الشاشات الصغيرة
        function setSidebarState() {
            console.log('setSidebarState called.');
            // Check if a state is saved in localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            console.log('Saved sidebar state from localStorage:', savedState);

            if (savedState === 'true') {
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
                if (navbarMain) navbarMain.classList.add('expanded');
            } else if (savedState === 'false') {
                if (sidebar) sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                if (navbarMain) navbarMain.classList.remove('expanded');
            } else {
                // Default behavior based on screen size if no state is saved
                if (window.innerWidth < 768) {
                    if (sidebar) sidebar.classList.add('collapsed');
                    if (mainContent) mainContent.classList.add('expanded');
                    if (navbarMain) navbarMain.classList.add('expanded');
                } else {
                    if (sidebar) sidebar.classList.remove('collapsed');
                    if (mainContent) mainContent.classList.remove('expanded');
                    if (navbarMain) navbarMain.classList.remove('expanded');
                }
            }

            // Update the icon based on the final state, only if toggleSidebar exists
            if (toggleSidebar) {
                const icon = toggleSidebar.querySelector('i');
                if (icon) {
                    if (sidebar.classList.contains('collapsed')) {
                        icon.className = 'fas fa-chevron-left';
                    } else {
                        icon.className = 'fas fa-chevron-right';
                    }
                }
            }
        }

        // تعيين الحالة الأولية عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOMContentLoaded fired.');
            setSidebarState(); // Call on DOMContentLoaded to ensure elements are available
            window.addEventListener('resize', setSidebarState); // Re-evaluate on resize

            const currentPath = window.location.pathname;

            // تحديث القوائم الفرعية النشطة
            document.querySelectorAll('.submenu-item').forEach(item => {
                if (item.href && currentPath.includes(new URL(item.href).pathname)) {
                    @auth
                        {{ auth()->user()->role_name }}
                    @endauth
                                </small >
                            </div >

                        <!-- Notifications Dropdown -->
                        @auth
                            < div class="dropdown me-3" >
                                <a class="nav-link dropdown-toggle position-relative" href="#"
                                    id="notificationsDropdown" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
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
                                    <li><a class="dropdown-item text-center small text-primary fw-bold py-2"
                                            href="{{ route('notifications.index') }}">عرض كل الإشعارات</a></li>
                                </ul>
                            </div >
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
                                $userIcon = $roleIcons[auth()->user()->role] ?? 'fas fa-user'; // Default icon
                            @endphp
                            < div class="user-avatar pulse-animation" >
                                <i class="{{ $userIcon }}"></i>
                                            </div >
                        @endauth
                        </div >
                    </div >
                </nav >

                < !--محتوى الصفحة-- >
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
            </div >
        </div >
    </div >

                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
            console.log('Script started.');

            // التحكم في إظهار/إخفاء الشريط الجانبي
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const navbarMain = document.getElementById('navbarMain'); // Added navbarMain
            const toggleSidebar = document.getElementById('toggleSidebar');
            const toggleSidebarMain = document.getElementById('toggleSidebarMain');

            console.log('Elements found:', {sidebar, mainContent, navbarMain, toggleSidebar, toggleSidebarMain});

            function toggleSidebarFunc() {
                console.log('toggleSidebarFunc called.');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbarMain.classList.toggle('expanded'); // Toggle navbarMain as well

            // تغيير الأيقونة
            const icon = toggleSidebar.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-left';
                } else {
                icon.className = 'fas fa-chevron-right';
                }
            }
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            console.log('Sidebar collapsed state saved:', sidebar.classList.contains('collapsed'));
        }

            if (toggleSidebar) {
                toggleSidebar.addEventListener('click', toggleSidebarFunc);
            console.log('toggleSidebar event listener attached.');
        }

            if (toggleSidebarMain) {
                toggleSidebarMain.addEventListener('click', toggleSidebarFunc);
            console.log('toggleSidebarMain event listener attached.');
        }

            // تبديل القوائم الفرعية
            function toggleSubmenu(menuId) {
                console.log('toggleSubmenu called for:', menuId);
            const submenu = document.getElementById(menuId);
            const menuGroup = submenu.closest('.menu-group');

            submenu.classList.toggle('show');
            menuGroup.classList.toggle('active');
        }

            // إغلاق الشريط الجانبي تلقائياً على الشاشات الصغيرة
            function setSidebarState() {
                console.log('setSidebarState called.');
            // Check if a state is saved in localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            console.log('Saved sidebar state from localStorage:', savedState);

            if (savedState === 'true') {
                if (sidebar) sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
            if (navbarMain) navbarMain.classList.add('expanded');
            } else if (savedState === 'false') {
                if (sidebar) sidebar.classList.remove('collapsed');
            if (mainContent) mainContent.classList.remove('expanded');
            if (navbarMain) navbarMain.classList.remove('expanded');
            } else {
                // Default behavior based on screen size if no state is saved
                if (window.innerWidth < 768) {
                    if (sidebar) sidebar.classList.add('collapsed');
            if (mainContent) mainContent.classList.add('expanded');
            if (navbarMain) navbarMain.classList.add('expanded');
                } else {
                    if (sidebar) sidebar.classList.remove('collapsed');
            if (mainContent) mainContent.classList.remove('expanded');
            if (navbarMain) navbarMain.classList.remove('expanded');
                }
            }

            // Update the icon based on the final state, only if toggleSidebar exists
            if (toggleSidebar) {
                const icon = toggleSidebar.querySelector('i');
            if (icon) {
                    if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-left';
                    } else {
                icon.className = 'fas fa-chevron-right';
                    }
                }
            }
        }

            // تعيين الحالة الأولية عند تحميل الصفحة
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOMContentLoaded fired.');
            setSidebarState(); // Call on DOMContentLoaded to ensure elements are available
            window.addEventListener('resize', setSidebarState); // Re-evaluate on resize

            const currentPath = window.location.pathname;

            // تحديث القوائم الفرعية النشطة
            document.querySelectorAll('.submenu-item').forEach(item => {
                if (item.href && currentPath.includes(new URL(item.href).pathname)) {
                item.classList.add('active');
            const parentMenu = item.closest('.menu-group');
            if (parentMenu) {
                parentMenu.classList.add('active');
            const submenu = item.closest('.submenu');
            if (submenu) {
                submenu.classList.add('show');
                        }
                    }
                }
            });
        });

            // Notifications Script
            document.addEventListener('DOMContentLoaded', function() {
            const dropdownElement = document.getElementById('notificationsDropdown');
            if (dropdownElement) {
                // Initialize Bootstrap dropdown manually to ensure it works
                // const dropdown = new bootstrap.Dropdown(dropdownElement);

                dropdownElement.addEventListener('show.bs.dropdown', function () {
                    console.log('Dropdown showing, fetching notifications...');
                    fetch('{{ route("notifications.api") }}')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Notifications data:', data);
                            const list = document.getElementById('notifications-list');
                            list.innerHTML = '';
                            if (data.notifications.length === 0) {
                                list.innerHTML = '<li class="dropdown-item text-center text-muted py-3">لا توجد إشعارات جديدة</li>';
                            } else {
                                data.notifications.forEach(notification => {
                                    const item = `
                                        <li>
                                            <a class="dropdown-item d-flex align-items-start py-2 border-bottom" href="/notifications/${notification.id}">
                                                <div class="me-3 mt-1">
                                                    <div class="icon-circle bg-${notification.type || 'primary'} rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-${notification.icon || 'bell'} text-white small"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="small text-muted float-end" style="font-size: 0.7rem;">${new Date(notification.created_at).toLocaleDateString('ar-EG')}</div>
                                                    <span class="fw-bold d-block text-dark" style="font-size: 0.9rem;">${notification.title}</span>
                                                    <div class="small text-muted text-truncate" style="max-width: 200px;">${notification.message}</div>
                                                </div>
                                            </a>
                                        </li>
                                    `;
                                    list.innerHTML += item;
                                });
                            }

                            // Update badge if needed
                            const badge = document.getElementById('notification-badge');
                            if (data.unread_count > 0) {
                                if (badge) {
                                    badge.innerText = data.unread_count;
                                } else {
                                    // Create badge if it doesn't exist
                                    const badgeSpan = document.createElement('span');
                                    badgeSpan.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                                    badgeSpan.id = 'notification-badge';
                                    badgeSpan.innerText = data.unread_count;
                                    dropdownElement.appendChild(badgeSpan);
                                }
                            } else if (badge) {
                                badge.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching notifications:', error);
                            document.getElementById('notifications-list').innerHTML = '<li class="dropdown-item text-center text-danger py-3">حدث خطأ في تحميل الإشعارات</li>';
                        });
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
    @stack('scripts')
    <script src="{{ asset('js/notifications.js') }}"></script>

</body>

</html>