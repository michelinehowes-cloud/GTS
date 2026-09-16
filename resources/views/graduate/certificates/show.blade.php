<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة معتمدة — {{ $certificate->recipient_name }} — {{ $certificate->certificate_code }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@400;600;700;800;900&family=Reem+Kufi:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --gold: #b45309;
            --gold-light: #fef3c7;
            --gold-border: #d97706;
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
            background: #f1f5f9;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* Top Action Bar */
        .action-bar {
            width: 100%;
            max-width: 1100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 50px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
        }

        .btn-outline {
            background: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        /* ═════════════════════════════════════════════
           OFFICIAL ROYAL CERTIFICATE FRAME (A4 Landscape)
        ═════════════════════════════════════════════ */
        .cert-container {
            width: 100%;
            max-width: 1100px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
            padding: 24px;
            position: relative;
        }

        .cert-outer-border {
            border: 4px solid #b45309;
            padding: 8px;
            position: relative;
            background: #fffdfa;
        }

        .cert-inner-border {
            border: 1.5px solid #d97706;
            padding: 32px 40px;
            position: relative;
            background: #ffffff;
            background-image: 
                radial-gradient(#b45309 0.75px, transparent 0.75px),
                radial-gradient(#b45309 0.75px, #ffffff 0.75px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            background-opacity: 0.02;
        }

        /* Decorative Corner Ornaments */
        .corner {
            position: absolute;
            width: 45px;
            height: 45px;
            border: 3px solid #b45309;
            z-index: 2;
        }
        .corner-tl { top: -6px; left: -6px; border-right: none; border-bottom: none; }
        .corner-tr { top: -6px; right: -6px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: -6px; left: -6px; border-right: none; border-top: none; }
        .corner-br { bottom: -6px; right: -6px; border-left: none; border-top: none; }

        /* Watermark Background */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            height: 320px;
            opacity: 0.04;
            pointer-events: none;
            z-index: 1;
        }
        .cert-watermark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .cert-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        /* Header (Logos & University Title) */
        .cert-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid rgba(180, 83, 9, 0.2);
        }

        .header-col {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .header-titles {
            text-align: right;
        }
        .header-titles .country {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
        }
        .header-titles .univ {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e3a8a;
            line-height: 1.2;
        }
        .header-titles .office {
            font-size: 0.9rem;
            font-weight: 700;
            color: #b45309;
        }

        .center-emblem {
            text-align: center;
        }
        .center-emblem .emblem-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(180, 83, 9, 0.1), rgba(217, 119, 6, 0.15));
            color: #b45309;
            border: 1px solid rgba(180, 83, 9, 0.3);
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 800;
        }

        /* Partner Header (if Cooperative) */
        .partner-box {
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .partner-box .partner-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
            border-radius: 10px;
            padding: 4px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        /* Certificate Title Section */
        .cert-main-title-box {
            margin: 10px 0 20px;
        }
        .cert-main-title {
            font-family: 'Amiri', serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #1e3a8a;
            letter-spacing: -0.5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 4px;
        }
        .cert-subtitle {
            font-size: 1.1rem;
            font-weight: 700;
            color: #b45309;
            letter-spacing: 0.5px;
        }

        /* Body Statement */
        .cert-statement {
            font-size: 1.15rem;
            color: #334155;
            margin-bottom: 12px;
            line-height: 1.8;
        }

        .cert-recipient {
            font-family: 'Amiri', serif;
            font-size: 2.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 10px auto 16px;
            padding: 4px 40px;
            display: inline-block;
            border-bottom: 2px solid #b45309;
            letter-spacing: 0.5px;
        }

        .cert-details {
            font-size: 1.1rem;
            color: #334155;
            max-width: 820px;
            margin: 0 auto 24px;
            line-height: 1.9;
        }
        .cert-details strong {
            color: #1e3a8a;
            font-weight: 800;
        }

        /* Info Pills Bar */
        .cert-pills {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }
        .cert-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.9rem;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .cert-pill strong {
            color: #0f172a;
        }

        /* Signatures & Seal Section */
        .cert-footer {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: flex-end;
            margin-top: 15px;
            padding-top: 20px;
        }

        .sig-box {
            text-align: center;
        }
        .sig-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
        }
        .sig-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .sig-subtitle {
            font-size: 0.8rem;
            color: #64748b;
        }
        .sig-line {
            width: 160px;
            height: 1px;
            background: #cbd5e1;
            margin: 10px auto;
        }

        /* Official Gold Seal */
        .official-seal {
            text-align: center;
            padding: 0 20px;
        }
        .seal-circle {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: radial-gradient(circle, #fef3c7 0%, #fde68a 60%, #f59e0b 100%);
            border: 3px double #b45309;
            box-shadow: 0 4px 15px rgba(180, 83, 9, 0.3);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #78350f;
            margin: 0 auto;
            position: relative;
        }
        .seal-circle i {
            font-size: 1.8rem;
            margin-bottom: 2px;
        }
        .seal-circle span {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        /* QR Code & Verification Line */
        .cert-verify-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px dashed rgba(180, 83, 9, 0.3);
            font-size: 0.78rem;
            color: #64748b;
        }
        .qr-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qr-wrapper img {
            width: 55px;
            height: 55px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 2px;
            background: white;
        }

        /* ═════════════════════════════════════════════
           PRINT MEDIA STYLES (A4 Landscape PDF Export)
        ═════════════════════════════════════════════ */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .action-bar {
                display: none !important;
            }

            .cert-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                page-break-inside: avoid;
            }

            .cert-outer-border {
                border-width: 4px !important;
            }

            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar (Hidden on Print) --}}
    <div class="action-bar">
        <a href="{{ route('graduate.certificates.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لسجل الشهادات</span>
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('certificates.verify', $certificate->certificate_code) }}" target="_blank" class="btn btn-outline">
                <i class="fas fa-shield-alt text-primary"></i>
                <span>فحص كود التحقق</span>
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                <span>طباعة / حفظ الشهادة PDF</span>
            </button>
        </div>
    </div>

    {{-- Main Official Certificate Document --}}
    <div class="cert-container" id="certificateDoc">
        <div class="cert-outer-border">
            {{-- Corners --}}
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <div class="cert-inner-border">
                {{-- Background Watermark --}}
                <div class="cert-watermark">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Watermark">
                </div>

                <div class="cert-content">
                    {{-- HEADER --}}
                    <div class="cert-header">
                        {{-- University & Office Titles (Right) --}}
                        <div class="header-col">
                            <img src="{{ asset('images/logo.jpg') }}" alt="شعار مكتب تدريب الخريجين" class="logo-img" onerror="this.style.display='none'">
                            <div class="header-titles">
                                <div class="country">دولة ليبيا — وزارة التعليم العالي والبحث العلمي</div>
                                <div class="univ">جامعة طرابلس</div>
                                <div class="office">مكتب تدريب وتوظيف الخريجين</div>
                            </div>
                        </div>

                        {{-- Center Type Badge --}}
                        <div class="center-emblem">
                            <div class="emblem-badge">
                                @if($certificate->type == 'cooperative_attendance')
                                    <i class="fas fa-handshake"></i>
                                    <span>شهادة تدريب تعاوني مشترك</span>
                                @elseif($certificate->type == 'workshop_attendance')
                                    <i class="fas fa-lightbulb"></i>
                                    <span>شهادة حضور ورشة عمل</span>
                                @else
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>شهادة إتمام برنامج تدريبي</span>
                                @endif
                            </div>
                        </div>

                        {{-- Left Header: Partner Company Logo (if cooperative) OR University Emblem --}}
                        <div class="header-col" style="justify-content: flex-end;">
                            @if($certificate->has_company_collaboration)
                                <div class="partner-box">
                                    <div class="text-start" style="text-align: left;">
                                        <div class="country" style="font-size: 0.75rem;">بالشراكة الاستراتيجية مع</div>
                                        <strong style="color: #059669; font-size: 0.95rem;">{{ $certificate->company_name }}</strong>
                                    </div>
                                    @if($certificate->company_logo)
                                        <img src="{{ Storage::url($certificate->company_logo) }}" alt="{{ $certificate->company_name }}" class="partner-logo">
                                    @else
                                        <div class="partner-logo d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building fa-2x text-success"></i>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <img src="{{ asset('images/uni_logo_white.png') }}" alt="شعار جامعة طرابلس" class="logo-img" style="filter: brightness(0.2);" onerror="this.style.display='none'">
                            @endif
                        </div>
                    </div>

                    {{-- CERTIFICATE TITLE --}}
                    <div class="cert-main-title-box">
                        <h1 class="cert-main-title">شـــهادة حـضــــور</h1>
                        <div class="cert-subtitle">CERTIFICATE OF ATTENDANCE & COMPLETION</div>
                    </div>

                    {{-- STATEMENT --}}
                    <p class="cert-statement">
                        يشهد مكتب تدريب وتوظيف الخريجين بجامعة طرابلس
                        @if($certificate->has_company_collaboration)
                            بالتعاون المشترك والشراكة الميدانية مع <strong>{{ $certificate->company_name }}</strong>
                        @endif
                        بأن المتدرب/ـة:
                    </p>

                    {{-- RECIPIENT NAME --}}
                    <div class="cert-recipient">
                        {{ $certificate->recipient_name }}
                    </div>

                    {{-- PROGRAM DETAILS --}}
                    <p class="cert-details">
                        قد شاركـ/ـت وأتمـ/ـت بنجاح كافة متطلبات
                        @if($certificate->type == 'cooperative_attendance')
                            <strong>البرنامج التدريبي التعاوني المشترك:</strong>
                        @elseif($certificate->type == 'workshop_attendance')
                            <strong>ورشة العمل التخصصية:</strong>
                        @else
                            <strong>البرنامج التدريبي المكثف:</strong>
                        @endif
                        <br>
                        <strong style="font-size: 1.35rem; color: #1e3a8a;">« {{ $certificate->title }} »</strong>
                        @if($certificate->notes)
                            <br><small class="text-muted" style="font-size: 0.9rem;">{{ $certificate->notes }}</small>
                        @endif
                    </p>

                    {{-- INFO PILLS --}}
                    <div class="cert-pills">
                        <div class="cert-pill">
                            <i class="far fa-clock text-warning"></i>
                            <span>الساعات المعتمدة:</span>
                            <strong>{{ $certificate->hours }} ساعة تدريبية</strong>
                        </div>

                        @if($certificate->start_date && $certificate->end_date)
                        <div class="cert-pill">
                            <i class="far fa-calendar-alt text-primary"></i>
                            <span>الفترة:</span>
                            <strong>من {{ $certificate->start_date->format('d/m/Y') }} إلى {{ $certificate->end_date->format('d/m/Y') }}</strong>
                        </div>
                        @endif

                        <div class="cert-pill">
                            <i class="far fa-calendar-check text-success"></i>
                            <span>تاريخ التحرير:</span>
                            <strong>{{ $certificate->issue_date->locale('ar')->translatedFormat('j F Y') }}</strong>
                        </div>

                        @if($certificate->instructor_name)
                        <div class="cert-pill">
                            <i class="fas fa-user-check text-info"></i>
                            <span>المدرب / المشرف:</span>
                            <strong>{{ $certificate->instructor_name }}</strong>
                        </div>
                        @endif
                    </div>

                    {{-- FOOTER / SIGNATURES & OFFICIAL SEAL --}}
                    <div class="cert-footer">
                        {{-- Signature 1: Training Coordinator --}}
                        <div class="sig-box">
                            <div class="sig-title">منسق البرامج التدريبية</div>
                            <div class="sig-name">{{ $certificate->instructor_name ?? 'أ.د. عبد الباسط البكوش' }}</div>
                            <div class="sig-line"></div>
                            <div class="sig-subtitle">مكتب تدريب الخريجين</div>
                        </div>

                        {{-- Center: Official University Seal --}}
                        <div class="official-seal">
                            <div class="seal-circle">
                                <i class="fas fa-stamp"></i>
                                <span>ختم الاعتماد الرسمي</span>
                                <span style="font-size: 0.55rem; opacity: 0.8;">OFFICIAL SEAL</span>
                            </div>
                        </div>

                        {{-- Signature 2: Office Director / Partner Representative --}}
                        <div class="sig-box">
                            @if($certificate->has_company_collaboration)
                                <div class="sig-title">عن شركة {{ $certificate->company_name }}</div>
                                <div class="sig-name">مدير إدارة الموارد البشرية والتدريب</div>
                                <div class="sig-line"></div>
                                <div class="sig-subtitle">الشريك الميداني للبرنامج</div>
                            @else
                                <div class="sig-title">مدير مكتب تدريب وتوظيف الخريجين</div>
                                <div class="sig-name">د. عادل محمد الصويعي</div>
                                <div class="sig-line"></div>
                                <div class="sig-subtitle">جامعة طرابلس</div>
                            @endif
                        </div>
                    </div>

                    {{-- VERIFICATION STRIP --}}
                    <div class="cert-verify-strip">
                        <div class="qr-wrapper">
                            @php
                                $verifyUrl = route('certificates.verify', $certificate->certificate_code);
                                $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);
                            @endphp
                            <img src="{{ $qrApiUrl }}" alt="QR Code">
                            <div style="text-align: right;">
                                <div><strong>رمز التحقق الرقمي:</strong> {{ $certificate->certificate_code }}</div>
                                <div style="font-size: 0.7rem;">امسح رمز QR للتحقق من صحة واعتماد الشهادة عبر المنصة الرسمية</div>
                            </div>
                        </div>

                        <div style="text-align: left;">
                            <div>مكتب تدريب وتوظيف الخريجين — جامعة طرابلس</div>
                            <div style="font-size: 0.7rem;">صادرة عبر النظام الإلكتروني الموحد لإدارة التدريب والتوظيف</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
