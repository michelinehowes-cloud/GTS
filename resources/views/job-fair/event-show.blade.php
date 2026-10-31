<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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
        /* ══════════════════════════════════════════════════════════
           UIVERSE.IO by adamgiebl: Dynamic Bottom-Up Fill Buttons
        ══════════════════════════════════════════════════════════ */
        .nav-btn {
            position: relative;
            padding: 8px 18px;
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            background: transparent;
            transition: ease-out 0.5s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1.4;
            border: 2px solid transparent;
            box-shadow: inset 0 0 0 0 transparent;
            outline: none;
        }

        .nav-btn:active {
            transform: scale(0.9) !important;
        }

        .nav-btn i {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .nav-btn:hover i {
            transform: scale(1.15);
        }

        /* 1. تسجيل الدخول */
        .nav-btn-login {
            color: #38BDF8 !important;
            border: 2px solid #38BDF8 !important;
            box-shadow: inset 0 0 0 0 #38BDF8 !important;
            background: transparent !important;
        }

        .nav-btn-login:hover {
            color: #042444 !important;
            border-color: #38BDF8 !important;
            box-shadow: inset 0 -100px 0 0 #38BDF8 !important;
        }

        /* 2. سجّل كخريج */
        .nav-btn-gold {
            color: #EECA3E !important;
            border: 2px solid #EECA3E !important;
            box-shadow: inset 0 0 0 0 #EECA3E !important;
            background: transparent !important;
        }

        .nav-btn-gold:hover {
            color: #042444 !important;
            border-color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #EECA3E !important;
        }

        /* 3. تسجيل زائر */
        .nav-btn-visitor {
            color: #10B981 !important;
            border: 2px solid #10B981 !important;
            box-shadow: inset 0 0 0 0 #10B981 !important;
            background: transparent !important;
        }

        .nav-btn-visitor:hover {
            color: #ffffff !important;
            border-color: #10B981 !important;
            box-shadow: inset 0 -100px 0 0 #10B981 !important;
        }

        .nav-btn-outline {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.28);
            color: white;
            backdrop-filter: blur(8px);
            box-shadow: inset 0 0 0 0 #043C73;
        }

        .nav-btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
            box-shadow: inset 0 -100px 0 0 #043C73;
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
        .badge-type-panel,
        .badge-type-panel_discussion {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(16,185,129,0.32);
        }
        .badge-type-keynote {
            background: linear-gradient(135deg, #eeca3e, #f59e0b);
            color: #061c38;
            box-shadow: 0 4px 15px rgba(238,202,62,0.35);
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
            font-size: 1.02rem;
            color: rgba(255, 255, 255, 0.88);
            max-width: 100%;
            line-height: 1.75;
            margin: 0;
        }

        .hero-panelists-inline {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-right: 6px;
        }

        .hero-panelist-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(238, 202, 62, 0.32);
            border-radius: 50px;
            padding: 3px 12px;
            font-size: 0.86rem;
            color: #ffffff;
        }

        .hero-panelist-chip .chip-role {
            font-size: 0.73rem;
            color: var(--gold-lt);
            font-weight: 700;
        }

        /* ══════════════════════════════════════════════════════════
           TOP WIDE SPEAKERS & PANELISTS SHOWCASE BANNER (~1/3 PAGE)
        ══════════════════════════════════════════════════════════ */
        .speakers-showcase-banner {
            position: relative;
            background: linear-gradient(145deg, rgba(6, 26, 56, 0.92) 0%, rgba(8, 42, 86, 0.86) 55%, rgba(4, 72, 138, 0.78) 100%);
            border: 1.5px solid rgba(238, 202, 62, 0.42);
            border-radius: 26px;
            padding: 28px 30px 30px;
            margin-bottom: 32px;
            box-shadow: 0 22px 50px rgba(0, 0, 0, 0.42), 0 0 35px rgba(238, 202, 62, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .speakers-showcase-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent 0%, var(--gold) 25%, #38bdf8 50%, var(--gold) 75%, transparent 100%);
            opacity: 0.9;
        }

        .speakers-banner-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .speakers-banner-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .speakers-banner-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, rgba(238, 202, 62, 0.25), rgba(14, 165, 233, 0.25));
            border: 1.5px solid rgba(238, 202, 62, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 1.15rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        .speakers-banner-title {
            font-size: 1.3rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
        }

        .speakers-banner-subtitle {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.68);
            margin: 2px 0 0;
        }

        .speakers-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(238, 202, 62, 0.16);
            border: 1px solid rgba(238, 202, 62, 0.45);
            color: var(--gold-lt);
            font-size: 0.84rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 50px;
        }

        .speakers-showcase-grid {
            display: grid;
            gap: 18px;
            align-items: stretch;
        }

        .speakers-showcase-grid.grid-count-1 {
            grid-template-columns: 1fr;
        }
        .speakers-showcase-grid.grid-count-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .speakers-showcase-grid.grid-count-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .speakers-showcase-grid.grid-count-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        .speakers-showcase-grid.grid-count-5 {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 15px;
        }
        .speakers-showcase-grid.grid-count-many {
            grid-template-columns: repeat(auto-fit, minmax(215px, 1fr));
        }

        .panelist-showcase-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.07) 0%, rgba(4, 24, 52, 0.72) 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 20px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .panelist-showcase-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.45), 0 0 22px rgba(238, 202, 62, 0.22);
        }

        .panelist-showcase-card.is-moderator {
            border-color: rgba(238, 202, 62, 0.65);
            background: linear-gradient(180deg, rgba(238, 202, 62, 0.12) 0%, rgba(6, 28, 60, 0.82) 100%);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35), 0 0 20px rgba(238, 202, 62, 0.16);
        }

        /* Single Speaker Panoramic Horizontal Card */
        .speakers-showcase-grid.grid-count-1 .panelist-showcase-card {
            flex-direction: row;
            align-items: center;
            gap: 28px;
            padding: 20px 24px;
        }

        .panelist-portrait-stage {
            width: 100%;
            aspect-ratio: 4 / 4.3;
            max-height: 265px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: radial-gradient(circle at 50% 25%, rgba(56, 189, 248, 0.22) 0%, rgba(8, 34, 69, 0.92) 70%, rgba(2, 16, 36, 0.98) 100%);
            border: 2px solid rgba(238, 202, 62, 0.55);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45);
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .speakers-showcase-grid.grid-count-1 .panelist-portrait-stage {
            width: 235px;
            height: 255px;
            max-height: none;
            aspect-ratio: auto;
            margin-bottom: 0;
        }

        .speakers-showcase-grid.grid-count-5 .panelist-portrait-stage {
            aspect-ratio: 4 / 4.25;
            max-height: 230px;
        }

        .panelist-portrait-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 16%;
            display: block;
            transition: transform 0.4s ease;
        }

        .panelist-showcase-card:hover .panelist-portrait-img {
            transform: scale(1.04);
        }

        .panelist-portrait-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--gold);
            background: linear-gradient(145deg, rgba(4, 93, 176, 0.35), rgba(2, 20, 43, 0.92));
        }

        .panelist-portrait-fallback i {
            font-size: 3.4rem;
            filter: drop-shadow(0 4px 12px rgba(238, 202, 62, 0.35));
        }

        .panelist-role-pill {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 50px;
            font-size: 0.74rem;
            font-weight: 800;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.45);
        }

        .panelist-role-pill.role-moderator {
            background: linear-gradient(135deg, #eeca3e 0%, #f59e0b 100%);
            color: #051b37;
            border: 1px solid rgba(255, 255, 255, 0.45);
        }

        .panelist-role-pill.role-panelist {
            background: rgba(6, 28, 60, 0.86);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.55);
        }

        .panelist-role-pill.role-keynote {
            background: rgba(6, 28, 60, 0.88);
            color: var(--gold-lt);
            border: 1px solid rgba(238, 202, 62, 0.55);
        }

        .panelist-card-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            text-align: center;
        }

        .speakers-showcase-grid.grid-count-1 .panelist-card-body {
            text-align: right;
        }

        .panelist-name {
            font-size: 1.08rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0 0 5px;
            line-height: 1.35;
        }

        .speakers-showcase-grid.grid-count-1 .panelist-name {
            font-size: 1.45rem;
            margin-bottom: 8px;
        }

        .speakers-showcase-grid.grid-count-5 .panelist-name {
            font-size: 0.98rem;
        }

        .panelist-job-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gold-lt);
            margin-bottom: 8px;
            line-height: 1.45;
        }

        .speakers-showcase-grid.grid-count-1 .panelist-job-title {
            font-size: 0.98rem;
            margin-bottom: 12px;
        }

        .panelist-bio {
            font-size: 0.8rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.82);
            margin: 0;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.09);
        }

        .speakers-showcase-grid.grid-count-1 .panelist-bio {
            font-size: 0.94rem;
            line-height: 1.8;
            padding-top: 12px;
        }

        .sidebar-box-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 6px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        }

        .sidebar-box-header-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(238, 202, 62, 0.18);
            border: 1px solid rgba(238, 202, 62, 0.45);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sidebar-box-header h3 {
            font-size: 1.08rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
        }

        .sidebar-box-header span {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.7);
            display: block;
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
            padding: 24px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            margin-bottom: 24px;
        }

        /* ── Sidebar Speaker Spotlight Box ── */
        .sidebar-speaker-spotlight {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 18px;
        }

        .sidebar-speaker-portrait-wrap {
            width: 100%;
            height: 330px;
            border-radius: 18px;
            overflow: hidden;
            border: 2.5px solid var(--gold);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.65), 0 0 24px rgba(238, 202, 62, 0.35);
            background: rgba(2, 20, 43, 0.85);
            margin-bottom: 16px;
            position: relative;
        }

        .sidebar-speaker-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 20%;
            display: block;
            transition: transform 0.4s ease;
        }

        .sidebar-speaker-portrait-wrap:hover .sidebar-speaker-img {
            transform: scale(1.04);
        }

        .sidebar-speaker-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4.5rem;
            color: var(--gold);
            background: linear-gradient(135deg, #043c73, #02142b);
        }

        .sidebar-speaker-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.45);
            color: var(--gold);
            font-size: 0.82rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .sidebar-speaker-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 4px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
        }

        .sidebar-speaker-role {
            font-size: 0.88rem;
            color: #93c5fd;
            font-weight: 700;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .sidebar-speaker-session-banner {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.84rem;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.5;
            font-weight: 600;
            width: 100%;
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

        @media (max-width: 1199px) {
            .speakers-showcase-grid.grid-count-5 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .speakers-showcase-grid.grid-count-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991px) {
            .event-hero-card { padding: 24px 20px; }
            .event-hero-title { font-size: 1.55rem; }
            .speakers-showcase-banner { padding: 22px 18px; }
            .speakers-showcase-grid.grid-count-3,
            .speakers-showcase-grid.grid-count-4,
            .speakers-showcase-grid.grid-count-5 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .speakers-showcase-grid.grid-count-1 .panelist-showcase-card {
                flex-direction: column;
                text-align: center;
            }
            .speakers-showcase-grid.grid-count-1 .panelist-card-body {
                text-align: center;
            }
            .speaker-profile-card { flex-direction: column; align-items: center; text-align: center; }
            .speaker-name-badge { justify-content: center; }
            .speaker-job-title { justify-content: center; }
            .sidebar-sticky { position: static; }
        }

        @media (max-width: 575px) {
            .speakers-showcase-grid.grid-count-2,
            .speakers-showcase-grid.grid-count-3,
            .speakers-showcase-grid.grid-count-4,
            .speakers-showcase-grid.grid-count-5,
            .speakers-showcase-grid.grid-count-many {
                grid-template-columns: 1fr;
            }
            .panelist-portrait-stage {
                max-height: 290px;
            }
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
                    <a href="{{ route('job-fair.public.program', $fair->id) }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-arrow-right"></i>
                        <span>جدول الفعاليات</span>
                    </a>
                    <a href="{{ route('job-fair.public', $fair->id) }}" class="nav-btn nav-btn-outline">
                        <i class="fas fa-home"></i>
                        <span>الرئيسية للمعرض</span>
                    </a>
                @endif

                @auth
                    <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-login">
                        <i class="fas fa-th-large"></i>لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn nav-btn-login">تسجيل الدخول</a>
                    <a href="{{ route('graduate.register') }}" class="nav-btn nav-btn-gold">
                        <i class="fas fa-user-plus"></i>سجّل كخريج
                    </a>
                    @if($fair)
                        <a href="{{ route('job-fair.public', $fair->id) }}#visitor-register" class="nav-btn nav-btn-visitor">
                            <i class="fas fa-id-badge"></i>سجّل كزائر
                        </a>
                    @endif
                @endauth
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

                @php
                    $speakers = $event->speakers_list;
                    $speakersTotal = count($speakers);
                    $gridCountClass = match(true) {
                        $speakersTotal <= 1 => 'grid-count-1',
                        $speakersTotal === 2 => 'grid-count-2',
                        $speakersTotal === 3 => 'grid-count-3',
                        $speakersTotal === 4 => 'grid-count-4',
                        $speakersTotal === 5 => 'grid-count-5',
                        default => 'grid-count-many',
                    };
                @endphp

                @if($speakersTotal > 1)
                <div class="event-hero-subtitle">
                    <i class="fas fa-users text-warning me-1"></i>
                    <span>يشارك في الجلسة:</span>
                    <div class="hero-panelists-inline">
                        @foreach($speakers as $sp)
                            @if(!empty($sp['name']))
                            <span class="hero-panelist-chip">
                                <strong>{{ $sp['name'] }}</strong>
                                @if(!empty($sp['role']))
                                    <span class="chip-role">({{ $sp['role'] }})</span>
                                @endif
                            </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @elseif($speakersTotal === 1 && !empty($speakers[0]['name']))
                <p class="event-hero-subtitle">
                    <i class="fas fa-user-tie text-warning me-1"></i>
                    يقدمها: <strong>{{ $speakers[0]['name'] }}</strong>
                    @if(!empty($speakers[0]['title']))
                        ({{ $speakers[0]['title'] }})
                    @endif
                </p>
                @endif
            </div>

            <!-- Wide Top-of-Page Speakers & Panelists Showcase Banner (~1/3 of page) -->
            @if($speakersTotal > 0)
            <section class="speakers-showcase-banner" aria-label="المتحدثون والمحاورون">
                <div class="speakers-banner-header">
                    <div class="speakers-banner-title-wrap">
                        <div class="speakers-banner-icon">
                            <i class="fas {{ $speakersTotal > 1 ? 'fa-users' : 'fa-user-tie' }}"></i>
                        </div>
                        <div>
                            <h3 class="speakers-banner-title">
                                {{ $speakersTotal > 1 ? 'المتحدثون والمحاورون في الجلسة' : 'المتحدث ومقدم الفعالية' }}
                            </h3>
                            <p class="speakers-banner-subtitle">
                                {{ $speakersTotal > 1 ? 'نخبة من الخبراء والمتخصصين المشاركين في إثراء محاور الجلسة الحوارية' : 'نبذة تعريفية عن المتحدث والخبرة المهنية والأكاديمية' }}
                            </p>
                        </div>
                    </div>

                    @if($speakersTotal > 1)
                    <span class="speakers-count-badge">
                        <i class="fas fa-microphone-lines"></i>
                        <span>{{ $speakersTotal }} محاورين ومتحدثين</span>
                    </span>
                    @endif
                </div>

                <div class="speakers-showcase-grid {{ $gridCountClass }}">
                    @foreach($speakers as $sp)
                        @php
                            $roleText = $sp['role'] ?? '';
                            $isModerator = str_contains($roleText, 'مدير');
                            $rolePillClass = $isModerator
                                ? 'role-moderator'
                                : (str_contains($roleText, 'رئيسي') ? 'role-keynote' : 'role-panelist');
                            $roleIcon = $isModerator
                                ? 'fas fa-microphone-lines'
                                : (str_contains($roleText, 'رئيسي') ? 'fas fa-star' : 'fas fa-user-tie');
                        @endphp
                        <article class="panelist-showcase-card {{ $isModerator ? 'is-moderator' : '' }}">
                            <div class="panelist-portrait-stage">
                                @if(!empty($roleText))
                                <span class="panelist-role-pill {{ $rolePillClass }}">
                                    <i class="{{ $roleIcon }}"></i>
                                    <span>{{ $roleText }}</span>
                                </span>
                                @endif

                                @if(!empty($sp['image_url']))
                                    <img src="{{ $sp['image_url'] }}" alt="{{ $sp['name'] ?: 'متحدث الجلسة' }}" class="panelist-portrait-img">
                                @else
                                    <div class="panelist-portrait-fallback">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="panelist-card-body">
                                <h4 class="panelist-name">{{ $sp['name'] ?: 'متحدث مشارك' }}</h4>

                                @if(!empty($sp['title']))
                                    <div class="panelist-job-title">
                                        <i class="fas fa-briefcase me-1" style="font-size: 0.75rem; opacity: 0.85;"></i>
                                        <span>{{ $sp['title'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($sp['bio']))
                                    <p class="panelist-bio">{{ $sp['bio'] }}</p>
                                @elseif($speakersTotal === 1)
                                    <p class="panelist-bio">مقدم فعالية «{{ $event->title }}» ضمن البرنامج العلمي المصاحب لمعرض التوظيف بجامعة طرابلس.</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
            @endif

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

                        <!-- Event Schedule & Registration Box -->
                        <div class="reg-action-box">
                            <div class="sidebar-box-header">
                                <div class="sidebar-box-header-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <h3>تفاصيل الموعد والحجز</h3>
                                    <span>بيانات الحضور والتسجيل الرسمي</span>
                                </div>
                            </div>

                            @if($event->capacity)
                                @php
                                    $bookedCount = $event->attendees_count;
                                    $fillPct = min(100, round(($bookedCount / max(1, $event->capacity)) * 100));
                                @endphp
                                <div class="capacity-bar-wrap mt-3 mb-1">
                                    <div class="capacity-header">
                                        <span>المقاعد المحجوزة</span>
                                        <span class="capacity-number">{{ $bookedCount }} / {{ $event->capacity }} مقعد</span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $fillPct }}%;"></div>
                                    </div>
                                </div>
                            @endif

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
                                            {{ $event->timing_text ?: 'يحدد لاحقاً وفق جدول المعرض' }}
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

                            <!-- Graduate Event Registration Action -->
                            @auth
                                @if(auth()->user()->role === 'graduate')
                                    <div class="mt-4 pt-3 border-top border-white border-opacity-15">
                                        <form action="{{ route('job-fair.event.toggle', $event->id) }}" method="POST">
                                            @csrf
                                            @if($isRegistered)
                                                <div class="p-2.5 rounded-3 mb-2 text-center" style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.45);">
                                                    <i class="fas fa-check-circle text-success fs-5 me-1"></i>
                                                    <strong class="text-white">أنت مسجل في هذه الفعالية</strong>
                                                </div>
                                                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold" onclick="return confirm('هل تريد بالتأكيد إلغاء التسجيل في هذه الفعالية؟');">
                                                    <i class="fas fa-times me-1"></i> إلغاء التسجيل
                                                </button>
                                            @else
                                                @if($event->is_full)
                                                    <button type="button" class="btn btn-secondary w-100 rounded-pill py-2 fw-bold" disabled>
                                                        <i class="fas fa-ban me-1"></i> المقاعد مكتملة
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-bold shadow">
                                                        <i class="fas fa-user-plus me-1"></i> سجّل حضورك في هذه الفعالية الآن
                                                    </button>
                                                @endif
                                            @endif
                                        </form>
                                    </div>
                                @endif
                            @else
                                <div class="mt-4 pt-3 border-top border-white border-opacity-15 text-center">
                                    <a href="{{ route('login') }}" class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-bold shadow">
                                        <i class="fas fa-sign-in-alt me-1"></i> سجل دخولك كخريج لحجز مقعدك
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- Dynamic QR Code Card -->
                        <div class="qr-card-wrap">
                            <h4 style="font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 8px;">
                                <i class="fas fa-qrcode text-warning me-1"></i>
                                رمز الاستجابة السريعة (QR Code)
                            </h4>
                            <p class="qr-caption">
                                امسح الرمز من هاتفك للاطلاع المباشر على تفاصيل الفعالية ومشاركتها عبر وسائل التواصل
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
                                <i class="fas {{ count($rel->speakers_list) > 1 ? 'fa-users' : 'fa-user-circle' }} text-warning"></i>
                                <span>{{ $rel->speakers_names_summary ?: 'نخبة من المحاورين والمدربين' }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Official Unified Sponsors Strip -->
        @include('job-fair.partials.footer-sponsors-strip', ['fair' => $fair, 'sponsors' => $sponsors ?? null, 'companies' => $companies ?? null])

        <!-- Official Footer -->
        <footer class="event-footer">
            <div class="container text-center">
                <p class="mb-1" style="font-weight: 700; color: #ffffff;">
                    مكتب تدريب الخريجين — جامعة طرابلس &bull; الراعي الاستراتيجي: <span class="text-warning">شركة الواحة لتنظيم المعارض والمؤتمرات</span>
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
