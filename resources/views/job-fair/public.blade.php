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

        /* ══════════════════════════════════════════════════════════
           UIVERSE.IO by adamgiebl: Dynamic Bottom-Up Fill Buttons
           تسجيل الدخول / سجّل كخريج / تسجيل زائر
        ══════════════════════════════════════════════════════════ */
        .btn-adam,
        .nav-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
            padding: 9px 20px;
            border-radius: 10px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            letter-spacing: 0.5px;
            cursor: pointer;
            text-decoration: none;
            background: transparent;
            transition: ease-out 0.5s;
            line-height: 1.3;
            outline: none;
            border: 2px solid transparent;
            box-shadow: inset 0 0 0 0 transparent;
        }

        .btn-adam:active,
        .nav-btn:active {
            transform: scale(0.9) !important;
        }

        .btn-adam i,
        .nav-btn i {
            transition: transform 0.3s ease, color 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-adam:hover i,
        .nav-btn:hover i {
            transform: scale(1.15);
        }

        /* 1. تسجيل الدخول (Login) - Cyber Ice / Cyan */
        .btn-adam-login,
        .nav-btn-login {
            color: #38BDF8 !important;
            border: 2px solid #38BDF8 !important;
            box-shadow: inset 0 0 0 0 #38BDF8 !important;
            background: transparent !important;
        }

        .btn-adam-login:hover,
        .nav-btn-login:hover {
            color: #042444 !important;
            border-color: #38BDF8 !important;
            box-shadow: inset 0 -100px 0 0 #38BDF8 !important;
        }

        .btn-adam-login:hover i,
        .nav-btn-login:hover i {
            color: #042444 !important;
        }

        /* 2. سجّل كخريج (Graduate Registration) - Luxury Gold */
        .btn-adam-graduate,
        .nav-btn-gold {
            color: #EECA3E !important;
            border: 2px solid #EECA3E !important;
            box-shadow: inset 0 0 0 0 #EECA3E !important;
            background: transparent !important;
        }

        .btn-adam-graduate::before,
        .nav-btn-gold::before {
            display: none !important;
        }

        .btn-adam-graduate:hover,
        .nav-btn-gold:hover {
            color: #042444 !important;
            border-color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #EECA3E !important;
        }

        .btn-adam-graduate:hover i,
        .nav-btn-gold:hover i {
            color: #042444 !important;
        }

        /* 3. تسجيل زائر (Visitor Badge) - هوية المعرض الرسمية: الأخضر الزمردي (#10B981) */
        .btn-adam-visitor,
        .nav-btn-visitor {
            color: #10B981 !important;
            border: 2px solid #10B981 !important;
            box-shadow: inset 0 0 0 0 #10B981 !important;
            background: transparent !important;
        }

        .btn-adam-visitor i,
        .nav-btn-visitor i {
            color: #10B981 !important;
        }

        .btn-adam-visitor:hover,
        .nav-btn-visitor:hover {
            color: #ffffff !important;
            border-color: #10B981 !important;
            box-shadow: inset 0 -100px 0 0 #10B981 !important;
        }

        .btn-adam-visitor:hover i,
        .nav-btn-visitor:hover i {
            color: #ffffff !important;
        }

        /* الأزرار العادية nav-btn-outline */
        .nav-btn-outline {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.35);
            color: white;
            box-shadow: inset 0 0 0 0 rgba(255, 255, 255, 0.2);
        }

        .nav-btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
            box-shadow: inset 0 -100px 0 0 rgba(238, 202, 62, 0.15);
        }

        .nav-btn-emerald {
            color: #10b981;
            border: 2px solid #10b981;
            box-shadow: inset 0 0 0 0 #10b981;
            background: transparent;
        }

        .nav-btn-emerald:hover {
            color: white;
            border-color: #10b981;
            box-shadow: inset 0 -100px 0 0 #10b981;
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

        .hero-action-buttons .btn-adam,
        .hero-action-buttons .nav-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            white-space: nowrap !important;
            padding: 11px 24px !important;
            font-size: 0.96rem !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            border-radius: 10px !important;
            text-decoration: none !important;
            background: transparent !important;
            cursor: pointer !important;
            transition: ease-out 0.5s !important;
            outline: none !important;
        }

        .hero-action-buttons .btn-adam:active,
        .hero-action-buttons .nav-btn:active {
            transform: scale(0.9) !important;
        }

        .hero-action-buttons .btn-adam i,
        .hero-action-buttons .nav-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            line-height: 1;
            margin: 0 !important;
            transition: transform 0.3s ease, color 0.3s ease !important;
        }

        .hero-action-buttons .btn-adam:hover i,
        .hero-action-buttons .nav-btn:hover i {
            transform: scale(1.18) !important;
        }

        .hero-action-buttons .btn-adam span,
        .hero-action-buttons .nav-btn span {
            display: inline-block;
            white-space: nowrap;
            line-height: 1.2;
        }

        /* زر سجّل كخريج بالهيرو - adamgiebl Gold Fill */
        .hero-action-buttons .btn-adam-graduate,
        .hero-action-buttons .nav-btn-gold {
            font-size: 0.98rem !important;
            padding: 11px 24px !important;
            background: transparent !important;
            border: 2px solid #EECA3E !important;
            color: #EECA3E !important;
            font-weight: 800 !important;
            box-shadow: inset 0 0 0 0 #EECA3E !important;
        }

        .hero-action-buttons .btn-adam-graduate:hover,
        .hero-action-buttons .nav-btn-gold:hover {
            color: #042444 !important;
            border-color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #EECA3E !important;
        }

        .hero-action-buttons .btn-adam-graduate:hover i,
        .hero-action-buttons .nav-btn-gold:hover i {
            color: #042444 !important;
        }

        /* زر تسجيل زائر بالهيرو - هوية المعرض الرسمية: الأخضر الزمردي (#10B981) */
        .hero-action-buttons .btn-adam-visitor,
        .hero-action-buttons .nav-btn-visitor {
            background: transparent !important;
            border: 2px solid #10B981 !important;
            color: #10B981 !important;
            font-weight: 800 !important;
            box-shadow: inset 0 0 0 0 #10B981 !important;
        }

        .hero-action-buttons .btn-adam-visitor i,
        .hero-action-buttons .nav-btn-visitor i {
            color: #10B981 !important;
        }

        .hero-action-buttons .btn-adam-visitor:hover,
        .hero-action-buttons .nav-btn-visitor:hover {
            color: #ffffff !important;
            border-color: #10B981 !important;
            box-shadow: inset 0 -100px 0 0 #10B981 !important;
        }

        .hero-action-buttons .btn-adam-visitor:hover i,
        .hero-action-buttons .nav-btn-visitor:hover i {
            color: #ffffff !important;
        }

        /* الأزرار الفرعية (دليل الشركات، البرنامج، المشاريع) */
        .hero-action-buttons .nav-btn-outline {
            background: rgba(255, 255, 255, 0.06) !important;
            border: 2px solid rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            backdrop-filter: blur(8px) !important;
            box-shadow: inset 0 0 0 0 #043C73 !important;
        }

        .hero-action-buttons .nav-btn-outline:hover {
            border-color: #EECA3E !important;
            color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #043C73 !important;
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
           SECTION: STRATEGIC HERO PALETTE + SPONSORS UIVERSE CARDS
           Adapted from Uiverse.io by arshshaikh06
           Exclusively using Exhibition Brand Colors:
           1. #02142B (Midnight Navy)
           2. #043C73 (Royal Blue)
           3. #0284C7 (Electric Azure)
           4. #38BDF8 (Cyber Cyan)
           5. #EECA3E (Royal Gold)
           ══════════════════════════════════════════════════════ */
        .strategic-palette-hero {
            max-width: 920px;
            margin: 0 auto 2.5rem;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #02142B;
            border: 2px solid rgba(238, 202, 62, 0.6);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.75), 0 0 45px rgba(238, 202, 62, 0.22);
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            position: relative;
        }

        .strategic-palette-hero:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 75px rgba(0, 0, 0, 0.85), 0 0 60px rgba(238, 202, 62, 0.45);
            border-color: #EECA3E;
        }

        .strategic-palette-hero .palette {
            height: 190px;
            display: flex;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .strategic-palette-hero .palette .color {
            height: 100%;
            flex: 1;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 14px;
            font-weight: 800;
            transition: flex 0.22s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.22s ease;
            position: relative;
            cursor: pointer;
            user-select: none;
        }

        .strategic-palette-hero .palette .color span {
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.22s ease, transform 0.22s ease;
            font-family: monospace;
            font-size: 0.82rem;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0,0,0,0.85);
            pointer-events: none;
        }

        .strategic-palette-hero .palette .color:hover {
            flex: 2.3;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.25), 0 0 25px rgba(0, 0, 0, 0.6);
            z-index: 2;
        }

        .strategic-palette-hero .palette .color:hover span {
            opacity: 1;
            transform: translateY(0);
        }

        .strategic-palette-hero .palette-stats-bar {
            padding: 1.1rem 1.8rem;
            background: #02142B;
            border-top: 1px solid rgba(238, 202, 62, 0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 6;
        }

        /* ══════════════════════════════════════════════════════
           SPONSORS SECTION: LUXURY CYBER SPONSOR CARDS
           (University of Tripoli Exhibition Brand Colors)
           ══════════════════════════════════════════════════════ */
        .sponsors-uiverse-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 320px));
            gap: 2.2rem;
            justify-content: center;
            max-width: 1140px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .sponsor-uiverse-card {
            width: 100%;
            max-width: 320px;
            min-height: 330px;
            background: linear-gradient(155deg, #021226 0%, #06264c 55%, #041d3b 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.65), 0 0 20px rgba(4, 93, 176, 0.2);
            border: 1.5px solid rgba(56, 189, 248, 0.22);
            text-decoration: none;
            cursor: pointer;
            margin: 0 auto;
            padding: 24px 20px 20px;
        }

        /* الإطار السيبراني المتوهج المستوحى من Uiverse */
        .sponsor-uiverse-card .uiverse-border {
            position: absolute;
            inset: 0px;
            border: 2px solid #EECA3E;
            opacity: 0;
            transform: rotate(4deg) scale(0.96);
            transition: all 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            z-index: 1;
            border-radius: 16px;
            box-shadow: inset 0 0 16px rgba(238, 202, 62, 0.25);
        }

        .sponsor-uiverse-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.85), 0 0 35px rgba(238, 202, 62, 0.35);
            border-color: rgba(238, 202, 62, 0.75);
        }

        .sponsor-uiverse-card:hover .uiverse-border {
            inset: 8px;
            opacity: 1;
            transform: rotate(0) scale(1);
        }

        /* المحتوى الداخلي للبطاقة */
        .sponsor-uiverse-card .card-inner {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            width: 100%;
            text-align: center;
            gap: 16px;
        }

        /* شارة باقة الرعاية */
        .sponsor-uiverse-card .sponsor-tier-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 5px 16px;
            border-radius: 50px;
            letter-spacing: 0.3px;
            white-space: nowrap;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
            background: rgba(2, 20, 43, 0.9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
        }

        /* صندوق الشعار المحاط بخلفية ناصعة وأبعاد متناسقة */
        .sponsor-logo-box {
            width: 105px;
            height: 105px;
            background: #ffffff;
            border-radius: 20px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(0, 0, 0, 0.06);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
            flex-shrink: 0;
        }

        .sponsor-logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 12px;
            display: block;
        }

        .sponsor-monogram-box {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            background: linear-gradient(135deg, #043C73 0%, #02142B 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #EECA3E;
            font-weight: 900;
            font-size: 2.2rem;
            border: 2px solid #EECA3E;
        }

        .sponsor-uiverse-card:hover .sponsor-logo-box {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, 0.65), 0 0 20px rgba(255, 255, 255, 0.3);
        }

        /* حاوية اسم الشركة الراعية - تضمن ظهور الاسم كاملاً بدون أي قطع */
        .sponsor-name-wrap {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 4px;
        }

        .sponsor-brand-title {
            color: #ffffff;
            font-weight: 800;
            font-size: 1.12rem;
            line-height: 1.45;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
            transition: color 0.3s ease;
        }

        .sponsor-uiverse-card:hover .sponsor-brand-title {
            color: #ffffff;
        }

        /* زر أسفل البطاقة */
        .sponsor-bottom-action {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: auto;
        }

        .sponsor-bottom-action .action-btn-text {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.35s ease;
            white-space: nowrap;
        }

        /* ألوان الفئات (Tier Specific Styling) */
        /* 1. الماسي (Diamond) */
        .sponsor-uiverse-card.tier-diamond {
            border-color: rgba(56, 189, 248, 0.35);
        }
        .sponsor-uiverse-card.tier-diamond .uiverse-border {
            border-color: #38BDF8;
            box-shadow: inset 0 0 18px rgba(56, 189, 248, 0.35);
        }
        .sponsor-uiverse-card.tier-diamond .sponsor-tier-chip {
            color: #38BDF8;
            border: 1px solid rgba(56, 189, 248, 0.6);
            background: rgba(14, 165, 233, 0.12);
        }
        .sponsor-uiverse-card.tier-diamond .action-btn-text {
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.08);
            border: 1px solid rgba(56, 189, 248, 0.35);
        }
        .sponsor-uiverse-card.tier-diamond:hover {
            border-color: rgba(56, 189, 248, 0.85);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.85), 0 0 35px rgba(56, 189, 248, 0.4);
        }
        .sponsor-uiverse-card.tier-diamond:hover .action-btn-text {
            background: #38BDF8;
            color: #02142B;
            border-color: #38BDF8;
            box-shadow: 0 4px 16px rgba(56, 189, 248, 0.4);
        }

        /* 2. الذهبي (Gold) */
        .sponsor-uiverse-card.tier-gold {
            border-color: rgba(238, 202, 62, 0.35);
        }
        .sponsor-uiverse-card.tier-gold .uiverse-border {
            border-color: #EECA3E;
            box-shadow: inset 0 0 18px rgba(238, 202, 62, 0.35);
        }
        .sponsor-uiverse-card.tier-gold .sponsor-tier-chip {
            color: #EECA3E;
            border: 1px solid rgba(238, 202, 62, 0.6);
            background: rgba(238, 202, 62, 0.12);
        }
        .sponsor-uiverse-card.tier-gold .action-btn-text {
            color: #EECA3E;
            background: rgba(238, 202, 62, 0.08);
            border: 1px solid rgba(238, 202, 62, 0.35);
        }
        .sponsor-uiverse-card.tier-gold:hover {
            border-color: rgba(238, 202, 62, 0.85);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.85), 0 0 35px rgba(238, 202, 62, 0.4);
        }
        .sponsor-uiverse-card.tier-gold:hover .action-btn-text {
            background: #EECA3E;
            color: #02142B;
            border-color: #EECA3E;
            box-shadow: 0 4px 16px rgba(238, 202, 62, 0.4);
        }

        /* 3. الفضي (Silver) */
        .sponsor-uiverse-card.tier-silver {
            border-color: rgba(203, 213, 225, 0.3);
        }
        .sponsor-uiverse-card.tier-silver .uiverse-border {
            border-color: #CBD5E1;
            box-shadow: inset 0 0 18px rgba(203, 213, 225, 0.25);
        }
        .sponsor-uiverse-card.tier-silver .sponsor-tier-chip {
            color: #CBD5E1;
            border: 1px solid rgba(203, 213, 225, 0.5);
            background: rgba(203, 213, 225, 0.1);
        }
        .sponsor-uiverse-card.tier-silver .action-btn-text {
            color: #CBD5E1;
            background: rgba(203, 213, 225, 0.08);
            border: 1px solid rgba(203, 213, 225, 0.3);
        }
        .sponsor-uiverse-card.tier-silver:hover {
            border-color: rgba(203, 213, 225, 0.8);
            box-shadow: 0 24px 55px rgba(0, 0, 0, 0.85), 0 0 35px rgba(203, 213, 225, 0.3);
        }
        .sponsor-uiverse-card.tier-silver:hover .action-btn-text {
            background: #CBD5E1;
            color: #02142B;
            border-color: #CBD5E1;
            box-shadow: 0 4px 16px rgba(203, 213, 225, 0.35);
        }

        /* ── Strategic Hero Partner Banner (Grand Uiverse Aesthetic) ── */
        .strategic-uiverse-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(2, 20, 43, 0.96) 0%, rgba(6, 38, 76, 0.94) 50%, rgba(2, 14, 30, 0.98) 100%);
            border: 1.5px solid rgba(238, 202, 62, 0.55);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7), 0 0 35px rgba(238, 202, 62, 0.18);
            margin-bottom: 3.2rem;
            transition: all 0.5s ease-in-out;
        }

        .strategic-uiverse-hero .uiverse-border {
            position: absolute;
            inset: 0px;
            border: 2px solid #EECA3E;
            opacity: 0;
            transform: rotate(4deg);
            transition: all 0.5s ease-in-out;
            pointer-events: none;
            z-index: 1;
            box-shadow: inset 0 0 25px rgba(238, 202, 62, 0.25);
        }

        .strategic-uiverse-hero:hover {
            border-radius: 4px;
            border-color: #EECA3E;
            box-shadow: 0 26px 65px rgba(0, 0, 0, 0.85), 0 0 45px rgba(238, 202, 62, 0.35);
        }

        .strategic-uiverse-hero:hover .uiverse-border {
            inset: 12px;
            opacity: 1;
            transform: rotate(0);
        }

        .strategic-uiverse-hero .bottom-text {
            position: absolute;
            left: 50%;
            bottom: 8px;
            transform: translateX(-50%);
            font-size: 8px;
            text-transform: uppercase;
            padding: 2px 10px;
            color: #EECA3E;
            background: #02142B;
            opacity: 0;
            letter-spacing: 7px;
            transition: all 0.5s ease-in-out;
            font-weight: 800;
            border-radius: 4px;
            z-index: 3;
            border: 1px solid rgba(238, 202, 62, 0.3);
            white-space: nowrap;
        }

        .strategic-uiverse-hero:hover .bottom-text {
            letter-spacing: 3px;
            opacity: 1;
            transform: translateX(-50%);
        }

        .strategic-logo-frame {
            background: rgba(2, 20, 43, 0.75);
            padding: 10px 20px;
            border-radius: 14px;
            border: 1px solid rgba(238, 202, 62, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .sponsor-tier-chip.tier-strategic {
            background: linear-gradient(135deg, rgba(238, 202, 62, 0.25), rgba(4, 60, 115, 0.65));
            color: #EECA3E;
            border: 1px solid rgba(238, 202, 62, 0.7);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 3px 12px;
            border-radius: 50px;
        }

        /* ══════════════════════════════════════════════════════
           SCIENTIFIC PROGRAM: INTERACTIVE PALETTE SLICES CARDS
           (Hover over each slice to reveal speaker, timing, details...)
           ══════════════════════════════════════════════════════ */
        .program-slides-card {
            background: linear-gradient(180deg, rgba(6, 20, 44, 0.96) 0%, rgba(2, 10, 24, 0.98) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.35);
            border-radius: 20px;
            padding: 1.35rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            color: #ffffff;
            height: 100%;
        }

        .program-slides-card:hover {
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.7), 0 0 35px rgba(56, 189, 248, 0.3);
            transform: translateY(-6px);
        }

        .program-slides-card.is-featured {
            border-color: rgba(238, 202, 62, 0.55);
            background: linear-gradient(180deg, rgba(16, 32, 58, 0.96) 0%, rgba(4, 14, 30, 0.98) 100%);
            box-shadow: 0 16px 38px rgba(0, 0, 0, 0.6), 0 0 28px rgba(238, 202, 62, 0.2);
        }

        .program-slides-card.is-featured:hover {
            border-color: rgba(238, 202, 62, 0.9);
            box-shadow: 0 22px 55px rgba(0, 0, 0, 0.75), 0 0 42px rgba(238, 202, 62, 0.4);
        }

        .psc-header {
            margin-bottom: 1rem;
        }

        .psc-time-badge {
            font-size: 0.74rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 3px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        .psc-title {
            color: #ffffff;
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.45;
            margin-bottom: 0;
            text-align: right;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.1rem;
        }

        /* ── Interactive 5 Slices Container ── */
        .psc-slices-wrapper {
            display: flex;
            height: 335px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(56, 189, 248, 0.25);
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.45);
            margin-bottom: 1rem;
        }

        .psc-slice {
            height: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 6px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            user-select: none;
            transition: flex 0.42s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.3s ease;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .psc-slice:last-child {
            border-right: none;
        }

        /* Slices Color Palette (5 Exhibition Colors) */
        .psc-slice.slice-time {
            background: linear-gradient(180deg, #02142B 0%, #062347 100%);
            color: #ffffff;
        }
        .psc-slice.slice-speaker {
            background: linear-gradient(180deg, #043C73 0%, #02264d 100%);
            color: #ffffff;
        }
        .psc-slice.slice-track {
            background: linear-gradient(180deg, #0284C7 0%, #03527d 100%);
            color: #ffffff;
        }
        .psc-slice.slice-details {
            background: linear-gradient(180deg, #38BDF8 0%, #0ea5e9 100%);
            color: #02142B;
        }
        .psc-slice.slice-venue {
            background: linear-gradient(180deg, #EECA3E 0%, #d97706 100%);
            color: #02142B;
        }

        /* Compact view (when slice is NOT hovered) */
        .psc-slice .slice-compact {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-align: center;
            width: 100%;
            height: 100%;
            transition: opacity 0.2s ease;
        }

        .psc-slice .slice-compact .slice-icon {
            font-size: 1.25rem;
            opacity: 0.92;
        }

        .psc-slice .slice-compact .slice-label {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .psc-slice .slice-compact .slice-micro-val {
            font-size: 0.65rem;
            font-weight: 700;
            opacity: 0.8;
        }

        .psc-slice .slice-compact .slice-mini-avatar {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #EECA3E;
        }

        /* Expanded view (when slice IS hovered) */
        .psc-slice .slice-expanded {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            height: 100%;
            padding: 12px 10px;
            opacity: 0;
            transform: scale(0.92);
            transition: opacity 0.35s ease 0.1s, transform 0.35s ease 0.1s;
        }

        .psc-slice:hover {
            flex: 5.6;
            box-shadow: inset 0 0 25px rgba(255, 255, 255, 0.22), 0 0 25px rgba(0, 0, 0, 0.6);
            z-index: 5;
        }

        .psc-slice.slice-speaker:hover {
            flex: 7.8;
        }

        .psc-slice:hover .slice-compact {
            display: none;
        }

        .psc-slice:hover .slice-expanded {
            display: flex;
            opacity: 1;
            transform: scale(1);
        }

        /* Slice Details Elements */
        .slice-badge {
            font-size: 0.74rem;
            font-weight: 800;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .slice-highlight-val {
            font-size: 1.15rem;
            font-weight: 900;
            color: #EECA3E;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .slice-sub-info {
            font-size: 0.78rem;
            opacity: 0.88;
            margin-bottom: 8px;
        }

        .slice-tag {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
        }

        .slice-tag-dark {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 50px;
            background: rgba(2, 20, 43, 0.22);
            border: 1px solid rgba(2, 20, 43, 0.35);
            color: #02142B;
            display: inline-flex;
            align-items: center;
        }

        /* Speaker Slice Elements (Large Rectangular Portrait filling ~2/3 of space) */
        .slice-speaker-portrait {
            width: 90%;
            max-width: 220px;
            height: 185px;
            border-radius: 14px;
            margin-bottom: 8px;
            overflow: hidden;
            border: 2.5px solid #EECA3E;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.75), 0 0 24px rgba(238, 202, 62, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(2, 20, 43, 0.85);
            flex-shrink: 0;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .psc-slice.slice-speaker:hover .slice-speaker-portrait {
            transform: scale(1.03);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.85), 0 0 30px rgba(238, 202, 62, 0.6);
        }

        .slice-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
        }

        .slice-avatar-placeholder {
            font-size: 3.5rem;
            color: #EECA3E;
        }

        .slice-speaker-name {
            font-size: 1.08rem;
            font-weight: 900;
            color: #ffffff;
            margin-top: 4px;
            margin-bottom: 2px;
            line-height: 1.3;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.85);
        }

        .slice-speaker-role {
            font-size: 0.78rem;
            color: #93c5fd;
            margin-bottom: 6px;
            line-height: 1.35;
        }

        /* Track Slice Elements */
        .slice-track-name {
            font-size: 1.05rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 4px;
        }

        /* Details Slice Elements */
        .slice-desc-text {
            font-size: 0.78rem;
            line-height: 1.5;
            margin-bottom: 8px;
            color: #02142B;
            font-weight: 600;
            text-align: center;
        }

        /* Venue Slice Elements */
        .slice-hall-name {
            font-size: 1.05rem;
            font-weight: 900;
            color: #02142B;
            margin-bottom: 4px;
        }

        .slice-cta-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 50px;
            background: #02142B;
            color: #EECA3E;
            font-size: 0.75rem;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .slice-cta-action:hover {
            background: #043C73;
            color: #ffffff;
            transform: scale(1.05);
        }

        /* Card Footer Strip */
        .psc-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.82rem;
        }

        .psc-hint {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.74rem;
            font-weight: 600;
        }

        .psc-link {
            color: #38BDF8;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .psc-link:hover {
            color: #EECA3E;
            transform: translateX(-4px);
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

        /* ══════════════════════════════════════════════════
           GRADUATION PROJECTS UIVERSE WALLET SYSTEM (byllzz)
           Adapted to Fair Identity (#043C73, #EECA3E, #38BDF8)
        ══════════════════════════════════════════════════ */
        .project-wallet-col {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .project-wallet {
            position: relative;
            width: 310px;
            height: 270px;
            cursor: pointer;
            perspective: 1000px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            transition: transform 0.4s ease;
            margin: 55px auto 60px auto;
        }

        .project-wallet::after {
            content: "مرّر المؤشر لتصفح بطاقات المشروع ✦";
            position: absolute;
            bottom: -32px;
            font-size: 12px;
            font-weight: 700;
            color: #EECA3E;
            text-decoration: none;
            letter-spacing: 0.5px;
            opacity: 0.9;
            transition: all 0.3s ease;
            white-space: nowrap;
            pointer-events: none;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
        }

        .project-wallet:hover::after {
            color: #38BDF8;
            transform: translateY(2px);
        }

        @keyframes walletSlideIntoPocket {
            0% {
                transform: translateY(-90px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Wallet Back */
        .project-wallet .wallet-back {
            position: absolute;
            bottom: 0;
            width: 310px;
            height: 220px;
            background: linear-gradient(160deg, #021226 0%, #043c73 100%);
            border-radius: 22px 22px 60px 60px;
            border: 1.5px solid rgba(56, 189, 248, 0.3);
            z-index: 5;
            box-shadow: inset 0 25px 35px rgba(0, 0, 0, 0.65), inset 0 5px 15px rgba(0, 0, 0, 0.7), 0 15px 35px rgba(0, 0, 0, 0.45);
        }

        /* Project Cards */
        .project-wallet .p-card {
            position: absolute;
            width: 286px;
            height: 155px;
            left: 12px;
            border-radius: 18px;
            padding: 13px 16px;
            color: #ffffff;
            text-decoration: none;
            direction: rtl;
            box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.35), 0 -4px 18px rgba(0, 0, 0, 0.3);
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, z-index 0s;
            animation: walletSlideIntoPocket 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) backwards;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .project-wallet .p-card-inner {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .project-wallet .p-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            font-weight: 800;
        }

        .project-wallet .p-card-top .p-card-type {
            letter-spacing: 0.3px;
        }

        .project-wallet .p-chip {
            width: 28px;
            height: 22px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 5px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .project-wallet .p-card-body {
            flex: 1;
            display: flex;
            align-items: center;
            margin: 4px 0;
        }

        .project-wallet .p-card-summary {
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: right;
        }

        .project-wallet .p-team-lead {
            font-size: 12px;
            font-weight: 800;
            color: #02142B;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .project-wallet .p-team-badge {
            font-size: 10px;
            background: rgba(2, 20, 43, 0.15);
            padding: 2px 7px;
            border-radius: 50px;
            font-weight: 700;
        }

        .project-wallet .p-project-title {
            font-size: 12px;
            font-weight: 800;
            color: #043C73;
            margin: 0;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: right;
        }

        .project-wallet .p-card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .project-wallet .p-label {
            font-size: 9px;
            opacity: 0.8;
            margin-bottom: 1px;
            display: block;
            font-weight: 700;
        }

        .project-wallet .p-value {
            font-size: 11px;
            font-weight: 800;
        }

        .project-wallet .p-visible-badge {
            font-size: 10px;
            font-weight: 800;
            color: #043C73;
            background: rgba(4, 60, 115, 0.08);
            padding: 2px 8px;
            border-radius: 50px;
            border: 1px solid rgba(4, 60, 115, 0.15);
        }

        .project-wallet .p-hidden-cta {
            display: none;
            font-size: 10.5px;
            font-weight: 800;
            color: #ffffff;
            background: linear-gradient(135deg, #043C73, #0284c7);
            padding: 3px 10px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(4, 60, 115, 0.35);
        }

        /* Card Variants */
        /* 1. Deepest Card: Cyan / Innovation Summary */
        .project-wallet .card-cyan {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 40%, #043c73 100%);
            border: 1px solid rgba(56, 189, 248, 0.55);
            bottom: 95px;
            z-index: 10;
            animation-delay: 0.1s;
        }

        /* 2. Middle Card: Gold / Researchers Team */
        .project-wallet .card-gold {
            background: linear-gradient(135deg, #EECA3E 0%, #f59e0b 50%, #d97706 100%);
            border: 1px solid rgba(254, 240, 138, 0.7);
            color: #02142B;
            bottom: 68px;
            z-index: 20;
            animation-delay: 0.2s;
        }
        .project-wallet .card-gold .p-chip {
            background: rgba(0, 0, 0, 0.1);
            border-color: rgba(0, 0, 0, 0.2);
            color: #02142B;
        }
        .project-wallet .card-gold .p-label {
            color: rgba(2, 20, 43, 0.75);
        }

        /* 3. Front Card: White / Title & Accreditation */
        .project-wallet .card-white {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.95);
            color: #043C73;
            bottom: 40px;
            z-index: 30;
            animation-delay: 0.3s;
        }
        .project-wallet .card-white .p-chip {
            background: rgba(4, 60, 115, 0.08);
            border-color: rgba(4, 60, 115, 0.2);
            color: #043C73;
        }
        .project-wallet .card-white .p-label {
            color: #64748b;
        }

        /* The Pocket */
        .project-wallet .pocket {
            position: absolute;
            bottom: 0;
            width: 310px;
            height: 170px;
            z-index: 40;
            filter: drop-shadow(0 15px 30px rgba(0, 10, 25, 0.85));
            pointer-events: none;
        }

        .project-wallet .pocket-svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .project-wallet .pocket-content {
            position: absolute;
            top: 36px;
            width: 100%;
            text-align: center;
            z-index: 50;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 0 16px;
        }

        .project-wallet .pocket-meta-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .project-wallet .pocket-booth-tag {
            font-size: 10.5px;
            font-weight: 800;
            color: #EECA3E;
            background: rgba(238, 202, 62, 0.15);
            border: 1px solid rgba(238, 202, 62, 0.4);
            padding: 1px 9px;
            border-radius: 50px;
        }

        .project-wallet .pocket-year-tag {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.08);
            padding: 1px 7px;
            border-radius: 50px;
        }

        .project-wallet .pocket-title-box {
            position: relative;
            height: 24px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .project-wallet .pocket-stars {
            color: #EECA3E;
            font-size: 18px;
            letter-spacing: 5px;
            transition: 0.3s;
        }

        .project-wallet .pocket-real-title {
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 800;
            opacity: 0;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translate(-50%, 8px);
            transition: 0.3s;
            width: 90%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .project-wallet .pocket-faculty-label {
            color: #38BDF8;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 90%;
        }

        /* Eye Icon Logic */
        .project-wallet .eye-icon-wrapper {
            margin-top: 2px;
            height: 20px;
            width: 20px;
            position: relative;
            opacity: 0.4;
            transition: 0.3s;
        }

        .project-wallet .eye-icon {
            position: absolute;
            top: 0;
            left: 0;
            transition: 0.3s;
        }

        .project-wallet .eye-open {
            opacity: 0;
        }

        /* ── Hover Effects (Uiverse Mechanics) ── */
        .project-wallet:hover {
            transform: translateY(-8px);
        }

        .project-wallet:hover .eye-icon-wrapper {
            opacity: 1;
        }

        .project-wallet:hover .eye-slash {
            opacity: 0;
            transform: scale(0.5);
        }

        .project-wallet:hover .eye-open {
            opacity: 1;
            transform: scale(1.15);
        }

        .project-wallet:hover .pocket-stars {
            opacity: 0;
        }

        .project-wallet:hover .pocket-real-title {
            opacity: 1;
            transform: translate(-50%, 0);
        }

        /* Hover on wallet pushes cards out */
        .project-wallet:hover .card-cyan {
            transform: translateY(-85px) rotate(-4deg);
        }

        .project-wallet:hover .card-gold {
            transform: translateY(-52px) rotate(3deg);
        }

        .project-wallet:hover .card-white {
            transform: translateY(-18px);
        }

        /* Individual Card Hover (Super Focus) */
        .project-wallet .p-card:hover {
            z-index: 100 !important;
            transition-delay: 0s !important;
        }

        .project-wallet:hover .card-cyan:hover {
            transform: translateY(-92px) scale(1.06) rotate(0) !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.7), 0 0 28px rgba(56, 189, 248, 0.55);
            border-color: #38BDF8;
        }

        .project-wallet:hover .card-gold:hover {
            transform: translateY(-78px) scale(1.06) rotate(0) !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.7), 0 0 28px rgba(238, 202, 62, 0.65);
            border-color: #EECA3E;
        }

        .project-wallet:hover .card-white:hover {
            transform: translateY(-68px) scale(1.06) rotate(0) !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.7), 0 0 28px rgba(255, 255, 255, 0.7);
            border-color: #ffffff;
        }

        .project-wallet:hover .card-white:hover .p-visible-badge {
            display: none;
        }

        .project-wallet:hover .card-white:hover .p-hidden-cta {
            display: inline-flex;
            align-items: center;
        }

        /* ══════════════════════════════════════════════════
           LOADING WAVE COMPONENT (From Uiverse.io by mrpumps31232)
           Customized to Exhibition Brand Identity (#043C73, #EECA3E, #38BDF8)
        ══════════════════════════════════════════════════ */
        .loading-wave-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
            text-align: center;
        }

        .loading-wave {
            width: 240px;
            height: 75px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
            margin: 0 auto 16px auto;
        }

        .loading-bar {
            width: 16px;
            height: 10px;
            margin: 0 6px;
            border-radius: 6px;
            animation: loading-wave-animation 1s ease-in-out infinite;
            transition: all 0.3s ease;
        }

        /* Exhibition Brand Colors with Neon Glow */
        .loading-bar:nth-child(1) {
            background: linear-gradient(180deg, #38BDF8, #0284C7);
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.65);
            animation-delay: 0s;
        }

        .loading-bar:nth-child(2) {
            background: linear-gradient(180deg, #EECA3E, #F59E0B);
            box-shadow: 0 0 16px rgba(238, 202, 62, 0.65);
            animation-delay: 0.12s;
        }

        .loading-bar:nth-child(3) {
            background: linear-gradient(180deg, #60A5FA, #043C73);
            box-shadow: 0 0 16px rgba(96, 165, 250, 0.65);
            animation-delay: 0.24s;
        }

        .loading-bar:nth-child(4) {
            background: linear-gradient(180deg, #FDE047, #EECA3E);
            box-shadow: 0 0 16px rgba(253, 224, 71, 0.65);
            animation-delay: 0.36s;
        }

        @keyframes loading-wave-animation {
            0% {
                height: 10px;
                opacity: 0.55;
            }
            50% {
                height: 52px;
                opacity: 1;
            }
            100% {
                height: 10px;
                opacity: 0.55;
            }
        }

        .loading-wave-text {
            font-size: 0.96rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.3px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .loading-wave-subtext {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 5px;
            font-weight: 500;
        }

        /* Mini Inline Wave (Buttons & Badges) */
        .loading-wave-sm {
            width: auto;
            height: 18px;
            display: inline-flex;
            align-items: flex-end;
            gap: 3px;
            vertical-align: middle;
        }
        .loading-wave-sm .loading-bar {
            width: 4px;
            height: 5px;
            margin: 0;
            border-radius: 3px;
            animation: loading-wave-sm-animation 0.9s ease-in-out infinite;
        }
        .loading-wave-sm .loading-bar:nth-child(1) { animation-delay: 0s; box-shadow: 0 0 8px rgba(56, 189, 248, 0.6); }
        .loading-wave-sm .loading-bar:nth-child(2) { animation-delay: 0.12s; box-shadow: 0 0 8px rgba(238, 202, 62, 0.6); }
        .loading-wave-sm .loading-bar:nth-child(3) { animation-delay: 0.24s; box-shadow: 0 0 8px rgba(96, 165, 250, 0.6); }
        .loading-wave-sm .loading-bar:nth-child(4) { animation-delay: 0.36s; box-shadow: 0 0 8px rgba(253, 224, 71, 0.6); }

        @keyframes loading-wave-sm-animation {
            0%, 100% { height: 5px; opacity: 0.6; }
            50% { height: 16px; opacity: 1; }
        }

        /* Global Cyber Processing Overlay */
        .cyber-processing-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 18, 38, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            z-index: 10999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }
        .cyber-processing-overlay.is-active {
            opacity: 1;
            visibility: visible;
        }
        .cyber-processing-box {
            background: linear-gradient(160deg, rgba(4, 32, 66, 0.96) 0%, rgba(2, 18, 38, 0.98) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.45);
            border-radius: 24px;
            padding: 32px 42px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85), 0 0 35px rgba(4, 93, 176, 0.4);
            text-align: center;
            min-width: 310px;
            max-width: 420px;
            position: relative;
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
            margin: 0.5rem 0 2rem;
        }

        /* مسرح الأسطوانة */
        .cyber-cylinder-wrapper {
            width: 100%;
            height: 490px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            user-select: none;
            padding: 0;
            margin-bottom: 3rem;
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
            top: 48%;
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

        /* إيقاف الدوران فقط وحصرياً عند وضع المؤشر مباشرة فوق إحدى البطاقات وليس من مسافة بعيدة */
        .cyber-cylinder-inner:has(.cyber-cylinder-card:hover),
        .cyber-cylinder-inner.is-card-hovered,
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
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            cursor: pointer;
            text-align: center;
            backface-visibility: hidden;
            will-change: transform;
        }

        .cyber-cylinder-card:hover {
            border-color: rgba(var(--color-card, 56, 189, 248), 1);
            border-top-color: #ffffff;
            box-shadow: 
                0 32px 75px rgba(0, 0, 0, 0.9), 
                0 0 50px rgba(var(--color-card, 56, 189, 248), 0.85), 
                inset 0 0 30px rgba(var(--color-card, 56, 189, 248), 0.35),
                inset 0 2px 5px rgba(255, 255, 255, 0.6);
            background: radial-gradient(
                circle at 50% 15%,
                rgba(var(--color-card, 56, 189, 248), 0.45) 0%,
                rgba(4, 45, 95, 0.96) 55%,
                rgba(2, 16, 38, 1) 100%
            );
            transform: rotateY(calc((360deg / var(--quantity)) * var(--index))) translateZ(calc(var(--translateZ) + 24px)) scale(1.05);
            z-index: 25;
        }

        .cyber-cylinder-card:hover .cylinder-identity-hub {
            transform: translateZ(52px);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 94px;
            margin: 0.15rem auto 0.45rem;
            position: relative;
            z-index: 6;
        }

        .cyber-cylinder-card:hover .cylinder-content-body {
            transform: translateZ(36px);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            position: relative;
            z-index: 5;
        }

        .cyber-cylinder-card:hover .cylinder-action-cta {
            transform: translateZ(42px);
            width: 100%;
            margin-top: auto;
            padding: 7px 12px;
            border-radius: 10px;
            background: transparent !important;
            border: 1.5px solid rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
            font-size: 0.74rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.28s ease;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            box-shadow: none !important;
            position: relative;
            z-index: 6;
        }

        .cyber-cylinder-card:hover .cylinder-action-cta {
            background: rgba(var(--color-card, 56, 189, 248), 0.2) !important;
            border-color: rgba(var(--color-card, 56, 189, 248), 0.95) !important;
            box-shadow: 0 0 20px rgba(var(--color-card, 56, 189, 248), 0.4) !important;
            color: #ffffff !important;
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
            width: 100%;
            height: 94px;
            background: transparent !important;
            border-radius: 14px;
            padding: 4px 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none !important;
            border: none !important;
            transition: transform 0.35s ease;
        }

        .cyber-cylinder-card:hover .cylinder-logo-dock {
            transform: scale(1.05);
        }

        .cylinder-logo-dock img {
            max-width: 92%;
            max-height: 84px;
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.85)) drop-shadow(0 0 16px rgba(var(--color-card, 56, 189, 248), 0.4));
            transition: filter 0.35s ease, transform 0.35s ease;
        }

        .cyber-cylinder-card:hover .cylinder-logo-dock img {
            filter: drop-shadow(0 16px 30px rgba(0, 0, 0, 0.95)) drop-shadow(0 0 25px rgba(var(--color-card, 56, 189, 248), 0.75));
            transform: scale(1.08);
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
            background: transparent !important;
            border-radius: 18px;
            padding: 4px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none !important;
            border: none !important;
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

        .companies-dir-cta-wrap {
            position: relative;
            z-index: 10;
            margin-top: 5rem;
            padding-top: 1.5rem;
            clear: both;
        }

        @media (max-width: 991px) {
            .companies-dir-cta-wrap {
                margin-top: 2.5rem;
                padding-top: 0.8rem;
            }
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
            width: 120px;
            height: 95px;
            background: transparent !important;
            border: none !important;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            box-shadow: none !important;
        }

        .company-modal-logo-wrapper img {
            max-width: 100%;
            max-height: 90px;
            object-fit: contain;
            filter: drop-shadow(0 10px 24px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 20px rgba(56, 189, 248, 0.45));
        }

        #modalCompanyLogoFallback {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(4, 60, 115, 0.9) 0%, rgba(2, 20, 43, 0.95) 100%);
            border: 1.5px solid rgba(56, 189, 248, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-size: 1.8rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5), 0 0 20px rgba(56, 189, 248, 0.3);
        }

        /* شارات المسمى الوظيفي فقط - مظهر فائق الأناقة والجاذبية */
        .company-job-title-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, rgba(4, 60, 115, 0.55) 0%, rgba(2, 20, 43, 0.8) 100%);
            border: 1.2px solid rgba(56, 189, 248, 0.45);
            color: #f0f9ff;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .company-job-title-pill i {
            color: #38bdf8;
            font-size: 0.85rem;
        }

        .company-job-title-pill:hover {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.45) 0%, rgba(238, 202, 62, 0.25) 100%);
            border-color: rgba(238, 202, 62, 0.8);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(56, 189, 248, 0.3);
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
            background: transparent !important;
            border: 2px solid #EECA3E !important;
            color: #EECA3E !important;
            box-shadow: inset 0 0 0 0 #EECA3E !important;
            transition: ease-out 0.5s !important;
        }

        .hero-action-buttons .nav-btn-gold::before {
            display: none !important;
        }

        .hero-action-buttons .nav-btn-gold:hover {
            color: #042444 !important;
            border-color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #EECA3E !important;
            transform: none !important;
        }

        .hero-action-buttons .nav-btn-visitor {
            position: relative;
            background: transparent !important;
            border: 2px solid #10B981 !important;
            color: #10B981 !important;
            box-shadow: inset 0 0 0 0 #10B981 !important;
            transition: ease-out 0.5s !important;
        }

        .hero-action-buttons .nav-btn-visitor:hover {
            color: #ffffff !important;
            border-color: #10B981 !important;
            box-shadow: inset 0 -100px 0 0 #10B981 !important;
            transform: none !important;
        }

        .hero-action-buttons .nav-btn-outline {
            position: relative;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 2px solid rgba(255, 255, 255, 0.28) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            transition: ease-out 0.5s !important;
            color: #ffffff !important;
            box-shadow: inset 0 0 0 0 #043C73 !important;
        }

        .hero-action-buttons .nav-btn-outline:hover {
            background: transparent !important;
            border-color: #EECA3E !important;
            color: #EECA3E !important;
            box-shadow: inset 0 -100px 0 0 #043C73 !important;
            transform: none !important;
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
                <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-login">
                    <i class="fas fa-th-large me-1"></i>لوحة التحكم
                </a>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
                    <a href="{{ route('job-fair.admin.index') }}" class="nav-btn nav-btn-gold">
                        <i class="fas fa-calendar-check me-1"></i>إدارة الفعاليات
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="nav-btn nav-btn-login">تسجيل الدخول</a>
                <a href="{{ route('graduate.register') }}" class="nav-btn nav-btn-gold d-none d-sm-inline-flex">
                    <i class="fas fa-user-plus me-1"></i>سجّل كخريج
                </a>
                @if(isset($fair) && $fair)
                    <button type="button" class="nav-btn nav-btn-visitor d-none d-md-inline-flex" data-bs-toggle="modal"
                        data-bs-target="#visitorRegisterModal">
                        <i class="fas fa-id-badge"></i>
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
                                <button type="button" class="nav-btn nav-btn-visitor" data-bs-toggle="modal"
                                    data-bs-target="#visitorRegisterModal">
                                    <i class="fas fa-id-badge"></i>
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

                    {{-- 1. البانر الرئيسي للراعي الاستراتيجي (Strategic Hero Banner - Uiverse Luxury Aesthetic) --}}
                    <div class="strategic-uiverse-hero">
                        <div class="uiverse-border"></div>
                        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-4 p-4 p-lg-5 position-relative z-2">
                            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4 text-center text-md-end">
                                <div class="strategic-logo-frame">
                                    <img src="{{ $stratLogo }}" alt="{{ $stratName }}" style="max-height: 68px; max-width: 240px; object-fit: contain;"
                                         onerror="this.src='{{ asset('images/wahaexpo_horizontal_white.png') }}';">
                                </div>
                                <div>
                                    <span class="sponsor-tier-chip tier-strategic mb-2 d-inline-flex">
                                        <i class="fas fa-crown text-warning me-1"></i>
                                        <span>الراعي والشريك الاستراتيجي</span>
                                    </span>
                                    <h3 class="mb-1 fw-bold text-white fs-4">{{ $stratName }}</h3>
                                    <p class="text-white-50 mb-0 d-none d-md-block" style="max-width: 580px; font-size: 0.92rem; line-height: 1.6;">
                                        {{ $stratDesc }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ $stratWebsite }}" target="_blank" rel="noopener noreferrer"
                                   class="btn text-decoration-none rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2"
                                   style="background: linear-gradient(135deg, #EECA3E, #f59e0b); color: #02142B; box-shadow: 0 6px 20px rgba(238, 202, 62, 0.4);">
                                    <span>زيارة المنصة الرسمية</span>
                                    <i class="fas fa-external-link-alt" style="font-size:0.8rem;"></i>
                                </a>
                            </div>
                        </div>
                        <span class="bottom-text">الراعي الاستراتيجي • معرض جامعة طرابلس</span>
                    </div>

                    {{-- 2. بطاقات الرعاة المعتمدين بنظام Uiverse Smit-Prajapati بألوان هوية المعرض --}}
                    @if($otherSponsors->count() > 0)
                        <div class="podium-divider-wrap mb-4">
                            <div class="podium-divider-line"></div>
                            <div class="podium-divider-pill">
                                <i class="fas fa-layer-group text-info"></i>
                                <span>الرعاة والشركاء المعتمدون</span>
                            </div>
                            <div class="podium-divider-line"></div>
                        </div>

                        <div class="sponsors-uiverse-grid">
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
                                    $tierRawLbl = $sponsor->tier_label ?: match($tKey) {
                                        'diamond', 'الراعي الماسي' => 'الراعي الماسي',
                                        'platinum', 'الراعي البلاتيني' => 'الراعي البلاتيني',
                                        'gold', 'الراعي الذهبي' => 'الراعي الذهبي',
                                        'bronze', 'الراعي البرونزي' => 'الراعي البرونزي',
                                        default => 'الراعي الفضي',
                                    };
                                    $tierPackageName = Str::startsWith($tierRawLbl, 'باقة') ? $tierRawLbl : ('باقة ' . $tierRawLbl);
                                    $monogramLetter = mb_substr(trim($sponsor->name), 0, 1, 'UTF-8');
                                    $sponsorUrl = $sponsor->website ? (Str::startsWith($sponsor->website, 'http') ? $sponsor->website : 'https://' . $sponsor->website) : '#';

                                    // حل دقيق وموثوق لمسار شعار الراعي
                                    $sLogo = $sponsor->logo_path ? trim($sponsor->logo_path) : null;
                                    $resolvedLogo = null;
                                    if ($sLogo) {
                                        if (str_starts_with($sLogo, 'http://') || str_starts_with($sLogo, 'https://')) {
                                            $resolvedLogo = $sLogo;
                                        } elseif (str_starts_with($sLogo, 'images/')) {
                                            $resolvedLogo = asset($sLogo);
                                        } elseif (str_starts_with($sLogo, '/storage/') || str_starts_with($sLogo, 'storage/')) {
                                            $resolvedLogo = asset(ltrim($sLogo, '/'));
                                        } else {
                                            $resolvedLogo = Storage::url($sLogo);
                                        }
                                    }
                                @endphp

                                <a href="{{ $sponsorUrl }}" target="{{ $sponsor->website ? '_blank' : '_self' }}" rel="noopener noreferrer"
                                   class="sponsor-uiverse-card {{ $tierClass }}" title="{{ $sponsor->name }} — {{ $tierPackageName }}">
                                    <div class="uiverse-border"></div>
                                    <div class="card-inner">
                                        {{-- 1. باقة الرعاية في الأعلى --}}
                                        <div class="sponsor-tier-chip">
                                            <i class="{{ $tierIcon }}"></i>
                                            <span>{{ $tierPackageName }}</span>
                                        </div>

                                        {{-- 2. إطار الشعار المضيء --}}
                                        <div class="sponsor-logo-box">
                                            @if($resolvedLogo)
                                                <img src="{{ $resolvedLogo }}" alt="{{ $sponsor->name }}" loading="lazy"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="sponsor-monogram-box" style="display:none;">
                                                    <span>{{ $monogramLetter }}</span>
                                                </div>
                                            @else
                                                <div class="sponsor-monogram-box">
                                                    <span>{{ $monogramLetter }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- 3. اسم الشركة الراعية كاملاً وبوضوح تام بدون أي اقتطاع --}}
                                        <div class="sponsor-name-wrap">
                                            <h4 class="sponsor-brand-title">{{ $sponsor->name }}</h4>
                                        </div>

                                        {{-- 4. إجراء / شارة أسفل البطاقة --}}
                                        <div class="sponsor-bottom-action">
                                            @if($sponsor->website)
                                                <span class="action-btn-text">
                                                    <span>زيارة الموقع الرسمي</span>
                                                    <i class="fas fa-external-link-alt"></i>
                                                </span>
                                            @else
                                                <span class="action-btn-text">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>شريك معتمد</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
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
                // ألوان هوية المعرض الرسمية الحصرية لبطاقات وإطارات الأسطوانة (الأزرق السياني والذهبي الملكي فقط)
                $cardColors = [
                    '56, 189, 248',   // أزرق سياني سيبراني المعرض (#38bdf8)
                    '238, 202, 62',   // ذهبي ملكي فاخر للمعرض (#eeca3e)
                ];

                // للأسطوانة ثلاثية الأبعاد: إدراج كافة الشركات المشاركة في المعرض بالكامل
                $cylinderCompanies = collect();
                $baseCompCount = $companies->count();
                if ($baseCompCount > 0) {
                    if ($baseCompCount >= 6) {
                        // إدراج كافة الشركات المشاركة دون استثناء
                        $cylinderCompanies = $companies;
                    } else {
                        // في حال كان عدد الشركات قليلاً (أقل من 6)، نكررها حتى تكتمل الدائرة الهندسية للأسطوانة 3D
                        $minCylinderQty = 8;
                        for ($ci = 0; $ci < $minCylinderQty; $ci++) {
                            $cylinderCompanies->push($companies[$ci % $baseCompCount]);
                        }
                    }
                }
                $cylinderQty = $cylinderCompanies->count();

                // حساب نصف القطر المناسب 3D هندسياً حسب عدد الشركات لضمان التباعد المثالي وعدم تداخل البطاقات
                $cardW = 235;
                $calcRadius = (int) round(($cardW / (2 * tan(M_PI / max($cylinderQty, 3))))) + 25;
                $cylinderTranslateZ = max(420, $calcRadius);
                $cylinderSpeed = max(26, (int) round($cylinderQty * 2.8));

                // عرض معاينة الشركات في الشبكة
                $previewCompanies = $companies;
            @endphp
            <section class="companies-section cyber-matrix-wrap" id="companies">
                <div class="container position-relative z-2">
                    <div class="text-center mb-2">
                        <div class="cyber-section-badge"><i class="fas fa-building me-1"></i>الشركات والمؤسسات المشاركة</div>
                        <h2 class="cyber-section-title scramble-on-scroll" data-scramble="أجنحة الشركات المشاركة" title="انقر لإعادة فك التشفير">أجنحة الشركات المشاركة</h2>
                        <p class="cyber-section-subtitle mb-0">{{ $countText }} في المعرض — انقر على أي جناح للتعرف على فرصه وتفاصيله
                        </p>
                    </div>

                    {{-- مشهد الأسطوانة ثلاثية الأبعاد (3D Cylinder Showcase) --}}
                    <div class="cyber-cylinder-scene" id="companiesCylinderScene">
                        {{-- مسرح الأسطوانة ثلاثية الأبعاد 3D --}}
                        <div class="cyber-cylinder-wrapper" id="companiesCylinderWrapper">
                            <div class="cylinder-floor-glow"></div>
                            <div class="cyber-cylinder-inner" id="companiesCylinderInner" style="--quantity: {{ $cylinderQty }}; --translateZ: {{ $cylinderTranslateZ }}px; animation-duration: {{ $cylinderSpeed }}s;">
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
                                    @php
                                        $jobTitles = ($fc->company && $fc->company->jobOpportunities) ? $fc->company->jobOpportunities->where('status', 'open')->pluck('title')->values()->all() : [];
                                    @endphp
                                    <div role="button" tabindex="0" class="cyber-cylinder-card"
                                        style="--index: {{ $cIndex }}; --color-card: {{ $colorCard }};"
                                        data-bs-toggle="modal" data-bs-target="#companyDetailModal"
                                        data-name="{{ $companyName }}" data-logo="{{ $companyLogo }}"
                                        data-industry="{{ $industry }}"
                                        data-booth="{{ $fc->booth_number ? strtoupper($fc->booth_number) : '' }}"
                                        data-jobs="{{ $pos }}" data-desc="{{ $companyDesc }}"
                                        data-job-titles="{{ json_encode($jobTitles) }}"
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
                            @php
                                $gridJobTitles = ($fc->company && $fc->company->jobOpportunities) ? $fc->company->jobOpportunities->where('status', 'open')->pluck('title')->values()->all() : [];
                            @endphp
                            <div role="button" tabindex="0" class="company-booth-card" data-bs-toggle="modal"
                                data-bs-target="#companyDetailModal" data-name="{{ $companyName }}" data-logo="{{ $companyLogo }}"
                                data-industry="{{ $industry }}"
                                data-booth="{{ $fc->booth_number ? strtoupper($fc->booth_number) : '' }}" data-jobs="{{ $pos }}"
                                data-desc="{{ $companyDesc }}"
                                data-job-titles="{{ json_encode($gridJobTitles) }}"
                                data-website="{{ $fc->company->website ?? '' }}"
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
                    <div class="companies-dir-cta-wrap text-center">
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
                                    $isCenterCard = ($idx === 1);
                                    $speakerImg = $event->speaker_image_url ?: '';
                                    $startTimeStr = $event->start_time ? $event->start_time->format('H:i') : '10:00';
                                    $endTimeStr = $event->end_time ? $event->end_time->format('H:i') : '12:30';
                                    $dateStr = $event->start_time ? $event->start_time->format('Y-m-d') : 'اليوم الأول';
                                    $locationStr = $event->location ?: 'المدرج الرئيسي';
                                @endphp
                                <div class="col-lg-4 col-md-6">
                                    <div class="program-slides-card {{ $isCenterCard ? 'is-featured' : '' }}">
                                        {{-- Header bar --}}
                                        <div class="psc-header">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="cyber-track-pill {{ $typeClass }}">
                                                    <i class="{{ $event->type_icon }} me-1"></i>{{ $event->type_short_label ?: 'ورشة تدريبية' }}
                                                </span>
                                                <span class="psc-time-badge">
                                                    <i class="far fa-clock text-warning me-1"></i>{{ $startTimeStr }} - {{ $endTimeStr }}
                                                </span>
                                            </div>
                                            <h3 class="psc-title" title="{{ $event->title }}">
                                                {{ $event->title }}
                                            </h3>
                                        </div>

                                        {{-- Interactive 5 Slices (الشرائح التفاعلية الخمسة بألوان هوية المعرض) --}}
                                        <div class="psc-slices-wrapper">
                                            {{-- 1. شريحة التوقيت والجدول --}}
                                            <div class="psc-slice slice-time">
                                                <div class="slice-compact">
                                                    <i class="far fa-clock slice-icon text-warning"></i>
                                                    <span class="slice-label">التوقيت</span>
                                                    <span class="slice-micro-val">{{ $startTimeStr }}</span>
                                                </div>
                                                <div class="slice-expanded">
                                                    <div class="slice-badge"><i class="far fa-clock me-1 text-warning"></i> التوقيت والجدول</div>
                                                    <div class="slice-highlight-val">{{ $startTimeStr }} — {{ $endTimeStr }}</div>
                                                    <div class="slice-sub-info"><i class="far fa-calendar-alt me-1"></i> {{ $dateStr }}</div>
                                                    <span class="slice-tag">تأكيد الموعد</span>
                                                </div>
                                            </div>

                                            {{-- 2. شريحة المحاضر والمدرب --}}
                                            <div class="psc-slice slice-speaker">
                                                <div class="slice-compact">
                                                    @if($speakerImg)
                                                        <img src="{{ $speakerImg }}" alt="{{ $event->speaker_name }}" class="slice-mini-avatar">
                                                    @else
                                                        <i class="fas fa-user-tie slice-icon text-info"></i>
                                                    @endif
                                                    <span class="slice-label">المحاضر</span>
                                                    <span class="slice-micro-val text-truncate" style="max-width:45px;">{{ Str::limit($event->speaker_name ?: 'المدرب', 6) }}</span>
                                                </div>
                                                <div class="slice-expanded">
                                                    <div class="slice-speaker-portrait">
                                                        @if($speakerImg)
                                                            <img src="{{ $speakerImg }}" alt="{{ $event->speaker_name }}" class="slice-avatar-img">
                                                        @else
                                                            <div class="slice-avatar-placeholder"><i class="fas fa-user-tie"></i></div>
                                                        @endif
                                                    </div>
                                                    <div class="slice-speaker-name">{{ $event->speaker_name ?: 'نخبة من المدربين والخبراء' }}</div>
                                                    <div class="slice-speaker-role">{{ $event->speaker_title ?: 'خبير ومحاضر معتمد' }}</div>
                                                    <span class="slice-tag"><i class="fas fa-certificate text-warning me-1"></i> مدرب معتمد</span>
                                                </div>
                                            </div>

                                            {{-- 3. شريحة المحور ونوع الجلسة --}}
                                            <div class="psc-slice slice-track">
                                                <div class="slice-compact">
                                                    <i class="{{ $event->type_icon ?: 'fas fa-layer-group' }} slice-icon text-white"></i>
                                                    <span class="slice-label">المحور</span>
                                                    <span class="slice-micro-val">المجال</span>
                                                </div>
                                                <div class="slice-expanded">
                                                    <div class="slice-badge"><i class="fas fa-compass me-1 text-warning"></i> مسار البرنامج</div>
                                                    <div class="slice-track-name">{{ $event->type_short_label ?: 'ورشة تطبيقية' }}</div>
                                                    <div class="slice-sub-info"><i class="fas fa-users me-1"></i> {{ $event->target_audience ?: 'الخريجون والباحثون عن عمل' }}</div>
                                                    <span class="slice-tag">شهادة حضور معتمدة</span>
                                                </div>
                                            </div>

                                            {{-- 4. شريحة تفاصيل ومحاور الورشة --}}
                                            <div class="psc-slice slice-details">
                                                <div class="slice-compact">
                                                    <i class="fas fa-file-alt slice-icon text-dark"></i>
                                                    <span class="slice-label">المحاور</span>
                                                    <span class="slice-micro-val">التفاصيل</span>
                                                </div>
                                                <div class="slice-expanded">
                                                    <div class="slice-badge text-dark"><i class="fas fa-bullseye me-1"></i> نبذة وأهداف</div>
                                                    <p class="slice-desc-text">
                                                        {{ Str::limit($event->description ?: 'جلسة تدريبية تطبيقية مكثفة تؤهلك لمتطلبات سوق العمل وتمكّنك من اكتساب مهارات احترافية.', 115) }}
                                                    </p>
                                                    <span class="slice-tag-dark">تطبيق عملي وتفاعلي</span>
                                                </div>
                                            </div>

                                            {{-- 5. شريحة القاعة والتسجيل --}}
                                            <div class="psc-slice slice-venue">
                                                <div class="slice-compact">
                                                    <i class="fas fa-map-marker-alt slice-icon text-dark"></i>
                                                    <span class="slice-label">القاعة</span>
                                                    <span class="slice-micro-val">الموقع</span>
                                                </div>
                                                <div class="slice-expanded">
                                                    <div class="slice-badge text-dark"><i class="fas fa-door-open me-1"></i> الموقع والقاعة</div>
                                                    <div class="slice-hall-name">{{ $locationStr }}</div>
                                                    <span class="slice-tag-dark">المقاعد محدودة</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- الشريط السفلي التفاعلي --}}
                                        <div class="psc-footer">
                                            <span class="psc-hint">
                                                <i class="fas fa-hand-pointer text-warning me-1"></i> مرر الماوس على الشرائح
                                            </span>
                                            <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="psc-link">
                                                <span>عرض التفاصيل</span>
                                                <i class="fas fa-arrow-left ms-1"></i>
                                            </a>
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
                        <div class="row g-4 mb-5 justify-content-center">
                            @foreach($previewProjects as $idx => $project)
                                @php
                                    $firstMember = (is_array($project->team_members) && count($project->team_members) > 0) ? $project->team_members[0]['name'] : 'نخبة من خريجي الكلية';
                                    $otherMembersCount = (is_array($project->team_members) && count($project->team_members) > 1) ? count($project->team_members) - 1 : 0;
                                    $projUrl = route('job-fair.public.projects.show', $project->id);
                                @endphp
                                <div class="col-lg-4 col-md-6 project-wallet-col">
                                    <div class="project-wallet" onclick="if(!event.target.closest('.p-card')){ window.location.href='{{ $projUrl }}'; }">
                                        <div class="wallet-back"></div>

                                        {{-- 1. البطاقة الأولى (Cyan): ملخص الابتكار والأهداف --}}
                                        <a href="{{ $projUrl }}" class="p-card card-cyan" title="ملخص الابتكار وقيمة المشروع">
                                            <div class="p-card-inner">
                                                <div class="p-card-top">
                                                    <span class="p-card-type"><i class="fas fa-lightbulb text-warning me-1"></i> ملخص الابتكار</span>
                                                    <div class="p-chip"><i class="fas fa-microchip"></i></div>
                                                </div>
                                                <div class="p-card-body">
                                                    <p class="p-card-summary">
                                                        {{ Str::limit($project->summary ?: $project->title, 75) }}
                                                    </p>
                                                </div>
                                                <div class="p-card-bottom">
                                                    <div>
                                                        <span class="p-label">سنة التخرج</span>
                                                        <span class="p-value">{{ $project->graduation_year }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="p-label">المشاهدات</span>
                                                        <span class="p-value"><i class="far fa-eye text-warning"></i> {{ $project->views_count ?: 120 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        {{-- 2. البطاقة الثانية (Gold): فريق العمل والطلبة --}}
                                        <a href="{{ $projUrl }}" class="p-card card-gold" title="فريق الباحثين والطلبة المشرفين">
                                            <div class="p-card-inner">
                                                <div class="p-card-top">
                                                    <span class="p-card-type"><i class="fas fa-users text-dark me-1"></i> فريق الباحثين</span>
                                                    <div class="p-chip"><i class="fas fa-user-graduate"></i></div>
                                                </div>
                                                <div class="p-card-body">
                                                    <div class="p-team-lead">
                                                        <span><i class="fas fa-user-check me-1"></i>{{ Str::limit($firstMember, 20) }}</span>
                                                        @if($otherMembersCount > 0)
                                                            <span class="p-team-badge">+ {{ $otherMembersCount }} زملاء</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="p-card-bottom">
                                                    <div>
                                                        <span class="p-label">القسم الأكاديمي</span>
                                                        <span class="p-value">{{ Str::limit($project->department, 22) }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="p-label">الإشراف</span>
                                                        <span class="p-value">{{ Str::limit($project->supervisor_name ?: 'إشراف معتمد', 18) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        {{-- 3. البطاقة الثالثة (White): عنوان المشروع والرمز الرقمي --}}
                                        <a href="{{ $projUrl }}" class="p-card card-white" title="عنوان المشروع والرمز الرقمي">
                                            <div class="p-card-inner">
                                                <div class="p-card-top">
                                                    <span class="p-card-type"><i class="fas fa-award text-primary me-1"></i> {{ Str::limit($project->faculty, 24) }}</span>
                                                    <div class="p-chip"><i class="fas fa-qrcode text-warning"></i></div>
                                                </div>
                                                <div class="p-card-body">
                                                    <h4 class="p-project-title" title="{{ $project->title }}">
                                                        {{ Str::limit($project->title, 48) }}
                                                    </h4>
                                                </div>
                                                <div class="p-card-bottom">
                                                    <div>
                                                        <span class="p-label">الجناح بالمعرض</span>
                                                        <span class="p-value">{{ $project->booth_number ?: 'الجناح الرئيسي' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="p-visible-badge">مشروع تخرج معتمد</span>
                                                        <span class="p-hidden-cta"><i class="fas fa-arrow-left me-1"></i> عرض التفاصيل</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        {{-- 4. الجيب الأمامي الفاخر (The Pocket) --}}
                                        <div class="pocket">
                                            <svg class="pocket-svg" viewBox="0 0 310 170" fill="none">
                                                <defs>
                                                    <linearGradient id="pocketGrad{{ $project->id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#02142B" />
                                                        <stop offset="50%" stop-color="#043C73" />
                                                        <stop offset="100%" stop-color="#021024" />
                                                    </linearGradient>
                                                </defs>
                                                <path
                                                    d="M 0 20 C 0 10, 6 10, 12 10 C 24 10, 30 25, 48 25 L 262 25 C 280 25, 286 10, 298 10 C 304 10, 310 10, 310 20 L 310 128 C 310 162, 288 170, 262 170 L 48 170 C 22 170, 0 162, 0 128 Z"
                                                    fill="url(#pocketGrad{{ $project->id }})"
                                                ></path>
                                                <path
                                                    d="M 9 22 C 9 16, 14 16, 18 16 C 27 16, 32 29, 48 29 L 262 29 C 278 29, 283 16, 292 16 C 296 16, 301 16, 301 22 L 301 128 C 301 158, 282 161, 262 161 L 48 161 C 28 161, 9 158, 9 128 Z"
                                                    stroke="#EECA3E"
                                                    stroke-width="1.6"
                                                    stroke-dasharray="6 4"
                                                ></path>
                                            </svg>
                                            <div class="pocket-content">
                                                <div class="pocket-meta-row">
                                                    <span class="pocket-booth-tag">
                                                        <i class="fas fa-map-pin me-1"></i>جناح {{ $project->booth_number ?: 'A-0' . ($idx + 1) }}
                                                    </span>
                                                    <span class="pocket-year-tag">سنة {{ $project->graduation_year }}</span>
                                                </div>
                                                <div class="pocket-title-box">
                                                    <div class="pocket-stars">✦ ✦ ✦ ✦ ✦</div>
                                                    <div class="pocket-real-title">{{ Str::limit($project->title, 34) }}</div>
                                                </div>
                                                <div class="pocket-faculty-label">
                                                    <i class="{{ $project->faculty_icon ?: 'fas fa-graduation-cap' }} text-warning me-1"></i>
                                                    {{ Str::limit($project->faculty, 26) }}
                                                </div>
                                                <div class="eye-icon-wrapper">
                                                    <svg class="eye-icon eye-slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                        <line x1="3" y1="3" x2="21" y2="21"></line>
                                                    </svg>
                                                    <svg class="eye-icon eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EECA3E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                </div>
                                            </div>
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
                        {{-- قسم الوظائف المتاحة بالجناح (المسمى الوظيفي فقط - مختصر جداً) --}}
                        <div class="mb-4" id="modalCompanyJobsListWrap" style="display:none;">
                            <h6 class="fw-bold mb-2 d-flex align-items-center justify-content-between" style="font-size: 0.9rem; color: #38bdf8;">
                                <span><i class="fas fa-briefcase me-2 text-warning"></i>الوظائف المتاحة بالجناح:</span>
                                <span class="badge rounded-pill" id="modalJobsCountBadge" style="font-size:0.75rem; background: rgba(56, 189, 248, 0.2); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.4);"></span>
                            </h6>
                            <div class="d-flex flex-wrap gap-2 pt-1" id="modalCompanyJobsList">
                                {{-- تُملأ ديناميكياً بالمسميات الوظيفية فقط --}}
                            </div>
                        </div>

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

        <!-- Cyber Processing Overlay (Loading Wave by mrpumps31232) -->
        <div class="cyber-processing-overlay" id="cyberProcessingOverlay" aria-hidden="true">
            <div class="cyber-processing-box">
                <div class="loading-wave">
                    <div class="loading-bar"></div>
                    <div class="loading-bar"></div>
                    <div class="loading-bar"></div>
                    <div class="loading-bar"></div>
                </div>
                <div class="loading-wave-text" id="cyberProcessingText">
                    <i class="fas fa-microchip text-warning"></i>
                    <span>جاري معالجة وتأكيد البيانات...</span>
                </div>
                <div class="loading-wave-subtext" id="cyberProcessingSubtext">
                    يرجى الانتظار لحظات، يجري التواصل الآمن مع خوادم المنظومة
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

            function populateCompanyModalData(button) {
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

                // عرض الوظائف المتاحة بشكل مختصر جداً (المسمى الوظيفي فقط)
                const rawJobs = button.getAttribute('data-job-titles');
                let jobTitles = [];
                try {
                    if (rawJobs) jobTitles = JSON.parse(rawJobs);
                } catch (e) {
                    jobTitles = [];
                }

                const jobsWrap = document.getElementById('modalCompanyJobsListWrap');
                const jobsContainer = document.getElementById('modalCompanyJobsList');
                const jobsBadge = document.getElementById('modalJobsCountBadge');

                if (jobsWrap && jobsContainer) {
                    jobsContainer.innerHTML = '';
                    if (Array.isArray(jobTitles) && jobTitles.length > 0) {
                        jobTitles.forEach(function(title) {
                            const pill = document.createElement('span');
                            pill.className = 'company-job-title-pill';
                            pill.innerHTML = '<i class="fas fa-briefcase"></i><span>' + title + '</span>';
                            jobsContainer.appendChild(pill);
                        });
                        if (jobsBadge) {
                            jobsBadge.textContent = jobTitles.length + ' فرصة متاحة';
                        }
                        jobsWrap.style.display = 'block';
                    } else {
                        jobsWrap.style.display = 'none';
                    }
                }

                const logoImg = document.getElementById('modalCompanyLogo');
                const logoFallback = document.getElementById('modalCompanyLogoFallback');
                if (logo) {
                    logoImg.src = logo;
                    logoImg.style.display = 'block';
                    if (logoFallback) logoFallback.style.display = 'none';
                } else {
                    logoImg.style.display = 'none';
                    if (logoFallback) logoFallback.style.display = 'inline-flex';
                }

                const boothBadge = document.getElementById('modalCompanyBoothBadge');
                if (booth) {
                    document.getElementById('modalCompanyBooth').textContent = 'جناح ' + booth;
                    boothBadge.style.display = 'inline-flex';
                } else {
                    boothBadge.style.display = 'none';
                }

                const jobsBadgeEl = document.getElementById('modalCompanyJobsBadge');
                if (jobs > 0) {
                    document.getElementById('modalCompanyJobs').textContent = jobs;
                    jobsBadgeEl.style.display = 'inline-flex';
                } else {
                    jobsBadgeEl.style.display = 'none';
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
            }

            companyModal.addEventListener('show.bs.modal', function (event) {
                const rawTrigger = event.relatedTarget;
                const button = rawTrigger ? (rawTrigger.closest('[data-name]') || rawTrigger) : null;
                if (button && button.hasAttribute('data-name')) {
                    populateCompanyModalData(button);
                }
            });

            // ضمان فتح النافذة وملء البيانات فور النقر المباشر على البطاقة أو أي زر بداخلها
            document.addEventListener('click', function (e) {
                const card = e.target.closest && (e.target.closest('.cyber-cylinder-card') || e.target.closest('.company-booth-card'));
                if (card && card.hasAttribute('data-name')) {
                    populateCompanyModalData(card);
                    if (window.bootstrap && bootstrap.Modal) {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(companyModal);
                        modalInstance.show(card);
                    }
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

            // إيقاف الدوران فقط وحصرياً عند وضع المؤشر مباشرة على إحدى البطاقات
            const cylinderCards = inner.querySelectorAll('.cyber-cylinder-card');
            cylinderCards.forEach(function (card) {
                card.addEventListener('mouseenter', function () {
                    inner.classList.add('is-card-hovered');
                });
                card.addEventListener('mouseleave', function () {
                    inner.classList.remove('is-card-hovered');
                });
            });

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

        // ── Global Cyber Processing Wave Helpers (Uiverse mrpumps31232)
        window.showLoadingWave = function(text, subtext) {
            const overlay = document.getElementById('cyberProcessingOverlay');
            const textEl = document.getElementById('cyberProcessingText');
            const subtextEl = document.getElementById('cyberProcessingSubtext');
            if (text && textEl) {
                const s = textEl.querySelector('span');
                if (s) s.textContent = text;
            }
            if (subtext && subtextEl) subtextEl.textContent = subtext;
            if (overlay) overlay.classList.add('is-active');
        };

        window.hideLoadingWave = function() {
            const overlay = document.getElementById('cyberProcessingOverlay');
            if (overlay) overlay.classList.remove('is-active');
        };

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
                submitBtn.innerHTML = '<span class="loading-wave-sm me-2"><span class="loading-bar"></span><span class="loading-bar"></span><span class="loading-bar"></span><span class="loading-bar"></span></span><span>جاري إصدار بطاقة الدخول...</span>';

                if (window.showLoadingWave) {
                    window.showLoadingWave('جاري إصدار بطاقة الدخول الذكية...', 'يرجى الانتظار لحظات، يجري الربط مع النظام وإنشاء رمز QR الرقمي');
                }

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
                            if (window.showLoadingWave) {
                                window.showLoadingWave('تم تأكيد التسجيل بنجاح! 🎉', 'جاري تجهيز بطاقتك وتوجيهك فوراً...');
                            }
                            alertBox.className = 'alert alert-success d-block mb-3 fw-bold';
                            alertBox.innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + (res.data.message || 'تم تسجيلك بنجاح! جاري توجيهك لبطاقتك الرقمية...');
                            setTimeout(() => {
                                window.location.href = res.data.ticket_url;
                            }, 1000);
                        } else {
                            if (window.hideLoadingWave) window.hideLoadingWave();
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
                        if (window.hideLoadingWave) window.hideLoadingWave();
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
