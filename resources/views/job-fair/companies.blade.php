<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>دليل الشركات والمؤسسات المشاركة — {{ $fair ? $fair->title : 'معرض التوظيف 2026' }}</title>
    <meta name="description"
        content="دليل الشركات والمؤسسات المشاركة في {{ $fair ? $fair->title : 'معرض التوظيف' }} — جامعة طرابلس. استكشف الأجنحة والشواغر المتاحة.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --gold: #eeca3e;
            --gold-lt: #FDE68A;
            --navy: #045db0;
            /* University Primary Blue */
            --navy-md: #3b82f6;
            /* University Light Blue */
            --navy-lt: #60a5fa;
            --navy-dark: #022c5e;
            --teal: #0EA5E9;
            --green: #10B981;
            --white: #FFFFFF;
        }

        html {
            scroll-behavior: smooth;
        }

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
           (Identical to Approved Job Fair Brand)
        ══════════════════════════════════ */
        .hero-bg-layer {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 40%, rgba(245, 158, 11, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 20%, rgba(14, 165, 233, 0.22) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16, 185, 129, 0.12) 0%, transparent 50%),
                linear-gradient(160deg, #045db0 0%, #03488a 50%, #092347 100%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            pointer-events: none;
            animation: orb-float 8s ease-in-out infinite;
            z-index: 0;
        }

        .orb-1 {
            width: 450px;
            height: 450px;
            background: rgba(245, 158, 11, 0.14);
            top: -80px;
            right: -80px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 380px;
            height: 380px;
            background: rgba(14, 165, 233, 0.16);
            bottom: 5%;
            left: -80px;
            animation-delay: -3s;
        }

        .orb-3 {
            width: 280px;
            height: 280px;
            background: rgba(16, 185, 129, 0.12);
            top: 35%;
            left: 45%;
            animation-delay: -5s;
        }

        @keyframes orb-float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.06);
            }
        }

        /* Glowing floating embers / particles */
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
            0% {
                transform: translateY(110vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 0.7;
            }

            100% {
                transform: translateY(-10vh) scale(1.3);
                opacity: 0;
            }
        }

        /* ══════════════════════════════════
           TOP NAV (Exact Fair Identity)
        ══════════════════════════════════ */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
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
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid var(--gold);
            object-fit: cover;
        }

        .nav-brand img.jf-logo {
            width: auto;
            height: 38px;
            border: none;
            border-radius: 0;
            margin-right: 15px;
            object-fit: contain;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
        }

        .nav-brand-text {
            line-height: 1.2;
        }

        .nav-brand-text .main {
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .nav-brand-text .sub {
            color: var(--gold);
            font-size: 0.72rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .nav-btn {
            padding: 8px 20px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
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
            transform: translateY(-1px);
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

        .nav-btn-fair-back {
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(238, 202, 62, 0.6);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-btn-fair-back:hover {
            background: linear-gradient(135deg, var(--gold), #f59e0b);
            border-color: var(--gold);
            color: #071933;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 202, 62, 0.45);
        }

        .nav-btn-emerald {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white !important;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }
        .nav-btn-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(16, 185, 129, 0.5);
            color: white !important;
        }

        /* ── Strategic Sponsor Nav Logo ── */
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

        /* ══════════════════════════════════
           HERO HEADER SECTION
        ══════════════════════════════════ */
        .directory-hero {
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .hero-eyebrow .dot {
            width: 6px;
            height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.25
            }
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 55%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: clamp(1rem, 2vw, 1.2rem);
            margin: 0 auto 2.4rem;
            font-weight: 400;
            max-width: 720px;
            line-height: 1.75;
        }

        /* ══════════════════════════════════
           STATS ROW (Exact to Screenshot)
        ══════════════════════════════════ */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 2.2rem;
            flex-wrap: wrap;
            background: rgba(10, 38, 77, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 1.3rem 2.5rem;
            margin: 0 auto 2.8rem;
            max-width: 960px;
            backdrop-filter: blur(16px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
        }

        .hero-stat {
            text-align: center;
            min-width: 140px;
        }

        .hero-stat-num {
            font-size: 2.3rem;
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
            font-size: 0.85rem;
            margin-top: 5px;
            font-weight: 600;
        }

        .hero-stat-divider {
            width: 1px;
            background: rgba(255, 255, 255, 0.14);
            align-self: stretch;
        }

        /* ══════════════════════════════════
           CONTROLS PANEL (Search & Filters)
        ══════════════════════════════════ */
        .controls-panel {
            background: rgba(8, 32, 66, 0.6);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 26px;
            padding: 1.6rem 2rem;
            backdrop-filter: blur(18px);
            margin-bottom: 2rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.28);
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input-wrap i.search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
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
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }

        .search-box:focus {
            border-color: var(--gold);
            background: rgba(4, 23, 50, 0.95);
            box-shadow: 0 0 0 4px rgba(238, 202, 62, 0.25), inset 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .search-clear-btn {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #cbd5e1;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 4px;
            display: none;
            transition: color 0.2s;
        }

        .search-clear-btn:hover {
            color: #f87171;
        }

        /* Filter Chips */
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
            padding: 7px 16px;
            border-radius: 50px;
            font-size: 0.84rem;
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
            transform: translateY(-1px);
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

        /* Results Counter Header */
        .results-info-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 0 0.5rem;
        }

        .results-count-text {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
        }

        .results-count-text strong {
            color: var(--gold);
            font-size: 1.1rem;
        }

        /* ══════════════════════════════════
           COMPANIES CARDS GRID (Glassmorphism)
        ══════════════════════════════════ */
        .companies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(295px, 1fr));
            gap: 1.6rem;
            margin-bottom: 5rem;
        }

        .company-card {
            background: rgba(8, 34, 69, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 1.6rem;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            backdrop-filter: blur(16px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .company-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            background: rgba(12, 45, 92, 0.85);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4), 0 0 30px rgba(238, 202, 62, 0.25);
        }

        .card-top-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.2rem;
            gap: 0.6rem;
        }

        .company-logo-frame {
            width: 82px;
            height: 82px;
            background: #ffffff;
            border-radius: 20px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
            transition: transform 0.3s;
        }

        .company-card:hover .company-logo-frame {
            transform: scale(1.06);
        }

        .company-logo-frame img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .company-logo-frame .logo-fallback {
            font-size: 2.2rem;
            color: var(--navy);
        }

        .card-badges-col {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .booth-tag {
            background: linear-gradient(135deg, #045db0, #2563eb);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.5px;
        }

        .jobs-tag {
            font-size: 0.74rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .jobs-tag.tag-available {
            background: rgba(16, 185, 129, 0.25);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.5);
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.25);
        }

        .jobs-tag.tag-intro {
            background: rgba(14, 165, 233, 0.22);
            color: #bae6fd;
            border: 1px solid rgba(14, 165, 233, 0.4);
        }

        .company-card-body {
            flex-grow: 1;
        }

        .company-card-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.4rem;
            line-height: 1.35;
        }

        .company-card-industry {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 0.85rem;
        }

        .company-card-desc {
            font-size: 0.86rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.65;
            margin-bottom: 1.3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.8rem;
        }

        .card-actions-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-view-company {
            flex-grow: 1;
            background: rgba(255, 255, 255, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 0.88rem;
            font-weight: 700;
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

        .btn-view-company:hover {
            background: linear-gradient(135deg, var(--gold), #F59E0B);
            border-color: var(--gold);
            color: #071933;
            box-shadow: 0 4px 18px rgba(238, 202, 62, 0.4);
            transform: translateY(-2px);
        }

        .btn-web-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-web-link:hover {
            background: rgba(238, 202, 62, 0.3);
            border-color: var(--gold);
            color: var(--gold);
            transform: translateY(-2px);
        }

        /* ══════════════════════════════════
           EMPTY SEARCH RESULTS STATE
        ══════════════════════════════════ */
        .empty-results-box {
            text-align: center;
            padding: 4.5rem 1.5rem;
            background: rgba(8, 32, 66, 0.65);
            border: 1.5px dashed rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            margin: 2rem 0 4rem;
            display: none;
            backdrop-filter: blur(16px);
        }

        .empty-results-icon {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.15);
            color: var(--gold);
            font-size: 2.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.3rem;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        /* ══════════════════════════════════
           COMPANY DETAIL POPUP MODAL
        ══════════════════════════════════ */
        .company-modal-dialog {
            max-width: 580px;
        }

        .company-modal-content {
            background: linear-gradient(160deg, #052c5c 0%, #031c3b 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.18);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.7);
            color: #ffffff;
        }

        .company-modal-header {
            background: linear-gradient(135deg, rgba(4, 93, 176, 0.5) 0%, rgba(3, 30, 64, 0.95) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 2.2rem 2rem 1.6rem;
            position: relative;
        }

        .company-modal-logo-wrapper {
            width: 105px;
            height: 105px;
            background: #ffffff;
            border-radius: 24px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.3rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .company-modal-logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .company-modal-badge {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .company-modal-body {
            padding: 1.8rem 2rem 2rem;
        }

        .company-info-chip {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .company-info-chip .icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* ══════════════════════════════════
           SLIM FOOTER & SPONSORS STRIP — GLASSMORPHISM LOGOS
        ══════════════════════════════════ */
        /* Slim Footer Sponsors Strip — Blue Glassmorphism (Fully Responsive) */
        .footer-sponsors-strip {
            background: linear-gradient(90deg, #021A3E 0%, #033566 40%, #042952 100%);
            border-top: 2px solid rgba(56,189,248,0.5);
            padding: 0;
            position: relative;
            z-index: 10;
            overflow: hidden;
            box-shadow: 0 -4px 20px rgba(2, 26, 62, 0.4);
            width: 100%;
            max-width: 100vw;
        }
        .footer-sponsors-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(90deg, rgba(56,189,248,0.04) 0 1px, transparent 1px 60px);
            pointer-events: none;
        }
        .footer-sponsors-strip::after {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 180px;
            background: linear-gradient(90deg, rgba(3,53,102,1) 0%, transparent 100%);
            pointer-events: none;
            z-index: 2;
        }
        .strip-inner-row {
            height: 58px;
            width: 100%;
            display: flex;
            align-items: center;
        }
        .strip-featured-logos {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            flex-shrink: 0;
            position: relative;
            z-index: 3;
            height: 100%;
            border-left: 1px solid rgba(56,189,248,0.3);
            border-right: 1px solid rgba(56,189,248,0.3);
            background: linear-gradient(90deg, rgba(3,53,102,0.95) 0%, rgba(3,40,80,0.6) 100%);
        }
        .strip-office-logo {
            height: 36px;
            width: auto;
            object-fit: contain;
            opacity: 0.95;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
        }
        .strip-divider-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(56,189,248,0.5);
        }
        .strip-wahaexpo-logo {
            height: 28px;
            width: auto;
            object-fit: contain;
            opacity: 0.92;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
        }
        .sponsors-strip-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(56,189,248,0.9);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            white-space: nowrap;
            padding: 0 14px;
            flex-shrink: 0;
            border-left: 1px solid rgba(56,189,248,0.25);
            border-right: 1px solid rgba(56,189,248,0.25);
            position: relative;
            z-index: 3;
            display: flex;
            align-items: center;
            height: 100%;
        }
        .sponsors-ticker-wrap {
            overflow: hidden;
            width: 100%;
            min-width: 0;
            flex-grow: 1;
            mask-image: linear-gradient(to right, transparent 0%, black 4%, black 96%, transparent 100%);
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 4%, black 96%, transparent 100%);
        }
        .sponsors-ticker-track {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            width: max-content;
            animation: sponsorScroll 32s linear infinite;
            padding: 10px 0;
        }
        .sponsors-ticker-track:hover { animation-play-state: paused; }
        @keyframes sponsorScroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .sponsor-glass-card {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(56,189,248,0.22);
            border-radius: 12px;
            padding: 6px 16px 6px 10px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 12px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.08);
            transition: all 0.25s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .sponsor-glass-card.is-link { cursor: pointer; }
        .sponsor-glass-card.is-link:hover {
            background: rgba(56,189,248,0.14);
            border-color: rgba(56,189,248,0.55);
            box-shadow: 0 4px 18px rgba(56,189,248,0.2), inset 0 1px 0 rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }
        .sponsor-glass-logo {
            width: 38px;
            height: 32px;
            background: rgba(255,255,255,0.92);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .sponsor-glass-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
        }
        .sponsor-glass-logo .sponsor-icon-fb {
            font-size: 0.95rem;
            color: #033566;
        }
        .sponsor-glass-name {
            font-size: 0.74rem;
            font-weight: 700;
            color: rgba(255,255,255,0.88);
            letter-spacing: 0.01em;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sponsor-glass-card.is-link:hover .sponsor-glass-name { color: #38BDF8; }
        .sponsor-ticker-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(56,189,248,0.3);
            flex-shrink: 0;
        }

        /* ── Mobile Responsive for Sponsors Strip ── */
        @media (max-width: 768px) {
            .strip-inner-row {
                height: 48px !important;
            }
            .footer-sponsors-strip::after {
                width: 45px;
                opacity: 0.7;
            }
            .strip-featured-logos {
                padding: 0 10px;
                gap: 8px;
            }
            .strip-office-logo {
                height: 26px;
            }
            .strip-divider-dot {
                width: 3px;
                height: 3px;
            }
            .strip-wahaexpo-logo {
                height: 20px;
            }
            .sponsors-strip-label {
                padding: 0 8px;
                font-size: 0.6rem;
            }
            .sponsor-glass-card {
                padding: 4px 10px 4px 7px;
                gap: 6px;
                border-radius: 9px;
            }
            .sponsor-glass-logo {
                width: 30px;
                height: 25px;
                border-radius: 6px;
            }
            .sponsor-glass-logo img {
                padding: 2.5px;
            }
            .sponsor-glass-name {
                font-size: 0.68rem;
                max-width: 90px;
            }
            .sponsors-ticker-track {
                gap: 0.65rem;
                padding: 6px 0;
                animation-duration: 25s;
            }
        }

        @media (max-width: 480px) {
            .strip-inner-row {
                height: 44px !important;
            }
            .footer-sponsors-strip::after {
                display: none;
            }
            .strip-featured-logos {
                padding: 0 7px;
                gap: 5px;
            }
            .strip-office-logo {
                height: 22px;
            }
            .strip-divider-dot {
                width: 2.5px;
                height: 2.5px;
            }
            .strip-wahaexpo-logo {
                height: 17px;
            }
            .sponsors-strip-label {
                padding: 0 6px;
                font-size: 0.58rem;
            }
            .strip-label-text {
                display: none;
            }
            .sponsor-glass-card {
                padding: 3px 8px 3px 5px;
                gap: 5px;
                border-radius: 8px;
            }
            .sponsor-glass-logo {
                width: 26px;
                height: 22px;
                border-radius: 5px;
            }
            .sponsor-glass-logo .sponsor-icon-fb {
                font-size: 0.75rem;
            }
            .sponsor-glass-logo img {
                padding: 2px;
            }
            .sponsor-glass-name {
                font-size: 0.64rem;
                max-width: 75px;
            }
            .sponsors-ticker-track {
                gap: 0.5rem;
                padding: 4px 0;
            }
        }

@media (max-width: 991px) {
            .hero-stats {
                gap: 1.5rem;
                padding: 1.2rem 1.5rem;
            }

            .hero-stat {
                min-width: 110px;
            }

            .hero-stat-num {
                font-size: 1.9rem;
            }
        }

        @media (max-width: 768px) {
            .top-nav {
                padding: 0.65rem 1rem;
            }

            .nav-brand-text {
                display: none;
            }

            .directory-hero {
                padding: 105px 0 25px;
            }

            .hero-stats {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            .hero-stat-divider {
                display: none;
            }

            .controls-panel {
                padding: 1.2rem;
            }

            .companies-grid {
                grid-template-columns: 1fr;
            }
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

    {{-- FLOATING PARTICLES (EMBERS) CONTAINER --}}
    <div id="particles-container"></div>

    {{-- ══════════════════════════════════
    TOP NAV
    ══════════════════════════════════ --}}
    <nav class="top-nav" id="topNav">
        <a href="{{ $fair ? route('job-fair.public', $fair->id) : route('home') }}" class="nav-brand">
            <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="main-logo"
                onerror="this.style.display='none'">
            <div class="nav-brand-text">
                <div class="main">مكتب تدريب الخريجين</div>
                <div class="sub">جامعة طرابلس</div>
            </div>
            @if($fair)
                <img src="{{ $fair->white_logo_url }}" class="jf-logo" alt="{{ $fair->title }}"
                    onerror="this.onerror=null;this.src='{{ $fair->horizontal_logo_url }}';">
            @else
                <img src="{{ asset('images/job_fair_logo_white.png') }}" class="jf-logo" alt="شعار الفعالية"
                    onerror="this.style.display='none'">
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
                <a href="{{ route('job-fair.public.projects', $fair->id) }}" class="nav-btn nav-btn-outline">
                    <i class="fas fa-lightbulb"></i>مشاريع التخرج
                </a>
                <a href="{{ route('job-fair.public.program', $fair->id) }}" class="nav-btn nav-btn-outline">
                    <i class="fas fa-graduation-cap"></i>البرنامج العلمي
                </a>
            @endif
        </div>
    </nav>

    {{-- ══════════════════════════════════
    HERO & DIRECTORY HEADER
    ══════════════════════════════════ --}}
    <header class="directory-hero">
        <div class="container">

            <div class="hero-eyebrow">
                <span class="dot"></span>
                دليل الشركات والأجنحة المشاركة
                <span class="dot"></span>
            </div>

            <h1 class="hero-title">
                شركاء النجاح في {{ $fair ? $fair->title : 'معرض التوظيف 2026' }}
            </h1>

            <p class="hero-subtitle">
                استعرض الدليل الشامل لكافة الشركات والمؤسسات المشاركة، وتعرّف على أجنحتها، قطاعاتها، والفرص التدريبية
                والوظيفية الواعدة التي تقدمها لخريجي جامعة طرابلس.
            </p>

            {{-- STATS ROW (Matching Landing Page Signature Style) --}}
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $totalCompanies }}</div>
                    <div class="hero-stat-lbl">شركة ومؤسسة مشاركة</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $totalPositions > 0 ? $totalPositions : $totalCompanies }}</div>
                    <div class="hero-stat-lbl">فرصة وشاغر متاح</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $totalBooths > 0 ? $totalBooths : $totalCompanies }}</div>
                    <div class="hero-stat-lbl">جناح محجوز بالمعرض</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $industries->count() }}</div>
                    <div class="hero-stat-lbl">قطاعات وتخصصات</div>
                </div>
            </div>

        </div>
    </header>

    {{-- ══════════════════════════════════
    MAIN DIRECTORY CONTENT
    ══════════════════════════════════ --}}
    <main class="container position-relative z-10 mb-5">

        {{-- SEARCH & FILTERS CONTROLS PANEL --}}
        <div class="controls-panel">
            {{-- Live Search Input --}}
            <div class="search-input-wrap">
                <input type="text" id="companySearchInput" class="search-box"
                    placeholder="ابحث باسم الشركة، القطاع، أو رقم الجناح (مثال: تقنية، نفط، A1)..." autocomplete="off">
                <i class="fas fa-search search-icon"></i>
                <button type="button" id="clearSearchBtn" class="search-clear-btn" title="مسح البحث">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Filter Chips (Sectors) --}}
            @if($industries->count() > 0)
                <div class="filter-chips-container">
                    <span class="filter-label"><i class="fas fa-filter"></i>القطاع:</span>
                    <div class="filter-chip active" data-industry="all">
                        الكل <span class="chip-count">{{ $totalCompanies }}</span>
                    </div>
                    @foreach($industries as $ind)
                        @php
                            $indCount = $companies->filter(fn($c) => ($c->company->industry ?? '') === $ind)->count();
                        @endphp
                        <div class="filter-chip" data-industry="{{ $ind }}">
                            {{ $ind }} <span class="chip-count">{{ $indCount }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- RESULTS HEADER COUNTER --}}
        <div class="results-info-bar">
            <div class="results-count-text">
                يتم عرض <strong id="visibleCount">{{ $totalCompanies }}</strong> شركة من أصل {{ $totalCompanies }}
            </div>
            <div class="text-white-50" style="font-size: 0.86rem;">
                <i class="fas fa-info-circle me-1" style="color:var(--gold)"></i> اضغط على أي شركة لاستعراض كامل تفاصيل
                الجناح ووسائل التواصل
            </div>
        </div>

        {{-- EMPTY RESULTS STATE --}}
        <div class="empty-results-box" id="emptyResultsState">
            <div class="empty-results-icon">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3 class="fw-bold text-white mb-2" style="font-size: 1.35rem;">لم يتم العثور على شركات مطابقة</h3>
            <p class="text-white-50 mb-3" style="max-width: 480px; margin: 0 auto; font-size: 0.92rem;">
                جرب البحث بكلمات أخرى أو اختر قطاعاً مختلفاً لعرض الشركات المشاركة.
            </p>
            <button type="button" id="resetFiltersBtn" class="nav-btn nav-btn-outline px-4 py-2">
                <i class="fas fa-redo-alt me-1"></i>إعادة ضبط الفلاتر والبحث
            </button>
        </div>

        {{-- COMPANIES CARDS GRID --}}
        <div class="companies-grid" id="companiesGrid">
            @forelse($companies as $fc)
                @php
                    $company = $fc->company;
                    $companyLogo = $company && $company->logo_path ? Storage::url($company->logo_path) : ($company && $company->logo ? Storage::url($company->logo) : '');
                    $companyName = $company->name ?? 'شركة مشاركة';
                    $companyIndustry = $company->industry ?? 'قطاع الأعمال والخدمات';
                    $companyDesc = $company->description ?: ('جهة رائدة مشاركة في فعاليات ' . ($fair->title ?? 'معرض التوظيف') . ' لدعم وتوظيف الكفاءات الوطنية الشابة.');
                    $boothNumber = $fc->booth_number ? strtoupper(trim($fc->booth_number)) : '';
                    $availablePos = (int) ($fc->available_positions ?? 0);
                    if ($availablePos === 0 && $company && $company->jobOpportunities) {
                        $availablePos = $company->jobOpportunities->count();
                    }
                @endphp
                <div class="company-card company-item-card" data-name="{{ mb_strtolower($companyName) }}"
                    data-industry="{{ $companyIndustry }}" data-booth="{{ mb_strtolower($boothNumber) }}"
                    data-desc="{{ mb_strtolower($companyDesc) }}">

                    {{-- Top Row: Logo & Badges --}}
                    <div class="card-top-row">
                        <div class="company-logo-frame">
                            @if($companyLogo)
                                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" loading="lazy">
                            @else
                                <i class="fas fa-building logo-fallback"></i>
                            @endif
                        </div>

                        <div class="card-badges-col">
                            @if($boothNumber)
                                <span class="booth-tag">
                                    <i class="fas fa-map-marker-alt"></i>جناح {{ $boothNumber }}
                                </span>
                            @endif

                            @if($availablePos > 0)
                                @php
                                    $posText = ($availablePos == 1 ? 'فرصة واحدة' : ($availablePos == 2 ? 'فرصتان' : ($availablePos <= 10 ? $availablePos . ' فرص' : $availablePos . ' فرصة')));
                                @endphp
                                <span class="jobs-tag tag-available">
                                    <i class="fas fa-briefcase"></i>{{ $posText }}
                                </span>
                            @else
                                <span class="jobs-tag tag-intro">
                                    <i class="fas fa-handshake"></i>جناح تعريفي
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Body: Name, Industry, Snippet --}}
                    <div class="company-card-body">
                        <h3 class="company-card-title">{{ $companyName }}</h3>
                        <div class="company-card-industry">
                            <i class="fas fa-tag"></i>{{ $companyIndustry }}
                        </div>
                        <p class="company-card-desc">{{ $companyDesc }}</p>
                    </div>

                    {{-- Actions Row --}}
                    <div class="card-actions-row">
                        <button type="button" class="btn-view-company" data-bs-toggle="modal"
                            data-bs-target="#companyDetailModal" data-name="{{ $companyName }}"
                            data-logo="{{ $companyLogo }}" data-industry="{{ $companyIndustry }}"
                            data-booth="{{ $boothNumber }}" data-jobs="{{ $availablePos }}" data-desc="{{ $companyDesc }}"
                            data-website="{{ $company->website ?? '' }}" data-email="{{ $company->email ?? '' }}"
                            data-phone="{{ $company->phone ?? '' }}">
                            <i class="fas fa-info-circle"></i>تفاصيل الجناح والشركة
                        </button>

                        @if(!empty($company->website))
                            <a href="{{ Str::startsWith($company->website, 'http') ? $company->website : 'https://' . $company->website }}"
                                target="_blank" rel="noopener noreferrer" class="btn-web-link"
                                title="زيارة الموقع الإلكتروني الرسمي">
                                <i class="fas fa-globe"></i>
                            </a>
                        @endif
                    </div>

                </div>
            @empty
                <div class="col-12 text-center py-5 text-white-50">
                    <i class="fas fa-building fa-3x mb-3 text-white-50"></i>
                    <p>لا توجد شركات مؤكدة مسجلة في هذا المعرض حتى الآن.</p>
                </div>
            @endforelse
        </div>

    </main>

    {{-- ══════════════════════════════════
    COMPANY DETAIL POPUP MODAL
    ══════════════════════════════════ --}}
    <div class="modal fade" id="companyDetailModal" tabindex="-1" aria-labelledby="companyDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered company-modal-dialog">
            <div class="modal-content company-modal-content">
                <div class="company-modal-header text-center position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="company-modal-logo-wrapper" id="modalCompanyLogoBox">
                        <img id="modalCompanyLogo" src="" alt="شعار الشركة" style="display:none;">
                        <i id="modalCompanyLogoFallback" class="fas fa-building fa-2x"
                            style="color:var(--navy); display:none;"></i>
                    </div>

                    <h3 class="fw-bold mb-2 text-white" id="modalCompanyName">اسم الشركة</h3>

                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-1">
                        <span class="company-modal-badge"
                            style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.4);">
                            <i class="fas fa-tag me-1"></i>
                            <span id="modalCompanyIndustry">قطاع الأعمال</span>
                        </span>
                        <span class="company-modal-badge" id="modalCompanyBoothBadge"
                            style="background: rgba(245, 158, 11, 0.25); color: #fde047; border: 1px solid rgba(245, 158, 11, 0.45); display:none;">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <span id="modalCompanyBooth">جناح 1</span>
                        </span>
                        <span class="company-modal-badge" id="modalCompanyJobsBadge"
                            style="background: rgba(16, 185, 129, 0.25); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.45); display:none;">
                            <i class="fas fa-briefcase me-1"></i>
                            <span id="modalCompanyJobs">0</span> فرصة متاحة
                        </span>
                    </div>
                </div>

                <div class="company-modal-body">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2" style="font-size: 0.9rem; color: #fbbf24;">
                            <i class="fas fa-info-circle me-1"></i>نبذة تعريفية عن الشركة:
                        </h6>
                        <p class="lh-lg mb-0" id="modalCompanyDesc"
                            style="font-size: 0.95rem; color: #e2e8f0; white-space: pre-line;">
                            وصف الشركة
                        </p>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-4" id="modalCompanyContacts">
                        <div class="company-info-chip" id="modalWebsiteChip" style="display:none;">
                            <div class="icon" style="background:rgba(59, 130, 246, 0.25); color:#60a5fa;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-white-50 d-block" style="font-size:0.72rem">الموقع الإلكتروني
                                    الرسمي</small>
                                <a href="#" target="_blank" rel="noopener noreferrer" id="modalCompanyWebsite"
                                    class="text-white fw-semibold text-decoration-none" style="font-size:0.92rem"></a>
                            </div>
                        </div>

                        <div class="company-info-chip" id="modalEmailChip" style="display:none;">
                            <div class="icon" style="background:rgba(16, 185, 129, 0.25); color:#34d399;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-white-50 d-block" style="font-size:0.72rem">البريد الإلكتروني للتوظيف
                                    / التواصل</small>
                                <a href="#" id="modalCompanyEmail" class="text-white fw-semibold text-decoration-none"
                                    style="font-size:0.92rem"></a>
                            </div>
                        </div>

                        <div class="company-info-chip" id="modalPhoneChip" style="display:none;">
                            <div class="icon" style="background:rgba(245, 158, 11, 0.25); color:#fbbf24;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-white-50 d-block" style="font-size:0.72rem">هاتف الشركة</small>
                                <span id="modalCompanyPhone" class="text-white fw-semibold"
                                    style="font-size:0.92rem"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4"
                            data-bs-dismiss="modal">إغلاق</button>
                        @auth
                            @if(auth()->user()->role === 'graduate')
                                <a href="{{ route('graduate.job-opportunities.index') }}"
                                    class="nav-btn nav-btn-gold px-4 py-2 text-decoration-none">
                                    <i class="fas fa-briefcase me-1"></i>تصفح الفرص المتاحة
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Glassmorphism Blue Sponsors Strip -->
<div class="footer-sponsors-strip">
    <div class="strip-inner-row">
        <!-- Featured logos: office + wahaexpo -->
        <div class="strip-featured-logos">
            <img src="{{ asset('images/office_logo_white.png') }}"
                 alt="مكتب تدريب وتوظيف الخريجين"
                 class="strip-office-logo"
                 onerror="this.src='{{ asset('images/logo.jpg') }}'">
            <div class="strip-divider-dot"></div>
            <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}"
                 alt="شركة الواحة لتنظيم المعارض والمؤتمرات"
                 class="strip-wahaexpo-logo"
                 onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}'">
        </div>
        <!-- Label -->
        <div class="sponsors-strip-label">
            <i class="fas fa-star" style="color:#38BDF8;"></i>
            <span class="strip-label-text ms-1">رعاة المعرض</span>
        </div>
        <!-- Scrolling ticker -->
        <div class="sponsors-ticker-wrap">
            <div class="sponsors-ticker-track">
                @php
                    $footerSponsors = isset($sponsors) && $sponsors->count() > 0 ? $sponsors : collect([]);
                    $hasSponsors = $footerSponsors->count() > 0;
                    $placeholders = [
                        ["icon"=>"fa-broadcast-tower","name"=>"المدار الجديد"],
                        ["icon"=>"fa-signal","name"=>"ليبيانا"],
                        ["icon"=>"fa-network-wired","name"=>"LTT"],
                        ["icon"=>"fa-university","name"=>"مصرف التجارة"],
                        ["icon"=>"fa-oil-can","name"=>"الواحة للنفط"],
                        ["icon"=>"fa-landmark","name"=>"بنك الجمهورية"],
                        ["icon"=>"fa-globe","name"=>"شركة دولية"],
                        ["icon"=>"fa-industry","name"=>"مجموعة صناعية"],
                    ];
                    $items = $hasSponsors ? $footerSponsors->toArray() : $placeholders;
                    $allItems = array_merge((array)$items, (array)$items);
                @endphp
                @foreach($allItems as $idx => $item)
                    @if($hasSponsors)
                        @php $sp = (object)$item; @endphp
                        <div class="sponsor-glass-card{{ isset($sp->website) && $sp->website ? ' is-link' : '' }}"
                             @if(isset($sp->website) && $sp->website) onclick="window.open('{{ $sp->website }}','_blank')" @endif
                             title="{{ $sp->name }}">
                            <div class="sponsor-glass-logo">
                                @if(isset($sp->logo_path) && $sp->logo_path)
                                    <img src="{{ Storage::url($sp->logo_path) }}" alt="{{ $sp->name }}" loading="lazy"
                                         onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-block';">
                                    <i class="fas fa-building sponsor-icon-fb" style="display:none;"></i>
                                @else
                                    <i class="fas fa-building sponsor-icon-fb"></i>
                                @endif
                            </div>
                            <span class="sponsor-glass-name">{{ $sp->name }}</span>
                        </div>
                    @else
                        <div class="sponsor-glass-card">
                            <div class="sponsor-glass-logo">
                                <i class="fas {{ $item['icon'] }} sponsor-icon-fb"></i>
                            </div>
                            <span class="sponsor-glass-name">{{ $item['name'] }}</span>
                        </div>
                    @endif
                    <div class="sponsor-ticker-dot"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            // ── Floating particles (golden embers rising)
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

            // ── Top Nav Scrolled state
            window.addEventListener('scroll', function () {
                const nav = document.getElementById('topNav');
                if (nav) {
                    nav.classList.toggle('scrolled', window.scrollY > 30);
                }
            });

            // ── Filtering & Instant Search
            const searchInput = document.getElementById('companySearchInput');
            const clearBtn = document.getElementById('clearSearchBtn');
            const filterChips = document.querySelectorAll('.filter-chip');
            const cards = document.querySelectorAll('.company-item-card');
            const visibleCountEl = document.getElementById('visibleCount');
            const emptyState = document.getElementById('emptyResultsState');
            const resetFiltersBtn = document.getElementById('resetFiltersBtn');

            let currentIndustry = 'all';

            function applyFilters() {
                const query = (searchInput.value || '').trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const industry = card.getAttribute('data-industry') || '';
                    const booth = card.getAttribute('data-booth') || '';
                    const desc = card.getAttribute('data-desc') || '';

                    const matchesIndustry = (currentIndustry === 'all' || industry === currentIndustry);
                    const matchesQuery = !query ||
                        name.includes(query) ||
                        industry.toLowerCase().includes(query) ||
                        booth.includes(query) ||
                        desc.includes(query);

                    if (matchesIndustry && matchesQuery) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (visibleCountEl) visibleCountEl.textContent = visibleCount;
                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
                if (clearBtn) {
                    clearBtn.style.display = query.length > 0 ? 'block' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    searchInput.focus();
                    applyFilters();
                });
            }

            filterChips.forEach(chip => {
                chip.addEventListener('click', function () {
                    filterChips.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    currentIndustry = this.getAttribute('data-industry') || 'all';
                    applyFilters();
                });
            });

            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    currentIndustry = 'all';
                    filterChips.forEach(c => {
                        c.classList.toggle('active', c.getAttribute('data-industry') === 'all');
                    });
                    applyFilters();
                });
            }

            // ── Modal population
            const companyModal = document.getElementById('companyDetailModal');
            if (companyModal) {
                companyModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const name = button.getAttribute('data-name') || 'شركة مشاركة';
                    const logo = button.getAttribute('data-logo') || '';
                    const industry = button.getAttribute('data-industry') || 'قطاع الأعمال والخدمات';
                    const booth = button.getAttribute('data-booth') || '';
                    const jobs = parseInt(button.getAttribute('data-jobs') || '0', 10);
                    const desc = button.getAttribute('data-desc') || 'لا يوجد وصف متاح.';
                    const website = button.getAttribute('data-website') || '';
                    const email = button.getAttribute('data-email') || '';
                    const phone = button.getAttribute('data-phone') || '';

                    document.getElementById('modalCompanyName').textContent = name;
                    document.getElementById('modalCompanyIndustry').textContent = industry;
                    document.getElementById('modalCompanyDesc').textContent = desc;

                    const logoImg = document.getElementById('modalCompanyLogo');
                    const logoFallback = document.getElementById('modalCompanyLogoFallback');
                    if (logo) {
                        logoImg.src = logo;
                        logoImg.style.display = 'block';
                        logoFallback.style.display = 'none';
                    } else {
                        logoImg.style.display = 'none';
                        logoFallback.style.display = 'block';
                    }

                    const boothBadge = document.getElementById('modalCompanyBoothBadge');
                    if (booth) {
                        document.getElementById('modalCompanyBooth').textContent = 'جناح ' + booth;
                        boothBadge.style.display = 'inline-flex';
                    } else {
                        boothBadge.style.display = 'none';
                    }

                    const jobsBadge = document.getElementById('modalCompanyJobsBadge');
                    if (jobs > 0) {
                        document.getElementById('modalCompanyJobs').textContent = jobs;
                        jobsBadge.style.display = 'inline-flex';
                    } else {
                        jobsBadge.style.display = 'none';
                    }

                    const webChip = document.getElementById('modalWebsiteChip');
                    const webLink = document.getElementById('modalCompanyWebsite');
                    if (website) {
                        webLink.href = website.startsWith('http') ? website : 'https://' + website;
                        webLink.textContent = website.replace(/^https?:\/\//, '');
                        webChip.style.display = 'flex';
                    } else {
                        webChip.style.display = 'none';
                    }

                    const emailChip = document.getElementById('modalEmailChip');
                    const emailLink = document.getElementById('modalCompanyEmail');
                    if (email) {
                        emailLink.href = 'mailto:' + email;
                        emailLink.textContent = email;
                        emailChip.style.display = 'flex';
                    } else {
                        emailChip.style.display = 'none';
                    }

                    const phoneChip = document.getElementById('modalPhoneChip');
                    const phoneSpan = document.getElementById('modalCompanyPhone');
                    if (phone) {
                        phoneSpan.textContent = phone;
                        phoneChip.style.display = 'flex';
                    } else {
                        phoneChip.style.display = 'none';
                    }
                });
            }
        })();
    </script>
</body>

</html>