<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرض مشاريع التخرج والأرشيف السنوي — {{ $fair ? $fair->title : 'معرض التوظيف 2026' }}</title>
    <meta name="description" content="معرض وأرشيف مشاريع تخرج الطلبة المتميزة المشاركة في {{ $fair ? $fair->title : 'معرض التوظيف بجامعة طرابلس' }}. ابتكارات وإبداعات خريجي كليات جامعة طرابلس.">

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
                radial-gradient(ellipse 80% 60% at 15% 30%, rgba(245,158,11,0.18) 0%, transparent 60%),
                radial-gradient(ellipse 70% 80% at 85% 20%, rgba(14,165,233,0.22) 0%, transparent 55%),
                radial-gradient(ellipse 60% 60% at 50% 90%, rgba(16,185,129,0.14) 0%, transparent 50%),
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
        .orb-1 { width: 500px; height: 500px; background: rgba(238,202,62,0.15); top: -80px; left: -80px; }
        .orb-2 { width: 550px; height: 550px; background: rgba(14,165,233,0.18); bottom: 10%; right: -120px; animation-duration: 18s; animation-delay: -5s; }
        .orb-3 { width: 350px; height: 350px; background: rgba(59,130,246,0.2); top: 45%; left: 35%; animation-duration: 22s; animation-delay: -10s; }

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
        .top-nav {
            position: sticky;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(3, 40, 80, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }
        .top-nav.scrolled {
            padding: 0.6rem 2rem;
            background: rgba(2, 32, 66, 0.95);
            border-bottom: 1px solid rgba(238, 202, 62, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
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
            border: none;
            border-radius: 0;
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
            transition: transform 0.2s ease;
        }
        .waha-nav-logo:hover {
            transform: scale(1.05);
        }

        .nav-links { display: flex; align-items: center; gap: 0.65rem; }
        .nav-btn {
            padding: 8px 18px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 0.86rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1.4;
        }
        .nav-btn-outline {
            background: rgba(255, 255, 255, 0.08);
            border: 1.5px solid rgba(255, 255, 255, 0.28);
            color: white;
            backdrop-filter: blur(8px);
        }
        .nav-btn-outline:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: var(--gold);
            color: var(--gold);
        }
        .nav-btn-gold {
            background: linear-gradient(135deg, var(--gold), #F97316);
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
        }
        .nav-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(245, 158, 11, 0.55);
            color: white;
        }

        /* ══════════════════════════════════
           HERO SECTION & STATS BAR
        ══════════════════════════════════ */
        .projects-hero {
            padding: 45px 0 25px;
            text-align: center;
            position: relative;
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            border-radius: 30px;
            background: rgba(238,202,62,0.18);
            border: 1.5px solid rgba(238,202,62,0.45);
            color: var(--gold-lt);
            font-weight: 800;
            font-size: 0.88rem;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(238,202,62,0.2);
        }

        .hero-title {
            font-size: 2.6rem;
            font-weight: 900;
            line-height: 1.3;
            color: #ffffff;
            margin-bottom: 12px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.85);
            max-width: 850px;
            margin: 0 auto 30px;
            line-height: 1.7;
        }

        /* Stats Bar */
        .stats-bar-card {
            background: rgba(8, 34, 69, 0.7);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 20px 30px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            margin-bottom: 40px;
        }

        .stat-item {
            text-align: center;
            position: relative;
        }
        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: rgba(255,255,255,0.12);
        }

        .stat-val {
            font-size: 2rem;
            font-weight: 900;
            color: var(--gold);
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.75);
            font-weight: 600;
        }

        /* ══════════════════════════════════
           FILTER & SEARCH BAR
        ══════════════════════════════════ */
        .filter-panel {
            background: rgba(8, 34, 69, 0.75);
            border: 1.5px solid rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            padding: 24px 28px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            margin-bottom: 35px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
        }

        .search-input-box {
            position: relative;
        }

        .search-input-box input {
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            padding: 12px 46px 12px 18px;
            color: #fff;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.25s ease;
        }
        .search-input-box input:focus {
            outline: none;
            background: rgba(255,255,255,0.14);
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(238,202,62,0.2);
            color: #fff;
        }
        .search-input-box input::placeholder {
            color: rgba(255,255,255,0.5);
        }

        .search-icon-pos {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold-lt);
            font-size: 1.05rem;
            pointer-events: none;
        }

        .filter-select {
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.18);
            border-radius: 14px;
            padding: 12px 16px;
            color: #fff;
            font-size: 0.9rem;
            width: 100%;
            transition: all 0.25s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23eeca3e'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 14px center;
            background-size: 14px 12px;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--gold);
            background-color: #092347;
            color: #fff;
        }
        .filter-select option {
            background: #092347;
            color: #fff;
        }

        .btn-filter-reset {
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.18);
            color: rgba(255,255,255,0.8);
            border-radius: 14px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            width: 100%;
        }
        .btn-filter-reset:hover {
            background: rgba(255,255,255,0.16);
            color: #fff;
            border-color: var(--gold);
        }

        /* ══════════════════════════════════
           PROJECT CARDS GRID
        ══════════════════════════════════ */
        .project-card {
            background: rgba(8, 34, 69, 0.72);
            border: 1.5px solid rgba(255, 255, 255, 0.14);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 14px 35px rgba(0,0,0,0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.35s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .project-card:hover {
            border-color: var(--gold);
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.4);
        }

        .project-thumb-header {
            height: 160px;
            background: linear-gradient(135deg, rgba(4,93,176,0.8) 0%, rgba(9,35,71,0.95) 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .project-thumb-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            transition: transform 0.5s ease;
        }
        .project-card:hover .project-thumb-bg {
            transform: scale(1.08);
            opacity: 0.55;
        }

        .thumb-icon-overlay {
            width: 65px;
            height: 65px;
            border-radius: 18px;
            background: rgba(255,255,255,0.12);
            border: 1.5px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--gold-lt);
            position: relative;
            z-index: 2;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .thumb-top-badges {
            position: absolute;
            top: 14px;
            right: 14px;
            left: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 3;
        }

        .badge-faculty {
            background: rgba(8, 34, 69, 0.85);
            border: 1px solid rgba(238,202,62,0.4);
            color: var(--gold-lt);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            backdrop-filter: blur(6px);
        }

        .badge-booth {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(16,185,129,0.3);
        }

        .project-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .project-dept-meta {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.65);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .project-title {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.45;
            color: #ffffff;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 3.3em;
        }

        .project-summary {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 4.8em;
        }

        .project-meta-box {
            background: rgba(255,255,255,0.035);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: auto;
            margin-bottom: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 0.78rem;
        }

        .meta-line {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.85);
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        .meta-line i {
            color: var(--gold-lt);
            width: 14px;
            text-align: center;
            flex-shrink: 0;
        }

        .btn-view-project {
            background: linear-gradient(135deg, rgba(238,202,62,0.2) 0%, rgba(245,158,11,0.25) 100%);
            border: 1.5px solid var(--gold);
            color: #ffffff;
            border-radius: 12px;
            padding: 10px;
            font-weight: 800;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
        }
        .btn-view-project:hover {
            background: linear-gradient(135deg, #eeca3e 0%, #f59e0b 100%);
            color: #061c38;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245,158,11,0.35);
        }

        /* Footer */
        .projects-footer {
            margin-top: auto;
            background: rgba(4, 30, 60, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* Coming Soon Styles */
        .coming-soon-wrapper {
            position: relative;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            padding: 4rem 2rem;
            text-align: center;
            backdrop-filter: blur(16px);
            overflow: hidden;
            margin: 2rem auto 4rem;
            max-width: 1100px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .coming-soon-glow {
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 250px;
            background: radial-gradient(circle, rgba(238, 202, 62, 0.25) 0%, transparent 70%);
            pointer-events: none;
            filter: blur(40px);
        }

        .cs-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.35);
            border-radius: 50px;
            color: var(--gold);
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 1.8rem;
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 10px var(--gold);
            animation: pulse-gold 1.5s infinite;
        }

        @keyframes pulse-gold {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
        }

        .cs-main-title {
            font-size: 2.4rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 1.2rem;
        }

        .cs-main-title span {
            background: linear-gradient(135deg, #ffffff 30%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cs-description {
            font-size: 1.12rem;
            color: rgba(255, 255, 255, 0.82);
            max-width: 800px;
            margin: 0 auto 2.8rem;
            line-height: 1.8;
        }

        .cs-tracks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.4rem;
            max-width: 1050px;
            margin: 0 auto 3rem;
            text-align: right;
        }

        .cs-track-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1.4rem;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .cs-track-card:hover {
            transform: translateY(-4px);
            border-color: rgba(238, 202, 62, 0.4);
            background: rgba(255, 255, 255, 0.07);
        }

        .cs-track-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .cs-icon-gold { background: rgba(238, 202, 62, 0.2); color: var(--gold); }
        .cs-icon-blue { background: rgba(14, 165, 233, 0.2); color: #38bdf8; }
        .cs-icon-purple { background: rgba(168, 85, 247, 0.2); color: #c084fc; }
        .cs-icon-green { background: rgba(16, 185, 129, 0.2); color: #34d399; }

        .cs-track-text h4 {
            font-size: 1rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .cs-track-text p {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.65);
            margin: 0;
            line-height: 1.5;
        }

        .cs-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-cs-gold {
            background: linear-gradient(135deg, var(--gold), #f59e0b);
            color: #061c38;
            border: none;
            padding: 14px 34px;
            border-radius: 50px;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .btn-cs-gold:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.5);
            color: #04244d;
        }

        .btn-cs-outline {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            padding: 14px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .btn-cs-outline:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.5);
            color: #ffffff;
            transform: translateY(-3px);
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 1.8rem; }
            .stat-item:not(:last-child)::after { display: none; }
            .stat-item { margin-bottom: 16px; }
            .cs-main-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="hero-bg-layer"></div>
    <div class="hero-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="page-content-wrapper">

        <!-- Top Navigation -->
        <nav class="top-nav" id="topNav">
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

                {{-- فاصل رأسي --}}
                <div class="nav-brand-divider d-none d-sm-block"></div>

                {{-- شعار شركة الواحة للمعارض المباشر --}}
                <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" class="waha-nav-logo d-none d-sm-block" title="شركة الواحة لتنظيم المعارض والمؤتمرات — الراعي الاستراتيجي" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
            </a>

            <div class="nav-links">
                @if($fair)
                    <a href="{{ route('job-fair.public', $fair->id) }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-arrow-right"></i>العودة للمعرض
                    </a>
                    <a href="{{ route('job-fair.public.companies', $fair->id) }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-building"></i>دليل الشركات
                    </a>
                    <a href="{{ route('job-fair.public.program', $fair->id) }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-graduation-cap"></i>البرنامج العلمي
                    </a>
                @endif

                @auth
                    <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-th-large"></i>لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">تسجيل الدخول</a>
                    <a href="{{ route('graduate.register') }}" class="nav-btn nav-btn-gold">
                        <i class="fas fa-user-plus"></i>سجّل كخريج
                    </a>
                    @if($fair)
                        <a href="{{ route('job-fair.public', $fair->id) }}#visitor-register" class="nav-btn nav-btn-outline">
                            <i class="fas fa-id-badge" style="color:var(--gold)"></i>سجّل كزائر
                        </a>
                    @endif
                @endauth
            </div>
        </nav>

        <div class="container pb-5">

            <!-- Hero Section -->
            <section class="projects-hero">
                <div class="hero-badge-pill">
                    <i class="fas fa-award"></i>
                    <span>منصة ابتكارات خريجي جامعة طرابلس — الأرشيف السنوي</span>
                </div>
                <h2 class="hero-title">معرض مشاريع التخرج المتميزة</h2>
                <p class="hero-subtitle">
                    نستعرض هنا نخبة من ابتكارات وبحوث تخرج طلبتنا في مختلف التخصصات، لربط أصحاب الأفكار الإبداعية برجال الأعمال والمؤسسات الوطنية والدولية لدعمها وتمويلها وتوظيف كوادرها.
                </p>

                <!-- Stats Bar -->
                <div class="stats-bar-card">
                    <div class="row g-3">
                        <div class="col-6 col-md-3 stat-item">
                            <div class="stat-val">{{ $totalProjects }}</div>
                            <div class="stat-label">مشروع تخرج معروض</div>
                        </div>
                        <div class="col-6 col-md-3 stat-item">
                            <div class="stat-val">{{ $totalFaculties }}</div>
                            <div class="stat-label">كليات مشاركة</div>
                        </div>
                        <div class="col-6 col-md-3 stat-item">
                            <div class="stat-val">{{ $totalStudents }}</div>
                            <div class="stat-label">طالب وطالبة مبتكرين</div>
                        </div>
                        <div class="col-6 col-md-3 stat-item">
                            <div class="stat-val">{{ $years->count() }}</div>
                            <div class="stat-label">سنوات الأرشيف الأكاديمي</div>
                        </div>
                    </div>
                </div>
            </section>

            @php
                $isProjectsComingSoon = !($fair && $fair->is_projects_published);
            @endphp

            @if($isProjectsComingSoon)
            {{-- ══════════════════════════════════
                 COMING SOON SECTION (مشاريع التخرج قريباً)
            ══════════════════════════════════ --}}
            <div class="coming-soon-wrapper">
                <div class="coming-soon-glow"></div>
                
                <div class="cs-badge-pill">
                    <span class="pulse-dot"></span>
                    <span>ترقبوا الإطلاق الرسمي قريباً</span>
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill ms-2 font-monospace" style="font-size: 0.76rem;">Coming Soon</span>
                </div>

                <h2 class="cs-main-title">
                    معرض وأرشيف مشاريع التخرج <br>
                    <span>قيد التحضير والفهرسة النهائية</span>
                </h2>

                <p class="cs-description">
                    نستكمل حالياً استقبال وفهرسة نخبة مشاريع وبحوث تخرج طلبة كليات جامعة طرابلس، وتجهيز أجنحة العرض التفاعلية ورموز الـ QR Code الخاصة بكل ابتكار.
                    <br>
                    <strong class="text-white">سيتم إتاحة الأرشيف الكامل وتصفح المشاريع وتنزيل البوسترات قريباً لكافة الزوار والشركات.</strong>
                </p>

                {{-- Features Preview Grid --}}
                <div class="cs-tracks-grid">
                    <div class="cs-track-card">
                        <div class="cs-track-icon cs-icon-blue">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="cs-track-text">
                            <h4>الذكاء الاصطناعي والتقنية</h4>
                            <p>حلول برمجية وأنظمة ذكية من خريجي تقنية المعلومات والهندسة</p>
                        </div>
                    </div>

                    <div class="cs-track-card">
                        <div class="cs-track-icon cs-icon-gold">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="cs-track-text">
                            <h4>الابتكارات الهندسية والصناعية</h4>
                            <p>نماذج تطبيقية واختراعات صناعية قابلة للتبني والتطوير</p>
                        </div>
                    </div>

                    <div class="cs-track-card">
                        <div class="cs-track-icon cs-icon-green">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div class="cs-track-text">
                            <h4>الاستدامة والطاقة والبيئة</h4>
                            <p>بحوث تطبيقية لمعالجة قضايا البيئة والموارد المستدامة</p>
                        </div>
                    </div>

                    <div class="cs-track-card">
                        <div class="cs-track-icon cs-icon-purple">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="cs-track-text">
                            <h4>مشاريع ريادية واستثمارية</h4>
                            <p>أفكار مشاريع ناشئة واعدة تلبي احتياجات سوق العمل الليبي</p>
                        </div>
                    </div>
                </div>

                <div class="cs-actions">
                    <a href="{{ route('job-fair.public.companies', $fair ? $fair->id : 1) }}" class="btn-cs-gold">
                        <i class="fas fa-building me-1"></i>استكشف الشركات والجهات المشاركة في المعرض
                    </a>
                    <a href="{{ route('job-fair.public', $fair ? $fair->id : null) }}" class="btn-cs-outline">
                        <i class="fas fa-arrow-right me-1"></i>العودة للصفحة الرئيسية للمعرض
                    </a>
                </div>
            </div>

            @else

            <!-- Filter & Search Bar -->
            <div class="filter-panel">
                <form action="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <!-- Search Box -->
                        <div class="col-lg-4 col-md-12">
                            <div class="search-input-box">
                                <i class="fas fa-search search-icon-pos"></i>
                                <input type="text" name="search" id="projectSearchInput" value="{{ request('search') }}" placeholder="ابحث بالعنوان، المشرف، أو أسماء الطلبة...">
                            </div>
                        </div>

                        <!-- Faculty Filter -->
                        <div class="col-lg-3 col-md-4">
                            <select name="faculty" class="filter-select" onchange="this.form.submit()">
                                <option value="">جميع الكليات ({{ $faculties->count() }})</option>
                                @foreach($faculties as $fac)
                                    <option value="{{ $fac }}" {{ request('faculty') == $fac ? 'selected' : '' }}>{{ $fac }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department Filter -->
                        <div class="col-lg-3 col-md-4">
                            <select name="department" class="filter-select" onchange="this.form.submit()">
                                <option value="">جميع التخصصات / الأقسام</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter / Reset -->
                        <div class="col-lg-2 col-md-4 d-flex gap-2">
                            <select name="year" class="filter-select" onchange="this.form.submit()">
                                <option value="">سنة التخرج</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>

                            @if(request()->anyFilled(['search', 'faculty', 'department', 'year']))
                            <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="btn-filter-reset" title="إعادة تعيين الفلاتر">
                                <i class="fas fa-undo"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Projects Grid -->
            <div class="row g-4" id="projectsContainer">
                @forelse($projects as $proj)
                <div class="col-lg-4 col-md-6 project-item-col" 
                     data-title="{{ strtolower($proj->title) }}" 
                     data-faculty="{{ $proj->faculty }}" 
                     data-dept="{{ $proj->department }}">
                    <div class="project-card">
                        <!-- Thumbnail Header -->
                        <div class="project-thumb-header">
                            @if($proj->cover_url)
                                <img src="{{ $proj->cover_url }}" alt="{{ $proj->title }}" class="project-thumb-bg">
                            @endif
                            <div class="thumb-top-badges">
                                <span class="badge-faculty">
                                    <i class="{{ $proj->faculty_icon }}"></i>
                                    <span>{{ $proj->faculty }}</span>
                                </span>
                                @if($proj->booth_number)
                                <span class="badge-booth">
                                    <i class="fas fa-store-alt me-1"></i>جناح {{ $proj->booth_number }}
                                </span>
                                @endif
                            </div>

                            <div class="thumb-icon-overlay">
                                <i class="{{ $proj->faculty_icon }}"></i>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="project-card-body">
                            <div class="project-dept-meta">
                                <span><i class="fas fa-layer-group text-warning"></i> {{ $proj->department }}</span>
                                <span>•</span>
                                <span><i class="fas fa-calendar-alt text-info"></i> {{ $proj->graduation_year }}</span>
                            </div>

                            <h3 class="project-title" title="{{ $proj->title }}">{{ $proj->title }}</h3>

                            <p class="project-summary">
                                {{ $proj->summary ?: Str::limit(strip_tags($proj->description), 140) }}
                            </p>

                            <!-- Meta Box -->
                            <div class="project-meta-box">
                                <div class="meta-line" title="أعضاء الفريق">
                                    <i class="fas fa-users"></i>
                                    <span>
                                        @php
                                            $names = collect($proj->team_list)->pluck('name')->filter()->implode('، ');
                                        @endphp
                                        {{ $names ?: 'فريق الخريجين' }}
                                    </span>
                                </div>
                                <div class="meta-line" title="المشرف الأكاديمي">
                                    <i class="fas fa-user-tie"></i>
                                    <span>إشراف: {{ $proj->supervisor_name ?? 'القسم الأكاديمي' }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('job-fair.public.projects.show', $proj->id) }}" class="btn-view-project">
                                <i class="fas fa-qrcode"></i>
                                <span>تفاصيل المشروع ورمز QR</span>
                                <i class="fas fa-arrow-left me-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 py-5 text-center">
                    <div style="background: rgba(8, 34, 69, 0.6); border: 1.5px dashed rgba(255,255,255,0.2); border-radius: 20px; padding: 50px 20px;">
                        <i class="fas fa-folder-open fa-4x text-warning mb-3 d-block"></i>
                        <h4 class="fw-bold text-white mb-2">لم يتم العثور على مشاريع تخرج مطابقة</h4>
                        <p class="text-white-50 small mb-4">جرب البحث بكلمات أخرى أو اختر كلية أو تخصصاً مختلفاً.</p>
                        <a href="{{ $fair ? route('job-fair.public.projects', $fair->id) : route('job-fair.public.projects.index') }}" class="btn-nav-outline">
                            <i class="fas fa-undo"></i>
                            <span>عرض كافة المشاريع</span>
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            @endif

        </div>

        <!-- Footer -->
        <footer class="projects-footer">
            <div class="container text-center">
                <p class="mb-1 fw-bold text-white">
                    مكتب تدريب الخريجين — جامعة طرابلس &bull; الراعي الاستراتيجي: <span class="text-warning">شركة الواحة لتنظيم المعارض والمؤتمرات</span>
                </p>
                <p class="mb-0">
                    أرشيف مشاريع التخرج: ربط الابتكار الأكاديمي بفرص سوق العمل والتطوير المستدام.
                </p>
            </div>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Realtime search filtering on cards
        const searchInput = document.getElementById('projectSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                const cards = document.querySelectorAll('.project-item-col');
                cards.forEach(card => {
                    const title = card.getAttribute('data-title') || '';
                    const faculty = (card.getAttribute('data-faculty') || '').toLowerCase();
                    const dept = (card.getAttribute('data-dept') || '').toLowerCase();
                    const text = card.textContent.toLowerCase();

                    if (text.includes(term) || title.includes(term) || faculty.includes(term) || dept.includes(term)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>
