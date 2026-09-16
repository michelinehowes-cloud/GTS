<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} — البرنامج العلمي | معرض التوظيف 2026</title>
    <meta name="description" content="{{ Str::limit(strip_tags($event->description), 160) }}">
    
    <!-- Open Graph / Meta -->
    <meta property="og:title" content="{{ $event->title }} — معرض التوظيف 2026">
    <meta property="og:description" content="{{ Str::limit(strip_tags($event->description), 160) }}">
    <meta property="og:url" content="{{ $event->share_url }}">
    <meta property="og:type" content="website">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:     #eeca3e;
            --gold-lt:  #FDE68A;
            --navy:     #045db0; /* University Primary Blue */
            --navy-md:  #3b82f6; /* University Light Blue */
            --navy-lt:  #60a5fa;
            --navy-dark:#092347;
            --teal:     #0EA5E9;
            --green:    #10B981;
            --white:    #FFFFFF;
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
                radial-gradient(ellipse 80% 60% at 20% 40%, rgba(245,158,11,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 20%, rgba(14,165,233,0.22) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16,185,129,0.12) 0%, transparent 50%),
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
        .orb-1 { width: 450px; height: 450px; background: rgba(238,202,62,0.15); top: -80px; left: -80px; }
        .orb-2 { width: 520px; height: 520px; background: rgba(14,165,233,0.18); bottom: 10%; right: -120px; animation-duration: 18s; animation-delay: -5s; }
        .orb-3 { width: 320px; height: 320px; background: rgba(59,130,246,0.2); top: 40%; left: 40%; animation-duration: 22s; animation-delay: -10s; }

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
           TOP NAVBAR
        ══════════════════════════════════ */
        .glass-navbar {
            background: rgba(9, 35, 71, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
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

        .nav-logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .nav-brand-text h1 {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 0;
            color: #fff;
            letter-spacing: -0.2px;
        }

        .nav-brand-text small {
            font-size: 0.72rem;
            color: var(--gold);
            font-weight: 600;
            display: block;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

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
        .breadcrumb-wrap {
            padding: 22px 0 10px;
        }

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

        .custom-breadcrumb li a:hover {
            color: var(--gold);
        }

        .custom-breadcrumb li.separator {
            color: rgba(255,255,255,0.3);
            font-size: 0.75rem;
        }

        .custom-breadcrumb li.active {
            color: var(--gold-lt);
            font-weight: 700;
        }

        /* Event Hero */
        .event-hero-card {
            background: linear-gradient(135deg, rgba(8, 34, 69, 0.85) 0%, rgba(4, 93, 176, 0.65) 100%);
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

        .event-hero-card::after {
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

        .event-badges-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .badge-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }
        .badge-type-masterclass {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: #0b1e38;
            box-shadow: 0 4px 15px rgba(245,158,11,0.35);
        }
        .badge-type-workshop {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(14,165,233,0.3);
        }
        .badge-type-panel {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(168,85,247,0.3);
        }

        .badge-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .badge-status-open {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }
        .badge-status-completed {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }
        .badge-status-upcoming {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.4);
        }

        .event-hero-title {
            font-size: 2.1rem;
            font-weight: 900;
            line-height: 1.35;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .event-hero-subtitle {
            font-size: 1.05rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 850px;
            line-height: 1.7;
            margin: 0;
        }

        /* ══════════════════════════════════
           MAIN CONTENT & SIDEBAR CARDS
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
            margin-bottom: 22px;
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

        /* Speaker Card */
        .speaker-profile-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1.5px solid rgba(255, 255, 255, 0.14);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            align-items: flex-start;
            gap: 22px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        .speaker-profile-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(238,202,62,0.4);
            transform: translateY(-2px);
        }

        .speaker-avatar-wrap {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            background: linear-gradient(135deg, #045db0, #092347);
            border: 2px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .speaker-avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .speaker-avatar-wrap .avatar-fallback {
            font-size: 2.2rem;
            color: var(--gold-lt);
        }

        .speaker-info-col {
            flex: 1;
        }

        .speaker-name-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .speaker-name-badge h4 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .speaker-verified-icon {
            color: #38bdf8;
            font-size: 0.95rem;
        }

        .speaker-job-title {
            color: var(--gold-lt);
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .speaker-bio-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.88rem;
            line-height: 1.65;
            margin: 0;
        }

        /* Topics Checklist */
        .topics-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .topic-item {
            background: rgba(255, 255, 255, 0.035);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.92);
            transition: all 0.25s ease;
        }

        .topic-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(238,202,62,0.3);
            transform: translateX(-4px);
        }

        .topic-check-icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        /* Target Audience Chips */
        .audience-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .audience-chip {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.35);
            color: #93c5fd;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ══════════════════════════════════
           SIDEBAR: REGISTRATION & QR CARD
        ══════════════════════════════════ */
        .sidebar-sticky {
            position: sticky;
            top: 90px;
        }

        .reg-action-box {
            background: linear-gradient(145deg, rgba(8, 34, 69, 0.9) 0%, rgba(4, 93, 176, 0.75) 100%);
            border: 2px solid rgba(238, 202, 62, 0.4);
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            margin-bottom: 24px;
        }

        .capacity-bar-wrap {
            margin-bottom: 20px;
        }

        .capacity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.85);
        }

        .capacity-number {
            font-weight: 800;
            color: var(--gold);
        }

        .progress-track {
            height: 10px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #eeca3e);
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .btn-register-action {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 1.05rem;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-reg-primary {
            background: linear-gradient(135deg, #eeca3e 0%, #f59e0b 100%);
            color: #061c38;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        .btn-reg-primary:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #061c38;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.5);
        }

        .btn-reg-registered {
            background: rgba(16, 185, 129, 0.2);
            border: 1.5px solid #10b981;
            color: #34d399;
        }
        .btn-reg-registered:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: #ef4444;
            color: #fca5a5;
        }

        .btn-reg-disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.4);
            cursor: not-allowed;
        }

        /* Metadata table */
        .meta-list {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
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

        .meta-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.78rem;
            display: block;
        }

        .meta-value {
            color: #ffffff;
            font-weight: 700;
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

        .qr-image-frame img {
            width: 170px;
            height: 170px;
            display: block;
        }

        .qr-caption {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.5;
            margin-bottom: 14px;
        }

        .qr-action-btn-row {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

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

        /* Share Buttons */
        .social-share-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 14px;
        }

        .btn-social-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        .btn-social-icon:hover {
            transform: translateY(-2px);
            opacity: 0.9;
            color: #fff;
        }
        .bg-whatsapp { background: #25D366; }
        .bg-linkedin { background: #0A66C2; }
        .bg-twitter  { background: #000000; border: 1px solid rgba(255,255,255,0.2); }
        .bg-telegram { background: #229ED9; }

        /* ══════════════════════════════════
           RELATED EVENTS
        ══════════════════════════════════ */
        .related-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .related-event-card {
            background: rgba(8, 34, 69, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 20px;
            text-decoration: none;
            color: #ffffff;
            display: block;
            transition: all 0.3s ease;
            height: 100%;
        }
        .related-event-card:hover {
            background: rgba(8, 34, 69, 0.9);
            border-color: var(--gold);
            transform: translateY(-3px);
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .related-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 10px;
            background: rgba(238, 202, 62, 0.15);
            color: var(--gold-lt);
            border: 1px solid rgba(238, 202, 62, 0.3);
        }

        .related-title {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.45;
            margin-bottom: 10px;
            color: #fff;
        }

        .related-speaker {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ══════════════════════════════════
           FOOTER
        ══════════════════════════════════ */
        .event-footer {
            margin-top: auto;
            background: rgba(4, 30, 60, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* Toast notification */
        .toast-copied {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #10b981;
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            font-weight: 700;
            font-size: 0.9rem;
            display: none;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991px) {
            .event-hero-card { padding: 24px 20px; }
            .event-hero-title { font-size: 1.55rem; }
            .speaker-profile-card { flex-direction: column; align-items: center; text-align: center; }
            .speaker-name-badge { justify-content: center; }
            .speaker-job-title { justify-content: center; }
            .sidebar-sticky { position: static; }
        }
    </style>
</head>
<body>

    <!-- Ambient Background Elements -->
    <div class="hero-bg-layer"></div>
    <div class="hero-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-content-wrapper">

        <!-- Top Navigation -->
        <nav class="glass-navbar">
            <div class="container py-2">
                <div class="d-flex align-items-center justify-content-between">
                    <a href="{{ $fair ? route('job-fair.public.program', $fair->id) : route('job-fair.public.program.index') }}" class="nav-brand-group">
                        <div class="nav-logo-box">
                            <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين" onerror="this.src='{{ asset('images/uni_logo_white.png') }}'">
                        </div>
                        <div class="nav-brand-text">
                            <h1>البرنامج العلمي والتدريبي</h1>
                            <small>{{ $fair ? $fair->title : 'معرض التوظيف 2026 — جامعة طرابلس' }}</small>
                        </div>
                    </a>

                    <div class="nav-actions">
                        <a href="{{ $fair ? route('job-fair.public.program', $fair->id) : route('job-fair.public.program.index') }}" class="btn-nav-outline">
                            <i class="fas fa-arrow-right"></i>
                            <span class="d-none d-sm-inline">جدول الفعاليات الكامل</span>
                        </a>
                        @if($fair)
                        <a href="{{ route('job-fair.public', $fair->id) }}" class="btn-nav-outline d-none d-md-inline-flex">
                            <i class="fas fa-home"></i>
                            <span>الصفحة الرئيسية للمعرض</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <div class="container pb-5">
            <!-- Breadcrumb -->
            <div class="breadcrumb-wrap">
                <ul class="custom-breadcrumb">
                    <li><a href="{{ url('/') }}">الرئيسية</a></li>
                    <li class="separator"><i class="fas fa-chevron-left"></i></li>
                    @if($fair)
                        <li><a href="{{ route('job-fair.public', $fair->id) }}">{{ $fair->title }}</a></li>
                        <li class="separator"><i class="fas fa-chevron-left"></i></li>
                        <li><a href="{{ route('job-fair.public.program', $fair->id) }}">البرنامج العلمي</a></li>
                    @else
                        <li><a href="{{ route('job-fair.public.program.index') }}">البرنامج العلمي</a></li>
                    @endif
                    <li class="separator"><i class="fas fa-chevron-left"></i></li>
                    <li class="active">{{ Str::limit($event->title, 40) }}</li>
                </ul>
            </div>

            <!-- Event Hero Banner -->
            <div class="event-hero-card">
                <div class="event-badges-row">
                    <span class="badge-type-pill badge-type-{{ $event->type }}">
                        <i class="{{ $event->type_icon }}"></i>
                        <span>{{ $event->type_label }}</span>
                    </span>

                    <span class="badge-status-pill badge-status-{{ $event->effective_status }}">
                        <i class="fas fa-circle" style="font-size: 0.55rem;"></i>
                        <span>{{ $event->status_label }}</span>
                    </span>

                    @if($event->is_featured)
                    <span class="badge-status-pill" style="background: rgba(238,202,62,0.2); color: var(--gold-lt); border: 1px solid rgba(238,202,62,0.4);">
                        <i class="fas fa-star"></i>
                        <span>فعالية رئيسية مميزة</span>
                    </span>
                    @endif

                    <span class="badge-status-pill" style="background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8);">
                        <i class="fas fa-route"></i>
                        <span>رحلة الجاهزية المهنية</span>
                    </span>
                </div>

                <h2 class="event-hero-title">{{ $event->title }}</h2>

                @if($event->speaker_title)
                <p class="event-hero-subtitle">
                    <i class="fas fa-user-tie text-warning me-1"></i>
                    يقدمها: <strong>{{ $event->speaker_name }}</strong> ({{ $event->speaker_title }})
                </p>
                @endif
            </div>

            <!-- Two-Column Layout -->
            <div class="row g-4">
                <!-- Main Content Column -->
                <div class="col-lg-8">

                    <!-- Idea & Description Box -->
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h3>فكرة الفعالية وأهدافها</h3>
                        </div>
                        <div style="font-size: 1rem; line-height: 1.8; color: rgba(255, 255, 255, 0.9);">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Topics / Outlines Box -->
                    @if(!empty($event->topics_list) && count($event->topics_list) > 0)
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <h3>المحاور الرئيسية التي ستتناولها الفعالية</h3>
                        </div>
                        <ul class="topics-list">
                            @foreach($event->topics_list as $topic)
                            <li class="topic-item">
                                <div class="topic-check-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ $topic }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Speakers Profile Box -->
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h3>{{ $event->type === 'panel_discussion' ? 'المتحدثون والخبراء المشاركون' : 'المدرب / المتحدث الرئيسي' }}</h3>
                        </div>

                        @if($event->type === 'panel_discussion')
                            <!-- Panelists Breakdown -->
                            <div class="speaker-profile-card">
                                <div class="speaker-avatar-wrap">
                                    @if($event->speaker_image_url)
                                        <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}">
                                    @else
                                        <i class="fas fa-users-cog avatar-fallback"></i>
                                    @endif
                                </div>
                                <div class="speaker-info-col">
                                    <div class="speaker-name-badge">
                                        <h4>{{ $event->speaker_name }}</h4>
                                    </div>
                                    <div class="speaker-job-title">
                                        <i class="fas fa-id-badge"></i>
                                        <span>{{ $event->speaker_title ?: 'نخبة من القيادات التنفيذية والخبراء' }}</span>
                                    </div>
                                    <p class="speaker-bio-text">
                                        {{ $event->speaker_bio ?: 'جلسة حوارية رفيعة المستوى تجمع شخصيات قيادية وخبراء دوليين لنقل خبراتهم الميدانية وتقديم توصيات عملية ملموسة للطلبة والخريجين.' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <!-- Solo Speaker -->
                            <div class="speaker-profile-card">
                                <div class="speaker-avatar-wrap">
                                    @if($event->speaker_image_url)
                                        <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}">
                                    @else
                                        <i class="fas fa-user-tie avatar-fallback"></i>
                                    @endif
                                </div>
                                <div class="speaker-info-col">
                                    <div class="speaker-name-badge">
                                        <h4>{{ $event->speaker_name }}</h4>
                                        <i class="fas fa-check-circle speaker-verified-icon" title="متحدث معتمد"></i>
                                    </div>
                                    <div class="speaker-job-title">
                                        <i class="fas fa-briefcase"></i>
                                        <span>{{ $event->speaker_title ?: 'خبير ومدرب معتمد' }}</span>
                                    </div>
                                    <p class="speaker-bio-text">
                                        {{ $event->speaker_bio ?: 'يمتلك خبرة عملية واسعة في تدريب وتأهيل الكفاءات وتطوير المهارات المطلوبة في بيئات الأعمال التنافسية وسوق العمل الحديث.' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Target Audience & Prerequisites -->
                    <div class="glass-box">
                        <div class="box-title-row">
                            <div class="box-icon-wrap">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <h3>الفئة المستهدفة وشروط الحضور</h3>
                        </div>

                        <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem; margin-bottom: 14px; line-height: 1.7;">
                            {{ $event->target_audience ?: 'كافة الخريجين والطلبة المتوقع تخرجهم من مختلف كليات جامعة طرابلس، والمهتمين بتطوير جاهزيتهم المهنية واكتساب أدوات المنافسة في سوق العمل.' }}
                        </p>

                        <div class="audience-chips">
                            <span class="audience-chip"><i class="fas fa-user-graduate"></i> خريجو جامعة طرابلس</span>
                            <span class="audience-chip"><i class="fas fa-book-reader"></i> طلبة الفصول النهائية والمتوقع تخرجهم</span>
                            <span class="audience-chip"><i class="fas fa-briefcase"></i> الباحثون عن أول فرصة وظيفية</span>
                            <span class="audience-chip"><i class="fas fa-laptop"></i> المهتمون بالتقنية والذكاء الاصطناعي</span>
                        </div>
                    </div>

                </div>

                <!-- Sidebar Sticky Column -->
                <div class="col-lg-4">
                    <div class="sidebar-sticky">

                        <!-- Registration Action Box -->
                        <div class="reg-action-box">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h4 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: #fff;">
                                    <i class="fas fa-ticket-alt text-warning me-1"></i>
                                    التسجيل في الفعالية
                                </h4>
                                <span class="badge-status-pill badge-status-{{ $event->effective_status }}">
                                    {{ $event->status_label }}
                                </span>
                            </div>

                            @if($event->capacity)
                            <div class="capacity-bar-wrap">
                                <div class="capacity-header">
                                    <span>المقاعد المتاحة</span>
                                    <span class="capacity-number">{{ $event->attendees_count }} / {{ $event->capacity }} مقعد محجوز</span>
                                </div>
                                <div class="progress-track">
                                    @php
                                        $percent = min(100, round(($event->attendees_count / $event->capacity) * 100));
                                    @endphp
                                    <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 0.75rem; color: rgba(255,255,255,0.7);">
                                    <span>نسبة الحجز: {{ $percent }}%</span>
                                    <span class="text-warning">المتبقي: {{ $event->available_spots }} مقعد</span>
                                </div>
                            </div>
                            @endif

                            <!-- Registration Buttons based on Auth & Status -->
                            @auth
                                @if(Auth::user()->role === 'graduate')
                                    @if($isRegistered)
                                        <form method="POST" action="{{ route('job-fair.event.toggle', $event->id) }}" id="toggleRegForm">
                                            @csrf
                                            <button type="submit" class="btn-register-action btn-reg-registered" onclick="return confirm('هل أنت متأكد من رغبتك في إلغاء تسجيلك في هذه الفعالية؟');">
                                                <i class="fas fa-check-circle"></i>
                                                <span>أنت مسجل بالفعل (إلغاء التسجيل)</span>
                                            </button>
                                        </form>
                                        <small class="d-block text-center mt-2" style="font-size: 0.76rem; color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-info-circle text-info"></i> تم حجز مقعدك بنجاح، يرجى الحضور قبل الموعد بـ 15 دقيقة.
                                        </small>
                                    @elseif($event->effective_status === 'completed')
                                        <button class="btn-register-action btn-reg-disabled" disabled>
                                            <i class="fas fa-ban"></i>
                                            <span>نعتذر، اكتملت كافة المقاعد</span>
                                        </button>
                                        <small class="d-block text-center mt-2" style="font-size: 0.76rem; color: #fca5a5;">
                                            يمكنك متابعة الملخص أو مراجعة منصة الاستقبال في يوم المعرض في حال توفر مقاعد شاغرة.
                                        </small>
                                    @elseif($event->effective_status === 'ended')
                                        <button class="btn-register-action btn-reg-disabled" disabled>
                                            <i class="fas fa-hourglass-end"></i>
                                            <span>انتهت هذه الفعالية</span>
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('job-fair.event.toggle', $event->id) }}" id="toggleRegForm">
                                            @csrf
                                            <button type="submit" class="btn-register-action btn-reg-primary">
                                                <i class="fas fa-user-plus"></i>
                                                <span>سجّل الآن واحجز مقعدك</span>
                                            </button>
                                        </form>
                                        <small class="d-block text-center mt-2 text-warning" style="font-size: 0.76rem;">
                                            <i class="fas fa-shield-alt"></i> التسجيل مجاني لجميع خريجي وطلبة جامعة طرابلس.
                                        </small>
                                    @endif
                                @else
                                    <div class="p-3 text-center rounded-3" style="background: rgba(255,255,255,0.06); font-size: 0.85rem;">
                                        <i class="fas fa-user-shield text-warning mb-1 d-block" style="font-size: 1.2rem;"></i>
                                        التسجيل مخصص لحسابات الخريجين والطلبة.
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="btn-register-action btn-reg-primary">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>تسجيل الدخول لحجز المقعد</span>
                                </a>
                                <small class="d-block text-center mt-2" style="font-size: 0.76rem; color: rgba(255,255,255,0.75);">
                                    التسجيل يتطلب حساب خريج مسجل في النظام
                                </small>
                            @endauth

                            <!-- Metadata List -->
                            <div class="meta-list">
                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">التاريخ</span>
                                        <span class="meta-value">
                                            {{ $event->start_time ? $event->start_time->locale('ar')->isoFormat('dddd، D MMMM YYYY') : 'خلال أيام المعرض' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">الوقت والتوقيت</span>
                                        <span class="meta-value">
                                            @if($event->start_time && $event->end_time)
                                                من {{ $event->start_time->format('h:i A') }} إلى {{ $event->end_time->format('h:i A') }}
                                            @elseif($event->start_time)
                                                يبدأ الساعة {{ $event->start_time->format('h:i A') }}
                                            @else
                                                يحدد لاحقاً وفق جدول المعرض
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">المكان والقاعة</span>
                                        <span class="meta-value">{{ $event->location ?: 'المدرج الرئيسي / قاعات التدريب - جامعة طرابلس' }}</span>
                                    </div>
                                </div>

                                <div class="meta-row">
                                    <div class="meta-icon-bubble">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div>
                                        <span class="meta-label">الشهادة / الإثبات</span>
                                        <span class="meta-value">شهادة حضور معتمدة من مكتب تدريب الخريجين</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic QR Code Card -->
                        <div class="qr-card-wrap">
                            <h4 style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 8px;">
                                <i class="fas fa-qrcode text-warning me-1"></i>
                                رمز الاستجابة السريعة (QR Code)
                            </h4>
                            <p class="qr-caption">
                                امسح الرمز من هاتفك للتسجيل الفوري أو مشاركة الفعالية عبر وسائل التواصل
                            </p>

                            <div class="qr-image-frame">
                                <img src="{{ $event->qr_code_url }}" alt="QR Code for {{ $event->title }}" id="eventQrImage">
                            </div>

                            <div class="qr-action-btn-row">
                                <a href="{{ $event->qr_code_url }}" download="QR-{{ Str::slug($event->title) }}.png" target="_blank" class="btn-qr-action">
                                    <i class="fas fa-download"></i>
                                    <span>تحميل الرمز</span>
                                </a>
                                <button class="btn-qr-action" onclick="copyEventLink()">
                                    <i class="fas fa-link"></i>
                                    <span>نسخ الرابط</span>
                                </button>
                            </div>

                            <!-- Social Share -->
                            <div class="social-share-row">
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($event->title . "\n\n" . $event->share_url) }}" target="_blank" class="btn-social-icon bg-whatsapp" title="مشاركة عبر واتساب">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($event->share_url) }}" target="_blank" class="btn-social-icon bg-linkedin" title="مشاركة عبر LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($event->title) }}&url={{ urlencode($event->share_url) }}" target="_blank" class="btn-social-icon bg-twitter" title="مشاركة عبر X">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode($event->share_url) }}&text={{ urlencode($event->title) }}" target="_blank" class="btn-social-icon bg-telegram" title="مشاركة عبر تلغرام">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Related Events Section -->
            @if(isset($relatedEvents) && $relatedEvents->count() > 0)
            <div class="related-section">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin: 0; color: #fff;">
                        <i class="fas fa-calendar-week text-warning me-2"></i>
                        فعاليات أخرى ضمن البرنامج العلمي
                    </h3>
                    <a href="{{ $fair ? route('job-fair.public.program', $fair->id) : route('job-fair.public.program.index') }}" class="btn-nav-outline" style="padding: 5px 12px; font-size: 0.8rem;">
                        عرض كل الفعاليات
                    </a>
                </div>

                <div class="row g-3">
                    @foreach($relatedEvents as $rel)
                    <div class="col-md-4">
                        <a href="{{ route('job-fair.public.events.show', $rel->id) }}" class="related-event-card">
                            <span class="related-badge">
                                <i class="{{ $rel->type_icon }}"></i>
                                {{ $rel->type_short_label }}
                            </span>
                            <h4 class="related-title">{{ Str::limit($rel->title, 65) }}</h4>
                            <div class="related-speaker">
                                <i class="fas fa-user-circle text-warning"></i>
                                <span>{{ $rel->speaker_name ?: 'نخبة من المدربين' }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Official Footer -->
        <footer class="event-footer">
            <div class="container text-center">
                <p class="mb-1" style="font-weight: 700; color: #ffffff;">
                    مكتب تدريب الخريجين — جامعة طرابلس
                </p>
                <p class="mb-0">
                    معرض التوظيف 2026: من الجامعة إلى سوق العمل... نحو خريج أكثر جاهزية وقدرة على المنافسة.
                </p>
            </div>
        </footer>

    </div>

    <!-- Toast Notification for Copy -->
    <div class="toast-copied" id="copyToast">
        <i class="fas fa-check-circle"></i>
        <span>تم نسخ رابط الفعالية بنجاح!</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyEventLink() {
            const link = "{{ $event->share_url }}";
            navigator.clipboard.writeText(link).then(() => {
                const toast = document.getElementById('copyToast');
                toast.style.display = 'flex';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            }).catch(err => {
                prompt("انسخ الرابط التالي:", link);
            });
        }
    </script>
</body>
</html>
