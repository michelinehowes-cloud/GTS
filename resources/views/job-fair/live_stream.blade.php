<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>البث المباشر — {{ $fair ? $fair->title : 'معرض التوظيف والتدريب 2026' }} | جامعة طرابلس</title>

    <!-- Google Fonts: Cairo & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-main: #040914;
            --bg-card: #0a1529;
            --bg-card-hover: #0f1f3d;
            --bg-glass: rgba(10, 21, 41, 0.75);
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(59, 130, 246, 0.3);
            --primary: #2563eb;
            --primary-glow: rgba(37, 99, 235, 0.4);
            --accent: #38bdf8;
            --live-red: #ef4444;
            --live-glow: rgba(239, 68, 68, 0.5);
            --gold: #f59e0b;
            --gold-glow: rgba(245, 158, 11, 0.3);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(37, 99, 235, 0.15), transparent),
                radial-gradient(ellipse 60% 40% at 90% 80%, rgba(239, 68, 68, 0.08), transparent);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #060e1d;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e3a8a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        /* Top Navbar */
        .live-navbar {
            background: rgba(4, 9, 20, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-glass);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.75rem 1.5rem;
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: white;
        }
        .brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .brand-title {
            font-size: 1.05rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }
        .brand-subtitle {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 600;
        }

        /* Live Indicator Badge */
        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            animation: pulseGlow 2s infinite ease-in-out;
        }
        .live-dot {
            width: 9px;
            height: 9px;
            background-color: var(--live-red);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px var(--live-red);
            animation: blink 1.2s infinite ease-in-out;
        }

        .offline-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.4);
            color: #fde68a;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .viewers-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Main Theater Layout */
        .theater-container {
            max-width: 1720px;
            margin: 0 auto;
            padding: 1.25rem 1.5rem 3rem 1.5rem;
            flex: 1;
            width: 100%;
        }

        /* Video Stage */
        .video-stage-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.6);
            position: relative;
        }

        .player-viewport {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #020611;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Ambient Glow Behind Screen */
        .ambient-glow {
            position: absolute;
            inset: -20px;
            background: radial-gradient(circle at center, rgba(37, 99, 235, 0.25) 0%, transparent 70%);
            filter: blur(40px);
            z-index: 0;
            pointer-events: none;
        }

        .player-inner {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Watermark */
        .player-watermark {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(8px);
            padding: 0.4rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }
        .player-watermark img {
            width: 22px;
            height: 22px;
            border-radius: 4px;
        }
        .player-watermark span {
            font-size: 0.78rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.3px;
        }

        /* Stream Channel Selector */
        .stream-channels-bar {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .channel-btn {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #cbd5e1;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .channel-btn:hover, .channel-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 0 12px var(--primary-glow);
        }

        /* Simulated Stream Canvas & Graphics */
        .stream-graphics {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #050e21 0%, #020712 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .stream-pulse-ring {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.3) 0%, rgba(37, 99, 235, 0) 70%);
            border: 2px solid rgba(56, 189, 248, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 35px rgba(37, 99, 235, 0.4);
            animation: pulseRing 3s infinite ease-in-out;
            cursor: pointer;
            transition: transform 0.25s ease;
        }
        .stream-pulse-ring:hover {
            transform: scale(1.08);
        }
        .stream-pulse-ring i {
            font-size: 2.75rem;
            color: #ffffff;
            text-shadow: 0 0 15px rgba(255,255,255,0.6);
            margin-left: -3px; /* visual center for play icon */
        }

        /* Sound Equalizer Waves */
        .sound-wave-container {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 24px;
            margin-top: 1rem;
        }
        .sound-bar {
            width: 4px;
            background: linear-gradient(to top, var(--primary), var(--accent));
            border-radius: 2px;
            animation: soundBounce 1.2s infinite ease-in-out;
        }
        .sound-bar:nth-child(2) { animation-delay: 0.15s; }
        .sound-bar:nth-child(3) { animation-delay: 0.3s; }
        .sound-bar:nth-child(4) { animation-delay: 0.45s; }
        .sound-bar:nth-child(5) { animation-delay: 0.6s; }
        .sound-bar:nth-child(6) { animation-delay: 0.25s; }
        .sound-bar:nth-child(7) { animation-delay: 0.4s; }

        /* Custom Player Bottom Controls */
        .player-controls-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, transparent 100%);
            padding: 1.5rem 1.25rem 0.85rem 1.25rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            opacity: 0.92;
            transition: opacity 0.2s ease;
        }
        .player-viewport:hover .player-controls-bar {
            opacity: 1;
        }
        .ctrl-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .ctrl-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.15rem;
            cursor: pointer;
            padding: 0.4rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ctrl-btn:hover {
            color: var(--accent);
            transform: scale(1.1);
        }

        /* Stream Meta Info Bar below Video */
        .stream-info-bar {
            padding: 1.25rem 1.5rem;
            background: rgba(8, 17, 34, 0.95);
            border-top: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .stream-title-text {
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.25rem;
        }
        .stream-speaker-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Floating Reactions Bar */
        .reactions-cluster {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
        }
        .reaction-trigger-btn {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            transition: transform 0.15s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 0.15rem 0.3rem;
            border-radius: 50%;
        }
        .reaction-trigger-btn:hover {
            transform: scale(1.35);
        }
        .reaction-trigger-btn:active {
            transform: scale(0.95);
        }

        /* Side Interaction Deck (Chat / Schedule / Jobs) */
        .deck-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 650px;
            max-height: calc(100vh - 120px);
            box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.6);
            overflow: hidden;
        }

        /* Deck Tabs */
        .deck-nav-tabs {
            background: rgba(4, 9, 20, 0.6);
            border-bottom: 1px solid var(--border-glass);
            display: flex;
            padding: 0.5rem 0.75rem;
            gap: 0.4rem;
        }
        .deck-tab-btn {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.55rem 0.5rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            cursor: pointer;
        }
        .deck-tab-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.04);
        }
        .deck-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        /* Chat Body */
        .chat-container {
            display: flex;
            flex-direction: column;
            flex: 1;
            height: 100%;
            overflow: hidden;
        }
        .chat-messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .chat-msg {
            display: flex;
            gap: 0.65rem;
            align-items: flex-start;
            animation: fadeInUp 0.3s ease;
        }
        .chat-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1e3a8a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .chat-bubble {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            border-top-right-radius: 4px;
            padding: 0.5rem 0.85rem;
            max-width: 88%;
        }
        .chat-author {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.2rem;
        }
        .badge-mod {
            background: var(--live-red);
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-weight: 800;
        }
        .badge-grad {
            background: #0284c7;
            color: white;
            font-size: 0.65rem;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
        }
        .chat-text {
            font-size: 0.84rem;
            color: #e2e8f0;
            line-height: 1.4;
            word-break: break-word;
        }

        /* Pinned Q&A */
        .pinned-qa-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            padding: 0.65rem 0.85rem;
            border-radius: 12px;
            margin: 0.75rem 1rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.78rem;
            color: #fde68a;
        }

        /* Chat Input */
        .chat-input-wrapper {
            padding: 0.85rem 1rem;
            background: rgba(4, 9, 20, 0.8);
            border-top: 1px solid var(--border-glass);
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .chat-input-field {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            color: #ffffff;
            font-size: 0.85rem;
            padding: 0.6rem 0.9rem;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .chat-input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 10px var(--primary-glow);
        }
        .chat-send-btn {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .chat-send-btn:hover {
            background: #1d4ed8;
            transform: scale(1.05);
        }

        /* Agenda / Timeline tab */
        .timeline-container {
            padding: 1.25rem 1rem;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .timeline-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            position: relative;
            transition: all 0.2s ease;
        }
        .timeline-card.active-session {
            background: rgba(37, 99, 235, 0.1);
            border-color: var(--primary);
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
        }
        .timeline-time {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.35rem;
        }
        .timeline-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.35rem;
        }
        .timeline-speaker {
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Jobs Drop tab */
        .jobs-container {
            padding: 1.25rem 1rem;
            overflow-y: auto;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .job-drop-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-glass);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            transition: all 0.2s ease;
        }
        .job-drop-card:hover {
            border-color: var(--accent);
            background: rgba(56, 189, 248, 0.05);
            transform: translateY(-2px);
        }

        /* Floating Flying Emojis */
        .floating-emoji {
            position: fixed;
            bottom: 80px;
            font-size: 2.2rem;
            pointer-events: none;
            z-index: 9999;
            animation: flyUp 2.8s forwards ease-out;
        }

        /* Poll Widget Box */
        .poll-card {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            margin-top: 1.5rem;
        }
        .poll-option-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-glass);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            width: 100%;
            text-align: right;
            color: white;
            font-weight: 600;
            font-size: 0.88rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .poll-option-btn:hover {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
        }
        .poll-progress-fill {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.2);
            z-index: 0;
            transition: width 0.6s ease;
        }

        /* In-person Ticket Callout */
        .ticket-callout-banner {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(245, 158, 11, 0.15) 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* Animations */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 rgba(239, 68, 68, 0); }
            50% { box-shadow: 0 0 14px var(--live-glow); }
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }
        @keyframes pulseRing {
            0% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(56, 189, 248, 0); }
            100% { transform: scale(0.98); box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
        }
        @keyframes soundBounce {
            0%, 100% { height: 4px; }
            50% { height: 22px; }
        }
        @keyframes flyUp {
            0% {
                transform: translateY(0) scale(0.6) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-550px) scale(1.4) rotate(20deg);
                opacity: 0;
            }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Fixes */
        @media (max-width: 991px) {
            .deck-card {
                margin-top: 1.5rem;
                min-height: 520px;
                max-height: 580px;
            }
            .theater-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="live-navbar">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="brand-wrapper">
                <img src="{{ asset('images/logo.jpg') }}" alt="جامعة طرابلس" class="brand-logo" onerror="this.style.display='none'">
                <div>
                    <div class="brand-title">منصة البث المباشر — يوم التوظيف والتدريب</div>
                    <div class="brand-subtitle">جامعة طرابلس &bull; مكتب تدريب الخريجين</div>
                </div>
            </a>

            <!-- Status & Action Badges -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(isset($setting) && $setting->is_live_now)
                    <div class="live-pill">
                        <span class="live-dot"></span>
                        <span>بث حي ومباشر الآن</span>
                    </div>
                    <div class="viewers-badge">
                        <i class="fas fa-users text-danger"></i>
                        <span id="liveViewerCount">{{ number_format($setting->viewers_count ?: 1) }}</span>
                        <span class="text-white-50">مشاهد الآن</span>
                    </div>
                @else
                    <div class="offline-pill">
                        <i class="far fa-clock"></i>
                        <span>البث متوقف حالياً</span>
                    </div>
                @endif

                <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5" style="border-color: var(--border-glass);">
                    <i class="fas fa-home me-1"></i> الرئيسية
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5 shadow-sm" style="background: var(--primary);">
                        <i class="fas fa-th-large me-1"></i> لوحتي
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('graduate.register') }}" class="btn btn-warning text-dark btn-sm rounded-pill px-3 py-1.5 fw-bold">
                        تسجيل خريج
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Live Theater Stage -->
    <main class="theater-container">
        <div class="row g-4">
            
            <!-- Main Video Column (8 cols on large screens) -->
            <div class="col-lg-8 col-xl-8">
                
                <!-- Video Stage Box -->
                <div class="video-stage-card">
                    
                    <div class="player-viewport" id="playerViewport">
                        <div class="ambient-glow"></div>

                        <!-- Top Right Watermark -->
                        <div class="player-watermark">
                            <img src="{{ asset('images/logo.jpg') }}" alt="UoT" onerror="this.style.display='none'">
                            <span>UoT &bull; LIVE STREAM</span>
                        </div>

                        <!-- Top Left Channel Selector -->
                        @if(isset($cameras) && $cameras->count() > 0)
                        <div class="stream-channels-bar">
                            @foreach($cameras as $cam)
                                <button type="button" class="channel-btn {{ (isset($activeCamera) && $activeCamera->id === $cam->id) ? 'active' : '' }}" onclick="switchCameraFeed('{{ $cam->id }}', '{{ $cam->stream_type }}', '{{ $cam->embed_url }}', '{{ addslashes($cam->title) }}', '{{ addslashes($cam->location_tag ?? '') }}', this)">
                                    <i class="fas fa-video me-1"></i> {{ $cam->title }}
                                </button>
                            @endforeach
                        </div>
                        @endif

                        <!-- Screen Content -->
                        <div class="player-inner" id="playerInner">
                            @if(isset($setting) && $setting->is_live_now && isset($activeCamera))
                                <div id="liveVideoContainer" style="width: 100%; height: 100%; position: absolute; inset: 0; z-index: 5; background: #000;">
                                    @if($activeCamera->is_mjpeg)
                                        <img id="activeStreamImg" src="{{ $activeCamera->embed_url }}" style="width: 100%; height: 100%; object-fit: contain; background: #000;" alt="{{ $activeCamera->title }}">
                                    @elseif($activeCamera->stream_type === 'youtube_live')
                                        <iframe id="activeStreamIframe" src="{{ $activeCamera->embed_url }}" style="width: 100%; height: 100%; border: none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    @elseif($activeCamera->stream_type === 'hls_m3u8' || $activeCamera->is_direct_video)
                                        <video id="activeStreamVideo" controls autoplay muted playsinline style="width: 100%; height: 100%; object-fit: contain; background: #000;">
                                            <source src="{{ $activeCamera->stream_url }}">
                                        </video>
                                    @else
                                        <iframe id="activeStreamIframe" src="{{ $activeCamera->embed_url }}" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                                    @endif
                                </div>
                            @endif

                            <div class="stream-graphics" id="streamGraphicsFallback" style="{{ (isset($setting) && $setting->is_live_now && isset($activeCamera)) ? 'display: none;' : '' }}">
                                
                                <div class="stream-pulse-ring" id="streamPlayBtn" onclick="toggleStreamSimulation()">
                                    <i class="fas fa-play" id="playPauseIcon"></i>
                                </div>

                                <h3 class="fw-bold text-white mb-2" id="channelHeadline">
                                    {{ (isset($setting) && $setting->broadcast_title) ? $setting->broadcast_title : ($fair ? $fair->title : 'البث المباشر للفعاليات والأنشطة') }}
                                </h3>

                                <p class="text-white-50 small mb-2" style="max-width: 540px;" id="channelDesc">
                                    @if(isset($setting) && $setting->is_live_now)
                                        {{ $setting->broadcast_description ?: 'تجري الآن الفعاليات المباشرة والمراسم الرسمية مباشرة من جامعة طرابلس.' }}
                                    @else
                                        البث المباشر متوقف حالياً. سينطلق البث فور بدء الفعاليات المعتمدة في المنظومة.
                                    @endif
                                </p>

                                @if($fair && $fair->event_date)
                                    <div class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fde68a;">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($fair->event_date)->locale('ar')->translatedFormat('l، j F Y') }}
                                        @if($fair->start_time)
                                            &bull; {{ \Carbon\Carbon::parse($fair->start_time)->format('h:i A') }}
                                        @endif
                                    </div>
                                @endif

                                @if(isset($setting) && $setting->is_live_now)
                                <!-- Animated sound equalizer -->
                                <div class="sound-wave-container" id="soundWaves">
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                    <div class="sound-bar"></div>
                                </div>
                                @endif

                            </div>
                        </div>

                        <!-- Player Bottom Controls -->
                        <div class="player-controls-bar">
                            <div class="ctrl-group">
                                <button type="button" class="ctrl-btn" id="ctrlPlayToggle" onclick="toggleStreamSimulation()" title="تشغيل / إيقاف">
                                    <i class="fas fa-play" id="ctrlPlayIcon"></i>
                                </button>
                                <button type="button" class="ctrl-btn" id="ctrlVolumeToggle" onclick="toggleMute()" title="كتم / تشغيل الصوت">
                                    <i class="fas fa-volume-up" id="ctrlVolumeIcon"></i>
                                </button>
                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 small fw-bold" style="font-size: 0.72rem;">
                                    LIVE 1080p60
                                </span>
                            </div>

                            <div class="ctrl-group">
                                <button type="button" class="ctrl-btn" onclick="toggleCinemaMode()" title="وضع المسرح">
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </button>
                                <button type="button" class="ctrl-btn" onclick="toggleFullScreen()" title="شاشة كاملة">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Meta Information & Live Interaction Bar -->
                    <div class="stream-info-bar">
                        <div>
                            <div class="stream-title-text" id="streamCurrentTitle">
                                {{ (isset($setting) && $setting->broadcast_title) ? $setting->broadcast_title : ($fair ? $fair->title : 'البث المباشر للفعاليات والأنشطة') }}
                            </div>
                            <div class="stream-speaker-tag">
                                <i class="fas fa-broadcast-tower text-warning"></i>
                                <span id="streamCurrentSpeaker">
                                    @if(isset($setting) && $setting->is_live_now)
                                        {{ $setting->broadcast_description ?: 'بث حي ومباشر من جامعة طرابلس' }}
                                    @else
                                        البث المباشر متوقف حالياً &bull; ترقبوا بدء الفعاليات القادمة
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Quick Reactions & Share -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="reactions-cluster">
                                <button type="button" class="reaction-trigger-btn" onclick="triggerReaction('👏')" title="تصفيق">👏</button>
                                <button type="button" class="reaction-trigger-btn" onclick="triggerReaction('❤️')" title="أعجبني">❤️</button>
                                <button type="button" class="reaction-trigger-btn" onclick="triggerReaction('🔥')" title="حماس">🔥</button>
                                <button type="button" class="reaction-trigger-btn" onclick="triggerReaction('🎓')" title="فخور">🎓</button>
                                <button type="button" class="reaction-trigger-btn" onclick="triggerReaction('💼')" title="فرصة">💼</button>
                            </div>

                            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 d-flex align-items-center gap-2" onclick="shareStream()" style="border-color: var(--border-glass);">
                                <i class="fas fa-share-alt"></i>
                                <span>مشاركة</span>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- In-person Attendance Callout Banner -->
                @if($fair)
                <div class="ticket-callout-banner">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 1.6rem;">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-white mb-1">هل تود الحضور الشخصي في موقع المعرض؟</h5>
                            <p class="text-white-50 small mb-0">
                                احصل على تذكرتك الرقمية مع رمز QR الخاص بك لمقابلة ممثلي الشركات شخصياً داخل أروقة الجامعة.
                            </p>
                        </div>
                    </div>

                    <div>
                        @if(isset($myRegistration) && $myRegistration)
                            <a href="{{ route('job-fair.my-ticket', $myRegistration->id) }}" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm">
                                <i class="fas fa-qrcode me-1"></i> عرض تذكرتي الرقمية
                            </a>
                        @elseif($fair && $fair->can_register)
                            <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="fas fa-user-check me-1"></i> تسجيل تذكرة حضور المعرض
                            </button>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            <!-- Side Deck Column: Tabs for Live Chat, Agenda & Job Drops (4 cols) -->
            <div class="col-lg-4 col-xl-4">
                
                <div class="deck-card">
                    
                    <!-- Deck Navigation Tabs -->
                    <div class="deck-nav-tabs">
                        <button type="button" class="deck-tab-btn active" onclick="switchDeckTab('chat', this)">
                            <i class="fas fa-comments"></i>
                            <span>الدردشة المباشرة</span>
                        </button>
                        <button type="button" class="deck-tab-btn" onclick="switchDeckTab('schedule', this)">
                            <i class="far fa-calendar-check"></i>
                            <span>جدول البث</span>
                        </button>
                        <button type="button" class="deck-tab-btn" onclick="switchDeckTab('jobs', this)">
                            <i class="fas fa-briefcase"></i>
                            <span>فرص فورية</span>
                        </button>
                    </div>

                    <!-- TAB 1: LIVE CHAT -->
                    <div id="deckTabChat" class="chat-container">
                        
                        <!-- Pinned Announcement -->
                        <div class="pinned-qa-box">
                            <i class="fas fa-thumbtack text-warning"></i>
                            <div>
                                <strong>تنويه:</strong> أرسل أسئلتك للمتحدثين وسيتم اختيار أبرزها في فقرة الأسئلة والأجوبة.
                            </div>
                        </div>

                        <!-- Chat Messages List -->
                        <div class="chat-messages-area" id="chatMessagesArea">
                            <div class="chat-empty-state text-center py-5 px-3 text-white-50" id="chatEmptyState">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: rgba(255,255,255,0.06); color: #94a3b8;">
                                    <i class="fas fa-comments fs-4"></i>
                                </div>
                                <h6 class="text-white fw-bold mb-1">الدردشة المباشرة مفتوحة</h6>
                                <p class="small text-white-50 mb-0">لا توجد رسائل سابقة. شارك برأيك أو اطرح سؤالك أثناء البث.</p>
                            </div>
                        </div>

                        <!-- Chat Input Box -->
                        <div class="chat-input-wrapper">
                            <input type="text" id="liveChatInput" class="chat-input-field" placeholder="شارك برأيك أو اطرح سؤالك..." onkeypress="handleChatKeyPress(event)">
                            <button type="button" class="chat-send-btn" onclick="sendChatMessage()" title="إرسال">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>

                    </div>

                    <!-- TAB 2: BROADCAST SCHEDULE -->
                    <div id="deckTabSchedule" class="timeline-container" style="display: none;">
                        
                        @if(isset($events) && $events->count() > 0)
                            @foreach($events as $index => $event)
                                <div class="timeline-card {{ $index === 0 ? 'active-session' : '' }}">
                                    <div class="timeline-time">
                                        <i class="far fa-clock"></i>
                                        <span>{{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}</span>
                                        @if($index === 0)
                                            <span class="badge bg-danger ms-auto px-2 py-0.5" style="font-size: 0.65rem;">جارٍ الآن</span>
                                        @endif
                                    </div>
                                    <div class="timeline-title">{{ $event->title }}</div>
                                    @if($event->speaker_name)
                                        <div class="timeline-speaker">
                                            <i class="fas fa-user-tie text-primary"></i>
                                            <span>{{ $event->speaker_name }}</span>
                                        </div>
                                    @endif
                                    @if($event->description)
                                        <p class="text-white-50 small mt-2 mb-0">{{ Str::limit($event->description, 110) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5 px-3 text-white-50">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: rgba(255,255,255,0.06); color: #94a3b8;">
                                    <i class="far fa-calendar-times fs-4"></i>
                                </div>
                                <h6 class="text-white fw-bold mb-1">لا توجد فقرات مجدولة حالياً</h6>
                                <p class="small text-white-50 mb-0">سيتم إدراج جدول البث والفعاليات فور اعتمادها في المنظومة.</p>
                            </div>
                        @endif

                    </div>

                    <!-- TAB 3: IMMEDIATE JOB DROPS -->
                    <div id="deckTabJobs" class="jobs-container" style="display: none;">
                        
                        <div class="text-white-50 small mb-2 d-flex align-items-center gap-2">
                            <i class="fas fa-bullhorn text-accent"></i>
                            <span>وظائف وفرص تدريب معلنة خلال فعاليات المعرض:</span>
                        </div>

                        @if(isset($recentJobs) && $recentJobs->count() > 0)
                            @foreach($recentJobs as $job)
                                <div class="job-drop-card">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-white mb-0">{{ $job->title }}</h6>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">
                                            {{ $job->job_type ?? 'دوام كامل' }}
                                        </span>
                                    </div>
                                    <div class="text-white-50 small mb-2">
                                        <i class="far fa-building me-1 text-warning"></i>
                                        {{ $job->company->name ?? 'شركة وطنية رائدة' }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-secondary border-opacity-25">
                                        <small class="text-white-50">{{ $job->location ?? 'طرابلس' }}</small>
                                        <a href="{{ route('graduate.job-opportunities.show', $job->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                            عرض والتسجيل
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-white-50">
                                <i class="fas fa-briefcase fa-2x mb-2 text-white-50"></i>
                                <p class="small">سيتم عرض الفرص الوظيفية فور إعلانها من المتحدثين أثناء البث.</p>
                            </div>
                        @endif

                        <div class="text-center mt-2">
                            <a href="{{ route('graduate.job-opportunities.index') }}" class="btn btn-sm btn-link text-accent text-decoration-none">
                                عرض جميع الفرص الوظيفية في المنصة &larr;
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- Registration Modal for In-Person Attendance Ticket -->
    @if($fair && $fair->can_register && !$myRegistration)
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 text-white" style="background: #0a1529; border-radius: 20px; border: 1px solid var(--border-glass) !important;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="registerModalLabel">
                        <i class="fas fa-ticket-alt text-warning me-2"></i> حجز تذكرة المعرض الحضوري
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                
                <form action="{{ route('job-fair.register', $fair->id) }}" method="POST">
                    @csrf
                    <div class="modal-body py-3">
                        <p class="text-white-50 small mb-3">
                            تتيح لك هذه التذكرة الدخول المجاني لصالات المعرض في الحرم الجامعي والمشاركة في المقابلات المباشرة.
                        </p>

                        <div class="mb-3">
                            <label class="form-label small text-white-50">اهتماماتك المهنية في المعرض:</label>
                            <input type="text" name="interests" class="form-control bg-dark text-white border-secondary" placeholder="مثال: هندسة البرمجيات، شبكات، تدريب مصرفي...">
                        </div>

                        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            سيتم إنشاء رمز QR فريد خاص بك يمكنك حفظه في هاتفك أو طباعته.
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            تأكيد إصدار التذكرة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Toast Notification for Actions -->
    <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 99999">
        <div id="liveToast" class="toast align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body py-3 px-4 d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle text-success fs-5"></i>
                    <span id="toastMsg">تمت العملية بنجاح</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Live stream state
        let isPlaying = false;
        let isMuted = false;
        let activeChannel = 'main';

        // Toggle Stream Play / Pause Simulation
        function toggleStreamSimulation() {
            isPlaying = !isPlaying;
            const playPauseIcon = document.getElementById('playPauseIcon');
            const ctrlPlayIcon = document.getElementById('ctrlPlayIcon');
            const soundWaves = document.getElementById('soundWaves');

            if (isPlaying) {
                playPauseIcon.className = 'fas fa-pause';
                ctrlPlayIcon.className = 'fas fa-pause';
                soundWaves.style.opacity = '1';
                showToast('بدأ تشغيل البث المباشر');
            } else {
                playPauseIcon.className = 'fas fa-play';
                ctrlPlayIcon.className = 'fas fa-play';
                soundWaves.style.opacity = '0.3';
                showToast('تم إيقاف البث مؤقتاً');
            }
        }

        // Toggle Sound Mute
        function toggleMute() {
            isMuted = !isMuted;
            const volumeIcon = document.getElementById('ctrlVolumeIcon');
            if (isMuted) {
                volumeIcon.className = 'fas fa-volume-mute';
                showToast('تم كتم الصوت');
            } else {
                volumeIcon.className = 'fas fa-volume-up';
                showToast('تم تشغيل الصوت');
            }
        }

        // Switch Real Camera Feed from DB
        function switchCameraFeed(camId, type, embedUrl, title, location, btn) {
            document.querySelectorAll('.channel-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const container = document.getElementById('liveVideoContainer');
            const fallback = document.getElementById('streamGraphicsFallback');
            const headline = document.getElementById('channelHeadline');
            const streamTitle = document.getElementById('streamCurrentTitle');
            const streamSpeaker = document.getElementById('streamCurrentSpeaker');

            if (headline) headline.innerText = title;
            if (streamTitle) streamTitle.innerText = title;
            if (streamSpeaker) streamSpeaker.innerText = location ? ('الموقع: ' + location) : 'بث مباشر من جامعة طرابلس';

            if (container) {
                container.style.display = 'block';
                if (fallback) fallback.style.display = 'none';

                if (type === 'youtube_live') {
                    container.innerHTML = `<iframe src="${embedUrl}" style="width: 100%; height: 100%; border: none;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
                } else if (type === 'hls_m3u8') {
                    container.innerHTML = `<video id="activeStreamVideo" controls autoplay muted style="width: 100%; height: 100%; object-fit: cover;"><source src="${embedUrl}" type="application/x-mpegURL"></video>`;
                    if (window.Hls && Hls.isSupported()) {
                        const v = document.getElementById('activeStreamVideo');
                        const hls = new Hls();
                        hls.loadSource(embedUrl);
                        hls.attachMedia(v);
                    }
                } else {
                    container.innerHTML = `<iframe src="${embedUrl}" style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>`;
                }
            }

            showToast('تم التبديل إلى: ' + title);
        }

        // Switch Stream Channel
        function switchChannel(channel, btn) {
            document.querySelectorAll('.channel-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeChannel = channel;

            const headline = document.getElementById('channelHeadline');
            const desc = document.getElementById('channelDesc');
            const streamTitle = document.getElementById('streamCurrentTitle');
            const streamSpeaker = document.getElementById('streamCurrentSpeaker');

            if (channel === 'main') {
                headline.innerText = 'القاعة الرئيسية: حفل الافتتاح والمراسم الرسمية';
                desc.innerText = 'بث وقائع افتتاح المعرض وكلمات رئاسة الجامعة وممثلي كبرى المؤسسات والشركات.';
                streamTitle.innerText = 'الجلسة الافتتاحية: آفاق التوظيف وتأهيل الكفاءات الوطنية 2026';
                streamSpeaker.innerText = 'المتحدث: د. عميد شؤون الخريجين والتدريب • مسرح الجامعة الرئيسي';
            } else if (channel === 'workshops') {
                headline.innerText = 'قاعة ورش العمل المهنية والتطوير الوظيفي';
                desc.innerText = 'جلسات تدريبية مكثفة حول كتابة السيرة الذاتية التنافسية واجتياز المقابلات الوظيفية بنجاح.';
                streamTitle.innerText = 'ورشة: استراتيجيات المقابلات الوظيفية لدى المؤسسات الدولية والمحلية';
                streamSpeaker.innerText = 'المدرب: أ. مستشار التطوير المهني • القاعة الافتراضية B';
            } else if (channel === 'interviews') {
                headline.innerText = 'غرفة المقابلات السريعة وممثلي الشركات';
                desc.innerText = 'لقاءات حصرية ومباشرة مع مدراء الموارد البشرية لعرض الشواغر وفرص العمل الصيفي.';
                streamTitle.innerText = 'لقاءات مسؤولي التوظيف: استكشاف متطلبات الوظائف الشاغرة';
                streamSpeaker.innerText = 'ضيوف الجلسة: مدراء التوظيف في القطاع المصرفي والتقني';
            }

            showToast('تم الانتقال إلى: ' + btn.innerText.trim());
        }

        // Switch Deck Tabs (Chat, Schedule, Jobs)
        function switchDeckTab(tabName, btn) {
            document.querySelectorAll('.deck-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.getElementById('deckTabChat').style.display = (tabName === 'chat') ? 'flex' : 'none';
            document.getElementById('deckTabSchedule').style.display = (tabName === 'schedule') ? 'flex' : 'none';
            document.getElementById('deckTabJobs').style.display = (tabName === 'jobs') ? 'flex' : 'none';
        }

        // Trigger Floating Flying Reactions
        function triggerReaction(emoji) {
            const emojiEl = document.createElement('div');
            emojiEl.className = 'floating-emoji';
            emojiEl.innerText = emoji;

            // Random horizontal position around reaction cluster
            const randomX = Math.random() * 80 + (window.innerWidth < 768 ? 40 : 260);
            emojiEl.style.right = randomX + 'px';

            document.body.appendChild(emojiEl);

            setTimeout(() => {
                emojiEl.remove();
            }, 2700);
        }

        // Send Live Chat Message
        function sendChatMessage() {
            const input = document.getElementById('liveChatInput');
            const text = input.value.trim();
            if (!text) return;

            const chatArea = document.getElementById('chatMessagesArea');
            const msgEl = document.createElement('div');
            msgEl.className = 'chat-msg';

            @auth
                const userName = "{{ auth()->user()->name }}";
                const userRole = "{{ auth()->user()->role === 'admin' ? 'إدارة' : (auth()->user()->role === 'graduate' ? 'خريج' : 'مستخدم') }}";
                const badgeClass = "{{ auth()->user()->role === 'admin' ? 'badge-mod' : 'badge-grad' }}";
            @else
                const userName = "زائر كريم";
                const userRole = "مشاهد";
                const badgeClass = "badge-grad";
            @endauth

            const initials = userName.split(' ').map(n => n[0]).join('').substring(0, 2);

            const emptyState = document.getElementById('chatEmptyState');
            if (emptyState) emptyState.remove();

            msgEl.innerHTML = `
                <div class="chat-avatar" style="background: #2563eb;">${initials}</div>
                <div class="chat-bubble">
                    <div class="chat-author">
                        <span>${userName}</span>
                        <span class="${badgeClass}">${userRole}</span>
                    </div>
                    <div class="chat-text">${escapeHtml(text)}</div>
                </div>
            `;

            chatArea.appendChild(msgEl);
            chatArea.scrollTop = chatArea.scrollHeight;
            input.value = '';

            triggerReaction('💬');
        }

        function handleChatKeyPress(e) {
            if (e.key === 'Enter') {
                sendChatMessage();
            }
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Share Stream
        function shareStream() {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showToast('تم نسخ رابط البث المباشر إلى الحافظة');
                });
            } else {
                showToast('رابط البث: ' + window.location.href);
            }
        }

        // Full Screen Toggle
        function toggleFullScreen() {
            const viewport = document.getElementById('playerViewport');
            if (!document.fullscreenElement) {
                viewport.requestFullscreen().catch(err => {
                    console.log(err);
                });
            } else {
                document.exitFullscreen();
            }
        }

        // Cinema Mode Toggle
        function toggleCinemaMode() {
            const viewport = document.getElementById('playerViewport');
            viewport.classList.toggle('cinema-mode');
            showToast('تم تبديل وضع المشاهدة');
        }

        // Toast Helper
        function showToast(msg) {
            const toastEl = document.getElementById('liveToast');
            document.getElementById('toastMsg').innerText = msg;
            const toast = new bootstrap.Toast(toastEl, { delay: 2800 });
            toast.show();
        }
    </script>
</body>
</html>
