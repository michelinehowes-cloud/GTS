@extends('layouts.app')

@section('title', 'لوحة تحكم منسق التدريب')
@section('page-title', 'لوحة تحكم منسق التدريب')

@section('content')



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة برامج التدريب - منسق التدريب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --primary-dark: #1e40af;
            --primary-medium: #3b82f6;
            --primary-light: #60a5fa;
            --accent-gold: #d4af37;
            --accent-light: #fbbf24;
            --university-blue: #1e3a8a;
            --university-gold: #d4af37;
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
            overflow-x: hidden;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--university-blue) 0%, #1e3a8a 100%);
            min-height: 100vh;
            color: var(--white);
            position: fixed;
            width: 280px;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
            right: 0;
            top: 0;
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
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
        
        .main-content {
            margin-right: 280px;
            padding: 0;
            min-height: 100vh;
            transition: all 0.3s ease;
            width: calc(100% - 280px);
        }
        
        .main-content.expanded {
            margin-right: 0;
            width: 100%;
        }
        
        .navbar-main {
            background: var(--white);
            box-shadow: 0 3px 20px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--university-gold);
            padding: 12px 0;
            backdrop-filter: blur(10px);
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
        
        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-right: 4px solid var(--university-gold);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--white) 0%, #f0f4f8 100%);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 20px 25px;
            border-radius: 18px 18px 0 0 !important;
        }
        
        .card-title {
            color: var(--university-blue);
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 0;
        }
        
        .stat-card {
            border-right: 4px solid var(--university-gold);
            background: linear-gradient(135deg, var(--white) 0%, #f8fafe 100%);
            position: relative;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--university-blue), var(--university-gold));
        }
        
        .stat-card .card-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--university-blue), var(--primary-dark));
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
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
        
        .btn-primary {
            background: linear-gradient(135deg, var(--university-blue), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(30, 58, 138, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.4);
            background: linear-gradient(135deg, var(--primary-dark), #1e3a8a);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                transform: translateX(100%);
                opacity: 0;
            }
            
            .sidebar:not(.collapsed) {
                transform: translateX(0);
                opacity: 1;
                width: 280px;
            }
            
            .sidebar.collapsed .sidebar-content {
                opacity: 0;
            }
            
            .sidebar:not(.collapsed) .sidebar-content {
                opacity: 1;
            }
            
            .main-content {
                margin-right: 0;
                width: 100%;
            }
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- الشريط الجانبي -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="position-sticky sidebar-content">
                    <div class="sidebar-header">
                        <button class="toggle-sidebar" id="toggleSidebar">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="logo-container">
                            <!-- اللوقو في الشريط الجانبي -->
                            <div class="logo-img-placeholder university-logo d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; border-radius: 10px; background: white; margin-bottom: 10px;">
                                <img src="http://localhost:8000/storage/logo.png" alt="شعار مكتب تدريب الخريجين" class="logo-img" 
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="d-none align-items-center justify-content-center w-100 h-100">
                                    <i class="fas fa-graduation-cap" style="font-size: 2rem; color: #1e3a8a;"></i>
                                </div>
                            </div>
                            <div class="logo-text">مكتب تدريب الخريجين</div>
                            <div class="logo-subtext">جامعة طرابلس</div>
                        </div>
                        <small class="text-light opacity-85 mt-2 d-block">
                            منسق التدريب
                        </small>
                    </div>

                </div>
            </nav>

            <!-- المحتوى الرئيسي -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content" id="mainContent">
                <!-- شريط التنقل العلوي -->
                <nav class="navbar navbar-expand-lg navbar-main">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center">
                            <button class="toggle-sidebar-main" id="toggleSidebarMain">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h4 class="navbar-brand mb-0 ms-3">
                                <!-- اللوقو في الشريط العلوي -->
                                <img src="http://localhost:8000/storage/logo.png" alt="شعار مكتب تدريب الخريجين" 
                                     style="height: 40px; margin-left: 10px; display: inline-block;"
                                     onerror="this.style.display='none'">
                                <i class="fas fa-graduation-cap me-2"></i>
                                إدارة برامج التدريب - منسق التدريب
                            </h4>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                                <small class="text-muted">منسق التدريب</small>
                            </div>
                            <div class="user-avatar pulse-animation">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- محتوى الصفحة -->
                <div class="container-fluid px-4 py-4">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3>برامج التدريب الخاصة بي</h3>
                                        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>إضافة برنامج تدريب جديد
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <!-- إحصائيات سريعة -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="card stat-card">
                                                    <div class="card-body text-center">
                                                        <div class="card-icon mx-auto mb-3">
                                                            <i class="fas fa-graduation-cap"></i>
                                                        </div>
                                                        <h4>{{ $myTrainingsCount ?? 0 }}</h4>
                                                        <h6>إجمالي برامج التدريب</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card stat-card">
                                                    <div class="card-body text-center">
                                                        <div class="card-icon mx-auto mb-3" style="background: linear-gradient(135deg, var(--success), #059669);">
                                                            <i class="fas fa-play-circle"></i>
                                                        </div>
                                                        <h4>{{ $activeTrainingsCount ?? 0 }}</h4>
                                                        <h6>البرامج النشطة</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card stat-card">
                                                    <div class="card-body text-center">
                                                        <div class="card-icon mx-auto mb-3" style="background: linear-gradient(135deg, var(--warning), #d97706);">
                                                            <i class="fas fa-pause-circle"></i>
                                                        </div>
                                                        <h4>{{ ($myTrainingsCount ?? 0) - ($activeTrainingsCount ?? 0) }}</h4>
                                                        <h6>البرامج المتوقفة</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card stat-card">
                                                    <div class="card-body text-center">
                                                        <div class="card-icon mx-auto mb-3" style="background: linear-gradient(135deg, var(--info), #0e7490);">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                        <h4>0</h4>
                                                        <h6>المتدربين المسجلين</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(session('success'))
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @php
                                            $myTrainings = \App\Models\Training::where('coordinator_id', auth()->id())->get();
                                        @endphp
                                        
                                        @if($myTrainings->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>اسم البرنامج</th>
                                                            <th>النوع</th>
                                                            <th>المدة</th>
                                                            <th>تاريخ البدء</th>
                                                            <th>المقاعد</th>
                                                            <th>الحالة</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($myTrainings as $training)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <strong>{{ $training->title }}</strong>
                                                                <br><small class="text-muted">{{ $training->description }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-warning">{{ $training->type }}</span>
                                                            </td>
                                                            <td>{{ $training->duration }}</td>
                                                            <td>{{ $training->start_date }}</td>
                                                            <td>{{ $training->seats }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $training->status == 'active' ? 'success' : 'warning' }}">
                                                                    {{ $training->status == 'active' ? 'نشط' : 'متوقف' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.trainings.show', $training->id) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.trainings.edit', $training->id) }}" class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('admin.trainings.destroy', $training->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا البرنامج؟')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                                                <h4 class="text-muted">لا توجد برامج تدريب حتى الآن</h4>
                                                <p class="text-muted mb-4">يمكنك البدء بإضافة أول برنامج تدريب</p>
                                                <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-plus me-2"></i>إضافة أول برنامج تدريب
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // التحكم في إظهار/إخفاء الشريط الجانبي
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const toggleSidebarMain = document.getElementById('toggleSidebarMain');

        function toggleSidebarFunc() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
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
        }

        // إعادة الضبط عند تغيير حجم النافذة
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            } else {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        });
    </script>
</body>
</html>
@endsection