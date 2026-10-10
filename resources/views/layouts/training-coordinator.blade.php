<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'منسق التدريب')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/premium-forms.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #045db0;
            --primary-dark: #1e40af;
            --primary-medium: #3b82f6;
            --primary-light: #60a5fa;
            --accent-gold: #eeca3e;
            --accent-light: #fbbf24;
            --university-blue: #045db0;
            --university-gold: #eeca3e;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --background-light: #f8fafc;
            --white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * {
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.7;
            font-weight: 500;
            transition: all 0.3s ease;
            /* يجب أن يكون ارتفاع الجسم كافياً للسكرول */
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--university-blue) 0%, #045db0 100%);
            height: 100vh;
            height: 100dvh;
            color: var(--white);
            position: fixed;
            width: 280px;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
            right: 0;
            top: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar.collapsed {
            transform: translateX(100%);
            width: 0;
            opacity: 0;
        }

        .sidebar-content {
            transition: all 0.3s ease;
            opacity: 1;
        }

        .sidebar.collapsed .sidebar-content {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-header {
            padding: 20px 15px;
            background: rgba(255, 255, 255, 0.1);
            text-align: center;
            border-bottom: 2px solid var(--university-gold);
            position: relative;
        }

        .toggle-sidebar {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .toggle-sidebar:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-img {
            max-width: 80px;
            max-height: 80px;
            margin-bottom: 10px;
            border-radius: 10px;
            background: var(--white);
            padding: 5px;
            border: 2px solid var(--university-gold);
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--white);
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .logo-subtext {
            font-size: 0.8rem;
            color: var(--university-gold);
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.95);
            padding: 15px 20px;
            margin: 5px 8px;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            transform: translateX(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-medium) 0%, var(--university-gold) 100%);
            color: var(--white);
            font-weight: 700;
            box-shadow: 0 5px 20px rgba(59, 130, 246, 0.4);
        }

        .nav-link i {
            width: 20px;
            margin-left: 12px;
            font-size: 1rem;
        }

        .nav-badge {
            background: var(--university-gold);
            color: var(--university-blue);
            border-radius: 8px;
            padding: 2px 8px;
            font-size: 0.7rem;
            font-weight: 800;
            margin-left: auto;
        }

        /* التعديل الأهم لحل مشكلة التخطيط */
        .main-content {
            margin-right: 280px;
            /* لإزاحة المحتوى بمقدار عرض الشريط الجانبي */
            padding: 0;
            min-height: 100vh;
            transition: all 0.3s ease;
            width: calc(100% - 280px);
            /* ليأخذ العرض المتبقي */
        }

        .main-content.expanded {
            margin-right: 0;
            width: 100%;
        }

        /* تنسيق الشريط العلوي */
        .navbar-main {
            background: var(--white);
            box-shadow: 0 3px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid var(--university-gold);
            padding: 12px 0;
            backdrop-filter: blur(10px);
            /* التعديل هنا: جعله ثابتًا في الأعلى ويأخذ عرض الـ main-content */
            position: sticky;
            /* أفضل من fixed إذا لم يكن هناك حاجة لـ z-index عالي جداً */
            top: 0;
            z-index: 999;
            /* ليكون فوق المحتوى ولكن تحت الـ sidebar (1000) */
            transition: all 0.3s ease;
            width: calc(100% - 280px);
            /* نفس عرض المحتوى الرئيسي */
            right: 0;
        }

        .navbar-main.expanded {
            width: 100%;
        }

        /* تعديل إزاحة المحتوى الرئيسي بعد شريط التنقل */
        .content-area-padding {
            padding-top: 0;
            /* تم إزالة البادنج هنا والسماح للـ Navbar بإزاحة المحتوى */
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--university-blue) !important;
            font-size: 1.4rem;
        }

        .toggle-sidebar-main {
            background: var(--university-blue);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 15px;
        }

        .toggle-sidebar-main:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--university-blue), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 800;
            font-size: 1.2rem;
            box-shadow: 0 5px 20px rgba(30, 58, 138, 0.4);
            border: 2px solid var(--university-gold);
        }

        @media (max-width: 768px) {
            .sidebar {
                /* إظهار/إخفاء على الشاشات الصغيرة */
                transform: translateX(100%);
                opacity: 0;
            }

            .sidebar:not(.collapsed) {
                transform: translateX(0);
                opacity: 1;
                width: 280px;
            }

            .main-content {
                margin-right: 0;
                width: 100%;
            }

            .navbar-main,
            .navbar-main.expanded {
                width: 100%;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row g-0">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="position-sticky sidebar-content">
                    <div class="sidebar-header">
                        <button class="toggle-sidebar" id="toggleSidebar">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="logo-container">
                            <div class="logo-img-placeholder university-logo d-flex align-items-center justify-content-center mx-auto"
                                style="width: 80px; height: 80px; border-radius: 10px; background: white; margin-bottom: 10px;">
                                <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين"
                                    class="logo-img"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="d-none align-items-center justify-content-center w-100 h-100">
                                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #045db0;"></i>
                                </div>
                            </div>
                            <div class="logo-text">مكتب تدريب الخريجين</div>
                            <div class="logo-subtext">جامعة طرابلس</div>
                        </div>
                        <small class="text-light opacity-85 mt-2 d-block">
                            منسق التدريب
                        </small>
                    </div>

                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.dashboard') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
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
                            <a class="nav-link {{ request()->routeIs('training-coordinator.trainings*') && !request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.trainings') }}">
                                <i class="fas fa-graduation-cap"></i>
                                إدارة برامج التدريب
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
                                    <span class="nav-badge">{{ $pendingCount }}</span>
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
                            <a class="nav-link {{ request()->routeIs('training-coordinator.reports') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير والإحصائيات
                            </a>
                        </li>

                        <li class="nav-item mt-4 pt-3 border-top border-light">
                            <a class="nav-link text-warning fw-bold" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

            <div class="col-md-9 ms-sm-auto col-lg-10 main-content" id="mainContent">

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
                                @yield('page-title', 'منسق التدريب')
                            </h4>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                                <small class="text-muted">منسق التدريب</small>
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

                            <div class="user-avatar pulse-animation">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="container-fluid px-4 py-4 content-area-padding">
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
        const navbarMain = document.getElementById('navbarMain'); // تم إضافة عنصر الشريط العلوي
        const toggleSidebar = document.getElementById('toggleSidebar');
        const toggleSidebarMain = document.getElementById('toggleSidebarMain');

        function toggleSidebarFunc() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbarMain.classList.toggle('expanded'); // لتعديل عرض الشريط العلوي أيضاً

            // تغيير الأيقونة
            const icon = toggleSidebar.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-left';
            } else {
                icon.className = 'fas fa-chevron-right';
            }
        }

        toggleSidebar.addEventListener('click', toggleSidebarFunc);
        toggleSidebarMain.addEventListener('click', toggleSidebarFunc);

        // إغلاق الشريط الجانبي تلقائياً على الشاشات الصغيرة
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            navbarMain.classList.add('expanded');
        }

        // إعادة الضبط عند تغيير حجم النافذة
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                navbarMain.classList.remove('expanded');
            } else {
                </div>
                            </div>
                            <div class="logo-text">مكتب تدريب الخريجين</div>
                            <div class="logo-subtext">جامعة طرابلس</div>
                        </div>
                        <small class="text-light opacity-85 mt-2 d-block">
                            منسق التدريب
                        </small>
                    </div>

                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('training-coordinator.dashboard') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
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
                            <a class="nav-link {{ request()->routeIs('training-coordinator.trainings*') && !request()->routeIs('training-coordinator.trainings.create') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.trainings') }}">
                                <i class="fas fa-graduation-cap"></i>
                                إدارة برامج التدريب
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
                                    <span class="nav-badge">{{ $pendingCount }}</span>
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
                            <a class="nav-link {{ request()->routeIs('training-coordinator.reports') ? 'active' : '' }}"
                                href="{{ route('training-coordinator.reports') }}">
                                <i class="fas fa-chart-bar"></i>
                                التقارير والإحصائيات
                            </a>
                        </li>

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
                    </ul>
                </div>
            </nav>

            <div class="col-md-9 ms-sm-auto col-lg-10 main-content" id="mainContent">

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
                                @yield('page-title', 'منسق التدريب')
                            </h4>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                                <small class="text-muted">منسق التدريب</small>
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

                            <div class="user-avatar pulse-animation">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </nav>

                <main class="container-fluid px-4 py-4 content-area-padding">
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
        const navbarMain = document.getElementById('navbarMain'); // تم إضافة عنصر الشريط العلوي
        const toggleSidebar = document.getElementById('toggleSidebar');
        const toggleSidebarMain = document.getElementById('toggleSidebarMain');

        function toggleSidebarFunc() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            navbarMain.classList.toggle('expanded'); // لتعديل عرض الشريط العلوي أيضاً

            // تغيير الأيقونة
            const icon = toggleSidebar.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fas fa-chevron-left';
            } else {
                icon.className = 'fas fa-chevron-right';
            }
        }

        toggleSidebar.addEventListener('click', toggleSidebarFunc);
        toggleSidebarMain.addEventListener('click', toggleSidebarFunc);

        // إغلاق الشريط الجانبي تلقائياً على الشاشات الصغيرة
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            navbarMain.classList.add('expanded');
        }

        // إعادة الضبط عند تغيير حجم النافذة
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                navbarMain.classList.remove('expanded');
            } else {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                navbarMain.classList.add('expanded');
            }
        });
    </script>
    <script src="{{ asset('js/notifications.js') }}"></script>
    @stack('scripts')

    @auth
        @include('components.ai-assistant-widget')
    @endauth
</body>

</html>