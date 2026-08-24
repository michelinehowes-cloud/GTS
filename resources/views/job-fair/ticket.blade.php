<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة المعرض - {{ $registration->graduate->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            background: linear-gradient(135deg, #0A1628 0%, #1E3A5F 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* ===== Ticket Card ===== */
        .ticket-wrapper {
            max-width: 480px;
            width: 100%;
        }

        .ticket {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            position: relative;
        }

        /* Header الملوّن */
        .ticket-header {
            background: linear-gradient(135deg, #0A1628 0%, #1E3A5F 60%, #0A2647 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0; right: 0;
            height: 30px;
            background: #fff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .ticket-header::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 30% 50%, rgba(245,158,11,0.15), transparent 60%);
        }

        .ticket-logo {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .ticket-event-name {
            font-size: 1.3rem;
            font-weight: 900;
            color: #F59E0B;
            margin-bottom: 0.2rem;
        }
        .ticket-university {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }

        /* Zigzag separator */
        .ticket-separator {
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: relative;
            margin: 0 -1px;
        }
        .ticket-separator::before,
        .ticket-separator::after {
            content: '';
            flex: 1;
            height: 1px;
            background: repeating-linear-gradient(90deg, #e2e8f0 0, #e2e8f0 6px, transparent 6px, transparent 12px);
        }
        .ticket-hole {
            width: 20px; height: 20px;
            background: linear-gradient(135deg, #0A1628, #1E3A5F);
            border-radius: 50%;
            margin: 0 -10px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .ticket-hole-right {
            background: linear-gradient(135deg, #0A1628, #1E3A5F);
        }

        /* Body */
        .ticket-body {
            padding: 1.5rem 2rem 2rem;
        }

        /* Reg Number Badge */
        .reg-number-badge {
            background: linear-gradient(135deg, #F59E0B, #F97316);
            color: #fff;
            padding: 6px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        /* Graduate info */
        .grad-name {
            font-size: 1.4rem;
            font-weight: 900;
            color: #0A1628;
            margin-bottom: 0.3rem;
        }
        .grad-info {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
            margin: 1.2rem 0;
        }
        .info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.8rem;
        }
        .info-label {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: 700;
            color: #0A1628;
            font-size: 0.9rem;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            padding: 1.5rem 0 0;
            border-top: 2px dashed #e2e8f0;
            margin-top: 1rem;
        }
        .qr-title {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 0.8rem;
        }
        #qr-code canvas, #qr-code img {
            border-radius: 12px;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .qr-code-text {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 0.5rem;
            font-family: monospace;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .status-registered { background: #dbeafe; color: #1d4ed8; }
        .status-attended { background: #d1fae5; color: #065f46; }

        /* Print Button */
        .action-buttons {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.5rem;
        }
        .btn-print {
            flex: 1;
            background: linear-gradient(135deg, #0A1628, #1E3A5F);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(10,22,40,0.4);
        }
        .btn-back {
            flex: 1;
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: 'Cairo', sans-serif;
        }
        .btn-back:hover {
            background: #e2e8f0;
            color: #334155;
        }

        @media print {
            body { background: white; padding: 0; }
            .ticket { box-shadow: none; }
            .action-buttons { display: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="ticket-wrapper">

    @if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-3 shadow-sm no-print">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="ticket">

        <!-- Header -->
        <div class="ticket-header">
            <div class="ticket-logo">🎓</div>
            <div class="ticket-event-name">{{ $registration->jobFair->title }}</div>
            <div class="ticket-university">جامعة طرابلس</div>
            <div class="mt-2" style="font-size: 0.85rem; color: rgba(255,255,255,0.7)">
                <i class="fas fa-calendar ms-2"></i>{{ $registration->jobFair->event_date->format('d/m/Y') }}
                &nbsp;&nbsp;
                <i class="fas fa-map-marker-alt ms-2"></i>{{ $registration->jobFair->location }}
            </div>
        </div>

        <!-- Separator -->
        <div class="ticket-separator">
            <div class="ticket-hole"></div>
            <div class="ticket-hole ticket-hole-right" style="margin-right: auto"></div>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <div class="text-center">
                <span class="reg-number-badge">{{ $registration->registration_number }}</span>
            </div>

            <div class="text-center mb-3">
                <div class="grad-name">{{ $registration->graduate->name }}</div>
                <div class="grad-info">
                    {{ $registration->graduate->major ?? 'غير محدد' }}
                    @if($registration->graduate->faculty)
                    — {{ $registration->graduate->faculty }}
                    @endif
                </div>
                <div class="mt-2">
                    <span class="status-badge {{ $registration->attended ? 'status-attended' : 'status-registered' }}">
                        <i class="fas {{ $registration->attended ? 'fa-check-circle' : 'fa-ticket-alt' }}"></i>
                        {{ $registration->attended ? 'حضر' : 'مسجل' }}
                    </span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">سنة التخرج</div>
                    <div class="info-value">{{ $registration->graduate->graduation_year ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">المعدل</div>
                    <div class="info-value">{{ $registration->graduate->gpa ? number_format($registration->graduate->gpa, 2) : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">تاريخ التسجيل</div>
                    <div class="info-value">{{ $registration->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الجامعة</div>
                    <div class="info-value" style="font-size: 0.78rem">{{ $registration->graduate->university ?? 'جامعة طرابلس' }}</div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-title">
                    <i class="fas fa-qrcode ms-1"></i>
                    امسح لتسجيل الحضور
                </div>
                <div id="qr-code" class="d-flex justify-content-center"></div>
                <div class="qr-code-text">{{ $registration->qr_code }}</div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print ms-2"></i>طباعة البطاقة
        </button>
        <a href="{{ route('job-fair.public') }}" class="btn-back">
            <i class="fas fa-arrow-right"></i>
            العودة للمعرض
        </a>
    </div>

</div>

<script>
// توليد QR Code
new QRCode(document.getElementById('qr-code'), {
    text: "{{ $registration->qr_code }}",
    width: 180,
    height: 180,
    colorDark: "#0A1628",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
</script>

</body>
</html>
