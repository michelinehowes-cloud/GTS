<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'نظام إدارة الخريجين')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
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
                            <div class="logo-img-placeholder university-logo d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('storage/logo.png') }}" alt="شعار مكتب تدريب الخريجين" class="logo-img"
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
                                @endif
                            @endauth
                        </small>
                    </div>

                    <ul class="nav flex-column mt-3">
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
                            <a class="nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}" href="{{ route('admin.companies') }}">
                                <i class="fas fa-building"></i>
                                إدارة الشركات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="fas fa-users-cog"></i>
                                إدارة المستخدمين
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير والإحصائيات
                            </a>
                        </li>
                        @endif

                        <!-- التدريبات المتاحة (للخريج فقط) -->
                        @if(auth()->user()->role == 'graduate')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('graduate.trainings*') ? 'active' : '' }}" href="{{ route('graduate.trainings') }}">
                                <i class="fas fa-graduation-cap"></i>
                                التدريبات المتاحة
                            </a>
                        </li>
                        @endif

                        <!-- أقسام التقييم والمتابعة (لمستخدم التقييم والمتابعة فقط) -->
                        @if(auth()->user()->role == 'evaluation_followup')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.training-reports') ? 'active' : '' }}" href="{{ route('evaluation-followup.training-reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                تقارير التدريب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.partnership-employment-reports') ? 'active' : '' }}" href="{{ route('evaluation-followup.partnership-employment-reports') }}">
                                <i class="fas fa-chart-pie"></i>
                                تقارير الشراكات والتوظيف المتقدمة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('evaluation-followup.training-calendar') ? 'active' : '' }}" href="{{ route('evaluation-followup.training-calendar') }}">
                                <i class="fas fa-calendar-alt"></i>
                                تقويم التدريبات
                            </a>
                        </li>
                        @endif

                        <!-- مسؤول الإرشاد المهني -->
                        @if(auth()->user()->role == 'career_guidance_officer')
                            <!-- إدارة الخريجين -->
                            <li class="nav-item menu-group">
                                <a class="nav-link {{ request()->routeIs('career-guidance.graduates*') ? 'active' : '' }}"
                                   href="#" onclick="toggleSubmenu('graduates-menu')">
                                    <i class="fas fa-users"></i>
                                    إدارة الخريجين
                                    <i class="fas fa-chevron-down menu-arrow"></i>
                                </a>
                                <div class="submenu {{ request()->routeIs('career-guidance.graduates*') ? 'show' : '' }}" id="graduates-menu">
                                    <a href="{{ route('career-guidance.graduates') }}" class="submenu-item {{ request()->routeIs('career-guidance.graduates') ? 'active' : '' }}">
                                        عرض الخريجين
                                    </a>
                                    <a href="{{ route('career-guidance.graduates.create') }}" class="submenu-item {{ request()->routeIs('career-guidance.graduates.create') ? 'active' : '' }}">
                                        إضافة خريج
                                    </a>
                                    <a href="{{ route('career-guidance.import.graduates') }}" class="submenu-item {{ request()->routeIs('career-guidance.import.graduates') ? 'active' : '' }}">
                                        استيراد الخريجين
                                    </a>
                                    <a href="{{ route('career-guidance.download.template') }}" class="submenu-item {{ request()->routeIs('career-guidance.download.template') ? 'active' : '' }}">
                                        تنزيل قالب الخريجين
                                    </a>
                                </div>
                            </li>

                            <!-- الترشيحات -->
                            <li class="nav-item menu-group">
                                <a class="nav-link {{ request()->routeIs('career-guidance.nominations*') ? 'active' : '' }}"
                                   href="#" onclick="toggleSubmenu('nominations-menu')">
                                    <i class="fas fa-paper-plane"></i>
                                    الترشيحات
                                    @php
                                        $pendingNominations = \App\Models\Nomination::where('status', 'pending')->count();
                                    @endphp
                                    @if($pendingNominations > 0)
                                        <span class="nav-badge">{{ $pendingNominations }}</span>
                                    @endif
                                    <i class="fas fa-chevron-down menu-arrow"></i>
                                </a>
                                <div class="submenu {{ request()->routeIs('career-guidance.nominations*') ? 'show' : '' }}" id="nominations-menu">
                                    <a href="{{ route('career-guidance.nominations') }}" class="submenu-item {{ request()->routeIs('career-guidance.nominations') ? 'active' : '' }}">
                                        عرض الترشيحات
                                    </a>
                                    <a href="{{ route('career-guidance.nominations.create') }}" class="submenu-item {{ request()->routeIs('career-guidance.nominations.create') ? 'active' : '' }}">
                                        ترشيح جديد
                                    </a>
                                </div>
                            </li>

                            <!-- فرص العمل -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('job-opportunities*') ? 'active' : '' }}"
                                   href="{{ route('job-opportunities.index') }}">
                                    <i class="fas fa-briefcase"></i>
                                    فرص العمل
                                </a>
                            </li>

                            <!-- إدارة الشركات -->
                            <li class="nav-item menu-group">
                                <a class="nav-link {{ request()->routeIs('career-guidance.companies*') ? 'active' : '' }}"
                                   href="{{ route('career-guidance.companies') }}">
                                    <i class="fas fa-building"></i>
                                    إدارة الشركات
                                </a>
                            </li>

                            <!-- التقارير -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('career-guidance.advanced-reports') ? 'active' : '' }}"
                                   href="{{ route('career-guidance.advanced-reports') }}">
                                    <i class="fas fa-chart-line me-2"></i>
                                    التقارير والإحصائيات
                                </a>
                            </li>
                        @endif

                        <!-- مسؤول الشراكات والتوظيف -->
                        @if(auth()->user()->role == 'partnership_officer')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('partnership.companies*') ? 'active' : '' }}" href="{{ route('partnership.companies') }}">
                                    <i class="fas fa-building"></i>
                                    إدارة الشركات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('job-opportunities*') ? 'active' : '' }}" href="{{ route('job-opportunities.index') }}">
                                    <i class="fas fa-briefcase"></i>
                                    فرص العمل
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('partnership.documents*') ? 'active' : '' }}" href="{{ route('partnership.documents') }}">
                                    <i class="fas fa-file-alt"></i>
                                    وثائق الشراكة
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('partnership.reports*') ? 'active' : '' }}" href="{{ route('partnership.reports') }}">
                                    <i class="fas fa-chart-line"></i>
                                    التقارير
                                </a>
                            </li>
                        @endif

                        <!-- لوحة تحكم الشركة -->
                        @if(auth()->user()->role == 'company')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('company.profile') ? 'active' : '' }}" href="{{ route('company.profile') }}">
                                    <i class="fas fa-building"></i>
                                    ملف الشركة
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('company.job-opportunities*') ? 'active' : '' }}" href="{{ route('company.job-opportunities') }}">
                                    <i class="fas fa-briefcase"></i>
                                    فرص العمل
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('company.applications*') ? 'active' : '' }}" href="{{ route('company.applications') }}">
                                    <i class="fas fa-file-alt"></i>
                                    الطلبات
                                </a>
                            </li>
                        @endif
                        
                        <!-- تسجيل الخروج -->
                        <li class="nav-item mt-4 pt-3 border-top border-light">
                            <a class="nav-link text-warning fw-bold" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                تسجيل الخروج
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
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
                                <img src="{{ asset('storage/logo.png') }}" alt="شعار مكتب تدريب الخريجين" 
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
                                        @if(auth()->user()->role == 'admin')
                                            مدير النظام
                                        @elseif(auth()->user()->role == 'training_coordinator')
                                            منسق التدريب
                                        @elseif(auth()->user()->role == 'placement_coordinator')
                                            منسق التوظيف
                                        @elseif(auth()->user()->role == 'graduate')
                                            خريج
                                        @elseif(auth()->user()->role == 'evaluation_followup')
                                            مسؤول التقييم والمتابعة
                                        @elseif(auth()->user()->role == 'career_guidance_officer')
                                            مسؤول الإرشاد المهني
                                        @elseif(auth()->user()->role == 'partnership_officer')
                                            مسؤول الشراكات والتوظيف
                                        @elseif(auth()->user()->role == 'company')
                                            شركة
                                        @endif
                                    @endauth
                                </small>
                            </div>
                            <div class="user-avatar pulse-animation">
                                {{ substr(auth()->user()->name ?? 'م', 0, 1) }}
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // التحكم في إظهار/إخفاء الشريط الجانبي
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const navbarMain = document.getElementById('navbarMain'); // Added navbarMain
        const toggleSidebar = document.getElementById('toggleSidebar');
        const toggleSidebarMain = document.getElementById('toggleSidebarMain');

        function toggleSidebarFunc() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbarMain.classList.toggle('expanded'); // Toggle navbarMain as well
            
            // تغيير الأيقونة
            const icon = toggleSidebar.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-left';
            } else {
                icon.className = 'fas fa-chevron-right';
            }
        }

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', toggleSidebarFunc);
        }
        
        if (toggleSidebarMain) {
            toggleSidebarMain.addEventListener('click', toggleSidebarFunc);
        }

        // تبديل القوائم الفرعية
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId);
            const menuGroup = submenu.closest('.menu-group');
            
            submenu.classList.toggle('show');
            menuGroup.classList.toggle('active');
        }

        // إغلاق الشريط الجانبي تلقائياً على الشاشات الصغيرة
        function setSidebarState() {
            if (window.innerWidth < 768) {
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
                if (navbarMain) navbarMain.classList.add('expanded'); // Also collapse navbarMain
            } else {
                if (sidebar) sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                if (navbarMain) navbarMain.classList.remove('expanded'); // Also expand navbarMain
            }
        }

        // تعيين الحالة الأولية عند تحميل الصفحة
        setSidebarState();

        // إعادة الضبط عند تغيير حجم النافذة
        window.addEventListener('resize', setSidebarState);

        // تحديد العنصر النشط تلقائياً
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
