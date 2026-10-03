<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->title }} — مشاريع التخرج | معرض التوظيف 2026</title>
    <meta name="description" content="{{ Str::limit(strip_tags($project->summary ?: $project->description), 160) }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $project->title }} — مشروع تخرج | جامعة طرابلس">
    <meta property="og:description" content="{{ Str::limit(strip_tags($project->summary ?: $project->description), 160) }}">
    <meta property="og:url" content="{{ $project->share_url }}">
    <meta property="og:type" content="article">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:      #eeca3e;
            --gold-lt:   #FDE68A;
            --navy:      #045db0; /* University Primary Blue */
            --navy-md:   #3b82f6; /* University Light Blue */
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

        /* ══════════════════════════════════
           ANIMATED VIBRANT BACKGROUND LAYERS
        ══════════════════════════════════ */
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

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 14s ease-in-out infinite alternate;
        }
        .orb-1 { width: 480px; height: 480px; background: rgba(238,202,62,0.15); top: -80px; left: -80px; }
        .orb-2 { width: 520px; height: 520px; background: rgba(14,165,233,0.18); bottom: 10%; right: -120px; animation-duration: 18s; animation-delay: -5s; }

        @keyframes orbFloat {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(40px, -30px) scale(1.08); }
            100% { transform: translate(-30px, 40px) scale(0.95); }
        }

        .page-content-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ══════════════════════════════════
           NAVBAR
        ══════════════════════════════════ */
        .glass-navbar {
            background: rgba(9, 35, 71, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand-group {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #fff;
        }

        .nav-logo-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 4px;
        }
        .nav-logo-box img { max-width: 100%; max-height: 100%; object-fit: contain; }

        .btn-nav-outline {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-nav-outline:hover {
            background: rgba(255,255,255,0.18);
            color: var(--gold-lt);
            border-color: rgba(238,202,62,0.4);
            transform: translateY(-1px);
        }

        /* ══════════════════════════════════
           BREADCRUMB & HERO
        ══════════════════════════════════ */
        .breadcrumb-wrap { padding: 22px 0 10px; }
        .custom-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.85rem;
            flex-wrap: wrap;
        }
        .custom-breadcrumb li a {
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .custom-breadcrumb li a:hover { color: var(--gold); }
        .custom-breadcrumb li.separator { color: rgba(255,255,255,0.3); font-size: 0.75rem; }
        .custom-breadcrumb li.active { color: var(--gold-lt); font-weight: 700; }

        /* Project Hero Banner */
        .project-hero-card {
            background: linear-gradient(135deg, rgba(8, 34, 69, 0.88) 0%, rgba(4, 93, 176, 0.7) 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            padding: 36px 40px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin-bottom: 35px;
            position: relative;
            overflow: hidden;
        }

        .project-hero-card::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(238,202,62,0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .badges-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .badge-hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: #fff;
        }
        .badge-hero-gold {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #0b1e38;
            border: none;
            box-shadow: 0 4px 15px rgba(245,158,11,0.35);
        }
        .badge-hero-booth {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .project-hero-title {
            font-size: 2.2rem;
            font-weight: 900;
            line-height: 1.35;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .project-hero-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 850px;
            line-height: 1.7;
            margin: 0;
        }

        /* ══════════════════════════════════
           CONTENT BOXES
        ══════════════════════════════════ */
        .glass-box {
            background: rgba(8, 34, 69, 0.7);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            margin-bottom: 25px;
            transition: border-color 0.3s ease;
        }
        .glass-box:hover {
            border-color: rgba(238, 202, 62, 0.3);
        }

        .box-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .box-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(238,202,62,0.25), rgba(4,93,176,0.3));
            border: 1px solid rgba(238,202,62,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-lt);
            font-size: 1.1rem;
        }

        .box-title-row h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        /* Poster Box */
        .poster-card-wrap {
            background: rgba(0,0,0,0.25);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            position: relative;
        }

        .poster-img-frame {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            max-height: 480px;
            background: #0b1a30;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .poster-img-frame img {
            max-width: 100%;
            max-height: 480px;
            object-fit: contain;
        }

        /* Team Cards */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .team-member-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all 0.25s ease;
        }
        .team-member-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--gold);
            transform: translateY(-2px);
        }

        .member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #045db0, #092347);
            border: 1.5px solid var(--gold);
            color: var(--gold-lt);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .member-info h5 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2px;
        }
        .member-role {
            font-size: 0.78rem;
            color: var(--gold-lt);
            margin-bottom: 6px;
        }
        .member-contact-links {
            display: flex;
            gap: 8px;
            font-size: 0.8rem;
        }
        .member-contact-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.2s;
        }
        .member-contact-links a:hover { color: #fff; }

        /* Supervisor Card */
        .supervisor-card {
            background: rgba(238,202,62,0.06);
            border: 1.5px solid rgba(238,202,62,0.3);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .supervisor-avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #0b1e38;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* ══════════════════════════════════
           SIDEBAR STICKY CARDS
        ══════════════════════════════════ */
        .sidebar-sticky {
            position: sticky;
            top: 90px;
        }

        .info-card-box {
            background: linear-gradient(145deg, rgba(8, 34, 69, 0.9) 0%, rgba(4, 93, 176, 0.75) 100%);
            border: 2px solid rgba(238, 202, 62, 0.4);
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            margin-bottom: 24px;
        }

        .meta-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .meta-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
        }
        .meta-icon-bubble {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-lt);
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .meta-label { color: rgba(255, 255, 255, 0.6); font-size: 0.78rem; display: block; }
        .meta-value { color: #ffffff; font-weight: 700; }

        /* Contact Team CTA Button */
        .btn-hire-cta {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1rem;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            margin-top: 20px;
        }
        .btn-hire-cta:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.45);
        }

        /* QR Code Card */
        .qr-card-wrap {
            background: rgba(8, 34, 69, 0.75);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            margin-bottom: 24px;
        }
        .qr-image-frame {
            background: #ffffff;
            border-radius: 16px;
            padding: 14px;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            margin-bottom: 14px;
            border: 3px solid var(--gold);
        }
        .qr-image-frame img { width: 170px; height: 170px; display: block; }
        .qr-caption { font-size: 0.82rem; color: rgba(255, 255, 255, 0.75); line-height: 1.5; margin-bottom: 14px; }

        .btn-qr-action {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 10px;
            padding: 7px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-qr-action:hover {
            background: rgba(255, 255, 255, 0.18);
            color: var(--gold-lt);
            border-color: var(--gold);
        }

        /* Social Share */
        .social-share-row { display: flex; gap: 8px; justify-content: center; margin-top: 14px; }
        .btn-social-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; text-decoration: none; font-size: 1rem;
            transition: transform 0.2s ease;
        }
        .btn-social-icon:hover { transform: translateY(-2px); color: #fff; }
        .bg-whatsapp { background: #25D366; }
        .bg-linkedin { background: #0A66C2; }
        .bg-twitter  { background: #000000; border: 1px solid rgba(255,255,255,0.2); }
        .bg-telegram { background: #229ED9; }

        /* Toast */
        .toast-copied {
            position: fixed; bottom: 30px; right: 30px;
            background: #10b981; color: #fff; padding: 12px 24px;
            border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            font-weight: 700; font-size: 0.9rem; display: none;
            align-items: center; gap: 8px; z-index: 9999;
        }

        @media (max-width: 991px) {
            .project-hero-card { padding: 24px 20px; }
            .project-hero-title { font-size: 1.6rem; }
            .sidebar-sticky { position: static; }
        }
    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="hero-bg-layer"></div>
    <div class="hero-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="page-content-wrapper">

        <!-- Top Navigation -->
        <nav class="glass-navbar">
            <div class="container py-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="nav-brand-group">
                            <div class="nav-logo-box">
                                <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين" onerror="this.src='{{ asset('images/uni_logo_white.png') }}'">
                            </div>
                            <div class="nav-brand-text">
                                <h1>معرض مشاريع التخرج</h1>
                                <small>{{ $fair ? $fair->title : 'جامعة طرابلس' }}</small>
                            </div>
                        </a>
                        <div class="d-none d-md-flex align-items-center bg-white p-1 rounded-2 shadow-sm border border-warning" style="height: 38px;" title="الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض">
                            <img src="{{ asset('images/wahaexpo_logo.png') }}" alt="شركة الواحة للمعارض" style="height: 30px; width: auto; object-fit: contain;">
                        </div>
                    </div>

                    <div class="nav-actions">
                        <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="btn-nav-outline">
                            <i class="fas fa-arrow-right"></i>
                            <span class="d-none d-sm-inline">كافة المشاريع</span>
                        </a>
                        @if($fair)
                        <a href="{{ route('job-fair.public', $fair->id) }}" class="btn-nav-outline d-none d-md-inline-flex">
                            <i class="fas fa-home"></i>
                            <span>الرئيسية للمعرض</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <div class="container pb-5">

            <!-- Breadcrumbs -->
            <div class="breadcrumb-wrap">
                <ul class="custom-breadcrumb">
                    <li><a href="{{ url('/') }}">الرئيسية</a></li>
                    <li class="separator"><i class="fas fa-chevron-left"></i></li>
                    @if($fair)
                        <li><a href="{{ route('job-fair.public', $fair->id) }}">{{ $fair->title }}</a></li>
                        <li class="separator"><i class="fas fa-chevron-left"></i></li>
                        <li><a href="{{ route('job-fair.public.projects', $fair->id) }}">مشاريع التخرج</a></li>
                    @else
                        <li><a href="{{ route('job-fair.public.projects.index') }}">مشاريع التخرج</a></li>
                    @endif
                    <li class="separator"><i class="fas fa-chevron-left"></i></li>
                    <li class="active">{{ Str::limit($project->title, 40) }}</li>
                </ul>
            </div>

            <!-- Project Hero Banner -->
            <div class="project-hero-card">
                <div class="badges-row">
                    <span class="badge-hero-pill badge-hero-gold">
                        <i class="{{ $project->faculty_icon }}"></i>
                        <span>{{ $project->faculty }}</span>
                    </span>

                    <span class="badge-hero-pill">
                        <i class="fas fa-layer-group text-warning"></i>
                        <span>{{ $project->department }}</span>
                    </span>

                    <span class="badge-hero-pill">
                        <i class="fas fa-calendar-alt text-info"></i>
                        <span>دفعة تخرج: {{ $project->graduation_year }}</span>
                    </span>

                    @if($project->booth_number)
                    <span class="badge-hero-pill badge-hero-booth">
                        <i class="fas fa-store-alt me-1"></i>جناح رقم: {{ $project->booth_number }}
                    </span>
                    @endif

                    <span class="badge-hero-pill" style="color: rgba(255,255,255,0.7);">
                        <i class="fas fa-eye text-primary"></i> {{ $project->views_count }} مشاهدة
                    </span>
                </div>

                <h2 class="project-hero-title">{{ $project->title }}</h2>

                @if($project->supervisor_name)
                <p class="project-hero-subtitle">
                    <i class="fas fa-user-tie text-warning me-1"></i>
                    إشراف: <strong>{{ $project->supervisor_name }}</strong> @if($project->supervisor_title) ({{ $project->supervisor_title }}) @endif
                </p>
                @endif
            </div>

            <!-- Main Grid -->
            <div class="row g-4">
                <!-- Main Content Column -->
                <div class="col-lg-8">

                    <!-- Abstract / Summary -->
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-align-right"></i>
                            </div>
                            <h3>نبذة تعريفية عن المشروع (Abstract)</h3>
                        </div>
                        <div style="font-size: 1.05rem; line-height: 1.8; color: rgba(255,255,255,0.92);">
                            {!! nl2br(e($project->summary ?: $project->description)) !!}
                        </div>
                    </div>

                    <!-- Objectives -->
                    @if($project->objectives)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h3>فكرة وأهداف المشروع (Objectives)</h3>
                        </div>
                        <div style="font-size: 1rem; line-height: 1.8; color: rgba(255,255,255,0.9);">
                            {!! nl2br(e($project->objectives)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Details & Specifications -->
                    @if($project->description && $project->summary)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h3>التفاصيل الفنية والمخرجات التطبيقية</h3>
                        </div>
                        <div style="font-size: 1rem; line-height: 1.8; color: rgba(255,255,255,0.9);">
                            {!! nl2br(e($project->description)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Project Poster -->
                    @if($project->poster_url)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3>بوستر المشروع الرسمي (Project Poster)</h3>
                        </div>
                        <div class="poster-card-wrap">
                            <div class="poster-img-frame">
                                <img src="{{ $project->poster_url }}" alt="بوستر {{ $project->title }}">
                            </div>
                            <div class="mt-3 d-flex justify-content-center gap-2">
                                <a href="{{ $project->poster_url }}" download target="_blank" class="btn-view-project" style="padding: 8px 18px;">
                                    <i class="fas fa-download"></i>
                                    <span>تحميل البوستر بدقة عالية</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Video Demo if available -->
                    @if($project->video_url)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <h3>فيديو العرض التوضيحي (Demo Video)</h3>
                        </div>
                        <div class="text-center py-3">
                            <a href="{{ $project->video_url }}" target="_blank" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fas fa-external-link-alt me-2"></i>مشاهدة العرض التجريبي للمشروع
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Team Members -->
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>فريق العمل والطلبة المشاركون ({{ count($project->team_list) }})</h3>
                        </div>

                        <div class="team-grid">
                            @foreach($project->team_list as $member)
                            <div class="team-member-card">
                                <div class="member-avatar">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="member-info">
                                    <h5>{{ $member['name'] ?? 'طالب باحث' }}</h5>
                                    <div class="member-role">{{ $member['role'] ?? 'عضو فريق المشروع' }}</div>
                                    <div class="member-contact-links">
                                        @if(!empty($member['email']))
                                            <a href="mailto:{{ $member['email'] }}" title="مراسلة عبر البريد"><i class="fas fa-envelope"></i></a>
                                        @endif
                                        @if(!empty($member['phone']))
                                            <a href="tel:{{ $member['phone'] }}" title="اتصال هاتفي"><i class="fas fa-phone"></i></a>
                                        @endif
                                        @if(!empty($member['linkedin']))
                                            <a href="{{ $member['linkedin'] }}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Supervisor Profile -->
                    @if($project->supervisor_name)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3>الأستاذ المشرف الأكاديمي</h3>
                        </div>

                        <div class="supervisor-card">
                            <div class="supervisor-avatar">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-1 text-white">{{ $project->supervisor_name }}</h4>
                                <div class="text-warning small fw-bold mb-1">{{ $project->supervisor_title ?: 'مشرف المشروع' }}</div>
                                <div class="text-white-50 small">{{ $project->faculty }} — {{ $project->department }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Sidebar Sticky Column -->
                <div class="col-lg-4">
                    <div class="sidebar-sticky">

                        <!-- Info Card Box -->
                        <div class="info-card-box">
                            <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: #fff;">
                                <i class="fas fa-info-circle text-warning me-1"></i>
                                بطاقة بيانات المشروع
                            </h4>

                            <div class="meta-list">
                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">الكلية</span>
                                        <span class="meta-value">{{ $project->faculty }}</span>
                                    </div>
                                </div>

                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">القسم / التخصص</span>
                                        <span class="meta-value">{{ $project->department }}</span>
                                    </div>
                                </div>

                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">سنة التخرج</span>
                                        <span class="meta-value">{{ $project->graduation_year }}</span>
                                    </div>
                                </div>

                                @if($project->booth_number)
                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-store-alt"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">موقع الجناح في المعرض</span>
                                        <span class="meta-value">جناح رقم {{ $project->booth_number }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($project->project_url)
                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-code-branch"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">رابط المشروع البرمجي</span>
                                        <span class="meta-value">
                                            <a href="{{ $project->project_url }}" target="_blank" class="text-warning text-decoration-underline">GitHub / Live Demo</a>
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Hire / Contact Button -->
                            <button type="button" class="btn-hire-cta" data-bs-toggle="modal" data-bs-target="#contactTeamModal">
                                <i class="fas fa-briefcase"></i>
                                <span>تواصل مع الفريق / طلب توظيف</span>
                            </button>
                        </div>

                        <!-- Dynamic QR Code Card -->
                        <div class="qr-card-wrap">
                            <h4 style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 8px;">
                                <i class="fas fa-qrcode text-warning me-1"></i>
                                رمز QR الخاص بالمشروع
                            </h4>
                            <p class="qr-caption">
                                امسح الرمز من هاتفك للتصفح الفوري أو اطبعه بجانب البوستر في جناح المعرض
                            </p>

                            <div class="qr-image-frame">
                                <img src="{{ $project->qr_code_url }}" alt="QR Code for {{ $project->title }}">
                            </div>

                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ $project->qr_code_url }}" download="QR-{{ Str::slug($project->title) }}.png" target="_blank" class="btn-qr-action">
                                    <i class="fas fa-download"></i>
                                    <span>تحميل الرمز</span>
                                </a>
                                <button class="btn-qr-action" onclick="copyProjectLink()">
                                    <i class="fas fa-link"></i>
                                    <span>نسخ الرابط</span>
                                </button>
                            </div>

                            <!-- Social Share -->
                            <div class="social-share-row">
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($project->title . "\n\n" . $project->share_url) }}" target="_blank" class="btn-social-icon bg-whatsapp" title="واتساب">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($project->share_url) }}" target="_blank" class="btn-social-icon bg-linkedin" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($project->title) }}&url={{ urlencode($project->share_url) }}" target="_blank" class="btn-social-icon bg-twitter" title="X">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode($project->share_url) }}&text={{ urlencode($project->title) }}" target="_blank" class="btn-social-icon bg-telegram" title="تلغرام">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Related Projects -->
            @if(isset($relatedProjects) && $relatedProjects->count() > 0)
            <div class="mt-5 pt-4 border-top border-white border-opacity-10">
                <h3 class="fw-bold text-white mb-4 fs-5">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    مشاريع تخرج أخرى مقترحة
                </h3>
                <div class="row g-3">
                    @foreach($relatedProjects as $rel)
                    <div class="col-md-4">
                        <a href="{{ route('job-fair.public.projects.show', $rel->id) }}" class="glass-box text-decoration-none d-block h-100 p-3" style="margin: 0;">
                            <span class="badge bg-primary bg-opacity-20 text-info border border-info border-opacity-25 rounded-pill mb-2 small">
                                {{ $rel->faculty }}
                            </span>
                            <h5 class="fw-bold text-white fs-6 mb-2">{{ Str::limit($rel->title, 60) }}</h5>
                            <div class="text-white-50 small">
                                <i class="fas fa-user-tie text-warning me-1"></i> {{ $rel->supervisor_name ?? 'جامعة طرابلس' }}
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Footer -->
        <footer class="projects-footer">
            <div class="container text-center">
                <p class="mb-1 fw-bold text-white">مكتب تدريب الخريجين — جامعة طرابلس &bull; الراعي الاستراتيجي: <span class="text-warning">شركة الواحة لتنظيم المعارض والمؤتمرات</span></p>
                <p class="mb-0">من الجامعة إلى سوق العمل... منصة مشاريع التخرج والابتكار الطلابي.</p>
            </div>
        </footer>

    </div>

    <!-- Modal: Contact/Hire Team -->
    <div class="modal fade" id="contactTeamModal" tabindex="-1" aria-labelledby="contactTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="contactTeamModalLabel">
                        <i class="fas fa-handshake me-2"></i>تواصل مع فريق المشروع
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        يمكن للشركات وأصحاب الأعمال إرسال استفسار مباشر أو طلب مقابلة توظيف لأعضاء فريق مشروع <strong>«{{ $project->title }}»</strong>:
                    </p>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">اسم الجهة أو الشركة</label>
                        <input type="text" id="senderCompany" class="form-control" placeholder="اسم شركتك أو المؤسسة...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">البريد الإلكتروني للتواصل</label>
                        <input type="email" id="senderEmail" class="form-control" placeholder="name@company.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">الرسالة أو الفرصة المقترحة</label>
                        <textarea id="senderMsg" class="form-control" rows="3" placeholder="نود مقابلتكم لمناقشة فرصة تدريب/توظيف أو تبني فكرة المشروع..."></textarea>
                    </div>

                    <div class="alert alert-info py-2 small mb-0">
                        <i class="fas fa-info-circle me-1"></i> سيتم توجيه رسالتكم ومشاركتها مع كافة أعضاء الفريق والمشرف الأكاديمي.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-success fw-bold" onclick="alert('شكراً لتواصلكم! تم استلام رسالتكم بنجاح وسيتواصل معكم فريق المشروع في أقرب وقت.'); bootstrap.Modal.getInstance(document.getElementById('contactTeamModal')).hide();">
                        <i class="fas fa-paper-plane me-1"></i>إرسال الرسالة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-copied" id="copyToast">
        <i class="fas fa-check-circle"></i>
        <span>تم نسخ رابط المشروع بنجاح!</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyProjectLink() {
            const link = "{{ $project->share_url }}";
            navigator.clipboard.writeText(link).then(() => {
                const toast = document.getElementById('copyToast');
                toast.style.display = 'flex';
                setTimeout(() => { toast.style.display = 'none'; }, 3000);
            }).catch(err => {
                prompt("انسخ الرابط التالي:", link);
            });
        }
    </script>
</body>
</html>
