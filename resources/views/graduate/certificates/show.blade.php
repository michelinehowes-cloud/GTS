<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>شهادة حضور معتمدة — {{ $certificate->recipient_name }} — {{ $certificate->certificate_code }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@400;600;700;800;900&family=Alexandria:wght@400;600;700;800&family=Tajawal:wght@500;700;800&family=Reem+Kufi:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f3a53;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #eef2f6;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px 40px;
        }

        /* ═════════════════════════════════════════════
           TOP ACTION BAR (Hidden on Print)
        ═════════════════════════════════════════════ */
        .action-bar {
            width: 100%;
            max-width: 1120px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 14px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 24px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            gap: 12px;
            flex-wrap: wrap;
        }

        .action-bar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(13, 148, 136, 0.1);
            color: #0d9488;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            border: 1px solid rgba(13, 148, 136, 0.25);
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d9488, #0284c7);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 148, 136, 0.45);
            color: #ffffff;
        }

        .btn-outline {
            background: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        .print-tip {
            font-size: 0.78rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: 6px 16px;
            border-radius: 50px;
            border: 1px dashed #cbd5e1;
        }

        /* ═════════════════════════════════════════════
           A4 LANDSCAPE CERTIFICATE CANVAS
           Aspect Ratio: 297 / 210 = 1.4142857 (A4 Landscape)
        ═════════════════════════════════════════════ */
        .cert-viewport-wrapper {
            width: 100%;
            max-width: 1120px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .a4-landscape-cert {
            container-type: inline-size;
            position: relative;
            width: 100%;
            aspect-ratio: 297 / 210;
            background-color: #ffffff;
            background-image: url('{{ asset("images/certificate_template_a4_landscape_hd.png") }}?v={{ filemtime(public_path("images/certificate_template_a4_landscape_hd.png")) }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.16), 0 2px 6px rgba(0,0,0,0.06);
            overflow: hidden;
            user-select: text;
        }

        /* ═════════════════════════════════════════════
           DYNAMIC FIELDS ACCORDING TO NEW CERTIFICATE DESIGN
        ═════════════════════════════════════════════ */

        /* 1. Trainee Name (المتدرب/ة)
           Positioned directly above the trainee underline */
        .cert-field-recipient {
            position: absolute;
            top: 38.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 72%;
            text-align: center;
            font-family: 'Amiri', 'Cairo', serif;
            font-size: 3.4cqi;
            font-weight: 700;
            color: #0f3a53;
            letter-spacing: 0.3px;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 0 1px 2px rgba(15, 58, 83, 0.08);
            z-index: 10;
        }

        /* 2. Middle Section: Completion Sentence + Course Title + Accreditation Ribbon
           Spanning smoothly between Y = 40.5% and Y = 71.5% */
        .cert-middle-block {
            position: absolute;
            top: 55.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 82%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            z-index: 10;
            gap: 0.8cqi;
        }

        .cert-completion-intro {
            font-family: 'Cairo', 'Alexandria', sans-serif;
            font-size: 1.4cqi;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .cert-course-name {
            font-family: 'Cairo', 'Alexandria', sans-serif;
            font-size: 2.5cqi;
            font-weight: 800;
            color: #0f4c5c;
            letter-spacing: 0.2px;
            line-height: 1.25;
            max-width: 95%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Executive Accreditation & Hours Ribbon */
        .cert-accreditation-ribbon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 1.4cqi;
            background: rgba(240, 249, 250, 0.92);
            backdrop-filter: blur(4px);
            border: 1.5px solid rgba(13, 148, 136, 0.3);
            padding: 0.35cqi 1.4cqi;
            border-radius: 50px;
            box-shadow: 0 3px 12px rgba(15, 76, 92, 0.06);
            white-space: nowrap;
            max-width: 90%;
        }

        .cert-accreditation-ribbon .accred-item {
            display: inline-flex;
            align-items: center;
            gap: 0.45cqi;
            font-family: 'Cairo', sans-serif;
            font-size: 1.15cqi;
        }

        .cert-accreditation-ribbon .accred-icon {
            color: #0d9488;
            font-size: 1.15cqi;
            display: flex;
            align-items: center;
        }

        .cert-accreditation-ribbon .accred-label {
            color: #64748b;
            font-weight: 600;
        }

        .cert-accreditation-ribbon .accred-name {
            color: #0f3a53;
            font-weight: 800;
        }

        .cert-accreditation-ribbon .accred-logo {
            height: 2.1cqi;
            width: 2.1cqi;
            object-fit: contain;
            border-radius: 5px;
            background: #ffffff;
            padding: 0.1cqi;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .cert-accreditation-ribbon .accred-divider {
            width: 1px;
            height: 1.8cqi;
            background: rgba(203, 213, 225, 0.9);
        }

        /* 3. Trainer Field (بغطاء المدرب/ة)
           Positioned directly and perfectly under 'بعطاء المدرب/ة' at X = 61.6%, Y = 87.5% */
        .cert-field-trainer {
            position: absolute;
            left: 61.6%;
            top: 87.5%;
            transform: translate(-50%, -50%);
            width: 26cqi;
            text-align: center;
            z-index: 10;
        }

        .cert-trainer-name {
            font-family: 'Cairo', 'Alexandria', sans-serif;
            font-size: 1.85cqi;
            font-weight: 800;
            color: #0f3a53;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 4. Official Subtle Document Code (NO QR CODE as requested)
           Placed in the bottom left margin corner */
        .cert-doc-meta {
            position: absolute;
            bottom: 3.2%;
            left: 4.5%;
            display: flex;
            flex-direction: column;
            gap: 0.15cqi;
            z-index: 10;
            direction: rtl;
            text-align: right;
            font-family: 'Alexandria', 'Cairo', monospace;
            font-size: 0.76cqi;
            color: #64748b;
        }

        .cert-doc-code {
            font-weight: 700;
            color: #0f3a53;
            display: inline-flex;
            align-items: center;
            gap: 0.3cqi;
            letter-spacing: 0.3px;
        }

        .cert-doc-code i {
            color: #0d9488;
            font-size: 0.8cqi;
        }

        .cert-doc-date {
            font-size: 0.7cqi;
            color: #64748b;
        }

        /* ═════════════════════════════════════════════
           PRINT SPECIFICATION (Strict A4 Landscape PDF)
           Standard A4 Dimensions: 297mm × 210mm
        ═════════════════════════════════════════════ */
        @page {
            size: A4 landscape;
            margin: 0mm;
        }

        @media print {
            html, body {
                width: 297mm !important;
                height: 210mm !important;
                margin: 0mm !important;
                padding: 0mm !important;
                background: #ffffff !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .action-bar,
            .print-tip,
            .no-print {
                display: none !important;
            }

            .cert-viewport-wrapper {
                width: 297mm !important;
                height: 210mm !important;
                max-width: 297mm !important;
                max-height: 210mm !important;
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }

            .a4-landscape-cert {
                width: 297mm !important;
                height: 210mm !important;
                max-width: 297mm !important;
                max-height: 210mm !important;
                aspect-ratio: auto !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar (Screen Only - Hidden on Print) --}}
    <div class="action-bar no-print">
        <div class="action-bar-info">
            <a href="{{ route('graduate.certificates.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-right"></i>
                <span>العودة لسجل الشهادات</span>
            </a>
            <div class="action-badge">
                <i class="fas fa-file-contract"></i>
                <span>ورقة A4 بالعرض (297mm × 210mm)</span>
            </div>
        </div>

        <div class="action-buttons">
            <div class="print-tip">
                <i class="fas fa-info-circle text-primary"></i>
                <span>لأفضل نتيجة طباعة: اختر الاتجاه (أفقي / Landscape) وحجم (A4) وتفعيل (رسومات الخلفية)</span>
            </div>
            <a href="{{ route('certificates.verify', $certificate->certificate_code) }}" target="_blank" class="btn btn-outline">
                <i class="fas fa-shield-alt text-primary"></i>
                <span>التحقق الرقمي</span>
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                <span>طباعة / حفظ الشهادة PDF</span>
            </button>
        </div>
    </div>

    {{-- Main A4 Landscape Certificate Document --}}
    <div class="cert-viewport-wrapper">
        <div class="a4-landscape-cert" id="certificateDoc">

            {{-- 1. Trainee Name (المتدرب/ة) - فوق الخط الأفقي الأول تماماً --}}
            <div class="cert-field-recipient" title="{{ $certificate->recipient_name }}">
                {{ $certificate->recipient_name }}
            </div>

            {{-- 2. Middle Block: Course Details & Accreditation Ribbon --}}
            @php
                $cleanTitle = preg_replace('/^(شهادة إتمام\s*|شهادة مشاركة في\s*|شهادة\s*)/u', '', $certificate->title);
                $cleanTitle = trim($cleanTitle);
                $companyLogoPath = $certificate->effective_company_logo;
            @endphp
            <div class="cert-middle-block">
                <div class="cert-completion-intro">قد اجتاز بنجاح الدورة التدريبية بكافة متطلباتها:</div>
                <div class="cert-course-name" title="{{ $cleanTitle }}">« {{ $cleanTitle }} »</div>

                @if(($certificate->has_company_collaboration && $certificate->company_name) || ($certificate->hours && $certificate->hours > 0))
                <div class="cert-accreditation-ribbon">
                    @if($certificate->hours && $certificate->hours > 0)
                        <div class="accred-item">
                            <span class="accred-icon"><i class="fas fa-clock"></i></span>
                            <span class="accred-label">المدة:</span>
                            <span class="accred-name">{{ $certificate->hours }} ساعة تدريبية معتمدة</span>
                        </div>
                    @endif

                    @if($certificate->has_company_collaboration && $certificate->company_name)
                        @if($certificate->hours && $certificate->hours > 0)
                            <div class="accred-divider"></div>
                        @endif
                        <div class="accred-item">
                            <span class="accred-icon"><i class="fas fa-handshake"></i></span>
                            <span class="accred-label">بالشراكة والتعاون مع:</span>
                            @if($companyLogoPath)
                                <img src="{{ asset('storage/' . $companyLogoPath) }}" alt="{{ $certificate->company_name }}" class="accred-logo">
                            @endif
                            <span class="accred-name">{{ $certificate->company_name }}</span>
                        </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- 3. Trainer / Instructor Field (بغطاء المدرب/ة - فوق الخط الأوسط السفلي) --}}
            @php
                $instructorName = $certificate->instructor_name 
                    ?: ($certificate->training?->instructor_name ?? ($certificate->training?->trainer?->name ?? ''));
            @endphp
            @if($instructorName)
            <div class="cert-field-trainer" title="{{ $instructorName }}">
                <div class="cert-trainer-name">{{ $instructorName }}</div>
            </div>
            @endif

            {{-- 4. رمز الشهادة والتاريخ بشكل رسمي ومختصر (بدون QR Code بناءً على طلب المستخدم) --}}
            <div class="cert-doc-meta">
                <div class="cert-doc-code">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ $certificate->certificate_code }}</span>
                </div>
                @if($certificate->issue_date)
                <div class="cert-doc-date">
                    {{ $certificate->issue_date->locale('ar')->translatedFormat('d F Y') }}
                </div>
                @endif
            </div>

        </div>
    </div>

</body>
</html>
