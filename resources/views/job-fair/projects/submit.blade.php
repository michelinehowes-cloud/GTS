<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>نموذج تقديم مشروع تخرج — {{ $fair ? $fair->title : 'معرض التوظيف جامعة طرابلس' }}</title>
    <meta name="description" content="نموذج التقديم والمشاركة بمشاريع تخرج طلبة وخريجي كليات جامعة طرابلس في المعرض السنوي وأرشيف الابتكارات.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:      #eeca3e;
            --gold-lt:   #FDE68A;
            --navy:      #045db0;
            --navy-md:   #3b82f6;
            --navy-lt:   #60a5fa;
            --navy-dark: #092347;
            --teal:      #0EA5E9;
            --green:     #10B981;
            --white:     #FFFFFF;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--navy);
            color: #ffffff;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Animated Background */
        .hero-bg-layer {
            position: fixed; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(245,158,11,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 70% 80% at 80% 20%, rgba(14,165,233,0.22) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16,185,129,0.14) 0%, transparent 50%),
                linear-gradient(160deg, #045db0 0%, #03488a 50%, #092347 100%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        .page-content-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Navbar */
        .top-nav {
            position: sticky;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(3, 40, 80, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .nav-brand img.main-logo {
            width: 42px; height: 42px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            object-fit: cover;
        }

        .nav-brand img.jf-logo {
            width: auto; height: 38px;
            margin-right: 15px;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
        }

        .nav-brand-text { line-height: 1.2; }
        .nav-brand-text .main { color: white; font-weight: 700; font-size: 0.95rem; }
        .nav-brand-text .sub  { color: var(--gold); font-size: 0.72rem; }

        .nav-brand-divider {
            width: 1px;
            height: 30px;
            background: rgba(255, 255, 255, 0.22);
            margin: 0 10px;
            flex-shrink: 0;
        }

        .waha-nav-logo {
            height: 36px;
            width: auto;
            max-width: 120px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .nav-btn {
            padding: 8px 18px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 0.86rem;
            text-decoration: none;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-btn-outline {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.28);
            color: white;
        }

        .nav-btn-outline:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: var(--gold);
            color: var(--gold);
        }

        /* Form Header */
        .form-header-card {
            background: rgba(8, 34, 69, 0.75);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 35px 30px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            margin: 35px 0 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #eeca3e, #10b981);
        }

        .form-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 30px;
            background: rgba(238, 202, 62, 0.18);
            border: 1.5px solid rgba(238, 202, 62, 0.45);
            color: var(--gold-lt);
            font-weight: 800;
            font-size: 0.9rem;
            margin-bottom: 14px;
        }

        .form-header-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .form-header-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Section Container Cards */
        .form-section-card {
            background: rgba(8, 34, 69, 0.75);
            border: 1.5px solid rgba(255, 255, 255, 0.13);
            border-radius: 20px;
            padding: 28px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .form-section-card:hover {
            border-color: rgba(238, 202, 62, 0.35);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .section-icon-wrap {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .section-icon-admin {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.4);
            color: #f87171;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .section-desc {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
        }

        /* Form Controls */
        .form-label {
            font-weight: 700;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label .req { color: #f87171; margin-right: 4px; }
        .form-label .hint { font-size: 0.78rem; font-weight: normal; color: rgba(255, 255, 255, 0.6); }

        .form-control, .form-select {
            background: rgba(4, 25, 55, 0.85);
            border: 1.5px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Cairo', sans-serif;
            font-size: 0.95rem;
            padding: 10px 14px;
            transition: all 0.25s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(4, 25, 55, 0.95);
            border-color: var(--gold);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(238, 202, 62, 0.2);
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-select option {
            background: #092347;
            color: #ffffff;
        }

        .admin-box-wrap {
            border: 1.5px solid rgba(239, 68, 68, 0.4);
            background: rgba(45, 14, 28, 0.35);
        }

        .admin-banner-notice {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fca5a5;
            font-size: 0.88rem;
        }

        /* Submit Button */
        .btn-submit-main {
            background: linear-gradient(135deg, var(--gold) 0%, #f59e0b 100%);
            border: none;
            color: #092347;
            border-radius: 14px;
            padding: 16px 36px;
            font-weight: 900;
            font-size: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
            width: 100%;
        }

        .btn-submit-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.5);
            color: #06172d;
        }

        /* Footer */
        .page-footer {
            margin-top: auto;
            background: rgba(4, 30, 60, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
            text-align: center;
        }

        @media (max-width: 768px) {
            .form-header-title { font-size: 1.6rem; }
            .top-nav { padding: 0.75rem 1rem; }
            .form-section-card { padding: 20px 16px; }
        }
    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="hero-bg-layer"></div>
    <div class="hero-grid"></div>

    <div class="page-content-wrapper">

        <!-- Top Navigation -->
        <nav class="top-nav">
            <a href="{{ $fair ? route('job-fair.public', $fair->id) : route('home') }}" class="nav-brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="main-logo" onerror="this.style.display='none'">
                <div class="nav-brand-text">
                    <div class="main">مكتب تدريب الخريجين</div>
                    <div class="sub">جامعة طرابلس</div>
                </div>
                @if($fair)
                    <img src="{{ $fair->white_logo_url }}" class="jf-logo" alt="{{ $fair->title }}" onerror="this.onerror=null;this.src='{{ $fair->horizontal_logo_url }}';">
                @else
                    <img src="{{ asset('images/job_fair_logo_white.png') }}" class="jf-logo" alt="شعار الفعالية" onerror="this.style.display='none'">
                @endif

                <div class="nav-brand-divider d-none d-sm-block"></div>

                <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" class="waha-nav-logo d-none d-sm-block" title="شركة الواحة لتنظيم المعارض والمؤتمرات — الراعي الاستراتيجي" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="nav-btn nav-btn-outline">
                    <i class="fas fa-arrow-right"></i>
                    <span>معرض المشاريع</span>
                </a>
            </div>
        </nav>

        <div class="container py-4">

            <!-- Form Header Card -->
            <div class="form-header-card">
                <div class="form-badge-pill">
                    <i class="fas fa-graduation-cap"></i>
                    <span>بوابة مشاريع تخرج الطلبة المتميزة</span>
                </div>
                <h1 class="form-header-title">نموذج تسجيل وتقديم مشروع التخرج</h1>
                <p class="form-header-subtitle">
                    خريجينا الأعزاء، نرحب بمشاركتكم لعرض ابتكاراتكم وأفكاركم المتميزة في {{ $fair ? $fair->title : 'معرض التوظيف السنوي بجامعة طرابلس' }}.
                    تتم مراجعة كافة المشاريع واعتمادها من قبل اللجنة الإشرافية لنشرها في المعرض الرقمي وجناح العرض المخصص.
                </p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger shadow-lg rounded-4 p-3 mb-4" role="alert" style="background: rgba(220, 38, 38, 0.9); border: none; color: white;">
                    <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> يرجى تصحيح الأخطاء التالية قبل المتابعة:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('job-fair.public.projects.store-submission') }}" method="POST" enctype="multipart/form-data" id="projectSubmissionForm">
                @csrf

                <!-- معرض التوظيف المستهدف -->
                <div class="form-section-card">
                    <div class="section-header">
                        <div class="section-icon-wrap">
                            <i class="fas fa-calendar-star"></i>
                        </div>
                        <div>
                            <h3 class="section-title">المعرض الجامعي المستهدف</h3>
                            <p class="section-desc">اختر المعرض أو الفعالية التي ترغب بتقديم مشروع التخرج إليها</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">دورة المعرض <span class="req">*</span></label>
                            <select name="job_fair_id" class="form-select" required>
                                @foreach($allFairs as $f)
                                    <option value="{{ $f->id }}" {{ (old('job_fair_id', $fair?->id) == $f->id) ? 'selected' : '' }}>
                                        {{ $f->title }} — {{ $f->academic_year ?? date('Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- القسم الأول: البيانات الأساسية والأكاديمية -->
                <div class="form-section-card">
                    <div class="section-header">
                        <div class="section-icon-wrap">
                            <i class="fas fa-university"></i>
                        </div>
                        <div>
                            <h3 class="section-title">1. البيانات الأكاديمية والتصنيف</h3>
                            <p class="section-desc">المعلومات الرسمية الخاصة بالكلية والتخصص وسنة الإنجاز</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                <span>عنوان المشروع بالكامل <span class="req">*</span></span>
                                <span class="hint">عنوان واضح ومميز</span>
                            </label>
                            <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="مثال: المنظومة الوطنية للرعاية الصحية الذكية باستخدام تقنيات الذكاء الاصطناعي">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الكلية <span class="req">*</span></label>
                            <input type="text" name="faculty" class="form-control" required value="{{ old('faculty', $prefill['faculty'] ?? '') }}" placeholder="مثال: كلية تقنية المعلومات">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">القسم / التخصص <span class="req">*</span></label>
                            <input type="text" name="department" class="form-control" required value="{{ old('department', $prefill['department'] ?? '') }}" placeholder="مثال: هندسة البرمجيات">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">سنة التخرج <span class="req">*</span></label>
                            <input type="number" name="graduation_year" class="form-control" required value="{{ old('graduation_year', $prefill['graduation_year'] ?? 2026) }}" min="2015" max="2030">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">نوع المشروع <span class="req">*</span></label>
                            <input type="text" name="project_type" list="projectTypeList" class="form-control" required value="{{ old('project_type') }}" placeholder="اختر أو اكتب نوع المشروع...">
                            <datalist id="projectTypeList">
                                <option value="تطبيق ويب وسحابي (Web Application)">
                                <option value="تطبيق هاتف ذكي (Mobile App)">
                                <option value="ذكاء اصطناعي وتعلم آلة (AI / Machine Learning)">
                                <option value="إنترنت الأشياء وأنظمة مدمجة (IoT / Embedded Systems)">
                                <option value="أنظمة هندسية وصناعية (Engineering & Hardware)">
                                <option value="أمن سيبراني وشبكات (Cybersecurity & Networks)">
                                <option value="بحث علمي تطبيقي (Applied Research)">
                                <option value="تحليل بيانات ونظم معلومات (Data Science / GIS)">
                                <option value="أخرى">
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">المجال الرئيسي للمشروع <span class="req">*</span></label>
                            <input type="text" name="main_category" list="categoryList" class="form-control" required value="{{ old('main_category') }}" placeholder="اختر أو اكتب المجال الرئيسي...">
                            <datalist id="categoryList">
                                <option value="تقنية المعلومات والبرمجيات">
                                <option value="الصحة والرعاية الطبية">
                                <option value="الهندسة المدنية والمعمارية">
                                <option value="الطاقة المتجددة والاستدامة">
                                <option value="الاتصالات والشبكات">
                                <option value="القطاع المالي والمصرفي (FinTech)">
                                <option value="التعليم الإلكتروني وتقنيات التعلم">
                                <option value="الزراعة والبيئة">
                                <option value="التجارة الإلكترونية والخدمات اللوجستية">
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">المشرف الأكاديمي</label>
                            <input type="text" name="supervisor_name" class="form-control" value="{{ old('supervisor_name') }}" placeholder="اسم الأستاذ المشرف على المشروع">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">اللقب والصفة الأكاديمية للمشرف</label>
                            <input type="text" name="supervisor_title" class="form-control" value="{{ old('supervisor_title') }}" placeholder="مثال: أستاذ دكتور / أستاذ مشارك / محاضر">
                        </div>
                    </div>
                </div>

                <!-- القسم الثاني: فكرة المشروع وتفاصيله الفنية -->
                <div class="form-section-card">
                    <div class="section-header">
                        <div class="section-icon-wrap">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h3 class="section-title">2. تفاصيل ووصف المشروع</h3>
                            <p class="section-desc">نبذة تعريفية، المشكلة، والحل والمواصفات الفنية الكاملة</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                <span>نبذة تعريفية مختصرة (Abstract) <span class="req">*</span></span>
                                <span class="hint">ملخص يظهر في بطاقة العرض السريعة (3-5 أسطر)</span>
                            </label>
                            <textarea name="summary" class="form-control" rows="3" required placeholder="اكتب نبذة مركزة تلخص فكرة المشروع، أهميته، وأبرز مخرجاته...">{{ old('summary') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>المشكلة التي يعالجها المشروع</span>
                                <span class="hint">ما هو التحدي الواقعي أو الفجوة؟</span>
                            </label>
                            <textarea name="problem_statement" class="form-control" rows="3" placeholder="توضيح المشكلة التي دفع المشروع لإيجاد حل لها...">{{ old('problem_statement') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>الحل الذي يقدمه المشروع</span>
                                <span class="hint">كيف يقوم المشروع بحل هذه المشكلة؟</span>
                            </label>
                            <textarea name="solution_statement" class="form-control" rows="3" placeholder="طريقة الحل، الابتكار المقدم، والقيمة المضافة...">{{ old('solution_statement') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>أهداف المشروع (Objectives)</span>
                                <span class="hint">الأهداف الرئيسية التي حققها المشروع</span>
                            </label>
                            <textarea name="objectives" class="form-control" rows="3" placeholder="اكتب أهداف المشروع (يمكن كتابة كل هدف في سطر مستقل)...">{{ old('objectives') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>وصف المشروع الكامل <span class="req">*</span></span>
                                <span class="hint">شرح تفصيلي موسع لآلية العمل</span>
                            </label>
                            <textarea name="description" class="form-control" rows="4" required placeholder="وصف كامل وشامل لكافة جوانب المشروع ومكوناته...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>التفاصيل الفنية والمخرجات والمواصفات</span>
                                <span class="hint">التقنيات، لغات البرمجة، العتاد، الأدوات</span>
                            </label>
                            <textarea name="technical_specifications" class="form-control" rows="3" placeholder="مثال: Laravel, Python, Flutter, PostgreSQL, TensorFlow, Arduino...">{{ old('technical_specifications') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>أبرز النتائج والمميزات التنافسية</span>
                                <span class="hint">ما الذي يميّز عملكم؟ الدقة، السرعة، الكفاءة؟</span>
                            </label>
                            <textarea name="key_outcomes" class="form-control" rows="3" placeholder="أهم النتائج التي تم الوصول إليها، نسبة النجاح، المزايا الفريدة...">{{ old('key_outcomes') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>إمكانية تطوير المشروع إلى منتج أو خدمة قابلة للتسويق (Market Viability)</span>
                                <span class="hint">هل يمكن تحويل المشروع إلى شركة ناشئة أو منتج تجاري؟</span>
                            </label>
                            <textarea name="market_viability" class="form-control" rows="2" placeholder="الفرص الاستثمارية للمشروع، الجمهور المستهدف، وإمكانية تسويقه بالسوق الليبي والإقليمي...">{{ old('market_viability') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- القسم الثالث: فريق العمل ووسائل التواصل العامة -->
                <div class="form-section-card">
                    <div class="section-header">
                        <div class="section-icon-wrap">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="section-title">3. فريق العمل والتواصل العام</h3>
                            <p class="section-desc">أسماء الطلبة والبريد الإلكتروني المتاح للشركات والزوار</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                <span>أسماء أعضاء فريق العمل (الطلبة المشاركون) <span class="req">*</span></span>
                                <span class="hint">اكتب اسم كل طالب في سطر مستقل</span>
                            </label>
                            <textarea name="team_members_raw" class="form-control" rows="3" required placeholder="محمد علي الترهوني&#10;سارة أحمد الزاوي&#10;عبد الرحمن عمر الفيتوري">{{ old('team_members_raw', $prefill['name'] ?? '') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>البريد الإلكتروني العام للتواصل <span class="req">*</span></span>
                                <span class="hint">سيظهر للزوار والشركات الراغبة بالتواصل مع الفريق أو تقديم فرص توظيف</span>
                            </label>
                            <input type="email" name="contact_email" class="form-control" required value="{{ old('contact_email', $prefill['contact_email'] ?? '') }}" placeholder="project-team@example.com">
                        </div>
                    </div>
                </div>

                <!-- القسم الرابع: الوسائط والروابط الخارجية -->
                <div class="form-section-card">
                    <div class="section-header">
                        <div class="section-icon-wrap">
                            <i class="fas fa-photo-video"></i>
                        </div>
                        <div>
                            <h3 class="section-title">4. الوسائط، البوستر والروابط</h3>
                            <p class="section-desc">الملفات المرئية وروابط المستودعات البرمجية والعروض التوضيحية</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <span>بوستر المشروع (Poster)</span>
                                <span class="hint">صورة أو تصميم البوستر التعريفي (JPG / PNG)</span>
                            </label>
                            <input type="file" name="poster_image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>صورة الغلاف / الواجهة (Cover Image)</span>
                                <span class="hint">صورة جذابة تظهر كواجهة رئيسية للبطاقة</span>
                            </label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>رابط المشروع البرمجي أو GitHub</span>
                                <span class="hint">GitHub Repository / Live Demo</span>
                            </label>
                            <input type="url" name="project_url" class="form-control" value="{{ old('project_url') }}" placeholder="https://github.com/username/project">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>رابط فيديو العرض (Video Demo / YouTube)</span>
                                <span class="hint">رابط يوتيوب أو فيديو توضيحي لعمل النظام</span>
                            </label>
                            <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>
                </div>

                <!-- القسم الخامس: المعلومات الخاصة بالمسؤول فقط (سرية ولا تظهر للعامة) -->
                <div class="form-section-card admin-box-wrap">
                    <div class="section-header">
                        <div class="section-icon-wrap section-icon-admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h3 class="section-title text-warning">5. البيانات اللوجستية والإدارية (خاصة بإدارة المعرض فقط)</h3>
                            <p class="section-desc text-white-50">هذه البيانات مشفرة ولن تظهر للزوار أو العامة، وتُستخدم حصرياً لتنسيق جناحكم وتجهيزات العرض</p>
                        </div>
                    </div>

                    <div class="admin-banner-notice">
                        <i class="fas fa-lock fa-lg text-danger"></i>
                        <div>
                            <strong>خصوصية تامة:</strong> المعلومات التالية لن تنشر على الصفحة العامة للمشروع وتُحفظ لغايات التنظيم الإداري وتنسيق الأجنحة والمراجعة الداخلية فقط.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <span>الرقم الجامعي / رقم القيد (للطالب ممثل المشروع) <span class="req">*</span></span>
                                <span class="hint">للتثبت والتوثيق الرسمي</span>
                            </label>
                            <input type="text" name="student_university_id" class="form-control" required value="{{ old('student_university_id', $prefill['student_university_id'] ?? '') }}" placeholder="رقم القيد الجامعي">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>رقم الهاتف المسجل بالواتساب للتواصل المباشر <span class="req">*</span></span>
                                <span class="hint">للتواصل مع إدارة المعرض وتنسيق الجناح</span>
                            </label>
                            <input type="tel" name="whatsapp_phone" class="form-control" required value="{{ old('whatsapp_phone', $prefill['whatsapp_phone'] ?? '') }}" placeholder="091XXXXXXX / 092XXXXXXX">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>حالة النموذج الأولي (Prototype Status)</span>
                                <span class="hint">لتقييم مدى جاهزية العرض</span>
                            </label>
                            <select name="prototype_status" class="form-select">
                                <option value="">-- حدد حالة النموذج الأولي --</option>
                                <option value="فكرة وتصميم نظري (Design / Concept)" {{ old('prototype_status') == 'فكرة وتصميم نظري (Design / Concept)' ? 'selected' : '' }}>فكرة وتصميم نظري (Design / Concept)</option>
                                <option value="نموذج أولي فعال قيد التجربة (Working Prototype)" {{ old('prototype_status') == 'نموذج أولي فعال قيد التجربة (Working Prototype)' ? 'selected' : '' }}>نموذج أولي فعال قيد التجربة (Working Prototype)</option>
                                <option value="منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)" {{ old('prototype_status') == 'منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)' ? 'selected' : '' }}>منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)</option>
                                <option value="براءة اختراع / مسجل رسمياً" {{ old('prototype_status') == 'براءة اختراع / مسجل رسمياً' ? 'selected' : '' }}>براءة اختراع / مسجل رسمياً</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <span>المتطلبات اللوجستية التي يحتاجها المشروع في الجناح</span>
                                <span class="hint">مثال: طاولة إضافية، إنترنت سريع، كراسي</span>
                            </label>
                            <input type="text" name="project_requirements" class="form-control" value="{{ old('project_requirements') }}" placeholder="المتطلبات المكانية والتنظيمية">
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="needs_special_equipment" id="needsSpecialEquipment" value="1" {{ old('needs_special_equipment') ? 'checked' : '' }} onchange="document.getElementById('specialEquipmentWrap').style.display = this.checked ? 'block' : 'none';">
                                <label class="form-check-label fw-bold text-white" for="needsSpecialEquipment">
                                    <i class="fas fa-plug text-warning me-1"></i> هل يحتاج المشروع معدات أو توصيلات خاصة أثناء العرض؟
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12" id="specialEquipmentWrap" style="{{ old('needs_special_equipment') ? '' : 'display: none;' }}">
                            <label class="form-label">
                                <span>تفاصيل المعدات الخاصة المطلوبة</span>
                                <span class="hint">مثال: جهد كهربائي عالي، شاشة عرض، مجسمات كبيرة، مساحة مفتوحة...</span>
                            </label>
                            <textarea name="special_equipment_details" class="form-control" rows="2" placeholder="اشرح المعدات والتجهيزات الخاصة التي تحتاجونها...">{{ old('special_equipment_details') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>المتطلبات أو الملاحظات الإضافية</span>
                            </label>
                            <textarea name="additional_requirements" class="form-control" rows="2" placeholder="أي معلومات أو متطلبات ترغب بإبلاغ إدارة المعرض بها...">{{ old('additional_requirements') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <span>الملخص التنفيذي للمراجعة الداخلية واللجنة العلمية (Executive Summary)</span>
                                <span class="hint">مخصص للمحكمين والمشرفين الإداريين لتقييم ترشيح المشروع</span>
                            </label>
                            <textarea name="executive_summary" class="form-control" rows="3" placeholder="ملخص موجه للجنة التحكيم الإدارية والعلمية للمعرض...">{{ old('executive_summary') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center my-4">
                    <button type="submit" class="btn-submit-main" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>إرسال وتثبيت طلب تقديم مشروع التخرج</span>
                    </button>
                    <div class="text-white-50 small mt-2">
                        بمجرد الإرسال، سيتلقى المشروع رقم مرجعي وسيدخل في مرحلة المراجعة الإدارية للجنة المنظمة.
                    </div>
                </div>

            </form>

        </div>

        <!-- Footer -->
        <footer class="page-footer">
            <div class="container">
                <div>مكتب تدريب الخريجين — جامعة طرابلس &copy; {{ date('Y') }} بالتعاون مع شركة الواحة لتنظيم المعارض والمؤتمرات</div>
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('projectSubmissionForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري إرسال وتخزين بيانات المشروع...';
        });
    </script>
</body>
</html>
