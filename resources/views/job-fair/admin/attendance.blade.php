@extends('layouts.app')

@section('title', 'تسجيل الحضور - ' . $fair->title)

@push('styles')
<style>
    .scanner-section {
        background: linear-gradient(135deg, #03488a 0%, #045db0 100%);
        border-radius: 24px;
        padding: 2.5rem;
        color: white;
        box-shadow: 0 20px 40px rgba(4,93,176,0.3);
    }
    .scanner-box {
        border: 3px dashed rgba(238,202,62,0.6);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        margin: 1.5rem 0;
        background: rgba(255,255,255,0.05);
    }
    #qr-input {
        background: rgba(255,255,255,0.1);
        border: 2px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        color: white;
        font-size: 1.1rem;
        text-align: center;
        padding: 12px;
        width: 100%;
    }
    #qr-input::placeholder { color: rgba(255,255,255,0.4); }
    #qr-input:focus { outline: none; border-color: #eeca3e; box-shadow: 0 0 0 3px rgba(238,202,62,0.2); }

    .result-card {
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 1rem;
        display: none;
    }
    .result-success { background: #d1fae5; border: 2px solid #10B981; }
    .result-warning { background: #fef3c7; border: 2px solid #F59E0B; }
    .result-error   { background: #fee2e2; border: 2px solid #EF4444; }

    .attendance-stat {
        text-align: center;
        padding: 1rem;
        background: rgba(255,255,255,0.08);
        border-radius: 16px;
    }
    .attendance-stat .num { font-size: 2.5rem; font-weight: 900; color: #eeca3e; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .attendance-stat .lbl { color: rgba(255,255,255,0.6); font-size: 0.85rem; }
</style>
@endpush

@section('focus_mode', true)

@section('content')
<div class="container py-4" style="max-width: 800px">

    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="btn btn-light rounded-circle shadow-sm" style="width:45px;height:45px;display:flex;align-items:center;justify-content:center; color: #045db0;">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-0" style="color: #03488a">
                    <i class="fas fa-qrcode me-2" style="color: #eeca3e"></i>
                    تسجيل الحضور
                </h2>
                <small class="text-muted fw-bold">{{ $fair->title }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3 bg-white p-2 rounded-4 shadow-sm">
            <img src="{{ asset('images/logo.jpg') }}" alt="مكتب الخريجين" style="height: 40px; border-radius: 8px;">
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <img src="{{ asset('images/job_fair_logo.png') }}" alt="شعار المعرض" style="height: 45px;">
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="scanner-section mb-4">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="attendance-stat">
                    <div class="num" id="stat-registered">{{ $stats['total_registered'] }}</div>
                    <div class="lbl">مسجل</div>
                </div>
            </div>
            <div class="col-4">
                <div class="attendance-stat">
                    <div class="num" id="stat-attended">{{ $stats['total_attended'] }}</div>
                    <div class="lbl">حضر</div>
                </div>
            </div>
            <div class="col-4">
                <div class="attendance-stat">
                    <div class="num" id="stat-pct">
                        {{ $stats['total_registered'] > 0 ? round($stats['total_attended'] / $stats['total_registered'] * 100) : 0 }}%
                    </div>
                    <div class="lbl">نسبة الحضور</div>
                </div>
            </div>
        </div>

        <!-- QR Input -->
        <div class="scanner-box" id="scanner-init-controls">
            <div style="font-size: 3rem; margin-bottom: 1rem">📷</div>
            <p style="color: rgba(255,255,255,0.7)" class="mb-3">
                وجّه ماسح QR نحو بطاقة الخريج، أو اكتب الرمز يدوياً
            </p>
            <button id="start-camera-btn" class="btn mb-3 px-4 py-2" style="border-radius: 50px; font-weight: bold; background-color: #eeca3e; color: #03488a; border: none; box-shadow: 0 4px 15px rgba(238,202,62,0.4); transition: all 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <i class="fas fa-camera me-2"></i> فتح الكاميرا للمسح
            </button>
            <input type="text" id="qr-input" placeholder="JF-1-5-XXXXXXXX"
                   autocomplete="off" autocorrect="off" spellcheck="false" class="form-control text-center mx-auto" style="max-width: 300px;">
        </div>

        <!-- Camera Container -->
        <div id="reader" style="display: none; margin: 0 auto; max-width: 400px; border-radius: 12px; overflow: hidden; background: #000; box-shadow: 0 10px 25px rgba(0,0,0,0.5);"></div>

        <!-- Result -->
        <div class="result-card result-success" id="result-success">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size: 2.5rem">✅</div>
                <div>
                    <h5 class="fw-bold text-success mb-1">تم تسجيل الحضور بنجاح!</h5>
                    <p class="mb-1 text-dark" id="result-name"></p>
                    <small class="text-muted" id="result-major"></small>
                </div>
            </div>
        </div>
        <div class="result-card result-warning" id="result-warning">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size: 2.5rem">⚠️</div>
                <div>
                    <h5 class="fw-bold mb-1" style="color:#92400e">مسجّل الحضور مسبقاً</h5>
                    <p class="mb-0 text-dark" id="result-warning-msg"></p>
                </div>
            </div>
        </div>
        <div class="result-card result-error" id="result-error">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size: 2.5rem">❌</div>
                <div>
                    <h5 class="fw-bold text-danger mb-1">رمز QR غير صالح</h5>
                    <p class="mb-0 text-muted">تأكد من صحة الرمز وحاول مجدداً</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick scan history -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-history me-2 text-muted"></i>
                آخر عمليات تسجيل الحضور
            </h6>
            <div id="scan-history">
                <p class="text-muted text-center small">لا يوجد سجل بعد في هذه الجلسة</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
const input = document.getElementById('qr-input');
const checkInUrl = "{{ route('job-fair.admin.check-in') }}";
const csrfToken = "{{ csrf_token() }}";
let attendedCount = {{ $stats['total_attended'] }};
let registeredCount = {{ $stats['total_registered'] }};
let scanHistory = [];
let html5QrcodeScanner;
let isScanning = false;

// Auto-focus input
input.focus();

// Listen for QR scan (Enter key)
input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) {
        processQR(this.value.trim());
        this.value = '';
    }
});

// Camera Scanner Logic
document.getElementById('start-camera-btn').addEventListener('click', function() {
    document.getElementById('scanner-init-controls').style.display = 'none';
    document.getElementById('reader').style.display = 'block';
    
    html5QrcodeScanner = new Html5Qrcode("reader");
    const config = { 
        fps: 10, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        disableFlip: false
    };
    
    html5QrcodeScanner.start({ facingMode: "environment" }, config, (decodedText, decodedResult) => {
        if(!isScanning) {
            isScanning = true;
            processQR(decodedText);
            setTimeout(() => { isScanning = false; }, 3000); 
        }
    })
    .catch((err) => {
        alert("تعذر الوصول إلى الكاميرا. يرجى التأكد من منح الصلاحيات.");
        document.getElementById('scanner-init-controls').style.display = 'block';
        document.getElementById('reader').style.display = 'none';
    });
});

function processQR(code) {
    hideAllResults();
    
    fetch(checkInUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ qr_code: code })
    })
    .then(r => r.json())
    .then(data => {
        hideAllResults();

        if (!data.success) {
            showResult('error');
            playBeep('error');
        } else if (data.already_in) {
            document.getElementById('result-warning-msg').textContent =
                `${data.graduate} — دخل الساعة ${data.check_in_at}`;
            showResult('warning');
            playBeep('warning');
        } else {
            document.getElementById('result-name').textContent = data.graduate;
            document.getElementById('result-major').textContent = data.major + (data.faculty ? ' — ' + data.faculty : '');
            showResult('success');
            playBeep('success');

            // Update stats
            attendedCount++;
            document.getElementById('stat-attended').textContent = attendedCount;
            const pct = Math.round(attendedCount / registeredCount * 100);
            document.getElementById('stat-pct').textContent = pct + '%';

            // Add to history
            addToHistory(data.graduate, data.major);
        }

        // Auto-hide after 4 seconds
        setTimeout(hideAllResults, 4000);
        input.focus();
    })
    .catch(() => {
        showResult('error');
        input.focus();
    });
}

function showResult(type) {
    document.getElementById('result-' + type).style.display = 'flex';
}
function hideAllResults() {
    ['success', 'warning', 'error'].forEach(t => {
        document.getElementById('result-' + t).style.display = 'none';
    });
}

function addToHistory(name, major) {
    const historyDiv = document.getElementById('scan-history');
    const now = new Date().toLocaleTimeString('ar', {hour:'2-digit', minute:'2-digit'});

    scanHistory.unshift({ name, major, time: now });
    if (scanHistory.length > 10) scanHistory.pop();

    historyDiv.innerHTML = scanHistory.map(s => `
        <div class="d-flex align-items-center gap-3 p-2 border-bottom" style="font-size:0.9rem">
            <span class="text-muted" style="font-size:0.75rem">${s.time}</span>
            <span class="badge bg-success rounded-pill" style="font-size:0.65rem">✓</span>
            <strong>${s.name}</strong>
            <small class="text-muted">${s.major || ''}</small>
        </div>
    `).join('');
}

function playBeep(type) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.connect(g);
        g.connect(ctx.destination);
        o.frequency.value = type === 'success' ? 880 : type === 'warning' ? 440 : 220;
        g.gain.value = 0.3;
        o.start();
        o.stop(ctx.currentTime + 0.15);
    } catch(e) {}
}
</script>
@endpush
