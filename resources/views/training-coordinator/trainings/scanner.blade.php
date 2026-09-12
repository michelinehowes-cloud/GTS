@extends('layouts.app')

@section('title', 'ماسح الحضور متعدد الأيام - ' . $training->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bento-dashboard.css') }}">
<style>
    #qr-reader { width: 100%; min-height: 280px; border-radius: 16px; overflow: hidden; background: #000; }
    #qr-reader video { border-radius: 16px !important; }
    #qr-reader img { display: none !important; }
    #debug-box {
        background: rgba(15,23,42,0.85);
        color: #7dd3fc;
        font-family: monospace;
        font-size: 0.78rem;
        padding: 10px 14px;
        border-radius: 10px;
        min-height: 32px;
        word-break: break-all;
        margin-top: 12px;
    }
    .https-warning { background: linear-gradient(135deg,#fef3c7,#fde68a); border: 2px solid #f59e0b; border-radius:16px; padding:1.5rem; }
    .session-badge {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 10px;
        padding: 6px 14px;
        font-size: 0.85rem;
    }
</style>
@endpush

@php
    $isAdmin = auth()->user()->role === 'admin';
    $prefix  = $isAdmin ? 'admin' : 'training-coordinator';
@endphp

@section('content')
<div class="container-fluid py-4">

    {{-- رأس الصفحة --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="bento-card" style="background:linear-gradient(135deg,rgba(30,41,59,0.95),rgba(15,23,42,0.98)); border-right:4px solid #f59e0b;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-4"
                             style="width:60px;height:60px;background:rgba(245,158,11,0.2);border-radius:16px;font-size:1.8rem;color:#f59e0b;">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">ماسح الحضور اليومي</h3>
                            <p class="mb-0 text-white-50 fs-6"><i class="fas fa-graduation-cap me-1"></i>{{ $training->title }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route($prefix . '.trainings.attendance', $training->id) }}"
                           class="btn btn-warning fw-bold" style="border-radius:12px;padding:10px 20px;">
                            <i class="fas fa-table me-1"></i> جدول الحضور الشامل
                        </a>
                        <a href="{{ route($prefix . '.trainings.show', $training->id) }}"
                           class="btn btn-outline-light" style="border-radius:12px;padding:10px 20px;font-weight:600;border-color:rgba(255,255,255,0.2);">
                            <i class="fas fa-arrow-right me-1"></i>العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            {{-- شريط معلومات الجلسة واليوم المحدد --}}
            <div class="card-modern mb-4 p-3 bg-white">
                <div class="row g-3 align-items-center">
                    <div class="col-md-7">
                        <label class="text-muted small fw-bold mb-1 d-block"><i class="fas fa-calendar-day me-1 text-primary"></i>اختر يوم التسجيل:</label>
                        <select id="selected-date" class="form-select border-primary-subtle" style="border-radius: 10px; font-weight: bold;">
                            @foreach($trainingDays as $day)
                                <option value="{{ $day['date'] }}" {{ $day['is_today'] ? 'selected' : '' }}>
                                    يوم {{ $day['day_number'] }} - {{ $day['day_name'] }} ({{ $day['date'] }}) {{ $day['is_today'] ? '⭐ [اليوم الحالي]' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 text-md-start text-center">
                        <div class="p-2 bg-light rounded-3 border text-center">
                            <span class="text-muted small d-block mb-1">المسجلون في هذا اليوم:</span>
                            <span class="fs-5 fw-bold text-success" id="counter-today">{{ $todayAttended }}</span>
                            <span class="text-muted small">/ <span id="counter-total">{{ $totalApproved }}</span> متدرب</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- تحذير HTTPS --}}
            <div id="https-warning" class="https-warning mb-4 d-none text-center">
                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                <h5 class="fw-bold text-dark mb-2">الكاميرا تحتاج HTTPS</h5>
                <p class="text-dark mb-0 small">
                    المتصفح يرفض الكاميرا على روابط HTTP العادية.<br>
                    <strong>استخدم الإدخال اليدوي أدناه بدلاً من ذلك.</strong>
                </p>
            </div>

            {{-- بطاقة الكاميرا والماسح --}}
            <div class="bento-card mb-4">
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-dark mb-1"><i class="fas fa-camera me-2 text-primary"></i>مسح رمز QR لبطاقة الخريج</h5>
                    <p class="text-muted small mb-0">وجّه الكاميرا نحو رمز QR الخاص بالخريج لتسجيل حضوره فوراً لليوم المحدد</p>
                </div>

                <div class="d-flex gap-2 justify-content-center mb-4">
                    <button id="startBtn" onclick="startCamera()" class="btn btn-primary fw-bold px-4" style="border-radius:12px;">
                        <i class="fas fa-camera me-2"></i>تشغيل الكاميرا
                    </button>
                    <button id="stopBtn" onclick="stopCamera()" class="btn btn-danger fw-bold px-4 d-none" style="border-radius:12px;">
                        <i class="fas fa-stop me-2"></i>إيقاف
                    </button>
                </div>

                {{-- حاوية الكاميرا --}}
                <div id="reader-wrap" class="d-none mb-4" style="border-radius:16px;overflow:hidden;">
                    <div id="qr-reader"></div>
                </div>

                {{-- نتائج المسح التفاعلية --}}
                <div id="result-success" class="alert alert-success d-none border-0 rounded-4 p-3 text-center shadow-sm">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                    <h5 class="fw-bold mb-1" id="result-success-title">✅ تم تسجيل الحضور بنجاح!</h5>
                    <p class="mb-1 fw-bold text-dark" id="result-name" style="font-size: 1.1rem;"></p>
                    <div class="small text-muted" id="result-details"></div>
                </div>
                <div id="result-warning" class="alert alert-warning d-none border-0 rounded-4 p-3 text-center shadow-sm">
                    <i class="fas fa-exclamation-circle fa-2x mb-2 text-warning"></i>
                    <h5 class="fw-bold mb-1 text-dark">⚠️ مسجل مسبقاً لهذا اليوم</h5>
                    <p class="mb-0 text-dark" id="result-warning-msg"></p>
                </div>
                <div id="result-error" class="alert alert-danger d-none border-0 rounded-4 p-3 text-center shadow-sm">
                    <i class="fas fa-times-circle fa-2x mb-2 text-danger"></i>
                    <h5 class="fw-bold mb-1 text-danger">❌ تعذر تسجيل الحضور</h5>
                    <p class="mb-0 small" id="result-error-msg"></p>
                </div>

                {{-- صندوق التتبع اللحظي --}}
                <div id="debug-box">⏳ جاهز للمسح...</div>
            </div>

            {{-- الإدخال اليدوي المباشر --}}
            <div class="bento-card">
                <h5 class="fw-bold text-dark mb-2"><i class="fas fa-keyboard me-2 text-primary"></i>تسجيل يدوي سريع</h5>
                <p class="text-muted small mb-3">أدخل رقم الخريج التعريفي أو الرقم الجامعي للتسجيل الفوري</p>
                <div class="input-group">
                    <input type="number" id="qr-input" class="form-control"
                           placeholder="رقم الخريج..." autocomplete="off"
                           style="border-radius:12px 0 0 12px;padding:12px 16px;font-size:1rem;">
                    <button class="btn btn-primary fw-bold px-4" onclick="submitManual()"
                            style="border-radius:0 12px 12px 0;">
                        <i class="fas fa-check me-1"></i>تسجيل الحضور
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const SCAN_URL   = "{{ route($prefix . '.trainings.scan', $training->id) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";
let html5QrCode  = null;
let scanCooldown = false;

function log(msg, color) {
    const box = document.getElementById('debug-box');
    if (box) { box.style.color = color || '#7dd3fc'; box.textContent = msg; }
    console.log('[SCANNER]', msg);
}

// فحص HTTPS
document.addEventListener('DOMContentLoaded', function () {
    const isSecure = location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    if (!isSecure) {
        document.getElementById('https-warning').classList.remove('d-none');
        document.getElementById('startBtn').disabled = true;
        log('⚠️ HTTP فقط — استخدم الإدخال اليدوي.', '#fbbf24');
    } else {
        log('✅ جاهز لمسح رموز الخريجين.', '#34d399');
    }
});

// إدخال يدوي
document.getElementById('qr-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && this.value.trim()) submitManual();
});

function submitManual() {
    const val = document.getElementById('qr-input').value.trim();
    if (!val) return;
    document.getElementById('qr-input').value = '';
    log('📝 إدخال يدوي: ' + val);
    processQR(val);
}

// تشغيل الكاميرا
function startCamera() {
    log('📷 جاري تشغيل الكاميرا...');
    document.getElementById('reader-wrap').classList.remove('d-none');
    document.getElementById('startBtn').classList.add('d-none');
    document.getElementById('stopBtn').classList.remove('d-none');

    html5QrCode = new Html5Qrcode("qr-reader");
    const config = { fps: 15, qrbox: { width: 220, height: 220 }, aspectRatio: 1.333 };

    html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, () => {})
    .then(() => log('✅ الكاميرا الخلفية تعمل — وجّه نحو بطاقة الخريج', '#34d399'))
    .catch(() => {
        html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, () => {})
        .then(() => log('✅ الكاميرا الأمامية تعمل — وجّه نحو بطاقة الخريج', '#34d399'))
        .catch(err => {
            log('❌ فشل الكاميرا: ' + err, '#f87171');
            showError("لم نتمكن من تشغيل الكاميرا. يرجى التأكد من صلاحية الوصول أو استخدام الإدخال اليدوي.");
            stopCamera();
        });
    });
}

function stopCamera() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => { html5QrCode.clear(); html5QrCode = null; }).catch(() => {});
    }
    document.getElementById('reader-wrap').classList.add('d-none');
    document.getElementById('startBtn').classList.remove('d-none');
    document.getElementById('stopBtn').classList.add('d-none');
    log('⏹ تم إيقاف الكاميرا.');
}

function onScanSuccess(decodedText) {
    if (scanCooldown) return;
    scanCooldown = true;
    log('🎯 تم قراءة الرمز: ' + decodedText, '#fbbf24');
    try { if (navigator.vibrate) navigator.vibrate([100, 50, 100]); } catch(e) {}
    processQR(decodedText);
    setTimeout(() => { scanCooldown = false; log('🔄 مستعد لمسح جديد.', '#34d399'); }, 3500);
}

// معالجة الرمز
function processQR(code) {
    hideResults();
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
        log('❌ رمز غير صالح: ' + code, '#f87171');
        showError('رمز QR غير صالح. تأكد من مسح بطاقة الخريج الصحيحة.');
        return;
    }

    const selectedDate = document.getElementById('selected-date').value;
    log('✅ جاري إرسال الحضور للمعلم: ' + graduateId + ' في تاريخ: ' + selectedDate, '#a78bfa');

    fetch(SCAN_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ graduate_id: parseInt(graduateId), date: selectedDate })
    })
    .then(response => response.json())
    .then(data => {
        hideResults();
        if (data.success && !data.already_attended) {
            const nameEl = document.getElementById('result-name');
            if (nameEl) nameEl.textContent = data.student_name || 'خريج';

            const detailsEl = document.getElementById('result-details');
            if (detailsEl) {
                detailsEl.innerHTML = 
                    '<span class="badge bg-primary me-1">' + (data.faculty || 'خريج') + '</span>' +
                    '<span>حضور <strong>' + (data.user_attended_days ?? 1) + '</strong> من <strong>' + (data.total_days ?? 1) + '</strong> أيام</span>' +
                    '<span class="ms-2 text-muted">(' + (data.attended_time || '') + ')</span>';
            }

            const successEl = document.getElementById('result-success');
            if (successEl) successEl.classList.remove('d-none');
            
            // تحديث العداد بأمان
            const counterEl = document.getElementById('counter-today');
            if (counterEl && data.today_attended_count !== undefined) {
                counterEl.textContent = data.today_attended_count;
            }
            log('✅ نجاح: ' + data.message, '#34d399');
        } else if (data.already_attended) {
            const warnMsgEl = document.getElementById('result-warning-msg');
            if (warnMsgEl) {
                warnMsgEl.innerHTML = 
                    '<strong>' + (data.student_name || 'الخريج') + '</strong> مسجل مسبقاً في تاريخ ' + (data.date || '') + ' (في الساعة ' + (data.attended_time || '') + ').' +
                    '<br><small class="text-muted">إجمالي الأيام المحضورة: ' + (data.user_attended_days ?? '') + ' من ' + (data.total_days ?? '') + ' أيام</small>';
            }
            const warnEl = document.getElementById('result-warning');
            if (warnEl) warnEl.classList.remove('d-none');
            log('⚠️ مسجل مسبقاً', '#fbbf24');
        } else {
            const errMsg = data.message || 'خطأ غير معروف';
            showError(errMsg);
            log('❌ خطأ: ' + errMsg, '#f87171');
        }
        setTimeout(hideResults, 6000);
    })
    .catch(err => {
        console.error('[SCANNER ERROR]', err);
        log('🔴 خطأ: ' + err.message, '#f87171');
        showError('حدث خطأ أثناء معالجة الطلب. يرجى التحقق وإعادة المحاولة.');
        setTimeout(hideResults, 4000);
    });
}

function showError(msg) {
    const errorMsgEl = document.getElementById('result-error-msg');
    if (errorMsgEl) errorMsgEl.textContent = msg;
    const errorEl = document.getElementById('result-error');
    if (errorEl) errorEl.classList.remove('d-none');
}

function hideResults() {
    ['result-success','result-warning','result-error'].forEach(id => {
        document.getElementById(id)?.classList.add('d-none');
    });
}
</script>
@endpush
