<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Test Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .hero-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
            overflow: hidden;
        }

        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 3rem;
            color: #3b82f6;
            margin-bottom: 20px;
        }

        .btn-custom {
            background: linear-gradient(45deg, #3b82f6, #1e3a8a);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container hero-content">
            <h1 class="display-3 fw-bold mb-4 animate-fade-in">مرحباً بك في صفحة الاختبار الجديدة</h1>
            <p class="lead mb-5 animate-fade-in" style="animation-delay: 0.2s;">هذه صفحة تجريبية تم إنشاؤها للتحقق من
                عمل النظام بشكل صحيح.</p>
            <a href="/" class="btn btn-light btn-lg rounded-pill px-5 animate-fade-in"
                style="animation-delay: 0.4s; color: #1e3a8a; font-weight: bold;">العودة للرئيسية</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card card-custom h-100 p-4 text-center">
                        <div class="card-body">
                            <i class="fas fa-rocket card-icon"></i>
                            <h3 class="h4 fw-bold mb-3">أداء عالي</h3>
                            <p class="text-muted">تصميم سريع ومتجاوب يعمل بكفاءة على جميع الأجهزة.</p>
                        </div>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card card-custom h-100 p-4 text-center">
                        <div class="card-body">
                            <i class="fas fa-paint-brush card-icon" style="color: #10b981;"></i>
                            <h3 class="h4 fw-bold mb-3">تصميم عصري</h3>
                            <p class="text-muted">واجهة مستخدم جذابة وسهلة الاستخدام تعتمد على أحدث معايير التصميم.</p>
                        </div>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card card-custom h-100 p-4 text-center">
                        <div class="card-body">
                            <i class="fas fa-shield-alt card-icon" style="color: #f59e0b;"></i>
                            <h3 class="h4 fw-bold mb-3">آمن وموثوق</h3>
                            <p class="text-muted">بنية تحتية قوية تضمن حماية البيانات واستقرار النظام.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} نظام تدريب الخريجين - جامعة طرابلس. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>