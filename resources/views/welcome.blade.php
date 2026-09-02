<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام تدريب وتوظيف الخريجين - جامعة طرابلس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* الألوان الأساسية - اللون الأزرق الجديد المتناسق */
        :root {
            --primary-blue: #0d3882;
            --secondary-blue: #1565c0;
            --accent-blue: #1e88e5;
            --gold-accent: #FFC300;
            --light-bg: #F8F9FA;
            --dark-bg: #0b1f3a;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Cairo', sans-serif;
            /* استخدام خط القاهرة */
        }

        /* تحسينات الشريط العلوي والفقرات */
        .display-4,
        .lead {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* قسم البطل الفخم بالتدرج الأزرق الجديد */
        .hero-section {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 110px 0;
            text-align: center;
            box-shadow: 0 10px 30px rgba(13, 56, 130, 0.25);
            position: relative;
            overflow: hidden;
        }

        /* نافذة تسجيل الدخول المنبثقة الحديثة */
        .modal-login-content {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(13, 56, 130, 0.35);
        }
        .modal-login-header {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 2.2rem 2rem 1.8rem;
            position: relative;
            text-align: center;
        }
        .modal-login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }
        .modal-login-logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: white;
            padding: 4px;
            border: 3px solid #FFC300;
            margin: 0 auto 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            object-fit: cover;
        }
        .modal-login-body {
            padding: 2rem 2.2rem;
            background: #ffffff;
        }
        .modal-login-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .modal-login-input:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 4px rgba(21, 101, 192, 0.12);
        }
        .btn-modal-login {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.85rem;
            font-weight: 700;
            font-size: 1.05rem;
            transition: all 0.25s ease;
        }
        .btn-modal-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.35);
            color: #ffffff;
        }
            position: relative;
            overflow: hidden;
        }

        /* تراكب خفيف بنمط (Pattern Overlay) لزيادة الفخامة */
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.05) 0%, rgba(0, 0, 0, 0.1) 100%);
            opacity: 1;
            z-index: 1;
        }

        .hero-section>.container {
            position: relative;
            z-index: 2;
            /* تأكد من أن المحتوى فوق التراكب */
        }

        /* زر تسجيل الدخول الفخم */
        .btn-gold {
            background-color: var(--gold-accent);
            border-color: var(--gold-accent);
            color: var(--primary-blue);
            font-weight: bold;
            padding: 12px 35px;
            border-radius: 50px;
            /* زر دائري */
            transition: all 0.3s ease;
            /* ظل ثلاثي الأبعاد للزر */
            box-shadow: 0 5px 15px rgba(255, 195, 0, 0.4);
        }

        .btn-gold:hover {
            background-color: #FFD740;
            border-color: #FFD740;
            color: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 195, 0, 0.6);
        }

        /* شعار الجامعة المحسّن */
        .university-logo-img {
            width: 150px;
            height: 150px;
            border: 5px solid var(--gold-accent);
            border-radius: 50%;
            object-fit: cover;
            /* تأثير لمعان خفيف */
            filter: drop-shadow(0 0 10px rgba(255, 195, 0, 0.6));
        }

        /* بطاقات المميزات الفاخرة */
        .feature-card {
            border: none;
            border-radius: 20px;
            padding: 30px;
            background: white;
            /* ظل عميق يرفع البطاقة عن الخلفية */
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--secondary-blue), var(--gold-accent));
        }

        .feature-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
        }

        .feature-icon-circle {
            background-color: var(--secondary-blue);
            color: var(--gold-accent);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            /* ظل خفيف للأيقونة */
            box-shadow: 0 5px 15px rgba(20, 66, 114, 0.4);
        }

        /* تحسين التذييل */
        .footer {
            background-color: var(--dark-bg);
            color: var(--light-bg);
            padding: 40px 0;
            border-top: 3px solid var(--gold-accent);
        }

        .footer p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* تحسينات بطاقات التدريبات والإعلانات */
        .ad-card,
        .news-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .ad-card:hover,
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .ad-card .card-header {
            background: linear-gradient(45deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            font-weight: bold;
            border-bottom: none;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .ad-card .card-header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            transition: all 0.5s ease;
        }

        .ad-card:hover .card-header::before {
            transform: rotate(0deg);
            opacity: 0;
        }

        .ad-card .card-body {
            padding: 1.5rem;
            background-color: white;
        }

        .ad-card .list-group-item {
            border-color: #eee;
            font-size: 0.95rem;
        }

        .ad-card .btn-gold {
            background-color: var(--gold-accent);
            border-color: var(--gold-accent);
            color: var(--primary-blue);
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .ad-card .btn-gold:hover {
            background-color: #FFD740;
            border-color: #FFD740;
            color: var(--secondary-blue);
            transform: translateY(-1px);
        }

        .news-card .card-body {
            padding: 1.5rem;
        }

        .news-card .card-title {
            color: var(--primary-blue);
            font-weight: bold;
        }

        .announcement-card {
            border: 2px solid var(--gold-accent);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(255, 195, 0, 0.2);
        }

        .announcement-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 195, 0, 0.3);
        }

        .announcement-card .card-title {
            color: var(--secondary-blue);
            font-weight: bold;
        }

        .announcement-card .btn-warning {
            background-color: var(--gold-accent);
            border-color: var(--gold-accent);
            color: var(--primary-blue);
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .announcement-card .btn-warning:hover {
            background-color: #FFD740;
            border-color: #FFD740;
            color: var(--secondary-blue);
        }
    </style>
</head>

<body>

    <section class="hero-section">
        <div class="container">
            <img src="{{ asset('storage/logo.jpg') }}" alt="شعار الجامعة" class="university-logo-img mb-5"
                data-aos="zoom-in">

            <h1 class="display-3 fw-bolder mb-3" data-aos="fade-up" data-aos-delay="200">مكتب تدريب الخريجين</h1>

            <div class="d-flex justify-content-center gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="400">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg shadow-sm">
                        <i class="fas fa-th-large me-2"></i>لوحة التحكم
                    </a>
                @else
                    <button type="button" class="btn btn-gold btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                    </button>
                    <a href="{{ route('graduate.register') }}" class="btn btn-outline-light btn-lg fw-bold">
                        <i class="fas fa-user-plus me-2"></i>تسجيل خريج جديد
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- قسم الصور الترحيبية (Carousel) -->
    @php
        $totalTrainings = $advertisedTrainings->count();
    @endphp

    @if($welcomeImages->count() > 0)
        <section class="py-5">
            <div class="container">
                <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 overflow-hidden shadow-lg">
                        @foreach($welcomeImages as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image->file_path) }}" class="d-block w-100"
                                    alt="{{ $image->caption ?? 'صورة ترحيبية' }}" style="height: 400px; object-fit: cover;">
                                @if($image->caption)
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5 class="text-white shadow-sm">{{ $image->caption }}</h5>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($welcomeImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">التالي</span>
                        </button>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if($advertisedTrainings->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center display-5 fw-bold mb-5 text-primary-blue" data-aos="fade-down">تدريبات مميزة
                </h2>
                <div class="row">
                    @foreach($advertisedTrainings as $index => $training)
                        <div class="col-lg-4 col-md-6 mb-4 training-card {{ $index >= 3 ? 'hidden-training' : '' }}"
                            data-aos="fade-up" style="{{ $index >= 3 ? 'display: none;' : '' }}">
                            <div class="ad-card h-100">
                                <div class="card-header text-center py-3">
                                    <h5 class="mb-0">{{ $training->title }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text text-muted">{{ Str::limit($training->description, 150) }}</p>
                                    <ul class="list-group list-group-flush mb-3">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <i class="fas fa-calendar-alt me-2 text-gold-accent"></i> تاريخ البدء:
                                            <span
                                                class="badge bg-secondary-blue">{{ \Carbon\Carbon::parse($training->start_date)->format('d/m/Y') }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <i class="fas fa-clock me-2 text-gold-accent"></i> المدة:
                                            <span class="badge bg-secondary-blue">{{ $training->duration }} أيام</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <i class="fas fa-map-marker-alt me-2 text-gold-accent"></i> المكان:
                                            <span class="badge bg-secondary-blue">{{ $training->location }}</span>
                                        </li>
                                    </ul>
                                    <a href="#" class="btn btn-gold w-100 mt-2">
                                        <i class="fas fa-info-circle me-2"></i> تفاصيل التدريب
                                    </a>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    @if($totalTrainings > 3)
                        <div class="text-center mt-4">
                            <button id="showMoreBtn" class="btn btn-gold btn-lg" onclick="showMoreTrainings()">
                                <span id="btnText">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    عرض المزيد من التدريبات ({{ $totalTrainings - 3 }} تدريب إضافي)
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if($latestNews->count() > 0 || $activeAnnouncements->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <!-- الأخبار -->
                    @if($latestNews->count() > 0)
                        <div class="col-lg-6 mb-4">
                            <h3 class="text-primary mb-4">
                                <i class="fas fa-newspaper me-2"></i>
                                آخر الأخبار
                            </h3>
                            <div class="row">
                                @foreach($latestNews as $news)
                                    <div class="col-12 mb-3">
                                        <div class="news-card h-100">
                                            @if($news->thumbnail_path)
                                                <img src="{{ asset('storage/' . $news->thumbnail_path) }}" class="card-img-top"
                                                    alt="{{ $news->title }}" style="height: 200px; object-fit: cover;">
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $news->title }}</h6>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit(strip_tags($news->content), 100) }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ \Carbon\Carbon::parse($news->published_at)->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- الإعلانات -->
                    @if($activeAnnouncements->count() > 0)
                        <div class="col-lg-6 mb-4">
                            <h3 class="text-warning mb-4">
                                <i class="fas fa-bullhorn me-2"></i>
                                الإعلانات
                            </h3>
                            <div class="row">
                                @foreach($activeAnnouncements as $announcement)
                                    <div class="col-12 mb-3">
                                        <div class="announcement-card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $announcement->title }}</h6>
                                                <p class="card-text text-muted">
                                                    {{ Str::limit(strip_tags($announcement->content), 120) }}
                                                </p>
                                                @if($announcement->link)
                                                    <a href="{{ $announcement->link }}" class="btn btn-warning btn-sm mt-3 w-100"
                                                        target="_blank">
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        المزيد
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <!-- قسم "لماذا نحن؟" -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5 text-primary-blue" data-aos="fade-down">أهداف مكتبنا
            </h2>
            <div class="row text-center">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-4 border rounded-3 shadow-sm h-100">
                        <div class="feature-icon-circle mb-3 mx-auto">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-secondary-blue">تطوير مهني مستمر</h5>
                        <p class="text-muted">نقدم برامج تدريبية متطورة تواكب أحدث متطلبات سوق العمل.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-4 border rounded-3 shadow-sm h-100">
                        <div class="feature-icon-circle mb-3 mx-auto">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-secondary-blue">شراكات استراتيجية</h5>
                        <p class="text-muted">لإدماج الخريجين بأفضل الشركات والمؤسسات للتدريب.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-4 border rounded-3 shadow-sm h-100">
                        <div class="feature-icon-circle mb-3 mx-auto">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h5 class="fw-bold text-secondary-blue">متابعة وتقييم الأداء</h5>
                        <p class="text-muted">نظام متكامل لمتابعة تقدمك المهني وتقييم أدائك بانتظام.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <section class="py-6 py-xl-8">
        <div class="container">

            <div class="row justify-content-center">

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon-circle mb-4">
                                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                            </div>
                            <h4 class="card-title fw-bold text-secondary-blue">برامج تدريب النخبة</h4>
                            <p class="card-text text-muted">مسارات تدريبية مكثفة مصممة بالشراكة مع قادة الصناعة
                                لصقل
                                المهارات العملية.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon-circle mb-4">
                                <i class="fas fa-award fa-2x"></i>
                            </div>
                            <h4 class="card-title fw-bold text-secondary-blue">فرص عمل مرموقة</h4>
                            <p class="card-text text-muted">وصول حصري لأفضل فرص التوظيف في كبرى الشركات
                                والمؤسسات
                                الوطنية.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="feature-icon-circle mb-4">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </div>
                            <h4 class="card-title fw-bold text-secondary-blue">منصة آمنة وموثوقة</h4>
                            <p class="card-text text-muted">نظام متكامل لمتابعة وتقييم التقدم المهني للخريجين
                                بشكل مستمر
                                وفعال.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <!-- نافذة تسجيل الدخول المنبثقة الجميلة (Login Modal) -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('storage/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                    <h4 class="fw-bold mb-1" id="loginModalLabel">تسجيل الدخول</h4>
                    <p class="mb-0 text-white-50 small">مكتب تدريب وتأهيل الخريجين — جامعة طرابلس</p>
                </div>
                <div class="modal-login-body">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-exclamation-circle flex-shrink-0 fs-6 text-danger"></i>
                            <span class="fw-semibold">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="modalLoginForm">
                        @csrf
                        <div class="mb-3 text-start text-rtl">
                            <label for="modalEmail" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-envelope text-primary me-1"></i>البريد الإلكتروني
                            </label>
                            <input type="email" class="form-control modal-login-input @error('email') is-invalid @enderror" id="modalEmail" name="email" value="{{ old('email') }}" placeholder="example@uot.edu.ly" required autofocus>
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="modalPassword" class="form-label fw-bold text-dark small mb-0">
                                    <i class="fas fa-lock text-primary me-1"></i>كلمة المرور
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="small text-decoration-none fw-bold" href="{{ route('password.request') }}" style="color: #1565c0; font-size: 0.8rem;">
                                        نسيت كلمة المرور؟
                                    </a>
                                @endif
                            </div>
                            <div class="position-relative">
                                <input type="password" class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror" id="modalPassword" name="password" placeholder="••••••••" required>
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleModalPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleModalPass"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-4 text-start text-rtl">
                            <input class="form-check-input" type="checkbox" name="remember" id="modalRemember">
                            <label class="form-check-label text-muted small" for="modalRemember">
                                تذكر بياناتي على هذا الجهاز
                            </label>
                        </div>

                        <button type="submit" class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-sign-in-alt"></i><span>دخول إلى النظام</span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-0">
                            خريج جديد ولم تسجل بعد؟
                            <a href="{{ route('graduate.register') }}" class="fw-bold text-decoration-none ms-1" style="color: #1565c0;">
                                تسجيل خريج جديد <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <p><strong>جامعة طرابلس</strong> - مكتب تدريب الخريجين</p>
            <p class="text-gold-accent mb-0">© 2025 جميع الحقوق محفوظة. لمكتب تدريب الخريجين.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
        });

        // تفعيل إظهار وإخفاء كلمة المرور في نافذة تسجيل الدخول
        document.addEventListener('DOMContentLoaded', function () {
            const btnToggle = document.getElementById('btnToggleModalPass');
            const inputPass = document.getElementById('modalPassword');
            const iconToggle = document.getElementById('iconToggleModalPass');
            if (btnToggle && inputPass && iconToggle) {
                btnToggle.addEventListener('click', function () {
                    const isPass = inputPass.type === 'password';
                    inputPass.type = isPass ? 'text' : 'password';
                    iconToggle.className = isPass ? 'far fa-eye-slash' : 'far fa-eye';
                });
            }

            // الفتح التلقائي لنافذة تسجيل الدخول عند وجود أخطاء أو طلب الرابط
            const urlParams = new URLSearchParams(window.location.search);
            const shouldOpen = urlParams.has('open_login') || urlParams.has('login');
            @if($errors->any())
                const hasErrors = true;
            @else
                const hasErrors = false;
            @endif

            if (shouldOpen || hasErrors) {
                const loginModalEl = document.getElementById('loginModal');
                if (loginModalEl) {
                    const loginModal = new bootstrap.Modal(loginModalEl);
                    loginModal.show();
                }
            }
        });
        // دالة لعرض/إخفاء التدريبات الإضافية
        let trainingsExpanded = false;

        function showMoreTrainings() {
            const hiddenTrainings = document.querySelectorAll('.hidden-training');
            const btnText = document.getElementById('btnText');

            if (!trainingsExpanded) {
                // عرض التدريبات المخفية
                hiddenTrainings.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.display = 'block';
                        // إضافة تأثير الظهور التدريجي
                        setTimeout(() => {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            card.style.transition = 'all 0.5s ease';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 10);
                        }, 10);
                    }, index * 100);
                });

                btnText.innerHTML = '<i class="fas fa-minus-circle me-2"></i>إخفاء التدريبات الإضافية';
                trainingsExpanded = true;
            } else {
                // إخفاء التدريبات
                hiddenTrainings.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 500);
                    }, index * 50);
                });

                const totalTrainings = {{ $totalTrainings ?? 0 }};
                const hiddenCount = totalTrainings - 3;
                btnText.innerHTML = `<i class="fas fa-plus-circle me-2"></i>عرض المزيد من التدريبات (${hiddenCount} تدريب إضافي)`;
                trainingsExpanded = false;

                // التمرير إلى قسم التدريبات
                setTimeout(() => {
                    const firstCard = document.querySelector('.training-card');
                    if (firstCard) {
                        firstCard.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);
            }
        }
    </script>
</body>

</html>