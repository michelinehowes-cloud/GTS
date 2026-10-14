<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>مكتب تدريب الخريجين - جامعة طرابلس</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap"
        rel="stylesheet">
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

        /* 1. الشريط العلوي الحديث كامل العرض (Modern Dynamic Full-Width Navbar) */
        .home-navbar-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1050;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            background: linear-gradient(180deg, rgba(8, 28, 58, 0.55) 0%, rgba(13, 56, 130, 0.35) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            padding: 1rem 2rem;
        }

        .home-navbar-wrapper.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 10px 30px -5px rgba(11, 31, 58, 0.1), 0 1px 3px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            padding: 0.6rem 2rem;
        }

        .home-navbar {
            max-width: 1440px;
            margin: 0 auto;
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
            width: 100%;
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
            border: 1.5px solid rgba(245, 158, 11, 0.6);
            box-shadow: 0 4px 14px rgba(13, 56, 130, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            flex-shrink: 0;
        }

        .navbar-brand-wrap:hover .navbar-brand-logo-frame {
            transform: scale(1.05) rotate(-2deg);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
        }

        .navbar-brand-logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        /* Adaptive brand text */
        .brand-text-title {
            font-weight: 800;
            font-size: 1rem;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.2px;
            margin: 0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .home-navbar-wrapper.scrolled .brand-text-title {
            color: #0b1f3a;
            text-shadow: none;
        }

        .brand-text-subtitle {
            font-size: 0.74rem;
            color: rgba(255, 255, 255, 0.92);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 1px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .home-navbar-wrapper.scrolled .brand-text-subtitle {
            color: #64748b;
            text-shadow: none;
        }

        .brand-dot-pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.4);
            display: inline-block;
        }

        /* Modern nav links with animated underline (matching snippet) */
        .nav-link-custom {
            font-weight: 700;
            color: #ffffff !important;
            padding: 0.45rem 0.85rem !important;
            font-size: 0.95rem;
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            transition: color 0.25s ease, text-shadow 0.25s ease;
        }

        .home-navbar-wrapper.scrolled .nav-link-custom {
            color: #334155 !important;
            text-shadow: none;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #f59e0b;
            border-radius: 4px;
            transition: width 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .nav-link-custom:hover::after,
        .nav-link-custom.active::after {
            width: 80%;
        }

        .nav-link-custom:hover {
            color: #fbbf24 !important;
            background: transparent !important;
        }

        .home-navbar-wrapper.scrolled .nav-link-custom:hover {
            color: #0d3882 !important;
        }

        .nav-link-custom.active {
            color: #fbbf24 !important;
            background: transparent !important;
        }

        .home-navbar-wrapper.scrolled .nav-link-custom.active {
            color: #0d3882 !important;
        }

        .home-navbar-wrapper.scrolled .nav-link-custom.active::after {
            background: #0d3882;
            box-shadow: none;
        }

        .nav-badge-pill {
            font-size: 0.65rem;
            padding: 1px 7px;
            border-radius: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);
            margin-right: 4px;
        }

        /* Pill Action Buttons */
        .nav-btn-login {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.55);
            font-weight: 700;
            font-size: 0.88rem;
            padding: 0.5rem 1.45rem;
            border-radius: 50px;
            backdrop-filter: blur(8px);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .home-navbar-wrapper.scrolled .nav-btn-login {
            background: rgba(13, 56, 130, 0.06);
            color: #0d3882 !important;
            border: 1.5px solid rgba(13, 56, 130, 0.25);
            text-shadow: none;
        }

        .nav-btn-login:hover {
            background: #ffffff !important;
            color: #0d3882 !important;
            border-color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            text-shadow: none;
        }

        .home-navbar-wrapper.scrolled .nav-btn-login:hover {
            background: #0d3882 !important;
            color: #ffffff !important;
            border-color: #0d3882 !important;
            box-shadow: 0 6px 18px rgba(13, 56, 130, 0.25);
        }

        .nav-btn-register {
            background: rgba(245, 158, 11, 0.15) !important;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            border: 1.5px solid #f59e0b !important;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.2);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .nav-btn-register:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: #0b1f3a !important;
            border-color: #f59e0b !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.45);
            text-shadow: none;
        }

        .nav-btn-register:hover i {
            color: #0b1f3a !important;
        }

        .nav-btn-dashboard {
            background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%);
            color: #ffffff !important;
            border-radius: 50px;
            padding: 0.52rem 1.45rem;
            font-weight: 700;
            font-size: 0.88rem;
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
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: #ffffff;
            transition: all 0.25s ease;
        }

        .home-navbar-wrapper.scrolled .navbar-toggler-custom {
            background: rgba(13, 56, 130, 0.06);
            border-color: rgba(13, 56, 130, 0.18);
            color: #0d3882;
        }

        @media (max-width: 991.98px) {
            .home-navbar-wrapper {
                padding: 0.75rem 1.25rem;
            }

            .home-navbar-wrapper.scrolled {
                padding: 0.6rem 1.25rem;
            }

            .home-navbar .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                padding: 1.25rem 1.5rem;
                margin-top: 0.75rem;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
                border: 1px solid rgba(226, 232, 240, 0.85);
            }

            .home-navbar .navbar-collapse .nav-link-custom {
                color: #1e293b !important;
                padding: 0.7rem 1rem !important;
                border-radius: 10px;
            }

            .home-navbar .navbar-collapse .nav-link-custom:hover {
                background: rgba(13, 56, 130, 0.06) !important;
                color: #0d3882 !important;
            }

            .home-navbar .navbar-collapse .nav-btn-login {
                color: #0d3882 !important;
                background: rgba(13, 56, 130, 0.06);
                border-color: rgba(13, 56, 130, 0.2);
            }
        }

        /* 2. قسم البطل (Hero Section) - تصميم منقسم هادئ وفاخر بهوية المنصة وألوانها الأصلية */
        .hero-section {
            background: linear-gradient(135deg, #09255a 0%, #0d3882 45%, #1565c0 85%, #1976d2 100%);
            color: white;
            padding: 145px 0 115px;
            position: relative;
            overflow: hidden;
            text-align: right;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 85% 15%, rgba(255, 255, 255, 0.15) 0%, transparent 55%),
                radial-gradient(circle at 15% 85%, rgba(245, 158, 11, 0.14) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(30, 136, 229, 0.12) 0%, transparent 60%);
            pointer-events: none;
            z-index: 1;
        }

        .hero-container-relative {
            position: relative;
            z-index: 2;
        }

        /* الكبسولة الترحيبية الهادئة */
        .hero-capsule {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 1.2rem;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            font-size: 0.85rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.25s ease;
        }

        .hero-capsule:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: rgba(245, 158, 11, 0.5);
        }

        .hero-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
            display: inline-block;
        }

        /* نصوص العنوان الرئيسي */
        .hero-main-title {
            font-size: clamp(2.3rem, 4.2vw, 3.3rem);
            font-weight: 900;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 0.6rem;
            color: #ffffff;
            text-shadow: 0 2px 14px rgba(0, 0, 0, 0.28);
        }

        .hero-subline {
            display: block;
            font-size: clamp(1.2rem, 2.2vw, 1.65rem);
            font-weight: 800;
            color: #fbbf24 !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
            margin-top: 0.35rem;
        }

        .hero-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.12rem;
            line-height: 1.85;
            max-width: 620px;
            margin-bottom: 1.8rem;
            font-weight: 500;
        }

        /* أزرار الإجراءات */
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border: 1.5px solid #fbbf24 !important;
            color: #07162c !important;
            font-weight: 800;
            padding: 0.75rem 1.8rem;
            font-size: 0.98rem;
            border-radius: 50px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }

        .btn-gold i {
            color: #07162c !important;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
            border-color: #fef08a !important;
            color: #051329 !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.45);
        }

        .btn-outline-hero {
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.32);
            color: #ffffff !important;
            font-weight: 700;
            padding: 0.75rem 1.7rem;
            font-size: 0.98rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-hero:hover {
            background: #ffffff;
            color: #0d3882 !important;
            border-color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(255, 255, 255, 0.3);
        }

        /* سطر مؤشرات الثقة تحت الأزرار */
        .hero-trust-bar {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.92);
            font-weight: 600;
        }

        .hero-trust-bar span {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        /* بطاقة الهوية الأكاديمية الهادئة على اليسار */
        .hero-showcase-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 24px;
            padding: 2.2rem 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hero-showcase-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.32), inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }

        .showcase-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 1.3rem;
        }

        .showcase-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2.5px solid #f59e0b;
            background: #ffffff;
            padding: 3px;
            object-fit: cover;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
        }

        .showcase-titles h4 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.2rem;
        }

        .showcase-titles p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.35rem;
            font-weight: 500;
        }

        .showcase-badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.74rem;
            font-weight: 700;
            color: #10b981;
            background: rgba(16, 185, 129, 0.16);
            border: 1px solid rgba(16, 185, 129, 0.38);
            padding: 0.2rem 0.65rem;
            border-radius: 50px;
        }

        .showcase-feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.3rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .showcase-feature-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.8rem 1rem;
            border-radius: 14px;
            font-size: 0.92rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            transition: background 0.2s ease;
        }

        .showcase-feature-item:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .showcase-feature-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .showcase-card-footer {
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .hero-section {
                padding: 125px 0 90px;
                text-align: center;
            }

            .hero-description {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-trust-bar {
                justify-content: center;
                gap: 1rem;
                font-size: 0.82rem;
            }

            .hero-showcase-card {
                max-width: 480px;
                margin: 2.5rem auto 0;
                text-align: right;
            }
        }

        @media (max-width: 991px) {
            .hero-section {
                padding: 120px 0 80px;
            }

            .hero-spotlight-desc {
                font-size: 1rem;
                margin-bottom: 1.6rem;
            }

            .hero-spotlight-trust {
                gap: 0.8rem;
                font-size: 0.78rem;
            }
        }

        /* 3. شريط الإحصائيات الحية (Live Stats Ribbon - From Uiverse.io by mrhyddenn) */
        .stats-ribbon {
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .card_box {
            width: 100%;
            min-height: 240px;
            border-radius: 20px;
            background: linear-gradient(165deg, #0e274d 0%, #06152b 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            position: relative;
            box-shadow: 0 20px 45px rgba(5, 18, 38, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: visible;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 1.8rem 1.2rem 1.4rem;
        }

        .card_box:hover {
            transform: translateY(-8px);
            box-shadow: 0 28px 55px rgba(5, 18, 38, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* Diagonal Corner Ribbon */
        .card_box .card_ribbon {
            position: absolute;
            overflow: hidden;
            width: 130px;
            height: 130px;
            top: -8px;
            left: -8px;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 5;
            border-top-left-radius: 20px;
        }

        .card_box .card_ribbon::before {
            content: attr(data-ribbon);
            position: absolute;
            width: 160%;
            height: 34px;
            transform: rotate(-45deg) translateY(-18px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.76rem;
            letter-spacing: 0.05em;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
            font-family: 'Tajawal', sans-serif;
        }

        .card_box .card_ribbon::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            bottom: 0;
            left: 0;
            z-index: -1;
        }

        /* 1. Graduates - Platform Royal Blue */
        .card_box.ribbon-blue .card_ribbon::before {
            background-image: linear-gradient(45deg, #1d4ed8 0%, #38bdf8 51%, #1e40af 100%);
        }

        .card_box.ribbon-blue .card_ribbon::after {
            background-image: linear-gradient(45deg, #1e40af 0%, #38bdf8 51%, #1e40af 100%);
            box-shadow: 122px -122px #1e3a8a;
        }

        .card_box.ribbon-blue:hover {
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 28px 55px rgba(29, 78, 216, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* 2. Partner Companies - Platform Emerald */
        .card_box.ribbon-emerald .card_ribbon::before {
            background-image: linear-gradient(45deg, #059669 0%, #34d399 51%, #047857 100%);
        }

        .card_box.ribbon-emerald .card_ribbon::after {
            background-image: linear-gradient(45deg, #047857 0%, #34d399 51%, #047857 100%);
            box-shadow: 122px -122px #064e3b;
        }

        .card_box.ribbon-emerald:hover {
            border-color: rgba(52, 211, 153, 0.5);
            box-shadow: 0 28px 55px rgba(5, 150, 105, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* 3. Training Programs - Platform Tripoli Academic Gold */
        .card_box.ribbon-gold .card_ribbon::before {
            background-image: linear-gradient(45deg, #d97706 0%, #fbbf24 51%, #b45309 100%);
        }

        .card_box.ribbon-gold .card_ribbon::after {
            background-image: linear-gradient(45deg, #b45309 0%, #fbbf24 51%, #b45309 100%);
            box-shadow: 122px -122px #78350f;
        }

        .card_box.ribbon-gold:hover {
            border-color: rgba(251, 191, 36, 0.55);
            box-shadow: 0 28px 55px rgba(217, 119, 6, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* 4. Job Opportunities - Platform Cyan / Sky Blue */
        .card_box.ribbon-cyan .card_ribbon::before,
        .card_box.ribbon-purple .card_ribbon::before {
            background-image: linear-gradient(45deg, #0284c7 0%, #38bdf8 51%, #0369a1 100%);
        }

        .card_box.ribbon-cyan .card_ribbon::after,
        .card_box.ribbon-purple .card_ribbon::after {
            background-image: linear-gradient(45deg, #0369a1 0%, #38bdf8 51%, #0369a1 100%);
            box-shadow: 122px -122px #0c4a6e;
        }

        .card_box.ribbon-cyan:hover,
        .card_box.ribbon-purple:hover {
            border-color: rgba(56, 189, 248, 0.55);
            box-shadow: 0 28px 55px rgba(2, 132, 199, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* Card Content Inner Elements */
        .card_box .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 10px;
            transition: transform 0.35s ease;
        }

        .card_box:hover .stat-icon-wrap {
            transform: scale(1.1) rotate(-3deg);
        }

        .card_box .stat-box-num {
            font-size: 2.75rem;
            font-weight: 900;
            line-height: 1.05;
            margin: 0;
            letter-spacing: -0.5px;
            color: #ffffff;
            font-family: 'Tajawal', sans-serif;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease;
        }

        .card_box:hover .stat-box-num {
            transform: scale(1.05);
        }

        .card_box .stat-box-title {
            font-size: 0.96rem;
            font-weight: 800;
            color: #f1f5f9;
            margin: 6px 0 4px;
            line-height: 1.35;
        }

        .card_box .stat-box-sub {
            font-size: 0.76rem;
            font-weight: 700;
            color: #94a3b8;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .card_box {
                min-height: 200px;
                padding: 1.4rem 0.8rem 1rem;
            }

            .card_box .stat-box-num {
                font-size: 2.1rem;
            }

            .card_box .stat-box-title {
                font-size: 0.84rem;
            }

            .card_box .stat-box-sub {
                font-size: 0.7rem;
            }

            .card_box .card_ribbon {
                width: 105px;
                height: 105px;
            }

            .card_box .card_ribbon::before {
                height: 28px;
                font-size: 0.68rem;
                transform: rotate(-45deg) translateY(-14px);
            }

            .card_box .card_ribbon::after {
                box-shadow: 98px -98px #1e3a8a;
            }
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
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        /* 4.1 بطاقة المعرض ثلاثية الأبعاد التفاعلية (3D Interactive Fair Book Card) */
        .fair-book-stage {
            perspective: 1200px;
            -webkit-perspective: 1200px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
        }

        .fair-book {
            position: relative;
            border-radius: 14px;
            width: 242px;
            height: 334px;
            transform-style: preserve-3d;
            -webkit-transform-style: preserve-3d;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.6s ease;
            box-shadow: 0 18px 40px -10px rgba(0, 0, 0, 0.55), 0 0 25px rgba(245, 158, 11, 0.15);
            cursor: pointer;
            user-select: none;
        }

        .fair-book:hover {
            transform: translateY(-4px) rotateZ(-1deg);
        }

        /* الطبقات الورقية الجانبية للكتاب (Paper Pages Stack) */
        .fair-book-pages {
            position: absolute;
            top: 6px;
            left: -5px;
            width: 7px;
            height: calc(100% - 12px);
            background: repeating-linear-gradient(to bottom,
                    #f8fafc 0px,
                    #f8fafc 2px,
                    #94a3b8 3px,
                    #94a3b8 4px);
            border-radius: 4px 0 0 4px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.35);
            z-index: 1;
        }

        /* داخل الكتاب: بطاقة المعرض الذكية مع رمز QR */
        .fair-book-inner {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid rgba(226, 232, 240, 0.95);
            box-shadow: inset 14px 0 24px -12px rgba(0, 0, 0, 0.18), 0 10px 25px rgba(0, 0, 0, 0.25);
            padding: 1.15rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            z-index: 2;
            overflow: hidden;
        }

        .fair-card-chip-bar {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 6px;
        }

        .fair-card-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #d97706;
            font-weight: 800;
            font-size: 0.75rem;
        }

        .fair-card-header {
            margin: 6px 0 4px;
            width: 100%;
        }

        .fair-card-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0b1f3a;
            line-height: 1.3;
            margin: 0;
        }

        .fair-card-subtitle {
            font-size: 0.72rem;
            color: #1565c0;
            font-weight: 700;
            margin-top: 2px;
        }

        /* إطار رمز الـ QR التفاعلي */
        .fair-card-qr-box {
            position: relative;
            width: 116px;
            height: 116px;
            background: #ffffff;
            border-radius: 12px;
            padding: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(13, 56, 130, 0.08);
            border: 1px solid #e2e8f0;
            margin: 3px auto;
        }

        .fair-card-qr-box .qr-corner {
            position: absolute;
            width: 10px;
            height: 10px;
            border-color: #0d3882;
            border-style: solid;
        }

        .fair-card-qr-box .qr-tl {
            top: -2px;
            right: -2px;
            border-width: 2px 2px 0 0;
            border-top-right-radius: 4px;
        }

        .fair-card-qr-box .qr-tr {
            top: -2px;
            left: -2px;
            border-width: 2px 0 0 2px;
            border-top-left-radius: 4px;
        }

        .fair-card-qr-box .qr-bl {
            bottom: -2px;
            right: -2px;
            border-width: 0 2px 2px 0;
            border-bottom-right-radius: 4px;
        }

        .fair-card-qr-box .qr-br {
            bottom: -2px;
            left: -2px;
            border-width: 0 0 2px 2px;
            border-bottom-left-radius: 4px;
        }

        .fair-card-qr-svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* خط ليزر المسح الضوئي المتحرك الخفيف */
        .fair-card-laser {
            position: absolute;
            left: 8px;
            right: 8px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #f59e0b, transparent);
            box-shadow: 0 0 6px #f59e0b;
            animation: qrLaserScan 2.6s infinite ease-in-out;
            pointer-events: none;
        }

        @keyframes qrLaserScan {

            0%,
            100% {
                top: 10px;
                opacity: 0;
            }

            15% {
                opacity: 1;
            }

            85% {
                opacity: 1;
            }

            50% {
                top: 102px;
            }
        }

        .fair-card-meta {
            font-size: 0.72rem;
            color: #475569;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-weight: 600;
        }

        .fair-card-meta-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fair-card-status-badge {
            background: linear-gradient(135deg, rgba(13, 56, 130, 0.06) 0%, rgba(21, 101, 192, 0.08) 100%);
            border: 1px solid rgba(13, 56, 130, 0.15);
            color: #0d3882;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.42rem 0.8rem;
            border-radius: 50px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* غلاف الكتاب ثلاثي الأبعاد (The 3D Book Cover) */
        .fair-book-cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 14px;
            cursor: pointer;
            z-index: 5;
            transform-origin: right center;
            -webkit-transform-origin: right center;
            transform-style: preserve-3d;
            -webkit-transform-style: preserve-3d;
            transition: transform 0.65s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.65s ease;
            background: linear-gradient(150deg, #071938 0%, #0d2f66 45%, #05142b 100%);
            border: 1.5px solid rgba(245, 158, 11, 0.65);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45), inset -8px 0 16px rgba(0, 0, 0, 0.55), inset 0 0 0 2px rgba(245, 158, 11, 0.15);
            padding: 1.25rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            overflow: hidden;
        }

        /* شريط الكعب / الخياطة الفاخر في طرف الكتاب الأيمن (Spine) */
        .fair-book-spine-line {
            position: absolute;
            top: 0;
            right: 0;
            width: 12px;
            height: 100%;
            background: linear-gradient(180deg, #d97706 0%, #f59e0b 50%, #92400e 100%);
            border-top-right-radius: 13px;
            border-bottom-right-radius: 13px;
            box-shadow: -2px 0 6px rgba(0, 0, 0, 0.35);
        }

        .fair-book-spine-line::before {
            content: '';
            position: absolute;
            top: 8px;
            bottom: 8px;
            right: 4px;
            width: 1.5px;
            background: repeating-linear-gradient(to bottom, rgba(255, 255, 255, 0.6) 0px, rgba(255, 255, 255, 0.6) 4px, transparent 4px, transparent 8px);
        }

        .fair-cover-crest-wrap {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #f59e0b;
            padding: 3px;
            margin: 0 auto 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fair-cover-crest-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .fair-cover-title {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 800;
            line-height: 1.35;
            margin: 4px 0 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .fair-cover-badge {
            background: rgba(245, 158, 11, 0.18);
            border: 1px solid rgba(245, 158, 11, 0.45);
            color: #fbbf24;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 50px;
            margin-bottom: 4px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .fair-cover-hint {
            background: rgba(255, 255, 255, 0.08);
            border: 1px dashed rgba(245, 158, 11, 0.5);
            color: #fef08a;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(4px);
            transition: all 0.25s ease;
        }

        .fair-cover-hint i {
            color: #f59e0b;
            animation: hintWiggle 1.8s infinite ease-in-out;
        }

        @keyframes hintWiggle {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        /* 🌟 حركة فتح الغلاف ثلاثية الأبعاد (3D Flip Open) 🌟 */
        .fair-book:hover .fair-book-cover,
        .fair-book.book-opened .fair-book-cover {
            transform: rotateY(84deg);
            -webkit-transform: rotateY(84deg);
            box-shadow: -18px 16px 36px rgba(0, 0, 0, 0.45);
            pointer-events: none;
        }

        .fair-book:hover .fair-book-inner,
        .fair-book.book-opened .fair-book-inner {
            z-index: 6;
        }

        /* 5. بطاقات التدريبات المميزة بنمط Uiverse Cobp المتناسق مع هوية المنصة */
        .uiverse-train-card {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg,
                    #0d3882 0%,
                    #1565c0 30%,
                    #1e88e5 55%,
                    #f59e0b 85%,
                    #d97706 100%);
            padding: 3.5px;
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 14px 34px -8px rgba(11, 31, 58, 0.18), 0 0 16px rgba(245, 158, 11, 0.12);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .uiverse-train-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 22px 48px -10px rgba(13, 56, 130, 0.28), 0 0 28px rgba(245, 158, 11, 0.35);
        }

        .uiverse-train-card .title-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 18px 10px;
            color: #ffffff;
        }

        .uiverse-train-card .title-card p {
            margin: 0;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(13, 56, 130, 0.8);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .uiverse-train-card .title-card svg {
            color: #fbbf24;
            filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.8));
        }

        .uiverse-train-card .card-content {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #091e3e 0%, #06152c 100%);
            border-radius: 27px;
            color: #94a3b8;
            font-size: 0.85rem;
            padding: 22px 20px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
            position: relative;
            flex-grow: 1;
        }

        .uiverse-train-card .card-content .card-category {
            font-size: 0.76rem;
            font-weight: 800;
            color: #38bdf8;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.3px;
        }

        .uiverse-train-card .card-content .card-title-main {
            font-size: 1.12rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.45;
            margin: 0;
            min-height: 48px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .uiverse-train-card .card-content .plain {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 0;
            padding-bottom: 4px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.12);
        }

        .uiverse-train-card .card-content .plain :nth-child(1) {
            font-size: 2.1rem;
            font-weight: 900;
            color: #ffffff;
            font-family: monospace;
            line-height: 1;
            text-shadow: 0 2px 10px rgba(245, 158, 11, 0.35);
        }

        .uiverse-train-card .card-content .plain :nth-child(2) {
            font-size: 0.84rem;
            font-weight: 700;
            color: #fbbf24;
        }

        .uiverse-train-card .card-content .description {
            font-size: 0.84rem;
            color: #94a3b8;
            line-height: 1.6;
            margin: 0;
            flex-grow: 1;
            min-height: 42px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .uiverse-train-card .card-meta-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #cbd5e1;
        }

        .uiverse-train-card .card-btn {
            background: rgba(245, 158, 11, 0.12) !important;
            border: 2px solid #f59e0b !important;
            color: #ffffff !important;
            font-weight: 800;
            font-size: 0.9rem;
            padding: 0.65rem 1.4rem;
            border-radius: 50px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: all 0.28s ease;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2), inset 0 0 10px rgba(245, 158, 11, 0.08);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            text-decoration: none;
            cursor: pointer;
        }

        .uiverse-train-card .card-btn i {
            color: #fbbf24 !important;
            transition: all 0.28s ease;
        }

        .uiverse-train-card .card-btn:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            border-color: #f59e0b !important;
            color: #0b1f3a !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45);
            text-shadow: none;
        }

        .uiverse-train-card .card-btn:hover i {
            color: #0b1f3a !important;
            transform: translateX(-3px);
        }

        .uiverse-train-card .card-btn:active {
            transform: scale(0.98);
        }

        /* ========================================================
           6. رحلة الخريج — Premium Navy/Gold Horizontal Stepper
           ======================================================== */

        /* ── Section Shell ── */
        .journey-section {
            position: relative;
            background: linear-gradient(165deg, #071b3e 0%, #0b2654 45%, #0d3a72 100%);
            padding: 5.5rem 0 5rem;
            overflow: hidden;
            scroll-margin-top: 85px;
        }

        /* Hex-grid overlay */
        .journey-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(238, 202, 62, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(238, 202, 62, 0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* Radial glow spots */
        .journey-glow-top {
            position: absolute;
            top: -80px;
            right: -80px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238, 202, 62, 0.14) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
        }

        .journey-glow-bottom {
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.10) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Section Header ── */
        .journey-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(238, 202, 62, 0.12);
            border: 1px solid rgba(238, 202, 62, 0.35);
            color: #eecc3e;
            padding: 5px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            margin-bottom: 1rem;
        }

        .journey-eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #eecc3e;
            box-shadow: 0 0 0 3px rgba(238, 202, 62, 0.3);
            animation: journeyPulse 2s infinite;
        }

        @keyframes journeyPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(238, 202, 62, 0.5);
            }

            70% {
                box-shadow: 0 0 0 7px rgba(238, 202, 62, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(238, 202, 62, 0);
            }
        }

        .journey-main-title {
            font-size: clamp(1.85rem, 3.5vw, 2.6rem);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }

        .journey-main-title .gold-word {
            background: linear-gradient(135deg, #eecc3e 0%, #f59e0b 55%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .journey-sub {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.96rem;
            max-width: 580px;
            line-height: 1.7;
        }

        /* ── Stepper Wrapper ── */
        .journey-stepper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: row;
            align-items: stretch;
            justify-content: space-between;
            gap: 0;
        }

        /* ── Connector Track (Segment between steps) ── */
        .journey-stepper-track {
            display: none;
        }

        .journey-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 44px;
            right: 50%;
            width: 100%;
            height: 2px;
            background: linear-gradient(270deg,
                    rgba(238, 202, 62, 0.9) 0%,
                    rgba(238, 202, 62, 0.5) 100%);
            box-shadow: 0 0 10px rgba(238, 202, 62, 0.35);
            z-index: 0;
            pointer-events: none;
        }

        /* ── Individual Step ── */
        .journey-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            cursor: pointer;
            padding: 0 10px;
        }

        /* Step Number Circle */
        .journey-step-num {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            border: 2px solid rgba(238, 202, 62, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 2;
            flex-shrink: 0;
        }

        .journey-step-num .step-num-inner {
            width: 66px;
            height: 66px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0b2654 0%, #113376 100%);
            border: 1.5px solid rgba(238, 202, 62, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: all 0.4s ease;
        }

        .journey-step-num .step-num-icon {
            font-size: 1.5rem;
            color: #eecc3e;
            filter: drop-shadow(0 0 8px rgba(238, 202, 62, 0.6));
            transition: transform 0.4s ease;
        }

        .journey-step-num .step-ordinal {
            font-size: 0.62rem;
            font-weight: 800;
            color: rgba(238, 202, 62, 0.65);
            letter-spacing: 0.5px;
            margin-top: 1px;
        }

        /* Outer ring pulse on hover */
        .journey-step:hover .journey-step-num {
            border-color: rgba(238, 202, 62, 0.85);
            box-shadow: 0 0 0 8px rgba(238, 202, 62, 0.08),
                0 12px 28px rgba(0, 0, 0, 0.35);
        }

        .journey-step:hover .step-num-inner {
            background: linear-gradient(135deg, #1a4d9c 0%, #1c5ab5 100%);
            border-color: rgba(238, 202, 62, 0.9);
        }

        .journey-step:hover .step-num-icon {
            transform: scale(1.15) rotate(-8deg);
            filter: drop-shadow(0 0 14px rgba(238, 202, 62, 0.9));
        }

        /* Step Body Card */
        .journey-step-body {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 20px;
            padding: 1.35rem 1.2rem 1.2rem;
            margin-top: 1rem;
            width: 100%;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .journey-step-body::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(238, 202, 62, 0.07) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .journey-step:hover .journey-step-body {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(238, 202, 62, 0.35);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(238, 202, 62, 0.15);
            transform: translateY(-4px);
        }

        .journey-step:hover .journey-step-body::before {
            opacity: 1;
        }

        .journey-step-label {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(238, 202, 62, 0.7);
            margin-bottom: 0.5rem;
        }

        .journey-step-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.55rem;
            line-height: 1.35;
        }

        .journey-step-desc {
            font-size: 0.83rem;
            color: rgba(255, 255, 255, 0.58);
            line-height: 1.65;
            margin-bottom: 0.9rem;
        }

        .journey-step-bullets {
            list-style: none;
            padding: 0;
            margin: 0 0 0.9rem;
            text-align: right;
        }

        .journey-step-bullets li {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.72);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 5px;
        }

        .journey-step-bullets li i {
            color: #eecc3e;
            font-size: 0.68rem;
            flex-shrink: 0;
        }

        /* Step Footer */
        .journey-step-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 700;
        }

        .journey-step-progress {
            display: flex;
            gap: 3px;
        }

        .jsp-bar {
            width: 13px;
            height: 3px;
            border-radius: 2px;
            background: rgba(255, 255, 255, 0.15);
        }

        .jsp-bar.active {
            background: linear-gradient(90deg, #eecc3e, #f59e0b);
        }

        /* Special: last step (goal) */
        .journey-step-goal .journey-step-num {
            border-color: rgba(238, 202, 62, 0.65);
            background: rgba(238, 202, 62, 0.08);
        }

        .journey-step-goal .step-num-inner {
            background: linear-gradient(135deg, #eecc3e 0%, #f97316 100%);
            border-color: transparent;
        }

        .journey-step-goal .step-num-icon {
            color: #0b1f3a;
            filter: none;
        }

        .journey-step-goal .step-ordinal {
            color: rgba(11, 31, 58, 0.7);
        }

        .journey-step-goal:hover .journey-step-num {
            box-shadow: 0 0 0 8px rgba(238, 202, 62, 0.15),
                0 12px 30px rgba(238, 202, 62, 0.35);
        }

        .journey-step-goal .journey-step-body {
            border-color: rgba(238, 202, 62, 0.25);
            background: rgba(238, 202, 62, 0.07);
        }

        .journey-step-goal:hover .journey-step-body {
            border-color: rgba(238, 202, 62, 0.5);
            box-shadow: 0 16px 40px rgba(238, 202, 62, 0.2),
                0 0 0 1px rgba(238, 202, 62, 0.3);
        }

        .journey-step-goal .journey-step-label {
            color: #eecc3e;
        }

        .goal-achieved-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.4);
            color: #eecc3e;
            font-size: 0.74rem;
            font-weight: 800;
            padding: 3px 12px;
            border-radius: 50px;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .journey-stepper {
                flex-direction: column;
                align-items: stretch;
                gap: 1.5rem;
            }

            .journey-stepper-track {
                display: none;
            }

            .journey-step-num {
                width: 72px;
                height: 72px;
            }

            .journey-step-num .step-num-inner {
                width: 54px;
                height: 54px;
            }

            .journey-step-num .step-num-icon {
                font-size: 1.25rem;
            }

            .journey-step-body {
                text-align: right;
            }

            .journey-step-bullets {
                text-align: right;
            }

            .journey-step {
                flex-direction: row;
                align-items: flex-start;
                gap: 1rem;
            }

            .journey-step-body {
                margin-top: 0;
                flex: 1;
            }
        }

        @media (max-width: 576px) {
            .journey-step-title {
                font-size: 0.96rem;
            }

            .journey-step-desc {
                font-size: 0.8rem;
            }
        }

        /* ── CTA Banner (reused) ── */
        .journey-cta-banner {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(238, 202, 62, 0.2);
            border-radius: 22px;
            padding: 2.2rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .journey-cta-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(238, 202, 62, 0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .journey-cta-badge {
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.35);
            color: #eecc3e;
            font-size: 0.78rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 9999px;
        }

        .btn-journey-primary {
            background: linear-gradient(135deg, #eecc3e 0%, #f59e0b 50%, #ea580c 100%);
            color: #0b1f3a;
            font-weight: 800;
            font-size: 0.92rem;
            padding: 0.78rem 1.7rem;
            border-radius: 12px;
            border: none;
            box-shadow: 0 6px 18px rgba(238, 202, 62, 0.4);
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-journey-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(238, 202, 62, 0.55);
            color: #0b1f3a;
        }

        .btn-journey-outline {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border: 1.5px solid rgba(255, 255, 255, 0.28);
            font-weight: 700;
            font-size: 0.92rem;
            padding: 0.78rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(6px);
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-journey-outline:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            border-color: rgba(238, 202, 62, 0.5);
            transform: translateY(-2px);
        }

        /* 7. بطاقات الأخبار والإعلانات */
        .bento-news-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .bento-news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }

        /* 8. تذييل الصفحة الشامل (Modern Mega Footer) */
        .main-footer {
            background: linear-gradient(180deg, #07172c 0%, #030d1a 100%);
            color: #cbd5e1;
            padding: 65px 0 25px;
            border-top: 3px solid var(--gold-accent, #f59e0b);
            position: relative;
        }

        .footer-brand-logo-frame {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: transparent;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
            border: 2px solid var(--gold-accent, #f59e0b);
            overflow: hidden;
        }

        .footer-brand-logo-img {
            width: 100%;
            height: 100%;
            max-width: 48px;
            max-height: 48px;
            object-fit: cover;
            border-radius: 50%;
        }

        .navbar-brand-logo {
            width: 100%;
            height: 100%;
            max-width: 44px;
            max-height: 44px;
            object-fit: contain;
            border-radius: 8px;
        }

        .footer-social-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff;
            transition: all 0.25s ease;
            text-decoration: none;
            font-size: 0.92rem;
        }

        .footer-social-btn:hover {
            background: var(--gold-accent, #f59e0b);
            border-color: var(--gold-accent, #f59e0b);
            color: #0b1f3a !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
        }

        .footer-column-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 0.98rem;
            margin-bottom: 1.15rem;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
        }

        .footer-column-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28px;
            height: 2px;
            background: var(--gold-accent, #f59e0b);
            border-radius: 2px;
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 0.88rem;
        }

        .footer-contact-bubble {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
            flex-shrink: 0;
            font-size: 0.82rem;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 0.65rem;
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: var(--gold-accent, #f59e0b);
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
            max-height: calc(85dvh - 120px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* iOS smooth scroll fix */
            overscroll-behavior: contain;
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
                max-height: calc(100dvh - 180px);
            }

            .reg-step-label {
                font-size: 0.75rem;
            }

            /* Fix nav buttons wrapping on small screens */
            .modal-register-body .d-flex.justify-content-between {
                flex-wrap: wrap;
                gap: 10px;
            }

            .modal-register-body .d-flex.justify-content-between .btn {
                flex: 1 1 auto;
                min-width: 130px;
            }

            /* Make submit button full width on mobile */
            #modalSubmitRegisterBtn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 110px 0 80px;
            }

            .modal-register-header {
                padding: 18px 16px 14px;
            }

            .modal-register-header h3 {
                font-size: 1.2rem;
            }

            .modal-register-body {
                padding: 14px 12px;
                max-height: calc(100dvh - 160px);
            }

            .reg-progress-steps {
                padding: 0 4px;
                margin-bottom: 16px;
            }

            .reg-step-circle {
                width: 30px;
                height: 30px;
                font-size: 0.82rem;
            }

            .reg-step-label {
                font-size: 0.68rem;
            }

            .reg-section-title {
                font-size: 1rem;
            }
        }

        /* iOS safe area for notch/home indicator */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .modal-register-footer {
                padding-bottom: calc(16px + env(safe-area-inset-bottom));
            }
        }

        /* ========================================================
           شاشة أهداف مكتب تدريب الخريجين الموحدة (Wide HUD Screen Card)
           مستوحاة من كود Uiverse (StealthWorm) مع هوية جامعة طرابلس الملكية
           ======================================================== */
        .goals-hud-screen-border {
            z-index: 1;
            position: relative;
            padding: 1.1rem;
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            box-sizing: border-box;
            background: transparent;
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border-radius: 0px 2.5rem;
            background-image: radial-gradient(#07172c 0%, #030a16 100%),
                linear-gradient(135deg, #1565c0 0%, #0d3882 50%, #1565c0 100%);
            background-origin: border-box;
            background-clip: content-box, border-box;
            box-shadow: 0 20px 50px rgba(5, 17, 34, 0.45), 0 0 35px rgba(21, 101, 192, 0.45), 0 0 15px rgba(21, 101, 192, 0.3);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            animation: goals_screen_pulse 4s ease-in-out infinite;
        }

        .goals-hud-screen-border:hover {
            box-shadow: 0 25px 60px rgba(5, 17, 34, 0.55), 0 0 45px rgba(21, 101, 192, 0.65), 0 0 25px rgba(21, 101, 192, 0.45);
            transform: translateY(-4px);
        }

        .goals-hud-screen-bg {
            position: relative;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 0px 1.8rem;
            background: radial-gradient(circle at 90% 10%, rgba(21, 101, 192, 0.22) 0%, transparent 60%),
                radial-gradient(circle at 10% 90%, rgba(245, 158, 11, 0.15) 0%, transparent 60%),
                linear-gradient(155deg, #071936 0%, #05142b 55%, #020914 100%);
            padding: 2.25rem 2rem 2.25rem;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* شبكة نقاط تقنية ناعمة لخلفية الشاشة */
        .goals-hud-screen-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
            z-index: 0;
        }

        /* شريط العنوان التفاعلي أعلى الشاشة */
        .goals-screen-header {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .goals-screen-badge-box {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* إطار شعار المكتب الدائري الفاخر بدلاً من الشكل السداسي */
        .hud-office-logo-frame {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ffffff;
            padding: 2px;
            border: 2.5px solid #1565c0;
            box-shadow: 0 0 16px rgba(21, 101, 192, 0.5), 0 4px 10px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
        }

        .hud-office-logo-frame:hover {
            transform: scale(1.08);
            box-shadow: 0 0 25px rgba(21, 101, 192, 0.75), 0 6px 14px rgba(0, 0, 0, 0.35);
        }

        .hud-office-logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .goals-screen-title-area h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .goals-screen-title-area p {
            font-size: 0.8rem;
            color: #94a3b8;
            margin: 2px 0 0;
            font-weight: 500;
        }

        .goals-screen-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #fbd38d;
            font-family: monospace, 'Tajawal', sans-serif;
            letter-spacing: 0.5px;
        }

        .goals-screen-status-pill .hud-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 8px #10b981;
            display: inline-block;
            animation: hud_dot_pulse 2s infinite;
        }

        /* نص الماركيه الخلفي المتحرك من الكود المرفق */
        .goals-hud-marquee {
            top: 15%;
            z-index: 0;
            width: 100%;
            height: 70%;
            position: absolute;
            overflow: hidden;
            --offset: 2rem;
            --move-initial: calc(0% + var(--offset));
            --move-final: calc(-50% + var(--offset));
            filter: opacity(0.08);
            pointer-events: none;
            direction: ltr;
        }

        .goals-hud-marquee-inner {
            width: max-content;
            display: flex;
            position: relative;
            transform: translate3d(var(--move-initial), 0, 0);
            animation: hud_slide 45s linear infinite;
        }

        .goals-hud-giant-text {
            display: inline-flex;
            text-transform: uppercase;
            font-family: 'Tajawal', 'Open Sans', sans-serif;
            font-size: 5.5rem;
            font-weight: 900;
            align-items: center;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke-width: 2px;
            -webkit-text-stroke-color: #ffffff;
            white-space: nowrap;
            letter-spacing: 0.2rem;
            padding: 0 1rem;
        }

        /* طبقة الضباب المتوهج في الأسفل من الكود المرفق */
        .hud-mist-container {
            position: absolute;
            width: 70%;
            height: 180px;
            overflow: hidden;
            filter: blur(2rem);
            bottom: -30px;
            left: 15%;
            pointer-events: none;
            z-index: 0;
        }

        .hud-mist {
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 15%, rgba(21, 101, 192, 0.3) 45%, transparent 70%);
            animation: hud_mist 12s infinite both;
        }

        /* شبكة الأهداف الثلاثة داخل الشاشة */
        .goals-hud-grid {
            position: relative;
            z-index: 2;
        }

        .goal-hud-card {
            position: relative;
            background: rgba(13, 38, 76, 0.45);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-top: 2px solid rgba(245, 158, 11, 0.4);
            border-radius: 18px;
            padding: 1.75rem 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .goal-hud-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(245, 158, 11, 0.08) 0%, transparent 60%);
            pointer-events: none;
            transition: opacity 0.35s ease;
            opacity: 0.5;
        }

        .goal-hud-card:hover {
            transform: translateY(-6px);
            background: rgba(18, 48, 92, 0.65);
            border-color: rgba(245, 158, 11, 0.7);
            border-top-color: #f59e0b;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3), 0 0 20px rgba(245, 158, 11, 0.25);
        }

        .goal-hud-card:hover::before {
            opacity: 1;
        }

        .goal-hud-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .goal-hud-num {
            font-family: monospace, 'Tajawal', sans-serif;
            font-size: 1.4rem;
            font-weight: 900;
            color: transparent;
            -webkit-text-stroke-width: 1.5px;
            -webkit-text-stroke-color: #f59e0b;
            letter-spacing: 1px;
            background: linear-gradient(180deg, #f59e0b 0%, rgba(245, 158, 11, 0.3) 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .goal-hud-icon-badge {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: transform 0.35s ease;
        }

        .goal-hud-card:hover .goal-hud-icon-badge {
            transform: scale(1.1) rotate(-5deg);
        }

        .goal-hud-icon-1 {
            background: rgba(21, 101, 192, 0.25);
            border: 1px solid rgba(56, 189, 248, 0.4);
            color: #38bdf8;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
        }

        .goal-hud-icon-2 {
            background: rgba(245, 158, 11, 0.2);
            border: 1px solid rgba(245, 158, 11, 0.5);
            color: #fbbf24;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .goal-hud-icon-3 {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .goal-hud-title {
            font-size: 1.18rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.65rem;
            letter-spacing: -0.2px;
        }

        .goal-hud-desc {
            font-size: 0.88rem;
            line-height: 1.75;
            color: #cbd5e1;
            margin-bottom: 1.25rem;
            font-weight: 400;
        }

        .goal-hud-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding-top: 0.85rem;
            border-top: 1px dashed rgba(255, 255, 255, 0.12);
        }

        .goal-hud-tag-item {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .goal-hud-card:hover .goal-hud-tag-item {
            color: #e2e8f0;
            border-color: rgba(245, 158, 11, 0.3);
            background: rgba(245, 158, 11, 0.1);
        }

        /* شريط أسفل الشاشة التكنولوجي */
        .goals-screen-footer {
            position: relative;
            z-index: 2;
            margin-top: 2rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .hud-footer-vision {
            font-size: 0.88rem;
            font-weight: 700;
            color: #f59e0b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hud-footer-code {
            font-family: monospace, sans-serif;
            font-size: 0.75rem;
            color: #64748b;
            letter-spacing: 1px;
            direction: ltr;
        }

        /* الرسوم المتحركة Keyframes */
        @keyframes hud_spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes hud_mist {
            0% {
                transform: translateX(-30%) translateY(0%) scaleY(0.8) rotate(0deg);
                opacity: 0.25;
            }

            50% {
                transform: translateX(10%) translateY(20%) scaleY(1.3) rotate(10deg);
                opacity: 0.55;
            }

            100% {
                transform: translateX(-30%) translateY(0%) scaleY(0.8) rotate(0deg);
                opacity: 0.25;
            }
        }

        @keyframes goals_screen_pulse {
            0% {
                box-shadow: 0 20px 50px rgba(5, 17, 34, 0.45), 0 0 25px rgba(21, 101, 192, 0.35), 0 0 10px rgba(21, 101, 192, 0.2);
            }

            50% {
                box-shadow: 0 25px 60px rgba(5, 17, 34, 0.55), 0 0 38px rgba(21, 101, 192, 0.55), 0 0 20px rgba(21, 101, 192, 0.35);
            }

            100% {
                box-shadow: 0 20px 50px rgba(5, 17, 34, 0.45), 0 0 25px rgba(21, 101, 192, 0.35), 0 0 10px rgba(21, 101, 192, 0.2);
            }
        }

        @keyframes hud_slide {
            0% {
                transform: translate3d(var(--move-initial), 0, 0);
            }

            100% {
                transform: translate3d(var(--move-final), 0, 0);
            }
        }

        @keyframes hud_dot_pulse {
            0% {
                transform: scale(0.9);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.3);
                opacity: 1;
            }

            100% {
                transform: scale(0.9);
                opacity: 0.7;
            }
        }

        @media (max-width: 991px) {
            .goals-hud-screen-bg {
                padding: 1.5rem 1.25rem;
            }

            .goals-hud-giant-text {
                font-size: 3.5rem;
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
                        <img src="{{ asset('images/logo.jpg') }}" alt="شعار جامعة طرابلس" class="navbar-brand-logo-img"
                            onerror="this.onerror=null;">
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
                <button class="navbar-toggler navbar-toggler-custom" type="button" data-bs-toggle="collapse"
                    data-bs-target="#homeNavContent" aria-controls="homeNavContent" aria-expanded="false"
                    aria-label="تبديل القائمة">
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
                            <button type="button" class="btn nav-btn-login" data-bs-toggle="modal"
                                data-bs-target="#loginModal">
                                <i class="fas fa-arrow-right-to-bracket"></i>
                                <span>تسجيل الدخول</span>
                            </button>
                            <button type="button" class="btn nav-btn-register shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#graduateRegisterModal">
                                <i class="fas fa-user-plus text-warning"></i>
                                <span>تسجيل خريج</span>
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- ==================== 2. قسم البطل الرئيسي (Split Hero Section) ==================== -->
    <section class="hero-section" id="hero">
        <div class="container hero-container-relative">
            <div class="row align-items-center g-4 g-xl-5">

                <!-- العمود الأيمن: العناوين، النبذة، وأزرار الإجراءات -->
                <div class="col-lg-7 col-xl-7">

                    <!-- العنوان الرئيسي -->
                    <h1 class="hero-main-title mb-4" data-aos="fade-up" data-aos-delay="150">
                        منصة مكتب تدريب الخريجين
                    </h1>



                    <!-- أزرار الإجراءات -->
                    <div class="d-flex align-items-center gap-3 flex-wrap mb-4" data-aos="fade-up" data-aos-delay="250">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg d-flex align-items-center gap-2">
                                <i class="fas fa-th-large"></i>
                                <span>الانتقال للوحة التحكم</span>
                                <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        @else
                            <button type="button" class="btn btn-gold btn-lg d-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>تسجيل الدخول</span>
                            </button>
                            <button type="button" class="btn btn-outline-hero btn-lg d-flex align-items-center gap-2"
                                data-bs-toggle="modal" data-bs-target="#graduateRegisterModal">
                                <i class="fas fa-user-plus"></i>
                                <span>تسجيل خريج جديد</span>
                            </button>
                        @endauth
                        <a href="#trainings" class="btn btn-outline-hero btn-lg d-flex align-items-center gap-2">
                            <i class="fas fa-graduation-cap"></i>
                            <span>تصفح التدريبات</span>
                        </a>
                    </div>

                    <!-- سطر مؤشرات الثقة تحت الأزرار -->

                </div>

                <!-- العمود الأيسر: بطاقة الهوية الأكاديمية الهادئة والمميزة -->
                <div class="col-lg-5 col-xl-5" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-showcase-card">

                        <!-- رأس البطاقة: الشعار والعناوين الرسمية -->
                        <div class="showcase-card-header">
                            <img src="{{ asset('images/logo.jpg') }}" alt="شعار جامعة طرابلس" class="showcase-logo"
                                onerror="this.onerror=null;">
                            <div class="showcase-titles">
                                <h4>مكتب تدريب وتأهيل الخريجين
                                </h4>

                            </div>
                        </div>

                        <!-- قائمة الخدمات والمزايا الأكاديمية  -->
                        <ul class="showcase-feature-list">
                            <li class="showcase-feature-item">
                                <div class="showcase-feature-icon"
                                    style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <span>مسارات تدريب وتأهيل مهني </span>
                            </li>
                            <li class="showcase-feature-item">
                                <div class="showcase-feature-icon"
                                    style="background: rgba(59, 130, 246, 0.2); color: #93c5fd;">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <span>إصدار شهادات رقمية </span>
                            </li>
                            <li class="showcase-feature-item">
                                <div class="showcase-feature-icon"
                                    style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7;">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <span>تشبيك مع كبرى الشركات والمؤسسات</span>
                            </li>
                        </ul>



                    </div>
                </div>

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
                            @php
                                $ribbonTheme = match ($key) {
                                    'graduates' => 'ribbon-blue',
                                    'companies' => 'ribbon-emerald',
                                    'trainings' => 'ribbon-gold',
                                    'opportunities' => 'ribbon-cyan',
                                    default => 'ribbon-blue',
                                };
                                $ribbonText = match ($key) {
                                    'graduates' => 'معتمد',
                                    'companies' => 'شريك فعال',
                                    'trainings' => 'برنامج مكثف',
                                    'opportunities' => 'نشط الآن',
                                    default => 'مباشر',
                                };
                                $trendText = match ($key) {
                                    'graduates' => 'كفاءات وطنية واعدة',
                                    'companies' => 'شراكات عمل مستدامة',
                                    'trainings' => 'تأهيل احترافي مستمر',
                                    'opportunities' => 'ربط مباشر بسوق العمل',
                                    default => 'مؤشر أداء معتمد',
                                };
                                $trendIcon = match ($key) {
                                    'graduates' => 'fas fa-arrow-trend-up',
                                    'companies' => 'fas fa-handshake',
                                    'trainings' => 'fas fa-chart-line',
                                    'opportunities' => 'fas fa-bullseye',
                                    default => 'fas fa-arrow-trend-up',
                                };
                                $accentColor = match ($key) {
                                    'graduates' => '#38bdf8',
                                    'companies' => '#34d399',
                                    'trainings' => '#fbbf24',
                                    'opportunities' => '#38bdf8',
                                    default => '#38bdf8',
                                };
                                $iconBg = match ($key) {
                                    'graduates' => 'rgba(37, 99, 235, 0.22)',
                                    'companies' => 'rgba(16, 185, 129, 0.22)',
                                    'trainings' => 'rgba(245, 158, 11, 0.22)',
                                    'opportunities' => 'rgba(2, 132, 199, 0.22)',
                                    default => 'rgba(37, 99, 235, 0.22)',
                                };
                                $cardIcon = $card['icon'] ?? match ($key) {
                                    'graduates' => 'fas fa-user-graduate',
                                    'companies' => 'fas fa-building',
                                    'trainings' => 'fas fa-chalkboard-teacher',
                                    'opportunities' => 'fas fa-briefcase',
                                    default => 'fas fa-chart-pie',
                                };
                                $val = $card['value'] ?? null;
                                $displayVal = ($val !== null && $val > 0) ? number_format($val) : match ($key) {
                                    'graduates' => '69',
                                    'companies' => '4',
                                    'trainings' => '35',
                                    'opportunities' => '10',
                                    default => '0',
                                };
                                $prefix = $card['prefix'] ?? '+';
                            @endphp
                            <div class="col-6 col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="card_box {{ $ribbonTheme }}">
                                    <span class="card_ribbon" data-ribbon="{{ $ribbonText }}"></span>
                                    <div class="stat-icon-wrap" style="background: {{ $iconBg }}; color: {{ $accentColor }};">
                                        <i class="{{ $cardIcon }}"></i>
                                    </div>
                                    <div class="stat-box-num" dir="ltr">{{ $prefix }}{{ $displayVal }}</div>
                                    <div class="stat-box-title">{{ $card['label'] }}</div>
                                    <div class="stat-box-sub">
                                        <i class="{{ $trendIcon }}" style="color: {{ $accentColor }}; font-size: 0.72rem;"></i>
                                        <span>{{ $trendText }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- البطاقة 1: خريج مسجل ومعتمد -->
                    <div class="col-6 col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="card_box ribbon-blue">
                            <span class="card_ribbon" data-ribbon="معتمد"></span>
                            <div class="stat-icon-wrap" style="background: rgba(37, 99, 235, 0.22); color: #38bdf8;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stat-box-num" dir="ltr">
                                +{{ number_format(($stats['graduates_count'] ?? 0) > 0 ? $stats['graduates_count'] : 69) }}
                            </div>
                            <div class="stat-box-title">خريج مسجل ومعتمد</div>
                            <div class="stat-box-sub">
                                <i class="fas fa-arrow-trend-up" style="color: #38bdf8; font-size: 0.72rem;"></i>
                                <span>كفاءات وطنية واعدة</span>
                            </div>
                        </div>
                    </div>

                    <!-- البطاقة 2: شركة ومؤسسة شريكة -->
                    <div class="col-6 col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="card_box ribbon-emerald">
                            <span class="card_ribbon" data-ribbon="شريك فعال"></span>
                            <div class="stat-icon-wrap" style="background: rgba(16, 185, 129, 0.22); color: #34d399;">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="stat-box-num" dir="ltr">
                                +{{ number_format(($stats['companies_count'] ?? 0) > 0 ? $stats['companies_count'] : 4) }}</div>
                            <div class="stat-box-title">شركة ومؤسسة شريكة</div>
                            <div class="stat-box-sub">
                                <i class="fas fa-handshake" style="color: #34d399; font-size: 0.72rem;"></i>
                                <span>شراكات عمل مستدامة</span>
                            </div>
                        </div>
                    </div>

                    <!-- البطاقة 3: برنامج تدريبي وتأهيلي -->
                    <div class="col-6 col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="card_box ribbon-gold">
                            <span class="card_ribbon" data-ribbon="برنامج مكثف"></span>
                            <div class="stat-icon-wrap" style="background: rgba(245, 158, 11, 0.22); color: #fbbf24;">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="stat-box-num" dir="ltr">
                                +{{ number_format(($stats['trainings_count'] ?? 0) > 0 ? $stats['trainings_count'] : 35) }}
                            </div>
                            <div class="stat-box-title">برنامج تدريبي وتأهيلي</div>
                            <div class="stat-box-sub">
                                <i class="fas fa-chart-line" style="color: #fbbf24; font-size: 0.72rem;"></i>
                                <span>تأهيل احترافي مستمر</span>
                            </div>
                        </div>
                    </div>

                    <!-- البطاقة 4: فرصة عمل وترشيح -->
                    <div class="col-6 col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="card_box ribbon-cyan">
                            <span class="card_ribbon" data-ribbon="نشط الآن"></span>
                            <div class="stat-icon-wrap" style="background: rgba(2, 132, 199, 0.22); color: #38bdf8;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stat-box-num" dir="ltr">
                                +{{ number_format(($stats['opportunities_count'] ?? 0) > 0 ? $stats['opportunities_count'] : 10) }}
                            </div>
                            <div class="stat-box-title">فرصة عمل وترشيح</div>
                            <div class="stat-box-sub">
                                <i class="fas fa-bullseye" style="color: #38bdf8; font-size: 0.72rem;"></i>
                                <span>ربط مباشر بسوق العمل</span>
                            </div>
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
                        <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill mb-2 shadow-xs"
                            style="background: rgba(13, 56, 130, 0.08); border: 1px solid rgba(13, 56, 130, 0.18);">
                            <i class="fas fa-calendar-alt text-primary"></i>
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
                                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3"
                                        style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4);">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="text-warning fw-bold small">حدث سنوي معتمد — جامعة طرابلس</span>
                                    </div>

                                    <h2 class="display-6 fw-bold mb-3 text-white">
                                        {{ $fair->title }}
                                    </h2>
                                    <p class="text-white-50 mb-4"
                                        style="font-size: 1.05rem; line-height: 1.7; max-width: 680px;">
                                        {{ $fair->description ?? 'فرصتكم الذهبية للقاء مسؤولي الموارد البشرية بكبرى الشركات الوطنية والدولية، إجراء المقابلات الفورية، حجز بطاقتكم الرقمية الذكية المزودة برمز QR، واستكشاف أحدث الوظائف.' }}
                                    </p>
                                    <div class="d-flex align-items-center gap-3 text-white-50 small mb-4 flex-wrap">
                                        <span><i class="fas fa-map-marker-alt text-warning me-1"></i>
                                            {{ $fair->location ?? 'الحرم الجامعي — جامعة طرابلس' }}</span>
                                        <span>•</span>
                                        <span><i class="fas fa-calendar-alt text-warning me-1"></i> الموعد:
                                            {{ $fair->event_date ? \Carbon\Carbon::parse($fair->event_date)->translatedFormat('d F Y') : 'يحدد لاحقاً' }}</span>
                                        <span>•</span>
                                        <span><i class="fas fa-qrcode text-warning me-1"></i> تذاكر وبطاقات ذكية موحدة</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <a href="{{ route('job-fair.public', $fair->id) }}"
                                            class="btn btn-gold fw-bold rounded-pill px-4 py-2.5 d-flex align-items-center gap-2">
                                            <span>استكشف المعرض والشركات</span>
                                            <i class="fas fa-arrow-left small"></i>
                                        </a>
                                        @if(!$fair->registration_open)
                                            <span class="badge rounded-pill px-3 py-2 text-white-50"
                                                style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">
                                                <i class="fas fa-clock me-1 text-warning"></i>التسجيل متاح قريباً
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4 text-center">
                                    <div class="fair-book-stage">
                                        <div class="fair-book" id="fairBook_{{ $fair->id }}"
                                            onclick="this.classList.toggle('book-opened')" title="انقر أو مرر لفتح البطاقة">
                                            <!-- الطبقات الورقية السميكة لدفتر/بطاقة المعرض -->
                                            <div class="fair-book-pages"></div>

                                            <!-- داخل البطاقة: تصريح المعرض الذكي مع رمز QR -->
                                            <div class="fair-book-inner">
                                                <div class="fair-card-chip-bar">
                                                    <span class="fair-card-chip">
                                                        <i class="fas fa-microchip"></i>
                                                        <span>UOT PASS</span>
                                                    </span>
                                                    <span
                                                        class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5"
                                                        style="font-size: 0.68rem;">
                                                        <i class="fas fa-check-circle me-1"></i>معتمدة رقمياً
                                                    </span>
                                                </div>

                                                <div class="fair-card-header">
                                                    <h6 class="fair-card-title">{{ $fair->title }}</h6>
                                                    <div class="fair-card-subtitle">
                                                        <i class="fas fa-university me-1 text-primary"></i>جامعة طرابلس
                                                    </div>
                                                </div>

                                                <!-- إطار رمز الـ QR مع تأثير الليزر المتحرك -->
                                                <div class="fair-card-qr-box">
                                                    <div class="qr-corner qr-tl"></div>
                                                    <div class="qr-corner qr-tr"></div>
                                                    <div class="qr-corner qr-bl"></div>
                                                    <div class="qr-corner qr-br"></div>
                                                    <div class="fair-card-laser"></div>
                                                    <svg class="fair-card-qr-svg" viewBox="0 0 100 100"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <!-- QR Pattern Elements -->
                                                        <rect x="8" y="8" width="26" height="26" rx="4" fill="#0d3882" />
                                                        <rect x="13" y="13" width="16" height="16" rx="2" fill="#ffffff" />
                                                        <rect x="17" y="17" width="8" height="8" rx="1" fill="#0d3882" />

                                                        <rect x="66" y="8" width="26" height="26" rx="4" fill="#0d3882" />
                                                        <rect x="71" y="13" width="16" height="16" rx="2" fill="#ffffff" />
                                                        <rect x="75" y="17" width="8" height="8" rx="1" fill="#0d3882" />

                                                        <rect x="8" y="66" width="26" height="26" rx="4" fill="#0d3882" />
                                                        <rect x="13" y="71" width="16" height="16" rx="2" fill="#ffffff" />
                                                        <rect x="17" y="75" width="8" height="8" rx="1" fill="#0d3882" />

                                                        <!-- Data Bits with Gold Accent -->
                                                        <rect x="40" y="10" width="7" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="52" y="10" width="7" height="7" rx="1.5" fill="#f59e0b" />
                                                        <rect x="40" y="22" width="7" height="14" rx="1.5" fill="#0d3882" />
                                                        <rect x="52" y="22" width="7" height="7" rx="1.5" fill="#0d3882" />

                                                        <rect x="10" y="42" width="14" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="29" y="42" width="7" height="7" rx="1.5" fill="#f59e0b" />
                                                        <rect x="41" y="42" width="18" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="64" y="42" width="7" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="76" y="42" width="14" height="7" rx="1.5" fill="#0d3882" />

                                                        <rect x="41" y="54" width="7" height="7" rx="1.5" fill="#f59e0b" />
                                                        <rect x="53" y="54" width="18" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="76" y="54" width="14" height="7" rx="1.5" fill="#0d3882" />

                                                        <rect x="41" y="66" width="7" height="16" rx="1.5" fill="#0d3882" />
                                                        <rect x="53" y="66" width="14" height="7" rx="1.5" fill="#0d3882" />
                                                        <rect x="72" y="66" width="7" height="7" rx="1.5" fill="#f59e0b" />
                                                        <rect x="84" y="66" width="7" height="16" rx="1.5" fill="#0d3882" />

                                                        <rect x="53" y="78" width="22" height="10" rx="1.5" fill="#0d3882" />
                                                        <rect x="41" y="87" width="7" height="7" rx="1.5" fill="#0d3882" />
                                                    </svg>
                                                </div>

                                                <div class="fair-card-meta">
                                                    <div class="fair-card-meta-item">
                                                        <i class="fas fa-map-marker-alt text-warning small"></i>
                                                        <span>{{ Str::limit($fair->location ?? 'الحرم الجامعي', 24) }}</span>
                                                    </div>
                                                    <div class="fair-card-meta-item">
                                                        <i class="fas fa-calendar-alt text-warning small"></i>
                                                        <span>{{ $fair->event_date ? \Carbon\Carbon::parse($fair->event_date)->translatedFormat('d F Y') : 'قريباً' }}</span>
                                                    </div>
                                                </div>

                                                <div class="w-100 pt-1">
                                                    <div class="fair-card-status-badge">
                                                        <i class="fas fa-shield-check text-success"></i>
                                                        <span>تصريح دخول رسمي معتمد</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- غلاف الكتاب ثلاثي الأبعاد بتدرج كحلي وذهبي وتفاصيل راقية -->
                                            <div class="fair-book-cover">
                                                <div class="fair-book-spine-line"></div>

                                                <div class="fair-cover-crest-wrap">
                                                    <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس"
                                                        class="fair-cover-crest-img" onerror="this.onerror=null;">
                                                </div>

                                                @if($fair->white_logo_url)
                                                    <div class="my-1">
                                                        <img src="{{ $fair->white_logo_url }}" alt="{{ $fair->title }}"
                                                            class="img-fluid"
                                                            style="max-height: 52px; max-width: 155px; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.4));"
                                                            onerror="this.onerror=null;this.src='{{ $fair->logo_url }}';">
                                                    </div>
                                                @elseif($fair->logo_url)
                                                    <div class="my-1 bg-white rounded-3 p-1.5 shadow-sm d-inline-block">
                                                        <img src="{{ $fair->logo_url }}" alt="{{ $fair->title }}" class="img-fluid"
                                                            style="max-height: 44px; max-width: 130px; object-fit: contain;"
                                                            onerror="this.onerror=null;this.style.display='none'">
                                                    </div>
                                                @else
                                                    <div class="my-1 text-warning">
                                                        <i class="fas fa-id-card-clip fa-3x"></i>
                                                    </div>
                                                @endif

                                                <div>
                                                    <div class="fair-cover-badge">
                                                        <i class="fas fa-star text-warning"></i>
                                                        <span>بطاقة المعرض الرقمية</span>
                                                    </div>
                                                    <h5 class="fair-cover-title">
                                                        {{ $fair->title }}
                                                    </h5>
                                                    <small class="text-white-50" style="font-size: 0.7rem;">جامعة طرابلس • تصريح
                                                        رسمي</small>
                                                </div>

                                                <div class="fair-cover-hint">
                                                    <i class="fas fa-hand-pointer"></i>
                                                    <span>مرر أو انقر لفتح البطاقة</span>
                                                </div>
                                            </div>
                                        </div>
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
                                    <div class="carousel-caption d-none d-md-block p-3 rounded-3"
                                        style="background: rgba(11, 31, 58, 0.65); backdrop-filter: blur(6px); max-width: 600px; margin: 0 auto;">
                                        <h5 class="text-white mb-0 fw-bold">{{ $image->caption }}</h5>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($welcomeImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon p-3 rounded-circle"
                                style="background-color: rgba(0,0,0,0.4);" aria-hidden="true"></span>
                            <span class="visually-hidden">السابق</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon p-3 rounded-circle"
                                style="background-color: rgba(0,0,0,0.4);" aria-hidden="true"></span>
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
                    <span class="badge rounded-pill px-3 py-1.5 small fw-bold mb-2"
                        style="background: #eff6ff; color: #1565c0; border: 1px solid #bfdbfe;">
                        <i class="fas fa-chalkboard-teacher me-1"></i>بناء القدرات والكفاءات
                    </span>
                    <h2 class="display-6 fw-bold text-dark mb-2">أحدث البرامج والدورات التدريبية</h2>
                    <p class="text-muted small mx-auto" style="max-width: 600px;">برامج معتمدة من جامعة طرابلس تستهدف سد
                        الفجوة بين المناهج الأكاديمية واحتياجات سوق العمل الحقيقية.</p>
                </div>

                <div class="row g-4">
                    @foreach($advertisedTrainings as $index => $training)
                        <div class="col-lg-4 col-md-6 training-card {{ $index >= 3 ? 'hidden-training' : '' }}"
                            data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}"
                            style="{{ $index >= 3 ? 'display: none;' : '' }}">
                            <div class="uiverse-train-card">
                                <div class="title-card">
                                    <p>
                                        <i class="fas fa-certificate text-warning"></i>
                                        <span>برنامج تدريبي معتمد</span>
                                    </p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M10.277 16.515c.005-.11.187-.154.24-.058c.254.45.686 1.111 1.177 1.412c.49.3 1.275.386 1.791.408c.11.005.154.186.058.24c-.45.254-1.111.686-1.412 1.176s-.386 1.276-.408 1.792c-.005.11-.187.153-.24.057c-.254-.45-.686-1.11-1.176-1.411s-1.276-.386-1.792-.408c-.11-.005-.153-.187-.057-.24c.45-.254 1.11-.686 1.411-1.177c.301-.49.386-1.276.408-1.791m8.215-1c-.008-.11-.2-.156-.257-.062c-.172.283-.421.623-.697.793s-.693.236-1.023.262c-.11.008-.155.2-.062.257c.283.172.624.42.793.697s.237.693.262 1.023c.009.11.2.155.258.061c.172-.282.42-.623.697-.792s.692-.237 1.022-.262c.11-.009.156-.2.062-.258c-.283-.172-.624-.42-.793-.697s-.236-.692-.262-1.022M14.704 4.002l-.242-.306c-.937-1.183-1.405-1.775-1.95-1.688c-.545.088-.806.796-1.327 2.213l-.134.366c-.149.403-.223.604-.364.752c-.143.148-.336.225-.724.38l-.353.141l-.248.1c-1.2.48-1.804.753-1.881 1.283c-.082.565.49 1.049 1.634 2.016l.296.25c.325.275.488.413.58.6c.094.187.107.403.134.835l.024.393c.093 1.52.14 2.28.634 2.542s1.108-.147 2.336-.966l.318-.212c.35-.233.524-.35.723-.381c.2-.032.402.024.806.136l.368.102c1.422.394 2.133.591 2.52.188c.388-.403.196-1.14-.19-2.613l-.099-.381c-.11-.419-.164-.628-.134-.835s.142-.389.365-.752l.203-.33c.786-1.276 1.179-1.914.924-2.426c-.254-.51-.987-.557-2.454-.648l-.379-.024c-.417-.026-.625-.039-.806-.135c-.18-.096-.314-.264-.58-.6m-5.869 9.324C6.698 14.37 4.919 16.024 4.248 18c-.752-4.707.292-7.747 1.965-9.637c.144.295.332.539.5.73c.35.396.852.82 1.362 1.251l.367.31l.17.145c.005.064.01.14.015.237l.03.485c.04.655.08 1.294.178 1.805" />
                                    </svg>
                                </div>
                                <div class="card-content">
                                    <span class="card-category">
                                        <i class="fas fa-graduation-cap text-warning"></i>
                                        <span>تأهيل الكفاءات • جامعة طرابلس</span>
                                    </span>
                                    <h5 class="card-title-main" title="{{ $training->title }}">{{ $training->title }}</h5>
                                    <div class="plain">
                                        @php
                                            $durDigits = preg_replace('/[^0-9]/', '', $training->duration);
                                            $durIsHours = Str::contains(strtolower($training->duration), 'hour') || Str::contains($training->duration, 'ساعة');
                                        @endphp
                                        <span>{{ $durDigits ?: $training->duration }}</span>
                                        <span>{{ $durIsHours ? 'ساعة تدريبية معتمدة' : 'أيام تدريبية مكثفة' }}</span>
                                    </div>
                                    <p class="description">
                                        {{ Str::limit(strip_tags($training->description), 115) }}
                                    </p>
                                    <div class="card-meta-pill">
                                        <span><i class="fas fa-calendar-alt text-warning me-1"></i>
                                            {{ \Carbon\Carbon::parse($training->start_date)->format('Y/m/d') }}</span>
                                        <span><i class="fas fa-map-marker-alt text-info me-1"></i>
                                            {{ $training->location ?? 'جامعة طرابلس' }}</span>
                                    </div>
                                    @auth
                                        <a href="{{ route('graduate.trainings.show', $training->id) }}" class="card-btn">
                                            <span>تفاصيل التدريب والتقديم</span>
                                            <i class="fas fa-arrow-left ms-1 small"></i>
                                        </a>
                                    @else
                                        <button type="button" class="card-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
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
                        <button id="showMoreBtn" class="btn btn-gold rounded-pill px-4 py-2.5 shadow-sm"
                            onclick="showMoreTrainings()">
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

    <!-- ==================== 6. رحلة الخريج (How It Works - Connected Stepper) ==================== -->
    <section class="journey-section" id="journey">
        <!-- Ambient Glow Spots -->
        <div class="journey-glow-top"></div>
        <div class="journey-glow-bottom"></div>

        <div class="container position-relative" style="z-index: 2;">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-down">
                <div class="journey-eyebrow">
                    <span class="journey-eyebrow-dot"></span>
                    <i class="fas fa-route small"></i>
                    <span>مسارك المعتمد نحو المستقبل</span>
                </div>
                <h2 class="journey-main-title">
                    رحلة الخريج في <span class="gold-word">4 خطوات متصلة</span>
                </h2>
                <p class="journey-sub mx-auto mb-0">
                    صممنا المنصة لترافقك خطوة بخطوة منذ يوم تخرجك وحتى حصولك على وظيفتك الأولى، عبر آليات ذكية تضمن
                    تأهيلك ومطابقتك مع أفضل الفرص.
                </p>
            </div>

            <!-- Connected Horizontal Stepper -->
            <div class="journey-stepper" data-aos="fade-up" data-aos-delay="100">
                <!-- Glowing Connection Track (Desktop) -->
                <div class="journey-stepper-track"></div>

                <!-- الخطوة 1: التسجيل الأكاديمي -->
                <div class="journey-step" data-aos="fade-up" data-aos-delay="150">
                    <div class="journey-step-num">
                        <div class="step-num-inner">
                            <i class="fas fa-id-card-clip step-num-icon"></i>
                            <span class="step-ordinal">01</span>
                        </div>
                    </div>
                    <div class="journey-step-body">
                        <div class="journey-step-label">المرحلة الأولى</div>
                        <h3 class="journey-step-title">التسجيل الأكاديمي</h3>
                        <p class="journey-step-desc">
                            أنشئ حسابك المهني الموحد، وثّق بيانات كليتك وتخصصك ومعدلك، وأبرز مهاراتك بدقة.
                        </p>
                        <ul class="journey-step-bullets">
                            <li><i class="fas fa-check"></i><span>ملف خريج معتمد وموثق</span></li>
                            <li><i class="fas fa-check"></i><span>سيرة ذاتية احترافية تلقائية</span></li>
                            <li><i class="fas fa-check"></i><span>لوحة تحكم تفاعلية للمتابعة</span></li>
                        </ul>
                        <div class="journey-step-footer">
                            <span>الخطوة 01</span>
                            <div class="journey-step-progress">
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar"></span>
                                <span class="jsp-bar"></span>
                                <span class="jsp-bar"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 2: التأهيل والتدريب -->
                <div class="journey-step" data-aos="fade-up" data-aos-delay="250">
                    <div class="journey-step-num">
                        <div class="step-num-inner">
                            <i class="fas fa-laptop-code step-num-icon"></i>
                            <span class="step-ordinal">02</span>
                        </div>
                    </div>
                    <div class="journey-step-body">
                        <div class="journey-step-label">المرحلة الثانية</div>
                        <h3 class="journey-step-title">التأهيل والتدريب</h3>
                        <p class="journey-step-desc">
                            انضم للبرامج التطبيقية وورش العمل المتخصصة لصقل قدراتك وجاهزيتك لسوق العمل.
                        </p>
                        <ul class="journey-step-bullets">
                            <li><i class="fas fa-check"></i><span>ورش عمل حضورية وتفاعلية</span></li>
                            <li><i class="fas fa-check"></i><span>شهادات حضور برمز QR معتمد</span></li>
                            <li><i class="fas fa-check"></i><span>تدريب بإشراف نخبة من الخبراء</span></li>
                        </ul>
                        <div class="journey-step-footer">
                            <span>الخطوة 02</span>
                            <div class="journey-step-progress">
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar"></span>
                                <span class="jsp-bar"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 3: الترشيح الوظيفي -->
                <div class="journey-step" data-aos="fade-up" data-aos-delay="350">
                    <div class="journey-step-num">
                        <div class="step-num-inner">
                            <i class="fas fa-briefcase step-num-icon"></i>
                            <span class="step-ordinal">03</span>
                        </div>
                    </div>
                    <div class="journey-step-body">
                        <div class="journey-step-label">المرحلة الثالثة</div>
                        <h3 class="journey-step-title">الترشيح الوظيفي</h3>
                        <p class="journey-step-desc">
                            يرشحك المكتب مباشرة للوظائف الشاغرة لدى الشركات الشريكة بحسب كفاءتك وتخصصك.
                        </p>
                        <ul class="journey-step-bullets">
                            <li><i class="fas fa-check"></i><span>مطابقة ذكية للفرص المتاحة</span></li>
                            <li><i class="fas fa-check"></i><span>شراكات رسمية مع الشركات</span></li>
                            <li><i class="fas fa-check"></i><span>مشاركة حصرية بمعارض التوظيف</span></li>
                        </ul>
                        <div class="journey-step-footer">
                            <span>الخطوة 03</span>
                            <div class="journey-step-progress">
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الخطوة 4: المقابلات والتوظيف (Goal) -->
                <div class="journey-step journey-step-goal" data-aos="fade-up" data-aos-delay="450">
                    <div class="journey-step-num">
                        <div class="step-num-inner">
                            <i class="fas fa-handshake step-num-icon"></i>
                            <span class="step-ordinal">04</span>
                        </div>
                    </div>
                    <div class="journey-step-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="journey-step-label mb-0">المرحلة النهائية</span>
                            <span class="goal-achieved-tag"><i class="fas fa-star small"></i>الهدف</span>
                        </div>
                        <h3 class="journey-step-title">المقابلات والتوظيف</h3>
                        <p class="journey-step-desc">
                            استقبل مواعيد مقابلاتك عبر المنصة، وتابع حالة الترشيح حتى الحصول على عرض العمل.
                        </p>
                        <ul class="journey-step-bullets">
                            <li><i class="fas fa-check"></i><span>جدولة ومتابعة المقابلات</span></li>
                            <li><i class="fas fa-check"></i><span>إشعارات فورية بالنتائج والقرارات</span></li>
                            <li><i class="fas fa-check"></i><span>انطلاقة آمنة لمسارك المهني</span></li>
                        </ul>
                        <div class="journey-step-footer">
                            <span class="text-warning"><i class="fas fa-award me-1 small"></i>الهدف المحقق</span>
                            <div class="journey-step-progress">
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                                <span class="jsp-bar active"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Journey CTA Banner -->
            <div class="journey-cta-banner mt-5" data-aos="fade-up" data-aos-delay="450">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7 text-center text-lg-start text-rtl">
                        <div
                            class="d-inline-flex align-items-center gap-2 mb-2 flex-wrap justify-content-center justify-content-lg-start">
                            <span class="journey-cta-badge">
                                <i class="fas fa-sparkles text-warning me-1"></i>انطلاقة مسارك تبدأ هنا
                            </span>
                            <span class="text-white-50 small">
                                <i class="fas fa-shield-halved text-success me-1"></i>خدمة مجانية رسمية لخريجي جامعة
                                طرابلس
                            </span>
                        </div>
                        <h4 class="fw-bold text-white mb-2 fs-5">هل أنت خريج وجاهز لبدء رحلتك المهنية اليوم؟</h4>
                        <p class="text-white-50 small mb-0" style="max-width: 600px;">
                            أنشئ حسابك في خطوات معدودة لتصل سيرتك الذاتية إلى أصحاب الأعمال وتلتحق بأحدث البرامج
                            التأهيلية.
                        </p>
                    </div>
                    <div class="col-lg-5 text-center text-lg-end">
                        <div
                            class="d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-center justify-content-lg-end gap-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-journey-primary text-nowrap">
                                    <i class="fas fa-th-large ms-1"></i>
                                    <span>لوحة التحكم الخاصة بك</span>
                                    <i class="fas fa-arrow-left ms-1 small"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-journey-primary text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#graduateRegisterModal">
                                    <i class="fas fa-user-plus ms-1"></i>
                                    <span>ابدأ بالتسجيل كخريج</span>
                                    <i class="fas fa-arrow-left ms-1 small"></i>
                                </button>
                                <button type="button" class="btn btn-journey-outline text-nowrap" data-bs-toggle="modal"
                                    data-bs-target="#loginModal">
                                    <i class="fas fa-sign-in-alt ms-1"></i>
                                    <span>تسجيل الدخول</span>
                                </button>
                            @endauth
                        </div>
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
                                            <div class="bento-news-card p-3 p-md-4 transition-all"
                                                style="transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                                <div class="d-flex gap-3 align-items-start">
                                                    @if($news->thumbnail_url)
                                                        <img src="{{ $news->thumbnail_url }}"
                                                            class="rounded-3 flex-shrink-0 object-fit-cover" alt=""
                                                            style="width: 84px; height: 84px;"
                                                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center text-primary\' style=\'width: 84px; height: 84px; background: rgba(13, 56, 130, 0.08);\'><i class=\'fas fa-newspaper fs-3 opacity-50\'></i></div>';">
                                                    @else
                                                        <div class="rounded-3 flex-shrink-0 d-flex align-items-center justify-content-center text-primary"
                                                            style="width: 84px; height: 84px; background: rgba(13, 56, 130, 0.08);">
                                                            <i class="fas fa-newspaper fs-3 opacity-50"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <h6 class="fw-bold text-dark mb-1 fs-6"
                                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;"
                                                            title="{{ $news->title }}">{{ $news->title }}</h6>
                                                        <p class="text-muted small mb-2"
                                                            style="font-size: 0.84rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                            {{ $news->excerpt }}
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                                <i class="far fa-calendar-alt me-1 text-primary"></i>
                                                                {{ $news->published_at ? $news->published_at->format('Y/m/d') : '' }}
                                                            </small>
                                                            <span class="small fw-bold text-primary">
                                                                قراءة الخبر <i class="fas fa-arrow-left ms-1"
                                                                    style="font-size: 0.7rem;"></i>
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
                                        <a href="{{ route('public.announcements.show', $announcement) }}"
                                            class="text-decoration-none d-block">
                                            <div class="bento-news-card p-3 p-md-4 transition-all"
                                                style="border-right: 4px solid var(--gold-accent); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                                <h6 class="fw-bold text-dark mb-1 fs-6"
                                                    style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.45;"
                                                    title="{{ $announcement->title }}">{{ $announcement->title }}</h6>
                                                <p class="text-muted small mb-2"
                                                    style="font-size: 0.84rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                                    {{ $announcement->excerpt }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="far fa-clock me-1 text-warning"></i>
                                                        {{ $announcement->created_at->diffForHumans() }}
                                                    </small>
                                                    <span class="small fw-bold text-primary">
                                                        التفاصيل الكاملة <i class="fas fa-arrow-left ms-1"
                                                            style="font-size: 0.7rem;"></i>
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
                <span class="badge rounded-pill px-3 py-1.5 small fw-bold mb-2"
                    style="background: #eff6ff; color: #1565c0; border: 1px solid #bfdbfe;">
                    <i class="fas fa-bullseye me-1"></i>رؤيتنا ورسالتنا
                </span>
                <h2 class="display-6 fw-bold text-dark mb-2">أهداف مكتب تدريب الخريجين</h2>
                <p class="text-muted small mx-auto" style="max-width: 600px;">نسعى لتحقيق التميز الوطني في تدريب وتأهيل
                    الكفاءات الليبية الشابة لدخول سوق العمل بجدارة.</p>
            </div>

            <!-- بطاقة الشاشة العريضة الموحدة للأهداف (Wide HUD Screen Card) -->
            <div class="goals-hud-screen-border" data-aos="zoom-in-up" data-aos-duration="800">
                <div class="goals-hud-screen-bg">

                    <!-- الترويسة العلوية للشاشة مع شعار المكتب الرسمي -->
                    <div class="goals-screen-header">
                        <div class="goals-screen-badge-box">
                            <div class="hud-office-logo-frame">
                                <img src="{{ asset('images/gto_logo.jpg') }}" alt="شعار مكتب تدريب الخريجين"
                                    class="hud-office-logo-img" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                            </div>
                            <div class="goals-screen-title-area">
                                <h3>
                                    <span>نهدف إلى</span>
                                    <i class="fas fa-bullseye text-primary ms-1 fs-6"></i>
                                </h3>
                                <p>مكتب تدريب الخريجين • جامعة طرابلس</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="goals-screen-status-pill">
                                <span class="hud-pulse-dot"></span>
                            </div>
                        </div>
                    </div>

                    <!-- شريط الماركيه المتحرك في الخلفية (Marquee Text) -->
                    <div class="goals-hud-marquee">
                        <div class="goals-hud-marquee-inner" aria-hidden="true">
                            <span class="goals-hud-giant-text">GRADUATE TRAINING OFFICE • UNIVERSITY OF TRIPOLI •
                                مكتب تدريب الخريجين • </span>
                            <span class="goals-hud-giant-text">GRADUATE TRAINING OFFICE • UNIVERSITY OF TRIPOLI • أهداف
                                • </span>
                        </div>
                    </div>

                    <!-- الضباب المتوهج (Glow Mist) -->
                    <div class="hud-mist-container">
                        <div class="hud-mist"></div>
                    </div>

                    <!-- الأهداف الثلاثة مكتوبة وموزعة بوضوح تام داخل الشاشة -->
                    <div class="row g-4 goals-hud-grid">
                        <!-- الهدف 1 -->
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="goal-hud-card">
                                <div>
                                    <div class="goal-hud-card-top">
                                        <div class="goal-hud-icon-badge goal-hud-icon-1">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <span class="goal-hud-num">GOAL 1</span>
                                    </div>
                                    <h4 class="goal-hud-title">تطوير مهني احترافي</h4>
                                    <p class="goal-hud-desc">
                                        تقديم حزم تدريبية دورية تركز على المهارات التقنية، القيادية، واللغوية المطلوبة
                                        من أرباب العمل في سوق العمل المعاصر.
                                    </p>
                                </div>
                                <div class="goal-hud-tags">
                                    <span class="goal-hud-tag-item"><i class="fas fa-certificate text-warning"></i>
                                        برامج تطبيقية</span>
                                    <span class="goal-hud-tag-item"><i class="fas fa-user-tie text-info"></i> خبراء
                                        ومدربون</span>
                                </div>
                            </div>
                        </div>

                        <!-- الهدف 2 -->
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="goal-hud-card">
                                <div>
                                    <div class="goal-hud-card-top">
                                        <div class="goal-hud-icon-badge goal-hud-icon-2">
                                            <i class="fas fa-handshake"></i>
                                        </div>
                                        <span class="goal-hud-num">GOAL 2</span>
                                    </div>
                                    <h4 class="goal-hud-title">شراكات عمل استراتيجية</h4>
                                    <p class="goal-hud-desc">
                                        بناء مذكرات تفاهم وشراكات مستدامة مع القطاعين العام والخاص لتسهيل استيعاب
                                        الخريجين في برامج تدريب وتوظيف واعدة.
                                    </p>
                                </div>
                                <div class="goal-hud-tags">
                                    <span class="goal-hud-tag-item"><i class="fas fa-building text-warning"></i> قطاع
                                        عام وخاص</span>
                                    <span class="goal-hud-tag-item"><i class="fas fa-calendar-alt text-info"></i> معارض
                                        توظيف</span>
                                </div>
                            </div>
                        </div>

                        <!-- الهدف 3 -->
                        <div class="col-lg-4 col-md-12" data-aos="fade-up" data-aos-delay="300">
                            <div class="goal-hud-card">
                                <div>
                                    <div class="goal-hud-card-top">
                                        <div class="goal-hud-icon-badge goal-hud-icon-3">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <span class="goal-hud-num">GOAL 3</span>
                                    </div>
                                    <h4 class="goal-hud-title">متابعة الأثر والتقييم المستمر</h4>
                                    <p class="goal-hud-desc">
                                        توثيق معدلات التوظيف ونسب نجاح الخريجين واستطلاع آراء الشركات لتحسين مخرجات
                                        التعليم الجامعي ورفع الجاهزية المهنية.
                                    </p>
                                </div>
                                <div class="goal-hud-tags">
                                    <span class="goal-hud-tag-item"><i class="fas fa-poll text-warning"></i> قياس مؤشرات
                                        التوظيف</span>
                                    <span class="goal-hud-tag-item"><i class="fas fa-award text-success"></i> جودة
                                        مستدامة</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- التذييل السفلي للشاشة التكنولوجية -->
                    <div class="goals-screen-footer">
                        <div class="hud-footer-vision">


                        </div>
                        <div class="hud-footer-code">
                            <span>UOT-GTO </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ==================== 10. تذييل الصفحة الجامعي الشامل (Mega Footer) ==================== -->
    <footer class="main-footer" id="contact">
        <div class="container">
            <div class="row g-4 pb-4 ">
                <!-- Column 1: عن المنصة -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="footer-brand-logo-frame">
                            <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس" class="footer-brand-logo-img"
                                onerror="this.onerror=null;">
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0 fs-6">جامعة طرابلس</h5>
                            <small class="text-white-50" style="font-size: 0.78rem;">مكتب تدريب الخريجين</small>
                        </div>
                    </div>
                    <p class="text-white-50 small mb-3" style="line-height: 1.8;">
                        المنصة الإلكترونية الموحدة لربط خريجي جامعة طرابلس بسوق العمل، وتقديم البرامج التدريبية المعتمدة
                        وتنظيم معارض التوظيف السنوية.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://uot.edu.ly/gto/" target="_blank" rel="noopener noreferrer"
                            class="footer-social-btn" title="الموقع الرسمي لمكتب تدريب الخريجين">
                            <i class="fas fa-globe"></i>
                        </a>
                        <a href="https://www.facebook.com/p/%D9%85%D9%83%D8%AA%D8%A8-%D8%AA%D8%AF%D8%B1%D9%8A%D8%A8-%D8%A7%D9%84%D8%AE%D8%B1%D9%8A%D8%AC%D9%8A%D9%86-%D8%AC%D8%A7%D9%85%D8%B9%D8%A9-%D8%B7%D8%B1%D8%A8%D9%84%D8%B3-61554996546918/"
                            target="_blank" rel="noopener noreferrer" class="footer-social-btn"
                            title="صفحة فيسبوك الرسمية">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/traning2024office/" target="_blank"
                            rel="noopener noreferrer" class="footer-social-btn" title="صفحة لينكد إن الرسمية">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.tiktok.com/@graduate_training_office" target="_blank"
                            rel="noopener noreferrer" class="footer-social-btn" title="حساب تيك توك الرسمي">
                            <i class="fab fa-tiktok"></i>
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
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#graduateRegisterModal"
                                class="footer-link">تسجيل خريج جديد</a></li>
                        <li><a href="{{ route('company.register') }}" class="footer-link"><i
                                    class="fas fa-building me-1 text-warning"></i>تسجيل شركة أو مؤسسة جديدة</a></li>
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#loginModal"
                                class="footer-link">تسجيل الدخول للنظام</a></li>
                        @if(isset($activeFair) && $activeFair && !$activeFair->is_ended)
                            <li><a href="{{ route('job-fair.public', $activeFair->id) }}" class="footer-link">بوابة
                                    {{ $activeFair->title }}</a></li>
                        @endif
                        <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
                                class="footer-link">استعادة كلمة المرور</a></li>
                    </ul>
                </div>

                <!-- Column 4: بيانات التواصل -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white fw-bold mb-3 fs-6">بيانات التواصل</h6>
                    <ul class="list-unstyled text-white-50 small mb-0">
                        <li class="d-flex align-items-start gap-2 mb-2">
                            <span class="footer-contact-icon mt-1"><i class="fas fa-map-marker-alt"></i></span>
                            <a href="https://maps.app.goo.gl/U8z9xwycDn9qAYz47" target="_blank"
                                rel="noopener noreferrer" class="text-white-50 text-decoration-none text-hover-warning"
                                title="عرض موقع مكتب تدريب الخريجين على خرائط جوجل">
                                جامعة طرابلس، سيدي المصري، طرابلس — ليبيا
                                <i class="fas fa-external-link-alt ms-1 text-warning" style="font-size: 0.7rem;"></i>
                            </a>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <span class="footer-contact-icon"><i class="fas fa-envelope"></i></span>
                            <a href="mailto:officetraining@uot.edu.ly"
                                class="text-white-50 text-decoration-none text-hover-warning">officetraining@uot.edu.ly</a>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-2">
                            <span class="footer-contact-icon"><i class="fas fa-phone-alt"></i></span>
                            <a href="tel:0930822581" class="text-white-50 text-decoration-none text-hover-warning"
                                dir="ltr">093-0822581</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <span class="footer-contact-icon"><i class="fas fa-clock"></i></span>
                            <span>الأحد – الخميس: 8:30 ص – 2:30 م</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-top border-secondary pt-4 mt-2 text-center text-white-50 small d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>© {{ date('Y') }} جامعة طرابلس — جميع الحقوق محفوظة لمكتب تدريب الخريجين.</span>
                <span class="text-warning">منصة مكتب تدريب الخريجين</span>
            </div>
        </div>
    </footer>

    <!-- ==================== 11. نافذة تسجيل الدخول المنبثقة (Login Modal) ==================== -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true"
        style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button"
                        class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none"
                        data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block"
                        onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="loginModalLabel">تسجيل الدخول</h4>
                    <p class="mb-0 text-white-50 small">مكتب تدريب الخريجين — جامعة طرابلس</p>
                </div>
                <div class="modal-login-body">
                    @if(session('success'))
                        <div
                            class="alert alert-success border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span class="fw-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('status'))
                        <div
                            class="alert alert-success border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span class="fw-semibold">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if($errors->any() && !request('open_forgot') && !request('open_verify') && !request('open_register') && !old('reset_form') && !old('verify_form') && !old('from_register_modal'))
                        <div
                            class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
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
                            <input type="text"
                                class="form-control modal-login-input @error('email') is-invalid @enderror"
                                id="modalEmail" name="email" value="{{ old('email') }}"
                                placeholder="البريد الإلكتروني أو اسم المستخدم (مثال: admin)" required autofocus>
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="modalPassword" class="form-label fw-bold text-dark small mb-0">
                                    <i class="fas fa-lock text-primary me-1"></i>كلمة المرور
                                </label>
                                <a class="small text-decoration-none fw-bold" href="javascript:void(0)"
                                    id="btnOpenForgotModal" style="color: #1565c0; font-size: 0.8rem; cursor: pointer;">
                                    نسيت كلمة المرور؟
                                </a>
                            </div>
                            <div class="position-relative">
                                <input type="password"
                                    class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror"
                                    id="modalPassword" name="password" placeholder="••••••••" required>
                                <button type="button"
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0"
                                    id="btnToggleModalPass" style="z-index: 5;">
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

                        <button type="submit"
                            class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mt-2">
                            <i class="fas fa-sign-in-alt"></i><span>دخول إلى النظام</span>
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted small mb-2">
                            خريج جديد ولم تسجل بعد؟
                            <a href="javascript:void(0)" id="btnOpenRegisterFromLogin"
                                class="fw-bold text-decoration-none ms-1" style="color: #1565c0; cursor: pointer;">
                                تسجيل خريج جديد <i class="fas fa-arrow-left ms-1 small"></i>
                            </a>
                        </p>
                        <div class="pt-2 border-top border-light">
                            <p class="text-muted small mb-0">
                                تمثل شركة أو مؤسسة وتبحث عن كفاءات؟
                                <a href="{{ route('company.register') }}" class="fw-bold text-decoration-none ms-1"
                                    style="color: #0284c7;">
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
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button"
                        class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none"
                        data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block"
                        onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="forgotPasswordModalLabel">استعادة كلمة المرور</h4>
                    <p class="mb-0 text-white-50 small">أدخل بريدك الإلكتروني لاستلام رمز التحقق</p>
                </div>
                <div class="modal-login-body">
                    @if($errors->any() && (request('open_forgot') || old('reset_form')))
                        <div
                            class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
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
                            <input type="email"
                                class="form-control modal-login-input @error('email') is-invalid @enderror"
                                id="forgotEmail" name="email" value="{{ old('email') }}"
                                placeholder="example@uot.edu.ly" required autofocus>
                            <small class="text-muted" style="font-size: 0.78rem;">سنرسل لك رمز تحقق مكون من 6 أرقام
                                لإعادة تعيين كلمة المرور.</small>
                        </div>

                        <x-honeypot />
                        <x-turnstile action="forgot_password" />

                        <button type="submit"
                            class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mb-3 mt-2">
                            <i class="fas fa-paper-plane"></i><span>إرسال رمز التحقق</span>
                        </button>

                        <div class="text-center pt-2 border-top">
                            <a href="javascript:void(0)" id="btnBackToLoginModal"
                                class="fw-bold text-decoration-none small" style="color: #1565c0; cursor: pointer;">
                                <i class="fas fa-arrow-right me-1"></i>العودة لتسجيل الدخول
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 13. نافذة التحقق وتغيير كلمة المرور المنبثقة (Verify Code & Reset Modal) ==================== -->
    <div class="modal fade" id="verifyCodeModal" tabindex="-1" aria-labelledby="verifyCodeModalLabel" aria-hidden="true"
        style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content modal-login-content">
                <div class="modal-login-header">
                    <button type="button"
                        class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none"
                        data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="modal-login-logo d-block"
                        onerror="this.onerror=null;">
                    <h4 class="fw-bold mb-1" id="verifyCodeModalLabel">تغيير كلمة المرور</h4>
                    <p class="mb-0 text-white-50 small">أدخل الرمز المرسل إلى بريدك الإلكتروني</p>
                </div>
                <div class="modal-login-body">
                    @if(session('status_code_sent'))
                        <div
                            class="alert alert-success border-0 rounded-3 py-2 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
                            <i class="fas fa-check-circle flex-shrink-0 fs-6 text-success"></i>
                            <span>{{ session('status_code_sent') }}</span>
                        </div>
                    @endif

                    @if($errors->any() && (request('open_verify') || old('verify_form')))
                        <div
                            class="alert alert-danger border-0 rounded-3 py-2.5 px-3 small d-flex align-items-center gap-2 mb-3 shadow-none">
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

                        <div
                            class="p-2.5 rounded-3 bg-light border mb-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                                <i class="fas fa-envelope text-primary small"></i>
                                <span class="text-muted small">البريد:</span>
                                <strong class="text-dark small text-truncate"
                                    id="verifyModalEmailDisplay">{{ $safeVerifyEmail }}</strong>
                            </div>
                            <a href="javascript:void(0)" id="btnChangeEmailInVerify"
                                class="small fw-bold text-decoration-none flex-shrink-0"
                                style="color: #1565c0; font-size: 0.78rem; cursor: pointer;">
                                تغيير
                            </a>
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <label for="verifyCodeInput" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-key text-primary me-1"></i>رمز التحقق (6 أرقام)
                            </label>
                            <input type="text"
                                class="form-control modal-login-input text-center fw-bold fs-5 @error('code') is-invalid @enderror"
                                id="verifyCodeInput" name="code" value="{{ old('code') }}" placeholder="123456"
                                maxlength="6" required autofocus style="letter-spacing: 4px;">
                        </div>

                        <div class="mb-3 text-start text-rtl">
                            <label for="verifyNewPassword" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-lock text-primary me-1"></i>كلمة المرور الجديدة
                            </label>
                            <div class="position-relative">
                                <input type="password"
                                    class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror"
                                    id="verifyNewPassword" name="password" placeholder="••••••••" required>
                                <button type="button"
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0"
                                    id="btnToggleVerifyPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleVerifyPass"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 text-start text-rtl">
                            <label for="verifyConfirmPassword" class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-lock text-primary me-1"></i>تأكيد كلمة المرور الجديدة
                            </label>
                            <div class="position-relative">
                                <input type="password" class="form-control modal-login-input pe-5"
                                    id="verifyConfirmPassword" name="password_confirmation" placeholder="••••••••"
                                    required>
                                <button type="button"
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0"
                                    id="btnToggleVerifyConfirmPass" style="z-index: 5;">
                                    <i class="far fa-eye" id="iconToggleVerifyConfirmPass"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-modal-login w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="fas fa-check-circle"></i><span>تأكيد وتغيير كلمة المرور</span>
                        </button>

                        <div class="text-center pt-2 border-top">
                            <a href="javascript:void(0)" id="btnBackToLoginFromVerify"
                                class="fw-bold text-decoration-none small" style="color: #1565c0; cursor: pointer;">
                                <i class="fas fa-arrow-right me-1"></i>العودة لتسجيل الدخول
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 14. نافذة تسجيل خريج جديد المنبثقة (Graduate Registration Modal) ==================== -->
    <div class="modal fade" id="graduateRegisterModal" tabindex="-1" aria-labelledby="graduateRegisterModalLabel"
        aria-hidden="true" style="backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
            style="max-width: 860px; margin: 0.5rem auto;">
            <div class="modal-content modal-register-content">
                <!-- Header -->
                <div class="modal-register-header position-relative">
                    <button type="button"
                        class="btn-close btn-close-white position-absolute top-0 start-0 m-3 shadow-none"
                        data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    <h3><i class="fas fa-user-graduate me-2 text-warning"></i> تسجيل خريج جديد</h3>
                    <p class="text-white-50 mb-0 small">انضم إلينا للاستفادة من خدمات التدريب والتأهيل وفرص التوظيف
                        المباشر</p>
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

                    <!-- Step Alert Container -->
                    <div id="regStepAlert"
                        class="alert alert-warning border-0 rounded-3 py-2 px-3 small mb-3 shadow-none d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="regStepAlertText">يرجى استكمال الحقول الإلزامية المطلوبة.</span>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('graduate.register.store') }}" id="modalRegistrationForm"
                        novalidate>
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
                                    <input type="text"
                                        class="form-control modal-login-input @error('name') is-invalid @enderror"
                                        id="reg_name" name="name" value="{{ old('name') }}" required
                                        placeholder="الاسم الرباعي كما هو في الشهادة">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_student_id" class="form-label fw-bold text-dark small mb-1">
                                        رقم القيد الجامعي <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control modal-login-input @error('student_id') is-invalid @enderror @error('national_id') is-invalid @enderror"
                                        id="reg_student_id" name="student_id"
                                        value="{{ old('student_id', old('national_id')) }}" required
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
                                    <input type="email"
                                        class="form-control modal-login-input @error('email') is-invalid @enderror"
                                        id="reg_email" name="email" value="{{ old('email') }}" required
                                        placeholder="example@domain.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_phone" class="form-label fw-bold text-dark small mb-1">
                                        رقم الهاتف <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel"
                                        class="form-control modal-login-input @error('phone') is-invalid @enderror"
                                        id="reg_phone" name="phone" value="{{ old('phone') }}" required
                                        placeholder="09xxxxxxxx">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_password" class="form-label fw-bold text-dark small mb-1">
                                        كلمة المرور <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password"
                                            class="form-control modal-login-input pe-5 @error('password') is-invalid @enderror"
                                            id="reg_password" name="password" required minlength="8"
                                            placeholder="8 أحرف على الأقل">
                                        <button type="button"
                                            class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0"
                                            id="btnToggleRegPass" style="z-index: 5;">
                                            <i class="far fa-eye" id="iconToggleRegPass"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_password_confirmation"
                                        class="form-label fw-bold text-dark small mb-1">
                                        تأكيد كلمة المرور <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control modal-login-input pe-5"
                                            id="reg_password_confirmation" name="password_confirmation" required
                                            minlength="8" placeholder="أعد كتابة كلمة المرور">
                                        <button type="button"
                                            class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted text-decoration-none pe-3 shadow-none border-0"
                                            id="btnToggleRegConfirmPass" style="z-index: 5;">
                                            <i class="far fa-eye" id="iconToggleRegConfirmPass"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_date_of_birth" class="form-label fw-bold text-dark small mb-1">
                                        تاريخ الميلاد <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                        class="form-control modal-login-input @error('date_of_birth') is-invalid @enderror"
                                        id="reg_date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                        required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_gender" class="form-label fw-bold text-dark small mb-1">
                                        الجنس <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select modal-login-input @error('gender') is-invalid @enderror"
                                        id="reg_gender" name="gender" required>
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى
                                        </option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_city" class="form-label fw-bold text-dark small mb-1">
                                        المدينة <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control modal-login-input @error('city') is-invalid @enderror"
                                        id="reg_city" name="city" value="{{ old('city', 'طرابلس') }}" required
                                        placeholder="طرابلس">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="reg_address" class="form-label fw-bold text-dark small mb-1">
                                        العنوان بالتفصيل <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control modal-login-input @error('address') is-invalid @enderror"
                                        id="reg_address" name="address" value="{{ old('address') }}" required
                                        placeholder="الحي - الشارع">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-2 border-top">
                                <button type="button" class="btn btn-modal-login px-4 d-flex align-items-center gap-2"
                                    onclick="switchRegStep(2)">
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
                                    <select
                                        class="form-select modal-login-input @error('university') is-invalid @enderror"
                                        id="university" name="university" required>
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
                                    <select class="form-select modal-login-input @error('sector') is-invalid @enderror"
                                        id="sector" name="sector" required>
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
                                    <select class="form-select modal-login-input @error('faculty') is-invalid @enderror"
                                        id="faculty" name="faculty" required disabled>
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
                                    <select
                                        class="form-select modal-login-input @error('qualification') is-invalid @enderror"
                                        id="qualification" name="qualification" required>
                                        <option value="">اختر المؤهل</option>
                                        <option value="بكالوريوس" {{ old('qualification', 'بكالوريوس') == 'بكالوريوس' ? 'selected' : '' }}>بكالوريوس</option>
                                        <option value="ليسانس" {{ old('qualification') == 'ليسانس' ? 'selected' : '' }}>
                                            ليسانس</option>
                                        <option value="ماجستير" {{ old('qualification') == 'ماجستير' ? 'selected' : '' }}>
                                            ماجستير</option>
                                        <option value="دكتوراه" {{ old('qualification') == 'دكتوراه' ? 'selected' : '' }}>
                                            دكتوراه</option>
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
                                    <select
                                        class="form-select modal-login-input @error('specialization') is-invalid @enderror"
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
                                    <input type="number"
                                        class="form-control modal-login-input @error('graduation_year') is-invalid @enderror"
                                        id="graduation_year" name="graduation_year"
                                        value="{{ old('graduation_year', date('Y')) }}" required min="1960"
                                        max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                                    @error('graduation_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="gpa" class="form-label fw-bold text-dark small mb-1">
                                        المعدل التراكمي (%)
                                    </label>
                                    <input type="number"
                                        class="form-control modal-login-input @error('gpa') is-invalid @enderror"
                                        id="gpa" name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="100"
                                        placeholder="مثال: 85.50">
                                    @error('gpa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-2 border-top">
                                <button type="button"
                                    class="btn btn-outline-secondary rounded-3 px-4 d-flex align-items-center gap-2"
                                    onclick="switchRegStep(1)">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>السابق: البيانات الشخصية</span>
                                </button>
                                <button type="button" class="btn btn-modal-login px-4 d-flex align-items-center gap-2"
                                    onclick="switchRegStep(3)">
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
                                    <textarea
                                        class="form-control modal-login-input @error('experiences') is-invalid @enderror"
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
                                    <input type="text"
                                        class="form-control modal-login-input @error('skills') is-invalid @enderror"
                                        id="skills" name="skills" value="{{ old('skills') }}"
                                        placeholder="مثال: برمجة، تحليل بيانات، إدارة وقت">
                                    <small class="text-muted" style="font-size: 0.78rem;">افصل بين المهارات بفاصلة
                                        (,)</small>
                                    @error('skills')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="languages" class="form-label fw-bold text-dark small mb-1">
                                        اللغات المتقنة
                                    </label>
                                    <input type="text"
                                        class="form-control modal-login-input @error('languages') is-invalid @enderror"
                                        id="languages" name="languages"
                                        value="{{ old('languages', 'العربية، الإنجليزية') }}"
                                        placeholder="العربية، الإنجليزية">
                                    <small class="text-muted" style="font-size: 0.78rem;">افصل بين اللغات بفاصلة
                                        (,)</small>
                                    @error('languages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="my-3">
                                <x-turnstile action="register_graduate" />
                            </div>

                            <div class="d-flex justify-content-between mt-4 pt-2 border-top gap-2 flex-wrap">
                                <button type="button"
                                    class="btn btn-outline-secondary rounded-3 px-4 d-flex align-items-center gap-2"
                                    onclick="switchRegStep(2)" style="flex:1 1 auto; min-width:130px;">
                                    <i class="fas fa-arrow-right"></i>
                                    <span>السابق: البيانات الأكاديمية</span>
                                </button>
                                <button type="submit"
                                    class="btn btn-modal-login px-5 d-flex align-items-center justify-content-center gap-2"
                                    id="modalSubmitRegisterBtn"
                                    style="flex:1 1 auto; min-width:150px; touch-action: manipulation;"
                                    data-original-html='<i class="fas fa-user-plus"></i> <span>تسجيل حساب جديد</span>'>
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
                        <a href="javascript:void(0)" id="btnBackToLoginFromRegister"
                            class="fw-bold text-decoration-none ms-1" style="color: #1565c0; cursor: pointer;">
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

        // حالة الخطوة الحالية في نموذج تسجيل الخريج
        window.currentRegStep = 1;

        // دالة مسح رسائل الخطأ من قسم معين
        function clearRegFieldErrors(sectionEl) {
            if (!sectionEl) return;
            sectionEl.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            sectionEl.querySelectorAll('.js-validation-msg').forEach(el => el.remove());
            const alertBox = document.getElementById('regStepAlert');
            if (alertBox) alertBox.classList.add('d-none');
        }

        // دالة إظهار خطأ لحقل معين
        function showRegFieldError(inputEl, msg) {
            if (!inputEl) return;
            inputEl.classList.add('is-invalid');
            let feedback = inputEl.parentNode.querySelector('.js-validation-msg');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block js-validation-msg';
                inputEl.parentNode.appendChild(feedback);
            }
            feedback.textContent = msg;
        }

        // دالة إظهار تنبيه عام أعلى النموذج
        function showRegStepAlert(msg) {
            const alertBox = document.getElementById('regStepAlert');
            const alertText = document.getElementById('regStepAlertText');
            if (alertBox && alertText) {
                alertText.textContent = msg;
                alertBox.classList.remove('d-none');
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        // التحقق من صحة بيانات الخطوة 1 (البيانات الشخصية)
        window.validateRegStep1 = function () {
            const sec = document.getElementById('regSection1');
            if (!sec) return true;
            clearRegFieldErrors(sec);

            let isValid = true;
            let firstInvalid = null;

            const name = document.getElementById('reg_name');
            if (!name || !name.value.trim() || name.value.trim().length < 3) {
                showRegFieldError(name, 'يرجى إدخال الاسم الرباعي بشكل صحيح (3 أحرف على الأقل)');
                if (!firstInvalid) firstInvalid = name;
                isValid = false;
            }

            const studentId = document.getElementById('reg_student_id');
            if (!studentId || !studentId.value.trim()) {
                showRegFieldError(studentId, 'يرجى إدخال رقم القيد بالجامعة (الرقم الجامعي)');
                if (!firstInvalid) firstInvalid = studentId;
                isValid = false;
            }

            const email = document.getElementById('reg_email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !email.value.trim() || !emailRegex.test(email.value.trim())) {
                showRegFieldError(email, 'يرجى إدخال بريد إلكتروني صحيح');
                if (!firstInvalid) firstInvalid = email;
                isValid = false;
            }

            const phone = document.getElementById('reg_phone');
            if (!phone || !phone.value.trim() || phone.value.trim().length < 8) {
                showRegFieldError(phone, 'يرجى إدخال رقم هاتف صحيح');
                if (!firstInvalid) firstInvalid = phone;
                isValid = false;
            }

            const pass = document.getElementById('reg_password');
            if (!pass || pass.value.length < 8) {
                showRegFieldError(pass, 'كلمة المرور يجب أن لا تقل عن 8 أحرف وأرقام');
                if (!firstInvalid) firstInvalid = pass;
                isValid = false;
            }

            const confirmPass = document.getElementById('reg_password_confirmation');
            if (!confirmPass || confirmPass.value !== (pass ? pass.value : '')) {
                showRegFieldError(confirmPass, 'تأكيد كلمة المرور غير متطابق مع كلمة المرور');
                if (!firstInvalid) firstInvalid = confirmPass;
                isValid = false;
            }

            const dob = document.getElementById('reg_date_of_birth');
            if (!dob || !dob.value) {
                showRegFieldError(dob, 'يرجى تحديد تاريخ الميلاد');
                if (!firstInvalid) firstInvalid = dob;
                isValid = false;
            }

            const gender = document.getElementById('reg_gender');
            if (!gender || !gender.value) {
                showRegFieldError(gender, 'يرجى اختيار الجنس');
                if (!firstInvalid) firstInvalid = gender;
                isValid = false;
            }

            const city = document.getElementById('reg_city');
            if (!city || !city.value.trim()) {
                showRegFieldError(city, 'يرجى إدخال المدينة');
                if (!firstInvalid) firstInvalid = city;
                isValid = false;
            }

            const address = document.getElementById('reg_address');
            if (!address || !address.value.trim()) {
                showRegFieldError(address, 'يرجى إدخال العنوان بالتفصيل');
                if (!firstInvalid) firstInvalid = address;
                isValid = false;
            }

            if (!isValid) {
                showRegStepAlert('يرجى تصحيح الحقول المميزة باللون الأحمر للمتابعة.');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return isValid;
        };

        // التحقق من صحة بيانات الخطوة 2 (البيانات الأكاديمية)
        window.validateRegStep2 = function () {
            const sec = document.getElementById('regSection2');
            if (!sec) return true;
            clearRegFieldErrors(sec);

            let isValid = true;
            let firstInvalid = null;

            const univ = document.getElementById('university');
            if (!univ || !univ.value) {
                showRegFieldError(univ, 'يرجى اختيار الجامعة');
                if (!firstInvalid) firstInvalid = univ;
                isValid = false;
            }

            const sector = document.getElementById('sector');
            if (!sector || !sector.value) {
                showRegFieldError(sector, 'يرجى اختيار القطاع / القاطع');
                if (!firstInvalid) firstInvalid = sector;
                isValid = false;
            }

            const faculty = document.getElementById('faculty');
            if (!faculty || !faculty.value) {
                showRegFieldError(faculty, 'يرجى اختيار الكلية');
                if (!firstInvalid) firstInvalid = faculty;
                isValid = false;
            }

            const qual = document.getElementById('qualification');
            if (!qual || !qual.value) {
                showRegFieldError(qual, 'يرجى اختيار المؤهل العلمي');
                if (!firstInvalid) firstInvalid = qual;
                isValid = false;
            }

            const spec = document.getElementById('specialization');
            if (!spec || !spec.value) {
                showRegFieldError(spec, 'يرجى اختيار التخصص الأكاديمي');
                if (!firstInvalid) firstInvalid = spec;
                isValid = false;
            }

            const gradYear = document.getElementById('graduation_year');
            const currentYear = new Date().getFullYear();
            if (!gradYear || !gradYear.value || Number(gradYear.value) < 1960 || Number(gradYear.value) > currentYear + 1) {
                showRegFieldError(gradYear, 'يرجى إدخال سنة تخرج صحيحة');
                if (!firstInvalid) firstInvalid = gradYear;
                isValid = false;
            }

            if (!isValid) {
                showRegStepAlert('يرجى استكمال البيانات الأكاديمية المطلوبة (القطاع والكلية والتخصص).');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return isValid;
        };

        // دالة التبديل بين خطوات التسجيل
        window.switchRegStep = function (targetStep, force = false) {
            const currentStep = window.currentRegStep || 1;

            // إذا كان المستخدم يتقدم للأمام، نتحقق من صحة الخطوات السابقة
            if (!force && targetStep > currentStep) {
                if (currentStep === 1 && !window.validateRegStep1()) {
                    return false;
                }
                if (currentStep === 2 && !window.validateRegStep2()) {
                    return false;
                }
                if (currentStep === 1 && targetStep === 3) {
                    if (!window.validateRegStep1()) return false;
                    if (!window.validateRegStep2()) {
                        window.switchRegStep(2, true);
                        return false;
                    }
                }
            }

            // إخفاء كافة الأقسام
            document.querySelectorAll('.reg-step-section').forEach(el => el.classList.add('d-none'));

            // إظهار القسم المطلوب
            const targetSection = document.getElementById('regSection' + targetStep);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }

            window.currentRegStep = targetStep;

            // إخفاء رسالة التنبيه
            const alertBox = document.getElementById('regStepAlert');
            if (alertBox) alertBox.classList.add('d-none');

            // تحديث مؤشرات الخطوات
            for (let i = 1; i <= 3; i++) {
                const ind = document.getElementById('modalStepIndicator' + i);
                if (!ind) continue;
                ind.classList.remove('active', 'completed');
                if (i < targetStep) {
                    ind.classList.add('completed');
                } else if (i === targetStep) {
                    ind.classList.add('active');
                }
            }

            // تفعيل كاشف الروبوتات عند الوصول للخطوة 3
            if (targetStep === 3) {
                setTimeout(function () {
                    window.dispatchEvent(new CustomEvent('turnstile:render'));
                }, 60);
            }

            // التمرير لأعلى جسم المودال
            const modalBody = document.querySelector('.modal-register-body');
            if (modalBody) {
                modalBody.scrollTo({ top: 0, behavior: 'smooth' });
            }

            return true;
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

            // التعامل مع نموذج تسجيل الخريج الجديد
            const regForm = document.getElementById('modalRegistrationForm');
            if (regForm) {
                // إزالة الخطأ تلقائياً بمجرد إدخال أو تغيير أي قيمة
                regForm.querySelectorAll('input, select, textarea').forEach(input => {
                    input.addEventListener('input', function () {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            const msg = this.parentNode.querySelector('.js-validation-msg');
                            if (msg) msg.remove();
                        }
                    });
                    input.addEventListener('change', function () {
                        if (this.classList.contains('is-invalid')) {
                            this.classList.remove('is-invalid');
                            const msg = this.parentNode.querySelector('.js-validation-msg');
                            if (msg) msg.remove();
                        }
                    });
                });

                // فحص تطابق كلمة المرور أثناء الكتابة
                const regPass = document.getElementById('reg_password');
                const regConfirm = document.getElementById('reg_password_confirmation');
                if (regPass && regConfirm) {
                    function checkPasswordMatch() {
                        if (regConfirm.value.length > 0) {
                            if (regConfirm.value !== regPass.value) {
                                showRegFieldError(regConfirm, 'كلمتا المرور غير متطابقتين');
                            } else {
                                regConfirm.classList.remove('is-invalid');
                                const msg = regConfirm.parentNode.querySelector('.js-validation-msg');
                                if (msg) msg.remove();
                            }
                        }
                    }
                    regPass.addEventListener('input', checkPasswordMatch);
                    regConfirm.addEventListener('input', checkPasswordMatch);
                }

                // عند الضغط على زر الإرسال
                regForm.addEventListener('submit', function (e) {
                    // 1. التحقق من الخطوة 1
                    if (!window.validateRegStep1()) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.switchRegStep(1, true);
                        return false;
                    }

                    // 2. التحقق من الخطوة 2
                    if (!window.validateRegStep2()) {
                        e.preventDefault();
                        e.stopPropagation();
                        window.switchRegStep(2, true);
                        return false;
                    }

                    // 3. التأكد من تمكين القوائم المنسدلة لإرسال قيمها إلى السيرفر
                    ['university', 'sector', 'faculty', 'qualification', 'specialization'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el && el.value) {
                            el.disabled = false;
                        }
                    });

                    // 4. فحص كاشف الروبوتات Turnstile في حال كان مفعلاً
                    const tsWrapper = regForm.querySelector('.turnstile-wrapper');
                    if (tsWrapper) {
                        const tokenInput = regForm.querySelector('[name="cf-turnstile-response"]');
                        const hasToken = window.isTurnstileVerified || (tokenInput && tokenInput.value && tokenInput.value.trim().length > 10 && !tokenInput.value.startsWith('BYPASS_'));
                        if (!hasToken) {
                            e.preventDefault();
                            e.stopPropagation();
                            showRegStepAlert('يرجى النقر على كاشف الروبوتات (Cloudflare) وإكمال التحقق الأمني أولاً.');
                            tsWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            return false;
                        }
                    }

                    // 5. إظهار مؤشر التحميل وتعطيل الزر لمنع الإرسال المتكرر
                    const submitBtn = document.getElementById('modalSubmitRegisterBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.85';
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري تسجيل الحساب...';
                    }
                });
            }

            @if ($errors->any() && (request('open_register') || old('from_register_modal')))
                @if ($errors->has('university') || $errors->has('sector') || $errors->has('faculty') || $errors->has('qualification') || $errors->has('specialization') || $errors->has('graduation_year') || $errors->has('gpa'))
                    window.switchRegStep(2, true);
                @elseif ($errors->has('experiences') || $errors->has('skills') || $errors->has('languages') || $errors->has('cf-turnstile-response') || $errors->has('security'))
                    window.switchRegStep(3, true);
                @else
                    window.switchRegStep(1, true);
                @endif
            @endif
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
        (function () {
            const wrapper = document.querySelector('.home-navbar-wrapper');
            const navbar = document.querySelector('.home-navbar');
            if (!navbar) return;

            let isScrolled = false;
            let ticking = false;

            function updateNavbar() {
                const scrollY = window.pageYOffset || document.documentElement.scrollTop;
                if (!isScrolled && scrollY > 20) {
                    isScrolled = true;
                    if (wrapper) wrapper.classList.add('scrolled');
                    navbar.classList.add('scrolled');
                } else if (isScrolled && scrollY <= 20) {
                    isScrolled = false;
                    if (wrapper) wrapper.classList.remove('scrolled');
                    navbar.classList.remove('scrolled');
                }
                ticking = false;
            }

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(updateNavbar);
                    ticking = true;
                }
            }, { passive: true });

            updateNavbar();
        })();

        // التمرير السلس للروابط الداخلية فقط دون التأثير على سلاسة تمرير الماوس
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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