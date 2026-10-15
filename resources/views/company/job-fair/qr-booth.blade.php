<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>ورقة باركود الجناح الرسمي — {{ $company->name }} | {{ $fair->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --primary-navy: #0d3882;
            --secondary-blue: #1565c0;
            --accent-gold: #f59e0b;
            --accent-amber: #d97706;
            --dark-navy: #071e4a;
            --surface-bg: #f8fafc;
            --border-soft: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            font-family: 'Cairo', 'Tajawal', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #0b1f3a 0%, #1e3a8a 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 24px 16px 40px;
            color: #0f172a;
        }

        /* ── شريط أدوات التحكم العلوي (لا يطبع) ── */
        .controls-bar {
            width: 100%;
            max-width: 820px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            z-index: 20;
            flex-wrap: wrap;
        }

        .controls-title {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ctrl-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ctrl-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .ctrl-btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b1f3a;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }

        .ctrl-btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #000;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
        }

        .ctrl-btn-outline {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .ctrl-btn-outline:hover {
            background: #ffffff;
            color: var(--primary-navy);
            transform: translateY(-2px);
        }

        #save-status-msg {
            display: none;
            background: #10b981;
            color: white;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* ── غلاف ورقة الـ A4 الرسمية للطباعة ── */
        .booth-page-wrapper {
            width: 100%;
            max-width: 820px;
            display: flex;
            justify-content: center;
        }

        .booth-sheet {
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
            border: 10px solid #ffffff;
            display: flex;
            flex-direction: column;
        }

        /* الإطار الزخرفي الذهبي الداخلي */
        .sheet-inner-border {
            border: 2px solid #0d3882;
            border-radius: 12px;
            padding: 24px 28px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            position: relative;
            background: radial-gradient(circle at 50% 0%, rgba(13, 56, 130, 0.02) 0%, transparent 60%);
        }

        /* الزوايا الذهبية الزخرفية */
        .corner-accent {
            position: absolute;
            width: 24px;
            height: 24px;
            border-color: #f59e0b;
            border-style: solid;
            pointer-events: none;
        }
        .corner-top-right { top: 6px; right: 6px; border-width: 3px 3px 0 0; }
        .corner-top-left  { top: 6px; left: 6px; border-width: 3px 0 0 3px; }
        .corner-bottom-right { bottom: 6px; right: 6px; border-width: 0 3px 3px 0; }
        .corner-bottom-left  { bottom: 6px; left: 6px; border-width: 0 0 3px 3px; }

        /* ── ترويسة هوية المعرض الرسمية ── */
        .fair-identity-header {
            background: linear-gradient(135deg, #071e4a 0%, #0d3882 60%, #1565c0 100%);
            border-radius: 14px;
            padding: 16px 20px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(13, 56, 130, 0.2);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .fair-identity-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 20%, rgba(245, 158, 11, 0.2) 0%, transparent 60%);
            pointer-events: none;
        }

        .header-content-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: relative;
            z-index: 2;
        }

        .org-logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .org-logo-box img {
            height: 60px;
            width: auto;
            object-fit: contain;
            background: #ffffff;
            border-radius: 10px;
            padding: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .uni-logo-box img {
            max-width: 65px;
            padding: 4px;
        }

        .fair-logo-box img {
            max-width: 170px;
            padding: 5px 12px;
        }

        .fair-title-center {
            text-align: center;
            flex-grow: 1;
        }

        .fair-title-center .sup-title {
            font-size: 0.82rem;
            color: #fde68a;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .fair-title-center h1 {
            font-size: 1.45rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
            line-height: 1.3;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .fair-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 6px;
        }

        /* ── بطاقة هوية الشركة والجناح ── */
        .company-booth-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .company-brand-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .company-logo-frame {
            width: 78px;
            height: 78px;
            border-radius: 14px;
            border: 2.5px solid #0d3882;
            background: #ffffff;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(13, 56, 130, 0.12);
            flex-shrink: 0;
        }

        .company-logo-frame img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-logo-placeholder {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary-navy);
            background: #eff6ff;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .company-details h2 {
            font-size: 1.4rem;
            font-weight: 900;
            color: #0b1f3a;
            margin: 0 0 4px 0;
            line-height: 1.2;
        }

        .company-details p {
            font-size: 0.88rem;
            color: #64748b;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .booth-tag-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .booth-badge-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b1f3a;
            font-weight: 900;
            font-size: 0.95rem;
            padding: 6px 16px;
            border-radius: 30px;
            box-shadow: 0 3px 10px rgba(245, 158, 11, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #fde68a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .official-badge-navy {
            background: #eff6ff;
            color: var(--primary-navy);
            border: 1px solid #bfdbfe;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* ── قسم الباركود QR المركزي ── */
        .qr-hero-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 2px dashed #0d3882;
            border-radius: 16px;
            padding: 22px 20px 18px;
            text-align: center;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-action-banner {
            background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.05rem;
            padding: 8px 24px;
            border-radius: 40px;
            box-shadow: 0 4px 12px rgba(13, 56, 130, 0.25);
            margin-bottom: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .qr-action-banner i {
            color: #f59e0b;
            font-size: 1.2rem;
            animation: pulse-cam 2s infinite ease-in-out;
        }

        @keyframes pulse-cam {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.18); }
        }

        /* صندوق رمز الـ QR مع زوايا الاستهداف */
        .qr-code-target-box {
            position: relative;
            background: #ffffff;
            padding: 16px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 2px solid #e2e8f0;
            display: inline-block;
            margin-bottom: 12px;
        }

        .target-corner {
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #0d3882;
            border-style: solid;
        }
        .tc-tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 10px; }
        .tc-tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 10px; }
        .tc-bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 10px; }
        .tc-br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 10px; }

        #qrcode canvas, #qrcode img {
            border-radius: 8px;
            display: block;
            margin: 0 auto;
        }

        .scan-instructions-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
        }

        .step-num {
            width: 22px;
            height: 22px;
            background: #f59e0b;
            color: #0b1f3a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 900;
        }

        /* ── قسم الرعاة الرسميين للمعرض ── */
        .sponsors-strip-card {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            padding: 12px 18px 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        }

        .sponsors-header-ribbon {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .ribbon-line {
            flex-grow: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
        }

        .ribbon-text {
            font-size: 0.84rem;
            font-weight: 800;
            color: #0d3882;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 3px 16px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
        }

        .sponsors-grid {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .sponsor-chip-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 150px;
            max-width: 220px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .sponsor-chip-item.tier-diamond { border-color: #93c5fd; background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%); }
        .sponsor-chip-item.tier-gold    { border-color: #fde68a; background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); }
        .sponsor-chip-item.tier-silver  { border-color: #cbd5e1; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
        .sponsor-chip-item.tier-strategic {
            border: 2px solid #f59e0b;
            background: linear-gradient(135deg, #ffffff 0%, #fffbeb 60%, #fef3c7 100%);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.18);
            min-width: 195px;
        }
        .sponsor-chip-item.tier-strategic .sponsor-thumb {
            border: 1.5px solid #fde68a;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.15);
        }
        .sponsor-chip-item.tier-strategic .sponsor-tier-tag {
            color: #b45309;
            font-weight: 800;
        }
        .sponsor-chip-item.tier-strategic .sponsor-name {
            color: #0b1f3a;
            font-weight: 900;
        }

        .sponsor-thumb {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 6px;
            background: #ffffff;
            padding: 2px;
            flex-shrink: 0;
            border: 1px solid #f1f5f9;
        }

        .sponsor-thumb-fallback {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sponsor-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sponsor-name {
            font-size: 0.82rem;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .sponsor-tier-tag {
            font-size: 0.68rem;
            font-weight: 700;
            margin-top: 2px;
        }
        .sponsor-chip-item.tier-diamond .sponsor-tier-tag { color: #1d4ed8; }
        .sponsor-chip-item.tier-gold .sponsor-tier-tag    { color: #b45309; }
        .sponsor-chip-item.tier-silver .sponsor-tier-tag  { color: #475569; }
        .sponsor-chip-item.tier-strategic .sponsor-tier-tag { color: #b45309; }

        /* ── تذييل الوثيقة الرسمية ── */
        .sheet-official-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-top: 1.5px solid #e2e8f0;
            padding-top: 10px;
            font-size: 0.74rem;
            color: #64748b;
            font-weight: 600;
            flex-wrap: wrap;
        }

        .footer-seal {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #0d3882;
            font-weight: 800;
        }

        .footer-serial {
            font-family: monospace;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
        }

        /* ── إعدادات الطباعة الرسمية بمقاس A4 ── */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .controls-bar, #save-status-msg {
                display: none !important;
            }

            .booth-page-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .booth-sheet {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                padding: 0 !important;
            }

            .sheet-inner-border {
                border: 2px solid #0d3882 !important;
                border-radius: 0 !important;
                padding: 16px 20px !important;
                height: 100% !important;
                justify-content: space-between !important;
            }

            .fair-identity-header,
            .booth-badge-gold,
            .qr-action-banner {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    @php
        $companyLogo = $company->logo_path ? \Illuminate\Support\Facades\Storage::url($company->logo_path) : null;
        // نستخدم الشعار الرسمي الملون لمعرض التوظيف ليكون واضحاً وعالي التباين على الحاوية البيضاء
        $fairLogo = $fair->logo_url ?? asset('images/job_fair_logo.png');
        $uniLogo = asset('images/logo.jpg');
        $scanUrl = route('graduate.job-fairs.company.scan', ['fair' => $fair->id, 'company' => $company->id]);
        $boothNumber = $fairCompany->booth_number ?? null;
        $boothLocation = $fairCompany->booth_location ?? null;
        $serialCode = 'GT-BOOTH-' . $fair->id . '-' . $company->id . '-' . strtoupper(substr(md5($fair->id . '-' . $company->id . '-gts-tripoli'), 0, 6));

        // تنظيم قائمة الرعاة والتأكد التام من إدراج الراعي الاستراتيجي (شركة الواحة لتنظيم المعارض) في المقدمة دائماً
        $strategicSponsor = (isset($sponsors) ? $sponsors->first(function($s) {
            return in_array(strtolower(trim((string)$s->tier)), ['strategic', 'الراعي الاستراتيجي']);
        }) : null);

        if (!$strategicSponsor) {
            $strategicSponsor = (object)[
                'name' => 'شركة الواحة لتنظيم المعارض والمؤتمرات',
                'tier' => 'strategic',
                'tier_label' => 'الراعي الاستراتيجي',
                'tier_icon' => 'fa-crown',
                'logo_url' => asset('images/wahaexpo_logo_transparent.png'),
            ];
        }

        $otherSponsors = isset($sponsors) ? $sponsors->filter(function($s) {
            return !in_array(strtolower(trim((string)$s->tier)), ['strategic', 'الراعي الاستراتيجي']);
        }) : collect();
    @endphp

    <!-- شريط أدوات التحكم العلوي -->
    <div class="controls-bar">
        <div class="controls-title">
            <i class="fas fa-print text-warning fs-5"></i>
            <span>ورقة باركود الجناح الرسمي — جاهزة للطباعة والعرض المكتبي</span>
        </div>

        <div class="ctrl-actions">
            <span id="save-status-msg"><i class="fas fa-check-circle me-1"></i> تم الحفظ بنجاح</span>

            <button type="button" class="ctrl-btn ctrl-btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span>طباعة الورقة (Print A4)</span>
            </button>

            <button type="button" class="ctrl-btn ctrl-btn-outline" onclick="saveAsImage()">
                <i class="fas fa-download"></i>
                <span>تنزيل كصورة (PNG)</span>
            </button>

            <a href="{{ route('company.job-fairs.index') }}" class="ctrl-btn ctrl-btn-outline">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للمعرض</span>
            </a>
        </div>
    </div>

    <!-- بطاقة الورقة الرسمية بمقاس A4 -->
    <div class="booth-page-wrapper">
        <div class="booth-sheet" id="print-booth-card">
            <div class="sheet-inner-border">
                <!-- زوايا زخرفية مذهبة -->
                <div class="corner-accent corner-top-right"></div>
                <div class="corner-accent corner-top-left"></div>
                <div class="corner-accent corner-bottom-right"></div>
                <div class="corner-accent corner-bottom-left"></div>

                <!-- 1. ترويسة هوية المعرض الرسمية -->
                <div class="fair-identity-header">
                    <div class="header-content-grid">
                        <!-- شعار جامعة طرابلس -->
                        <div class="org-logo-box uni-logo-box">
                            <img src="{{ $uniLogo }}" alt="جامعة طرابلس" onerror="this.src='{{ asset('storage/logo.jpg') }}'">
                        </div>

                        <!-- اسم وتفاصيل المعرض -->
                        <div class="fair-title-center">
                            <div class="sup-title">جامعة طرابلس • مكتب تدريب وتأهيل الخريجين</div>
                            <h1>{{ $fair->title }}</h1>
                            <div class="fair-meta-pill">
                                @if($fair->event_date)
                                    <span><i class="fas fa-calendar-alt text-warning me-1"></i>{{ $fair->event_date->translatedFormat('l d F Y') }}</span>
                                    <span>•</span>
                                @endif
                                <span><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $fair->location ?? 'الحرم الجامعي — جامعة طرابلس' }}</span>
                            </div>
                        </div>

                        <!-- شعار المعرض المعتمد -->
                        <div class="org-logo-box fair-logo-box">
                            <img src="{{ $fairLogo }}" alt="{{ $fair->title }}" onerror="this.src='{{ asset('images/job_fair_logo.png') }}'">
                        </div>
                    </div>
                </div>

                <!-- 2. كرت تعريف الشركة والجناح -->
                <div class="company-booth-card">
                    <div class="company-brand-group">
                        <div class="company-logo-frame">
                            @if($companyLogo)
                                <img src="{{ $companyLogo }}" alt="{{ $company->name }}" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                <div class="company-logo-placeholder" style="display: none;">
                                    {{ mb_substr($company->name, 0, 1) }}
                                </div>
                            @else
                                <div class="company-logo-placeholder">
                                    {{ mb_substr($company->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="company-details">
                            <h2>{{ $company->name }}</h2>
                            <p>
                                <i class="fas fa-briefcase text-primary"></i>
                                <span>{{ $company->industry ?? 'قطاع الأعمال والخدمات المهنية' }}</span>
                                @if($company->city || $company->address)
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-location-dot text-danger"></i>
                                    <span>{{ $company->city ?? Str::limit($company->address, 25) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="booth-tag-container">
                        @if($boothNumber)
                            <div class="booth-badge-gold">
                                <i class="fas fa-store"></i>
                                <span>جناح رقم: {{ $boothNumber }}</span>
                            </div>
                        @else
                            <div class="booth-badge-gold">
                                <i class="fas fa-award"></i>
                                <span>جناح رسمي معتمد</span>
                            </div>
                        @endif

                        <div class="official-badge-navy">
                            <i class="fas fa-circle-check text-success"></i>
                            <span>مشارك ومستقبل للسير الذاتية</span>
                        </div>
                    </div>
                </div>

                <!-- 3. كرت رمز الاستجابة السريعة QR المركزي -->
                <div class="qr-hero-card">
                    <div class="qr-action-banner">
                        <i class="fas fa-camera"></i>
                        <span>امسح الرمز بكاميرا هاتفك لتسليم سيرتك الذاتية فوراً للجناح</span>
                    </div>

                    <div class="qr-code-target-box">
                        <div class="target-corner tc-tl"></div>
                        <div class="target-corner tc-tr"></div>
                        <div class="target-corner tc-bl"></div>
                        <div class="target-corner tc-br"></div>

                        <div id="qrcode"></div>
                    </div>

                    <div class="scan-instructions-row">
                        <div class="step-item">
                            <span class="step-num">1</span>
                            <span>افتح كاميرا الهاتف</span>
                        </div>
                        <div class="step-item">
                            <span class="step-num">2</span>
                            <span>وجّه الكاميرا نحو الرمز</span>
                        </div>
                        <div class="step-item">
                            <span class="step-num">3</span>
                            <span>اضغط الإشعار وسلّم سيرتك مباشرة</span>
                        </div>
                    </div>
                </div>

                <!-- 4. قسم الرعاة الرسميين للمعرض -->
                <div class="sponsors-strip-card">
                    <div class="sponsors-header-ribbon">
                        <div class="ribbon-line"></div>
                        <div class="ribbon-text">
                            <i class="fas fa-handshake text-warning"></i>
                            <span>الرعاة والشركاء الرسميون للمعرض • Official Sponsors</span>
                        </div>
                        <div class="ribbon-line"></div>
                    </div>

                    <div class="sponsors-grid">
                        {{-- 1. الراعي الاستراتيجي دائماً في صدارة الرعاة --}}
                        @if($strategicSponsor)
                            @php
                                $stratLogo = $strategicSponsor->logo_url 
                                    ?? ($strategicSponsor->logo_path ? (str_starts_with($strategicSponsor->logo_path, 'images/') ? asset($strategicSponsor->logo_path) : \Illuminate\Support\Facades\Storage::url($strategicSponsor->logo_path)) : asset('images/wahaexpo_logo_transparent.png'));
                            @endphp
                            <div class="sponsor-chip-item tier-strategic">
                                <img src="{{ $stratLogo }}" alt="{{ $strategicSponsor->name }}" class="sponsor-thumb" onerror="this.src='{{ asset('images/wahaexpo_logo.png') }}'">
                                <div class="sponsor-info">
                                    <div class="sponsor-name" title="{{ $strategicSponsor->name }}">{{ $strategicSponsor->name }}</div>
                                    <div class="sponsor-tier-tag">
                                        <i class="fas fa-crown text-warning me-1"></i>الراعي الاستراتيجي
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 2. بقية الرعاة المعتمدين --}}
                        @if(isset($otherSponsors) && $otherSponsors->count() > 0)
                            @foreach($otherSponsors as $sponsor)
                                @php
                                    $sponsorLogo = $sponsor->logo_url 
                                        ?? ($sponsor->logo_path ? (str_starts_with($sponsor->logo_path, 'images/') ? asset($sponsor->logo_path) : \Illuminate\Support\Facades\Storage::url($sponsor->logo_path)) : null);
                                    $tierClass = match(strtolower(trim((string)$sponsor->tier))) {
                                        'strategic', 'الراعي الاستراتيجي' => 'tier-strategic',
                                        'diamond', 'الراعي الماسي' => 'tier-diamond',
                                        'platinum', 'الراعي البلاتيني' => 'tier-diamond',
                                        'gold', 'الراعي الذهبي' => 'tier-gold',
                                        'silver', 'الراعي الفضي' => 'tier-silver',
                                        default => 'tier-gold'
                                    };
                                @endphp
                                <div class="sponsor-chip-item {{ $tierClass }}">
                                    @if($sponsorLogo)
                                        <img src="{{ $sponsorLogo }}" alt="{{ $sponsor->name }}" class="sponsor-thumb" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                        <div class="sponsor-thumb-fallback" style="display: none;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @else
                                        <div class="sponsor-thumb-fallback">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    @endif

                                    <div class="sponsor-info">
                                        <div class="sponsor-name" title="{{ $sponsor->name }}">{{ $sponsor->name }}</div>
                                        <div class="sponsor-tier-tag">
                                            <i class="fas {{ $sponsor->tier_icon ?? 'fa-trophy' }} me-1"></i>{{ $sponsor->tier_label }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- 5. تذييل الوثيقة الرسمية والأمان -->
                <div class="sheet-official-footer">
                    <div class="footer-seal">
                        <i class="fas fa-certificate text-warning"></i>
                        <span>وثيقة اعتماد رسمية صادرة عن منظومة معارض التوظيف المركزية — جامعة طرابلس</span>
                    </div>

                    <div class="footer-serial">
                        <span>كود التحقق: {{ $serialCode }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- سكربت توليد الـ QR وحفظ الصورة -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scanUrl = "{{ $scanUrl }}";

            // توليد رمز الاستجابة السريعة عالي الدقة (300x300)
            new QRCode(document.getElementById("qrcode"), {
                text: scanUrl,
                width: 250,
                height: 250,
                colorDark : "#071e4a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });

        // حفظ الورقة كصورة PNG عالية الدقة
        function saveAsImage() {
            const card = document.getElementById('print-booth-card');
            const msg = document.getElementById('save-status-msg');

            if (!card) return;

            if (msg) {
                msg.style.display = 'inline-block';
                msg.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري توليد الصورة...';
            }

            html2canvas(card, {
                scale: 2.5,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = "ورقة_باركود_الجناح_{{ preg_replace('/[^\p{L}\p{N}_-]+/u', '_', $company->name) }}.png";
                link.href = canvas.toDataURL('image/png');
                link.click();

                if (msg) {
                    msg.innerHTML = '<i class="fas fa-check-circle me-1"></i> تم تنزيل الصورة بنجاح!';
                    setTimeout(() => { msg.style.display = 'none'; }, 4000);
                }
            }).catch(err => {
                console.error(err);
                if (msg) {
                    msg.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> حدث خطأ أثناء التوليد، يرجى استخدام زر الطباعة.';
                }
            });
        }
    </script>
</body>
</html>
