<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نموذج تقديم مشروع تخرج معتمد — {{ $fair ? $fair->title : 'معرض التوظيف جامعة طرابلس' }}</title>
    <meta name="description" content="النموذج الرسمي المعتمد لتسجيل وتقديم مشاريع تخرج طلبة وخريجي كليات جامعة طرابلس في المعرض السنوي وأرشيف الابتكارات.">

    <!-- Bootstrap 5.3 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --uot-blue:       #0d3882;
            --uot-blue-hover: #09275e;
            --uot-blue-light: #1565c0;
            --uot-blue-subtle:#eff6ff;
            --uot-gold:       #eeca3e;
            --uot-gold-dark:  #d97706;
            --uot-gold-subtle:#fef3c7;
            --slate-50:       #f8fafc;
            --slate-100:      #f1f5f9;
            --slate-200:      #e2e8f0;
            --slate-300:      #cbd5e1;
            --slate-600:      #475569;
            --slate-700:      #334155;
            --slate-800:      #1e293b;
            --slate-900:      #0f172a;
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--slate-100);
            color: var(--slate-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        /* ── Official Top Header Navbar ── */
        .university-navbar {
            background: linear-gradient(135deg, #0a2b66 0%, var(--uot-blue) 60%, #1565c0 100%);
            border-bottom: 3.5px solid var(--uot-gold);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 4px 20px rgba(10, 43, 102, 0.25);
        }

        .navbar-brand-group {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .uot-emblem-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid var(--uot-gold);
            object-fit: cover;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .navbar-title-text {
            display: flex;
            flex-direction: column;
            line-height: 1.25;
        }

        .navbar-title-main {
            font-size: 1rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.2px;
        }

        .navbar-title-sub {
            font-size: 0.76rem;
            color: var(--uot-gold);
            font-weight: 600;
        }

        .navbar-divider {
            width: 1px;
            height: 32px;
            background: rgba(255, 255, 255, 0.25);
            margin: 0 4px;
        }

        .fair-nav-logo {
            height: 36px;
            width: auto;
            max-width: 130px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));
        }

        .waha-sponsor-logo {
            height: 32px;
            width: auto;
            max-width: 110px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));
            opacity: 0.95;
            transition: opacity 0.2s ease;
        }

        .waha-sponsor-logo:hover {
            opacity: 1;
        }

        .nav-btn-link {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
        }

        .nav-btn-link:hover {
            background: #ffffff;
            color: var(--uot-blue);
            border-color: #ffffff;
            transform: translateY(-1px);
        }

        /* ── Hero Banner (Official University Style) ── */
        .page-hero-banner {
            background: linear-gradient(135deg, #092347 0%, var(--uot-blue) 45%, #1958b7 100%);
            color: #ffffff;
            padding: 40px 20px 48px;
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid var(--uot-gold);
        }

        .page-hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(238, 202, 62, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge-capsule {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(238, 202, 62, 0.18);
            border: 1px solid var(--uot-gold);
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .hero-banner-heading {
            font-size: 2.15rem;
            font-weight: 900;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .hero-banner-subtext {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 820px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ── Main Layout Cards ── */
        .form-section-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 18px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .form-section-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.07);
        }

        .section-header-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 16px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--slate-100);
        }

        .section-icon-badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--uot-blue-subtle);
            border: 1.5px solid rgba(13, 56, 130, 0.2);
            color: var(--uot-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .section-header-title {
            font-size: 1.22rem;
            font-weight: 800;
            color: var(--uot-blue);
            margin: 0 0 3px 0;
        }

        .section-header-subtitle {
            font-size: 0.86rem;
            color: var(--slate-600);
            margin: 0;
        }

        /* ── Modern Form Controls ── */
        .form-label {
            font-weight: 700;
            font-size: 0.92rem;
            color: var(--slate-700);
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-label .req {
            color: #dc2626;
            margin-inline-start: 4px;
            font-weight: 800;
        }

        .form-label .hint {
            font-size: 0.77rem;
            font-weight: 400;
            color: #64748b;
        }

        .form-control,
        .form-select {
            background-color: #ffffff;
            border: 1.5px solid var(--slate-300);
            border-radius: 10px;
            color: var(--slate-900);
            font-family: inherit;
            font-size: 0.95rem;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff;
            border-color: var(--uot-blue);
            box-shadow: 0 0 0 4px rgba(13, 56, 130, 0.12);
            color: var(--slate-900);
            outline: none;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        /* ── Confidential Admin Box ── */
        .admin-confidential-card {
            border: 2px solid #e2e8f0;
            border-right: 6px solid var(--uot-gold-dark);
            background: #fcfcfd;
        }

        .admin-icon-badge {
            background: #fef3c7;
            border-color: #fde68a;
            color: #b45309;
        }

        .admin-privacy-banner {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #92400e;
            font-size: 0.88rem;
        }

        /* ── Primary Submit Button ── */
        .btn-submit-project {
            background: linear-gradient(135deg, var(--uot-blue) 0%, #1565c0 100%);
            border: 2px solid var(--uot-gold);
            color: #ffffff;
            border-radius: 14px;
            padding: 15px 34px;
            font-weight: 800;
            font-size: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(13, 56, 130, 0.3);
            width: 100%;
        }

        .btn-submit-project:hover {
            background: linear-gradient(135deg, var(--uot-blue-hover) 0%, var(--uot-blue) 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(13, 56, 130, 0.4);
            border-color: #ffd84d;
        }

        /* ── Official University Footer ── */
        .university-footer {
            margin-top: auto;
            background: #0a254f;
            border-top: 4px solid var(--uot-gold);
            color: #cbd5e1;
            padding: 24px 0;
            font-size: 0.88rem;
            text-align: center;
        }

        .university-footer-text {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
        }

        /* ── Responsive & iOS Adjustments ── */
        @media (max-width: 768px) {
            .page-hero-banner {
                padding: 30px 16px 36px;
            }
            .hero-banner-heading {
                font-size: 1.65rem;
            }
            .form-section-card {
                padding: 20px 16px;
                border-radius: 14px;
            }
            .university-navbar {
                padding: 0.65rem 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- University Approved Top Navigation Bar -->
    <header class="university-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ $fair ? route('job-fair.public', $fair->id) : route('home') }}" class="navbar-brand-group">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار جامعة طرابلس" class="uot-emblem-img" onerror="this.style.display='none'">
                <div class="navbar-title-text">
                    <span class="navbar-title-main">جامعة طرابلس</span>
                    <span class="navbar-title-sub">مكتب تدريب وتوظيف الخريجين</span>
                </div>

                @if($fair)
                    <img src="{{ $fair->white_logo_url }}" class="fair-nav-logo ms-2 d-none d-sm-block" alt="{{ $fair->title }}" onerror="this.onerror=null;this.src='{{ $fair->horizontal_logo_url }}';">
                @else
                    <img src="{{ asset('images/job_fair_logo_white.png') }}" class="fair-nav-logo ms-2 d-none d-sm-block" alt="شعار الفعالية" onerror="this.style.display='none'">
                @endif

                <div class="navbar-divider d-none d-md-block"></div>

                <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" class="waha-sponsor-logo d-none d-md-block" title="شركة الواحة لتنظيم المعارض والمؤتمرات — الشريك الاستراتيجي" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
            </a>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="nav-btn-link">
                    <i class="fas fa-th-large"></i>
                    <span>معرض المشاريع</span>
                </a>
            </div>
        </div>
    </header>

    <!-- University Hero Banner -->
    <section class="page-hero-banner text-center">
        <div class="container">
            <div class="hero-badge-capsule">
                <i class="fas fa-graduation-cap text-warning"></i>
                <span>النموذج المعتمد — مشاريع تخرج الطلبة والخريجين</span>
            </div>
            <h1 class="hero-banner-heading">استمارة تقديم وتسجيل مشروع التخرج</h1>
            <p class="hero-banner-subtext">
                أهلاً بكم في المنصة الرسمية لتوثيق وعرض ابتكارات خريجي جامعة طرابلس في {{ $fair ? $fair->title : 'معرض التوظيف السنوي' }}.
                تخضع كافة المشاريع للمراجعة العلمية والاعتماد الإداري لعرضها في الأرشيف الرقمي وأجنحة المعرض المتخصصة.
            </p>
        </div>
    </section>

    <!-- Main Submission Form Container -->
    <main class="container py-4 flex-grow-1">

        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 p-3 mb-4" role="alert">
                <div class="fw-bold mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i> يرجى مراجعة وتصحيح الملاحظات التالية قبل المتابعة:
                </div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('job-fair.public.projects.store-submission') }}" method="POST" enctype="multipart/form-data" id="projectSubmissionForm">
            @csrf

            <!-- 0. المعرض الجامعي المستهدف -->
            <div class="form-section-card">
                <div class="section-header-box">
                    <div class="section-icon-badge">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title">المعرض الجامعي المستهدف</h2>
                        <p class="section-header-subtitle">حدد الدورة أو المعرض الذي ترغب بإدراج مشروع التخرج ضمنه</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">
                            <span>دورة المعرض المعتمدة <span class="req">*</span></span>
                        </label>
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

            <!-- 1. البيانات الأكاديمية والتصنيف -->
            <div class="form-section-card">
                <div class="section-header-box">
                    <div class="section-icon-badge">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title">1. البيانات الأكاديمية والتصنيف العلمي</h2>
                        <p class="section-header-subtitle">البيانات الرسمية المعتمدة بالكلية، القسم، المشرف وسنة التخرج</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">
                            <span>عنوان مشروع التخرج بالكامل <span class="req">*</span></span>
                            <span class="hint">عنوان علمي دقيق وواضح</span>
                        </label>
                        <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="مثال: نظام إدارة الطاقة المتجددة الذكي باستخدام تقنيات إنترنت الأشياء والتعلم الآلي">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            <span>الكلية <span class="req">*</span></span>
                        </label>
                        <input type="text" name="faculty" class="form-control" required value="{{ old('faculty', $prefill['faculty'] ?? '') }}" placeholder="مثال: كلية الهندسة / كلية تقنية المعلومات">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            <span>القسم / التخصص الأكاديمي <span class="req">*</span></span>
                        </label>
                        <input type="text" name="department" class="form-control" required value="{{ old('department', $prefill['department'] ?? '') }}" placeholder="مثال: هندسة البرمجيات / الهندسة الكهربائية">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            <span>سنة التخرج <span class="req">*</span></span>
                        </label>
                        <input type="number" name="graduation_year" class="form-control" required value="{{ old('graduation_year', $prefill['graduation_year'] ?? 2026) }}" min="2015" max="2030">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>طبيعة ونوع المشروع <span class="req">*</span></span>
                        </label>
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
                        <label class="form-label">
                            <span>المجال العلمي والتطبيقي <span class="req">*</span></span>
                        </label>
                        <input type="text" name="main_category" list="categoryList" class="form-control" required value="{{ old('main_category') }}" placeholder="اختر أو حدد المجال الرئيسي...">
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
                        <label class="form-label">
                            <span>الأستاذ المشرف على المشروع</span>
                            <span class="hint">المشرف الأكاديمي الرئيسي</span>
                        </label>
                        <input type="text" name="supervisor_name" class="form-control" value="{{ old('supervisor_name') }}" placeholder="الاسم الكامل للأستاذ المشرف">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>الصفة والدرجة العلمية للمشرف</span>
                            <span class="hint">مثال: أستاذ دكتور / أستاذ مشارك / محاضر</span>
                        </label>
                        <input type="text" name="supervisor_title" class="form-control" value="{{ old('supervisor_title') }}" placeholder="الدرجة العلمية للمشرف">
                    </div>
                </div>
            </div>

            <!-- 2. تفاصيل ووصف المشروع -->
            <div class="form-section-card">
                <div class="section-header-box">
                    <div class="section-icon-badge">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title">2. فكرة المشروع والمواصفات الفنية</h2>
                        <p class="section-header-subtitle">النبذة التنفيذية، المشكلة، الحل المقترح والمخرجات التنافسية</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">
                            <span>نبذة تعريفية مركزة (Abstract) <span class="req">*</span></span>
                            <span class="hint">تظهر في بطاقة المشروع والأرشيف الرقمي (3 - 5 أسطر)</span>
                        </label>
                        <textarea name="summary" class="form-control" rows="3" required placeholder="ملخص مكثف لفكرة المشروع، أهميته التطبيقية، وأبرز الإنجازات المحققة...">{{ old('summary') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>المشكلة والتحدي الواقعي (Problem Statement)</span>
                            <span class="hint">ما هي الفجوة أو الصعوبة التي تعالجونها؟</span>
                        </label>
                        <textarea name="problem_statement" class="form-control" rows="3" placeholder="توضيح المشكلة أو الفجوة الميدانية التي دفعت لإنجاز المشروع...">{{ old('problem_statement') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>الحل والابتكار المقدم (Solution Statement)</span>
                            <span class="hint">كيف يعالج المشروع هذه المشكلة بكفاءة؟</span>
                        </label>
                        <textarea name="solution_statement" class="form-control" rows="3" placeholder="آلية الحل، الميزة المبتكرة، والقيمة المضافة المقدمة...">{{ old('solution_statement') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>أهداف المشروع المعتمدة (Objectives)</span>
                            <span class="hint">الأهداف الرئيسية المحققة</span>
                        </label>
                        <textarea name="objectives" class="form-control" rows="3" placeholder="اكتب أهداف المشروع المحققة (يمكن كتابة كل هدف بسطر مستقل)...">{{ old('objectives') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>الوصف التفصيلي الشامل <span class="req">*</span></span>
                            <span class="hint">شرح متكامل لآلية العمل ومنهجية التنفيذ</span>
                        </label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="شرح وافٍ وتفصيلي لكافة عناصر النظام، المعمارية الهندسية، وسير العمل...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>المواصفات الفنية والتقنيات المستخدمة</span>
                            <span class="hint">لغات البرمجة، الحزم، الأجهزة، العتاد</span>
                        </label>
                        <textarea name="technical_specifications" class="form-control" rows="3" placeholder="مثال: Laravel 11, Vue.js, Python, PostgreSQL, ESP32, Docker...">{{ old('technical_specifications') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>أبرز النتائج والميزات التنافسية</span>
                            <span class="hint">نسبة الدقة، زمن الاستجابة، الكفاءة، التوفير</span>
                        </label>
                        <textarea name="key_outcomes" class="form-control" rows="3" placeholder="أهم النتائج المقاسة، مقاييس الأداء والمزايا التنافسية للحل...">{{ old('key_outcomes') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>الجدوى وقابلية التطبيق التجاري (Market Viability)</span>
                            <span class="hint">إمكانية تحويل المشروع إلى شركة ناشئة أو منتج بالسوق الليبي</span>
                        </label>
                        <textarea name="market_viability" class="form-control" rows="2" placeholder="الفرص الاستثمارية المستهدفة، شرائح المستفيدين، وآفاق التطوير التجاري...">{{ old('market_viability') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. فريق العمل والتواصل العام -->
            <div class="form-section-card">
                <div class="section-header-box">
                    <div class="section-icon-badge">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title">3. فريق العمل وبيانات التواصل العام</h2>
                        <p class="section-header-subtitle">أسماء الطلبة والبريد المخصص للتواصل المهني مع الشركات والزوار</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">
                            <span>أسماء أعضاء فريق العمل (الطلبة المشاركون) <span class="req">*</span></span>
                            <span class="hint">يرجى كتابة كل اسم ثلاثي أو رباعي بسطر مستقل</span>
                        </label>
                        <textarea name="team_members_raw" class="form-control" rows="3" required placeholder="محمد علي الترهوني&#10;سارة أحمد الزاوي&#10;عبد الرحمن عمر الفيتوري">{{ old('team_members_raw', $prefill['name'] ?? '') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>البريد الإلكتروني العام للمشروع <span class="req">*</span></span>
                            <span class="hint">سيظهر للمؤسسات والشركات الراغبة بتقديم عروض عمل أو دعم للمشروع</span>
                        </label>
                        <input type="email" name="contact_email" class="form-control" required value="{{ old('contact_email', $prefill['contact_email'] ?? '') }}" placeholder="project-team@example.com">
                    </div>
                </div>
            </div>

            <!-- 4. الوسائط، البوستر والروابط -->
            <div class="form-section-card">
                <div class="section-header-box">
                    <div class="section-icon-badge">
                        <i class="fas fa-photo-video"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title">4. الوسائط المرئية والروابط الخارجية</h2>
                        <p class="section-header-subtitle">البوستر العلمي، الصور التوضيحية، روابط المستودعات والعروض المرئية</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <span>بوستر المشروع العلمي (Poster)</span>
                            <span class="hint">ملف صورة للبوستر (JPG / PNG / WebP) بحد أقصى 10MB</span>
                        </label>
                        <input type="file" name="poster_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>صورة الغلاف الرئيسية (Cover Image)</span>
                            <span class="hint">صورة ترويجية بارزة تظهر في بطاقة المشروع الرقمية</span>
                        </label>
                        <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>رابط الكود المصدري أو المستودع (GitHub / Repository)</span>
                            <span class="hint">رابط المستودع أو النسخة التجريبية الحية</span>
                        </label>
                        <input type="url" name="project_url" class="form-control" value="{{ old('project_url') }}" placeholder="https://github.com/username/project">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>رابط فيديو العرض التوضيحي (Video Demo)</span>
                            <span class="hint">رابط يوتيوب أو فيديو يشرح عمل المنظومة عملياً</span>
                        </label>
                        <input type="url" name="video_url" class="form-control" value="{{ old('video_url') }}" placeholder="https://youtube.com/watch?v=...">
                    </div>
                </div>
            </div>

            <!-- 5. البيانات اللوجستية والإدارية (سرية ومحمية للجنة المنظمة) -->
            <div class="form-section-card admin-confidential-card">
                <div class="section-header-box">
                    <div class="section-icon-badge admin-icon-badge">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h2 class="section-header-title text-primary">5. البيانات اللوجستية والإدارية (خاصة بإدارة المعرض فقط)</h2>
                        <p class="section-header-subtitle">تُستخدم حصرياً لأغراض التحقق الرسمي وتنسيق الأجنحة والمعدات</p>
                    </div>
                </div>

                <div class="admin-privacy-banner">
                    <i class="fas fa-lock fa-lg"></i>
                    <div>
                        <strong>خصوصية وسرية تامة:</strong> هذه البيانات لن تُنشر للعامة أو على الصفحة المفتوحة للمشروع، ومخصصة للجنة المنظمة للتواصل المباشر معكم وتجهيز الجناح.
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <span>الرقم الجامعي / رقم القيد (للطالب ممثل المشروع) <span class="req">*</span></span>
                            <span class="hint">للتحقق من السجلات الأكاديمية</span>
                        </label>
                        <input type="text" name="student_university_id" class="form-control" required value="{{ old('student_university_id', $prefill['student_university_id'] ?? '') }}" placeholder="رقم القيد الجامعي">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>رقم هاتف الواتساب للتواصل والتنسيق <span class="req">*</span></span>
                            <span class="hint">للتواصل السريع لتسليم البوستر وموقع الجناح</span>
                        </label>
                        <input type="tel" name="whatsapp_phone" class="form-control" required value="{{ old('whatsapp_phone', $prefill['whatsapp_phone'] ?? '') }}" placeholder="091XXXXXXX / 092XXXXXXX">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>حالة النموذج الأولي (Prototype Readiness)</span>
                            <span class="hint">تقييم جاهزية العرض الحي بالمعرض</span>
                        </label>
                        <select name="prototype_status" class="form-select">
                            <option value="">-- اختر حالة النموذج الأولي --</option>
                            <option value="فكرة وتصميم نظري (Design / Concept)" {{ old('prototype_status') == 'فكرة وتصميم نظري (Design / Concept)' ? 'selected' : '' }}>فكرة وتصميم نظري (Design / Concept)</option>
                            <option value="نموذج أولي فعال قيد التجربة (Working Prototype)" {{ old('prototype_status') == 'نموذج أولي فعال قيد التجربة (Working Prototype)' ? 'selected' : '' }}>نموذج أولي فعال قيد التجربة (Working Prototype)</option>
                            <option value="منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)" {{ old('prototype_status') == 'منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)' ? 'selected' : '' }}>منتج كامل قابل للتشغيل والإنتاج (Production Ready / MVP)</option>
                            <option value="براءة اختراع / مسجل رسمياً" {{ old('prototype_status') == 'براءة اختراع / مسجل رسمياً' ? 'selected' : '' }}>براءة اختراع / مسجل رسمياً</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <span>المتطلبات اللوجستية التي يحتاجها الجناح</span>
                            <span class="hint">مثال: طاولة إضافية، توصيلات إنترنت، كراسي</span>
                        </label>
                        <input type="text" name="project_requirements" class="form-control" value="{{ old('project_requirements') }}" placeholder="المتطلبات المكانية والتنظيمية للجناح">
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="needs_special_equipment" id="needsSpecialEquipment" value="1" {{ old('needs_special_equipment') ? 'checked' : '' }} onchange="document.getElementById('specialEquipmentWrap').style.display = this.checked ? 'block' : 'none';">
                            <label class="form-check-label fw-bold text-dark" for="needsSpecialEquipment">
                                <i class="fas fa-plug text-warning me-1"></i> هل يحتاج المشروع تجهيزات كهربائية أو أجهزة خاصة أثناء العرض؟
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12" id="specialEquipmentWrap" style="{{ old('needs_special_equipment') ? '' : 'display: none;' }}">
                        <label class="form-label">
                            <span>تفاصيل التجهيزات الخاصة المطلوبة</span>
                            <span class="hint">مثال: جهد كهربائي عالي، شاشة عرض، مجسمات كبيرة، مساحة مفتوحة...</span>
                        </label>
                        <textarea name="special_equipment_details" class="form-control" rows="2" placeholder="اشرح المعدات والتجهيزات الخاصة التي تحتاجونها بالتفصيل...">{{ old('special_equipment_details') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>أي ملاحظات أو متطلبات تنظيمية إضافية</span>
                        </label>
                        <textarea name="additional_requirements" class="form-control" rows="2" placeholder="أي معلومات أو رغبات تودون إبلاغ اللجنة المنظمة بها...">{{ old('additional_requirements') }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            <span>الملخص التنفيذي للجنة العلمية والمحكمين (Executive Summary)</span>
                            <span class="hint">مخصص للتقييم الأكاديمي والتحكيم الداخلي للمشاريع المرشحة للجوائز</span>
                        </label>
                        <textarea name="executive_summary" class="form-control" rows="3" placeholder="ملخص موجه لمحكمي اللجنة العلمية للمعرض يبرز القيمة الابتكارية...">{{ old('executive_summary') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- زر التقديم والإرسال -->
            <div class="text-center my-4">
                <button type="submit" class="btn-submit-project" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span>إرسال وتثبيت طلب تقديم مشروع التخرج</span>
                </button>
                <div class="text-muted small mt-2">
                    <i class="fas fa-shield-alt text-success me-1"></i>
                    عند الإرسال، يُمنح المشروع كوداً مرجعياً وسيدخل مرحلة المراجعة من قِبل اللجنة المنظمة.
                </div>
            </div>

        </form>

    </main>

    <!-- University Approved Footer -->
    <footer class="university-footer">
        <div class="container">
            <div class="university-footer-text mb-1">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} جامعة طرابلس — مكتب تدريب وتوظيف الخريجين
            </div>
            <div class="small text-secondary">
                بالتعاون والشراكة الاستراتيجية مع شركة الواحة لتنظيم المعارض والمؤتمرات
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('projectSubmissionForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> جاري إرسال وتخزين بيانات المشروع...';
        });
    </script>
</body>
</html>
