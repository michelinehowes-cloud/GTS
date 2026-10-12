<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $fair ? $fair->title : 'معرض التوظيف السنوي' }} — جامعة طرابلس</title>
    <meta name="description"
        content="{{ $fair ? $fair->title : 'معرض التوظيف السنوي' }} — جامعة طرابلس. سجّل الآن وابدأ مستقبلك المهني.">

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
            --teal: #0EA5E9;
            --green: #10B981;
            --white: #FFFFFF;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #043c73;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        /* ══════════════════════════════════
           TOP NAV
        ══════════════════════════════════ */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: transparent;
            border-bottom: 1px solid transparent;
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }

        .top-nav.scrolled {
            padding: 0.6rem 2rem;
            background: rgba(10, 22, 40, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(245, 158, 11, 0.2);
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
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.25));
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

        /* ── Hero Strip Logos (Bottom of Blue Screen) ── */
        .hero-strip-logo {
            height: 48px;
            width: auto;
            max-width: 170px;
            object-fit: contain;
            opacity: 0.92;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            transition: all 0.25s ease;
        }

        .hero-strip-logo-office {
            border-radius: 50%;
            height: 48px;
            width: 48px;
            border: 2px solid var(--gold);
        }

        .hero-strip-logo:hover {
            opacity: 1;
            transform: translateY(-2px) scale(1.04);
        }

        .hero-strip-divider {
            width: 1px;
            height: 32px;
            background: rgba(255, 255, 255, 0.25);
        }

        /* ── Strategic Sponsor Tier Badge in Sponsors Grid ── */
        .tier-strategic {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.35));
            color: #FDE68A;
            border: 1px solid rgba(245, 158, 11, 0.6);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
        }

        .sponsor-card-strategic {
            border: 1.5px solid rgba(245, 158, 11, 0.45) !important;
            background: linear-gradient(145deg, rgba(245, 158, 11, 0.05) 0%, rgba(15, 23, 42, 0.75) 100%) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        .sponsor-card-strategic:hover {
            border-color: rgba(245, 158, 11, 0.85) !important;
            box-shadow: 0 20px 45px rgba(245, 158, 11, 0.25) !important;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
            padding: 9px 20px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            border: none;
            line-height: 1.4;
        }

        .nav-btn-outline {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .nav-btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        .nav-btn-gold {
            background: linear-gradient(135deg, var(--gold), #F97316);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
        }

        .nav-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(245, 158, 11, 0.5);
            color: white;
        }

        .nav-btn-emerald {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }

        .nav-btn-emerald:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(16, 185, 129, 0.5);
            color: white;
        }

        .nav-link-pill {
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-pill:hover {
            color: var(--gold);
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(238, 202, 62, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 1.25rem;
        }

        .hero-action-buttons .nav-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            white-space: nowrap !important;
            padding: 10px 20px !important;
            font-size: 0.92rem !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            border-radius: 50px !important;
            text-decoration: none !important;
            transition: all 0.25s ease !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        }

        .hero-action-buttons .nav-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            line-height: 1;
            margin: 0 !important;
        }

        .hero-action-buttons .nav-btn span {
            display: inline-block;
            white-space: nowrap;
            line-height: 1.2;
        }

        .hero-action-buttons .nav-btn-gold {
            font-size: 0.98rem !important;
            padding: 11px 24px !important;
            background: linear-gradient(135deg, var(--gold) 0%, #f59e0b 50%, #ea580c 100%) !important;
            color: #0b1f3a !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 18px rgba(245, 158, 11, 0.45) !important;
        }

        .hero-action-buttons .nav-btn-gold:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 24px rgba(245, 158, 11, 0.6) !important;
            color: #0b1f3a !important;
        }

        .hero-action-buttons .nav-btn-outline {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
            backdrop-filter: blur(8px) !important;
        }

        .hero-action-buttons .nav-btn-outline:hover {
            background: rgba(255, 255, 255, 0.16) !important;
            border-color: var(--gold) !important;
            color: var(--gold) !important;
            transform: translateY(-2px) !important;
        }

        /* ══════════════════════════════════
           HERO
        ══════════════════════════════════ */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #045db0;
            border-bottom: none !important;
        }

        /* Animated background layers with seamless landing on #043c73 */
        .hero-bg-layer {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 40%, rgba(245, 158, 11, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 20%, rgba(14, 165, 233, 0.1) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                linear-gradient(180deg, #045db0 0%, #03488a 50%, #043c73 100%);
            pointer-events: none;
        }

        /* Grid overlay with smooth bottom fade */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%);
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            animation: orb-float 8s ease-in-out infinite;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(245, 158, 11, 0.08);
            top: -100px;
            right: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: rgba(14, 165, 233, 0.08);
            bottom: 0;
            left: -80px;
            animation-delay: -3s;
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            background: rgba(16, 185, 129, 0.07);
            top: 50%;
            left: 50%;
            animation-delay: -5s;
        }

        @keyframes orb-float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.05);
            }
        }

        /* Particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.7);
            pointer-events: none;
            animation: particle-rise linear infinite;
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
                opacity: 0.6;
            }

            100% {
                transform: translateY(-10vh) scale(1.2);
                opacity: 0;
            }
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 0 1rem;
            max-width: 900px;
            width: 100%;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: var(--gold);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 0.85rem;
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
                opacity: 0.2
            }
        }

        .hero-title {
            margin-bottom: 0.85rem;
            line-height: 1.15;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.35));
        }

        .scramble-on-scroll {
            cursor: pointer;
            user-select: none;
            transition: opacity 0.2s ease;
        }

        .scramble-cursor {
            display: inline-block;
            width: 3px;
            height: 0.85em;
            background: var(--gold);
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.9);
            margin-inline-start: 6px;
            vertical-align: middle;
            border-radius: 2px;
            animation: scrambleBlink 0.7s infinite;
        }

        @keyframes scrambleBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .hero-title-main {
            display: block;
            white-space: nowrap;
            font-size: clamp(2rem, 3.4vw, 3.25rem);
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.01em;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 60%, var(--gold-lt) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-title-sub {
            display: block;
            white-space: nowrap;
            font-size: clamp(1.6rem, 2.7vw, 2.5rem);
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.01em;
            margin-top: 0.2rem;
            background: linear-gradient(135deg, #ffffff 0%, var(--gold) 65%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: clamp(0.95rem, 1.35vw, 1.15rem);
            margin-bottom: 1.15rem;
            font-weight: 400;
            max-width: 600px;
            line-height: 1.5;
        }

        /* ══════════════════════════════════
           PREMIUM 3D COMPOSITION
        ══════════════════════════════════ */
        .premium-3d-composition {
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            padding: 1rem 0;
        }

        .hero-3d-cap-wrapper {
            position: relative;
            z-index: 5;
            width: 250px;
            height: 155px;
            margin-bottom: -55px;
            animation: float3DCap 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.45));
            pointer-events: none;
        }

        .hero-3d-cap-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform: rotate(-6deg);
        }

        @keyframes float3DCap {

            0%,
            100% {
                transform: translateY(0) rotate(-6deg);
            }

            50% {
                transform: translateY(-12px) rotate(-3deg);
            }
        }

        .premium-glow {
            position: absolute;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(238, 202, 62, 0.4) 0%, rgba(14, 165, 233, 0.3) 40%, transparent 70%);
            filter: blur(40px);
            animation: pulseGlow 4s ease-in-out infinite alternate;
            z-index: 0;
        }

        .premium-glass-card {
            position: relative;
            z-index: 2;
            width: 280px;
            height: 280px;
            background: rgba(255, 255, 255, 0.05);
            /* True glass */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 36px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), inset 0 0 20px rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 26px;
            transform-style: preserve-3d;
            animation: float3DCard 8s ease-in-out infinite;
        }

        /* Glowing orb behind logo for visibility */
        .premium-glass-card::before {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0) 70%);
            filter: blur(20px);
            z-index: 1;
            transform: translateZ(10px);
        }

        .premium-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform: translateZ(40px);
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.4));
            position: relative;
            z-index: 2;
        }

        .float-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            z-index: 3;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .shape-1 {
            width: 62px;
            height: 62px;
            top: 26%;
            right: 8%;
            font-size: 1.55rem;
            color: var(--gold);
            animation: floatElement 6s ease-in-out infinite 1s;
        }

        .shape-4 {
            width: 64px;
            height: 64px;
            bottom: 10%;
            right: 8%;
            font-size: 1.6rem;
            color: var(--teal);
            animation: floatElement 6.5s ease-in-out infinite 1.5s;
        }

        .shape-2 {
            width: 68px;
            height: 68px;
            bottom: 10%;
            left: 5%;
            font-size: 1.75rem;
            color: var(--teal);
            animation: floatElement 7s ease-in-out infinite 0.5s;
        }

        .shape-3 {
            padding: 10px 22px;
            top: 26%;
            left: -20px;
            font-size: 0.95rem;
            font-weight: 800;
            color: white;
            animation: floatElement 5s ease-in-out infinite 2s;
        }

        /* Partners Strip */
        .partner-logo {
            max-height: 40px;
            max-width: 140px;
            object-fit: contain;
            opacity: 0.5;
            transition: all 0.3s ease;
            user-select: none;
            filter: grayscale(1) brightness(200%);
        }

        .partner-logo:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        .partner-logo-jpg {
            /* Removes white background and makes logo white */
            filter: grayscale(1) invert(1) brightness(200%);
            mix-blend-mode: screen;
        }

        .partner-text {
            font-weight: 600;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.5);
            margin: 0;
            line-height: 1;
            transition: all 0.3s ease;
        }

        .partner-text:hover {
            color: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        @keyframes pulseGlow {
            0% {
                opacity: 0.5;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        @keyframes float3DCard {

            0%,
            100% {
                transform: rotateX(8deg) rotateY(-12deg) translateY(0);
            }

            50% {
                transform: rotateX(12deg) rotateY(-8deg) translateY(-20px);
            }
        }

        @keyframes floatElement {

            0%,
            100% {
                transform: translateY(0) rotate(0);
            }

            50% {
                transform: translateY(-15px) rotate(5deg);
            }
        }

        @media (max-width: 991px) {
            .hero-text-col {
                text-align: center !important;
                margin-bottom: 3rem;
            }

            .hero-subtitle {
                margin-left: auto;
                margin-right: auto;
            }

            .event-pills {
                justify-content: center !important;
            }

            .countdown-row {
                justify-content: center !important;
            }

            .premium-glass-card {
                width: 260px;
                height: 260px;
            }

            .shape-3 {
                display: none;
            }
        }

        /* Event info pills */
        .event-pills {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.15rem;
        }

        .event-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgba(255, 255, 255, 0.9);
            padding: 7px 16px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        .event-pill i {
            color: var(--gold);
        }

        /* ══ COUNTDOWN ══ */
        .countdown-row {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.15rem;
            flex-wrap: wrap;
        }

        .cd-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 0.65rem 0.95rem;
            min-width: 70px;
            text-align: center;
            position: relative;
            transition: transform 0.3s, border-color 0.3s;
        }

        .cd-box:hover {
            transform: translateY(-4px);
            border-color: rgba(245, 158, 11, 0.5);
        }

        .cd-box::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), transparent);
            pointer-events: none;
        }

        .cd-num {
            font-size: 1.9rem;
            font-weight: 900;
            color: var(--gold);
            line-height: 1;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 0 25px rgba(245, 158, 11, 0.4);
        }

        .cd-sep {
            display: flex;
            align-items: center;
            padding-bottom: 0.8rem;
            color: var(--gold);
            font-size: 1.5rem;
            font-weight: 900;
            opacity: 0.5;
            animation: blink 1s infinite;
        }

        .cd-lbl {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 5px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ══ STATS ══ */
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1.2rem 2rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(6px);
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat-num {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--gold), #FBBF24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .hero-stat-lbl {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.8rem;
            margin-top: 4px;
            font-weight: 500;
        }

        .hero-stat-divider {
            width: 1px;
            background: rgba(255, 255, 255, 0.1);
            align-self: stretch;
        }

        /* ══ CTA BUTTONS ══ */
        .hero-cta {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .cta-primary {
            background: linear-gradient(135deg, var(--gold) 0%, #F97316 100%);
            color: white;
            border: none;
            padding: 15px 42px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.4);
            transition: all 0.3s;
        }

        .cta-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 40px rgba(245, 158, 11, 0.6);
            color: white;
        }

        .cta-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            padding: 15px 42px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(8px);
            transition: all 0.3s;
        }

        .cta-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-3px);
        }

        /* Registered banner */
        .registered-banner {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(16, 185, 129, 0.15);
            border: 1.5px solid rgba(16, 185, 129, 0.5);
            border-radius: 50px;
            padding: 12px 28px;
            color: white;
            font-weight: 600;
        }

        .registered-banner .reg-num {
            background: rgba(16, 185, 129, 0.3);
            padding: 3px 12px;
            border-radius: 50px;
            color: #6EE7B7;
            font-weight: 700;
        }

        .cta-ticket {
            background: linear-gradient(135deg, var(--green), #059669);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
        }

        .cta-ticket:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.7rem;
            letter-spacing: 2px;
            animation: bounce-down 2s ease-in-out infinite;
        }

        @keyframes bounce-down {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            50% {
                transform: translateX(-50%) translateY(8px);
            }
        }

        /* ══════════════════════════════════
           SECTION TYPOGRAPHY (Global)
        ══════════════════════════════════ */
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .title-line,
        .kinetic-divider {
            display: none !important;
        }

        /* ══════════════════════════════════
           BENTO BOX INFO SECTION
        ══════════════════════════════════ */
        .info-section {
            padding: 120px 0;
            background: #0b1c36;
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.03);
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, transparent 60%);
            z-index: 0;
            pointer-events: none;
        }

        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .bento-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 32px;
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .bento-item:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
        }

        /* Bento Grid Areas */
        .bento-reg {
            grid-column: span 12;
        }

        .bento-date {
            grid-column: span 12;
        }

        .bento-loc {
            grid-column: span 12;
        }

        .bento-desc {
            grid-column: span 12;
        }

        @media(min-width: 992px) {
            .bento-reg {
                grid-column: span 7;
                grid-row: span 2;
                padding: 3.5rem;
                justify-content: center;
            }

            .bento-date {
                grid-column: span 5;
                grid-row: span 1;
            }

            .bento-loc {
                grid-column: span 5;
                grid-row: span 1;
            }
        }

        .bento-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }

        .bento-reg .bento-icon-wrapper {
            width: 90px;
            height: 90px;
            font-size: 2.5rem;
            border-radius: 28px;
            margin-bottom: 2rem;
        }

        .bento-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.8rem;
            z-index: 2;
            position: relative;
        }

        .bento-reg .bento-title {
            font-size: 2.2rem;
            margin-bottom: 1.2rem;
        }

        .bento-text {
            color: rgba(255, 255, 255, 0.65);
            font-size: 1.05rem;
            line-height: 1.6;
            margin: 0;
            z-index: 2;
            position: relative;
        }

        .bento-reg .bento-text {
            font-size: 1.2rem;
        }

        .bento-glow {
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            z-index: 1;
            pointer-events: none;
        }

        /* ══════════════════════════════════
           SECTIONS UNIFIED CANVAS (#043c73)
           توحيد لون خلفية كامل الصفحة تحت الهيرو بانسيابية تامة وبدون فواصل أو تباينات لونية
        ══════════════════════════════════ */
        .sponsors-section,
        .companies-section,
        .program-preview-section,
        .projects-preview-section,
        .cyber-matrix-wrap {
            padding: 95px 0 105px;
            background: #043c73 !important;
            background-color: #043c73 !important;
            background-image: none !important;
            border-top: none !important;
            border-bottom: none !important;
            border: none !important;
            box-shadow: none !important;
            position: relative;
            overflow: hidden;
        }

        /* Side Decorative Dot Matrix Meters — إخفاء النقاط المزعجة وغير المتفاعلية */
        .cyber-side-meter,
        .cyber-dot {
            display: none !important;
        }

        /* Section Titles & Badges */
        .cyber-section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(14, 116, 144, 0.22);
            border: 1px solid rgba(56, 189, 248, 0.4);
            color: #7dd3fc;
            padding: 6px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            margin-bottom: 0.9rem;
            box-shadow: 0 0 18px rgba(14, 116, 144, 0.25);
        }

        .cyber-section-title {
            font-size: 2.35rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 0.75rem;
            letter-spacing: -0.5px;
            text-shadow: 0 0 35px rgba(56, 189, 248, 0.25);
        }

        .cyber-section-title .gold-accent {
            background: linear-gradient(135deg, #fef08a 0%, #f59e0b 60%, #eeca3e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cyber-section-subtitle {
            color: rgba(255, 255, 255, 0.68);
            font-size: 1rem;
            line-height: 1.75;
            max-width: 680px;
            margin: 0 auto;
        }

        /* ══════════════════════════════════════════════════════
           SECTION: STRATEGIC HERO SHOWCASE + SPONSORS PODIUM
           ══════════════════════════════════════════════════════ */
        .sponsors-showcase-wrap {
            max-width: 1140px;
            margin: 3.2rem auto 0;
            position: relative;
        }

        /* ── Strategic Partner Hero Showcase ── */
        .strategic-hero-showcase {
            position: relative;
            background: linear-gradient(135deg, rgba(28, 20, 6, 0.94) 0%, rgba(13, 29, 56, 0.96) 50%, rgba(6, 18, 38, 0.98) 100%);
            border: 1.5px solid rgba(245, 158, 11, 0.65);
            border-radius: 26px;
            padding: 2.2rem 2.6rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 50px rgba(245, 158, 11, 0.18), inset 0 1px 0 rgba(254, 240, 138, 0.35);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin-bottom: 3.2rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .strategic-hero-showcase:hover {
            transform: translateY(-5px);
            border-color: rgba(245, 158, 11, 0.9);
            box-shadow: 0 32px 75px rgba(0, 0, 0, 0.75), 0 0 65px rgba(245, 158, 11, 0.3), inset 0 1px 0 rgba(254, 240, 138, 0.5);
        }

        .strategic-hero-showcase::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(40px);
        }

        .strategic-hero-showcase::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.14) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(40px);
        }

        .strategic-hero-badge-strip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 119, 6, 0.35) 100%);
            border: 1px solid rgba(245, 158, 11, 0.65);
            color: #fbbf24;
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            margin-bottom: 1.4rem;
            box-shadow: 0 0 16px rgba(245, 158, 11, 0.25);
        }

        .strategic-hero-grid {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 2.4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        @media (max-width: 991px) {
            .strategic-hero-grid {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 1.6rem;
            }
            .strategic-hero-showcase {
                padding: 1.8rem 1.4rem;
            }
            .strategic-hero-badge-strip {
                margin: 0 auto 1.2rem;
            }
        }

        .strategic-logo-pod {
            width: 100%;
            height: 120px;
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .strategic-hero-showcase:hover .strategic-logo-pod {
            transform: translateY(-2px) scale(1.05);
        }

        .strategic-logo-pod img {
            max-width: 100%;
            max-height: 110px;
            object-fit: contain;
            filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.6));
            transition: filter 0.3s ease, transform 0.3s ease;
        }

        .strategic-hero-showcase:hover .strategic-logo-pod img {
            filter: drop-shadow(0 8px 24px rgba(245, 158, 11, 0.35)) drop-shadow(0 4px 12px rgba(0, 0, 0, 0.6));
        }

        .strategic-hero-title {
            color: #ffffff;
            font-size: 1.55rem;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 0.7rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .strategic-hero-desc {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.4rem;
            max-width: 720px;
        }

        .strategic-hero-actions-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 991px) {
            .strategic-hero-actions-row {
                justify-content: center;
            }
            .strategic-hero-desc {
                margin-left: auto;
                margin-right: auto;
            }
        }

        .strategic-gold-btn {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            border: 1px solid rgba(254, 240, 138, 0.8);
            color: #0b1528 !important;
            font-size: 0.9rem;
            font-weight: 800;
            padding: 9px 26px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.35);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .strategic-gold-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #fef08a 0%, #f59e0b 100%);
            box-shadow: 0 12px 32px rgba(245, 158, 11, 0.5);
            color: #040c1a !important;
        }

        .strategic-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            padding: 7px 16px;
            border-radius: 50px;
        }

        /* ── Divider between Hero and Podium ── */
        .podium-divider-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.2rem;
            margin: 2.6rem auto 2.2rem;
            max-width: 780px;
        }

        .podium-divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.35), transparent);
        }

        .podium-divider-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 20px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 800;
            color: #93c5fd;
            background: rgba(15, 34, 64, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.3);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* ── Podium Grid for Other Sponsors ── */
        .sponsors-podium-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.8rem;
            position: relative;
            z-index: 2;
        }

        /* ── Tiered Sponsor Card (Podium) ── */
        .podium-sponsor-card {
            position: relative;
            background: linear-gradient(160deg, rgba(6, 20, 44, 0.95) 0%, rgba(2, 10, 24, 0.98) 100%);
            border-radius: 22px;
            padding: 2.4rem 1.6rem 1.6rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 310px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            border: 1.5px solid rgba(56, 189, 248, 0.32);
        }

        .podium-sponsor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.65);
        }

        /* Tier specific card themes */
        .podium-sponsor-card.tier-diamond {
            border-color: rgba(56, 189, 248, 0.45);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(56, 189, 248, 0.12);
        }
        .podium-sponsor-card.tier-diamond:hover {
            border-color: #38bdf8;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.65), 0 0 40px rgba(56, 189, 248, 0.25);
        }

        .podium-sponsor-card.tier-gold {
            border-color: rgba(245, 158, 11, 0.48);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 30px rgba(245, 158, 11, 0.12);
        }
        .podium-sponsor-card.tier-gold:hover {
            border-color: #f59e0b;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.65), 0 0 40px rgba(245, 158, 11, 0.25);
        }

        .podium-sponsor-card.tier-silver {
            border-color: rgba(203, 213, 225, 0.38);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 25px rgba(203, 213, 225, 0.1);
        }
        .podium-sponsor-card.tier-silver:hover {
            border-color: #e2e8f0;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(203, 213, 225, 0.2);
        }

        /* Top Tier Badges on Card */
        .sponsor-tier-badge {
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 18px;
            border-radius: 0 0 14px 14px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            z-index: 3;
        }

        .sponsor-tier-badge.tier-diamond {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.95), rgba(14, 165, 233, 0.95));
            border: 1px solid rgba(56, 189, 248, 0.6);
            border-top: none;
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.35);
        }

        .sponsor-tier-badge.tier-gold {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.95), rgba(245, 158, 11, 0.95));
            border: 1px solid rgba(254, 240, 138, 0.65);
            border-top: none;
            color: #0a1628;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
        }

        .sponsor-tier-badge.tier-silver {
            background: linear-gradient(135deg, rgba(71, 85, 105, 0.95), rgba(148, 163, 184, 0.95));
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-top: none;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(148, 163, 184, 0.25);
        }

        /* ── Crest / Logo Container (SOLVING THE UGLY WHITE SQUARE PROBLEM!) ── */
        .sponsor-identity-hub {
            margin: 1rem auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        /* When sponsor has a real image logo */
        .sponsor-image-dock {
            width: 150px;
            height: 74px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            border: 1.5px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.3s ease;
        }

        .podium-sponsor-card:hover .sponsor-image-dock {
            transform: scale(1.05);
        }

        .sponsor-image-dock img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* When sponsor has NO logo (Regal Luxury Monogram Crest) */
        .sponsor-crest-emblem {
            width: 82px;
            height: 82px;
            border-radius: 22px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .podium-sponsor-card:hover .sponsor-crest-emblem {
            transform: scale(1.06);
        }

        .sponsor-crest-emblem.tier-diamond {
            background: radial-gradient(circle at 30% 30%, rgba(56, 189, 248, 0.3) 0%, rgba(3, 105, 161, 0.45) 100%);
            border: 2px solid rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.3), inset 0 0 15px rgba(56, 189, 248, 0.15);
            color: #38bdf8;
        }

        .sponsor-crest-emblem.tier-gold {
            background: radial-gradient(circle at 30% 30%, rgba(245, 158, 11, 0.3) 0%, rgba(180, 83, 9, 0.45) 100%);
            border: 2px solid rgba(245, 158, 11, 0.75);
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.3), inset 0 0 15px rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .sponsor-crest-emblem.tier-silver {
            background: radial-gradient(circle at 30% 30%, rgba(203, 213, 225, 0.25) 0%, rgba(71, 85, 105, 0.45) 100%);
            border: 2px solid rgba(203, 213, 225, 0.65);
            box-shadow: 0 0 22px rgba(203, 213, 225, 0.25), inset 0 0 12px rgba(203, 213, 225, 0.15);
            color: #f1f5f9;
        }

        .sponsor-crest-emblem .crest-monogram {
            font-size: 2rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .sponsor-crest-emblem .crest-subicon {
            font-size: 0.72rem;
            margin-top: 3px;
            opacity: 0.9;
        }

        /* ── Content on Sponsor Card ── */
        .podium-sponsor-name {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.45rem;
            line-height: 1.35;
        }

        .podium-sponsor-desc {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.84rem;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 1.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.7rem;
        }

        /* ── Action / Verified Status Footer ── */
        .podium-sponsor-action {
            width: 100%;
            margin-top: auto;
            text-align: center;
        }

        .podium-card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 7px 20px;
            border-radius: 50px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            width: 100%;
            max-width: 220px;
        }

        .podium-sponsor-card.tier-diamond .podium-card-btn {
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.4);
            color: #38bdf8;
        }
        .podium-sponsor-card.tier-diamond .podium-card-btn:hover {
            background: #38bdf8;
            color: #041224 !important;
            box-shadow: 0 4px 18px rgba(56, 189, 248, 0.4);
            transform: translateY(-2px);
        }

        .podium-sponsor-card.tier-gold .podium-card-btn {
            background: rgba(245, 158, 11, 0.14);
            border: 1px solid rgba(245, 158, 11, 0.45);
            color: #fbbf24;
        }
        .podium-sponsor-card.tier-gold .podium-card-btn:hover {
            background: #f59e0b;
            color: #041224 !important;
            box-shadow: 0 4px 18px rgba(245, 158, 11, 0.4);
            transform: translateY(-2px);
        }

        .podium-sponsor-card.tier-silver .podium-card-btn {
            background: rgba(203, 213, 225, 0.12);
            border: 1px solid rgba(203, 213, 225, 0.35);
            color: #e2e8f0;
        }
        .podium-sponsor-card.tier-silver .podium-card-btn:hover {
            background: #e2e8f0;
            color: #041224 !important;
            box-shadow: 0 4px 18px rgba(203, 213, 225, 0.3);
            transform: translateY(-2px);
        }

        .podium-verified-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.75);
        }

        .podium-sponsor-card.tier-diamond .podium-verified-pill {
            border-color: rgba(56, 189, 248, 0.25);
            color: #bae6fd;
        }
        .podium-sponsor-card.tier-gold .podium-verified-pill {
            border-color: rgba(245, 158, 11, 0.25);
            color: #fde68a;
        }
        .podium-sponsor-card.tier-silver .podium-verified-pill {
            border-color: rgba(203, 213, 225, 0.25);
            color: #f1f5f9;
        }

        /* CHAMFERED CARDS SYSTEM */
        .cyber-chamfer-card {
            position: relative;
            background: linear-gradient(180deg, rgba(6, 20, 44, 0.94) 0%, rgba(2, 10, 24, 0.98) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.32);
            border-radius: 18px;
            padding: 1.45rem 1.4rem 1.15rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.52), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            text-decoration: none;
            color: #ffffff;
            cursor: pointer;
        }

        .cyber-chamfer-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 34px;
            height: 34px;
            background: linear-gradient(225deg, rgba(56, 189, 248, 0.6) 0%, transparent 60%);
            clip-path: polygon(100% 0, 0 0, 100% 100%);
            pointer-events: none;
            opacity: 0.75;
            transition: opacity 0.3s ease;
        }

        .cyber-chamfer-card:hover {
            transform: translateY(-7px);
            border-color: rgba(56, 189, 248, 0.65);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 0 30px rgba(56, 189, 248, 0.24);
            color: #ffffff;
        }

        .cyber-chamfer-card:hover::before {
            opacity: 1;
        }

        .cyber-chamfer-card.is-featured,
        .cyber-chamfer-card.is-gold {
            border-color: rgba(245, 158, 11, 0.48);
            background: linear-gradient(180deg, rgba(18, 38, 70, 0.88) 0%, rgba(6, 18, 40, 0.98) 100%);
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.5), 0 0 28px rgba(245, 158, 11, 0.15);
        }

        .cyber-chamfer-card.is-featured::before,
        .cyber-chamfer-card.is-gold::before {
            background: linear-gradient(225deg, rgba(245, 158, 11, 0.75) 0%, transparent 60%);
        }

        .cyber-chamfer-card.is-featured:hover,
        .cyber-chamfer-card.is-gold:hover {
            border-color: rgba(245, 158, 11, 0.85);
            box-shadow: 0 22px 55px rgba(0, 0, 0, 0.65), 0 0 40px rgba(245, 158, 11, 0.3);
        }

        .cyber-card-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 1.15rem;
            width: 100%;
        }

        .cyber-time-pill {
            font-size: 0.74rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            padding: 3.5px 11px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .cyber-track-pill {
            font-size: 0.74rem;
            font-weight: 800;
            padding: 3.5px 12px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            letter-spacing: 0.2px;
        }

        .cyber-track-pill.badge-workshop,
        .cyber-track-pill.badge-type-workshop {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.3), rgba(56, 189, 248, 0.35));
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.2);
        }

        .cyber-track-pill.badge-masterclass,
        .cyber-track-pill.badge-type-masterclass {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.3), rgba(168, 85, 247, 0.35));
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.5);
            box-shadow: 0 0 12px rgba(168, 85, 247, 0.2);
        }

        .cyber-track-pill.badge-panel,
        .cyber-track-pill.badge-type-panel {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.3), rgba(52, 211, 153, 0.35));
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.5);
            box-shadow: 0 0 12px rgba(52, 211, 153, 0.2);
        }

        .cyber-track-pill.badge-gold-featured {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(217, 119, 6, 0.35));
            color: #fcd34d;
            border: 1px solid rgba(245, 158, 11, 0.55);
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.25);
        }

        .cyber-card-title {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.45;
            margin-bottom: 0.65rem;
            text-align: right;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.35rem;
        }

        .cyber-card-desc {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.86rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: right;
            min-height: 2.75rem;
        }

        .cyber-card-speaker {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 1rem;
            font-size: 0.82rem;
            color: #93c5fd;
            font-weight: 700;
        }

        .cyber-action-strip {
            margin-top: auto;
            padding-top: 0.95rem;
            border-top: 1px solid rgba(255, 255, 255, 0.09);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #38bdf8;
            text-decoration: none;
            transition: all 0.25s ease;
            width: 100%;
        }

        .cyber-chamfer-card:hover .cyber-action-strip {
            color: #fcd34d;
        }

        .cyber-chamfer-card.is-featured .cyber-action-strip,
        .cyber-chamfer-card.is-gold .cyber-action-strip {
            color: #fbbf24;
        }

        .cyber-action-strip i {
            transition: transform 0.25s ease;
        }

        .cyber-chamfer-card:hover .cyber-action-strip i.fa-arrow-left {
            transform: translateX(-5px);
        }

        /* ══════════════════════════════════
           COMPANIES SECTION (Interactive Expo Booths)
        ══════════════════════════════════ */
        .companies-section {
            padding: 95px 0 105px;
            background: #043c73 !important;
            border: none !important;
            box-shadow: none !important;
            position: relative;
        }

        .companies-booth-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(265px, 1fr));
            gap: 1.7rem;
            max-width: 1240px;
            margin: 0 auto;
        }

        /* ══════════════════════════════════════════════════════════
           3D CYLINDER SHOWCASE CAROUSEL (أسطوانة الشركات ثلاثية الأبعاد)
           مستوحاة من Uiverse ومُطورة بهوية المعرض الملكية السيبرانية
        ══════════════════════════════════════════════════════════ */
        .cyber-cylinder-scene {
            width: 100%;
            position: relative;
            margin: 1.5rem 0 2.5rem;
        }

        /* شريط التحكم والتبديل العلوي */
        .cylinder-controls-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            max-width: 960px;
            margin: 0 auto 1.8rem;
            padding: 8px 18px;
            background: rgba(3, 22, 50, 0.75);
            border: 1px solid rgba(56, 189, 248, 0.28);
            border-radius: 50px;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .cylinder-view-switch {
            display: inline-flex;
            align-items: center;
            background: rgba(2, 14, 32, 0.65);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 40px;
            padding: 3px;
            gap: 4px;
        }

        .view-switch-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 30px;
            transition: all 0.25s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .view-switch-btn.active {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.3) 0%, rgba(4, 60, 115, 0.8) 100%);
            color: #ffffff;
            border: 1px solid rgba(56, 189, 248, 0.55);
            box-shadow: 0 0 14px rgba(56, 189, 248, 0.35);
        }

        .cylinder-playback-controls {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cylinder-play-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(238, 202, 62, 0.12);
            border: 1px solid rgba(238, 202, 62, 0.4);
            color: #fbbf24;
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .cylinder-play-btn:hover {
            background: rgba(238, 202, 62, 0.25);
            border-color: #fbbf24;
            color: #ffffff;
            box-shadow: 0 0 16px rgba(238, 202, 62, 0.35);
        }

        .cylinder-step-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .cylinder-step-btn:hover {
            background: rgba(56, 189, 248, 0.25);
            color: #38bdf8;
            border-color: #38bdf8;
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.4);
        }

        /* مسرح الأسطوانة */
        .cyber-cylinder-wrapper {
            width: 100%;
            height: 520px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            user-select: none;
            padding: 2rem 0;
        }

        /* حلقة الأرضية الهولوجرافية */
        .cylinder-floor-glow {
            position: absolute;
            width: 760px;
            height: 760px;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%) rotateX(76deg);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(4, 60, 115, 0.35) 45%, transparent 72%);
            border: 1.5px dashed rgba(56, 189, 248, 0.35);
            box-shadow: 0 0 50px rgba(56, 189, 248, 0.2);
            pointer-events: none;
            z-index: 1;
        }

        /* الأسطوانة الدوارة 3D */
        .cyber-cylinder-inner {
            --w: 235px;
            --h: 335px;
            --translateZ: 420px;
            --rotateX: -9deg;
            --perspective: 1400px;
            position: absolute;
            width: var(--w);
            height: var(--h);
            top: 50%;
            left: 50%;
            margin-top: calc(-1 * (var(--h) / 2));
            margin-left: calc(-1 * (var(--w) / 2));
            z-index: 2;
            transform-style: preserve-3d;
            transform: perspective(var(--perspective)) rotateX(var(--rotateX)) rotateY(0deg);
            animation: cylinderRotating 26s linear infinite;
            transition: animation-play-state 0.3s ease;
        }

        @keyframes cylinderRotating {
            from {
                transform: perspective(var(--perspective)) rotateX(var(--rotateX)) rotateY(0deg);
            }
            to {
                transform: perspective(var(--perspective)) rotateX(var(--rotateX)) rotateY(360deg);
            }
        }

        /* إيقاف الدوران عند مرور الماوس على المسرح */
        .cyber-cylinder-wrapper:hover .cyber-cylinder-inner,
        .cyber-cylinder-inner.is-paused {
            animation-play-state: paused !important;
        }

        /* بطاقة الشركة ثلاثية الأبعاد */
        .cyber-cylinder-card {
            position: absolute;
            inset: 0;
            border: 2px solid rgba(var(--color-card, 56, 189, 248), 0.75);
            border-radius: 20px;
            overflow: hidden;
            transform-style: preserve-3d;
            transform: rotateY(calc((360deg / var(--quantity)) * var(--index))) translateZ(var(--translateZ));
            background: radial-gradient(
                circle at 50% 25%,
                rgba(var(--color-card, 56, 189, 248), 0.26) 0%,
                rgba(4, 38, 77, 0.88) 60%,
                rgba(2, 18, 42, 0.98) 100%
            );
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.65), 0 0 25px rgba(var(--color-card, 56, 189, 248), 0.28), inset 0 0 20px rgba(var(--color-card, 56, 189, 248), 0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.15rem 1.05rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            text-align: center;
            backface-visibility: hidden;
        }

        .cyber-cylinder-card:hover {
            border-color: rgba(var(--color-card, 56, 189, 248), 1);
            box-shadow: 0 0 45px rgba(var(--color-card, 56, 189, 248), 0.8), inset 0 0 30px rgba(var(--color-card, 56, 189, 248), 0.35), 0 24px 50px rgba(0, 0, 0, 0.85);
            transform: rotateY(calc((360deg / var(--quantity)) * var(--index))) translateZ(calc(var(--translateZ) + 28px)) scale(1.06);
            z-index: 20;
        }

        /* تفاصيل بطاقة الأسطوانة */
        .cylinder-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            width: 100%;
            margin-bottom: 0.6rem;
        }

        .cylinder-tag-number {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(var(--color-card, 56, 189, 248), 0.15);
            color: #ffffff;
            border: 1px solid rgba(var(--color-card, 56, 189, 248), 0.45);
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .cylinder-tag-jobs {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .cylinder-tag-jobs.is-hiring {
            background: rgba(16, 185, 129, 0.18);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.45);
        }

        .cylinder-tag-jobs.is-intro {
            background: rgba(var(--color-card, 56, 189, 248), 0.15);
            color: rgba(var(--color-card, 56, 189, 248), 1);
            border: 1px solid rgba(var(--color-card, 56, 189, 248), 0.35);
        }

        .cylinder-identity-hub {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0.2rem auto 0.6rem;
            min-height: 78px;
        }

        .cylinder-logo-dock {
            width: 125px;
            height: 72px;
            background: #ffffff;
            border-radius: 16px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.45);
            border: 1.5px solid rgba(255, 255, 255, 0.8);
            transition: transform 0.3s ease;
        }

        .cyber-cylinder-card:hover .cylinder-logo-dock {
            transform: scale(1.05);
        }

        .cylinder-logo-dock img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .cylinder-crest-emblem {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: radial-gradient(circle at 35% 30%, rgba(var(--color-card, 56, 189, 248), 0.3) 0%, rgba(5, 22, 50, 0.98) 100%);
            border: 2px solid rgba(var(--color-card, 56, 189, 248), 0.65);
            box-shadow: 0 0 20px rgba(var(--color-card, 56, 189, 248), 0.35);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .cylinder-crest-emblem .crest-monogram-label {
            font-size: 1.1rem;
            font-weight: 900;
            line-height: 1.1;
            color: #fbbf24;
            max-width: 68px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cylinder-crest-emblem .crest-sub-icon {
            font-size: 0.8rem;
            color: #38bdf8;
            margin-top: 3px;
        }

        .cylinder-content-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .cylinder-company-title {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 0.35rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: center;
            width: 100%;
            min-height: 2.7rem;
        }

        .cylinder-sector-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 50px;
            max-width: 92%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.4rem;
        }

        .cylinder-action-cta {
            width: 100%;
            margin-top: auto;
            padding: 7px 14px;
            border-radius: 10px;
            background: rgba(var(--color-card, 56, 189, 248), 0.12);
            border: 1px solid rgba(var(--color-card, 56, 189, 248), 0.35);
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .cyber-cylinder-card:hover .cylinder-action-cta {
            background: rgba(var(--color-card, 56, 189, 248), 0.28);
            border-color: rgba(var(--color-card, 56, 189, 248), 0.8);
            box-shadow: 0 4px 14px rgba(var(--color-card, 56, 189, 248), 0.3);
        }

        .cylinder-action-arrow {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .cyber-cylinder-card:hover .cylinder-action-arrow {
            transform: translateX(-3px);
            background: #ffffff;
            color: #041224;
        }

        /* استجابة الشاشات الصغيرة للمسرح ثلاثي الأبعاد */
        @media (max-width: 991px) {
            .cyber-cylinder-inner {
                --w: 200px;
                --h: 295px;
                --translateZ: 330px;
                --rotateX: -7deg;
                --perspective: 1100px;
            }
            .cyber-cylinder-wrapper {
                height: 460px;
            }
            .cylinder-floor-glow {
                width: 600px;
                height: 600px;
                bottom: -120px;
            }
        }

        @media (max-width: 576px) {
            .cyber-cylinder-inner {
                --w: 165px;
                --h: 250px;
                --translateZ: 255px;
                --rotateX: -5deg;
                --perspective: 900px;
            }
            .cyber-cylinder-wrapper {
                height: 400px;
            }
            .cylinder-logo-dock {
                width: 95px;
                height: 56px;
                padding: 4px 8px;
            }
            .cylinder-company-title {
                font-size: 0.9rem;
                min-height: 2.4rem;
            }
            .cylinder-floor-glow {
                width: 440px;
                height: 440px;
                bottom: -90px;
            }
            .cylinder-controls-bar {
                justify-content: center;
            }
        }

        /* ══════════════════════════════════════════════════════════
           LUXURY CYBER BOOTH CARD (معرض الشركات والمؤسسات المشاركة - شبكة)
        ══════════════════════════════════════════════════════════ */
        .companies-booth-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(285px, 1fr));
            gap: 1.8rem;
            max-width: 1260px;
            margin: 0 auto;
        }

        .company-booth-card {
            background: radial-gradient(circle at 50% 15%, rgba(56, 189, 248, 0.22) 0%, rgba(4, 38, 77, 0.92) 65%, rgba(2, 18, 42, 0.98) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.45);
            border-radius: 24px;
            padding: 1.4rem 1.35rem 1.25rem;
            text-align: center;
            text-decoration: none;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            cursor: pointer;
            overflow: hidden;
            min-height: 380px;
        }

        /* Top Glowing Ambient Line */
        .company-booth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.85), rgba(238, 202, 62, 0.8), transparent);
            opacity: 0.5;
            transition: all 0.35s ease;
        }

        .company-booth-card:hover {
            transform: translateY(-8px);
            border-color: rgba(56, 189, 248, 0.65);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.65), 0 0 35px rgba(56, 189, 248, 0.25);
        }

        .company-booth-card:hover::before {
            left: 5%;
            right: 5%;
            opacity: 1;
        }

        /* ── Header Badge Strip ── */
        .booth-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 1.2rem;
            width: 100%;
        }

        .booth-tag-number {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(238, 202, 62, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(238, 202, 62, 0.35);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .booth-tag-jobs {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.76rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .booth-tag-jobs.is-hiring {
            background: rgba(16, 185, 129, 0.16);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.45);
            box-shadow: 0 2px 10px rgba(16, 185, 129, 0.15);
        }

        .booth-tag-jobs.is-intro {
            background: rgba(56, 189, 248, 0.14);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.35);
        }

        .pulse-glow-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: pulseDotGlow 1.8s infinite;
            display: inline-block;
        }

        @keyframes pulseDotGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        /* ── Center Identity Emblem / Logo Dock ── */
        .booth-identity-hub {
            margin: 0.4rem auto 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 84px;
        }

        .booth-logo-dock {
            width: 140px;
            height: 76px;
            background: #ffffff;
            border-radius: 18px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
            border: 1.5px solid rgba(255, 255, 255, 0.6);
            transition: transform 0.3s ease;
        }

        .company-booth-card:hover .booth-logo-dock {
            transform: scale(1.05);
        }

        .booth-logo-dock img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Holographic Digital Crest (When no logo is uploaded) */
        .booth-crest-emblem {
            width: 82px;
            height: 82px;
            border-radius: 22px;
            background: radial-gradient(circle at 35% 30%, rgba(18, 58, 118, 0.95) 0%, rgba(5, 22, 50, 0.98) 100%);
            border: 2px solid rgba(56, 189, 248, 0.55);
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.25), inset 0 0 15px rgba(56, 189, 248, 0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: all 0.3s ease;
            position: relative;
        }

        .company-booth-card:hover .booth-crest-emblem {
            transform: scale(1.06);
            border-color: #38bdf8;
            box-shadow: 0 0 30px rgba(56, 189, 248, 0.45);
        }

        .booth-crest-emblem .crest-monogram-label {
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.1;
            color: #fbbf24;
            letter-spacing: -0.3px;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
            max-width: 72px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .booth-crest-emblem .crest-sub-icon {
            font-size: 0.85rem;
            color: #38bdf8;
            margin-top: 4px;
            opacity: 0.9;
        }

        /* ── Body: Title, Sector, Teaser ── */
        .booth-content-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .booth-company-title {
            color: #ffffff;
            font-size: 1.18rem;
            font-weight: 800;
            line-height: 1.4;
            margin-bottom: 0.45rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.3rem;
            text-align: center;
            width: 100%;
        }

        .booth-sector-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 95%;
        }

        .booth-sector-badge i {
            color: #38bdf8;
            font-size: 0.78rem;
        }

        .booth-desc-teaser {
            font-size: 0.81rem;
            color: rgba(255, 255, 255, 0.62);
            line-height: 1.55;
            margin-bottom: 1.15rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.5rem;
            text-align: center;
            width: 100%;
        }

        /* ── Bottom Interactive Action CTA ── */
        .booth-action-cta {
            width: 100%;
            margin-top: auto;
            padding: 9px 18px;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.08);
            border: 1px solid rgba(56, 189, 248, 0.25);
            color: #38bdf8;
            font-size: 0.84rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .company-booth-card:hover .booth-action-cta {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.22) 0%, rgba(238, 202, 62, 0.18) 100%);
            border-color: rgba(56, 189, 248, 0.55);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.2);
        }

        .booth-action-arrow {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .company-booth-card:hover .booth-action-arrow {
            background: #38bdf8;
            color: #041224;
            transform: translateX(-4px);
        }

        /* ── Modern Directory Button ── */
        .companies-dir-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, rgba(6, 43, 84, 0.9) 0%, rgba(14, 76, 148, 0.95) 50%, rgba(217, 119, 6, 0.85) 150%);
            color: #ffffff;
            border: 1.5px solid rgba(238, 202, 62, 0.6);
            padding: 15px 36px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.05rem;
            text-decoration: none;
            box-shadow: 0 12px 30px rgba(4, 93, 176, 0.45), 0 0 20px rgba(238, 202, 62, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .companies-dir-btn:hover {
            color: #ffffff;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 18px 40px rgba(238, 202, 62, 0.4), 0 0 30px rgba(56, 189, 248, 0.3);
            border-color: #eeca3e;
        }

        .companies-dir-btn i {
            transition: transform 0.25s ease;
        }

        .companies-dir-btn:hover i {
            transform: translateX(-5px);
        }


        /* ══════════════════════════════════
           SCIENTIFIC PROGRAM PREVIEW SECTION (Compact & Refined)
        ══════════════════════════════════ */
        .program-preview-section {
            padding: 95px 0 105px;
            background: #043c73 !important;
            border: none !important;
            box-shadow: none !important;
            position: relative;
            overflow: hidden;
        }

        .projects-preview-section {
            padding: 95px 0 105px;
            background: #043c73 !important;
            border: none !important;
            box-shadow: none !important;
            position: relative;
            overflow: hidden;
        }

        .program-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(285px, 1fr));
            gap: 1.4rem;
            margin-bottom: 3rem;
        }

        .program-preview-card {
            background: linear-gradient(160deg, rgba(5, 18, 42, 0.94) 0%, rgba(2, 10, 24, 0.98) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.32);
            border-radius: 20px;
            padding: 1.35rem 1.4rem;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.95rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            text-decoration: none;
            color: #ffffff;
            position: relative;
            cursor: pointer;
        }

        .program-preview-card:hover {
            transform: translateY(-6px);
            border-color: rgba(56, 189, 248, 0.6);
            background: rgba(12, 45, 92, 0.85);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35), 0 0 25px rgba(56, 189, 248, 0.2);
            color: #ffffff;
        }

        .program-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .badge-type-masterclass {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.3), rgba(168, 85, 247, 0.35));
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.45);
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 11px;
            border-radius: 50px;
        }

        .badge-type-workshop {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.25), rgba(56, 189, 248, 0.3));
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 11px;
            border-radius: 50px;
        }

        .badge-type-panel {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.25), rgba(52, 211, 153, 0.3));
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.4);
            font-size: 0.72rem;
            font-weight: 800;
            padding: 4px 11px;
            border-radius: 50px;
        }

        .event-time-pill {
            font-size: 0.74rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 3px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
        }

        .event-card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.45;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.9rem;
        }

        .event-compact-speaker {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .speaker-avatar-sm {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid rgba(56, 189, 248, 0.6);
            flex-shrink: 0;
        }

        .speaker-avatar-sm-fallback {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }

        .speaker-name-compact {
            font-size: 0.82rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .program-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: auto;
        }

        .event-loc-compact {
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            max-width: 60%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-event-quick-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--gold);
            text-decoration: none;
            padding: 5px 12px;
            border-radius: 50px;
            background: rgba(238, 202, 62, 0.12);
            border: 1px solid rgba(238, 202, 62, 0.3);
            transition: all 0.2s ease;
        }

        .btn-event-quick-view:hover {
            background: linear-gradient(135deg, var(--gold), #f59e0b);
            color: #071933;
            border-color: var(--gold);
            transform: translateX(-2px);
        }

        /* ══════════════════════════════════
           MODAL
        ══════════════════════════════════ */
        .modal-card {
            border: none;
            border-radius: 24px;
            overflow: hidden;
        }

        .modal-header-grad {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-md) 100%);
            padding: 1.5rem 2rem;
            border: none;
        }

        .grad-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .grad-info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.8rem;
        }

        .grad-info-item .lbl {
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .grad-info-item .val {
            font-weight: 700;
            color: var(--navy);
            font-size: 0.9rem;
        }

        /* Company Detail Modal */
        .company-modal-dialog {
            max-width: 580px;
        }

        .company-modal-content {
            background: #0b192e;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            color: #ffffff;
        }

        .company-modal-header {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.5) 0%, rgba(15, 23, 42, 0.8) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 2rem 2rem 1.5rem;
            position: relative;
        }

        .company-modal-logo-wrapper {
            width: 100px;
            height: 100px;
            background: #ffffff;
            border-radius: 22px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }

        .company-modal-logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-modal-badge {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .company-modal-body {
            padding: 1.8rem 2rem 2rem;
        }

        .company-info-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-info-chip .icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        /* ══════════════════════════════════
           SLIM FOOTER & SPONSORS STRIP — BLUE GLASSMORPHISM
        ══════════════════════════════════ */
        /* Slim Footer Sponsors Strip — Blue Glassmorphism (Fully Responsive) */
        .footer-sponsors-strip {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0;
            position: relative;
            z-index: 10;
            overflow: hidden;
            width: 100%;
            max-width: 100vw;
        }

        .footer-sponsors-strip::before,
        .footer-sponsors-strip::after {
            display: none !important;
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
            border-left: 1px solid rgba(56, 189, 248, 0.3);
            border-right: 1px solid rgba(56, 189, 248, 0.3);
            background: linear-gradient(90deg, rgba(3, 53, 102, 0.95) 0%, rgba(3, 40, 80, 0.6) 100%);
        }

        .strip-office-logo {
            height: 36px;
            width: auto;
            object-fit: contain;
            opacity: 0.95;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
        }

        .strip-divider-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.5);
        }

        .strip-wahaexpo-logo {
            height: 28px;
            width: auto;
            object-fit: contain;
            opacity: 0.92;
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
        }

        .sponsors-strip-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(56, 189, 248, 0.9);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            white-space: nowrap;
            padding: 0 14px;
            flex-shrink: 0;
            border-left: 1px solid rgba(56, 189, 248, 0.25);
            border-right: 1px solid rgba(56, 189, 248, 0.25);
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

        .sponsors-ticker-track:hover {
            animation-play-state: paused;
        }

        @keyframes sponsorScroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .sponsor-glass-card {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(56, 189, 248, 0.22);
            border-radius: 12px;
            padding: 6px 16px 6px 10px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .sponsor-glass-card.is-link {
            cursor: pointer;
        }

        .sponsor-glass-card.is-link:hover {
            background: rgba(56, 189, 248, 0.14);
            border-color: rgba(56, 189, 248, 0.55);
            box-shadow: 0 4px 18px rgba(56, 189, 248, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .sponsor-glass-logo {
            width: 38px;
            height: 32px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
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
            color: rgba(255, 255, 255, 0.88);
            letter-spacing: 0.01em;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sponsor-glass-card.is-link:hover .sponsor-glass-name {
            color: #38BDF8;
        }

        .sponsor-ticker-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.3);
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

        .page-footer {
            background: var(--navy);
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            padding: 1.5rem 1rem;
            font-size: 0.85rem;
        }

        .page-footer a {
            color: var(--gold);
            text-decoration: none;
        }

        /* ══════════════════════════════════
           EMPTY STATE
        ══════════════════════════════════ */
        .empty-hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, var(--navy) 0%, var(--navy-md) 100%);
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .empty-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 30px rgba(245, 158, 11, 0.4));
        }

        /* ══════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════ */
        @media (max-width: 768px) {
            .top-nav {
                padding: 0.5rem 0.85rem !important;
                background: rgba(10, 22, 40, 0.92) !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            }

            .nav-brand {
                gap: 0.5rem !important;
            }

            .nav-brand img.main-logo {
                width: 34px !important;
                height: 34px !important;
            }

            .nav-brand img.jf-logo {
                height: 30px !important;
                margin-right: 4px !important;
            }

            .nav-brand-text {
                display: none !important;
            }

            .nav-brand-divider {
                display: none !important;
            }

            .waha-nav-logo {
                display: none !important;
            }

            .nav-btn {
                padding: 7px 14px !important;
                font-size: 0.82rem !important;
            }

            /* Hero Layout on Mobile */
            .hero {
                min-height: auto !important;
                padding-bottom: 2.5rem !important;
            }

            #hero .container {
                padding-top: 68px !important;
            }

            .hero-text-col {
                text-align: center !important;
                margin-top: 1rem !important;
                margin-bottom: 1.5rem !important;
            }

            .hero-eyebrow {
                font-size: 0.72rem !important;
                padding: 4px 12px !important;
                margin-bottom: 0.85rem !important;
                letter-spacing: 0.5px !important;
                line-height: 1.4 !important;
            }

            .hero-title {
                margin-bottom: 0.75rem !important;
            }

            .hero-title-main {
                font-size: clamp(1.45rem, 5.8vw, 2.1rem) !important;
                white-space: nowrap !important;
                line-height: 1.2 !important;
            }

            .hero-title-sub {
                font-size: clamp(1.2rem, 4.6vw, 1.7rem) !important;
                white-space: nowrap !important;
                line-height: 1.25 !important;
                margin-top: 0.15rem !important;
            }

            .hero-subtitle {
                font-size: 0.9rem !important;
                line-height: 1.5 !important;
                margin-bottom: 1.25rem !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .event-pills {
                gap: 0.4rem !important;
                margin-bottom: 1.25rem !important;
            }

            .event-pill {
                padding: 5px 12px !important;
                font-size: 0.78rem !important;
            }

            /* Countdown */
            .countdown-row {
                gap: 0.4rem !important;
                margin-bottom: 1.25rem !important;
            }

            .cd-box {
                min-width: 58px !important;
                padding: 0.45rem 0.55rem !important;
                border-radius: 12px !important;
            }

            .cd-num {
                font-size: 1.35rem !important;
            }

            .cd-lbl {
                font-size: 0.62rem !important;
            }

            .cd-sep {
                font-size: 1.1rem !important;
            }

            /* Action Buttons: 2-column balanced grid */
            .hero-action-buttons {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
                margin-top: 1rem !important;
                width: 100% !important;
            }

            .hero-action-buttons .nav-btn-gold {
                grid-column: 1 / -1 !important;
                width: 100% !important;
                font-size: 0.98rem !important;
                padding: 11px 16px !important;
            }

            .hero-action-buttons .nav-btn {
                width: 100% !important;
                padding: 9px 10px !important;
                font-size: 0.8rem !important;
                white-space: normal !important;
                text-align: center !important;
            }

            /* 3D Visual Card on Mobile */
            .premium-3d-composition {
                padding: 0.75rem 0 !important;
                margin: 0 auto !important;
            }

            .premium-glass-card {
                width: 180px !important;
                height: 180px !important;
                padding: 16px !important;
                border-radius: 26px !important;
                margin: 0 auto !important;
            }

            .premium-glow {
                width: 200px !important;
                height: 200px !important;
            }

            .float-element.shape-1,
            .float-element.shape-2,
            .float-element.shape-3,
            .float-element.shape-4 {
                display: none !important;
            }

            /* Stats on Mobile */
            .hero-stats {
                gap: 0.8rem !important;
                padding: 0.85rem !important;
                flex-wrap: wrap !important;
                justify-content: center !important;
            }

            .hero-stat {
                min-width: 70px !important;
            }

            .hero-stat-num {
                font-size: 1.5rem !important;
            }

            .hero-stat-lbl {
                font-size: 0.72rem !important;
            }

            .hero-stat-divider {
                display: none !important;
            }

            /* Hero Strip Logos */
            .hero-strip-logo {
                height: 38px !important;
                max-width: 130px !important;
            }

            .hero-strip-logo-office {
                width: 38px !important;
                height: 38px !important;
            }

            .hero-strip-divider {
                height: 24px !important;
            }
        }

        /* ══════════════════════════════════════════════════════════
           OMNIX STUDIO (PARALLAX, SPOTLIGHT & KINETIC MICRO-INTERACTIONS)
        ══════════════════════════════════════════════════════════ */

        /* ── 2. Omnix Interactive Mouse Spotlight on Cards ── */
        .omnix-spotlight-card {
            position: relative;
            overflow: hidden !important;
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                        border-color 0.45s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .omnix-spotlight-card::after {
            content: '';
            position: absolute;
            top: var(--mouse-y, -200px);
            left: var(--mouse-x, -200px);
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.18) 0%, rgba(14, 165, 233, 0.08) 45%, transparent 70%);
            transform: translate(-50%, -50%);
            border-radius: 50%;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.35s ease;
            z-index: 2;
        }

        .omnix-spotlight-card:hover::after {
            opacity: 1;
        }

        /* ── 3. High-End Card Hover Lift & Border Shimmer ── */
        .company-booth-card:hover,
        .sponsor-card:hover,
        .project-card:hover,
        .workshop-card:hover {
            transform: translateY(-9px) scale(1.015) !important;
            border-color: rgba(245, 158, 11, 0.75) !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65), 0 0 35px rgba(245, 158, 11, 0.22) !important;
        }

        .company-booth-card .booth-logo-dock,
        .sponsor-card .sponsor-logo-box,
        .booth-crest-emblem {
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .company-booth-card:hover .booth-logo-dock,
        .company-booth-card:hover .booth-crest-emblem,
        .sponsor-card:hover .sponsor-logo-box {
            transform: scale(1.1) translateY(-4px) !important;
        }

        /* ── 4. Omnix Smooth Magnetic Buttons with Ambient Aura ── */
        .hero-action-buttons .nav-btn-gold {
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 6px 24px rgba(245, 158, 11, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }

        .hero-action-buttons .nav-btn-gold::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: skewX(-20deg);
            transition: left 0.75s ease;
        }

        .hero-action-buttons .nav-btn-gold:hover::before {
            left: 140%;
        }

        .hero-action-buttons .nav-btn-gold:hover {
            transform: translateY(-3px) scale(1.03) !important;
            box-shadow: 0 12px 35px rgba(245, 158, 11, 0.65), 0 0 25px rgba(245, 158, 11, 0.4) !important;
        }

        .hero-action-buttons .nav-btn-outline {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.18) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .hero-action-buttons .nav-btn-outline:hover {
            background: rgba(245, 158, 11, 0.14) !important;
            border-color: #f59e0b !important;
            color: #fbbf24 !important;
            transform: translateY(-3px) scale(1.02) !important;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3) !important;
        }

        .hero-action-buttons .nav-btn i {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .hero-action-buttons .nav-btn:hover i {
            transform: scale(1.2) translateY(-1px) !important;
        }

        /* ── 5. Staggered Scroll Reveal System (GSAP/Framer feel) ── */
        .omnix-reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .omnix-reveal.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── 6. Futuristic Countdown Cards ── */
        .countdown-row .cd-box {
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .countdown-row .cd-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #f59e0b, transparent);
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .countdown-row .cd-box:hover {
            transform: translateY(-4px) scale(1.04);
            border-color: rgba(245, 158, 11, 0.8) !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5), 0 0 25px rgba(245, 158, 11, 0.35) !important;
        }

        .countdown-row .cd-box:hover::before {
            opacity: 1;
        }

        /* ── 7. Floating Depth Micro-Animations ── */
        .float-element {
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s ease !important;
        }

        .float-element:hover {
            transform: scale(1.15) !important;
            background: rgba(255, 255, 255, 0.22) !important;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(245, 158, 11, 0.4) !important;
            border-color: var(--gold) !important;
        }

        /* ══════════════════════════════════════════════════════════
           8. OMNIX SCROLL-REVEAL ANIMATION SYSTEM
        ══════════════════════════════════════════════════════════ */

        /* Scroll Progress Bar */
        #scroll-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), #f97316, var(--gold));
            background-size: 200% auto;
            z-index: 9999;
            width: 0%;
            transition: width 0.1s linear;
            animation: shiftGrad 3s linear infinite;
            box-shadow: 0 0 8px rgba(238,202,62,0.7);
        }
        @keyframes shiftGrad {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        /* Section Entrance Animations */
        .sr-fade-up {
            opacity: 0;
            transform: translateY(60px);
            transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
        }
        .sr-fade-left {
            opacity: 0;
            transform: translateX(-60px);
            transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
        }
        .sr-fade-right {
            opacity: 0;
            transform: translateX(60px);
            transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
        }
        .sr-zoom-in {
            opacity: 0;
            transform: scale(0.85);
            transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
        }
        .sr-flip-up {
            opacity: 0;
            transform: perspective(800px) rotateX(35deg) translateY(40px);
            transition: opacity 0.9s cubic-bezier(0.16,1,0.3,1), transform 0.9s cubic-bezier(0.16,1,0.3,1);
        }
        .sr-slide-scale {
            opacity: 0;
            transform: scale(0.7) translateY(30px);
            transition: opacity 0.75s cubic-bezier(0.34,1.56,0.64,1), transform 0.75s cubic-bezier(0.34,1.56,0.64,1);
        }

        /* Revealed state */
        .sr-revealed {
            opacity: 1 !important;
            transform: none !important;
        }

        /* Omnix Stagger Delays */
        .sr-d1 { transition-delay: 0.05s; }
        .sr-d2 { transition-delay: 0.12s; }
        .sr-d3 { transition-delay: 0.19s; }
        .sr-d4 { transition-delay: 0.26s; }
        .sr-d5 { transition-delay: 0.33s; }
        .sr-d6 { transition-delay: 0.40s; }

        /* Section Title Kinetic Underline */
        .kinetic-title-wrap {
            position: relative;
            display: inline-block;
        }
        .kinetic-title-wrap::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            transition: width 1.2s cubic-bezier(0.16,1,0.3,1);
        }
        .kinetic-title-wrap.line-revealed::after {
            width: 80%;
        }

        /* Glitch Text Effect on Section Badges */
        .glitch-badge {
            position: relative;
        }
        .glitch-badge::before,
        .glitch-badge::after {
            content: attr(data-text);
            position: absolute;
            top: 0; left: 0;
            opacity: 0;
            pointer-events: none;
        }
        .glitch-badge.is-glitching::before {
            opacity: 0.5;
            color: #00e5ff;
            clip-path: polygon(0 20%, 100% 20%, 100% 40%, 0 40%);
            animation: glitch-1 0.4s steps(2) forwards;
        }
        .glitch-badge.is-glitching::after {
            opacity: 0.5;
            color: #ff0066;
            clip-path: polygon(0 60%, 100% 60%, 100% 80%, 0 80%);
            animation: glitch-2 0.4s steps(2) forwards;
        }
        @keyframes glitch-1 {
            0%   { transform: translateX(0); }
            25%  { transform: translateX(-4px); }
            50%  { transform: translateX(4px); }
            75%  { transform: translateX(-2px); }
            100% { transform: translateX(0); opacity: 0; }
        }
        @keyframes glitch-2 {
            0%   { transform: translateX(0); }
            25%  { transform: translateX(4px); }
            50%  { transform: translateX(-4px); }
            75%  { transform: translateX(2px); }
            100% { transform: translateX(0); opacity: 0; }
        }

        /* Counter Animation Highlight */
        .count-up-num {
            display: inline-block;
            font-variant-numeric: tabular-nums;
            transition: color 0.3s;
        }
        .count-up-num.counting {
            color: var(--gold);
            text-shadow: 0 0 20px rgba(238,202,62,0.5);
        }

        /* Spotlight Card Effect */
        .omnix-spotlight-card {
            position: relative;
            overflow: hidden;
        }
        .omnix-spotlight-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle 200px at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(238,202,62,0.12) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 1;
        }
        .omnix-spotlight-card:hover::before {
            opacity: 1;
        }

        /* Omnix Reveal for Cards */
        .omnix-reveal {
            opacity: 0;
            transform: translateY(45px) scale(0.96);
            transition: opacity 0.75s cubic-bezier(0.16,1,0.3,1),
                        transform 0.75s cubic-bezier(0.16,1,0.3,1);
        }
        .omnix-reveal.is-revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* Horizontal Scan Line & Glowing Parallax Blobs — تم إلغاؤها لمنع التشويش البصري */
        .section-scan-line,
        .scroll-orb,
        .kinetic-divider {
            display: none !important;
        }

        /* Staggered Number Odometer Effect */
        @keyframes digitFlip {
            0%   { transform: translateY(-100%); opacity: 0; }
            100% { transform: translateY(0);     opacity: 1; }
        }
        .digit-reveal {
            display: inline-block;
            animation: digitFlip 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* Section entrance with wave of cards */
        @keyframes cardWaveIn {
            0%   { opacity: 0; transform: translateY(50px) rotateX(20deg); }
            100% { opacity: 1; transform: translateY(0) rotateX(0deg); }
        }
        .card-wave-in {
            animation: cardWaveIn 0.7s cubic-bezier(0.16,1,0.3,1) both;
            perspective: 1000px;
        }

        /* Pulsing Border on Hover for Sponsor Cards */
        .podium-sponsor-card,
        .strategic-hero-showcase {
            position: relative;
            transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        .podium-sponsor-card:hover {
            transform: translateY(-6px);
        }

        /* Text Shimmer on Hero Title */
        @keyframes textShimmer {
            0%   { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .shimmer-text {
            background: linear-gradient(90deg, var(--gold) 0%, #fff 40%, var(--gold-lt) 60%, var(--gold) 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShimmer 4s linear infinite;
        }

        /* ── CURSOR FOLLOWER ── */
        #cursor-glow {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238,202,62,0.06) 0%, transparent 70%);
            pointer-events: none;
            z-index: 9998;
            transform: translate(-50%, -50%);
            transition: left 0.12s ease, top 0.12s ease;
            will-change: left, top;
        }

        /* Section Number Counter Pill */
        .stat-counter-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(238,202,62,0.1);
            border: 1px solid rgba(238,202,62,0.3);
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-size: 0.9rem;
            color: var(--gold);
            margin-bottom: 1.5rem;
        }

    </style>
</head>

<body>

    {{-- ══ SCROLL PROGRESS BAR ══ --}}
    <div id="scroll-progress-bar"></div>

    {{-- ══ CURSOR GLOW FOLLOWER ══ --}}
    <div id="cursor-glow"></div>

    {{-- ══════════════════════════════════
    TOP NAV
    ══════════════════════════════════ --}}
    <nav class="top-nav" id="topNav">
        <a href="{{ route('home') }}" class="nav-brand">
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
            <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات"
                class="waha-nav-logo d-none d-sm-block"
                title="شركة الواحة لتنظيم المعارض والمؤتمرات — الراعي الاستراتيجي"
                onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
        </a>


        <div class="nav-links">
            @auth
                <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-outline">
                    <i class="fas fa-th-large me-1"></i>لوحة التحكم
                </a>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('job-fair.admin.index') }}" class="nav-btn nav-btn-gold">
                        <i class="fas fa-calendar-check me-1"></i>إدارة الفعاليات
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">تسجيل الدخول</a>
                <a href="{{ route('graduate.register') }}" class="nav-btn nav-btn-gold d-none d-sm-inline-flex">
                    <i class="fas fa-user-plus me-1"></i>سجّل كخريج
                </a>
                @if(isset($fair) && $fair)
                    <button type="button" class="nav-btn nav-btn-outline d-none d-md-inline-flex" data-bs-toggle="modal"
                        data-bs-target="#visitorRegisterModal">
                        <i class="fas fa-id-badge" style="color:var(--gold)"></i>
                        <span>تسجيل زائر</span>
                    </button>
                @endif
            @endauth
        </div>
    </nav>

    {{-- ══════════════════════════════════
    MAIN CONTENT
    ══════════════════════════════════ --}}

    @if($fair)

        {{-- HERO --}}
        <section class="hero" id="hero">
            <div class="hero-bg-layer"></div>
            <div class="hero-grid"></div>

            {{-- Orbs --}}
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>

            {{-- Particles --}}
            <div id="particles-container"></div>

            <div class="container position-relative z-10" style="padding-top: 80px;">
                <div class="row align-items-center mb-4">
                    {{-- Text Side --}}
                    <div class="col-lg-6 hero-text-col text-lg-end text-center mt-4 mt-lg-0 order-1 order-lg-1">

                        {{-- Eyebrow --}}
                        <div class="hero-eyebrow">
                            <span class="dot"></span>
                            {{ $fair->subtitle ?: ($fair->title ?? 'فعالية معتمدة') }}
                            <span class="dot"></span>
                        </div>

                        @php
                            $fullFairTitle = $fair->title ?? 'معرض التوظيف 2026 جامعة طرابلس';
                            // فصل ذكي ومتقن لضمان بقاء "معرض التوظيف 2026" في سطر واحد بدون انكسار، و"جامعة طرابلس" في سطر متناسق
                            if (preg_match('/^(.*?20\d\d)(.*)$/u', $fullFairTitle, $m)) {
                                $mainFairTitle = trim($m[1]);
                                $subFairTitle = trim($m[2], " -–\t\n\r\0\x0B");
                            } elseif (str_contains($fullFairTitle, 'جامعة طرابلس')) {
                                $parts = explode('جامعة طرابلس', $fullFairTitle);
                                $mainFairTitle = trim($parts[0], " -–\t\n\r\0\x0B");
                                $subFairTitle = 'جامعة طرابلس';
                            } else {
                                $mainFairTitle = $fullFairTitle;
                                $subFairTitle = '';
                            }
                        @endphp

                        {{-- Title (Solid & Clean without scramble) --}}
                        <h1 class="hero-title">
                            <span class="hero-title-main">{{ $mainFairTitle }}</span>
                            @if(!empty($subFairTitle))
                                <span class="hero-title-sub">{{ $subFairTitle }}</span>
                            @endif
                        </h1>

                        @php
                            $heroSubtitleText = $fair->subtitle ?: 'انطلق نحو مستقبلك المهني — فرصتك الذهبية تبدأ هنا';
                        @endphp
                        <p class="hero-subtitle">
                            <span id="scrambleSubtitle" data-text="{{ $heroSubtitleText }}" style="cursor: pointer;" title="انقر لإعادة فك التشفير">{{ $heroSubtitleText }}</span>
                            <span class="scramble-cursor" id="scrambleCursor"></span>
                        </p>

                        {{-- Event Pills --}}
                        <div class="event-pills justify-content-lg-start justify-content-center">
                            <span class="event-pill">
                                <i class="fas fa-calendar-alt"></i>
                                @php
                                    try {
                                        $eventDateText = \Carbon\Carbon::parse($fair->event_date)->locale('ar')->translatedFormat('j F Y');
                                    } catch (\Throwable $e) {
                                        $eventDateText = \Carbon\Carbon::parse($fair->event_date)->format('Y-m-d');
                                    }
                                @endphp
                                {{ $eventDateText }}
                            </span>
                            @if($fair->start_time)
                                <span class="event-pill">
                                    <i class="fas fa-clock"></i>
                                    {{ \Str::substr($fair->start_time, 0, 5) }}
                                    @if($fair->end_time) — {{ \Str::substr($fair->end_time, 0, 5) }} @endif
                                </span>
                            @endif
                            <span class="event-pill">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $fair->location }}
                            </span>
                        </div>

                        {{-- Countdown --}}
                        @if($fair->is_upcoming)
                            @php
                                $fairTime = $fair->start_time ?: '09:00:00';
                                $eventTargetCarbon = \Carbon\Carbon::parse($fair->event_date->format('Y-m-d') . ' ' . substr($fairTime, 0, 5) . ':00');
                                $diffSecs = max(0, \Carbon\Carbon::now()->diffInSeconds($eventTargetCarbon, false));
                                $initD = floor($diffSecs / 86400);
                                $initH = floor(($diffSecs % 86400) / 3600);
                                $initM = floor(($diffSecs % 3600) / 60);
                                $initS = $diffSecs % 60;
                                $targetIso = $eventTargetCarbon->toIso8601String();
                            @endphp
                            <div class="d-flex justify-content-lg-start justify-content-center">
                                <div class="countdown-row justify-content-center m-0" id="countdown">
                                    <div class="cd-box">
                                        <div class="cd-num" id="cd-d">{{ sprintf('%02d', $initD) }}</div>
                                        <div class="cd-lbl">يوم</div>
                                    </div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box">
                                        <div class="cd-num" id="cd-h">{{ sprintf('%02d', $initH) }}</div>
                                        <div class="cd-lbl">ساعة</div>
                                    </div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box">
                                        <div class="cd-num" id="cd-m">{{ sprintf('%02d', $initM) }}</div>
                                        <div class="cd-lbl">دقيقة</div>
                                    </div>
                                    <div class="cd-sep">:</div>
                                    <div class="cd-box">
                                        <div class="cd-num" id="cd-s">{{ sprintf('%02d', $initS) }}</div>
                                        <div class="cd-lbl">ثانية</div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                (function () {
                                    var targetDate = new Date("{{ $targetIso }}");
                                    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }
                                    function tick() {
                                        var now = new Date();
                                        var diff = targetDate.getTime() - now.getTime();
                                        if (diff <= 0) {
                                            var el = document.getElementById('countdown');
                                            if (el) el.innerHTML = '<span class="badge bg-success py-2 px-3 fs-6 rounded-pill"><i class="fas fa-check-circle me-1"></i> الحدث جارٍ الآن!</span>';
                                            return;
                                        }
                                        var d = Math.floor(diff / (1000 * 60 * 60 * 24));
                                        var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                        var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                        var s = Math.floor((diff % (1000 * 60)) / 1000);
                                        var elD = document.getElementById('cd-d');
                                        var elH = document.getElementById('cd-h');
                                        var elM = document.getElementById('cd-m');
                                        var elS = document.getElementById('cd-s');
                                        if (elD) elD.textContent = pad(d);
                                        if (elH) elH.textContent = pad(h);
                                        if (elM) elM.textContent = pad(m);
                                        if (elS) elS.textContent = pad(s);
                                    }
                                    tick();
                                    setInterval(tick, 1000);
                                })();
                            </script>
                        @else
                            <div class="d-flex justify-content-lg-start justify-content-center mb-4">
                                <span
                                    style="background: rgba(16,185,129,0.2); border: 1.5px solid rgba(16,185,129,0.5); color: #6EE7B7; padding: 10px 28px; border-radius: 50px; font-weight: 700; font-size: 1rem">
                                    <i class="fas fa-circle me-2" style="animation: blink 1s infinite; font-size: 0.6rem"></i>
                                    الحدث جارٍ الآن
                                </span>
                            </div>
                        @endif

                        {{-- CTA Buttons --}}
                        <div class="hero-action-buttons justify-content-lg-start justify-content-center">
                            @auth
                                @if(isset($myRegistration) && $myRegistration)
                                    <a href="{{ route('job-fair.my-ticket', $myRegistration->id) }}" class="nav-btn nav-btn-gold">
                                        <i class="fas fa-qrcode"></i>
                                        <span>عرض بطاقتي الرقمية</span>
                                    </a>
                                @elseif($fair->can_register)
                                    <button class="nav-btn nav-btn-gold" data-bs-toggle="modal" data-bs-target="#registerModal">
                                        <i class="fas fa-user-plus"></i>
                                        <span>سجّل الآن كخريج</span>
                                    </button>
                                @endif
                            @else
                                @if($fair->can_register)
                                    <a href="{{ route('login') }}" class="nav-btn nav-btn-gold">
                                        <i class="fas fa-user-plus"></i>
                                        <span>سجّل الآن كخريج</span>
                                    </a>
                                @endif
                            @endauth

                            @if(isset($fair) && $fair)
                                <button type="button" class="nav-btn nav-btn-outline" data-bs-toggle="modal"
                                    data-bs-target="#visitorRegisterModal">
                                    <i class="fas fa-id-badge" style="color:var(--gold)"></i>
                                    <span>تسجيل زائر / ضيف</span>
                                </button>
                            @endif
                            <a href="{{ route('job-fair.public.companies', $fair->id) }}" class="nav-btn nav-btn-outline">
                                <i class="fas fa-building" style="color:var(--gold)"></i>
                                <span>دليل الشركات</span>
                            </a>
                            <a href="{{ route('job-fair.public.program', $fair->id) }}" class="nav-btn nav-btn-outline">
                                <i class="fas fa-graduation-cap" style="color:var(--gold)"></i>
                                <span>البرنامج العلمي</span>
                            </a>
                            <a href="{{ route('job-fair.public.projects', $fair->id) }}" class="nav-btn nav-btn-outline">
                                <i class="fas fa-lightbulb" style="color:var(--gold)"></i>
                                <span>مشاريع التخرج</span>
                            </a>
                        </div>
                    </div>

                    {{-- Visual Side (Premium 3D Tilted Cap & Glass Logo Plaque) --}}
                    <div class="col-lg-6 position-relative order-2 order-lg-2 mb-4 mb-lg-0">
                        <div class="premium-3d-composition">
                            <div class="premium-glow"></div>

                            {{-- 3D Tilted Glass Graduation Cap perched atop the plaque --}}
                            <div class="hero-3d-cap-wrapper">
                                <img src="{{ asset('images/tilted_glass_graduation_cap.png') }}"
                                    alt="قبعة التخرج الزجاجية ثلاثية الأبعاد" class="hero-3d-cap-img">
                            </div>

                            {{-- Illuminated Frosted Glass Plaque with Job Fair Logo underneath the cap --}}
                            <div class="premium-glass-card">
                                @if($fair)
                                    <img src="{{ $fair->logo_url }}" class="premium-logo-img" alt="{{ $fair->title }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                                @else
                                    <img src="{{ asset('images/job_fair_logo.png') }}" class="premium-logo-img"
                                        alt="شعار المعرض">
                                @endif
                            </div>

                            {{-- Floating Depth Elements --}}
                            <div class="float-element shape-1" title="وظائف وفرص عمل"><i class="fas fa-briefcase"></i></div>
                            <div class="float-element shape-4" title="شركات ومؤسسات رائدة"><i class="fas fa-building"></i></div>
                            <div class="float-element shape-3">1000+ فرصة</div>
                            <div class="float-element shape-2" title="خريجون وكفاءات"><i class="fas fa-user-graduate"></i></div>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="row pb-3">
                    <div class="col-12">
                        <div class="hero-stats" style="max-width: 900px; margin: 0 auto;">
                            <div class="hero-stat">
                                <div class="hero-stat-num">{{ $stats['total_registered'] ?? 0 }}</div>
                                <div class="hero-stat-lbl">خريج مسجّل</div>
                            </div>
                            <div class="hero-stat-divider"></div>
                            <div class="hero-stat">
                                <div class="hero-stat-num">{{ $stats['total_visitors'] ?? 0 }}</div>
                                <div class="hero-stat-lbl">زائر مسجّل</div>
                            </div>
                            <div class="hero-stat-divider"></div>
                            <div class="hero-stat">
                                <div class="hero-stat-num">{{ $stats['total_companies'] ?? 0 }}</div>
                                <div class="hero-stat-lbl">شركة مشاركة</div>
                            </div>
                            @if(($stats['total_attended'] ?? 0) > 0)
                                <div class="hero-stat-divider"></div>
                                <div class="hero-stat">
                                    <div class="hero-stat-num">{{ $stats['total_attended'] }}</div>
                                    <div class="hero-stat-lbl">حضور الفعالية</div>
                                </div>
                            @endif
                            @if($fair->is_upcoming)
                                <div class="hero-stat-divider"></div>
                                <div class="hero-stat">
                                    <div class="hero-stat-num">{{ $stats['days_remaining'] ?? 0 }}</div>
                                    <div class="hero-stat-lbl">يوم متبقي</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- Partners Strip & Strategic Sponsor --}}
                {{-- شعار المكتب وشعار الواحة فقط أسفل الشاشة الزرقاء --}}
                <div class="row pb-4">
                    <div class="col-12 text-center">
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-4 gap-md-5 mt-2">
                            <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين — جامعة طرابلس"
                                class="hero-strip-logo hero-strip-logo-office" title="مكتب تدريب الخريجين — جامعة طرابلس"
                                onerror="this.src='{{ asset('images/uni_logo_white.png') }}'">
                            <div class="hero-strip-divider"></div>
                            <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}"
                                alt="شركة الواحة لتنظيم المعارض والمؤتمرات" class="hero-strip-logo"
                                title="شركة الواحة لتنظيم المعارض والمؤتمرات — الراعي الاستراتيجي"
                                onerror="this.src='{{ asset('images/wahaexpo_horizontal_gold.png') }}'">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SPONSORS SECTION (الشركاء والرعاة) --}}
        <section class="sponsors-section cyber-matrix-wrap" id="sponsors">
            <div class="container position-relative z-2">
                <div class="text-center mb-5">
                    <div class="cyber-section-badge">
                        <i class="fas fa-crown text-warning"></i>
                        <span>شركاء النجاح والرعاية</span>
                    </div>
                    <h2 class="cyber-section-title scramble-on-scroll" data-scramble="الشركاء والرعاة" title="انقر لإعادة فك التشفير">الشركاء والرعاة</h2>
                    <p class="cyber-section-subtitle">
                        نفتخر برعاية ودعم نخبة المؤسسات الوطنية الرائدة لمسيرة تمكين وتوظيف الكفاءات الوطنية الشابة
                    </p>
                </div>

                <div class="sponsors-showcase-wrap">
                    @php
                        $strategicSponsor = isset($sponsors) ? $sponsors->first(function($s) {
                            $k = strtolower(trim((string)$s->tier));
                            return in_array($k, ['strategic', 'الراعي الاستراتيجي']);
                        }) : null;

                        $otherSponsors = isset($sponsors) ? $sponsors->filter(function($s) {
                            $k = strtolower(trim((string)$s->tier));
                            return !in_array($k, ['strategic', 'الراعي الاستراتيجي']);
                        }) : collect([]);

                        $stratName = $strategicSponsor ? $strategicSponsor->name : 'شركة الواحة لتنظيم المعارض والمؤتمرات';
                        $stratDesc = $strategicSponsor && $strategicSponsor->description ? $strategicSponsor->description : 'الشريك والراعي الاستراتيجي لمعرض التوظيف السنوي بجامعة طرابلس، الرائد في صناعة وتنظيم كبرى الفعاليات والمعارض والملتقيات المهنية الوطنية.';
                        $stratWebsite = $strategicSponsor && $strategicSponsor->website ? $strategicSponsor->website : 'https://ly.linkedin.com/company/wahaexpo';
                        $stratLogo = $strategicSponsor && $strategicSponsor->logo_path ? Storage::url($strategicSponsor->logo_path) : asset('images/wahaexpo_horizontal_white.png');
                    @endphp

                    {{-- 1. البانر الرئيسي للراعي الاستراتيجي (Strategic Hero Showcase) --}}
                    <div class="strategic-hero-showcase">
                        <div class="strategic-hero-badge-strip">
                            <i class="fas fa-crown text-warning"></i>
                            <span>الراعي والشريك الاستراتيجي</span>
                        </div>

                        <div class="strategic-hero-grid">
                            <div class="strategic-logo-pod">
                                <img src="{{ $stratLogo }}"
                                     alt="{{ $stratName }}"
                                     onerror="this.src='{{ asset('images/wahaexpo_horizontal_white.png') }}';">
                            </div>

                            <div>
                                <h3 class="strategic-hero-title">{{ $stratName }}</h3>
                                <p class="strategic-hero-desc">
                                    {{ $stratDesc }}
                                </p>
                                <div class="strategic-hero-actions-row">
                                    <a href="{{ $stratWebsite }}" target="_blank" rel="noopener noreferrer"
                                       class="strategic-gold-btn">
                                        <span>زيارة منصة الشركة الرسمية</span>
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <span class="strategic-status-pill">
                                        <i class="fas fa-handshake text-info"></i>
                                        <span>شراكة رائدة لتنظيم كبرى الفعاليات المهنية</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. بوديوم الرعاة المعتمدين (Diamond / Gold / Silver) --}}
                    @if($otherSponsors->count() > 0)
                        <div class="podium-divider-wrap">
                            <div class="podium-divider-line"></div>
                            <div class="podium-divider-pill">
                                <i class="fas fa-layer-group text-info"></i>
                                <span>الرعاة والشركاء المعتمدون</span>
                            </div>
                            <div class="podium-divider-line"></div>
                        </div>

                        <div class="sponsors-podium-grid">
                            @foreach($otherSponsors as $sponsor)
                                @php
                                    $tKey = strtolower(trim((string)$sponsor->tier));
                                    $tierClass = match($tKey) {
                                        'diamond', 'الراعي الماسي', 'platinum', 'الراعي البلاتيني' => 'tier-diamond',
                                        'gold', 'الراعي الذهبي' => 'tier-gold',
                                        default => 'tier-silver',
                                    };
                                    $tierIcon = match($tKey) {
                                        'diamond', 'الراعي الماسي', 'platinum', 'الراعي البلاتيني' => 'fas fa-gem',
                                        'gold', 'الراعي الذهبي' => 'fas fa-award',
                                        default => 'fas fa-star',
                                    };
                                    $tierLbl = $sponsor->tier_label ?: match($tKey) {
                                        'diamond', 'الراعي الماسي' => 'الراعي الماسي',
                                        'platinum', 'الراعي البلاتيني' => 'الراعي البلاتيني',
                                        'gold', 'الراعي الذهبي' => 'الراعي الذهبي',
                                        'bronze', 'الراعي البرونزي' => 'الراعي البرونزي',
                                        default => 'الراعي الفضي',
                                    };
                                    $monogramLetter = mb_substr(trim($sponsor->name), 0, 1, 'UTF-8');
                                @endphp

                                <div class="podium-sponsor-card {{ $tierClass }}">
                                    <div class="sponsor-tier-badge {{ $tierClass }}">
                                        <i class="{{ $tierIcon }}"></i>
                                        <span>{{ $tierLbl }}</span>
                                    </div>

                                    <div class="sponsor-identity-hub">
                                        @if($sponsor->logo_path)
                                            <div class="sponsor-image-dock">
                                                <img src="{{ Storage::url($sponsor->logo_path) }}" alt="{{ $sponsor->name }}" loading="lazy"
                                                     onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                            </div>
                                            <div class="sponsor-crest-emblem {{ $tierClass }}" style="display:none;">
                                                <span class="crest-monogram">{{ $monogramLetter }}</span>
                                                <i class="{{ $tierIcon }} crest-subicon"></i>
                                            </div>
                                        @else
                                            <div class="sponsor-crest-emblem {{ $tierClass }}">
                                                <span class="crest-monogram">{{ $monogramLetter }}</span>
                                                <i class="{{ $tierIcon }} crest-subicon"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <h3 class="podium-sponsor-name">{{ $sponsor->name }}</h3>
                                    <p class="podium-sponsor-desc">
                                        {{ $sponsor->description ?: 'شريك رعاية متميز داعم لمسارات التوظيف وتمكين الكفاءات الوطنية الشابة.' }}
                                    </p>

                                    <div class="podium-sponsor-action">
                                        @if($sponsor->website)
                                            <a href="{{ Str::startsWith($sponsor->website, 'http') ? $sponsor->website : 'https://' . $sponsor->website }}"
                                               target="_blank" rel="noopener noreferrer"
                                               class="podium-card-btn">
                                                <span>زيارة المنصة</span>
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <div class="podium-verified-pill">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>شريك رعاية معتمد</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- COMPANIES SECTION (Interactive Expo Booths) --}}
        @if($companies->count() > 0)
            @php
                $totalComp = $companies->count();
                if ($totalComp === 1) {
                    $countText = 'شركة رائدة واحدة تتواجد';
                } elseif ($totalComp === 2) {
                    $countText = 'شركتان رائدتان تتواجدان';
                } elseif ($totalComp >= 3 && $totalComp <= 10) {
                    $countText = "{$totalComp} شركات رائدة تتواجد";
                } else {
                    $countText = "{$totalComp} شركة رائدة تتواجد";
                }
                // تحضير ألوان الهوية الخاصة ببطاقات المعرض المستوحاة من الطابع السيبراني الملكي
                $cardColors = [
                    '56, 189, 248',   // Cyber Cyan (#38bdf8)
                    '238, 202, 62',   // Royal Gold (#eeca3e)
                    '16, 185, 129',   // Emerald Green (#10b981)
                    '129, 140, 248',  // Electric Indigo (#818cf8)
                    '245, 158, 11',   // Warm Amber (#f59e0b)
                    '45, 212, 191',   // Neon Teal (#2dd4bf)
                    '168, 85, 247',   // Vivid Purple (#a855f7)
                    '244, 114, 182',  // Cyber Rose (#f472b6)
                ];

                // للأسطوانة ثلاثية الأبعاد: ضمان اكتمال الدائرة بحد أدنى 8 بطاقات لتعطي انطباعاً أسطوانياً مكتملاً
                $cylinderCompanies = collect();
                $baseCompCount = $companies->count();
                if ($baseCompCount > 0) {
                    $targetCylinderQty = ($baseCompCount >= 8) ? min($baseCompCount, 12) : 8;
                    for ($ci = 0; $ci < $targetCylinderQty; $ci++) {
                        $cylinderCompanies->push($companies[$ci % $baseCompCount]);
                    }
                }
                $cylinderQty = $cylinderCompanies->count();

                // عرض معاينة لأول 12 شركة لإبقاء الصفحة الرئيسية خفيفة وسريعة التصفح
                $previewCompanies = $companies->take(12);
            @endphp
            <section class="companies-section cyber-matrix-wrap" id="companies">
                <div class="container position-relative z-2">
                    <div class="text-center mb-4">
                        <div class="cyber-section-badge"><i class="fas fa-building me-1"></i>الشركات والمؤسسات المشاركة</div>
                        <h2 class="cyber-section-title scramble-on-scroll" data-scramble="أجنحة الشركات المشاركة" title="انقر لإعادة فك التشفير">أجنحة الشركات المشاركة</h2>
                        <p class="cyber-section-subtitle">{{ $countText }} في المعرض — انقر على أي جناح للتعرف على فرصه وتفاصيله
                        </p>
                    </div>

                    {{-- مشهد الأسطوانة ثلاثية الأبعاد (3D Cylinder Showcase) --}}
                    <div class="cyber-cylinder-scene" id="companiesCylinderScene">
                        {{-- شريط التحكم الذكي والتبديل بين العرض الأسطواني 3D وعرض الشبكة --}}
                        <div class="cylinder-controls-bar">
                            <div class="cylinder-view-switch">
                                <button type="button" class="view-switch-btn active" id="btnView3d" title="عرض أسطواني ثلاثي الأبعاد">
                                    <i class="fas fa-cube me-1"></i> استعراض ثلاثي الأبعاد (3D)
                                </button>
                                <button type="button" class="view-switch-btn" id="btnViewGrid" title="عرض الشبكة المنظمة">
                                    <i class="fas fa-th-large me-1"></i> عرض الشبكة
                                </button>
                            </div>

                            <div class="cylinder-playback-controls" id="cylinderPlaybackControls">
                                <button type="button" class="cylinder-step-btn" id="btnCylinderPrev" title="تدوير لليمين">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button type="button" class="cylinder-play-btn" id="btnCylinderPause" title="إيقاف / تشغيل الدوران">
                                    <i class="fas fa-pause" id="cylinderPlayIcon"></i>
                                    <span id="cylinderPlayText">إيقاف مؤقت</span>
                                </button>
                                <button type="button" class="cylinder-step-btn" id="btnCylinderNext" title="تدوير لليسار">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>

                        {{-- مسرح الأسطوانة ثلاثية الأبعاد 3D --}}
                        <div class="cyber-cylinder-wrapper" id="companiesCylinderWrapper">
                            <div class="cylinder-floor-glow"></div>
                            <div class="cyber-cylinder-inner" id="companiesCylinderInner" style="--quantity: {{ $cylinderQty }};">
                                @foreach($cylinderCompanies as $cIndex => $fc)
                                    @php
                                        $colorCard = $cardColors[$cIndex % count($cardColors)];
                                        $companyLogo = $fc->company->logo_path ? Storage::url($fc->company->logo_path) : ($fc->company->logo ? Storage::url($fc->company->logo) : '');
                                        $companyName = $fc->company->name ?? 'شركة مشاركة';
                                        $companyDesc = $fc->company->description ?: ('جهة رائدة مشاركة في فعاليات ' . ($fair->title ?? 'المعرض والملتقى') . ' لتوفير أفضل الفرص المهنية والتدريبية للكفاءات الوطنية.');
                                        $pos = (int) ($fc->available_positions ?? 0);
                                        $industry = $fc->company->industry ?? 'قطاع الأعمال والخدمات';

                                        if (!$companyLogo && (str_contains(mb_strtolower($companyName, 'UTF-8'), 'ليبيانا') || str_contains(mb_strtolower($companyName, 'UTF-8'), 'libyana'))) {
                                            $companyLogo = asset('images/libyana_logo.png');
                                        }

                                        $cleanName = preg_replace('/^(شركة|مؤسسة|مجموعة|مصرف|هيئة|منظمة)\s+/u', '', trim($companyName));
                                        if (preg_match('/([A-Za-z0-9]{2,6})/u', $companyName, $matches)) {
                                            $crestText = strtoupper($matches[1]);
                                        } else {
                                            $words = preg_split('/\s+/u', $cleanName, -1, PREG_SPLIT_NO_EMPTY);
                                            if (!empty($words) && mb_strlen($words[0], 'UTF-8') <= 7) {
                                                $crestText = $words[0];
                                            } else {
                                                $crestText = mb_substr($cleanName, 0, 3, 'UTF-8');
                                            }
                                        }

                                        $indLower = mb_strtolower($industry . ' ' . $companyName, 'UTF-8');
                                        $crestIcon = 'fa-building';
                                        if (str_contains($indLower, 'اتصالات') || str_contains($indLower, 'شبكات') || str_contains($indLower, 'هاتف') || str_contains($indLower, 'مدار') || str_contains($indLower, 'ليبيانا') || str_contains($indLower, 'ltt')) {
                                            $crestIcon = 'fa-tower-broadcast';
                                        } elseif (str_contains($indLower, 'تقني') || str_contains($indLower, 'برمج') || str_contains($indLower, 'ذكاء') || str_contains($indLower, 'معلومات') || str_contains($indLower, 'حاسوب')) {
                                            $crestIcon = 'fa-microchip';
                                        } elseif (str_contains($indLower, 'نفط') || str_contains($indLower, 'طاقة') || str_contains($indLower, 'غاز')) {
                                            $crestIcon = 'fa-gas-pump';
                                        } elseif (str_contains($indLower, 'مال') || str_contains($indLower, 'مصرف') || str_contains($indLower, 'بنك') || str_contains($indLower, 'استثمار')) {
                                            $crestIcon = 'fa-landmark';
                                        } elseif (str_contains($indLower, 'تعليم') || str_contains($indLower, 'تدريب') || str_contains($indLower, 'أكاديم')) {
                                            $crestIcon = 'fa-graduation-cap';
                                        }
                                    @endphp
                                    <div role="button" tabindex="0" class="cyber-cylinder-card"
                                        style="--index: {{ $cIndex }}; --color-card: {{ $colorCard }};"
                                        data-bs-toggle="modal" data-bs-target="#companyDetailModal"
                                        data-name="{{ $companyName }}" data-logo="{{ $companyLogo }}"
                                        data-industry="{{ $industry }}"
                                        data-booth="{{ $fc->booth_number ? strtoupper($fc->booth_number) : '' }}"
                                        data-jobs="{{ $pos }}" data-desc="{{ $companyDesc }}"
                                        data-website="{{ $fc->company->website ?? '' }}"
                                        data-email="{{ $fc->company->email ?? '' }}"
                                        data-phone="{{ $fc->company->phone ?? '' }}"
                                        title="انقر للاطلاع على تفاصيل جناح {{ $companyName }}">

                                        {{-- شريط الشارات العلوي --}}
                                        <div class="cylinder-card-header">
                                            @if($fc->booth_number)
                                                <span class="cylinder-tag-number">
                                                    <i class="fas fa-store"></i>
                                                    <span>جناح {{ strtoupper($fc->booth_number) }}</span>
                                                </span>
                                            @else
                                                <span class="cylinder-tag-number">
                                                    <i class="fas fa-store"></i>
                                                    <span>جناح بالمعرض</span>
                                                </span>
                                            @endif

                                            @if($pos > 0)
                                                @php
                                                    $posText = ($pos == 1 ? 'فرصة واحدة' : ($pos == 2 ? 'فرصتان' : ($pos <= 10 ? $pos . ' فرص' : $pos . ' فرصة')));
                                                @endphp
                                                <span class="cylinder-tag-jobs is-hiring">
                                                    <span class="pulse-glow-dot"></span>
                                                    <span>{{ $posText }}</span>
                                                </span>
                                            @else
                                                <span class="cylinder-tag-jobs is-intro">
                                                    <i class="fas fa-handshake"></i>
                                                    <span>تعريفي</span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- مساحة الشعار أو الوسام --}}
                                        <div class="cylinder-identity-hub">
                                            @if($companyLogo)
                                                <div class="cylinder-logo-dock">
                                                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}" loading="lazy"
                                                        onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                                </div>
                                                <div class="cylinder-crest-emblem" style="display:none;">
                                                    <span class="crest-monogram-label">{{ $crestText }}</span>
                                                    <i class="fas {{ $crestIcon }} crest-sub-icon"></i>
                                                </div>
                                            @else
                                                <div class="cylinder-crest-emblem">
                                                    <span class="crest-monogram-label">{{ $crestText }}</span>
                                                    <i class="fas {{ $crestIcon }} crest-sub-icon"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- تفاصيل الشركة --}}
                                        <div class="cylinder-content-body">
                                            <h4 class="cylinder-company-title" title="{{ $companyName }}">
                                                {{ $companyName }}
                                            </h4>
                                            <div class="cylinder-sector-badge" title="{{ $industry }}">
                                                <i class="fas {{ $crestIcon }} me-1"></i>
                                                <span>{{ $industry }}</span>
                                            </div>
                                        </div>

                                        {{-- زر الاستكشاف السفلي --}}
                                        <div class="cylinder-action-cta">
                                            <span>استكشاف الجناح</span>
                                            <div class="cylinder-action-arrow">
                                                <i class="fas fa-arrow-left"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- شبكة البطاقات المنظمة (يمكن التبديل إليها بنقرة واحدة) --}}
                    <div class="companies-booth-grid d-none" id="companiesGridWrapper">
                        @foreach($previewCompanies as $fc)
                            @php
                                $companyLogo = $fc->company->logo_path ? Storage::url($fc->company->logo_path) : ($fc->company->logo ? Storage::url($fc->company->logo) : '');
                                $companyName = $fc->company->name ?? 'شركة مشاركة';
                                $companyDesc = $fc->company->description ?: ('جهة رائدة مشاركة في فعاليات ' . ($fair->title ?? 'المعرض والملتقى') . ' لتوفير أفضل الفرص المهنية والتدريبية للكفاءات الوطنية.');
                                $pos = (int) ($fc->available_positions ?? 0);
                                $industry = $fc->company->industry ?? 'قطاع الأعمال والخدمات';

                                // توفير شعار ليبيانا التلقائي في حال عدم وجود ملف شعار مرفوع
                                if (!$companyLogo && (str_contains(mb_strtolower($companyName, 'UTF-8'), 'ليبيانا') || str_contains(mb_strtolower($companyName, 'UTF-8'), 'libyana'))) {
                                    $companyLogo = asset('images/libyana_logo.png');
                                }

                                // استخراج رمز شرفي ذكي ومخصص للشركة في حال عدم وجود شعار مخصص
                                $cleanName = preg_replace('/^(شركة|مؤسسة|مجموعة|مصرف|هيئة|منظمة)\s+/u', '', trim($companyName));
                                if (preg_match('/([A-Za-z0-9]{2,6})/u', $companyName, $matches)) {
                                    $crestText = strtoupper($matches[1]);
                                } else {
                                    $words = preg_split('/\s+/u', $cleanName, -1, PREG_SPLIT_NO_EMPTY);
                                    if (!empty($words) && mb_strlen($words[0], 'UTF-8') <= 7) {
                                        $crestText = $words[0];
                                    } else {
                                        $crestText = mb_substr($cleanName, 0, 3, 'UTF-8');
                                    }
                                }

                                // أيقونة ذكية متوافقة مع قطاع الشركة وطبيعة عملها
                                $indLower = mb_strtolower($industry . ' ' . $companyName, 'UTF-8');
                                $crestIcon = 'fa-building';
                                if (str_contains($indLower, 'اتصالات') || str_contains($indLower, 'شبكات') || str_contains($indLower, 'هاتف') || str_contains($indLower, 'مدار') || str_contains($indLower, 'ليبيانا') || str_contains($indLower, 'ltt')) {
                                    $crestIcon = 'fa-tower-broadcast';
                                } elseif (str_contains($indLower, 'تقني') || str_contains($indLower, 'برمج') || str_contains($indLower, 'ذكاء') || str_contains($indLower, 'معلومات') || str_contains($indLower, 'حاسوب')) {
                                    $crestIcon = 'fa-microchip';
                                } elseif (str_contains($indLower, 'نفط') || str_contains($indLower, 'طاقة') || str_contains($indLower, 'غاز')) {
                                    $crestIcon = 'fa-gas-pump';
                                } elseif (str_contains($indLower, 'مال') || str_contains($indLower, 'مصرف') || str_contains($indLower, 'بنك') || str_contains($indLower, 'استثمار')) {
                                    $crestIcon = 'fa-landmark';
                                } elseif (str_contains($indLower, 'تعليم') || str_contains($indLower, 'تدريب') || str_contains($indLower, 'أكاديم')) {
                                    $crestIcon = 'fa-graduation-cap';
                                }
                            @endphp
                            <div role="button" tabindex="0" class="company-booth-card" data-bs-toggle="modal"
                                data-bs-target="#companyDetailModal" data-name="{{ $companyName }}" data-logo="{{ $companyLogo }}"
                                data-industry="{{ $industry }}"
                                data-booth="{{ $fc->booth_number ? strtoupper($fc->booth_number) : '' }}" data-jobs="{{ $pos }}"
                                data-desc="{{ $companyDesc }}" data-website="{{ $fc->company->website ?? '' }}"
                                data-email="{{ $fc->company->email ?? '' }}" data-phone="{{ $fc->company->phone ?? '' }}"
                                title="انقر للاطلاع على تفاصيل جناح {{ $companyName }}">

                                {{-- شريط الشارات العلوي --}}
                                <div class="booth-card-header">
                                    @if($fc->booth_number)
                                        <span class="booth-tag-number">
                                            <i class="fas fa-store"></i>
                                            <span>جناح {{ strtoupper($fc->booth_number) }}</span>
                                        </span>
                                    @else
                                        <span class="booth-tag-number">
                                            <i class="fas fa-store"></i>
                                            <span>جناح بالمعرض</span>
                                        </span>
                                    @endif

                                    @if($pos > 0)
                                        @php
                                            $posText = ($pos == 1 ? 'فرصة واحدة' : ($pos == 2 ? 'فرصتان' : ($pos <= 10 ? $pos . ' فرص' : $pos . ' فرصة')));
                                        @endphp
                                        <span class="booth-tag-jobs is-hiring">
                                            <span class="pulse-glow-dot"></span>
                                            <span>{{ $posText }}</span>
                                        </span>
                                    @else
                                        <span class="booth-tag-jobs is-intro">
                                            <i class="fas fa-handshake"></i>
                                            <span>جناح تعريفي</span>
                                        </span>
                                    @endif
                                </div>

                                {{-- مساحة الشعار أو الوسام الرقمي الفاخر --}}
                                <div class="booth-identity-hub">
                                    @if($companyLogo)
                                        <div class="booth-logo-dock">
                                            <img src="{{ $companyLogo }}" alt="{{ $companyName }}" loading="lazy"
                                                onerror="this.parentElement.style.display='none'; this.parentElement.nextElementSibling.style.display='flex';">
                                        </div>
                                        <div class="booth-crest-emblem" style="display:none;">
                                            <span class="crest-monogram-label">{{ $crestText }}</span>
                                            <i class="fas {{ $crestIcon }} crest-sub-icon"></i>
                                        </div>
                                    @else
                                        <div class="booth-crest-emblem">
                                            <span class="crest-monogram-label">{{ $crestText }}</span>
                                            <i class="fas {{ $crestIcon }} crest-sub-icon"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- عنوان وبيانات الشركة --}}
                                <div class="booth-content-body">
                                    <h3 class="booth-company-title" title="{{ $companyName }}">
                                        {{ $companyName }}
                                    </h3>
                                    <div class="booth-sector-badge" title="{{ $industry }}">
                                        <i class="fas {{ $crestIcon }}"></i>
                                        <span>{{ $industry }}</span>
                                    </div>
                                    <p class="booth-desc-teaser">
                                        {{ Str::limit($companyDesc, 95) }}
                                    </p>
                                </div>

                                {{-- زر استكشاف الجناح --}}
                                <div class="booth-action-cta">
                                    <span>استكشاف تفاصيل الجناح</span>
                                    <div class="booth-action-arrow">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- زر استعراض الدليل الكامل المخصص لجميع الشركات --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('job-fair.public.companies', $fair->id) }}" class="companies-dir-btn">
                            <span>استعراض الدليل الكامل لجميع الشركات المشاركة ({{ $totalComp }} شركة)</span>
                            <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                        <div class="mt-2 text-white-50" style="font-size: 0.88rem;">
                            <i class="fas fa-search me-1 text-warning"></i> تصفح حسب القطاع، رقم الجناح، والفرص الوظيفية المتاحة
                        </div>
                    </div>

                </div>
            </section>
        @endif

        {{-- SCIENTIFIC PROGRAM PREVIEW SECTION (البرنامج العلمي والتدريبي المصاحب) --}}
        @if(isset($events) && $events->count() > 0)
            @php
                $previewEvents = $events->take(3);
            @endphp
            <section class="program-preview-section cyber-matrix-wrap" id="program">
                <div class="container position-relative z-2">
                    <div class="text-center mb-5">
                        <div class="cyber-section-badge">
                            <i class="fas fa-graduation-cap text-warning"></i>
                            <span>رحلة الجاهزية المهنية</span>
                        </div>
                        <h2 class="cyber-section-title scramble-on-scroll" data-scramble="البرنامج العلمي والتدريبي المصاحب" title="انقر لإعادة فك التشفير">البرنامج العلمي والتدريبي المصاحب</h2>
                        <p class="cyber-section-subtitle">
                            سلسلة متكاملة من ورش العمل التطبيقية، الماستر كلاس، والجلسات الحوارية الاستراتيجية لتأهيلك لسوق
                            العمل
                        </p>
                    </div>

                    @php
                        $isProgramComingSoon = !($fair && $fair->is_program_published);
                    @endphp

                    @if($isProgramComingSoon)
                        <div class="text-center p-5 rounded-4"
                            style="background: rgba(255,255,255,0.04); border: 1px dashed rgba(238, 202, 62, 0.35); backdrop-filter: blur(10px); max-width: 820px; margin: 0 auto 2.5rem;">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3"
                                style="background: rgba(238, 202, 62, 0.15); border: 1px solid rgba(238, 202, 62, 0.3); color: var(--gold); font-size: 0.88rem; font-weight: 700;">
                                <i class="fas fa-clock"></i>
                                <span>ترقبوا الإطلاق الرسمي قريباً</span>
                                <span class="badge bg-warning text-dark rounded-pill ms-1 font-monospace">Coming Soon</span>
                            </div>
                            <h3 class="fw-bold text-white mb-3" style="font-size: 1.75rem;">
                                البرنامج العلمي والتدريبي قيد التحضير النهائي
                            </h3>
                            <p class="text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 0.98rem; line-height: 1.7;">
                                نضع حالياً اللمسات الأخيرة على جدول ورش العمل التطبيقية وجلسات الماستر كلاس مع نخبة من كبار المدربين
                                والخبراء.
                            </p>
                            <a href="{{ route('job-fair.public.program', $fair->id) }}"
                                class="companies-dir-btn d-inline-flex align-items-center">
                                <span>استعراض محاور البرنامج والتفاصيل</span>
                                <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                        </div>
                    @else
                        <div class="row g-4 mb-5">
                            @foreach($previewEvents as $idx => $event)
                                @php
                                    $typeClass = match ($event->type) {
                                        'masterclass' => 'badge-type-masterclass',
                                        'panel_discussion' => 'badge-type-panel',
                                        default => 'badge-type-workshop',
                                    };
                                    $isCenterCard = ($idx === 1); // Center card gets gold accent like reference image
                                @endphp
                                <div class="col-lg-4 col-md-6">
                                    <div class="cyber-chamfer-card h-100 {{ $isCenterCard ? 'is-featured' : '' }}"
                                        onclick="window.location.href='{{ route('job-fair.public.events.show', $event->id) }}'">

                                        {{-- شريط الوقت ونوع الورشة --}}
                                        <div class="cyber-card-top-bar">
                                            <span class="cyber-time-pill">
                                                <i
                                                    class="far fa-clock text-warning me-1"></i>{{ $event->start_time ? $event->start_time->format('H:i') : '10:00' }}
                                                - {{ $event->end_time ? $event->end_time->format('H:i') : '12:30' }}
                                            </span>
                                            <span class="cyber-track-pill {{ $isCenterCard ? 'badge-gold-featured' : $typeClass }}">
                                                <i
                                                    class="{{ $event->type_icon }} me-1"></i>{{ $event->type_short_label ?: 'ورشة تدريبية' }}
                                            </span>
                                        </div>

                                        {{-- عنوان الفعالية / الورشة --}}
                                        <h3 class="cyber-card-title" title="{{ $event->title }}">
                                            {{ $event->title }}
                                        </h3>

                                        @if($event->speaker_name)
                                            <div class="cyber-card-speaker">
                                                @if($event->speaker_image_url)
                                                    <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}"
                                                        class="speaker-avatar-sm">
                                                @else
                                                    <div class="speaker-avatar-sm-fallback">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                @endif
                                                <span>{{ $event->speaker_name }}</span>
                                            </div>
                                        @endif

                                        <p class="cyber-card-desc" title="{{ $event->description }}">
                                            {{ Str::limit($event->description ?: 'جلسة تدريبية نوعية تستهدف رفع كفاءة الخريجين وإكسابهم المهارات التطبيقية التي يتطلبها سوق العمل المعاصر.', 130) }}
                                        </p>

                                        {{-- الشريط السفلي التفاعلي --}}
                                        <div class="cyber-action-strip">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            <span>عرض التفاصيل الكاملة والجدول</span>
                                            <i class="fas fa-arrow-left ms-auto"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- زر استعراض الجدول الكامل للبرنامج العلمي --}}
                        <div class="text-center">
                            <a href="{{ route('job-fair.public.program', $fair->id) }}" class="companies-dir-btn">
                                <span>استعراض الجدول الكامل للبرنامج العلمي ({{ $events->count() }} فعالية)</span>
                                <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                            <div class="mt-2 text-white-50" style="font-size: 0.88rem;">
                                <i class="fas fa-calendar-alt me-1 text-warning"></i> مواعيد ورش العمل، الماستر كلاس، والجلسات
                                الحوارية
                            </div>
                        </div>
                    @endif

                </div>
            </section>
        @endif

        {{-- GRADUATE PROJECTS PREVIEW SECTION (معرض مشاريع التخرج المتميزة) --}}
        @if(isset($projects) && $projects->count() > 0)
            @php
                $previewProjects = $projects->take(3);
                $isProjectsComingSoon = !($fair && $fair->is_projects_published);
            @endphp
            <section class="projects-preview-section cyber-matrix-wrap" id="projects">
                <div class="container position-relative z-2">
                    <div class="text-center mb-5">
                        <div class="cyber-section-badge">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <span>ابتكارات كفاءات المستقبل</span>
                        </div>
                        <h2 class="cyber-section-title scramble-on-scroll" data-scramble="معرض مشاريع التخرج المتميزة" title="انقر لإعادة فك التشفير">معرض مشاريع التخرج المتميزة</h2>
                        <p class="cyber-section-subtitle">
                            نافذة حية تبرز نتاج إبداعات خريجي كليات جامعة طرابلس، وفرصة استثمارية مباشرة لأصحاب الأعمال لتبني
                            العقول الواعدة
                        </p>
                    </div>

                    @if($isProjectsComingSoon)
                        <div class="text-center p-5 rounded-4 mb-4"
                            style="background: rgba(255,255,255,0.04); border: 1px dashed rgba(238, 202, 62, 0.35); backdrop-filter: blur(10px); max-width: 820px; margin: 0 auto 2.5rem;">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3"
                                style="background: rgba(238, 202, 62, 0.15); border: 1px solid rgba(238, 202, 62, 0.3); color: var(--gold); font-size: 0.88rem; font-weight: 700;">
                                <i class="fas fa-clock"></i>
                                <span>ترقبوا الإطلاق الرسمي قريباً</span>
                                <span class="badge bg-warning text-dark rounded-pill ms-1 font-monospace">Coming Soon</span>
                            </div>
                            <h3 class="fw-bold text-white mb-3" style="font-size: 1.75rem;">
                                معرض وأرشيف مشاريع التخرج قيد التحضير والتنسيق
                            </h3>
                            <p class="text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 0.98rem; line-height: 1.7;">
                                نستكمل حالياً استقبال وفهرسة نخبة مشاريع وبحوث تخرج طلبة كليات جامعة طرابلس، وتجهيز أجنحة العرض
                                التفاعلية ورموز الـ QR Code الخاصة بكل ابتكار.
                            </p>
                            <a href="{{ route('job-fair.public.projects', $fair->id) }}"
                                class="companies-dir-btn d-inline-flex align-items-center">
                                <span>استعراض محاور ومسارات المشاريع</span>
                                <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                        </div>
                    @else
                        <div class="row g-4 mb-5">
                            @foreach($previewProjects as $idx => $project)
                                @php
                                    $isCenterProject = ($idx === 1); // Center card gets gold accent like reference image
                                @endphp
                                <div class="col-lg-4 col-md-6">
                                    <div class="cyber-chamfer-card h-100 {{ $isCenterProject ? 'is-featured' : '' }}"
                                        onclick="window.location.href='{{ route('job-fair.public.projects.show', $project->id) }}'">

                                        {{-- شريط الكلية والسنة / الجناح --}}
                                        <div class="cyber-card-top-bar">
                                            <span class="cyber-time-pill">
                                                <i class="fas fa-calendar-alt text-warning me-1"></i>سنة {{ $project->graduation_year }}
                                                @if($project->booth_number)
                                                    &bull; جناح {{ $project->booth_number }}
                                                @endif
                                            </span>
                                            <span
                                                class="cyber-track-pill {{ $isCenterProject ? 'badge-gold-featured' : 'badge-workshop' }}">
                                                <i
                                                    class="{{ $project->faculty_icon ?: 'fas fa-graduation-cap' }} me-1"></i>{{ $project->faculty }}
                                            </span>
                                        </div>

                                        {{-- عنوان مشروع التخرج --}}
                                        <h3 class="cyber-card-title" title="{{ $project->title }}">
                                            {{ $project->title }}
                                        </h3>

                                        <div class="text-info small mb-3 text-truncate" style="font-size: 0.8rem;"
                                            title="{{ $project->department }} @if($project->supervisor_name) • إشراف: {{ $project->supervisor_name }} @endif">
                                            <i class="fas fa-code-branch me-1"></i>{{ $project->department }}
                                            @if($project->supervisor_name)
                                                &bull; إشراف: {{ $project->supervisor_name }}
                                            @endif
                                        </div>

                                        {{-- شريط تفاصيل المشروع ورمز QR المطابق للصورة تماماً --}}
                                        <div class="cyber-action-strip">
                                            <i class="fas fa-plus text-warning me-1"></i>
                                            <span>عرض تفاصيل المشروع كاملة ورمز QR</span>
                                            <i class="fas fa-qrcode ms-auto text-warning fs-6"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- زر استعراض الأرشيف الكامل لمشاريع التخرج --}}
                        <div class="text-center">
                            <a href="{{ route('job-fair.public.projects', $fair->id) }}" class="companies-dir-btn">
                                <span>استعراض الأرشيف الكامل لمشاريع التخرج ({{ $projects->count() }} مشروع)</span>
                                <i class="fas fa-arrow-left ms-2"></i>
                            </a>
                            <div class="mt-2 text-white-50" style="font-size: 0.88rem;">
                                <i class="fas fa-search me-1 text-warning"></i> تصفح وفلترة حسب الكلية، التخصص، وسنة التخرج، وتحميل
                                الملصقات والملفات
                            </div>
                        </div>
                    @endif

                </div>
            </section>
        @endif

        {{-- REGISTRATION MODAL --}}
        @auth
            @if(!$myRegistration && $fair->can_register)
                <div class="modal fade" id="registerModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-card modal-content shadow-lg">
                            <div class="modal-header modal-header-grad border-0">
                                <h5 class="modal-title text-white fw-bold">
                                    <i class="fas fa-user-plus me-2" style="color:var(--gold)"></i>
                                    التسجيل في المعرض
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('job-fair.register', $fair->id) }}" method="POST">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="alert border-0 rounded-3 mb-3"
                                        style="background:#EFF6FF; color:#1E40AF; font-size:0.88rem">
                                        <i class="fas fa-info-circle me-2"></i>
                                        ستحصل على بطاقة دخول رقمية مع رمز QR فريد فور التسجيل.
                                    </div>

                                    <div class="grad-info-grid mb-3">
                                        <div class="grad-info-item">
                                            <div class="lbl">الاسم</div>
                                            <div class="val">{{ auth()->user()->name }}</div>
                                        </div>
                                        <div class="grad-info-item">
                                            <div class="lbl">التخصص</div>
                                            <div class="val">{{ auth()->user()->major ?? '—' }}</div>
                                        </div>
                                        <div class="grad-info-item">
                                            <div class="lbl">الكلية</div>
                                            <div class="val">{{ auth()->user()->faculty ?? '—' }}</div>
                                        </div>
                                        <div class="grad-info-item">
                                            <div class="lbl">سنة التخرج</div>
                                            <div class="val">{{ auth()->user()->graduation_year ?? '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label fw-semibold" style="font-size:0.9rem">
                                            القطاعات التي تهمك <small class="text-muted">(اختياري)</small>
                                        </label>
                                        <textarea name="interests" class="form-control rounded-3" rows="2"
                                            placeholder="مثال: تكنولوجيا، هندسة، إدارة أعمال..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0 gap-2">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit" class="cta-primary py-2 px-4">
                                        <i class="fas fa-check"></i>تأكيد التسجيل
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

        {{-- COMPANY DETAIL POPUP MODAL (نافذة تفاصيل الشركة المشاركة) --}}
        <div class="modal fade" id="companyDetailModal" tabindex="-1" aria-labelledby="companyDetailModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered company-modal-dialog">
                <div class="modal-content company-modal-content">
                    <div class="company-modal-header text-center position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>

                        <div class="company-modal-logo-wrapper" id="modalCompanyLogoBox">
                            <img id="modalCompanyLogo" src="" alt="شعار الشركة" style="display:none;"
                                onerror="this.style.display='none'; document.getElementById('modalCompanyLogoFallback').style.display='block';">
                            <i id="modalCompanyLogoFallback" class="fas fa-building fa-2x text-primary"
                                style="display:none;"></i>
                        </div>

                        <h3 class="fw-bold mb-2 text-white" id="modalCompanyName">اسم الشركة</h3>

                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-1">
                            <span class="company-modal-badge"
                                style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                <i class="fas fa-tag me-1"></i>
                                <span id="modalCompanyIndustry">قطاع الأعمال</span>
                            </span>
                            <span class="company-modal-badge" id="modalCompanyBoothBadge"
                                style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); display:none;">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <span id="modalCompanyBooth">جناح 1</span>
                            </span>
                            <span class="company-modal-badge" id="modalCompanyJobsBadge"
                                style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); display:none;">
                                <i class="fas fa-briefcase me-1"></i>
                                <span id="modalCompanyJobs">0</span> فرصة متاحة
                            </span>
                        </div>
                    </div>

                    <div class="company-modal-body">
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2" style="font-size: 0.88rem; color: #fbbf24;">
                                <i class="fas fa-info-circle me-1"></i>نبذة تعريفية عن الشركة:
                            </h6>
                            <p class="lh-lg mb-0" id="modalCompanyDesc"
                                style="font-size: 0.95rem; color: #cbd5e1; white-space: pre-line;">
                                وصف الشركة
                            </p>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-4" id="modalCompanyContacts">
                            <div class="company-info-chip" id="modalWebsiteChip" style="display:none;">
                                <div class="icon bg-primary bg-opacity-25 text-primary">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="text-truncate">
                                    <small class="text-white-50 d-block" style="font-size:0.72rem">الموقع الإلكتروني
                                        الرسمي</small>
                                    <a href="#" target="_blank" id="modalCompanyWebsite"
                                        class="text-white fw-semibold text-decoration-none" style="font-size:0.9rem"></a>
                                </div>
                            </div>

                            <div class="company-info-chip" id="modalEmailChip" style="display:none;">
                                <div class="icon bg-success bg-opacity-25 text-success">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="text-truncate">
                                    <small class="text-white-50 d-block" style="font-size:0.72rem">البريد الإلكتروني للتوظيف
                                        / التواصل</small>
                                    <a href="#" id="modalCompanyEmail" class="text-white fw-semibold text-decoration-none"
                                        style="font-size:0.9rem"></a>
                                </div>
                            </div>

                            <div class="company-info-chip" id="modalPhoneChip" style="display:none;">
                                <div class="icon bg-warning bg-opacity-25 text-warning">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="text-truncate">
                                    <small class="text-white-50 d-block" style="font-size:0.72rem">هاتف الشركة</small>
                                    <span id="modalCompanyPhone" class="text-white fw-semibold"
                                        style="font-size:0.9rem"></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-light rounded-pill px-4"
                                data-bs-dismiss="modal">إغلاق</button>
                            @auth
                                @if(auth()->user()->role === 'graduate')
                                    <a href="{{ route('graduate.job-opportunities.index') }}"
                                        class="cta-primary py-2 px-4 text-decoration-none">
                                        <i class="fas fa-briefcase"></i>تصفح الفرص المتاحة
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════
        نافذة تسجيل زائر / ضيف بالمعرض
        ══════════════════════════════════ --}}
        <div class="modal fade" id="visitorRegisterModal" tabindex="-1" aria-labelledby="visitorRegisterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content"
                    style="background: #0d1e38; border: 1px solid rgba(16,185,129,0.3); border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.85); color: #fff; overflow: hidden;">
                    <!-- شريط شعارات الشركاء في رأس النموذج -->
                    <div class="px-4 pt-3 pb-2 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2"
                        style="background: rgba(2, 44, 94, 0.45);">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            @if($fair && $fair->white_logo_url)
                                <img src="{{ $fair->white_logo_url }}" alt="{{ $fair->title }}"
                                    style="height: 44px; width: auto; max-width: 130px; object-fit: contain;"
                                    onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_white.png') }}';">
                            @else
                                <img src="{{ asset('images/job_fair_logo_white.png') }}" alt="معرض التوظيف"
                                    style="height: 44px; width: auto; max-width: 130px; object-fit: contain;">
                            @endif
                            <div style="width: 1px; height: 32px; background: rgba(255,255,255,0.2);"></div>
                            <img src="{{ asset('images/office_logo_white.png') }}" alt="مكتب تدريب الخريجين بجامعة طرابلس"
                                style="height: 44px; width: auto; max-width: 130px; object-fit: contain;"
                                onerror="this.src='{{ asset('images/logo.jpg') }}'">
                            <div style="width: 1px; height: 32px; background: rgba(255,255,255,0.2);"></div>
                            <div class="bg-white px-2 py-1 rounded-2 shadow-sm d-flex align-items-center"
                                style="height: 44px;" title="تنظيم: شركة الواحة للمعارض">
                                <img src="{{ asset('images/wahaexpo_logo.png') }}"
                                    alt="شركة الواحة لتنظيم المعارض والمؤتمرات"
                                    style="height: 36px; width: auto; max-width: 110px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1"
                                onclick="copyVisitorFormLink(this)" style="font-size: 0.78rem;">
                                <i class="fas fa-qrcode me-1"></i>نسخ رابط الـ QR
                            </button>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                    </div>

                    <div class="modal-header border-0 pb-0 pt-3 px-4 position-relative">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; color: #fff; box-shadow: 0 8px 20px rgba(16,185,129,0.35);">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bold text-white mb-1" id="visitorRegisterModalLabel">تسجيل حضور
                                    زائر / ضيف</h4>
                                <p class="text-white-50 small mb-0">أهلاً بك في
                                    {{ $fair ? $fair->title : 'ملتقى ومعرض التوظيف' }} — جامعة طرابلس بالتعاون مع شركة
                                    الواحة</p>
                            </div>
                        </div>
                    </div>

                    <form id="visitorRegisterForm" action="{{ route('job-fair.visitor.register', $fair->id) }}"
                        method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div id="visitorFormAlert" class="alert d-none mb-3" role="alert"></div>

                            <div class="row g-3">
                                {{-- الاسم الكامل --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-user text-warning me-1"></i>الاسم الرباعي <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="الاسم الثلاثي أو الرباعي" required>
                                </div>

                                {{-- رقم الهاتف / واتساب --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-phone-alt text-warning me-1"></i>رقم الهاتف المحمول / الواتساب
                                        <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="مثال: 0912345678" required dir="ltr">
                                </div>

                                {{-- البريد الإلكتروني --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-envelope text-warning me-1"></i>البريد الإلكتروني <span
                                            class="text-white-50 fw-normal">(اختياري)</span></label>
                                    <input type="email" name="email"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="name@example.com" dir="ltr">
                                </div>

                                {{-- صفة الزائر (تم إلغاء الخيار وتعيينه تلقائياً كخريج باحث عن عمل) --}}
                                <input type="hidden" name="visitor_type" value="job_seeker">

                                {{-- المستوى التعليمي --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-graduation-cap text-warning me-1"></i>المستوى التعليمي</label>
                                    <select name="education_level"
                                        class="form-select text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;">
                                        <option value="" selected>-- غير محدد --</option>
                                        <option value="ثانوي">ثانوي أو ما يعادله</option>
                                        <option value="دبلوم">دبلوم متوسط / عالي</option>
                                        <option value="بكالوريوس">بكالوريوس / ليسانس</option>
                                        <option value="ماجستير">ماجستير</option>
                                        <option value="دكتوراه">دكتوراه</option>
                                        <option value="أخرى">أخرى</option>
                                    </select>
                                </div>

                                {{-- التخصص --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-laptop-code text-warning me-1"></i>التخصص الأكاديمي أو
                                        المهني</label>
                                    <input type="text" name="specialization"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="مثال: هندسة تقنية، إدارة، محاسبة...">
                                </div>

                                {{-- جهة العمل أو الكلية --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-university text-warning me-1"></i>الكلية أو جهة العمل
                                        الحالية</label>
                                    <input type="text" name="organization"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="مثال: جامعة طرابلس، شركة...">
                                </div>

                                {{-- المدينة --}}
                                <div class="col-md-6">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-map-marker-alt text-warning me-1"></i>المدينة / المنطقة</label>
                                    <input type="text" name="city"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;" placeholder="مثال: طرابلس، جنزور، مصراتة...">
                                </div>

                                {{-- الهدف من الزيارة --}}
                                <div class="col-12">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-bullseye text-warning me-1"></i>الهدف الأساسي من زيارة
                                        المعرض</label>
                                    <select name="visit_purpose"
                                        class="form-select text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;">
                                        <option value="explore_jobs">البحث عن فرص عمل وتدريب بأجنحة الشركات</option>
                                        <option value="view_projects">الاطلاع على مشاريع تخرج الطلاب والابتكارات</option>
                                        <option value="networking">بناء علاقات وشراكات مهنية مع الشركات</option>
                                        <option value="attend_workshops">حضور ورش العمل والجلسات الحوارية</option>
                                        <option value="support_graduates">تشجيع ومساندة الخريجين</option>
                                        <option value="general_visit">زيارة استكشافية عامة للفعاليات</option>
                                    </select>
                                </div>

                                {{-- ملاحظات --}}
                                <div class="col-12">
                                    <label class="form-label text-light small fw-bold"><i
                                            class="fas fa-comment-dots text-warning me-1"></i>ملاحظات أو استفسارات
                                        إضافية</label>
                                    <textarea name="notes" rows="2"
                                        class="form-control text-white border-secondary rounded-3 py-2"
                                        style="background:#162b4c;"
                                        placeholder="أي معلومات أو اهتمامات إضافية تود مشاركتها مع المنظمين..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" id="btnSubmitVisitor"
                                class="nav-btn nav-btn-gold px-4 py-2 rounded-pill fw-bold border-0">
                                <i class="fas fa-check-circle me-1"></i>
                                <span>تأكيد التسجيل وإصدار التذكرة الرقمية</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @else

        {{-- NO FAIR STATE --}}
        <div class="empty-hero">
            <div class="empty-icon">🎪</div>
            <h2 style="font-size:2rem; font-weight:800; margin-bottom:0.8rem; color:white">
                لا توجد فعاليات أو معارض منشورة حالياً
            </h2>
            <p style="color:rgba(255,255,255,0.55); max-width:400px; margin-bottom:2rem; line-height:1.7">
                تابع الإعلانات للاطلاع على مواعيد المعارض والفعاليات القادمة
            </p>
            <a href="{{ route('home') }}" class="cta-primary">
                <i class="fas fa-home"></i>الصفحة الرئيسية
            </a>
        </div>

    @endif

    {{-- GLASSMORPHISM SPONSORS STRIP --}}
    @if($fair)
        @include('job-fair.partials.footer-sponsors-strip', ['fair' => $fair, 'sponsors' => $sponsors ?? null, 'companies' => $companies ?? null])
    @endif

    {{-- FOOTER removed per user request --}}

    {{-- ══════════════════════════════════
    SCRIPTS
    ══════════════════════════════════ --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/ScrambleTextPlugin.min.js"></script>

    <script>
        // ─── Scramble Text Animation (GSAP ScrambleTextPlugin) ───
        (function initScrambleEffects() {
            if (typeof gsap === 'undefined' || typeof ScrambleTextPlugin === 'undefined') return;

            gsap.registerPlugin(ScrambleTextPlugin);

            // 1. تأثير فك التشفير على السطر الوصفي في الهيرو ("من الجامعة إلى سوق العمل")
            function runSubtitleScramble() {
                const descEl = document.getElementById('scrambleSubtitle');
                const cursorEl = document.getElementById('scrambleCursor');
                if (!descEl) return;

                const targetDesc = descEl.getAttribute('data-text') || descEl.textContent.trim();
                const tl = gsap.timeline({ defaults: { ease: "none" } });

                if (cursorEl) {
                    cursorEl.style.display = 'inline-block';
                    gsap.set(cursorEl, { opacity: 1 });
                }

                tl.to(descEl, {
                    duration: 1.4,
                    scrambleText: {
                        text: targetDesc,
                        chars: "010123456789ابجدهوزحطيكلمنسعفصقرشت",
                        speed: 0.35
                    }
                });

                if (cursorEl) {
                    tl.to(cursorEl, {
                        opacity: 0,
                        duration: 0.5,
                        delay: 1.0,
                        onComplete: function() {
                            cursorEl.style.display = 'none';
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', runSubtitleScramble);
            } else {
                runSubtitleScramble();
            }

            const subtitleEl = document.getElementById('scrambleSubtitle');
            if (subtitleEl) {
                subtitleEl.addEventListener('click', runSubtitleScramble);
            }

            // 2. تأثير فك التشفير عند التمرير لكل قسم (الشركاء والرعاة، الشركات، البرنامج، المشاريع)
            function scrambleElement(el) {
                const targetText = el.getAttribute('data-scramble') || el.textContent.trim();
                gsap.to(el, {
                    duration: 1.2,
                    scrambleText: {
                        text: targetText,
                        chars: "010123456789ابجدهوزحطيكلمنسعفصقرشت",
                        speed: 0.38
                    },
                    ease: "none"
                });
            }

            if ('IntersectionObserver' in window) {
                const scrollElements = document.querySelectorAll('.scramble-on-scroll');
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting && !entry.target.dataset.scrambled) {
                            entry.target.dataset.scrambled = "true";
                            scrambleElement(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3, rootMargin: '0px 0px -40px 0px' });

                scrollElements.forEach(function(el) {
                    observer.observe(el);
                    // النقر على العنوان يعيد تشغيل التأثير في أي وقت
                    el.addEventListener('click', function() {
                        scrambleElement(el);
                    });
                });
            }
        })();
    </script>

    <script>
        // ── Scroll nav style
        window.addEventListener('scroll', function () {
            document.getElementById('topNav').classList.toggle('scrolled', window.scrollY > 50);
        });

        // ── Floating particles
        (function () {
            const c = document.getElementById('particles-container');
            if (!c) return;
            c.style.cssText = 'position:absolute;inset:0;pointer-events:none;overflow:hidden;';
            for (let i = 0; i < 25; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const size = 2 + Math.random() * 4;
                p.style.cssText = `
            left:${Math.random() * 100}%;
            width:${size}px; height:${size}px;
            animation-duration:${10 + Math.random() * 15}s;
            animation-delay:${-Math.random() * 20}s;
            opacity:${0.3 + Math.random() * 0.5};
        `;
                c.appendChild(p);
            }
        })();


        // ── Company Detail Modal Population
        (function () {
            const companyModal = document.getElementById('companyDetailModal');
            if (!companyModal) return;

            companyModal.addEventListener('show.bs.modal', function (event) {
                const rawTrigger = event.relatedTarget;
                const button = rawTrigger ? (rawTrigger.closest('[data-name]') || rawTrigger) : null;
                if (!button) return;

                const name = button.getAttribute('data-name') || 'شركة مشاركة';
                const logo = button.getAttribute('data-logo') || '';
                const industry = button.getAttribute('data-industry') || 'قطاع الأعمال والخدمات';
                const booth = button.getAttribute('data-booth') || '';
                const jobs = parseInt(button.getAttribute('data-jobs') || '0', 10);
                const desc = button.getAttribute('data-desc') || '';
                const website = button.getAttribute('data-website') || '';
                const email = button.getAttribute('data-email') || '';
                const phone = button.getAttribute('data-phone') || '';

                const nameEl = document.getElementById('modalCompanyName');
                if (nameEl) nameEl.textContent = name;

                const indEl = document.getElementById('modalCompanyIndustry');
                if (indEl) indEl.textContent = industry;

                const descEl = document.getElementById('modalCompanyDesc');
                if (descEl) descEl.textContent = desc || 'جهة رائدة مشاركة في فعاليات المعرض لتوفير أفضل الفرص للخرجين.';

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

            // Keyboard accessibility for booth cards and 3D cylinder cards
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    const card = e.target.closest && (e.target.closest('.company-booth-card') || e.target.closest('.cyber-cylinder-card'));
                    if (card && document.activeElement === card) {
                        e.preventDefault();
                        card.click();
                    }
                }
            });
        })();

        // ── 3D Cylinder Carousel Controller
        (function () {
            const inner = document.getElementById('companiesCylinderInner');
            const playPauseBtn = document.getElementById('btnCylinderPause');
            const playIcon = document.getElementById('cylinderPlayIcon');
            const playText = document.getElementById('cylinderPlayText');
            const prevBtn = document.getElementById('btnCylinderPrev');
            const nextBtn = document.getElementById('btnCylinderNext');
            const btnView3d = document.getElementById('btnView3d');
            const btnViewGrid = document.getElementById('btnViewGrid');
            const cylinderWrapper = document.getElementById('companiesCylinderWrapper');
            const gridWrapper = document.getElementById('companiesGridWrapper');
            const playbackControls = document.getElementById('cylinderPlaybackControls');

            if (!inner) return;

            let isPaused = false;
            let currentAngle = 0;

            // View Switcher (3D vs Grid)
            if (btnView3d && btnViewGrid && cylinderWrapper && gridWrapper) {
                btnView3d.addEventListener('click', function () {
                    btnView3d.classList.add('active');
                    btnViewGrid.classList.remove('active');
                    cylinderWrapper.classList.remove('d-none');
                    if (playbackControls) playbackControls.classList.remove('d-none');
                    gridWrapper.classList.add('d-none');
                });

                btnViewGrid.addEventListener('click', function () {
                    btnViewGrid.classList.add('active');
                    btnView3d.classList.remove('active');
                    cylinderWrapper.classList.add('d-none');
                    if (playbackControls) playbackControls.classList.add('d-none');
                    gridWrapper.classList.remove('d-none');
                });
            }

            // Play / Pause Toggle
            if (playPauseBtn) {
                playPauseBtn.addEventListener('click', function () {
                    isPaused = !isPaused;
                    if (isPaused) {
                        inner.classList.add('is-paused');
                        if (playIcon) playIcon.className = 'fas fa-play';
                        if (playText) playText.textContent = 'تشغيل';
                    } else {
                        inner.style.animation = '';
                        inner.style.transition = '';
                        inner.classList.remove('is-paused');
                        if (playIcon) playIcon.className = 'fas fa-pause';
                        if (playText) playText.textContent = 'إيقاف مؤقت';
                    }
                });
            }

            // Manual Stepping (Prev / Next)
            const qty = parseInt(inner.style.getPropertyValue('--quantity') || '8', 10);
            const stepDeg = 360 / Math.max(qty, 1);

            function stepCylinder(dir) {
                inner.classList.add('is-paused');
                isPaused = true;
                if (playIcon) playIcon.className = 'fas fa-play';
                if (playText) playText.textContent = 'تشغيل';

                currentAngle += (dir * stepDeg);
                inner.style.animation = 'none';
                inner.style.transition = 'transform 0.65s cubic-bezier(0.16, 1, 0.3, 1)';
                inner.style.transform = 'perspective(var(--perspective, 1400px)) rotateX(var(--rotateX, -9deg)) rotateY(' + currentAngle + 'deg)';
            }

            if (prevBtn) prevBtn.addEventListener('click', () => stepCylinder(1));
            if (nextBtn) nextBtn.addEventListener('click', () => stepCylinder(-1));
        })();

        // ── Visitor Registration Form Handling
        (function () {
            const vForm = document.getElementById('visitorRegisterForm');
            if (!vForm) return;

            vForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = document.getElementById('btnSubmitVisitor');
                const alertBox = document.getElementById('visitorFormAlert');
                const originalBtnHtml = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> جاري إصدار بطاقة الدخول...';

                alertBox.className = 'alert d-none mb-3';
                alertBox.innerHTML = '';

                const formData = new FormData(vForm);

                fetch(vForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        return response.json().then(data => ({
                            status: response.status,
                            ok: response.ok,
                            data: data
                        }));
                    })
                    .then(res => {
                        if (res.ok && res.data.success) {
                            alertBox.className = 'alert alert-success d-block mb-3 fw-bold';
                            alertBox.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + (res.data.message || 'تم تسجيلك بنجاح! جاري توجيهك لبطاقتك الرقمية...');
                            setTimeout(() => {
                                window.location.href = res.data.ticket_url;
                            }, 1000);
                        } else {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnHtml;
                            alertBox.className = 'alert alert-danger d-block mb-3';
                            let errorMsg = res.data.message || 'حدث خطأ أثناء التسجيل، يرجى مراجعة البيانات المدخلة.';
                            if (res.data.errors) {
                                const list = Object.values(res.data.errors).map(err => `<li>${err[0]}</li>`).join('');
                                errorMsg += `<ul class="mb-0 mt-2 text-start">${list}</ul>`;
                            }
                            alertBox.innerHTML = errorMsg;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        // Fallback: submit standard form
                        vForm.submit();
                    });
            });
        })();

            function copyVisitorFormLink(btn) {
                const url = window.location.origin + window.location.pathname + '#visitor-register';
                navigator.clipboard.writeText(url).then(() => {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check text-success me-1"></i>تم نسخ الرابط!';
                    setTimeout(() => { btn.innerHTML = orig; }, 2500);
                }).catch(() => {
                    prompt('انسخ الرابط:', url);
                });
            }

            // فتح نموذج تسجيل الزائر تلقائياً عند الدخول بالرابط الحامل لـ #visitor-register
            function checkVisitorModalHash() {
                if (window.location.hash === '#visitor-register' || window.location.hash === '#register-visitor') {
                    const modalEl = document.getElementById('visitorRegisterModal');
                    if (modalEl) {
                        const openModal = function () {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                modal.show();
                            }
                        };
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', openModal);
                        } else {
                            openModal();
                        }
                        setTimeout(openModal, 400);
                    }
                }
            }
            window.addEventListener('DOMContentLoaded', checkVisitorModalHash);
            window.addEventListener('hashchange', checkVisitorModalHash);
            setTimeout(checkVisitorModalHash, 300);

            // ══════════════════════════════════════════════════════════
            // OMNIX STUDIO INTERACTIVE MICRO-ANIMATIONS & PARALLAX
            // ══════════════════════════════════════════════════════════

            // 1. Omnix Mouse Spotlight Tracker on all interactive cards
            (function initOmnixSpotlight() {
                const targetCards = document.querySelectorAll('.company-booth-card, .sponsor-card, .workshop-card, .project-card, .cd-box, .stat-pill');
                targetCards.forEach(function(card) {
                    card.classList.add('omnix-spotlight-card');
                    card.addEventListener('mousemove', function(e) {
                        const rect = card.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        card.style.setProperty('--mouse-x', x + 'px');
                        card.style.setProperty('--mouse-y', y + 'px');
                    });
                });
            })();

            // 2. Omnix Interactive 3D Hero Parallax & Depth Tilt
            (function initOmnixHeroParallax() {
                const heroSection = document.getElementById('hero');
                const glassCard = document.querySelector('.premium-glass-card');
                const capWrapper = document.querySelector('.hero-3d-cap-wrapper');
                const floatShapes = document.querySelectorAll('.float-element');

                if (!heroSection) return;

                let targetRotX = 0, targetRotY = 0;
                let currentRotX = 0, currentRotY = 0;
                let isMouseInside = false;

                heroSection.addEventListener('mousemove', function(e) {
                    isMouseInside = true;
                    const rect = heroSection.getBoundingClientRect();
                    const xRatio = (e.clientX - rect.left) / rect.width - 0.5;
                    const yRatio = (e.clientY - rect.top) / rect.height - 0.5;
                    targetRotY = xRatio * 24; // Y rotation in degrees
                    targetRotX = yRatio * -24; // X rotation in degrees
                });

                heroSection.addEventListener('mouseleave', function() {
                    isMouseInside = false;
                    targetRotX = 0;
                    targetRotY = 0;
                });

                function stepParallax() {
                    currentRotX += (targetRotX - currentRotX) * 0.08;
                    currentRotY += (targetRotY - currentRotY) * 0.08;

                    if (glassCard && (isMouseInside || Math.abs(currentRotX) > 0.01)) {
                        glassCard.style.transform = 'perspective(1000px) rotateY(' + currentRotY.toFixed(2) + 'deg) rotateX(' + currentRotX.toFixed(2) + 'deg)';
                    }
                    if (capWrapper && (isMouseInside || Math.abs(currentRotX) > 0.01)) {
                        capWrapper.style.transform = 'perspective(1000px) rotateY(' + (currentRotY * 1.3).toFixed(2) + 'deg) rotateX(' + (currentRotX * 1.3).toFixed(2) + 'deg) translateY(' + (currentRotX * -0.8).toFixed(2) + 'px)';
                    }
                    if (floatShapes.length && (isMouseInside || Math.abs(currentRotX) > 0.01)) {
                        floatShapes.forEach(function(shape, idx) {
                            const speed = (idx + 1) * 0.55;
                            shape.style.transform = 'translate(' + (currentRotY * speed * -1.2).toFixed(2) + 'px, ' + (currentRotX * speed * 1.2).toFixed(2) + 'px)';
                        });
                    }

                    requestAnimationFrame(stepParallax);
                }
                requestAnimationFrame(stepParallax);
            })();

            // 3. Omnix Staggered Scroll Reveal System (Framer/GSAP feel)
            (function initOmnixScrollReveal() {
                const revealElements = document.querySelectorAll('.company-booth-card, .sponsor-card, .workshop-card, .project-card, .countdown-row, .hero-action-buttons');
                
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-revealed');
                                observer.unobserve(entry.target);
                            }
                        });
                    }, {
                        threshold: 0.12,
                        rootMargin: '0px 0px -30px 0px'
                    });

                    revealElements.forEach(function(el, index) {
                        el.classList.add('omnix-reveal');
                        const staggerDelay = (index % 6) * 0.08;
                        el.style.transitionDelay = staggerDelay + 's';
                        observer.observe(el);
                    });
                }
            })();

            // ══════════════════════════════════════════════════════════
            //  OMNIX CREATIVE SCROLL ANIMATION ENGINE v2
            // ══════════════════════════════════════════════════════════

            // ─── 4. Scroll Progress Bar
            (function initScrollProgress() {
                const bar = document.getElementById('scroll-progress-bar');
                if (!bar) return;
                function updateBar() {
                    const scrollTop = window.scrollY || document.documentElement.scrollTop;
                    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
                    bar.style.width = pct.toFixed(2) + '%';
                }
                window.addEventListener('scroll', updateBar, { passive: true });
                updateBar();
            })();

            // ─── 5. Cursor Glow Follower
            (function initCursorGlow() {
                const glow = document.getElementById('cursor-glow');
                if (!glow) return;
                document.addEventListener('mousemove', function(e) {
                    glow.style.left = e.clientX + 'px';
                    glow.style.top  = e.clientY + 'px';
                }, { passive: true });
            })();

            // ─── 6. Advanced Section Reveal
            (function initAdvancedSectionReveal() {
                if (!('IntersectionObserver' in window)) return;
                const sectionHeaders = document.querySelectorAll(
                    '.cyber-section-badge, .cyber-section-title, .cyber-section-subtitle, ' +
                    '.strategic-hero-showcase, .podium-sponsor-card, ' +
                    '.cyber-chamfer-card, .project-preview-card, ' +
                    '.stat-pill, .cd-box, .title-line, .podium-divider-wrap'
                );
                const delays = ['sr-d1','sr-d2','sr-d3','sr-d4','sr-d5','sr-d6'];
                sectionHeaders.forEach(function(el, idx) {
                    let animClass = 'sr-fade-up';
                    if (el.classList.contains('cyber-section-badge'))     animClass = 'sr-zoom-in';
                    if (el.classList.contains('strategic-hero-showcase')) animClass = 'sr-flip-up';
                    if (el.classList.contains('podium-sponsor-card'))     animClass = 'sr-slide-scale';
                    if (el.classList.contains('cd-box'))                  animClass = 'sr-zoom-in';
                    if (el.classList.contains('cyber-chamfer-card'))      animClass = 'sr-flip-up';
                    if (el.classList.contains('title-line'))              animClass = 'sr-fade-left';
                    if (el.classList.contains('stat-pill'))               animClass = 'sr-slide-scale';
                    el.classList.add(animClass, delays[idx % delays.length]);
                });
                const srObs = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) { entry.target.classList.add('sr-revealed'); srObs.unobserve(entry.target); }
                    });
                }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
                sectionHeaders.forEach(function(el) { srObs.observe(el); });
            })();

            // ─── 7. Kinetic Title Underline
            (function initKineticTitles() {
                if (!('IntersectionObserver' in window)) return;
                const titles = document.querySelectorAll('.cyber-section-title');
                titles.forEach(function(t) { t.classList.add('kinetic-title-wrap'); });
                const tObs = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            setTimeout(function() { entry.target.classList.add('line-revealed'); }, 400);
                            tObs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.3 });
                titles.forEach(function(t) { tObs.observe(t); });
            })();

            // ─── 8. Glitch on Section Badges
            (function initGlitchBadges() {
                if (!('IntersectionObserver' in window)) return;
                const badges = document.querySelectorAll('.cyber-section-badge');
                badges.forEach(function(b) { b.classList.add('glitch-badge'); b.setAttribute('data-text', b.innerText); });
                const gObs = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-glitching');
                            setTimeout(function() { entry.target.classList.remove('is-glitching'); }, 500);
                            gObs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                badges.forEach(function(b) { gObs.observe(b); });
            })();

            // ─── 9. Count-Up Animation
            (function initCountUp() {
                if (!('IntersectionObserver' in window)) return;
                function animateCount(el, target, dur) {
                    const t0 = performance.now();
                    el.classList.add('counting');
                    (function step(now) {
                        const p = Math.min((now - t0) / dur, 1);
                        const e = 1 - Math.pow(1 - p, 3);
                        el.innerText = Math.round(target * e);
                        if (p < 1) requestAnimationFrame(step);
                        else { el.innerText = target; el.classList.remove('counting'); }
                    })(t0);
                }
                const numEls = document.querySelectorAll('.cd-num, .stat-value, .hero-stat-value, [data-count-target]');
                const cObs = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const t = parseInt(entry.target.innerText.replace(/[^\d]/g,''), 10);
                            if (!isNaN(t) && t > 0 && t < 99999) animateCount(entry.target, t, 1800);
                            cObs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                numEls.forEach(function(el) { el.classList.add('count-up-num'); cObs.observe(el); });
            })();

            // Animations for orbs, scan lines and kinetic dividers disabled to keep the UI clean and calm.

            // ─── 13. Card Tilt Effect
            (function initCardTilt() {
                document.querySelectorAll('.podium-sponsor-card, .cyber-chamfer-card, .project-preview-card').forEach(function(card) {
                    card.addEventListener('mousemove', function(e) {
                        const r = card.getBoundingClientRect();
                        const dx = (e.clientX - (r.left + r.width/2)) / (r.width/2);
                        const dy = (e.clientY - (r.top  + r.height/2)) / (r.height/2);
                        card.style.transform = 'perspective(800px) rotateX('+(dy*-8).toFixed(1)+'deg) rotateY('+(dx*8).toFixed(1)+'deg) translateY(-4px)';
                    });
                    card.addEventListener('mouseleave', function() { card.style.transform = ''; });
                });
            })();

            // ─── 14. Extended Spotlight
            (function extendSpotlight() {
                document.querySelectorAll('.podium-sponsor-card, .cyber-chamfer-card, .project-preview-card, .strategic-hero-showcase, .strategic-logo-pod').forEach(function(card) {
                    card.classList.add('omnix-spotlight-card');
                    card.addEventListener('mousemove', function(e) {
                        const r = card.getBoundingClientRect();
                        card.style.setProperty('--mouse-x', (e.clientX-r.left)+'px');
                        card.style.setProperty('--mouse-y', (e.clientY-r.top) +'px');
                    });
                });
            })();

    </script>
</body>

</html>
