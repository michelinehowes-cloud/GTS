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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; }
        body {
            background: linear-gradient(135deg, #045db0 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* ─── Control Bar ─── */
        .ctrl-bar {
            display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; justify-content: center; z-index: 10;
        }
        .ctrl-btn {
            display: inline-flex; align-items: center; gap: 8px; padding: 12px 25px; border-radius: 50px;
            font-size: 1rem; font-weight: 800; cursor: pointer; border: none; text-decoration: none; transition: all 0.3s;
        }
        .ctrl-btn-primary { background: linear-gradient(135deg, #045db0, #3b82f6); color: white; box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4); }
        .ctrl-btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(30, 58, 138, 0.5); color: white; }
        
        .ctrl-btn-secondary { background: rgba(255,255,255,0.7); color: #045db0; border: 1.5px solid rgba(255,255,255,0.9); backdrop-filter: blur(8px); }
        .ctrl-btn-secondary:hover { background: white; color: #045db0; transform: translateY(-3px); }

        #saveMsg { color: #045db0; font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: none; background: white; padding: 10px 20px; border-radius: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}

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
            background: #045db0;
            color: white;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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
            margin-bottom: 0.5rem;
        }
        .ticket-logo img {
            height: 55px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            background: white;
            padding: 5px;
            object-fit: contain;
        }
        .ticket-event-name {
            font-size: 1.5rem;
            font-weight: 900;
            margin-bottom: 0.2rem;
            color: #FDE68A;
        }
        .ticket-university {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
        }

        /* Hole punches */
        .ticket-separator {
            height: 0;
            position: relative;
            z-index: 10;
            display: flex;
        }
        .ticket-hole {
            width: 20px; height: 20px;
            background: #f1f5f9;
            border-radius: 50%;
            margin: 0 -10px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .ticket-hole-right {
            background: #045db0;
        }

        /* Body */
        .ticket-body {
            padding: 2rem;
            text-align: right;
        }

        /* Reg Number Badge */
        .reg-number-badge {
            background: linear-gradient(135deg, #eeca3e, #F97316);
            color: #fff;
            padding: 6px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        /* Graduate info */
        .grad-name {
            font-size: 1.4rem;
            font-weight: 900;
            color: #045db0;
            margin-bottom: 0.3rem;
        }
        .grad-info {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
            margin: 1.5rem 0;
        }
        .info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.8rem;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: 700;
            color: #045db0;
            font-size: 0.95rem;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            padding: 1.5rem 0 0;
            border-top: 2px dashed #e2e8f0;
            margin-top: 1rem;
        }
        .qr-title {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 0.8rem;
        }
        #qr-code canvas, #qr-code img {
            border-radius: 12px;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        .qr-code-text {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.5rem;
            font-family: monospace;
            letter-spacing: 1px;
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

        /* Loading overlay */
        #saveOverlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px);
            z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center; color: white; gap: 1rem;
        }
        .spinner {
            width: 60px; height: 60px; border: 5px solid rgba(255, 255, 255, 0.2); border-top-color: #3b82f6; border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media print {
            body { background: white; padding: 0; }
            .ctrl-bar, #saveMsg, #saveOverlay { display: none !important; }
            .ticket-wrapper { box-shadow: none; max-width: 100%; border: none; }
            .ticket { box-shadow: none; border: 2px solid #e2e8f0; border-radius: 0; }
            .ticket-hole { display: none; }
            @page { size: A6 portrait; margin: 0; }
        }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div id="saveOverlay">
    <div class="spinner"></div>
    <div style="font-weight: 800; font-size: 1.2rem">جاري إعداد البطاقة بدقة عالية...</div>
</div>

<!-- Controls -->
<div class="ctrl-bar no-print">
    <button class="ctrl-btn ctrl-btn-primary" onclick="saveBadge()">
        <i class="fas fa-download"></i> حفظ كصورة (PNG)
    </button>
    <button class="ctrl-btn ctrl-btn-secondary" onclick="window.print()">
        <i class="fas fa-print"></i> طباعة البطاقة
    </button>
    <a href="{{ url()->previous() }}" class="ctrl-btn ctrl-btn-secondary">
        <i class="fas fa-arrow-right"></i> العودة
    </a>
</div>

<div id="saveMsg"><i class="fas fa-check-circle"></i> تم الحفظ بنجاح! تحقق من مجلد التنزيلات.</div>

<div class="ticket-wrapper">
    <div class="ticket" id="badge-card">

        <!-- Header -->
        <div class="ticket-header">
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2 ticket-logo flex-wrap">
                <img src="{{ $registration->jobFair->white_logo_url }}" alt="{{ $registration->jobFair->title }}" style="height: 46px; width: auto; max-width: 130px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_white.png') }}';">
                <img src="{{ asset('storage/logo.jpg') }}" alt="شعار مكتب الخريجين" style="height: 48px; width: auto;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                <div class="px-2 py-1 rounded-3 d-flex align-items-center" style="height: 44px; background: rgba(255,255,255,0.12); border: 1px solid rgba(245,158,11,0.4);" title="الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض والمؤتمرات">
                    <img src="{{ asset('images/wahaexpo_horizontal_gold.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" style="height: 36px; width: auto; max-width: 125px; object-fit: contain;" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}'">
                </div>
            </div>
            <div class="ticket-event-name">{{ $registration->jobFair->title }}</div>
            <div class="ticket-university">مكتب تدريب وتأهيل الخريجين — جامعة طرابلس</div>
            <div class="mt-1 d-inline-flex align-items-center gap-1.5 px-3 py-0.5 rounded-pill" style="background: rgba(255,255,255,0.15); font-size: 0.76rem; color: #ffffff;">
                <i class="fas fa-crown text-warning"></i>
                <span>الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض والمؤتمرات</span>
            </div>
            <div class="mt-2" style="font-size: 0.85rem; color: rgba(255,255,255,0.85)">
                @if($registration->jobFair->event_date)
                    <i class="fas fa-calendar ms-2 text-warning"></i>{{ $registration->jobFair->event_date->format('d/m/Y') }}
                    &nbsp;&nbsp;
                @endif
                <i class="fas fa-map-marker-alt ms-2 text-danger"></i>{{ $registration->jobFair->location ?? 'جامعة طرابلس' }}
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
                        {{ $registration->attended ? 'تم الحضور' : 'مسجل ومعتمد' }}
                    </span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">سنة التخرج</div>
                    <div class="info-value">{{ $registration->graduate->graduation_year ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">المعدل التراكمي</div>
                    <div class="info-value">{{ $registration->graduate->gpa ? number_format($registration->graduate->gpa, 2) : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">تاريخ التسجيل</div>
                    <div class="info-value">{{ $registration->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الجامعة</div>
                    <div class="info-value" style="font-size: 0.85rem">{{ $registration->graduate->university ?? 'جامعة طرابلس' }}</div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-title">
                    <i class="fas fa-qrcode ms-1 text-warning"></i>
                    امسح الرمز لتسجيل الحضور وتأكيد الهوية
                </div>
                <div id="qr-code" class="d-flex justify-content-center"></div>
                <div class="qr-code-text">{{ $registration->qr_code }}</div>
            </div>
        </div>
    </div>
</div>

<script>
// توليد QR Code
new QRCode(document.getElementById('qr-code'), {
    text: "{{ $registration->qr_code }}",
    width: 180,
    height: 180,
    colorDark: "#045db0",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

// دالة حفظ البطاقة كصورة
function saveBadge() {
    const card = document.getElementById('badge-card');
    const overlay = document.getElementById('saveOverlay');
    const msg = document.getElementById('saveMsg');
    
    overlay.style.display = 'flex';
    msg.style.display = 'none';

    setTimeout(() => {
        html2canvas(card, {
            scale: 4, // دقة فائقة للطباعة
            useCORS: true,
            backgroundColor: null,
            logging: false,
        }).then(canvas => {
            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.download = 'Ticket_{{ $registration->registration_number }}.png';
                link.href = url;
                link.click();
                
                setTimeout(() => URL.revokeObjectURL(url), 100);
                
                overlay.style.display = 'none';
                msg.style.display = 'block';
                setTimeout(() => msg.style.display = 'none', 5000);
            }, 'image/png');
            
        }).catch(err => {
            console.error(err);
            overlay.style.display = 'none';
            alert('حدث خطأ أثناء حفظ البطاقة. يرجى المحاولة مرة أخرى.');
        });
    }, 300);
}
</script>

</body>
</html>
