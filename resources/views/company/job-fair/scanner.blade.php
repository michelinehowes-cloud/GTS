@extends('layouts.app')

@section('title', 'ماسح التذاكر - ' . $fair->title)

@push('styles')
<style>
    /* ── Scanner Page Theme ── */
    .scanner-wrapper {
        min-height: calc(100vh - 120px);
        background: linear-gradient(160deg, #f0f4ff 0%, #f8faff 60%, #eef2ff 100%);
        padding: 2rem 1rem;
    }

    /* ── Hero Header ── */
    .scanner-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .scanner-hero::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.07);
    }
    .scanner-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -30px;
        width: 260px; height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .scanner-hero-icon {
        width: 72px; height: 72px;
        border-radius: 18px;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .scanner-hero .badge-fair {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.8rem;
        backdrop-filter: blur(5px);
    }

    /* ── Main Card ── */
    .scanner-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 30px rgba(30, 58, 138, 0.1);
        overflow: hidden;
        border: 1px solid rgba(30, 58, 138, 0.08);
    }

    /* ── Camera Container ── */
    #reader-container {
        background: #0f172a;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
    }
    #qr-reader {
        width: 100% !important;
    }
    #qr-reader video {
        border-radius: 16px;
        width: 100% !important;
    }
    /* Hide html5-qrcode default header */
    #qr-reader__header_message,
    #qr-reader__status_span,
    #qr-reader__camera_selection,
    #qr-reader__filescan_input,
    #qr-reader__dashboard_section_swaplink,
    #qr-reader__dashboard_section_fsr {
        display: none !important;
    }

    /* ── Scan Buttons ── */
    .btn-start-scan {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 32px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
    }
    .btn-start-scan:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.45);
        color: white;
    }
    .btn-stop-scan {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px 32px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-stop-scan:hover { transform: translateY(-1px); color: white; }

    /* ── Manual Input ── */
    .manual-input-group .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px 0 0 12px;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        letter-spacing: 3px;
        padding: 14px;
        color: #1e3a8a;
        transition: border-color 0.2s;
    }
    .manual-input-group .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        outline: none;
    }
    .manual-input-group .btn-confirm {
        background: #1e3a8a;
        color: white;
        border: none;
        border-radius: 0 12px 12px 0;
        padding: 14px 20px;
        font-size: 1.1rem;
    }
    .manual-input-group .btn-confirm:hover {
        background: #1d4ed8;
        color: white;
    }

    /* ── Divider ── */
    .divider-with-text {
        display: flex; align-items: center; gap: 12px;
        color: #94a3b8; font-size: 0.8rem; font-weight: 500;
        margin: 1.2rem 0;
    }
    .divider-with-text::before, .divider-with-text::after {
        content: ''; flex: 1;
        height: 1px; background: #e2e8f0;
    }

    /* ── Result Alerts ── */
    .result-card {
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        border: none;
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(10px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .result-success {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 2px solid #6ee7b7;
    }
    .result-warning {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 2px solid #fcd34d;
    }
    .result-error {
        background: linear-gradient(135deg, #fff1f2, #ffe4e6);
        border: 2px solid #fca5a5;
    }
    .result-icon {
        width: 60px; height: 60px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 0.75rem;
    }
    .result-success .result-icon { background: #d1fae5; color: #059669; }
    .result-warning .result-icon { background: #fef3c7; color: #d97706; }
    .result-error   .result-icon { background: #ffe4e6; color: #dc2626; }

    /* ── Counter Badge ── */
    .scan-counter {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        color: white;
        border-radius: 50px;
        padding: 6px 18px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* ── Footer action ── */
    .leads-link-btn {
        background: transparent;
        border: 2px solid #1e3a8a;
        color: #1e3a8a;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.25s;
    }
    .leads-link-btn:hover {
        background: #1e3a8a;
        color: white;
    }

    /* Scan frame pulse animation */
    .scan-frame {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 200px; height: 200px;
        border: 3px solid rgba(255,255,255,0.5);
        border-radius: 16px;
        pointer-events: none;
        z-index: 10;
        animation: pulse-border 2s ease infinite;
    }
    @keyframes pulse-border {
        0%, 100% { border-color: rgba(255,255,255,0.5); box-shadow: 0 0 0 0 rgba(37,99,235,0.4); }
        50%       { border-color: rgba(255,255,255,0.9); box-shadow: 0 0 0 10px rgba(37,99,235,0); }
    }
    .scan-line {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        animation: scan-line 2s ease-in-out infinite;
    }
    @keyframes scan-line {
        0%   { top: 0; opacity: 1; }
        90%  { top: 100%; opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
</style>
@endpush

@section('content')
<div class="scanner-wrapper">
<div class="container" style="max-width: 620px;">

    {{-- ── Hero Header ── --}}
    <div class="scanner-hero">
        <div class="d-flex justify-content-between align-items-start position-relative" style="z-index:1">
            <div class="d-flex align-items-start gap-3">
                {{-- Fair Banner/Logo --}}
                <img src="{{ $fair->logo_url }}"
                     alt="{{ $fair->title }}"
                     class="rounded-3 flex-shrink-0"
                     style="width:72px; height:72px; object-fit:contain; border:2px solid rgba(255,255,255,0.3); box-shadow:0 4px 15px rgba(0,0,0,0.2); background: white; padding: 4px;"
                     onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                <div>
                    <p class="mb-1 text-white-50 small fw-semibold" style="letter-spacing:0.5px;">
                        <i class="fas fa-calendar-alt me-1"></i>
                        {{ $fair->event_date ? $fair->event_date->format('d M Y') : '' }}
                        @if($fair->location)
                            &nbsp;·&nbsp;<i class="fas fa-map-marker-alt me-1"></i>{{ $fair->location }}
                        @endif
                    </p>
                    <h2 class="fw-bold mb-2" style="font-size:1.4rem; line-height:1.3;">{{ $fair->title }}</h2>
                    <span class="badge-fair">
                        <i class="fas fa-qrcode me-1"></i> ماسح التذاكر
                    </span>
                </div>
            </div>
            <a href="{{ route('company.job-fairs.index') }}"
               class="btn btn-sm text-white opacity-75 flex-shrink-0"
               style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); border-radius:10px; padding: 8px 16px;">
                <i class="fas fa-arrow-right me-1"></i> العودة
            </a>
        </div>
    </div>

    {{-- ── Main Scanner Card ── --}}
    <div class="scanner-card p-4 mb-3">

        {{-- Stats row --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="text-muted small fw-semibold">
                <i class="fas fa-circle-dot text-success me-1" style="font-size:0.6rem;"></i>
                جاهز للمسح
            </div>
            <span class="scan-counter" id="scan-count-badge">
                <i class="fas fa-user-check me-1"></i>
                <span id="scan-count">0</span> تم المسح
            </span>
        </div>

        {{-- Camera Controls --}}
        <div class="text-center mb-3">
            <button id="startCameraBtn" class="btn-start-scan" onclick="startCamera()">
                <i class="fas fa-camera me-2"></i> تشغيل الكاميرا
            </button>
            <button id="stopCameraBtn" class="btn-stop-scan d-none" onclick="stopCamera()">
                <i class="fas fa-stop me-2"></i> إيقاف الكاميرا
            </button>
        </div>

        {{-- Camera Feed --}}
        <div id="reader-container" class="d-none" style="position:relative;">
            <div id="qr-reader"></div>
            <div class="scan-frame">
                <div class="scan-line"></div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="divider-with-text">
            <span>أو أدخل الرمز يدوياً</span>
        </div>

        {{-- Manual Input --}}
        <div class="input-group manual-input-group mb-1">
            <input type="text" id="qr-input"
                class="form-control"
                placeholder="رقم التذكرة أو QR Code"
                autocomplete="off">
            <button class="btn-confirm" onclick="submitManual()">
                <i class="fas fa-check"></i>
            </button>
        </div>
        <div class="text-center text-muted" style="font-size:0.78rem;">
            اضغط <kbd style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px; padding:1px 6px; font-size:0.75rem;">Enter</kbd> لتأكيد الإدخال
        </div>

        {{-- Result Area --}}
        <div id="result-container" class="mt-4">
            <div id="result-success" class="result-card result-success d-none text-start px-4">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="result-icon m-0"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1 text-success">تم الاستلام بنجاح! ✅</h5>
                        <p class="fs-5 fw-bold mb-0 text-dark" id="result-name"></p>
                    </div>
                </div>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> التخصص</small>
                        <span class="fw-semibold text-dark" id="result-major"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-university me-1"></i> الجامعة</small>
                        <span class="fw-semibold text-dark" id="result-university"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-calendar-alt me-1"></i> سنة التخرج</small>
                        <span class="fw-semibold text-dark" id="result-grad-year"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-star me-1"></i> المعدل التراكمي (%)</small>
                        <span class="fw-semibold text-dark" id="result-gpa"></span>
                    </div>
                    <div class="col-12 mt-2">
                        <small class="text-muted d-block"><i class="fas fa-phone me-1"></i> رقم الهاتف</small>
                        <span class="fw-semibold text-dark" id="result-phone" dir="ltr"></span>
                    </div>
                </div>

                <div id="result-trainings-container" class="d-none mt-3 pt-3 border-top">
                    <h6 class="fw-bold text-primary mb-2"><i class="fas fa-certificate me-1"></i> الدورات التدريبية المنجزة</h6>
                    <ul class="list-unstyled mb-0" id="result-trainings-list" style="font-size: 0.9rem;">
                    </ul>
                </div>
            </div>

            <div id="result-warning" class="result-card result-warning d-none">
                <div class="result-icon"><i class="fas fa-clock"></i></div>
                <h5 class="fw-bold mb-1" style="color:#92400e;">تم الاستلام مسبقاً</h5>
                <p class="mb-0" style="color:#78350f;" id="result-warning-msg"></p>
            </div>

            <div id="result-error" class="result-card result-error d-none">
                <div class="result-icon"><i class="fas fa-times-circle"></i></div>
                <h5 class="fw-bold mb-1 text-danger">رمز غير صالح</h5>
                <p class="mb-0 text-muted small" id="result-error-msg">تأكد من أن الخريج مسجل في هذا المعرض.</p>
            </div>
        </div>
    </div>

    {{-- Footer Link --}}
    <div class="text-center pb-3">
        <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="leads-link-btn">
            <i class="fas fa-users me-2"></i> عرض السير المستلمة
        </a>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const checkInUrl  = "{{ route('company.job-fairs.store-visit', $fair->id) }}";
const csrfToken   = "{{ csrf_token() }}";
const input       = document.getElementById('qr-input');
let html5QrCode   = null;
let scanCooldown  = false;
let scanCount     = 0;

// ── Auto-focus ────────────────────────────────────────────
input.focus();
document.body.addEventListener('click', e => {
    if (!['INPUT','BUTTON','A'].includes(e.target.tagName)) input.focus();
});

// ── Manual Input ──────────────────────────────────────────
input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) submitManual();
});
function submitManual() {
    const val = input.value.trim();
    if (!val) return;
    input.value = '';
    processQR(val);
}

// ── Camera ────────────────────────────────────────────────
function startCamera() {
    document.getElementById('reader-container').classList.remove('d-none');
    document.getElementById('startCameraBtn').classList.add('d-none');
    document.getElementById('stopCameraBtn').classList.remove('d-none');

    html5QrCode = new Html5Qrcode("qr-reader");
    const config = { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.333 };

    html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, () => {})
    .catch(() => {
        html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, () => {})
        .catch(err => {
            showResultError("تعذّر فتح الكاميرا. تأكد من منح الإذن في المتصفح.");
            stopCamera();
        });
    });
}

function stopCamera() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => { html5QrCode.clear(); html5QrCode = null; }).catch(() => {});
    }
    document.getElementById('reader-container').classList.add('d-none');
    document.getElementById('startCameraBtn').classList.remove('d-none');
    document.getElementById('stopCameraBtn').classList.add('d-none');
}

function onScanSuccess(decodedText) {
    if (scanCooldown) return;
    scanCooldown = true;
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
    processQR(decodedText);
    setTimeout(() => { scanCooldown = false; }, 3500);
}

// ── API Call ──────────────────────────────────────────────
function processQR(code) {
    hideAllResults();

    let graduateId = null;
    const patterns = [
        /\/graduate\/profile\/(\d+)/i,   
        /\/profile\/(\d+)/i,             
        /profile[\/=](\d+)/i,            
        /[&?]id=(\d+)/i,                 
        /[\/\-_=](\d+)[\/\s]*$/,         
        /^JF-\d+-(\d+)-/i,               
        /^(\d+)$/,                        
    ];

    for (const pattern of patterns) {
        const m = code.match(pattern);
        if (m) { graduateId = m[1]; break; }
    }

    if (!graduateId) {
        showResultError("رمز QR غير صالح. تأكد من مسح البطاقة الصحيحة.");
        return;
    }

    fetch(checkInUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ graduate_id: parseInt(graduateId) })
    })
    .then(r => r.json())
    .then(data => {
        hideAllResults();
        if (!data.success) {
            showResultError(data.message || 'رمز غير صالح أو الخريج غير مسجل في المعرض.');
        } else if (data.already_visited) {
            document.getElementById('result-warning-msg').textContent = (data.graduate_name || '') + ' — مسجّل مسبقاً';
            document.getElementById('result-warning').classList.remove('d-none');
        } else {
            document.getElementById('result-name').textContent   = data.graduate_name || '';
            document.getElementById('result-major').textContent  = data.major || '—';
            document.getElementById('result-university').textContent = data.university || '—';
            document.getElementById('result-grad-year').textContent = data.graduation_year || '—';
            document.getElementById('result-gpa').textContent = data.gpa ? data.gpa + '%' : '—';
            document.getElementById('result-phone').textContent = data.phone || '—';
            
            const trainingsContainer = document.getElementById('result-trainings-container');
            const trainingsList = document.getElementById('result-trainings-list');
            trainingsList.innerHTML = '';
            
            if (data.trainings && data.trainings.length > 0) {
                data.trainings.forEach(t => {
                    const li = document.createElement('li');
                    li.className = 'mb-1 text-dark';
                    li.innerHTML = '<i class="fas fa-check text-success me-1"></i> ' + t.title + ' <small class="text-muted ms-1">(' + t.date + ')</small>';
                    trainingsList.appendChild(li);
                });
                trainingsContainer.classList.remove('d-none');
            } else {
                trainingsContainer.classList.add('d-none');
            }

            document.getElementById('result-success').classList.remove('d-none');
            // Increment counter
            scanCount++;
            document.getElementById('scan-count').textContent = scanCount;
        }
        // Increase timeout to 8 seconds so they have time to read the details
        setTimeout(hideAllResults, 8000);
        input.focus();
    })
    .catch(() => {
        showResultError('حدث خطأ في الاتصال. تحقق من الإنترنت وأعد المحاولة.');
        setTimeout(hideAllResults, 4000);
    });
}

function showResultError(msg) {
    document.getElementById('result-error-msg').textContent = msg;
    document.getElementById('result-error').classList.remove('d-none');
}

function hideAllResults() {
    ['result-success','result-warning','result-error'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
}
</script>
@endpush
