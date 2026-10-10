<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'بوابة الشركاء والتوظيف - جامعة طرابلس')</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/premium-forms.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-navy: #1e3a8a;
            --primary-blue: #0284c7;
            --primary-gold: #f59e0b;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        .guest-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .guest-footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
    </style>

    @stack('styles')
    @yield('styles')
</head>
<body>
    <!-- شريط تنقل خفيف للزوار -->
    <nav class="navbar guest-navbar py-2.5">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark">
                <img src="{{ asset('storage/logo.jpg') }}" alt="شعار جامعة طرابلس" class="rounded-circle shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                <div>
                    <h6 class="mb-0 fw-bold" style="color: var(--primary-navy); font-size: 1.05rem;">جامعة طرابلس</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">مكتب تدريب الخريجين</small>
                </div>
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5">
                    <i class="fas fa-arrow-right"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="background: var(--primary-blue); border-color: var(--primary-blue);">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>تسجيل الدخول</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- محتوى الصفحة -->
    <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- تذييل الصفحة -->
    <footer class="guest-footer">
        <div class="container">
            <p class="mb-1 fw-bold text-dark">منظومة متابعة وتدريب الخريجين والربط مع سوق العمل</p>
            <p class="mb-0 text-muted small">جميع الحقوق محفوظة &copy; {{ date('Y') }} — جامعة طرابلس، ليبيا</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
