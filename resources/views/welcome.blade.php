<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مكتب تدريب الخريجين - جامعة طرابلس</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0d3882;
            --primary-hover: #0a2d69;
            --secondary-blue: #1565c0;
            --accent-azure: #1e88e5;
            --light-blue: #eff6ff;
            --gold-accent: #f59e0b;
            --gold-hover: #d97706;
            --light-bg: #f8fafc;
            --dark-navy: #0b1f3a;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bento-radius: 20px;
        }

        * {
            font-family: 'Tajawal', sans-serif;
            box-sizing: border-box;
        }

        html {
            /* تم إيقاف التمرير السلس العام لمنع ارتعاش عجلة الماوس وتعارضه مع الهيدر العائم و AOS */
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* 1. الشريط العلوي العائم الفاخر (Floating Glassmorphic Navbar) */
        .home-navbar-wrapper {
            position: fixed;
            top: 14px;
            left: 0;
            right: 0;
            z-index: 1050;
            padding: 0 1.25rem;
            pointer-events: none;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            will-change: transform;
        }

        .home-navbar {
            pointer-events: auto;
            max-width: 1260px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 22px;
            box-shadow: 0 10px 30px -5px rgba(11, 31, 58, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.55);
            padding: 0.55rem 1.25rem;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            transition: background-color 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .home-navbar.scrolled {
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 14px 40px -5px rgba(11, 31, 58, 0.12), 0 0 0 1px rgba(203, 213, 225, 0.75);
            border-color: rgba(255, 255, 255, 0.95);
        }

        .navbar-brand-wrap {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
        }

        .navbar-brand-logo-frame {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: #ffffff;
            padding: 2.5px;
            border: 1.5px solid rgba(245, 158, 11, 0.45);
            box-shadow: 0 4px 14px rgba(13, 56, 130, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            flex-shrink: 0;
        }

        .navbar-brand-wrap:hover .navbar-brand-logo-frame {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.28);
        }

        .navbar-brand-logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .brand-text-title {
            font-weight: 800;
            font-size: 0.96rem;
            color: #0b1f3a;
            line-height: 1.2;
            letter-spacing: -0.2px;
            margin: 0;
        }

        .brand-text-subtitle {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 1px;
        }

        .brand-dot-pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.22);
            display: inline-block;
        }

        .nav-link-custom {
            font-weight: 600;
            color: #475569 !important;
            padding: 0.5rem 0.95rem !important;
            border-radius: 12px;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            font-size: 0.92rem;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-custom:hover {
            color: #0d3882 !important;
            background: rgba(13, 56, 130, 0.06);
            transform: translateY(-1px);
        }

        .nav-link-custom.active {
            color: #0d3882 !important;
            background: rgba(13, 56, 130, 0.08);
            font-weight: 700;
        }

        .nav-badge-pill {
            font-size: 0.65rem;
            padding: 1px 7px;
            border-radius: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
        }

        .nav-btn-login {
            background: rgba(13, 56, 130, 0.05);
            color: #0d3882 !important;
            border: 1px solid rgba(13, 56, 130, 0.16);
            font-weight: 700;
            font-size: 0.86rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .nav-btn-login:hover {
            background: #0d3882;
            color: #ffffff !important;
            border-color: #0d3882;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 56, 130, 0.22);
        }

        .nav-btn-login:hover i {
            color: #ffffff !important;
        }

        .nav-btn-register {
            background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25);
            font-weight: 700;
            font-size: 0.86rem;
            padding: 0.5rem 1.35rem;
            border-radius: 50px;
            box-shadow: 0 6px 18px rgba(13, 56, 130, 0.25);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            position: relative;
            overflow: hidden;
        }

        .nav-btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.22), transparent);
            transform: skewX(-20deg);
            transition: left 0.75s ease;
        }

        .nav-btn-register:hover::before {
            left: 140%;
        }

        .nav-btn-register:hover {
            background: linear-gradient(135deg, #0a2d69 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 56, 130, 0.35);
        }

        .nav-btn-dashboard {
            background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%);
            color: #ffffff !important;
            border-radius: 50px;
            padding: 0.5rem 1.35rem;
            font-weight: 700;
            font-size: 0.86rem;
            box-shadow: 0 6px 18px rgba(13, 56, 130, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.25s ease;
        }

        .nav-btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(13, 56, 130, 0.35);
        }

        .navbar-toggler-custom {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: rgba(13, 56, 130, 0.06);
            border: 1px solid rgba(13, 56, 130, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: #0d3882;
            transition: all 0.2s ease;
        }

        .navbar-toggler-custom:hover,
        .navbar-toggler-custom:focus {
            background: rgba(13, 56, 130, 0.12);
            box-shadow: none;
            outline: none;
        }

        @media (max-width: 991.98px) {
            .home-navbar-wrapper {
                top: 8px;
                padding: 0 0.65rem;
            }
            .home-navbar {
                border-radius: 18px;
                padding: 0.5rem 0.9rem;
            }
            .home-navbar .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 16px;
                margin-top: 10px;
                padding: 1rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                border: 1px solid #f1f5f9;
            }
            .nav-link-custom {
                padding: 0.65rem 1rem !important;
                justify-content: center;
            }
        }

        /* 2. قسم البطل (Hero Section) */
        .hero-section {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 155px 0 120px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 85% 15%, rgba(255, 255, 255, 0.15) 0%, transparent 60%),
                        radial-gradient(circle at 15% 85%, rgba(245, 158, 11, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }

        .university-logo-img {
            width: 125px;
            height: 125px;
            border: 4px solid var(--gold-accent);
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            background: white;
            padding: 4px;
        }

        .btn-gold {
            background: var(--gold-accent);
            color: #0b1f3a !important;
            font-weight: 800;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            border: none;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
        }

        .btn-gold:hover {
            background: #fbbf24;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.45);
        }

        .btn-outline-hero {
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            color: white !important;
            font-weight: 700;
            padding: 0.75rem 1.8rem;
            border-radius: 50px;
            backdrop-filter: blur(8px);
            transition: all 0.25s ease;
        }

        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-blue) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        /* 3. شريط الإحصائيات الحية (Live Stats Ribbon) */
        .stats-ribbon {
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .stat-bento-card {
            background: #ffffff;
            border-radius: var(--bento-radius);
            padding: 1.6rem 1.2rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
            text-align: center;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }

        .stat-bento-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(13, 56, 130, 0.12);
            border-color: #cbd5e1;
        }

        .stat-icon-wrapper {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.9rem;
            font-size: 1.4rem;
        }

        /* 4. بطاقة تسليط الضوء على معرض التوظيف (Job Fair Spotlight) */
        .job-fair-spotlight {
            background: linear-gradient(135deg, #0b1f3a 0%, #1565c0 100%);
            border-radius: 28px;
            color: white;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(11, 31, 58, 0.25);
        }

        .job-fair-spotlight::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        /* 5. بطاقات التدريبات المميزة (Bento Training Cards) */
        .bento-training-card {
            background: #ffffff;
            border-radius: var(--bento-radius);
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .bento-training-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(13, 56, 130, 0.1);
            border-color: #93c5fd;
        }

        .bento-card-header {
            background: linear-gradient(135deg, rgba(13, 56, 130, 0.04) 0%, rgba(21, 101, 192, 0.08) 100%);
            padding: 1.4rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* 6. رحلة الخريج (Journey Steps) */
        .journey-step-card {
            background: white;
            border-radius: 20px;
            padding: 2rem 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 20px rgba(0,0,0,0.03);
            text-align: center;
            position: relative;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
            height: 100%;
        }

        .journey-step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 30px rgba(0,0,0,0.08);
            border-color: #bfdbfe;
        }

        .step-number-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--gold-accent);
            color: #0b1f3a;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* 7. بطاقات الأخبار والإعلانات */
        .bento-news-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .bento-news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }

        /* 8. تذييل الصفحة الشامل (Modern Mega Footer) */
        .main-footer {
            background: var(--dark-navy);
            color: #cbd5e1;
            padding: 70px 0 30px;
            border-top: 4px solid var(--gold-accent);
        }

        .footer-brand-logo-frame {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #ffffff;
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            border: 1.5px solid rgba(245, 158, 11, 0.4);
        }

        .footer-brand-logo-img,
        .navbar-brand-logo {
            width: 100%;
            height: 100%;
            max-width: 44px;
            max-height: 44px;
            object-fit: contain;
            border-radius: 8px;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
            margin-bottom: 0.6rem;
            font-size: 0.92rem;
        }

        .footer-link:hover {
            color: #ffffff;
            transform: translateX(-4px);
        }

        /* 9. نافذة تسجيل الدخول المنبثقة (Login Modal) */
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
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: white;
            padding: 4px;
            border: 3px solid #f59e0b;
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

        /* 10. تنسيقات نافذة تسجيل خريج جديد المنبثقة */
        .modal-register-content {
            border: none;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(13, 56, 130, 0.35);
        }

        .modal-register-header {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
            color: white;
            padding: 26px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 4px solid #f59e0b;
        }

        .modal-register-header h3 {
            font-weight: 800;
            font-size: 1.65rem;
            margin-bottom: 6px;
        }

        .modal-register-body {
            padding: 28px 32px;
            background: #ffffff;
            max-height: calc(85vh - 120px);
            overflow-y: auto;
        }

        .reg-progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 0 20px;
            position: relative;
        }

        .reg-step {
            flex: 1;
            text-align: center;
            position: relative;
            cursor: pointer;
        }

        .reg-step::before {
            content: '';
            position: absolute;
            top: 18px;
            right: 50%;
            width: 100%;
            height: 3px;
            background: #e2e8f0;
            z-index: 0;
            transition: all 0.3s ease;
        }

        .reg-step:first-child::before {
            display: none;
        }

        .reg-step.completed::before,
        .reg-step.active::before {
            background: #1565c0;
        }

        .reg-step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .reg-step.active .reg-step-circle {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(21, 101, 192, 0.35);
        }

        .reg-step.completed .reg-step-circle {
            background: #10b981;
            color: white;
        }

        .reg-step-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 8px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .reg-step.active .reg-step-label {
            color: #1565c0;
            font-weight: 700;
        }

        .reg-section-title {
            color: #1565c0;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 3px solid #f59e0b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-register-footer {
            background: #f8fafc;
            padding: 16px 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 115px 0 90px;
            }
            .job-fair-spotlight {
                padding: 2rem 1.5rem;
            }
            .stats-ribbon {
                margin-top: -45px;
            }
            .modal-register-body {
                padding: 20px 16px;
            }
            .reg-step-label {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 110px 0 80px;
            }
        }
    </style>
</head>

<body>

    <!-- ==================== 1. الشريط العلوي العائم الفاخر (Floating Glassmorphic Navbar) ==================== -->
    <header class="home-navbar-wrapper">
        <nav class="navbar navbar-expand-lg home-navbar">
            <div class="container-fluid px-1 px-lg-2">
                <!-- الشعار والهوية -->
                <a class="navbar-brand navbar-brand-wrap" href="{{ route('home') }}">
                    <div class="navbar-brand-logo-frame">
                        <img src="{{ asset('images/logo.jpg') }}" alt="شعار جامعة طرابلس" class="navbar-brand-logo-img" onerror="this.onerror=null;">
                    </div>
                    <div class="d-flex flex-column text-start text-rtl">
                        <span class="brand-text-title">مكتب تدريب الخريجين</span>
                        <div class="brand-text-subtitle">
                            <span class="brand-dot-pulse"></span>
                            <span>جامعة طرابلس</span>
                        </div>
                    </div>
                </a>

                <!-- زر القائمة للأجهزة الصغيرة -->
                <button class="navbar-toggler navbar-toggler-custom" type="button" data-bs-toggle="collapse" data-bs-target="#homeNavContent" aria-controls="homeNavContent" aria-expanded="false" aria-label="تبديل القائمة">
                    <i class="fas fa-bars-staggered fs-6"></i>
                </button>

                <!-- الروابط وأزرار الإجراءات -->
                <div class="collapse navbar-collapse" id="homeNavContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 text-center py-2 py-lg-0">
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom active" href="#hero">
                                <i class="fas fa-home-alt small opacity-75"></i>
                                <span>الرئيسية</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="#trainings">
                                <span>البرامج التدريبية</span>
                            </a>
                        </li>
                        @if(isset($activeFair) && $activeFair && !$activeFair->is_ended)
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="#jobfair">
                                    <span>الفعاليات</span>
                                    @php
                                        $fairYear = $activeFair->event_date ? \Carbon\Carbon::parse($activeFair->event_date)->format('Y') : date('Y');
                                    @endphp
                                    @if(isset($activeFairs) && $activeFairs->count() > 1)
                                        <span class="nav-badge-pill">{{ $activeFairs->count() }}</span>
                                    @else
                                        <span class="nav-badge-pill">{{ $fairYear }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="#journey">
                                <span>رحلة الخريج</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="#news">
                                <span>الأخبار والإعلانات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="#about">
                                <span>أهدافنا</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-custom" href="#contact">
                                <span>تواصل معنا</span>
                            </a>
                        </li>
                    </ul>

                    <!-- أزرار الإجراءات الفخمة المتناسقة -->
                    <div class="d-flex align-items-center justify-content-center gap-2 pt-2 pt-lg-0">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn nav-btn-dashboard shadow-sm">
                                <i class="fas fa-th-large text-warning"></i>
                                <span>لوحة التحكم</span>
                                <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        @else
                            <button type="button" class="btn nav-btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-arrow-right-to-bracket"></i>
                                <span>تسجيل الدخول</span>
                            </button>
                            <button type="button" class="btn nav-btn-register shadow-sm" data-bs-toggle="modal" data-bs-target="#graduateRegisterModal">
                                <i class="fas fa-user-plus text-warning"></i>
                                <span>تسجيل خريج</span>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- ==================== 2. قسم البطل الرئيسي (Hero Section) ==================== -->
    <section class="hero-section" id="hero">
        <div class="container">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-4" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.32); backdrop-filter: blur(8px);">
                <i class="fas fa-university text-warning"></i>
                <span class="fw-bold" style="font-size: 0.85rem;">جامعة طرابلس — المنصة لتدريب وتأهيل الخريجين</span>
            </div>

            <div class="mb-4" data-aos="zoom-in">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار جامعة طرابلس" class="university-logo-img" onerror="this.onerror=null;">
            </div>

            <h1 class="display-4 fw-bolder mb-3 text-white" data-aos="fade-up" data-aos-delay="150" style="letter-spacing: -0.5px;">
                مكتب تدريب الخريجين
            </h1>

            <p class="lead text-white-50 mx-auto mb-4 px-3" style="max-width: 720px; font-size: 1.15rem; line-height: 1.8;" data-aos="fade-up" data-aos-delay="250">
                جسركم الموثوق من مقاعد الدراسة إلى آفاق سوق العمل، عبر مسارات تدريبية احترافية معتمدة، شراكات استراتيجية مع كبرى الشركات، ومعارض توظيف سنوية رائدة.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="350">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg shadow-sm d-flex align-items-center gap-2">
                        <i class="fas fa-th-large"></i>
                        <span>الانتقال للوحة التحكم</span>
                        <i class="fas fa-arrow-left ms-1 small"></i>
                    </a>
                @else
                    <button type="button" class="btn btn-gold btn-lg shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>تسجيل الدخول</span>
                    </button>
                    <button type="button" class="btn btn-outline-hero btn-lg d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#graduateRegisterModal">
                        <i class="fas fa-user-plus"></i>
                        <span>إنشاء حساب خريج جديد</span>
                    </button>
                @endauth
                <a href="#trainings" class="btn btn-outline-hero btn-lg d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap"></i>
                    <span>تصفح التدريبات</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== 3. شريط الإحصائيات الحية (Live Stats Ribbon) ==================== -->
    @if(!isset($statsSettings['is_ribbon_visible']) || $statsSettings['is_ribbon_visible'])
        <div class="container stats-ribbon">
            <div class="row g-3 justify-content-center">
                @if(isset($statsSettings['cards']))
                    @foreach($statsSettings['cards'] as $key => $card)
                        @if($card['is_visible'])
                            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="stat-bento-card">
                                    <div class="stat-icon-wrapper" style="background: {{ $card['bg_color'] }}; color: {{ $card['color'] }};">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </div>
                                    <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">
                                        {{ $card['prefix'] }}{{ number_format($card['value']) }}
                                    </h3>
                                    <p class="text-muted small mb-0 fw-semibold">{{ $card['label'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-bento-card">
                            <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">{{ number_format($stats['graduates_count'] ?? 0) }}</h3>
                            <p class="text-muted small mb-0 fw-semibold">خريج مسجل ومعتمد</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-bento-card">
                            <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">{{ number_format($stats['companies_count'] ?? 0) }}</h3>
                            <p class="text-muted small mb-0 fw-semibold">شركة ومؤسسة شريكة</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-bento-card">
                            <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">{{ number_format($stats['trainings_count'] ?? 0) }}</h3>
                            <p class="text-muted small mb-0 fw-semibold">برنامج تدريبي وتأهيلي</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-bento-card">
                            <div class="stat-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <h3 class="fw-bolder text-dark mb-1 fs-4 font-monospace">{{ number_format($stats['opportunities_count'] ?? 0) }}</h3>
                            <p class="text-muted small mb-0 fw-semibold">فرصة عمل وترشيح</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- ==================== 4. الفعاليات والمعارض النشطة (Job Fairs Showcase) ==================== -->
    @if(isset($activeFairs) && $activeFairs->count() > 0)
    <section class="py-5" id="jobfair">
        <div class="container">
            @if($activeFairs->count() > 1)
                <div class="text-center mb-4" data-aos="fade-up">
                    <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill mb-2 shadow-xs" style="background: rgba(13, 56, 130, 0.08); border: 1px solid rgba(13, 56, 130, 0.18);">
                        <i class="fas fa-calendar-star text-primary"></i>
                        <span class="text-primary fw-bold small">الفعاليات والمعارض المعتمدة — جامعة طرابلس</span>
                    </div>
                    <h2 class="display-6 fw-bold text-dark mb-2">الفعاليات والمعارض النشطة</h2>
                    <p class="text-muted mx-auto" style="max-width: 620px; font-size: 0.95rem;">
                        استكشف أحدث المعارض والملتقيات الوظيفية المنظمة، واحجز بطاقتك الرقمية المعتمدة لحضور كل فعالية.
                    </p>
                </div>
            @endif

            <div class="d-flex flex-column gap-4">
                @foreach($activeFairs as $fair)
                    <div class="job-fair-spotlight" data-aos="fade-up">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4);">
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="text-warning fw-bold small">حدث سنوي معتمد — جامعة طرابلس</span>
                                </div>

                                <h2 class="display-6 fw-bold mb-3 text-white">
                                    {{ $fair->title }}
                                </h2>
                                <p class="text-white-50 mb-4" style="font-size: 1.05rem; line-height: 1.7; max-width: 680px;">
                                    {{ $fair->description ?? 'فرصتكم الذهبية للقاء مسؤولي الموارد البشرية بكبرى الشركات الوطنية والدولية، إجراء المقابلات الفورية، حجز بطاقتكم الرقمية الذكية المزودة برمز QR، واستكشاف أحدث الوظائف.' }}
                                </p>
                                <div class="d-flex align-items-center gap-3 text-white-50 small mb-4 flex-wrap">
                                    <span><i class="fas fa-map-marker-alt text-warning me-1"></i> {{ $fair->location ?? 'الحرم الجامعي — جامعة طرابلس' }}</span>
                                    <span>•</span>
                                    <span><i class="fas fa-calendar-alt text-warning me-1"></i> الموعد: {{ $fair->event_date ? \Carbon\Carbon::parse($fair->event_date)->translatedFormat('d F Y') : 'يحدد لاحقاً' }}</span>
                                    <span>•</span>
                                    <span><i class="fas fa-qrcode text-warning me-1"></i> تذاكر وبطاقات ذكية موحدة</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <a href="{{ route('job-fair.public', $fair->id) }}" class="btn btn-gold fw-bold rounded-pill px-4 py-2.5 d-flex align-items-center gap-2">
                                        <span>استكشف المعرض والشركات</span>
                                        <i class="fas fa-arrow-left small"></i>
                                    </a>
                                    @if($fair->registration_open)
                                        <a href="{{ route('job-fair.public', $fair->id) }}#register" class="btn btn-outline-hero rounded-pill px-4 py-2.5">
                                            <i class="fas fa-ticket-alt me-1.5"></i>حجز بطاقة المعرض
                                        </a>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2 text-white-50" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">
                                            <i class="fas fa-clock me-1 text-warning"></i>التسجيل متاح قريباً
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px);">
                                    @if($fair->white_logo_url)
                                        <img src="{{ $fair->white_logo_url }}" alt="{{ $fair->title }}" class="img-fluid mb-3" style="max-height: 85px; max-width: 180px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.25));" onerror="this.onerror=null;this.src='{{ $fair->logo_url }}';">
                                    @elseif($fair->logo_url)
                                        <div class="d-inline-block bg-white rounded-3 p-2 mb-3 shadow-sm">
                                            <img src="{{ $fair->logo_url }}" alt="{{ $fair->title }}" class="img-fluid" style="max-height: 70px; max-width: 140px; object-fit: contain;" onerror="this.onerror=null;this.style.display='none'">
                                        </div>
                                    @elseif($fair->banner_image)
                                        <img src="{{ asset('storage/' . $fair->banner_image) }}" alt="{{ $fair->title }}" class="img-fluid mb-3 rounded-3" style="max-height: 70px; object-fit: contain;" onerror="this.onerror=null;this.style.display='none'">
                                    @else
                                        <i class="fas fa-id-card fa-4x text-warning mb-3"></i>
                                    @endif
                                    <h5 class="text-white fw-bold mb-1">بطاقتك الرقمية للفعالية</h5>
                                    <p class="text-white-50 small mb-3">رمز QR موحد لتسجيل الحضور وتسهيل التواصل مع أجنحة الشركات</p>
                                    @if($fair->registration_open)
                                        <span class="badge rounded-pill px-3 py-1.5 text-white" style="background: rgba(16, 185, 129, 0.25); border: 1px solid rgba(16, 185, 129, 0.4);">
                                            <i class="fas fa-check-circle me-1 text-success"></i>التسجيل متاح لجميع الخريجين
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1.5 text-white-50" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                                            <i class="fas fa-info-circle me-1 text-warning"></i>تصدر البطاقة آلياً فور التسجيل
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ==================== 5. معرض الصور والفعاليات (Media Carousel) ==================== -->
    @if(isset($welcomeImages) && $welcomeImages->count() > 0)
        <section class="py-4">
            <div class="container">
                <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-4 overflow-hidden shadow-sm" style="border: 1px solid #e2e8f0;">
                        @foreach($welcomeImages as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image->file_path) }}" class="d-block w-100 object-fit-cover"
                                    alt="{{ $image->caption ?? 'صورة ترحيبية' }}" style="height: 380px;"
                                    onerror="this.onerror=null;this.src='{{ asset('images/logo.jpg') }}'">
                                @if($image->caption)
                                    <div class="carousel-caption d-none d-md-block p-3 rounded-3" style="background: rgba(11, 31, 58, 0.65); backdrop-filter: blur(6px); max-width: 600px; margin: 0 auto;">
                                        <h5 class="text-white mb-0 fw-bold">{{ $image->caption }}</h5>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($welcomeImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon p-3 rounded-circle" style="background-color: rgba(0,0,0,0.4);" aria-hidden="true"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon p-3 rounded-circle" style="background-color: rgba(0,0,0,0.4);" aria-hidden="true"></span>
                            <span class="visually-hidden">التالي</span>
                        </button>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- ==================== 6. البرامج التدريبية المتاحة (Trainings Grid) ==================== -->
    @php
        $totalTrainings = $advertisedTrainings->count();
    @endphp

    @if($advertisedTrainings->count() > 0)
        <section class="py-5" id="trainings">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-down">
                    <span class="badge rounded-pill px-3 py-1.5 small fw-bold mb-2" style="background: #eff6ff; color: #1565c0; border: 1px solid #bfdbfe;">
                        <i class="fas fa-chalkboard-teacher me-1"></i>بناء القدرات والكفاءات
                    </span>
                    <h2 class="display-6 fw-bold text-dark mb-2">أحدث البرامج والدورات التدريبية</h2>
                    <p class="text-muted small mx-auto" style="max-width: 600px;">برامج معتمدة من جامعة طرابلس تستهدف سد الفجوة بين المناهج الأكاديمية واحتياجات سوق العمل الحقيقية.</p>
                </div>

                <div class="row g-4">
                    @foreach($advertisedTrainings as $index => $training)
                        <div class="col-lg-4 col-md-6 training-card {{ $index >= 3 ? 'hidden-training' : '' }}"
                            data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}" style="{{ $index >= 3 ? 'display: none;' : '' }}">
                            <div class="bento-training-card">
                                <div class="bento-card-header d-flex justify-content-between align-items-center">
                                    <span class="badge rounded-pill px-2.5 py-1 small fw-bold" style="background: #eff6ff; color: #1565c0; border: 1px solid #bfdbfe;">
                                        <i class="fas fa-certificate me-1 text-warning"></i>برنامج معتمد
                                    </span>
                                    <small class="text-muted"><i class="fas fa-clock me-1 text-warning"></i>{{ $training->duration }} أيام</small>
                                </div>
                                <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                    <h5 class="fw-bold text-dark mb-2 fs-6">{{ $training->title }}</h5>
                                    <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.6;">
                                        {{ Str::limit(strip_tags($training->description), 130) }}
                                    </p>
                                    <div class="p-2.5 rounded-3 bg-light mb-3 text-muted small d-flex justify-content-between align-items-center" style="font-size: 0.8rem;">
                                        <span><i class="fas fa-calendar-alt text-primary me-1"></i> {{ \Carbon\Carbon::parse($training->start_date)->format('Y/m/d') }}</span>
                                        <span><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $training->location ?? 'جامعة طرابلس' }}</span>
                                    </div>
                                    @auth
                                        <a href="{{ route('graduate.trainings.show', $training->id) }}" class="btn btn-outline-primary rounded-3 w-100 fw-bold py-2">
                                            <span>تفاصيل التدريب والتسجيل</span>
                                            <i class="fas fa-arrow-left ms-1 small"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-outline-primary rounded-3 w-100 fw-bold py-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                            <span>سجل دخولك للتقديم</span>
                                            <i class="fas fa-arrow-left ms-1 small"></i>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($totalTrainings > 3)
                    <div class="text-center mt-4 pt-2">
                        <button id="showMoreBtn" class="btn btn-gold rounded-pill px-4 py-2.5 shadow-sm" onclick="showMoreTrainings()">
                            <span id="btnText">
                                <i class="fas fa-plus-circle me-1.5"></i>
                                عرض المزيد من التدريبات ({{ $totalTrainings - 3 }} تدريب إضافي)
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- ==================== 7. رحلة الخريج (How It Works) ==================== -->
    <section class="py-5 bg-white border-top border-bottom" id="journey">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-down">
                <span class="badge rounded-pill px-3 py-1.5 small fw-bold mb-2" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                    <i class="fas fa-road me-1"></i>مسارك نحو المستقبل
                </span>
                <h2 class="display-6 fw-bold text-dark mb-2">رحلة الخريج في 4 خطوات بسيطة</h2>
                <p class="text-muted small mx-auto" style="max-width: 600px;">صممنا المنصة لترافقك خطوة بخطوة منذ يوم تخرجك وحتى حصولك على وظيفتك الأولى.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="journey-step-card">
                        <div class="step-number-badge">1</div>
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: rgba(37,99,235,0.1); color: #2563eb; font-size: 1.5rem;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">التسجيل الأكاديمي</h5>
                        <p class="text-muted small mb-0">أنشئ حسابك ووثق بيانات كليتك، تخصصك، معدلك التراكمي، ومهاراتك الشخصية.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="journey-step-card">
                        <div class="step-number-badge">2</div>
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: rgba(16,185,129,0.1); color: #10b981; font-size: 1.5rem;">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">التأهيل والتدريب</h5>
                        <p class="text-muted small mb-0">التحق بورش العمل والدورات المكثفة واحصل على شهادات حضور معتمدة ومسجلة.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="journey-step-card">
                        <div class="step-number-badge">3</div>
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: rgba(245,158,11,0.1); color: #f59e0b; font-size: 1.5rem;">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">الترشيح الوظيفي</h5>
                        <p class="text-muted small mb-0">يرشحك المكتب مباشرة للوظائف المناسبة لدى الشركات الشريكة حسب كفاءتك وتخصصك.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="journey-step-card">
                        <div class="step-number-badge">4</div>
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 58px; height: 58px; background: rgba(139,92,246,0.1); color: #8b5cf6; font-size: 1.5rem;">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">المقابلات والتوظيف</h5>
                        <p class="text-muted small mb-0">تابع مواعيد مقابلاتك عبر لوحة التحكم، وتفاعل في معارض التوظيف السنوية.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 8. الأخبار والإعلانات (News & Announcements) ==================== -->
    @if($latestNews->count() > 0 || $activeAnnouncements->count() > 0)
        <section class="py-5 bg-light" id="news">
            <div class="container">
                <div class="row g-4">
                    <!-- الأخبار -->
                    @if($latestNews->count() > 0)
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h4 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                                    <i class="fas fa-newspaper text-primary"></i>
                                    <span>آخر الأخبار والفعاليات</span>
                                </h4>
                            </div>
                            <div class="row g-3">
                                @foreach($latestNews as $news)
                                    <div class="col-12">
                                        <a href="{{ route('public.news.show', $news) }}" class="text-decoration-none d-block">
                                            <div class="bento-news-card p-3 p-md-4 transition-all" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                                <div class="d-flex gap-3 align-items-start">
                                                    @if($news->thumbnail_url)
                                                        <img src="{{ $news->thumbnail_url }}" class="rounded-3 flex-shrink-0 object-fit-cover" alt="" style="width: 84px; height: 84px;" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center text-primary\' style=\'width: 84px; height: 84px; background: rgba(13, 56, 130, 0.08);\'><i class=\'fas fa-newspaper fs-3 opacity-50\'></i></div>';">
                                                    @else
                                                        <div class="rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center text-primary" style="width: 84px; height: 84px; background: rgba(13, 56, 130, 0.08);">
                                                            <i class="fas fa-newspaper fs-3 opacity-50"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <h6 class="fw-bold text-dark mb-1 fs-6" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;" title="{{ $news->title }}">{{ $news->title }}</h6>
                                                        <p class="text-muted small mb-2" style="font-size: 0.84rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                            {{ $news->excerpt }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                                                {{ $news->published_at ? $news->published_at->format('Y/m/d') : '' }}
                                                            </small>
                                                            <span class="small fw-bold text-primary">
                                                                قراءة الخبر <i class="fas fa-arrow-left ms-1" style="font-size: 0.7rem;"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- الإعلانات والتعميمات -->
                    @if($activeAnnouncements->count() > 0)
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h4 class="fw-bold text-dark mb-0 fs-5 d-flex align-items-center gap-2">
                                    <i class="fas fa-bullhorn text-warning"></i>
                                    <span>التعميمات والإعلانات الهامة</span>
                                </h4>
                            </div>
                            <div class="row g-3">
                                @foreach($activeAnnouncements as $announcement)
                                    <div class="col-12">
                                        <a href="{{ route('public.announcements.show', $announcement) }}" class="text-decoration-none d-block">
                                            <div class="bento-news-card p-3 p-md-4 transition-all" style="border-right: 4px solid var(--gold-accent); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                                <h6 class="fw-bold text-dark mb-1 fs-6" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;" title="{{ $announcement->title }}">{{ $announcement->title }}</h6>
                                                <p class="text-muted small mb-2" style="font-size: 0.84rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                    {{ $announcement->excerpt }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-clock me-1 text-warning"></i>
                                                        {{ $announcement->created_at->diffForHumans() }}
                                                    </small>
                                                    <span class="small fw-bold text-primary">
                                                        التفاصيل الكاملة <i class="fas fa-arrow-left ms-1" style="font-size: 0.7rem;"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- ==================== 9. أهدافنا وقيمنا (About Section) ==================== -->
    <section class="py-5 bg-white" id="about">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-down">
                <span class="badge rounded-pill px-3 py-1.5 small fw-bold mb-2" style="background: #eff6ff; color: #1565c0; border: 1px solid #bfdbfe;">
                    <i class="fas fa-bullseye me-1"></i>رؤيتنا ورسالتنا
                </span>
                <h2 class="display-6 fw-bold text-dark mb-2">أهداف مكتب تدريب الخريجين</h2>
                <p class="text-muted small mx-auto" style="max-width: 600px;">نسعى لتحقيق التميز الوطني في تدريب وتأهيل الكفاءات الليبية الشابة لدخول سوق العمل بجدارة.</p>
            </div>

            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-4 border rounded-4 shadow-sm h-100" style="background: #f8fafc;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: rgba(21, 101, 192, 0.1); color: #1565c0; font-size: 1.6rem;">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">تطوير مهني احترافي</h5>
                        <p class="text-muted small mb-0">تقديم حزم تدريبية دورية تركز على المهارات التقنية، القيادية، واللغوية المطلوبة من أرباب العمل.</p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-4 border rounded-4 shadow-sm h-100" style="background: #f8fafc;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.6rem;">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">شراكات عمل استراتيجية</h5>
                        <p class="text-muted small mb-0">بناء مذكرات تفاهم وشراكات مستدامة مع القطاعين العام والخاص لتسهيل استيعاب الخريجين في برامج تدريب وتوظيف.</p>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-4 border rounded-4 shadow-sm h-100" style="background: #f8fafc;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 1.6rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">متابعة الأثر والتقييم المستمر</h5>
                        <p class="text-muted small mb-0">توثيق معدلات التوظيف ونسب نجاح الخريجين واستطلاع آراء الشركات لتحسين مخرجات التعليم الجامعي.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 10. تذييل الصفحة الجامعي الشامل (Mega Footer) ==================== -->
    <footer class="main-footer" id="contact">
        <div class="container">
            <div class="row g-4 pb-4">
                <!-- Column 1: عن المنصة -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2.5 mb-3">
                        <div class="footer-brand-logo-frame">
                            <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس" class="footer-brand-logo-img" onerror="this.onerror=null;">
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0 fs-6">جامعة طرابلس</h5>
                            <small class="text-white-50" style="font-size: 0.78rem;">مكتب تدريب الخريجين</small>
                        </div>
                    </div>
                    <p class="text-white-50 small mb-3" style="line-height: 1.8;">
                        المنصة الإلكترونية الموحدة لربط خريجي جامعة طرابلس بسوق العمل، وتقديم البرامج التدريبية المعتمدة وتنظيم معارض التوظيف السنوية.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://uot.edu.ly" target="_blank" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-globe"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Column 2: روابط سريعة -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6 class="text-white fw-bold mb-3 fs-6">روابط سريعة</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#hero" class="footer-link">الرئيسية</a></li>
                        @if(isset($activeFair) && $activeFair && !$activeFair->is_ended)
                            <li><a href="#jobfair" class="footer-link">الفعاليات والمعارض</a></li>
                        @endif
                        <li><a href="#journey" class="footer-link">رحلة الخريج</a></li>
                        <li><a href="#news" class="footer-link">الأخبار والإعلانات</a></li>
                    </ul>
                </div>

                <!-- Column 3: البوابات والخدمات -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="text-white fw-bold mb-3 fs-6">البوابات والخدمات</h6>
                    <ul class="list-unstyled mb-0">
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#graduateRegisterModal" class="footer-link">تسجيل خريج جديد</a></li>
                        <li><a href="{{ route('company.register') }}" class="footer-link"><i class="fas fa-building me-1 text-warning"></i>تسجيل شركة أو مؤسسة جديدة</a></li>
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#loginModal" class="footer-link">تسجيل الدخول للنظام</a></li>
                        @if(isset($activeFair) && $activeFair && !$activeFair->is_ended)
                            <li><a href="{{ route('job-fair.public', $activeFair->id) }}" class="footer-link">بوابة {{ $activeFair->title }}</a></li>
                        @endif
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" class="footer-link">استعادة كلمة المرور</a></li>
                    </ul>
                </div>

                <!-- Column 4: بيانات التواصل -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3 fs-6">بيانات التواصل</h6>
                    <ul class="list-unstyled text-white-50 small mb-0">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <i class="fas fa-map-marker-alt text-warning mt-1"></i>
                            <a href="https://maps.app.goo.gl/U8z9xwycDn9qAYz47" target="_blank" rel="noopener noreferrer" class="text-white-50 text-decoration-none text-hover-warning" title="عرض موقع مكتب تدريب الخريجين على خرائط جوجل">
                                جامعة طرابلس، سيدي المصري، طرابلس — ليبيا
                                <i class="fas fa-external-link-alt ms-1 text-warning" style="font-size: 0.7rem;"></i>
                            </a>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-envelope text-warning"></i>
                            <span>graduate.training@uot.edu.ly</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-phone-alt text-warning"></i>
                            <span>+218 21 4625500</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="fas fa-clock text-warning"></i>
                            <span>الأحد – الخميس: 8:30 ص – 2:30 م</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-top border-secondary pt-4 mt-2 text-center text-white-50 small d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>© {{ date('Y') }} جامعة طرابلس — جميع الحقوق محفوظة لمكتب تدريب الخريجين.</span>
                <span class="text-warning">منصة الخريجين وسوق العمل</span>
            </div>
        </div>
    </footer>

    <!-- ==================== 11. نافذة تسجيل الدخول المنبثقة (Login Modal) ==================== -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block" onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="loginModalLabel">تسجيل الدخول</h4>
                    <p class="mb-0 text-white-50 small">مكتب تدريب الخريجين — جامعة طرابلس</p>
                </div>
                <div class="modal-login-body">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span class="fw-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span class="fw-semibold">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if($errors->any() && !request('open_forgot') && !request('open_verify') && !request('open_register') && !old('reset_form') && !old('verify_form') && !old('from_register_modal'))
                        <div class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-exclamation-circle flex-shrink-0 fs-6 text-danger"></i>
                            <span class="fw-semibold">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="modalLoginForm">
                        @csrf
                        <div class="mb-3 text-start text-rtl">
                            <label for="modalEmail" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-user-circle text-primary me-1"></i>البريد الإلكتروني أو اسم المستخدم
                            </label>
                            <input type="text" class="form-control modal-login-input @error('email') is-invalid @enderror" id="modalEmail" name="email" value="{{ old('email') }}" placeholder="البريد الإلكتروني أو اسم المستخدم (مثال: admin)" required autofocus>
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="modalPassword" class="form-label fw-bold text-dark small mb-0">
                                    <i class="fas fa-lock text-primary me-1"></i>كلمة المرور
                                </label>
                                <a class="small text-decoration-none fw-bold" href="javascript:void(0)" id="btnOpenForgotModal" style="color: #1565c0; font-size: 0.8rem; cursor: pointer;">
                                    نسيت كلمة المرور؟
                                </a>
                            </div>
                            <div class="position-relative">
                                <input type="password" class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror" id="modalPassword" name="password" placeholder="••••••••" required>
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleModalPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleModalPass"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check mb-3 text-start text-rtl">
                            <input class="form-check-input" type="checkbox" name="remember" id="modalRemember">
                            <label class="form-check-label text-muted small" for="modalRemember">
                                تذكر بياناتي على هذا الجهاز
                            </label>
                        </div>

                        <x-honeypot />
                        <x-turnstile action="login" />

                        <button type="submit" class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                            <i class="fas fa-sign-in-alt"></i><span>دخول إلى النظام</span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-2">
                            خريج جديد ولم تسجل بعد؟
                            <a href="javascript:void(0)" id="btnOpenRegisterFromLogin" class="fw-bold text-decoration-none ms-1" style="color: #1565c0; cursor: pointer;">
                                تسجيل خريج جديد <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        </p>
                        <div class="pt-2 border-top border-light">
                            <p class="text-muted small mb-0">
                                تمثل شركة أو مؤسسة وتبحث عن كفاءات؟
                                <a href="{{ route('company.register') }}" class="fw-bold text-decoration-none ms-1" style="color: #0284c7;">
                                    <i class="fas fa-building me-1"></i>تسجيل حساب شركة شريكة
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 12. نافذة استعادة كلمة المرور المنبثقة (Forgot Password Modal) ==================== -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block" onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="forgotPasswordModalLabel">استعادة كلمة المرور</h4>
                    <p class="mb-0 text-white-50 small">أدخل بريدك الإلكتروني لاستلام رمز التحقق</p>
                </div>
                <div class="modal-login-body">
                    @if($errors->any() && (request('open_forgot') || old('reset_form')))
                        <div class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-exclamation-circle flex-shrink-0 fs-6 text-danger"></i>
                            <span class="fw-semibold">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.code.store') }}" id="modalForgotForm">
                        @csrf
                        <input type="hidden" name="reset_form" value="1">
                        
                        <div class="mb-3 text-start text-rtl">
                            <label for="forgotEmail" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-envelope text-primary me-1"></i>البريد الإلكتروني المسجل
                            </label>
                            <input type="email" class="form-control modal-login-input @error('email') is-invalid @enderror" id="forgotEmail" name="email" value="{{ old('email') }}" placeholder="example@uot.edu.ly" required autofocus>
                            <small class="text-muted" style="font-size: 0.78rem;">سنرسل لك رمز تحقق مكون من 6 أرقام لإعادة تعيين كلمة المرور.</small>
                        </div>

                        <x-honeypot />
                        <x-turnstile action="forgot_password" />

                        <button type="submit" class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mb-3 mt-2">
                            <i class="fas fa-paper-plane"></i><span>إرسال رمز التحقق</span>
                        </button>

                        <div class="text-center pt-2 border-top">
                            <a href="javascript:void(0)" id="btnBackToLoginModal" class="fw-bold text-decoration-none small" style="color: #1565c0; cursor: pointer;">
                                <i class="fas fa-arrow-right me-1"></i>العودة لتسجيل الدخول
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 13. نافذة التحقق وتغيير كلمة المرور المنبثقة (Verify Code & Reset Modal) ==================== -->
    <div class="modal fade" id="verifyCodeModal" tabindex="-1" aria-labelledby="verifyCodeModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block" onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="verifyCodeModalLabel">تغيير كلمة المرور</h4>
                    <p class="mb-0 text-white-50 small">أدخل الرمز المرسل إلى بريدك الإلكتروني</p>
                </div>
                <div class="modal-login-body">
                    @if(session('status_code_sent'))
                        <div class="alert alert-success border-0 rounded-3 py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span>{{ session('status_code_sent') }}</span>
                        </div>
                    @endif

                    @if($errors->any() && (request('open_verify') || old('verify_form')))
                        <div class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-exclamation-circle flex-shrink-0 fs-6 text-danger"></i>
                            <span class="fw-semibold">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.code.update') }}" id="modalVerifyCodeForm">
                        @csrf
                        <input type="hidden" name="verify_form" value="1">
                        @php
                            $safeVerifyEmail = filter_var(request('email'), FILTER_VALIDATE_EMAIL) ? e(request('email')) : (filter_var(old('email'), FILTER_VALIDATE_EMAIL) ? e(old('email')) : '');
                        @endphp
                        <input type="hidden" name="email" id="verifyModalEmailInput" value="{{ $safeVerifyEmail }}">

                        <div class="p-2.5 rounded-3 bg-light border mb-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                <i class="fas fa-envelope text-primary small"></i>
                                <span class="text-muted small">البريد:</span>
                                <strong class="text-dark small text-truncate" id="verifyModalEmailDisplay">{{ $safeVerifyEmail }}</strong>
                            </div>
                            <a href="javascript:void(0)" id="btnChangeEmailInVerify" class="small fw-bold text-decoration-none flex-shrink-0" style="color: #1565c0; font-size: 0.78rem; cursor: pointer;">
                                تغيير
                            </a>
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <label for="verifyCodeInput" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-key text-primary me-1"></i>رمز التحقق (6 أرقام)
                            </label>
                            <input type="text" class="form-control modal-login-input text-center fw-bold fs-5 @error('code') is-invalid @enderror" id="verifyCodeInput" name="code" value="{{ old('code') }}" placeholder="123456" maxlength="6" required autofocus style="letter-spacing: 4px;">
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <label for="verifyNewPassword" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-lock text-primary me-1"></i>كلمة المرور الجديدة
                            </label>
                            <div class="position-relative">
                                <input type="password" class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror" id="verifyNewPassword" name="password" placeholder="••••••••" required>
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleVerifyPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleVerifyPass"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 text-start text-rtl">
                            <label for="verifyConfirmPassword" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-lock text-primary me-1"></i>تأكيد كلمة المرور الجديدة
                            </label>
                            <div class="position-relative">
                                <input type="password" class="form-control modal-login-input pe-5" id="verifyConfirmPassword" name="password_confirmation" placeholder="••••••••" required>
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleVerifyConfirmPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleVerifyConfirmPass"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="fas fa-check-circle"></i><span>تأكيد وتغيير كلمة المرور</span>
                        </button>

                        <div class="text-center pt-2 border-top">
                            <a href="javascript:void(0)" id="btnBackToLoginFromVerify" class="fw-bold text-decoration-none small" style="color: #1565c0; cursor: pointer;">
                                <i class="fas fa-arrow-right me-1"></i>العودة لتسجيل الدخول
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 14. نافذة تسجيل خريج جديد المنبثقة (Graduate Registration Modal) ==================== -->
    <div class="modal fade" id="graduateRegisterModal" tabindex="-1" aria-labelledby="graduateRegisterModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 860px;">
            <div class="modal-content modal-register-content">
                <!-- Header -->
                <div class="modal-register-header position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <h3><i class="fas fa-user-graduate me-2 text-warning"></i> تسجيل خريج جديد</h3>
                    <p class="text-white-50 mb-0 small">انضم إلينا للاستفادة من خدمات التدريب والتأهيل وفرص التوظيف المباشر</p>
                </div>

                <!-- Body -->
                <div class="modal-register-body">
                    <!-- Progress Steps -->
                    <div class="reg-progress-steps">
                        <div class="reg-step active" id="modalStepIndicator1" onclick="switchRegStep(1)">
                            <div class="reg-step-circle">1</div>
                            <div class="reg-step-label">البيانات الشخصية</div>
                        </div>
                        <div class="reg-step" id="modalStepIndicator2" onclick="switchRegStep(2)">
                            <div class="reg-step-circle">2</div>
                            <div class="reg-step-label">البيانات الأكاديمية</div>
                        </div>
                        <div class="reg-step" id="modalStepIndicator3" onclick="switchRegStep(3)">
                            <div class="reg-step-circle">3</div>
                            <div class="reg-step-label">المهارات والخبرات</div>
                        </div>
                    </div>

                    <!-- Errors -->
                    @if ($errors->any() && (request('open_register') || old('from_register_modal')))
                        <div class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small mb-4 shadow-none">
                            <strong class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                                <span>يرجى تصحيح الأخطاء التالية:</span>
                            </strong>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('graduate.register.store') }}" id="modalRegistrationForm">
                        @csrf
                        <input type="hidden" name="from_register_modal" value="1">
                        <x-honeypot />

                        <!-- Step 1: Personal Info -->
                        <div class="reg-step-section" id="regSection1">
                            <h5 class="reg-section-title">
                                <i class="fas fa-user text-primary"></i>
                                <span>البيانات الشخصية</span>
                            </h5>

                            <div class="row g-3 text-start text-rtl">
                                <div class="col-md-6">
                                    <label for="reg_name" class="form-label fw-bold text-dark small mb-1">
                                        الاسم الرباعي <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('name') is-invalid @enderror" id="reg_name"
                                        name="name" value="{{ old('name') }}" required placeholder="الاسم الرباعي كما هو في الشهادة">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_student_id" class="form-label fw-bold text-dark small mb-1">
                                        رقم القيد الجامعي <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('student_id') is-invalid @enderror @error('national_id') is-invalid @enderror"
                                        id="reg_student_id" name="student_id" value="{{ old('student_id', old('national_id')) }}" required
                                        placeholder="رقم القيد بالجامعة (الرقم الجامعي)">
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_email" class="form-label fw-bold text-dark small mb-1">
                                        البريد الإلكتروني <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control modal-login-input @error('email') is-invalid @enderror" id="reg_email"
                                        name="email" value="{{ old('email') }}" required placeholder="example@domain.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_phone" class="form-label fw-bold text-dark small mb-1">
                                        رقم الهاتف <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control modal-login-input @error('phone') is-invalid @enderror" id="reg_phone"
                                        name="phone" value="{{ old('phone') }}" required placeholder="09xxxxxxxx">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_password" class="form-label fw-bold text-dark small mb-1">
                                        كلمة المرور <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror"
                                            id="reg_password" name="password" required minlength="8" placeholder="8 أحرف على الأقل">
                                        <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleRegPass" style="z-index: 5;">
                                            <i class="far fa-eye" id="iconToggleRegPass"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_password_confirmation" class="form-label fw-bold text-dark small mb-1">
                                        تأكيد كلمة المرور <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control modal-login-input pe-5" id="reg_password_confirmation"
                                            name="password_confirmation" required minlength="8" placeholder="أعد كتابة كلمة المرور">
                                        <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0" id="btnToggleRegConfirmPass" style="z-index: 5;">
                                            <i class="far fa-eye" id="iconToggleRegConfirmPass"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_date_of_birth" class="form-label fw-bold text-dark small mb-1">
                                        تاريخ الميلاد <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control modal-login-input @error('date_of_birth') is-invalid @enderror"
                                        id="reg_date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_gender" class="form-label fw-bold text-dark small mb-1">
                                        الجنس <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('gender') is-invalid @enderror" id="reg_gender"
                                        name="gender" required>
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_city" class="form-label fw-bold text-dark small mb-1">
                                        المدينة <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('city') is-invalid @enderror" id="reg_city"
                                        name="city" value="{{ old('city', 'طرابلس') }}" required placeholder="طرابلس">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_address" class="form-label fw-bold text-dark small mb-1">
                                        العنوان بالتفصيل <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('address') is-invalid @enderror"
                                        id="reg_address" name="address" value="{{ old('address') }}" required
                                        placeholder="الحي - الشارع">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-modal-login px-4 d-flex align-items-center gap-2" onclick="switchRegStep(2)">
                                    <span>المتابعة: البيانات الأكاديمية</span>
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Academic Info -->
                        <div class="reg-step-section d-none" id="regSection2">
                            <h5 class="reg-section-title">
                                <i class="fas fa-graduation-cap text-primary"></i>
                                <span>البيانات الأكاديمية</span>
                            </h5>

                            <div class="row g-3 text-start text-rtl">
                                <div class="col-md-6">
                                    <label for="university" class="form-label fw-bold text-dark small mb-1">
                                        الجامعة <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('university') is-invalid @enderror" id="university"
                                        name="university" required>
                                        <option value="جامعة طرابلس" selected>جامعة طرابلس</option>
                                    </select>
                                    @error('university')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="sector" class="form-label fw-bold text-dark small mb-1">
                                        القطاع / القاطع <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('sector') is-invalid @enderror" id="sector"
                                        name="sector" required>
                                        <option value="">اختر القطاع</option>
                                    </select>
                                    <input type="hidden" id="old_sector" value="{{ old('sector') }}">
                                    @error('sector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="faculty" class="form-label fw-bold text-dark small mb-1">
                                        الكلية <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('faculty') is-invalid @enderror" id="faculty"
                                        name="faculty" required disabled>
                                        <option value="">اختر الكلية</option>
                                    </select>
                                    <input type="hidden" id="old_faculty" value="{{ old('faculty') }}">
                                    @error('faculty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="qualification" class="form-label fw-bold text-dark small mb-1">
                                        المؤهل العلمي <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('qualification') is-invalid @enderror"
                                        id="qualification" name="qualification" required>
                                        <option value="">اختر المؤهل</option>
                                        <option value="بكالوريوس" {{ old('qualification', 'بكالوريوس') == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                        <option value="ليسانس" {{ old('qualification') == 'ليسانس' ? 'selected' : '' }}>ليسانس</option>
                                        <option value="ماجستير" {{ old('qualification') == 'ماجستير' ? 'selected' : '' }}>ماجستير</option>
                                        <option value="دكتوراه" {{ old('qualification') == 'دكتوراه' ? 'selected' : '' }}>دكتوراه</option>
                                        <option value="دبلوم عالي" {{ old('qualification') == 'دبلوم عالي' ? 'selected' : '' }}>دبلوم عالي</option>
                                    </select>
                                    @error('qualification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label for="specialization" class="form-label fw-bold text-dark small mb-1">
                                        التخصص الأكاديمي <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('specialization') is-invalid @enderror"
                                        id="specialization" name="specialization" required disabled>
                                        <option value="">اختر التخصص</option>
                                    </select>
                                    <input type="hidden" id="old_specialization" value="{{ old('specialization') }}">
                                    @error('specialization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="graduation_year" class="form-label fw-bold text-dark small mb-1">
                                        سنة التخرج <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control modal-login-input @error('graduation_year') is-invalid @enderror"
                                        id="graduation_year" name="graduation_year" value="{{ old('graduation_year', date('Y')) }}"
                                        required min="1960" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                                    @error('graduation_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="gpa" class="form-label fw-bold text-dark small mb-1">
                                        المعدل التراكمي (%)
                                    </label>
                                    <input type="number" class="form-control modal-login-input @error('gpa') is-invalid @enderror" id="gpa"
                                        name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="100"
                                        placeholder="مثال: 85.50">
                                    @error('gpa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-outline-secondary rounded-3 px-4 d-flex align-items-center gap-2" onclick="switchRegStep(1)">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>السابق: البيانات الشخصية</span>
                                </button>
                                <button type="button" class="btn btn-modal-login px-4 d-flex align-items-center gap-2" onclick="switchRegStep(3)">
                                    <span>المتابعة: المهارات والخبرات</span>
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Skills & Experience -->
                        <div class="reg-step-section d-none" id="regSection3">
                            <h5 class="reg-section-title">
                                <i class="fas fa-briefcase text-primary"></i>
                                <span>المهارات والخبرات</span>
                            </h5>

                            <div class="row g-3 text-start text-rtl">
                                <div class="col-12">
                                    <label for="experiences" class="form-label fw-bold text-dark small mb-1">
                                        الخبرة العملية السابقة (إن وجدت)
                                    </label>
                                    <textarea class="form-control modal-login-input @error('experiences') is-invalid @enderror"
                                        id="experiences" name="experiences" rows="3"
                                        placeholder="اذكر خبراتك العملية السابقة أو مشاريع التخرج والتدريبات الميدانية...">{{ old('experiences') }}</textarea>
                                    @error('experiences')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="skills" class="form-label fw-bold text-dark small mb-1">
                                        المهارات الشخصية والتقنية
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('skills') is-invalid @enderror"
                                        id="skills" name="skills" value="{{ old('skills') }}"
                                        placeholder="مثال: برمجة، تحليل بيانات، إدارة وقت">
                                    <small class="text-muted" style="font-size: 0.78rem;">افصل بين المهارات بفاصلة (,)</small>
                                    @error('skills')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="languages" class="form-label fw-bold text-dark small mb-1">
                                        اللغات المتقنة
                                    </label>
                                    <input type="text" class="form-control modal-login-input @error('languages') is-invalid @enderror"
                                        id="languages" name="languages" value="{{ old('languages', 'العربية، الإنجليزية') }}"
                                        placeholder="العربية، الإنجليزية">
                                    <small class="text-muted" style="font-size: 0.78rem;">افصل بين اللغات بفاصلة (,)</small>
                                    @error('languages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="my-3">
                                <x-turnstile action="register_graduate" />
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-outline-secondary rounded-3 px-4 d-flex align-items-center gap-2" onclick="switchRegStep(2)">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>السابق: البيانات الأكاديمية</span>
                                </button>
                                <button type="submit" class="btn btn-modal-login px-5 d-flex align-items-center gap-2" id="modalSubmitRegisterBtn">
                                    <i class="fas fa-user-plus"></i>
                                    <span>تسجيل حساب جديد</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="modal-register-footer">
                    <p class="mb-0 text-muted small">
                        لديك حساب خريج بالفعل؟
                        <a href="javascript:void(0)" id="btnBackToLoginFromRegister" class="fw-bold text-decoration-none ms-1" style="color: #1565c0; cursor: pointer;">
                            تسجيل الدخول هنا <i class="fas fa-arrow-left ms-1 small"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/university-data.js') }}"></script>
    <script>
        AOS.init({
            duration: 650,
            once: true,
            offset: 50,
            disableMutationObserver: true
        });

        // دالة التبديل بين خطوات التسجيل
        window.switchRegStep = function (step) {
            // إخفاء كافة الأقسام
            document.querySelectorAll('.reg-step-section').forEach(el => el.classList.add('d-none'));
            // إظهار القسم المطلوب
            const targetSection = document.getElementById('regSection' + step);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }

            // تحديث مؤشرات الخطوات
            for (let i = 1; i <= 3; i++) {
                const ind = document.getElementById('modalStepIndicator' + i);
                if (!ind) continue;
                ind.classList.remove('active', 'completed');
                if (i < step) {
                    ind.classList.add('completed');
                } else if (i === step) {
                    ind.classList.add('active');
                }
            }
        };

        // تفعيل النوافذ المنبثقة وإظهار/إخفاء كلمة المرور
        document.addEventListener('DOMContentLoaded', function () {
            // أزرار إظهار وإخفاء كلمات المرور
            function setupEyeToggle(btnId, inputId, iconId) {
                const btn = document.getElementById(btnId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (btn && input && icon) {
                    btn.addEventListener('click', function () {
                        const isPass = input.type === 'password';
                        input.type = isPass ? 'text' : 'password';
                        icon.className = isPass ? 'far fa-eye-slash' : 'far fa-eye';
                    });
                }
            }

            setupEyeToggle('btnToggleModalPass', 'modalPassword', 'iconToggleModalPass');
            setupEyeToggle('btnToggleVerifyPass', 'verifyNewPassword', 'iconToggleVerifyPass');
            setupEyeToggle('btnToggleVerifyConfirmPass', 'verifyConfirmPassword', 'iconToggleVerifyConfirmPass');
            setupEyeToggle('btnToggleRegPass', 'reg_password', 'iconToggleRegPass');
            setupEyeToggle('btnToggleRegConfirmPass', 'reg_password_confirmation', 'iconToggleRegConfirmPass');

            // تهيئة النوافذ المنبثقة الأربع
            const loginModalEl = document.getElementById('loginModal');
            const forgotModalEl = document.getElementById('forgotPasswordModal');
            const verifyModalEl = document.getElementById('verifyCodeModal');
            const registerModalEl = document.getElementById('graduateRegisterModal');

            const loginModal = loginModalEl ? new bootstrap.Modal(loginModalEl) : null;
            const forgotModal = forgotModalEl ? new bootstrap.Modal(forgotModalEl) : null;
            const verifyModal = verifyModalEl ? new bootstrap.Modal(verifyModalEl) : null;
            const registerModal = registerModalEl ? new bootstrap.Modal(registerModalEl) : null;

            // التبديل من تسجيل الدخول إلى استعادة كلمة المرور
            const btnOpenForgot = document.getElementById('btnOpenForgotModal');
            if (btnOpenForgot && forgotModal && loginModalEl) {
                btnOpenForgot.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(loginModalEl)?.hide();
                    setTimeout(() => forgotModal.show(), 350);
                });
            }

            // التبديل من تسجيل الدخول إلى تسجيل خريج جديد
            const btnOpenRegisterFromLogin = document.getElementById('btnOpenRegisterFromLogin');
            if (btnOpenRegisterFromLogin && registerModal && loginModalEl) {
                btnOpenRegisterFromLogin.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(loginModalEl)?.hide();
                    setTimeout(() => registerModal.show(), 350);
                });
            }

            // التبديل من تسجيل خريج جديد إلى تسجيل الدخول
            const btnBackToLoginFromRegister = document.getElementById('btnBackToLoginFromRegister');
            if (btnBackToLoginFromRegister && loginModal && registerModalEl) {
                btnBackToLoginFromRegister.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(registerModalEl)?.hide();
                    setTimeout(() => loginModal.show(), 350);
                });
            }

            // التبديل من استعادة كلمة المرور إلى تسجيل الدخول
            const btnBackToLogin = document.getElementById('btnBackToLoginModal');
            if (btnBackToLogin && loginModal && forgotModalEl) {
                btnBackToLogin.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(forgotModalEl)?.hide();
                    setTimeout(() => loginModal.show(), 350);
                });
            }

            // التبديل من التحقق إلى استعادة كلمة المرور (لتغيير البريد)
            const btnChangeEmail = document.getElementById('btnChangeEmailInVerify');
            if (btnChangeEmail && forgotModal && verifyModalEl) {
                btnChangeEmail.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(verifyModalEl)?.hide();
                    setTimeout(() => forgotModal.show(), 350);
                });
            }

            // التبديل من التحقق إلى تسجيل الدخول
            const btnBackToLoginFromVerify = document.getElementById('btnBackToLoginFromVerify');
            if (btnBackToLoginFromVerify && loginModal && verifyModalEl) {
                btnBackToLoginFromVerify.addEventListener('click', function () {
                    bootstrap.Modal.getInstance(verifyModalEl)?.hide();
                    setTimeout(() => loginModal.show(), 350);
                });
            }

            // عند فتح نافذة التسجيل، التأكد من تحميل القطاعات والكليات
            if (registerModalEl) {
                registerModalEl.addEventListener('shown.bs.modal', function () {
                    if (typeof loadSectors === 'function') {
                        loadSectors();
                    }
                });
            }

            // الفتح التلقائي للنوافذ بحسب رابط الصفحة والأخطاء
            const urlParams = new URLSearchParams(window.location.search);
            const isRegisterRequested = urlParams.has('open_register') || {{ old('from_register_modal') ? 'true' : 'false' }};
            const isVerifyRequested = urlParams.has('open_verify') || {{ old('verify_form') ? 'true' : 'false' }};
            const isForgotRequested = urlParams.has('open_forgot') || {{ old('reset_form') ? 'true' : 'false' }};
            const isLoginRequested = urlParams.has('open_login') || urlParams.has('login');
            const hasSuccess = {{ session('success') ? 'true' : 'false' }};
            const hasStatus = {{ session('status') ? 'true' : 'false' }};
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};

            if (isRegisterRequested) {
                registerModal?.show();
            } else if (isVerifyRequested) {
                verifyModal?.show();
            } else if (isForgotRequested) {
                forgotModal?.show();
            } else if (isLoginRequested || hasErrors || hasStatus || hasSuccess) {
                loginModal?.show();
            }
        });

        // دالة لعرض/إخفاء التدريبات الإضافية
        let trainingsExpanded = false;

        function showMoreTrainings() {
            const hiddenTrainings = document.querySelectorAll('.hidden-training');
            const btnText = document.getElementById('btnText');

            if (!trainingsExpanded) {
                hiddenTrainings.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.display = 'block';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(15px)';
                        card.style.transition = 'all 0.4s ease';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    }, index * 80);
                });

                btnText.innerHTML = '<i class="fas fa-minus-circle me-1.5"></i>إخفاء التدريبات الإضافية';
                trainingsExpanded = true;
            } else {
                hiddenTrainings.forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(15px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 400);
                    }, index * 40);
                });

                const totalTrainings = {{ $totalTrainings ?? 0 }};
                const hiddenCount = totalTrainings - 3;
                btnText.innerHTML = `<i class="fas fa-plus-circle me-1.5"></i>عرض المزيد من التدريبات (${hiddenCount} تدريب إضافي)`;
                trainingsExpanded = false;
            }
        }

        // تأثير التمرير للشريط العلوي العائم (Scroll Elevation مع Hysteresis و rAF لمنع الارتعاش)
        (function() {
            const navbar = document.querySelector('.home-navbar');
            if (!navbar) return;

            let isScrolled = false;
            let ticking = false;

            function updateNavbar() {
                const scrollY = window.pageYOffset || document.documentElement.scrollTop;
                // عتبة التمرير مع Hysteresis لتفادي الارتعاش والتذبذب
                if (!isScrolled && scrollY > 50) {
                    isScrolled = true;
                    navbar.classList.add('scrolled');
                } else if (isScrolled && scrollY < 15) {
                    isScrolled = false;
                    navbar.classList.remove('scrolled');
                }
                ticking = false;
            }

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(updateNavbar);
                    ticking = true;
                }
            }, { passive: true });

            updateNavbar();
        })();

        // التمرير السلس للروابط الداخلية فقط دون التأثير على سلاسة تمرير الماوس
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId && targetId.length > 1) {
                    const targetEl = document.querySelector(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    </script>
</body>

</html>