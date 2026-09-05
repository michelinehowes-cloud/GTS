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
            /* If the logo is white, invert it so it shows on white background */
            filter: invert(1) brightness(0.5);
            border-radius: 0;
            margin-right: 15px;
            object-fit: contain;
        }
        .nav-brand-text {
            line-height: 1.2;
        }
        .nav-brand-text .main { color: white; font-weight: 700; font-size: 0.95rem; }
        .nav-brand-text .sub  { color: var(--gold); font-size: 0.72rem; }

        .nav-links { display: flex; align-items: center; gap: 0.5rem; }
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
        <img src="{{ asset('images/job_fair_logo_horizontal.png') }}" class="jf-logo" alt="شعار المعرض" onerror="this.style.display='none'">
    </a>

    <div class="nav-links">
        <a href="{{ route('job-fair.live-stream') }}" class="nav-btn" style="background: rgba(239, 68, 68, 0.2); border: 1.5px solid #ef4444; color: #ffffff;">
            <i class="fas fa-broadcast-tower text-danger me-1"></i>البث المباشر 🔴
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="nav-btn nav-btn-outline">
                <i class="fas fa-th-large me-1"></i>لوحة التحكم
            </a>
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'partnership_officer')
            <a href="{{ route('job-fair.admin.index') }}" class="nav-btn nav-btn-gold">
                <i class="fas fa-cog me-1"></i>إدارة المعرض
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
                @php
                    $liveSetting = \App\Models\LiveBroadcastSetting::current();
                @endphp
                @if($liveSetting && $liveSetting->is_live_now)
                    <div class="mb-3">
                        <a href="{{ route('job-fair.live-stream') }}" class="btn rounded-pill px-4 py-2 text-white fw-bold shadow-lg d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #dc2626, #ef4444); border: 2px solid rgba(255,255,255,0.4); box-shadow: 0 0 20px rgba(239,68,68,0.6);">
                            <span style="width: 10px; height: 10px; background: white; border-radius: 50%; display: inline-block;"></span>
                            <span>بث حي ومباشر الآن — انقر للمشاهدة &larr;</span>
                        </a>
                    </div>
                @endif

                {{-- Eyebrow --}}
                <div class="hero-eyebrow">
                    <span class="dot"></span>
                    معرض التوظيف السنوي
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
                        المعرض يجري الآن
                    </span>
                </div>
                @endif

                {{-- CTA Buttons --}}
                <div class="mt-4 d-flex justify-content-lg-start justify-content-center gap-3">
                    @auth
                        @if(isset($myRegistration) && $myRegistration)
                            <a href="{{ route('job-fair.my-ticket', $myRegistration->id) }}" class="nav-btn nav-btn-gold px-4 py-2" style="font-size: 1.1rem">
                                <i class="fas fa-qrcode me-2"></i>عرض بطاقتي الرقمية
                            </a>
                        @elseif($fair->can_register)
                            <button class="nav-btn nav-btn-gold px-4 py-2" data-bs-toggle="modal" data-bs-target="#registerModal" style="font-size: 1.1rem">
                                <i class="fas fa-user-plus me-2"></i>سجّل الآن كخريج
                            </button>
                        @endif
                    @else
                        @if($fair->can_register)
                            <a href="{{ route('login') }}" class="nav-btn nav-btn-gold px-4 py-2" style="font-size: 1.1rem">
                                <i class="fas fa-user-plus me-2"></i>سجّل الآن كخريج
                            </a>
                        @endif
                    @endauth
                    
                    <a href="#about" class="nav-btn nav-btn-outline px-4 py-2" style="font-size: 1.1rem">
                        <i class="fas fa-info-circle me-2"></i>اعرف أكثر
                    </a>
                </div>
            </div>

            {{-- Visual Side (Premium 3D Logo) --}}
            <div class="col-lg-6 position-relative order-1 order-lg-2 mb-5 mb-lg-0">
                <div class="premium-3d-composition">
                    <div class="premium-glow"></div>
                    <div class="premium-glass-card">
                        <img src="{{ asset('images/job_fair_logo.png') }}" class="premium-logo-img" alt="شعار المعرض">
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
                        <div class="hero-stat-lbl">حضر المعرض</div>
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

{{-- INFO SECTION (Bento Box) --}}
<section class="info-section" id="info">
    <div class="container">

        <div class="text-center mb-5">
            <div class="section-badge"><i class="fas fa-info-circle"></i>عن المعرض</div>
            <div class="title-line"></div>
            <h2 class="section-title">كل ما تحتاج معرفته</h2>
            <p class="section-subtitle">تفاصيل شاملة عن {{ $fair->title }}</p>
        </div>

        <div class="bento-grid">
            
            <!-- Registration Block (Large) -->
            <div class="bento-item bento-reg">
                <div class="bento-glow" style="bottom: -20px; left: -20px; background: #10B981;"></div>
                <div class="bento-icon-wrapper" style="color: #34D399; border-color: rgba(52, 211, 153, 0.2);">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="bento-title">حالة التسجيل</h3>
                <div class="bento-text">
                    @if($fair->can_register)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34D399; padding: 8px 16px; font-size: 1rem; border: 1px solid rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i> التسجيل متاح الآن
                            </span>
                        </div>
                        @if($fair->registration_deadline)
                            <p class="mb-2"><i class="far fa-clock me-2 opacity-75"></i> ينتهي التسجيل في: <strong class="text-white">{{ \Carbon\Carbon::parse($fair->registration_deadline)->format('d/m/Y') }}</strong></p>
                        @endif
                        @if($fair->max_graduates)
                            <p class="mb-0"><i class="fas fa-users me-2 opacity-75"></i> المقاعد المحجوزة: <strong class="text-white">{{ $stats['total_registered'] ?? 0 }} من {{ $fair->max_graduates }}</strong></p>
                        @endif
                    @else
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #F87171; padding: 8px 16px; font-size: 1rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                                <i class="fas fa-times-circle me-1"></i> التسجيل مغلق
                            </span>
                        </div>
                        <p class="mb-0">نعتذر، تم إغلاق باب التسجيل في المعرض حالياً.</p>
                    @endif
                </div>
            </div>

            <!-- Date & Time Block -->
            <div class="bento-item bento-date">
                <div class="bento-glow" style="top: -20px; right: -20px; background: #3B82F6;"></div>
                <div class="bento-icon-wrapper" style="color: #60A5FA; border-color: rgba(96, 165, 250, 0.2);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4 class="bento-title">التاريخ والوقت</h4>
                <div class="bento-text">
                    <p class="mb-1 text-white fw-bold">{{ \Carbon\Carbon::parse($fair->event_date)->locale('ar')->translatedFormat('l، j F Y') }}</p>
                    @if($fair->start_time)
                        <p class="mb-0 opacity-75">
                            من {{ \Str::substr($fair->start_time,0,5) }} 
                            @if($fair->end_time) إلى {{ \Str::substr($fair->end_time,0,5) }} @endif
                        </p>
                    @endif
                </div>
            </div>

            <!-- Location Block -->
            <div class="bento-item bento-loc">
                <div class="bento-glow" style="bottom: -20px; right: -20px; background: #F59E0B;"></div>
                <div class="bento-icon-wrapper" style="color: var(--gold); border-color: rgba(245, 158, 11, 0.2);">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4 class="bento-title">الموقع</h4>
                <div class="bento-text">
                    <p class="mb-0 text-white fw-bold">{{ $fair->location }}</p>
                    <p class="mb-0 opacity-75 mt-2"><i class="fas fa-location-arrow me-1"></i> طرابلس، ليبيا</p>
                </div>
            </div>

            <!-- Description Block -->
            @if($fair->description)
            <div class="bento-item bento-desc">
                <h4 class="bento-title" style="color: #38BDF8;"><i class="fas fa-align-right me-2"></i>تفاصيل الحدث</h4>
                <p class="bento-text mt-2">{{ $fair->description }}</p>
            </div>
            @endif

        </div>
    </div>
</section>

{{-- COMPANIES SECTION (Logos Only) --}}
@if($companies->count() > 0)
<section class="companies-section" id="companies">
    <div class="container">

        <div class="text-center mb-5">
            <div class="section-badge"><i class="fas fa-building"></i>الشركاء</div>
            <div class="title-line"></div>
            <h2 class="section-title">الشركات المشاركة</h2>
            <p class="section-subtitle">{{ $companies->count() }} شركة رائدة ستتواجد في المعرض</p>
        </div>

        <div class="companies-logo-grid">
            @foreach($companies as $fc)
            <a href="javascript:void(0)" class="company-logo-item" title="{{ $fc->company->name ?? 'شركة' }}">
                
                @if($fc->booth_number)
                    <span class="booth-badge"><i class="fas fa-map-marker-alt me-1"></i>{{ $fc->booth_number }}</span>
                @endif

                @if($fc->available_positions)
                    <span class="jobs-badge">{{ $fc->available_positions }} فرصة</span>
                @endif

                @if($fc->company->logo_path)
                    <img src="{{ Storage::url($fc->company->logo_path) }}" alt="{{ $fc->company->name }}">
                @else
                    <i class="fas fa-building fallback-icon"></i>
                    <span class="fallback-text">{{ $fc->company->name ?? 'شركة' }}</span>
                @endif
                
            </a>
            @endforeach
        </div>

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

@else

{{-- NO FAIR STATE --}}
<div class="empty-hero">
    <div class="empty-icon">🎪</div>
    <h2 style="font-size:2rem; font-weight:800; margin-bottom:0.8rem; color:white">
        لا يوجد معرض منشور حالياً
    </h2>
    <p style="color:rgba(255,255,255,0.55); max-width:400px; margin-bottom:2rem; line-height:1.7">
        تابع الإعلانات للاطلاع على موعد معرض التوظيف القادم
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
    {{ $fair ? $fair->title : 'معرض التوظيف السنوي' }}
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
</script>
</body>
</html>
