<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكرة زائر المعرض - {{ $visitor->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #091a32 0%, #03488a 50%, #091a32 100%);
            min-height: 100vh;
            margin: 0;
            padding: 30px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #1e293b;
        }

        .ticket-wrapper {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }

        .ticket {
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            position: relative;
        }

        /* Ticket Header */
        .ticket-header {
            background: linear-gradient(145deg, #03488a, #0d2444);
            color: white;
            padding: 2.2rem 1.8rem 2.8rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0; right: 0;
            height: 32px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .ticket-header-bg-glow {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%);
            border-radius: 50%;
        }

        .ticket-badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fde68a;
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
        }

        .ticket-event-name {
            font-size: 1.35rem;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 0.3rem;
            line-height: 1.3;
        }
        .ticket-university {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
        }

        /* Zigzag Separation */
        .ticket-separator {
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: relative;
            margin-top: -12px;
            z-index: 5;
        }
        .ticket-separator::before,
        .ticket-separator::after {
            content: '';
            flex: 1;
            height: 2px;
            background: repeating-linear-gradient(90deg, #cbd5e1 0, #cbd5e1 6px, transparent 6px, transparent 12px);
        }
        .ticket-hole {
            width: 26px; height: 26px;
            background: #03488a;
            border-radius: 50%;
            margin: 0 -13px;
            flex-shrink: 0;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.3);
        }

        /* Ticket Body */
        .ticket-body {
            padding: 1.5rem 2rem 2.2rem;
            text-align: center;
        }

        .ticket-code-pill {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            font-family: monospace;
            padding: 6px 22px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.1rem;
            letter-spacing: 1.5px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(16,185,129,0.35);
            margin-bottom: 1rem;
        }

        .visitor-title {
            font-size: 1.45rem;
            font-weight: 900;
            color: #0d2444;
            margin-bottom: 0.4rem;
        }

        .visitor-role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        /* Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
            text-align: right;
            margin-bottom: 1.5rem;
        }
        .info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.85rem;
        }
        .info-label {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .info-value {
            font-weight: 800;
            color: #0f172a;
            font-size: 0.88rem;
            word-break: break-word;
        }

        /* QR Section */
        .qr-section {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 1.5rem 1rem;
            margin-top: 1rem;
            border: 2px dashed #cbd5e1;
        }
        .qr-box {
            display: inline-flex;
            padding: 12px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            margin-bottom: 0.8rem;
        }
        .qr-hint {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
            font-weight: 600;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 1.8rem;
            justify-content: center;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.92rem;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }
        .btn-print {
            background: #ffffff;
            color: #0d2444;
            box-shadow: 0 4px 15px rgba(255,255,255,0.25);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            background: #f8fafc;
            color: #045db0;
        }
        .btn-share {
            background: #25D366;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(37,211,102,0.35);
        }
        .btn-share:hover {
            transform: translateY(-2px);
            color: #ffffff;
            filter: brightness(1.05);
        }
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            color: #ffffff;
        }

        /* Print Mode */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
            }
            .action-buttons, .back-nav {
                display: none !important;
            }
            .ticket-wrapper {
                max-width: 100% !important;
            }
            .ticket {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }
            .ticket-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .ticket-code-pill {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 text-center fw-bold" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
        @elseif(session('info'))
        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-3 text-center fw-bold" role="alert">
            <i class="fas fa-info-circle me-1"></i> {{ session('info') }}
        </div>
        @endif

        {{-- Ticket Card --}}
        <div class="ticket" id="visitorTicketCard">
            {{-- Header --}}
            <div class="ticket-header">
                <div class="ticket-header-bg-glow"></div>
                
                <div class="ticket-badge-tag">
                    <i class="fas fa-id-card"></i>
                    <span>بطاقة دخول زائر معتمدة</span>
                </div>

                <div class="ticket-event-name">
                    {{ $fair->title ?? 'ملتقى ومعرض التوظيف ومشاريع التخرج' }}
                </div>
                <div class="ticket-university">
                    جامعة طرابلس — مكتب تدريب وتوظيف الخريجين
                </div>
            </div>

            {{-- Separator with Holes --}}
            <div class="ticket-separator">
                <div class="ticket-hole"></div>
                <div class="ticket-hole ms-auto"></div>
            </div>

            {{-- Body --}}
            <div class="ticket-body">
                {{-- Ticket Number --}}
                <div class="ticket-code-pill">
                    {{ $visitor->ticket_number }}
                </div>

                {{-- Visitor Name --}}
                <h2 class="visitor-title">{{ $visitor->name }}</h2>

                {{-- Visitor Type Badge --}}
                @php
                    $badgeStyles = [
                        'job_seeker' => 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;',
                        'student' => 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;',
                        'company_rep' => 'background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff;',
                        'academic' => 'background: #fffbeb; color: #b45309; border: 1px solid #fde68a;',
                        'parent' => 'background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8;',
                        'general' => 'background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;',
                    ];
                    $badgeIcons = [
                        'job_seeker' => 'fa-briefcase',
                        'student' => 'fa-graduation-cap',
                        'company_rep' => 'fa-building',
                        'academic' => 'fa-chalkboard-teacher',
                        'parent' => 'fa-users',
                        'general' => 'fa-star',
                    ];
                    $style = $badgeStyles[$visitor->visitor_type] ?? $badgeStyles['general'];
                    $icon = $badgeIcons[$visitor->visitor_type] ?? 'fa-user';
                @endphp
                <div class="visitor-role-pill" style="{{ $style }}">
                    <i class="fas {{ $icon }}"></i>
                    <span>{{ $visitor->visitor_type_label }}</span>
                </div>

                {{-- Info Grid --}}
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-calendar-day text-primary"></i>تاريخ المعرض</div>
                        <div class="info-value">
                            {{ $fair->event_date ? $fair->event_date->translatedFormat('d F Y') : 'محدد قريباً' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-clock text-primary"></i>التوقيت</div>
                        <div class="info-value">
                            {{ $fair->start_time ? substr($fair->start_time, 0, 5) : '09:00' }}
                            @if($fair->end_time) - {{ substr($fair->end_time, 0, 5) }} @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-map-marker-alt text-danger"></i>المكان</div>
                        <div class="info-value">{{ $fair->location ?? 'جامعة طرابلس - الحرم الجامعي' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone text-success"></i>رقم التواصل</div>
                        <div class="info-value" dir="ltr">{{ $visitor->phone }}</div>
                    </div>
                    @if($visitor->organization)
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-university text-warning"></i>الجهة / الكلية</div>
                        <div class="info-value">{{ $visitor->organization }}</div>
                    </div>
                    @endif
                    @if($visitor->specialization)
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-laptop-code text-info"></i>التخصص</div>
                        <div class="info-value">{{ $visitor->specialization }}</div>
                    </div>
                    @endif
                </div>

                {{-- Status --}}
                @if($visitor->attended)
                <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
                    <i class="fas fa-check-circle me-1"></i> تم تسجيل الدخول في المعرض
                    @if($visitor->check_in_at)
                        <small class="d-block text-muted mt-1">الساعة: {{ \Carbon\Carbon::parse($visitor->check_in_at)->format('H:i') }}</small>
                    @endif
                </div>
                @else
                <div class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill mb-3 fw-bold">
                    <i class="fas fa-clock me-1"></i> تذكرة نشطة — بانتظار التحقق عند البوابة
                </div>
                @endif

                {{-- QR Code Section --}}
                <div class="qr-section">
                    <div class="qr-box">
                        <div id="qrcode"></div>
                    </div>
                    <p class="qr-hint">
                        <i class="fas fa-qrcode me-1"></i>
                        يرجى إبراز هذا الرمز لموظفي الاستقبال عند بوابات الدخول
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <button onclick="window.print()" class="action-btn btn-print">
                <i class="fas fa-print"></i>
                <span>طباعة البطاقة</span>
            </button>
            <a href="https://api.whatsapp.com/send?text={{ urlencode('بطاقة حضوري لمعرض التوظيف بجامعة طرابلس: ' . route('job-fair.visitor.ticket', $visitor->ticket_number)) }}" target="_blank" class="action-btn btn-share">
                <i class="fab fa-whatsapp"></i>
                <span>مشاركة بالواتساب</span>
            </a>
            <a href="{{ route('job-fair.public', $fair->id) }}" class="action-btn btn-back">
                <i class="fas fa-arrow-right"></i>
                <span>صفحة المعرض</span>
            </a>
        </div>
    </div>

    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}",
            width: 140,
            height: 140,
            colorDark : "#0d2444",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
