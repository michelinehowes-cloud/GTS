<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة زائر المعرض - {{ $visitor->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        * { font-family: 'Cairo', sans-serif; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #045db0 0%, #e2e8f0 100%);
            min-height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        /* ===== Ticket Card Wrapper ===== */
        .ticket-wrapper {
            max-width: 490px;
            width: 100%;
            padding: 15px;
            transform-origin: center center;
            margin: auto;
        }

        .ticket {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            position: relative;
        }

        /* Header الملوّن */
        .ticket-header {
            background: #045db0;
            color: white;
            padding: 1.8rem 1.5rem 2.2rem;
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
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .ticket-header::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 30% 50%, rgba(245,158,11,0.18), transparent 60%);
        }

        .ticket-logo {
            margin-bottom: 0.6rem;
        }
        .ticket-event-name {
            font-size: 1.35rem;
            font-weight: 900;
            color: #FDE68A;
            margin-bottom: 0.2rem;
            margin-top: 0.3rem;
            line-height: 1.3;
        }
        .ticket-university {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
            font-weight: 600;
        }
        .ticket-organizer-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            padding: 3px 12px;
            border-radius: 50px;
            font-size: 0.76rem;
            color: #ffffff;
            margin-top: 4px;
        }

        /* Zigzag separator */
        .ticket-separator {
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: relative;
            margin: 0 -1px;
            z-index: 5;
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
            background: linear-gradient(135deg, #045db0, #3b82f6);
            border-radius: 50%;
            margin: 0 -10px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .ticket-hole-right {
            background: linear-gradient(135deg, #045db0, #3b82f6);
        }

        /* Body */
        .ticket-body {
            padding: 1.4rem 1.8rem 2rem;
        }

        /* Reg Number Badge */
        .reg-number-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            padding: 6px 22px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 1.2px;
            font-family: monospace;
            display: inline-block;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
        }

        /* Visitor info */
        .grad-name {
            font-size: 1.4rem;
            font-weight: 900;
            color: #045db0;
            margin-bottom: 0.3rem;
        }
        .grad-info {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 18px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
        }
        .status-registered { background: #dbeafe; color: #1d4ed8; }
        .status-attended { background: #d1fae5; color: #065f46; }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin: 1.2rem 0;
        }
        .info-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.75rem 0.85rem;
            border: 1px solid #f1f5f9;
        }
        .info-label {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .info-value {
            font-weight: 800;
            color: #045db0;
            font-size: 0.88rem;
            word-break: break-word;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            padding: 1.2rem 0 0;
            border-top: 2px dashed #e2e8f0;
            margin-top: 0.8rem;
        }
        .qr-title {
            font-size: 0.78rem;
            color: #94a3b8;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 0.8rem;
        }
        #qr-code canvas, #qr-code img {
            border-radius: 12px;
            padding: 8px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.2rem;
        }
        .btn-print {
            flex: 1;
            background: linear-gradient(135deg, #045db0, #3b82f6);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30,58,138,0.4);
            color: white;
        }
        .btn-back {
            flex: 1;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
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
            background: #f1f5f9;
            color: #334155;
        }

        /* Toast notification for auto-save */
        .auto-save-toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.92);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.15);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        @media print {
            .no-print, .auto-save-toast { display: none !important; }
            body { background: none !important; padding: 0 !important; }
            .ticket-wrapper { max-width: 100% !important; padding: 0 !important; }
            .ticket { box-shadow: none !important; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

    {{-- Toast Auto-Save Indicator --}}
    <div id="autoSaveToast" class="auto-save-toast d-none">
        <i class="fas fa-check-circle text-success fs-5"></i>
        <span>جاري حفظ نسختك الرقمية تلقائياً...</span>
    </div>

<div class="ticket-wrapper">

    @if(session('success'))
    <div class="alert alert-success border-0 rounded-3 mb-3 shadow-sm no-print text-center fw-bold">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
    @elseif(session('info'))
    <div class="alert alert-info border-0 rounded-3 mb-3 shadow-sm no-print text-center fw-bold">
        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
    </div>
    @endif

    <div class="ticket" id="visitorTicketCard">

        <!-- Header -->
        <div class="ticket-header">
            <!-- شعارات الشركاء: المعرض، الجامعة، وشركة الواحة لتنظيم المعارض -->
            <div class="d-flex justify-content-center align-items-center gap-3 mb-2 ticket-logo flex-wrap">
                <img src="{{ $fair->white_logo_url }}" alt="{{ $fair->title }}" style="height: 48px; width: auto; max-width: 130px; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_white.png') }}';">
                <img src="{{ asset('images/office_logo_white.png') }}" alt="مكتب تدريب وتأهيل الخريجين بجامعة طرابلس" style="height: 52px; width: auto;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                <div class="bg-white p-1 rounded-3 shadow-sm d-flex align-items-center" style="height: 46px;" title="تنظيم: شركة الواحة للمعارض">
                    <img src="{{ asset('images/wahaexpo_logo.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" style="height: 38px; width: auto; max-width: 120px; object-fit: contain;">
                </div>
            </div>

            <div class="ticket-event-name">{{ $fair->title ?? 'ملتقى ومعرض التوظيف السنوي' }}</div>
            <div class="ticket-university">مكتب تدريب وتأهيل الخريجين — جامعة طرابلس</div>
            <div class="ticket-organizer-badge">
                <i class="fas fa-crown text-warning"></i>
                <span>الراعي الاستراتيجي: شركة الواحة لتنظيم المعارض والمؤتمرات</span>
            </div>

            <div class="mt-2" style="font-size: 0.85rem; color: rgba(255,255,255,0.85)">
                @if($fair->event_date)
                    <i class="fas fa-calendar ms-1 text-warning"></i>{{ $fair->event_date->format('d/m/Y') }}
                    &nbsp;&bull;&nbsp;
                @endif
                <i class="fas fa-map-marker-alt ms-1 text-danger"></i>{{ $fair->location ?? 'جامعة طرابلس' }}
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
                <span class="reg-number-badge">
                    <i class="fas fa-ticket-alt me-1"></i>{{ $visitor->ticket_number }}
                </span>
            </div>

            <div class="text-center mb-3">
                <div class="grad-name">{{ $visitor->name }}</div>
                <div class="grad-info">
                    {{ $visitor->visitor_type_label }}
                    @if($visitor->organization)
                        — {{ $visitor->organization }}
                    @endif
                </div>
                <div class="mt-2">
                    <span class="status-badge {{ $visitor->attended ? 'status-attended' : 'status-registered' }}">
                        <i class="fas {{ $visitor->attended ? 'fa-check-circle' : 'fa-id-badge' }}"></i>
                        {{ $visitor->attended ? 'تم الحضور بالمعرض' : 'تذكرة زائر معتمدة' }}
                    </span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">صفة الزائر</div>
                    <div class="info-value">{{ $visitor->visitor_type_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">رقم الهاتف</div>
                    <div class="info-value" dir="ltr">{{ $visitor->phone }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">التخصص / المجال</div>
                    <div class="info-value">{{ $visitor->specialization ?: ($visitor->education_level ?: '—') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">المدينة</div>
                    <div class="info-value">{{ $visitor->city ?: 'طرابلس' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">تاريخ التسجيل</div>
                    <div class="info-value">{{ $visitor->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">هدف الزيارة</div>
                    <div class="info-value" style="font-size: 0.78rem;">{{ $visitor->visit_purpose ?: 'زيارة واستكشاف المعرض' }}</div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-title">
                    <i class="fas fa-qrcode ms-1 text-primary"></i>
                    امسح عند البوابة لتسجيل الدخول والتحقق
                </div>
                <div id="qr-code" class="d-flex justify-content-center"></div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="action-buttons no-print d-flex flex-column gap-2 mt-3">
        <!-- زر حفظ كصورة المطلوب صراحة من المستخدم -->
        <button onclick="downloadTicket(false)" class="btn-print w-100" id="download-btn" style="background: #10b981; box-shadow: 0 8px 25px rgba(16,185,129,0.35);">
            <i class="fas fa-camera ms-2"></i>حفظ كصورة
        </button>

        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn-print m-0">
                <i class="fas fa-print ms-2"></i>طباعة البطاقة
            </button>
            <a href="https://api.whatsapp.com/send?text={{ urlencode('بطاقة حضوري لمعرض التوظيف بجامعة طرابلس المنظم بالتعاون مع شركة الواحة: ' . route('job-fair.visitor.ticket', $visitor->ticket_number)) }}" target="_blank" class="btn-print m-0" style="background: #25D366; flex: 0.7;">
                <i class="fab fa-whatsapp ms-1"></i>واتساب
            </a>
            <a href="{{ route('job-fair.public', $fair->id) }}" class="btn-back m-0">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

</div>

<script>
// توليد QR Code
new QRCode(document.getElementById('qr-code'), {
    text: "{{ route('job-fair.visitor.ticket', $visitor->ticket_number) }}",
    width: 170,
    height: 170,
    colorDark: "#045db0",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

// دالة حفظ البطاقة كصورة (تدعم الحفظ اليدوي والحفظ التلقائي)
function downloadTicket(isAuto = false) {
    const btn = document.getElementById('download-btn');
    const toast = document.getElementById('autoSaveToast');
    const originalText = btn.innerHTML;

    if (!isAuto) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري إنشاء وحفظ الصورة...';
        btn.disabled = true;
    } else {
        if (toast) {
            toast.classList.remove('d-none');
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 600);
            }, 3500);
        }
    }

    const ticketElement = document.querySelector('.ticket');
    
    html2canvas(ticketElement, {
        scale: 2, // جودة فائقة الدقة
        useCORS: true,
        backgroundColor: null,
        onclone: function(clonedDoc) {
            const clonedWrapper = clonedDoc.querySelector('.ticket-wrapper');
            if (clonedWrapper) {
                clonedWrapper.style.transform = 'none';
            }
        }
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = `visitor_ticket_{{ $visitor->ticket_number }}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        if (!isAuto) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }).catch(err => {
        console.error("Error generating ticket image: ", err);
        if (!isAuto) {
            alert("حدث خطأ أثناء حفظ البطاقة، يرجى المحاولة مرة أخرى.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

// دالة لتعديل حجم البطاقة تلقائياً لتناسب أي شاشة موبايل أو ديسكتوب
function fitTicketToScreen() {
    const wrapper = document.querySelector('.ticket-wrapper');
    if (!wrapper) return;
    
    wrapper.style.transform = 'none';
    
    const windowHeight = window.innerHeight;
    const windowWidth = window.innerWidth;
    
    const wrapperHeight = wrapper.offsetHeight;
    const wrapperWidth = wrapper.offsetWidth;
    
    const scaleY = (windowHeight - 30) / wrapperHeight;
    const scaleX = (windowWidth - 20) / wrapperWidth;
    
    const scale = Math.min(scaleX, scaleY, 1);
    
    wrapper.style.transform = `scale(${scale})`;
}

// تشغيل التعديل عند التحميل وتغيير الحجم
window.addEventListener('load', fitTicketToScreen);
window.addEventListener('resize', fitTicketToScreen);
setTimeout(fitTicketToScreen, 100);

// ── تنفيذ الحفظ التلقائي بعد تحميل الصفحة وتوليد QR Code
window.addEventListener('load', function() {
    // الانتظار قليلاً حتى يكتمل رندر الـ QR Code والصور بالكامل
    setTimeout(function() {
        // التحقق من عدم التكرار إذا كان قد تم الحفظ مسبقاً في نفس الجلسة
        const storageKey = 'saved_ticket_{{ $visitor->ticket_number }}';
        if (!sessionStorage.getItem(storageKey)) {
            sessionStorage.setItem(storageKey, 'true');
            downloadTicket(true);
        }
    }, 1100);
});
</script>

</body>
</html>
