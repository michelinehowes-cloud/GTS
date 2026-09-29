<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fair ? $fair->title : 'معرض التوظيف السنوي' }} — جامعة طرابلس</title>
    <meta name="description" content="{{ $fair ? $fair->title : 'معرض التوظيف السنوي' }} — جامعة طرابلس. سجّل الآن وابدأ مستقبلك المهني.">

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
            --teal:     #0EA5E9;
            --green:    #10B981;
            --white:    #FFFFFF;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Cairo', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* ══════════════════════════════════
           TOP NAV
        ══════════════════════════════════ */
        .top-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
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
            border-bottom: 1px solid rgba(245,158,11,0.2);
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
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.25));
        }
        .nav-brand-text {
            line-height: 1.2;
        }
        .nav-brand-text .main { color: white; font-weight: 700; font-size: 0.95rem; }
        .nav-brand-text .sub  { color: var(--gold); font-size: 0.72rem; }

        .nav-links { display: flex; align-items: center; gap: 0.5rem; }
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
            border: 1.5px solid rgba(255,255,255,0.3);
            color: white;
        }
        .nav-btn-outline:hover {
            border-color: var(--gold);
            color: var(--gold);
        }
        .nav-btn-gold {
            background: linear-gradient(135deg, var(--gold), #F97316);
            color: white;
            box-shadow: 0 4px 15px rgba(245,158,11,0.35);
        }
        .nav-btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(245,158,11,0.5);
            color: white;
        }

        .hero-action-buttons {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 1.5rem;
        }
        .hero-action-buttons .nav-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 10px !important;
            white-space: nowrap !important;
            padding: 11px 22px !important;
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            flex-shrink: 0 !important;
            text-align: center !important;
        }
        .hero-action-buttons .nav-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            line-height: 1;
            margin: 0 !important;
        }
        .hero-action-buttons .nav-btn span {
            display: inline-block;
            white-space: nowrap;
            line-height: 1.2;
        }
        .hero-action-buttons .nav-btn-gold {
            font-size: 1.05rem !important;
            padding: 12px 26px !important;
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
            background: var(--navy);
        }

        /* Animated background layers */
        .hero-bg-layer {
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 40%, rgba(245,158,11,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 20%, rgba(14,165,233,0.1) 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 50% 100%, rgba(16,185,129,0.08) 0%, transparent 50%),
                linear-gradient(160deg, #045db0 0%, #03488a 50%, #0d2444 100%);
            pointer-events: none;
        }

        /* Grid overlay */
        .hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            animation: orb-float 8s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(245,158,11,0.08); top: -100px; right: -100px; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: rgba(14,165,233,0.08); bottom: 0; left: -80px; animation-delay: -3s; }
        .orb-3 { width: 200px; height: 200px; background: rgba(16,185,129,0.07); top: 50%; left: 50%; animation-delay: -5s; }

        @keyframes orb-float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(245,158,11,0.7);
            pointer-events: none;
            animation: particle-rise linear infinite;
        }
        @keyframes particle-rise {
            0%   { transform: translateY(110vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.6; }
            100% { transform: translateY(-10vh) scale(1.2); opacity: 0; }
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
            background: rgba(245,158,11,0.15);
            border: 1px solid rgba(245,158,11,0.4);
            color: var(--gold);
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .hero-eyebrow .dot {
            width: 6px; height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

        .hero-title {
            font-size: clamp(2.8rem, 6vw, 5.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 50%, var(--gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            color: rgba(255,255,255,0.75);
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            margin-bottom: 2rem;
            font-weight: 400;
            max-width: 600px;
        }

        /* ══════════════════════════════════
           PREMIUM 3D COMPOSITION
        ══════════════════════════════════ */
        .premium-3d-composition {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            padding: 2rem;
        }

        .premium-glow {
            position: absolute;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(238,202,62,0.4) 0%, rgba(14,165,233,0.3) 40%, transparent 70%);
            filter: blur(40px);
            animation: pulseGlow 4s ease-in-out infinite alternate;
            z-index: 0;
        }

        .premium-glass-card {
            position: relative;
            z-index: 2;
            width: 320px;
            height: 320px;
            background: rgba(255, 255, 255, 0.05); /* True glass */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3), inset 0 0 20px rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            transform-style: preserve-3d;
            animation: float3DCard 8s ease-in-out infinite;
        }

        /* Glowing orb behind logo for visibility */
        .premium-glass-card::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0) 70%);
            filter: blur(20px);
            z-index: 1;
            transform: translateZ(10px);
        }

        .premium-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform: translateZ(40px);
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.4));
            position: relative;
            z-index: 2;
        }

        .float-element {
            position: absolute;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            z-index: 3;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .shape-1 {
            width: 60px; height: 60px;
            top: 10%; right: 10%;
            font-size: 1.5rem;
            color: var(--gold);
            animation: floatElement 6s ease-in-out infinite 1s;
        }
        .shape-2 {
            width: 70px; height: 70px;
            bottom: 10%; left: 5%;
            font-size: 1.8rem;
            color: var(--teal);
            animation: floatElement 7s ease-in-out infinite 0.5s;
        }
        .shape-3 {
            padding: 10px 20px;
            top: 50%; left: -20px;
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
            color: rgba(255,255,255,0.5);
            margin: 0;
            line-height: 1;
            transition: all 0.3s ease;
        }
        .partner-text:hover {
            color: rgba(255,255,255,1);
            transform: translateY(-2px);
        }

        @keyframes pulseGlow {
            0% { opacity: 0.5; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1.1); }
        }
        @keyframes float3DCard {
            0%, 100% { transform: rotateX(8deg) rotateY(-12deg) translateY(0); }
            50% { transform: rotateX(12deg) rotateY(-8deg) translateY(-20px); }
        }
        @keyframes floatElement {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }

        @media (max-width: 991px) {
            .hero-text-col { text-align: center !important; margin-bottom: 3rem; }
            .hero-subtitle { margin-left: auto; margin-right: auto; }
            .event-pills { justify-content: center !important; }
            .countdown-row { justify-content: center !important; }
            .premium-glass-card { width: 260px; height: 260px; }
            .shape-3 { display: none; }
        }

        /* Event info pills */
        .event-pills {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-bottom: 2.5rem;
        }
        .event-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.85);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 500;
            backdrop-filter: blur(4px);
        }
        .event-pill i { color: var(--gold); }

        /* ══ COUNTDOWN ══ */
        .countdown-row {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }
        .cd-box {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 1.1rem 1.4rem;
            min-width: 85px;
            text-align: center;
            position: relative;
            transition: transform 0.3s, border-color 0.3s;
        }
        .cd-box:hover {
            transform: translateY(-6px);
            border-color: rgba(245,158,11,0.5);
        }
        .cd-box::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(245,158,11,0.05), transparent);
            pointer-events: none;
        }
        .cd-num {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--gold);
            line-height: 1;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 0 30px rgba(245,158,11,0.4);
        }
        .cd-sep {
            display: flex;
            align-items: center;
            padding-bottom: 1.2rem;
            color: var(--gold);
            font-size: 2rem;
            font-weight: 900;
            opacity: 0.5;
            animation: blink 1s infinite;
        }
        .cd-lbl {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
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
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 1.2rem 2rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(6px);
        }
        .hero-stat { text-align: center; }
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
            color: rgba(255,255,255,0.55);
            font-size: 0.8rem;
            margin-top: 4px;
            font-weight: 500;
        }
        .hero-stat-divider {
            width: 1px;
            background: rgba(255,255,255,0.1);
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
            box-shadow: 0 8px 30px rgba(245,158,11,0.4);
            transition: all 0.3s;
        }
        .cta-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 40px rgba(245,158,11,0.6);
            color: white;
        }
        .cta-secondary {
            background: rgba(255,255,255,0.08);
            color: white;
            border: 1.5px solid rgba(255,255,255,0.25);
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
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.5);
            color: white;
            transform: translateY(-3px);
        }

        /* Registered banner */
        .registered-banner {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(16,185,129,0.15);
            border: 1.5px solid rgba(16,185,129,0.5);
            border-radius: 50px;
            padding: 12px 28px;
            color: white;
            font-weight: 600;
        }
        .registered-banner .reg-num {
            background: rgba(16,185,129,0.3);
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
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
            transition: all 0.3s;
        }
        .cta-ticket:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 8px 25px rgba(16,185,129,0.5);
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
            color: rgba(255,255,255,0.3);
            font-size: 0.7rem;
            letter-spacing: 2px;
            animation: bounce-down 2s ease-in-out infinite;
        }
        @keyframes bounce-down {
            0%,100% { transform: translateX(-50%) translateY(0); }
            50%      { transform: translateX(-50%) translateY(8px); }
        }

        /* ══════════════════════════════════
           SECTION TYPOGRAPHY (Global)
        ══════════════════════════════════ */
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.05);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            color: rgba(255,255,255,0.6);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .title-line {
            width: 60px;
            height: 4px;
            background: var(--gold);
            margin: 0 auto 1.5rem;
            border-radius: 10px;
        }

        /* ══════════════════════════════════
           BENTO BOX INFO SECTION
        ══════════════════════════════════ */
        .info-section {
            padding: 120px 0;
            background: #0b1c36;
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255,255,255,0.03);
        }
        .info-section::before {
            content: '';
            position: absolute; top: -20%; left: -10%; width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, transparent 60%);
            z-index: 0; pointer-events: none;
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
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.5);
        }

        /* Bento Grid Areas */
        .bento-reg { grid-column: span 12; }
        .bento-date { grid-column: span 12; }
        .bento-loc { grid-column: span 12; }
        .bento-desc { grid-column: span 12; }

        @media(min-width: 992px) {
            .bento-reg { grid-column: span 7; grid-row: span 2; padding: 3.5rem; justify-content: center; }
            .bento-date { grid-column: span 5; grid-row: span 1; }
            .bento-loc { grid-column: span 5; grid-row: span 1; }
        }

        .bento-icon-wrapper {
            width: 70px; height: 70px;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
            z-index: 2;
        }
        .bento-reg .bento-icon-wrapper { width: 90px; height: 90px; font-size: 2.5rem; border-radius: 28px; margin-bottom: 2rem; }

        .bento-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.8rem;
            z-index: 2; position: relative;
        }
        .bento-reg .bento-title { font-size: 2.2rem; margin-bottom: 1.2rem; }
        
        .bento-text {
            color: rgba(255,255,255,0.65);
            font-size: 1.05rem;
            line-height: 1.6;
            margin: 0;
            z-index: 2; position: relative;
        }
        .bento-reg .bento-text { font-size: 1.2rem; }

        .bento-glow {
            position: absolute; width: 150px; height: 150px; border-radius: 50%; filter: blur(60px); opacity: 0.3; z-index: 1; pointer-events: none;
        }

        /* ══════════════════════════════════
           SPONSORS SECTION (الجهات الراعية)
        ══════════════════════════════════ */
        .sponsors-section {
            padding: 90px 0;
            background: linear-gradient(180deg, #071324 0%, #0a182e 50%, #071324 100%);
            position: relative;
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sponsors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.8rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sponsor-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            padding: 2rem 1.5rem;
            text-align: center;
            backdrop-filter: blur(12px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
        }

        .sponsor-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
        }

        .sponsor-tier-badge {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.5px;
        }
        .tier-diamond {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(14, 165, 233, 0.3));
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.2);
        }
        .tier-platinum {
            background: linear-gradient(135deg, rgba(226, 232, 240, 0.2), rgba(203, 213, 225, 0.3));
            color: #f1f5f9;
            border: 1px solid rgba(226, 232, 240, 0.4);
            box-shadow: 0 0 15px rgba(226, 232, 240, 0.15);
        }
        .tier-gold {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.3));
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.4);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.2);
        }
        .tier-silver {
            background: linear-gradient(135deg, rgba(156, 163, 175, 0.2), rgba(107, 114, 128, 0.3));
            color: #d1d5db;
            border: 1px solid rgba(156, 163, 175, 0.4);
        }

        .sponsor-logo-box {
            width: 130px;
            height: 90px;
            background: #ffffff;
            border-radius: 16px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            transition: transform 0.3s;
        }
        .sponsor-card:hover .sponsor-logo-box {
            transform: scale(1.05);
        }
        .sponsor-logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .sponsor-name {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .sponsor-desc {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            flex-grow: 1;
        }
        .sponsor-link {
            color: var(--gold);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .sponsor-link:hover {
            color: #fbbf24;
            text-decoration: underline;
        }

        /* ══════════════════════════════════
           COMPANIES SECTION (Logos Grid)
        ══════════════════════════════════ */
        .companies-section {
            padding: 100px 0;
            background: #071324;
            position: relative;
        }

        .companies-logo-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .company-logo-item {
            width: 160px;
            height: 160px;
            background: #ffffff;
            border-radius: 24px;
            padding: 1.5rem;
            border: none;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .company-logo-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .company-logo-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            mix-blend-mode: multiply;
        }

        .company-logo-item .fallback-icon {
            font-size: 2.5rem;
            color: #d1d5db;
            margin-bottom: 0.5rem;
        }
        
        .company-logo-item .fallback-text {
            color: #1f2937;
            font-size: 0.85rem;
            font-weight: 700;
            text-align: center;
            line-height: 1.4;
        }

        .booth-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(37,99,235,0.4);
            border: 1px solid rgba(255,255,255,0.2);
            z-index: 2;
        }

        .jobs-badge {
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #10B981;
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(16,185,129,0.4);
            border: 1px solid rgba(255,255,255,0.2);
            white-space: nowrap;
        }

        .jobs-badge.jobs-badge-info {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.4);
        }

        .companies-dir-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #045db0 0%, #1e40af 50%, #d97706 150%);
            color: #ffffff;
            border: 1.5px solid rgba(238, 202, 62, 0.5);
            padding: 14px 34px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(4, 93, 176, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .companies-dir-btn:hover {
            color: #ffffff;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(238, 202, 62, 0.4);
            border-color: #eeca3e;
        }

        .companies-dir-btn i {
            transition: transform 0.25s ease;
        }

        .companies-dir-btn:hover i {
            transform: translateX(-4px);
        }

        /* ══════════════════════════════════
           SCIENTIFIC PROGRAM PREVIEW SECTION
        ══════════════════════════════════ */
        .program-preview-section {
            padding: 100px 0;
            background: linear-gradient(180deg, #071324 0%, #044b8e 50%, #033566 100%);
            position: relative;
            overflow: hidden;
        }

        .program-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.8rem;
            margin-bottom: 3.5rem;
        }

        .program-preview-card {
            background: rgba(8, 34, 69, 0.7);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 24px;
            padding: 1.8rem;
            backdrop-filter: blur(16px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            color: #ffffff;
            position: relative;
        }

        .program-preview-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
            background: rgba(12, 45, 92, 0.9);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(238, 202, 62, 0.25);
            color: #ffffff;
        }

        .badge-type-masterclass {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .badge-type-workshop {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .badge-type-panel {
            background: linear-gradient(135deg, #059669, #34d399);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 50px;
        }

        .event-speaker-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 1rem 0 1.2rem;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            direction: rtl;
        }

        .event-speaker-info {
            flex: 1;
            min-width: 0;
            text-align: right;
        }

        .event-speaker-avatar {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            flex-shrink: 0;
            object-fit: cover;
            margin-right: auto;
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
        .grad-info-item .lbl { font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-bottom: 3px; }
        .grad-info-item .val { font-weight: 700; color: var(--navy); font-size: 0.9rem; }

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
            box-shadow: 0 12px 28px rgba(0,0,0,0.35);
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
           FOOTER
        ══════════════════════════════════ */
        .page-footer {
            background: var(--navy);
            color: rgba(255,255,255,0.5);
            text-align: center;
            padding: 2rem;
            font-size: 0.85rem;
        }
        .page-footer a { color: var(--gold); text-decoration: none; }

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
        .empty-icon { font-size: 5rem; margin-bottom: 1.5rem; filter: drop-shadow(0 0 30px rgba(245,158,11,0.4)); }

        /* ══════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════ */
        @media (max-width: 768px) {
            .top-nav { padding: 0.75rem 1rem; }
            .nav-brand-text .main { font-size: 0.8rem; }
            .hero-stats { gap: 1.5rem; padding: 1rem; }
            .hero-stat-divider { display: none; }
            .cd-num { font-size: 2rem; }
            .cd-box { min-width: 70px; padding: 0.8rem 1rem; }
        }
    </style>
</head>
<body>

{{-- ══════════════════════════════════
     TOP NAV
══════════════════════════════════ --}}
<nav class="top-nav" id="topNav">
    <a href="{{ route('home') }}" class="nav-brand">
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
    <div class="d-none d-lg-flex align-items-center gap-2 mx-3">
        <a href="{{ route('job-fair.public.companies', $fair->id ?? 1) }}" class="text-white text-decoration-none px-3 py-1.5 rounded-pill" style="font-size: 0.85rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); transition: all 0.2s;">
            <i class="fas fa-building text-warning me-1"></i>الشركات
        </a>
        <a href="{{ route('job-fair.public.program', $fair->id ?? 1) }}" class="text-white text-decoration-none px-3 py-1.5 rounded-pill" style="font-size: 0.85rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); transition: all 0.2s;">
            <i class="fas fa-graduation-cap text-warning me-1"></i>البرنامج العلمي
        </a>
        <a href="{{ route('job-fair.public.projects', $fair->id ?? 1) }}" class="text-white text-decoration-none px-3 py-1.5 rounded-pill" style="font-size: 0.85rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); transition: all 0.2s;">
            <i class="fas fa-lightbulb text-warning me-1"></i>مشاريع التخرج
        </a>
    </div>
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
            <a href="{{ route('graduate.register') }}" class="nav-btn nav-btn-gold">
                <i class="fas fa-user-plus me-1"></i>سجّل كخريج
            </a>
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
            <div class="col-lg-6 hero-text-col text-lg-end text-center mt-5 mt-lg-0 order-2 order-lg-1">

                {{-- Eyebrow --}}
                <div class="hero-eyebrow">
                    <span class="dot"></span>
                    {{ $fair->subtitle ?: ($fair->title ?? 'فعالية معتمدة') }}
                    <span class="dot"></span>
                </div>

                {{-- Title --}}
                <h1 class="hero-title">{{ $fair->title }}</h1>

                @if($fair->subtitle)
                <p class="hero-subtitle">{{ $fair->subtitle }}</p>
                @else
                <p class="hero-subtitle">انطلق نحو مستقبلك المهني — فرصتك الذهبية تبدأ هنا</p>
                @endif

                {{-- Event Pills --}}
                <div class="event-pills justify-content-lg-start justify-content-center">
                    <span class="event-pill">
                        <i class="fas fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($fair->event_date)->locale('ar')->translatedFormat('j F Y') }}
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
                <div class="d-flex justify-content-lg-start justify-content-center">
                    <div dir="ltr" class="countdown-row justify-content-center m-0" id="countdown">
                        <div class="cd-box">
                            <div class="cd-num" id="cd-d">--</div>
                            <div class="cd-lbl">Days</div>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-box">
                            <div class="cd-num" id="cd-h">--</div>
                            <div class="cd-lbl">Hours</div>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-box">
                            <div class="cd-num" id="cd-m">--</div>
                            <div class="cd-lbl">Min</div>
                        </div>
                        <div class="cd-sep">:</div>
                        <div class="cd-box">
                            <div class="cd-num" id="cd-s">--</div>
                            <div class="cd-lbl">Sec</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="d-flex justify-content-lg-start justify-content-center mb-4">
                    <span style="background: rgba(16,185,129,0.2); border: 1.5px solid rgba(16,185,129,0.5); color: #6EE7B7; padding: 10px 28px; border-radius: 50px; font-weight: 700; font-size: 1rem">
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

            {{-- Visual Side (Premium 3D Logo) --}}
            <div class="col-lg-6 position-relative order-1 order-lg-2 mb-5 mb-lg-0">
                <div class="premium-3d-composition">
                    <div class="premium-glow"></div>
                    <div class="premium-glass-card">
                        @if($fair)
                            <img src="{{ $fair->logo_url }}" class="premium-logo-img" alt="{{ $fair->title }}" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                        @else
                            <img src="{{ asset('images/job_fair_logo.png') }}" class="premium-logo-img" alt="شعار المعرض">
                        @endif
                    </div>
                    
                    {{-- Floating Depth Elements --}}
                    <div class="float-element shape-1"><i class="fas fa-briefcase"></i></div>
                    <div class="float-element shape-2"><i class="fas fa-user-graduate"></i></div>
                    <div class="float-element shape-3">1000+ فرصة</div>
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
        {{-- Partners Strip --}}
        <div class="row pb-4">
            <div class="col-12 text-center">
                <p class="mb-4" style="color: rgba(255,255,255,0.3); font-size: 0.85rem; font-weight: 600; letter-spacing: 1px;">شركاء النجاح</p>
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-4 gap-md-5">
                    <img src="{{ asset('images/logo.jpg') }}" alt="مكتب تدريب الخريجين" class="partner-logo partner-logo-jpg" title="مكتب تدريب الخريجين">
                    
                    @if(isset($companies) && $companies->count() > 0)
                        @foreach($companies->take(5) as $fairCompany)
                            @if($fairCompany->company->logo)
                                <img src="{{ Storage::url($fairCompany->company->logo) }}" alt="{{ $fairCompany->company->name }}" class="partner-logo" title="{{ $fairCompany->company->name }}">
                            @else
                                <span class="partner-text" title="{{ $fairCompany->company->name }}">{{ $fairCompany->company->name }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SPONSORS SECTION (الجهات الراعية) --}}
@if(isset($sponsors) && $sponsors->count() > 0)
<section class="sponsors-section" id="sponsors">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-badge"><i class="fas fa-crown text-warning me-1"></i>الجهات الراعية والداعمة</div>
            <div class="title-line"></div>
            <h2 class="section-title">شركاء الرعاية والتميز</h2>
            <p class="section-subtitle">نفتخر برعاية ودعم نخبة المؤسسات الوطنية الرائدة لمسيرة تمكين وتوظيف الكفاءات الشابة</p>
        </div>

        <div class="sponsors-grid">
            @foreach($sponsors as $sponsor)
            <div class="sponsor-card">
                @php
                    $tierClass = match($sponsor->tier) {
                        'diamond' => 'tier-diamond',
                        'platinum' => 'tier-platinum',
                        'silver' => 'tier-silver',
                        default => 'tier-gold',
                    };
                    $tierIcon = match($sponsor->tier) {
                        'diamond' => '💎',
                        'platinum' => '⭐',
                        'silver' => '🥈',
                        default => '🏆',
                    };
                @endphp
                <div class="sponsor-tier-badge {{ $tierClass }}">
                    <span>{{ $tierIcon }}</span>
                    <span>{{ $sponsor->tier_label }}</span>
                </div>

                <div class="sponsor-logo-box">
                    @if($sponsor->logo_path)
                        <img src="{{ Storage::url($sponsor->logo_path) }}" alt="{{ $sponsor->name }}">
                    @else
                        <i class="fas fa-award fa-2x text-warning"></i>
                    @endif
                </div>

                <h3 class="sponsor-name">{{ $sponsor->name }}</h3>
                <p class="sponsor-desc">{{ $sponsor->description }}</p>

                @if($sponsor->website)
                    <a href="{{ $sponsor->website }}" target="_blank" rel="noopener noreferrer" class="sponsor-link">
                        <span>زيارة الموقع الإلكتروني</span>
                        <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- COMPANIES SECTION (Interactive Popup Cards) --}}
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
    // عرض معاينة لأول 12 شركة لإبقاء الصفحة الرئيسية خفيفة وسريعة التصفح
    $previewCompanies = $companies->take(12);
@endphp
<section class="companies-section" id="companies">
    <div class="container">

        <div class="text-center mb-5">
            <div class="section-badge"><i class="fas fa-building"></i>الشركات والمؤسسات المشاركة</div>
            <div class="title-line"></div>
            <h2 class="section-title">الشركات المشاركة</h2>
            <p class="section-subtitle">{{ $countText }} في المعرض — اضغط على أي شركة للتعرف على تفاصيلها وجناحها</p>
        </div>

        <div class="companies-logo-grid">
            @foreach($previewCompanies as $fc)
            @php
                $companyLogo = $fc->company->logo_path ? Storage::url($fc->company->logo_path) : ($fc->company->logo ? Storage::url($fc->company->logo) : '');
                $companyName = $fc->company->name ?? 'شركة مشاركة';
                $companyDesc = $fc->company->description ?: ('جهة رائدة مشاركة في فعاليات ' . ($fair->title ?? 'المعرض والملتقى') . ' لتوفير أفضل الفرص للخرجين.');
                $pos = (int)($fc->available_positions ?? 0);
            @endphp
            <div role="button" 
                 tabindex="0"
                 class="company-logo-item" 
                 data-bs-toggle="modal" 
                 data-bs-target="#companyDetailModal"
                 data-name="{{ $companyName }}"
                 data-logo="{{ $companyLogo }}"
                 data-industry="{{ $fc->company->industry ?? 'قطاع الأعمال والخدمات' }}"
                 data-booth="{{ $fc->booth_number ? strtoupper($fc->booth_number) : '' }}"
                 data-jobs="{{ $pos }}"
                 data-desc="{{ $companyDesc }}"
                 data-website="{{ $fc->company->website ?? '' }}"
                 data-email="{{ $fc->company->email ?? '' }}"
                 data-phone="{{ $fc->company->phone ?? '' }}"
                 title="انقر للاطلاع على تفاصيل {{ $companyName }}">
                
                @if($fc->booth_number)
                    <span class="booth-badge"><i class="fas fa-map-marker-alt me-1"></i>جناح {{ strtoupper($fc->booth_number) }}</span>
                @endif

                @if($pos > 0)
                    @php
                        $posText = ($pos == 1 ? 'فرصة واحدة' : ($pos == 2 ? 'فرصتان' : ($pos <= 10 ? $pos.' فرص' : $pos.' فرصة')));
                    @endphp
                    <span class="jobs-badge"><i class="fas fa-briefcase me-1"></i>{{ $posText }}</span>
                @else
                    <span class="jobs-badge jobs-badge-info"><i class="fas fa-handshake me-1"></i>جناح تعريفي</span>
                @endif

                @if($companyLogo)
                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}">
                @else
                    <i class="fas fa-building fallback-icon"></i>
                    <span class="fallback-text">{{ $companyName }}</span>
                @endif
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

{{-- SCIENTIFIC PROGRAM PREVIEW SECTION (رحلة الجاهزية المهنية) --}}
@if(isset($events) && $events->count() > 0)
@php
    $previewEvents = $events->take(4);
@endphp
<section class="program-preview-section" id="program">
    <div class="container">

        <div class="text-center mb-5">
            <div class="section-badge">
                <i class="fas fa-graduation-cap me-1"></i>رحلة الجاهزية المهنية
            </div>
            <div class="title-line"></div>
            <h2 class="section-title">البرنامج العلمي والتدريبي المصاحب</h2>
            <p class="section-subtitle">
                سلسلة متكاملة من ورش العمل التطبيقية، الماستر كلاس، والجلسات الحوارية الاستراتيجية بمشاركة نخبة من الخبراء والمدربين المحليين والدوليين لتأهيلك لسوق العمل.
            </p>
        </div>

        @php
            $isProgramComingSoon = !($fair && $fair->is_program_published);
        @endphp

        @if($isProgramComingSoon)
            <div class="text-center p-5 rounded-4" style="background: rgba(255,255,255,0.04); border: 1px dashed rgba(238, 202, 62, 0.35); backdrop-filter: blur(10px); max-width: 820px; margin: 0 auto 2.5rem;">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(238, 202, 62, 0.15); border: 1px solid rgba(238, 202, 62, 0.3); color: var(--gold); font-size: 0.88rem; font-weight: 700;">
                    <i class="fas fa-clock"></i>
                    <span>ترقبوا الإطلاق الرسمي قريباً</span>
                    <span class="badge bg-warning text-dark rounded-pill ms-1 font-monospace">Coming Soon</span>
                </div>
                <h3 class="fw-bold text-white mb-3" style="font-size: 1.75rem;">
                    البرنامج العلمي والتدريبي قيد التحضير النهائي
                </h3>
                <p class="text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 0.98rem; line-height: 1.7;">
                    نضع حالياً اللمسات الأخيرة على جدول ورش العمل التطبيقية وجلسات الماستر كلاس مع نخبة من كبار المدربين والخبراء. سيتم فتح باب التسجيل وتأكيد الحضور قريباً لجميع المسجلين.
                </p>
                <a href="{{ route('job-fair.public.program', $fair->id) }}" class="companies-dir-btn d-inline-flex align-items-center">
                    <span>استعراض محاور البرنامج والتفاصيل</span>
                    <i class="fas fa-arrow-left ms-2"></i>
                </a>
            </div>
        @else
        <div class="program-preview-grid">
            @foreach($previewEvents as $event)
            @php
                $typeClass = match($event->type) {
                    'masterclass' => 'badge-type-masterclass',
                    'panel_discussion' => 'badge-type-panel',
                    default => 'badge-type-workshop',
                };
            @endphp
            <div class="program-preview-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="{{ $typeClass }}">
                            <i class="{{ $event->type_icon }} me-1"></i>{{ $event->type_short_label }}
                        </span>
                        <span class="jobs-tag tag-available" style="font-size:0.72rem;">
                            <i class="fas fa-check-circle me-1"></i>{{ $event->status_label }}
                        </span>
                    </div>

                    <h4 class="fw-bold mb-2 text-white" style="font-size: 1.15rem; line-height: 1.4;">
                        {{ $event->title }}
                    </h4>

                    @if($event->speaker_name)
                    <div class="event-speaker-strip">
                        <div class="event-speaker-info text-truncate">
                            <div class="fw-bold text-white" style="font-size: 0.95rem;">{{ $event->speaker_name }}</div>
                            <small class="text-white-50 d-block text-truncate" style="font-size: 0.72rem;">{{ $event->speaker_title ?: 'متحدث وخبير معتمد' }}</small>
                        </div>
                        @if($event->speaker_image_url)
                            <img src="{{ $event->speaker_image_url }}" alt="{{ $event->speaker_name }}" class="event-speaker-avatar">
                        @else
                            <div class="event-speaker-avatar">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        @endif
                    </div>
                    @endif

                    <p class="text-white-50 mb-3" style="font-size: 0.82rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $event->description }}
                    </p>
                </div>

                <div>
                    <div class="d-flex align-items-center justify-content-between text-white-50 small mb-3 pt-2" style="font-size:0.76rem; border-top:1px solid rgba(255,255,255,0.08);">
                        <span><i class="far fa-clock text-warning me-1"></i>{{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</span>
                        <span><i class="fas fa-map-marker-alt text-info me-1"></i>{{ $event->location ?: 'المدرج الرئيسي' }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('job-fair.public.events.show', $event->id) }}" class="btn-view-company w-100 text-center py-2" style="font-size:0.85rem;">
                            <i class="fas fa-qrcode me-1"></i>تفاصيل الفعالية والتسجيل
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
                <i class="fas fa-calendar-alt me-1 text-warning"></i> مواعيد ورش العمل، الماستر كلاس، والجلسات الحوارية، وتأكيد الحضور
            </div>
        </div>
        @endif

    </div>
</section>
@endif

{{-- GRADUATE PROJECTS PREVIEW SECTION (معرض وأرشيف مشاريع التخرج) --}}
@if(isset($projects) && $projects->count() > 0)
@php
    $previewProjects = $projects->take(3);
    $isProjectsComingSoon = !($fair && $fair->is_projects_published);
@endphp
<section class="projects-preview-section" id="projects" style="padding: 5rem 0; position: relative; background: radial-gradient(circle at 80% 20%, rgba(238,202,62,0.06) 0%, transparent 50%), linear-gradient(180deg, rgba(6,30,62,0.95) 0%, rgba(4,22,46,0.98) 100%); border-top: 1px solid rgba(255,255,255,0.06);">
    <div class="container position-relative z-10">
        <div class="text-center mb-5">
            <div class="section-badge" style="background: rgba(238,202,62,0.15); border: 1px solid rgba(238,202,62,0.4); color: #fbd34d; padding: 6px 18px; border-radius: 999px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-lightbulb"></i>ابتكارات كفاءات المستقبل
            </div>
            <div class="title-line"></div>
            <h2 class="section-title text-white fw-bold" style="font-size: 2.2rem; margin-top: 0.8rem;">معرض مشاريع التخرج المتميزة</h2>
            <p class="section-subtitle text-white-50 mx-auto" style="max-width: 680px; font-size: 1rem; line-height: 1.7;">
                نافذة حية تبرز نتاج إبداعات خريجي جامعة طرابلس بمختلف الكليات والأقسام، وفرصة مباشرة لأصحاب الأعمال والشركات لتبني العقول الواعدة واستثمار الأفكار الابتكارية.
            </p>
        </div>

        @if($isProjectsComingSoon)
        <div class="text-center p-5 rounded-4 mb-4" style="background: rgba(255,255,255,0.04); border: 1px dashed rgba(238, 202, 62, 0.35); backdrop-filter: blur(10px); max-width: 820px; margin: 0 auto 2.5rem;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(238, 202, 62, 0.15); border: 1px solid rgba(238, 202, 62, 0.3); color: var(--gold); font-size: 0.88rem; font-weight: 700;">
                <i class="fas fa-clock"></i>
                <span>ترقبوا الإطلاق الرسمي قريباً</span>
                <span class="badge bg-warning text-dark rounded-pill ms-1 font-monospace">Coming Soon</span>
            </div>
            <h3 class="fw-bold text-white mb-3" style="font-size: 1.75rem;">
                معرض وأرشيف مشاريع التخرج قيد التحضير والتنسيق
            </h3>
            <p class="text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 0.98rem; line-height: 1.7;">
                نستكمل حالياً استقبال وفهرسة نخبة مشاريع وبحوث تخرج طلبة كليات جامعة طرابلس، وتجهيز أجنحة العرض التفاعلية ورموز الـ QR Code الخاصة بكل ابتكار. ترقبوا التدشين الرسمي قريباً!
            </p>
            <a href="{{ route('job-fair.public.projects', $fair->id) }}" class="companies-dir-btn d-inline-flex align-items-center">
                <span>استعراض محاور ومسارات المشاريع</span>
                <i class="fas fa-arrow-left ms-2"></i>
            </a>
        </div>
        @else
        <div class="row g-4 mb-5">
            @foreach($previewProjects as $project)
            <div class="col-lg-4 col-md-6">
                <div class="h-100 p-4 rounded-4 d-flex flex-column justify-content-between" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px); position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; right: 0; left: 0; height: 3px; background: linear-gradient(90deg, #eeca3e, #00d2ff);"></div>
                    
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge px-3 py-2 rounded-pill" style="background: rgba(4,93,176,0.4); border: 1px solid rgba(0,210,255,0.3); color: #7dd3fc; font-size: 0.78rem;">
                                <i class="{{ $project->faculty_icon }} me-1"></i>{{ $project->faculty }}
                            </span>
                            @if($project->booth_number)
                                <span class="badge px-2.5 py-1.5 rounded-pill" style="background: rgba(238,202,62,0.15); border: 1px solid rgba(238,202,62,0.3); color: #fef08a; font-size: 0.75rem;">
                                    <i class="fas fa-map-pin me-1"></i>جناح {{ $project->booth_number }}
                                </span>
                            @endif
                        </div>

                        <h4 class="text-white fw-bold mb-2" style="font-size: 1.18rem; line-height: 1.45;">
                            <a href="{{ route('job-fair.public.projects.show', $project->id) }}" class="text-white text-decoration-none" style="transition: color 0.2s;">
                                {{ $project->title }}
                            </a>
                        </h4>

                        <div class="text-info small mb-3" style="font-size: 0.82rem;">
                            <i class="fas fa-code-branch me-1"></i>{{ $project->department }} &bull; سنة التخرج {{ $project->graduation_year }}
                        </div>

                        <p class="text-white-50 mb-3" style="font-size: 0.85rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $project->summary }}
                        </p>

                        <div class="p-2.5 rounded-3 mb-3" style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.05); font-size: 0.8rem;">
                            <div class="d-flex align-items-center justify-content-between text-white-50 mb-1">
                                <span><i class="fas fa-users text-warning me-1"></i>فريق العمل:</span>
                                <span class="text-white fw-semibold">{{ count($project->team_list) }} أعضاء</span>
                            </div>
                            <div class="text-truncate text-white-50" style="font-size: 0.76rem;">
                                {{ implode('، ', array_column($project->team_list, 'name')) }}
                            </div>
                            @if($project->supervisor_name)
                            <div class="mt-1.5 pt-1.5 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between text-white-50" style="font-size: 0.76rem;">
                                <span><i class="fas fa-chalkboard-teacher text-info me-1"></i>إشراف:</span>
                                <span class="text-light">{{ $project->supervisor_title }} {{ $project->supervisor_name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('job-fair.public.projects.show', $project->id) }}" class="btn-view-company w-100 text-center py-2.5 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.88rem; font-weight: 700; text-decoration: none;">
                            <i class="fas fa-qrcode"></i>
                            <span>تفاصيل المشروع ورمز QR</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- زر استعراض الأرشيف الكامل لمشاريع التخرج --}}
        <div class="text-center">
            <a href="{{ route('job-fair.public.projects', $fair->id) }}" class="companies-dir-btn" style="display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #045db0, #092347); border: 1px solid rgba(238,202,62,0.4); color: white; padding: 12px 32px; border-radius: 999px; text-decoration: none; font-weight: 700; font-size: 1rem; box-shadow: 0 8px 25px rgba(4,93,176,0.3); transition: all 0.3s ease;">
                <span>استعراض الأرشيف الكامل لمشاريع التخرج ({{ $projects->count() }} مشروع)</span>
                <i class="fas fa-arrow-left ms-2 text-warning"></i>
            </a>
            <div class="mt-2 text-white-50" style="font-size: 0.88rem;">
                <i class="fas fa-search me-1 text-warning"></i> تصفح وفلترة حسب الكلية، التخصص، وسنة التخرج، وتحميل الملصقات والملفات
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
                    <div class="alert border-0 rounded-3 mb-3" style="background:#EFF6FF; color:#1E40AF; font-size:0.88rem">
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
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">إلغاء</button>
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
<div class="modal fade" id="companyDetailModal" tabindex="-1" aria-labelledby="companyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered company-modal-dialog">
        <div class="modal-content company-modal-content">
            <div class="company-modal-header text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <div class="company-modal-logo-wrapper" id="modalCompanyLogoBox">
                    <img id="modalCompanyLogo" src="" alt="شعار الشركة" style="display:none;">
                    <i id="modalCompanyLogoFallback" class="fas fa-building fa-2x text-primary" style="display:none;"></i>
                </div>

                <h3 class="fw-bold mb-2 text-white" id="modalCompanyName">اسم الشركة</h3>
                
                <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap mb-1">
                    <span class="company-modal-badge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-tag me-1"></i>
                        <span id="modalCompanyIndustry">قطاع الأعمال</span>
                    </span>
                    <span class="company-modal-badge" id="modalCompanyBoothBadge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); display:none;">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <span id="modalCompanyBooth">جناح 1</span>
                    </span>
                    <span class="company-modal-badge" id="modalCompanyJobsBadge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); display:none;">
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
                    <p class="lh-lg mb-0" id="modalCompanyDesc" style="font-size: 0.95rem; color: #cbd5e1; white-space: pre-line;">
                        وصف الشركة
                    </p>
                </div>

                <div class="d-flex flex-column gap-2 mb-4" id="modalCompanyContacts">
                    <div class="company-info-chip" id="modalWebsiteChip" style="display:none;">
                        <div class="icon bg-primary bg-opacity-25 text-primary">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="text-white-50 d-block" style="font-size:0.72rem">الموقع الإلكتروني الرسمي</small>
                            <a href="#" target="_blank" id="modalCompanyWebsite" class="text-white fw-semibold text-decoration-none" style="font-size:0.9rem"></a>
                        </div>
                    </div>

                    <div class="company-info-chip" id="modalEmailChip" style="display:none;">
                        <div class="icon bg-success bg-opacity-25 text-success">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="text-white-50 d-block" style="font-size:0.72rem">البريد الإلكتروني للتوظيف / التواصل</small>
                            <a href="#" id="modalCompanyEmail" class="text-white fw-semibold text-decoration-none" style="font-size:0.9rem"></a>
                        </div>
                    </div>

                    <div class="company-info-chip" id="modalPhoneChip" style="display:none;">
                        <div class="icon bg-warning bg-opacity-25 text-warning">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="text-truncate">
                            <small class="text-white-50 d-block" style="font-size:0.72rem">هاتف الشركة</small>
                            <span id="modalCompanyPhone" class="text-white fw-semibold" style="font-size:0.9rem"></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    @auth
                        @if(auth()->user()->role === 'graduate')
                            <a href="{{ route('graduate.job-opportunities.index') }}" class="cta-primary py-2 px-4 text-decoration-none">
                                <i class="fas fa-briefcase"></i>تصفح الفرص المتاحة
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
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

{{-- FOOTER --}}
<footer class="page-footer">
    مكتب تدريب وتوظيف الخريجين — <a href="{{ route('home') }}">جامعة طرابلس</a>
    &nbsp;|&nbsp;
    {{ $fair ? $fair->title : 'المعارض والفعاليات' }}
</footer>

{{-- ══════════════════════════════════
     SCRIPTS
══════════════════════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Scroll nav style
window.addEventListener('scroll', function() {
    document.getElementById('topNav').classList.toggle('scrolled', window.scrollY > 50);
});

// ── Floating particles
(function() {
    const c = document.getElementById('particles-container');
    if (!c) return;
    c.style.cssText = 'position:absolute;inset:0;pointer-events:none;overflow:hidden;';
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = 2 + Math.random() * 4;
        p.style.cssText = `
            left:${Math.random()*100}%;
            width:${size}px; height:${size}px;
            animation-duration:${10 + Math.random() * 15}s;
            animation-delay:${-Math.random() * 20}s;
            opacity:${0.3 + Math.random() * 0.5};
        `;
        c.appendChild(p);
    }
})();

@if($fair && $fair->is_upcoming)
// ── Countdown
(function() {
    const target = new Date("{{ $fair->event_date->format('Y-m-d') }}T{{ $fair->start_time ? substr($fair->start_time, 0, 5) : '09:00' }}:00");

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

    function tick() {
        const diff = target - new Date();
        if (diff <= 0) {
            document.getElementById('countdown')?.remove();
            return;
        }
        document.getElementById('cd-d').textContent = pad(Math.floor(diff / 864e5));
        document.getElementById('cd-h').textContent = pad(Math.floor(diff % 864e5 / 36e5));
        document.getElementById('cd-m').textContent = pad(Math.floor(diff % 36e5 / 6e4));
        document.getElementById('cd-s').textContent = pad(Math.floor(diff % 6e4 / 1e3));
    }

    tick();
    setInterval(tick, 1000);
})();
@endif

// ── Company Detail Modal Population
(function() {
    const companyModal = document.getElementById('companyDetailModal');
    if (!companyModal) return;

    companyModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (!button) return;

        const name = button.getAttribute('data-name') || 'شركة مشاركة';
        const logo = button.getAttribute('data-logo') || '';
        const industry = button.getAttribute('data-industry') || 'قطاع الأعمال والخدمات';
        const booth = button.getAttribute('data-booth') || '';
        const jobs = parseInt(button.getAttribute('data-jobs') || '0');
        const desc = button.getAttribute('data-desc') || '';
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
})();
</script>
</body>
</html>
