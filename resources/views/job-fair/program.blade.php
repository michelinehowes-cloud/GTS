<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البرنامج العلمي والتدريبي: رحلة الجاهزية المهنية — {{ $fair ? $fair->title : 'معرض التوظيف 2026' }}</title>
    <meta name="description" content="البرنامج العلمي والتدريبي المصاحب لـ {{ $fair ? $fair->title : 'معرض التوظيف' }}: ماستر كلاس، ورش عمل تطبيقية، وجلسات حوارية دولية.">

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
            filter: blur(70px);
            pointer-events: none;
            animation: orb-float 8s ease-in-out infinite;
            z-index: 0;
        }
        .orb-1 { width: 450px; height: 450px; background: rgba(245,158,11,0.15); top: -80px; right: -80px; }
        .orb-2 { width: 380px; height: 380px; background: rgba(14,165,233,0.18); bottom: 5%; left: -80px; animation-delay: -3s; }
        .orb-3 { width: 280px; height: 280px; background: rgba(16,185,129,0.12); top: 35%; left: 45%; animation-delay: -5s; }

        @keyframes orb-float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.06); }
        }

        .particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(238, 202, 62, 0.85);
            box-shadow: 0 0 10px rgba(238, 202, 62, 0.6);
            pointer-events: none;
            animation: particle-rise linear infinite;
            z-index: 1;
        }
        @keyframes particle-rise {
            0%   { transform: translateY(110vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.7; }
            100% { transform: translateY(-10vh) scale(1.3); opacity: 0; }
        }

        /* ══════════════════════════════════
           TOP NAV
        ══════════════════════════════════ */
        .top-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(3, 40, 80, 0.75);
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
           HERO & CAREER JOURNEY
        ══════════════════════════════════ */
        .program-hero {
            position: relative;
            z-index: 10;
            padding: 130px 0 35px;
            text-align: center;
        }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.45);
            color: var(--gold);
            padding: 6px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            backdrop-filter: blur(8px);
        }
        .hero-eyebrow .dot {
            width: 6px; height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.25} }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 55%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin: 0 auto 2.2rem;
            font-weight: 400;
            max-width: 780px;
            line-height: 1.8;
        }

        /* Career Journey 5-Steps Strip */
        .career-journey-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin: 0 auto 2.5rem;
            max-width: 1080px;
        }
        .journey-step {
            background: rgba(10, 38, 77, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(12px);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.25s;
        }
        .journey-step:hover {
            border-color: var(--gold);
            transform: translateY(-2px);
            background: rgba(15, 52, 105, 0.8);
        }
        .journey-step .step-num {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: var(--gold);
            color: #071933;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 900;
        }
        .journey-arrow {
            color: var(--gold);
            opacity: 0.6;
            font-size: 0.9rem;
        }

        /* Stats Bar */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            background: rgba(10, 38, 77, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 1.3rem 2.2rem;
            margin: 0 auto 2.5rem;
            max-width: 980px;
            backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }
        .hero-stat { text-align: center; min-width: 120px; }
        .hero-stat-num {
            font-size: 2.2rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold), #FBBF24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            text-shadow: 0 0 25px rgba(245, 158, 11, 0.35);
        }
        .hero-stat-lbl {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.82rem;
            margin-top: 4px;
            font-weight: 600;
        }
        .hero-stat-divider {
            width: 1px;
            background: rgba(255, 255, 255, 0.14);
            align-self: stretch;
        }

        /* ══════════════════════════════════
           CONTROLS PANEL
        ══════════════════════════════════ */
        .controls-panel {
            background: rgba(8, 32, 66, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 26px;
            padding: 1.6rem 2rem;
            backdrop-filter: blur(18px);
            margin-bottom: 2rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.28);
        }
        .search-input-wrap { position: relative; }
        .search-input-wrap i.search-icon {
            position: absolute; right: 20px; top: 50%;
            transform: translateY(-50%);
            color: var(--gold);
            font-size: 1.15rem;
            pointer-events: none;
        }
        .search-box {
            width: 100%;
            background: rgba(4, 23, 50, 0.75);
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 14px 50px 14px 45px;
            color: #ffffff;
            font-size: 1rem;
            font-family: 'Cairo', sans-serif;
            outline: none;
            transition: all 0.3s;
        }
        .search-box::placeholder { color: rgba(255, 255, 255, 0.55); }
        .search-box:focus {
            border-color: var(--gold);
            background: rgba(4, 23, 50, 0.95);
            box-shadow: 0 0 0 4px rgba(238, 202, 62, 0.25);
        }
        .search-clear-btn {
            position: absolute; left: 18px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #cbd5e1; font-size: 1.1rem;
            cursor: pointer; padding: 4px; display: none;
        }

        .filter-chips-container {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 1.2rem;
            padding-top: 1.1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .filter-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--gold);
            margin-left: 0.5rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-chip {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            padding: 7px 18px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            user-select: none;
        }
        .filter-chip:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: var(--gold);
            color: var(--gold);
        }
        .filter-chip.active {
            background: linear-gradient(135deg, var(--gold), #F59E0B);
            border-color: var(--gold);
            color: #071933;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(238, 202, 62, 0.4);
        }
        .chip-count {
            background: rgba(0, 0, 0, 0.22);
            padding: 1px 8px;
            border-radius: 20px;
            font-size: 0.74rem;
        }
        .filter-chip.active .chip-count {
            background: rgba(7, 25, 51, 0.25);
            color: #071933;
        }

        /* ══════════════════════════════════
           EVENT CARDS GRID
        ══════════════════════════════════ */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.8rem;
            margin-bottom: 5rem;
        }

        .event-card {
            background: rgba(8, 34, 69, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 1.8rem;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            backdrop-filter: blur(16px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }
        .event-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            background: rgba(12, 45, 92, 0.85);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4), 0 0 30px rgba(238, 202, 62, 0.25);
        }

        /* Type Badges */
        .badge-type-masterclass {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.76rem;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.35);
        }
        .badge-type-workshop {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.76rem;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }
        .badge-type-panel {
            background: linear-gradient(135deg, #059669, #34d399);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.76rem;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.74rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .status-open {
            background: rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.5);
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.25);
        }
        .status-upcoming {
            background: rgba(14, 165, 233, 0.25);
            color: #bae6fd;
            border: 1px solid rgba(14, 165, 233, 0.45);
        }
        .status-completed {
            background: rgba(245, 158, 11, 0.25);
            color: #fde047;
            border: 1px solid rgba(245, 158, 11, 0.5);
        }
        .status-ended {
            background: rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        .event-card-title {
            font-size: 1.22rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0.9rem 0 0.6rem;
            line-height: 1.4;
        }

        .event-speaker-box {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1rem 0;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .speaker-avatar {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: rgba(238, 202, 62, 0.2);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            border: 1.5px solid var(--gold);
            object-fit: cover;
        }

        .event-meta-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin: 1rem 0;
            font-size: 0.84rem;
            color: rgba(255, 255, 255, 0.78);
        }
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-meta-item i { width: 16px; text-align: center; }

        /* Progress Capacity */
        .capacity-bar-wrap {
            margin: 1rem 0 1.2rem;
        }
        .capacity-bar-track {
            height: 6px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .capacity-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10B981, var(--gold));
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        /* Card Action Buttons */
        .event-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-register-event {
            flex-grow: 1;
            background: linear-gradient(135deg, var(--gold), #F59E0B);
            border: none;
            color: #071933;
            font-size: 0.88rem;
            font-weight: 800;
            padding: 10px 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-register-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 202, 62, 0.4);
            color: #000000;
        }
        .btn-register-event.registered {
            background: rgba(16, 185, 129, 0.25);
            border: 1px solid #10B981;
            color: #6ee7b7;
        }
        .btn-register-event.disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.4);
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-qr-link {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .btn-qr-link:hover {
            background: rgba(238, 202, 62, 0.3);
            border-color: var(--gold);
            color: var(--gold);
            transform: translateY(-2px);
        }

        /* ══════════════════════════════════
           FOOTER
        ══════════════════════════════════ */
        .page-footer {
            margin-top: auto;
            background: rgba(2, 23, 48, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding: 1.8rem 1rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.88rem;
            position: relative;
            z-index: 10;
        }
        .page-footer a { color: var(--gold); text-decoration: none; font-weight: 700; }

        @media (max-width: 991px) {
            .hero-stats { gap: 1.2rem; padding: 1.2rem 1.5rem; }
            .hero-stat { min-width: 100px; }
            .hero-stat-num { font-size: 1.8rem; }
        }
        @media (max-width: 768px) {
            .top-nav { padding: 0.65rem 1rem; }
            .nav-brand-text { display: none; }
            .program-hero { padding: 105px 0 25px; }
            .hero-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
            .hero-stat-divider { display: none; }
            .controls-panel { padding: 1.2rem; }
            .events-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- VIBRANT ANIMATED BACKGROUND LAYERS --}}
<div class="hero-bg-layer"></div>
<div class="hero-grid"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

{{-- FLOATING PARTICLES (EMBERS) --}}
<div id="particles-container"></div>

{{-- ══════════════════════════════════
     TOP NAV
══════════════════════════════════ --}}
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
    </a>

    <div class="nav-links">
        @if($fair)
            <a href="{{ route('job-fair.public', $fair->id) }}" class="nav-btn nav-btn-outline">
                <i class="fas fa-arrow-right"></i>العودة للمعرض
            </a>
            <a href="{{ route('job-fair.public.companies', $fair->id) }}" class="nav-btn nav-btn-outline">
                <i class="fas fa-building"></i>دليل الشركات
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
        @endauth
    </div>
</nav>

{{-- ══════════════════════════════════
     HERO SECTION
══════════════════════════════════ --}}
<header class="program-hero">
    <div class="container">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            رحلة الجاهزية المهنية — معرض التوظيف 2026
            <span class="dot"></span>
        </div>

        <h1 class="hero-title">
            البرنامج العلمي والتدريبي <span>المصاحب</span>
        </h1>

        <p class="hero-subtitle">
            من الجامعة إلى سوق العمل... نحو خريج أكثر جاهزية وقدرة على المنافسة. سلسلة متكاملة من ورش العمل التطبيقية، الماستر كلاس، والجلسات الحوارية الاستراتيجية بمشاركة نخبة من الخبراء والمدربين المحليين والدوليين.
        </p>

        {{-- 5-STAGE CAREER JOURNEY STRIP --}}
        <div class="career-journey-strip">
            <div class="journey-step">
                <span class="step-num">1</span>
                <span>افهم السوق</span>
            </div>
            <i class="fas fa-arrow-left journey-arrow"></i>
            <div class="journey-step">
                <span class="step-num">2</span>
                <span>جهّز نفسك</span>
            </div>
            <i class="fas fa-arrow-left journey-arrow"></i>
            <div class="journey-step">
                <span class="step-num">3</span>
                <span>افهم كيف يتم اختيارك</span>
            </div>
            <i class="fas fa-arrow-left journey-arrow"></i>
            <div class="journey-step">
                <span class="step-num">4</span>
                <span>استخدم أدوات المستقبل (AI)</span>
            </div>
            <i class="fas fa-arrow-left journey-arrow"></i>
            <div class="journey-step">
                <span class="step-num">5</span>
                <span>ابنِ مساراً مهنياً منافساً</span>
            </div>
        </div>

        {{-- STATS BAR --}}
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $totalEvents }}</div>
                <div class="hero-stat-lbl">جلسات وفعاليات</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $masterclassCount }}</div>
                <div class="hero-stat-lbl">Masterclass رئيسية</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $workshopsCount }}</div>
                <div class="hero-stat-lbl">ورش عمل تطبيقية</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $panelsCount }}</div>
                <div class="hero-stat-lbl">جلسات حوارية</div>
            </div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat">
                <div class="hero-stat-num">{{ $totalCapacity > 0 ? $totalCapacity : '1000+' }}</div>
                <div class="hero-stat-lbl">مقعد تدريبي متاح</div>
            </div>
        </div>

    </div>
</header>

{{-- ══════════════════════════════════
     MAIN PROGRAM CONTENT
══════════════════════════════════ --}}
<main class="container position-relative z-10 mb-5">

    {{-- CONTROLS PANEL --}}
    <div class="controls-panel">
        <div class="search-input-wrap">
            <input type="text" 
                   id="eventSearchInput" 
                   class="search-box" 
                   placeholder="ابحث بعنوان الفعالية، المتحدث، المحاور، أو القاعة..."
                   autocomplete="off">
            <i class="fas fa-search search-icon"></i>
            <button type="button" id="clearSearchBtn" class="search-clear-btn" title="مسح">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="filter-chips-container">
            <span class="filter-label"><i class="fas fa-filter"></i>التصنيف:</span>
            <div class="filter-chip active" data-type="all">
                الكل <span class="chip-count">{{ $totalEvents }}</span>
            </div>
            <div class="filter-chip" data-type="masterclass">
                <i class="fas fa-graduation-cap"></i>ماستر كلاس <span class="chip-count">{{ $masterclassCount }}</span>
            </div>
            <div class="filter-chip" data-type="workshop">
                <i class="fas fa-laptop-code"></i>ورش عمل تطبيقية <span class="chip-count">{{ $workshopsCount }}</span>
            </div>
            <div class="filter-chip" data-type="panel_discussion">
                <i class="fas fa-comments"></i>جلسات حوارية <span class="chip-count">{{ $panelsCount }}</span>
            </div>
        </div>
    </div>

    {{-- RESULTS HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div class="text-white-50 small">
            يتم عرض <strong id="visibleCount" class="text-warning fw-bold">{{ $totalEvents }}</strong> من أصل {{ $totalEvents }} فعالية
        </div>
        <div class="text-white-50 small">
            <i class="fas fa-info-circle me-1 text-warning"></i> التسجيل مجاني ومتاح لكافة خريجي وطلبة جامعة طرابلس
        </div>
    </div>

    {{-- EMPTY STATE --}}
    <div id="emptyState" class="text-center py-5" style="display:none; background: rgba(8, 32, 66, 0.6); border-radius: 24px;">
        <i class="fas fa-search-minus fa-3x text-warning mb-3"></i>
        <h4 class="text-white fw-bold">لم يتم العثور على فعاليات مطابقة</h4>
        <p class="text-white-50">جرب البحث بكلمات أخرى أو اختر تصنيفاً آخر.</p>
    </div>

    {{-- EVENTS GRID --}}
    <div class="events-grid" id="eventsGrid">
        @forelse($events as $event)
        @php
            $typeClass = match($event->type) {
                'masterclass'      => 'badge-type-masterclass',
                'panel_discussion' => 'badge-type-panel',
                default            => 'badge-type-workshop',
            };
            $statusClass = match($event->effective_status) {
                'open'      => 'status-open',
                'upcoming'  => 'status-upcoming',
                'completed' => 'status-completed',
                'ended'     => 'status-ended',
                default     => 'status-open',
            };
            $isRegistered = in_array($event->id, $registeredEventIds);
            $attendeesCount = $event->attendees_count ?? 0;
            $cap = $event->capacity ?: 100;
            $percent = min(100, round(($attendeesCount / $cap) * 100));
        @endphp
        <div class="event-card event-item-card"
             data-title="{{ mb_strtolower($event->title) }}"
             data-type="{{ $event->type }}"
             data-speaker="{{ mb_strtolower($event->speaker_name ?? '') }}"
             data-location="{{ mb_strtolower($event->location ?? '') }}"
             data-topics="{{ mb_strtolower($event->topics ?? '') }}">

            {{-- Card Top Row: Type & Status --}}
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="{{ $typeClass }}">
                        <i class="{{ $event->type_icon }} me-1"></i>{{ $event->type_short_label }}
                    </span>
                    <span class="status-badge {{ $statusClass }}">
                        <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>{{ $event->status_label }}
                    </span>
                </div>

                <h3 class="event-card-title">{{ $event->title }}</h3>

                {{-- Speaker Strip --}}
                @if($event->speaker_name)
                <div class="event-speaker-box">
                    @if($event->speaker_image_url)
                        <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}" class="speaker-avatar">
                    @else
                        <div class="speaker-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    @endif
                    <div class="text-truncate">
                        <div class="fw-bold text-white" style="font-size:0.95rem;">{{ $event->speaker_name }}</div>
                        <small class="text-white-50 d-block text-truncate" style="font-size:0.75rem;">{{ $event->speaker_title ?: 'خبير ومحاضر معتمد' }}</small>
                    </div>
                </div>
                @endif

                <p class="text-white-50 mb-3" style="font-size:0.86rem; line-height:1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $event->description }}
                </p>

                {{-- Event Meta --}}
                <div class="event-meta-list">
                    <div class="event-meta-item">
                        <i class="far fa-calendar-alt text-warning"></i>
                        <span>{{ $event->start_time->format('Y-m-d') }} ({{ $event->start_time->locale('ar')->translatedFormat('l') }})</span>
                    </div>
                    <div class="event-meta-item">
                        <i class="far fa-clock text-info"></i>
                        <span>{{ $event->start_time->format('H:i') }} — {{ $event->end_time->format('H:i') }}</span>
                    </div>
                    <div class="event-meta-item">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span>{{ $event->location ?: 'المدرج الرئيسي' }}</span>
                    </div>
                    @if($event->target_audience)
                    <div class="event-meta-item">
                        <i class="fas fa-users text-primary"></i>
                        <span class="text-truncate">{{ $event->target_audience }}</span>
                    </div>
                    @endif
                </div>

                {{-- Capacity Progress --}}
                @if($event->capacity)
                <div class="capacity-bar-wrap">
                    <div class="d-flex justify-content-between align-items-center text-white-50 small">
                        <span>المقاعد المحجوزة</span>
                        <span class="fw-bold text-white">{{ $attendeesCount }} / {{ $event->capacity }}</span>
                    </div>
                    <div class="capacity-bar-track">
                        <div class="capacity-bar-fill" style="width: {{ $percent }}%;"></div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Card Actions --}}
            <div class="event-actions">
                @auth
                    @if(auth()->user()->role === 'graduate')
                        <form action="{{ route('job-fair.event.toggle', $event->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @if($isRegistered)
                                <button type="submit" class="btn-register-event registered w-100">
                                    <i class="fas fa-check-circle me-1"></i>أنت مسجل (إلغاء)
                                </button>
                            @elseif($event->is_full)
                                <button type="button" class="btn-register-event disabled w-100">
                                    <i class="fas fa-lock me-1"></i>اكتمل العدد
                                </button>
                            @else
                                <button type="submit" class="btn-register-event w-100">
                                    <i class="fas fa-user-plus me-1"></i>سجّل في الفعالية
                                </button>
                            @endif
                        </form>
                    @else
                        <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="btn-register-event flex-grow-1">
                            <i class="fas fa-info-circle me-1"></i>عرض التفاصيل
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-register-event flex-grow-1">
                        <i class="fas fa-sign-in-alt me-1"></i>سجّل للدخول واحجز مقعدك
                    </a>
                @endauth

                <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="btn-qr-link" title="صفحة الفعالية ورمز QR">
                    <i class="fas fa-qrcode"></i>
                </a>
            </div>

        </div>
        @empty
        <div class="col-12 text-center py-5 text-white-50">
            <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-white-50"></i>
            <p>لا توجد فعاليات علمية مضافة في هذا المعرض حالياً.</p>
        </div>
        @endforelse
    </div>

</main>

{{-- ══════════════════════════════════
     FOOTER
══════════════════════════════════ --}}
<footer class="page-footer">
    مكتب تدريب وتوظيف الخريجين — <a href="{{ route('home') }}">جامعة طرابلس</a>
    &nbsp;|&nbsp;
    {{ $fair ? $fair->title : 'معرض التوظيف 2026' }} — رحلة الجاهزية المهنية
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    // ── Floating particles
    const pContainer = document.getElementById('particles-container');
    if (pContainer) {
        for (let i = 0; i < 35; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = 2 + Math.random() * 4.5;
            p.style.cssText = `
                left:${Math.random() * 100}%;
                width:${size}px; height:${size}px;
                animation-duration:${9 + Math.random() * 14}s;
                animation-delay:${-Math.random() * 20}s;
                opacity:${0.35 + Math.random() * 0.55};
            `;
            pContainer.appendChild(p);
        }
    }

    // ── Top Nav Scroll
    window.addEventListener('scroll', function() {
        const nav = document.getElementById('topNav');
        if (nav) nav.classList.toggle('scrolled', window.scrollY > 30);
    });

    // ── Search & Filter Logic
    const searchInput = document.getElementById('eventSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    const filterChips = document.querySelectorAll('.filter-chip');
    const cards = document.querySelectorAll('.event-item-card');
    const visibleCountEl = document.getElementById('visibleCount');
    const emptyState = document.getElementById('emptyState');

    let currentType = 'all';

    function applyFilters() {
        const q = (searchInput.value || '').trim().toLowerCase();
        let count = 0;

        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const type = card.getAttribute('data-type') || '';
            const speaker = card.getAttribute('data-speaker') || '';
            const location = card.getAttribute('data-location') || '';
            const topics = card.getAttribute('data-topics') || '';

            const matchesType = (currentType === 'all' || type === currentType);
            const matchesQuery = !q || title.includes(q) || speaker.includes(q) || location.includes(q) || topics.includes(q);

            if (matchesType && matchesQuery) {
                card.style.display = 'flex';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCountEl) visibleCountEl.textContent = count;
        if (emptyState) emptyState.style.display = (count === 0 ? 'block' : 'none');
        if (clearBtn) clearBtn.style.display = (q.length > 0 ? 'block' : 'none');
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            applyFilters();
        });
    }

    filterChips.forEach(chip => {
        chip.addEventListener('click', function() {
            filterChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            currentType = this.getAttribute('data-type') || 'all';
            applyFilters();
        });
    });
})();
</script>
</body>
</html>
