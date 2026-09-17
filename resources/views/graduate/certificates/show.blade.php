<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            background-image: url('{{ asset("images/certificate_template_a4_landscape_hd.png") }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.16), 0 2px 6px rgba(0,0,0,0.06);
            overflow: hidden;
            user-select: text;
        }

        /* Dynamic Field: Trainee Name (المتدرب/ة)
           Positioned above the first teal underline */
        .cert-field-recipient {
            position: absolute;
            top: 36.6%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 66%;
            text-align: center;
            font-family: 'Amiri', 'Cairo', serif;
            font-size: 3.5cqi;
            font-weight: 700;
            color: #1a365d;
            letter-spacing: 0.4px;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-shadow: 0 1px 1px rgba(26, 54, 93, 0.08);
            z-index: 10;
        }

        /* Dynamic Field: Course / Program Name (الدورة التدريبية)
           Positioned above the second teal underline */
        .cert-field-course {
            position: absolute;
            top: 51.4%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 72%;
            text-align: center;
            font-family: 'Cairo', 'Alexandria', sans-serif;
            font-size: 2.25cqi;
            font-weight: 700;
            color: #0f4c5c;
            letter-spacing: 0.2px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 10;
        }

        /* Dynamic Field: Official Digital Verification Strip (Bottom Left)
           Balanced opposite to the Director's signature on the right */
        .cert-field-verification {
            position: absolute;
            bottom: 6.8%;
            left: 7.2%;
            display: flex;
            align-items: center;
            gap: 1.2cqi;
            z-index: 10;
            direction: rtl;
        }

        .cert-qr-wrapper {
            position: relative;
            background: #ffffff;
            padding: 0.35cqi;
            border-radius: 0.6cqi;
            border: 1px solid rgba(13, 148, 136, 0.35);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cert-qr-wrapper img {
            width: 5.2cqi;
            height: 5.2cqi;
            display: block;
            border-radius: 0.3cqi;
        }

        .cert-meta-info {
            display: flex;
            flex-direction: column;
            gap: 0.35cqi;
            text-align: right;
            color: #334155;
        }

        .cert-meta-code {
            font-family: 'Alexandria', 'Cairo', monospace;
            font-size: 1.12cqi;
            font-weight: 700;
            color: #0f3a53;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.4cqi;
        }

        .cert-meta-code i {
            color: #0d9488;
            font-size: 1.0cqi;
        }

        .cert-meta-date {
            font-size: 0.98cqi;
            font-weight: 600;
            color: #64748b;
        }

        .cert-meta-hours {
            font-size: 0.92cqi;
            font-weight: 700;
            color: #0d9488;
            background: rgba(13, 148, 136, 0.08);
            padding: 0.1cqi 0.6cqi;
            border-radius: 50px;
            display: inline-block;
            width: fit-content;
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

            {{-- 1. Trainee Name (المتدرب/ة) --}}
            <div class="cert-field-recipient" title="{{ $certificate->recipient_name }}">
                {{ $certificate->recipient_name }}
            </div>

            {{-- 2. Course Name (الدورة التدريبية) --}}
            @php
                // Clean leading redundant prefix like "شهادة إتمام" or "شهادة مشاركة في" so it fits naturally into "قد اجتاز الدورة التدريبية بنجاح بكافة متطلباتها: « ... »"
                $cleanTitle = preg_replace('/^(شهادة إتمام\s*|شهادة مشاركة في\s*|شهادة\s*)/u', '', $certificate->title);
                $cleanTitle = trim($cleanTitle);
            @endphp
            <div class="cert-field-course" title="{{ $cleanTitle }}">
                « {{ $cleanTitle }} »
            </div>

            {{-- 3. Digital Verification Block (Bottom Left) --}}
            <div class="cert-field-verification">
                <div class="cert-qr-wrapper">
                    @php
                        $verifyUrl = route('certificates.verify', $certificate->certificate_code);
                        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($verifyUrl);
                    @endphp
                    <img src="{{ $qrApiUrl }}" alt="QR Code" onerror="this.src='https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl={{ urlencode($verifyUrl) }}&choe=UTF-8'">
                </div>
                <div class="cert-meta-info">
                    <div class="cert-meta-code">
                        <i class="fas fa-shield-check"></i>
                        <span>{{ $certificate->certificate_code }}</span>
                    </div>
                    @if($certificate->issue_date)
                    <div class="cert-meta-date">
                        تاريخ الإصدار: {{ $certificate->issue_date->locale('ar')->translatedFormat('d F Y') }}
                    </div>
                    @endif
                    @if($certificate->hours)
                    <div class="cert-meta-hours">
                        {{ $certificate->hours }} ساعة تدريبية معتمدة
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</body>
</html>
