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
        /* الألوان الأساسية */
        :root {
            --primary-blue: #0A2647; /* أزرق داكن جداً وفخم */
            --secondary-blue: #144272; /* أزرق ثانوي عميق */
            --gold-accent: #FFC300; /* ذهبي ملكي */
            --light-bg: #F8F9FA;
            --dark-bg: #0A1C30;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Cairo', sans-serif; /* استخدام خط القاهرة */
        }

        /* تحسينات الشريط العلوي والفقرات */
        .display-4, .lead {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* قسم البطل الفخم */
        .hero-section {
            /* تدرج لوني عمودي فاخر مع لمعة خفيفة */
            background: linear-gradient(160deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 120px 0;
            text-align: center;
            /* إضافة ظل سفلي لجعل القسم يبرز */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
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
        
        .hero-section > .container {
            position: relative;
            z-index: 2; /* تأكد من أن المحتوى فوق التراكب */
        }
        
        /* زر تسجيل الدخول الفخم */
        .btn-gold {
            background-color: var(--gold-accent);
            border-color: var(--gold-accent);
            color: var(--primary-blue);
            font-weight: bold;
            padding: 12px 35px;
            border-radius: 50px; /* زر دائري */
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
    </style>
</head>
<body>
    
    <section class="hero-section">
        <div class="container">
            <img src="{{ asset('storage/logo.jpg') }}" alt="شعار الجامعة" class="university-logo-img mb-5" data-aos="zoom-in">

            <h1 class="display-3 fw-bolder mb-3" data-aos="fade-up" data-aos-delay="200">مكتب تدريب الخريجين</h1>
            
            <a href="{{ route('login') }}" class="btn btn-gold btn-lg" data-aos="fade-up" data-aos-delay="400">
                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول   
            </a>
        </div>
    </section>

    <!-- قسم الصور الترحيبية (Carousel) -->
    @php
        $welcomeImages = \App\Models\TrainingMedia::where('is_welcome_page_media', true)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
    @endphp

    @if($welcomeImages->count() > 0)
    <section class="py-5">
        <div class="container">
            <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-3 overflow-hidden shadow-lg">
                    @foreach($welcomeImages as $index => $image)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image->file_path) }}" class="d-block w-100" alt="{{ $image->caption ?? 'صورة ترحيبية' }}" style="height: 400px; object-fit: cover;">
                        @if($image->caption)
                        <div class="carousel-caption d-none d-md-block">
                            <h5 class="text-white shadow-sm">{{ $image->caption }}</h5>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if($welcomeImages->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">السابق</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">التالي</span>
                </button>
                @endif
            </div>
        </div>
    </section>
    @endif

    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <!-- قسم "لماذا نحن؟" -->
    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5 text-primary-blue" data-aos="fade-down">لماذا تختار نظامنا؟</h2>
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
                        <p class="text-muted">نربطك بأفضل الشركات والمؤسسات لفرص توظيف حصرية.</p>
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

    <!-- قسم شهادات الخريجين/الشركاء -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center display-5 fw-bold mb-5 text-primary-blue" data-aos="fade-down">ماذا يقولون عنا؟</h2>
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active" data-aos="fade-up">
                        <div class="d-flex justify-content-center">
                            <div class="card shadow-lg p-4 mx-2" style="max-width: 700px;">
                                <div class="card-body text-center">
                                    <img src="https://via.placeholder.com/80" class="rounded-circle mb-3 border border-3 border-gold-accent" alt="صورة خريج" style="width: 80px; height: 80px; object-fit: cover;">
                                    <p class="lead fst-italic">"بفضل هذا النظام، حصلت على تدريب ممتاز وفرصة عمل في شركة رائدة. لقد غير مساري المهني بالكامل!"</p>
                                    <footer class="blockquote-footer mt-3">أحمد الساعدي <cite title="Source Title">خريج هندسة برمجيات</cite></footer>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item" data-aos="fade-up">
                        <div class="d-flex justify-content-center">
                            <div class="card shadow-lg p-4 mx-2" style="max-width: 700px;">
                                <div class="card-body text-center">
                                    <img src="https://via.placeholder.com/80" class="rounded-circle mb-3 border border-3 border-gold-accent" alt="صورة شريك" style="width: 80px; height: 80px; object-fit: cover;">
                                    <p class="lead fst-italic">"نظام تدريب الخريجين يوفر لنا كفاءات ممتازة ومدربة جاهزة للانخراط في سوق العمل. شراكة ناجحة بكل المقاييس."</p>
                                    <footer class="blockquote-footer mt-3">فاطمة الزهراء <cite title="Source Title">مديرة الموارد البشرية، شركة التقنية المتقدمة</cite></footer>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">السابق</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">التالي</span>
                </button>
            </div>
        </div>
    </section>

    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <!-- قسم الأخبار والإعلانات -->
    @php
        $latestNews = \App\Models\News::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $activeAnnouncements = \App\Models\Announcement::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    @endphp

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
                            <div class="card border-0 shadow-sm">
                                @if($news->thumbnail_path)
                                <img src="{{ asset('storage/' . $news->thumbnail_path) }}" class="card-img-top" alt="{{ $news->title }}" style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title text-primary">{{ $news->title }}</h6>
                                    <p class="card-text text-muted small">{{ Str::limit(strip_tags($news->content), 100) }}</p>
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
                            <div class="card border-warning shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-warning">{{ $announcement->title }}</h6>
                                    <p class="card-text">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>
                                    @if($announcement->link)
                                    <a href="{{ $announcement->link }}" class="btn btn-warning btn-sm" target="_blank">
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
                            <p class="card-text text-muted">مسارات تدريبية مكثفة مصممة بالشراكة مع قادة الصناعة لصقل المهارات العملية.</p>
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
                            <p class="card-text text-muted">وصول حصري لأفضل فرص التوظيف في كبرى الشركات والمؤسسات الوطنية.</p>
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
                            <p class="card-text text-muted">نظام متكامل لمتابعة وتقييم التقدم المهني للخريجين بشكل مستمر وفعال.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <hr class="my-5 border-gold-accent"> <!-- Enhanced separator -->

    <footer class="footer">
        <div class="container text-center">
            <p><strong>جامعة طرابلس</strong> - مكتب تدريب الخريجين</p>
            <p class="text-gold-accent mb-0">© 2025 جميع الحقوق محفوظة.  لمكتب تدريب الخريجين.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000, // values from 0 to 3000, with step 50ms
            once: true, // whether animation should happen only once - while scrolling down
        });
    </script>
</body>
</html>
