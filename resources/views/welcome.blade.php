<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام تدريب وتوظيف الخريجين - جامعة طرابلس</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
            font-family: 'GE SS Unique Bold', Tahoma, sans-serif; /* يمكن استبداله بخط عربي فخم */
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
            <img src="{{ asset('storage/logo.jpg') }}" alt="شعار الجامعة" class="university-logo-img mb-5">

            <h1 class="display-3 fw-bolder mb-3">مكتب تدريب الخريجين</h1>
            
            <a href="{{ route('login') }}" class="btn btn-gold btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول   
            </a>
        </div>
    </section>

    <hr>

    <section class="py-6 py-xl-8">
        <div class="container">
            
            <div class="row justify-content-center">
                
                <div class="col-lg-4 col-md-6 mb-4">
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
                
                <div class="col-lg-4 col-md-6 mb-4">
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
                
                <div class="col-lg-4 col-md-6 mb-4">
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

    <hr>

    <footer class="footer">
        <div class="container text-center">
            <p><strong>جامعة طرابلس</strong> - مكتب تدريب الخريجين</p>
            <p class="text-gold-accent mb-0">© 2025 جميع الحقوق محفوظة.  لمكتب تدريب الخريجين.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
