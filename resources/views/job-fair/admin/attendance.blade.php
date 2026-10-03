@extends('layouts.app')

@section('title', 'محطة تسجيل الحضور - ' . $fair->title)

@push('styles')
<style>
    /* Global Page Variables */
    :root {
        --terminal-bg: linear-gradient(135deg, #091f3c 0%, #03488a 50%, #045db0 100%);
        --terminal-accent: #eeca3e;
        --terminal-cyan: #38bdf8;
    }

    /* Scanner Terminal Card */
    .scanner-terminal-card {
        background: linear-gradient(135deg, #091f3c 0%, #03488a 50%, #045db0 100%) !important;
        border-radius: 24px;
        padding: 2rem;
        color: white;
        box-shadow: 0 15px 35px rgba(3, 72, 138, 0.28);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-bottom: 3.5px solid #eeca3e;
        position: relative;
        overflow: hidden;
    }

    .scanner-terminal-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 80% 20%, rgba(238, 202, 62, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    /* Terminal Viewport / Camera Box */
    .scanner-viewport-box {
        background: rgba(255, 255, 255, 0.08);
        border: 2px dashed rgba(238, 202, 62, 0.6);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .scanner-viewport-box:hover {
        border-color: rgba(238, 202, 62, 0.95);
        box-shadow: 0 0 25px rgba(238, 202, 62, 0.25);
        background: rgba(255, 255, 255, 0.12);
    }

    /* High-tech Viewfinder Brackets */
    .viewfinder-wrapper {
        position: relative;
        max-width: 440px;
        margin: 0 auto;
        border-radius: 16px;
        overflow: hidden;
    }

    .viewfinder-frame {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 10;
    }

    .viewfinder-frame::before, .viewfinder-frame::after {
        content: '';
        position: absolute;
        width: 32px;
        height: 32px;
        border-color: var(--terminal-accent);
        border-style: solid;
    }

    .viewfinder-frame::before {
        top: 16px;
        right: 16px;
        border-width: 3px 3px 0 0;
        border-top-right-radius: 8px;
    }

    .viewfinder-frame::after {
        bottom: 16px;
        left: 16px;
        border-width: 0 0 3px 3px;
        border-bottom-left-radius: 8px;
    }

    .corner-tl, .corner-br {
        position: absolute;
        width: 32px;
        height: 32px;
        border-color: var(--terminal-accent);
        border-style: solid;
        pointer-events: none;
        z-index: 10;
    }

    .corner-tl {
        top: 16px;
        left: 16px;
        border-width: 3px 0 0 3px;
        border-top-left-radius: 8px;
    }

    .corner-br {
        bottom: 16px;
        right: 16px;
        border-width: 0 3px 3px 0;
        border-bottom-right-radius: 8px;
    }

    /* Laser Scanner Beam */
    .laser-scan-line {
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #eeca3e, #fff, #eeca3e, transparent);
        box-shadow: 0 0 15px #eeca3e, 0 0 30px #eeca3e;
        z-index: 11;
        animation: laserScan 2.4s ease-in-out infinite alternate;
        pointer-events: none;
    }

    @keyframes laserScan {
        0% { top: 8%; opacity: 0.3; }
        50% { opacity: 1; }
        100% { top: 92%; opacity: 0.3; }
    }

    /* Fast Barcode Input */
    .barcode-input-group {
        background: rgba(255, 255, 255, 0.12);
        border: 2px solid rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        padding: 4px 6px;
        transition: all 0.3s;
    }

    .barcode-input-group:focus-within {
        border-color: var(--terminal-accent);
        box-shadow: 0 0 0 4px rgba(238, 202, 62, 0.25);
        background: rgba(255, 255, 255, 0.18);
    }

    .barcode-input-group input {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 1.15rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .barcode-input-group input:focus {
        outline: none;
        box-shadow: none;
        background: transparent;
        color: #ffffff;
    }

    .barcode-input-group input::placeholder {
        color: rgba(255, 255, 255, 0.45);
        font-weight: normal;
        font-size: 0.95rem;
    }

    /* Feedback Result Banners */
    .status-feedback-card {
        border-radius: 18px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        display: none;
        animation: slideInResult 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideInResult {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .status-success {
        background: #ecfdf5;
        border: 2px solid #10b981;
        color: #065f46;
    }

    .status-warning {
        background: #fffbeb;
        border: 2px solid #f59e0b;
        color: #92400e;
    }

    .status-error {
        background: #fef2f2;
        border: 2px solid #ef4444;
        color: #991b1b;
    }

    /* Stat Bento Cards */
    .attendance-kpi-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .attendance-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }

    .kpi-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* History & Directory Scroll Box */
    .feed-scroll-box {
        max-height: 520px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }

    .feed-scroll-box::-webkit-scrollbar {
        width: 6px;
    }

    .feed-scroll-box::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }

    .attendee-feed-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 0.9rem 1rem;
        margin-bottom: 0.65rem;
        transition: all 0.2s ease;
    }

    .attendee-feed-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateX(-3px);
    }

    .attendee-feed-item.just-added {
        animation: highlightFlash 1.5s ease-out;
    }

    @keyframes highlightFlash {
        0% { background: #d1fae5; border-color: #10b981; }
        100% { background: #ffffff; border-color: #f1f5f9; }
    }

    /* Avatar Circle */
    .avatar-initials {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #ffffff;
        flex-shrink: 0;
    }

    /* Mode Pill Switches */
    .mode-tab-btn {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: rgba(255, 255, 255, 0.95);
        border-radius: 50px;
        padding: 0.45rem 1.15rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .mode-tab-btn.active, .mode-tab-btn:hover {
        background: var(--terminal-accent);
        border-color: var(--terminal-accent);
        color: #03488a;
    }

    #reader {
        width: 100% !important;
        border: none !important;
        background: transparent !important;
    }

    #reader video {
        border-radius: 14px !important;
        object-fit: cover !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-xl-4 py-3">

    {{-- الشريط العلوي الموحد للشعارات والعنوان --}}
    @include('job-fair.admin.partials.header', [
        'fair' => $fair,
        'page' => 'attendance',
        'title' => 'محطة تسجيل الحضور والتحقق الذكي'
    ])

    {{-- شريط الإجراءات السريعة وحالة المحطة --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 mt-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-7 d-inline-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-success" style="width: 0.6rem; height: 0.6rem;" role="status"></span>
                <span class="fw-bold">محطة الدخول جاهزة ونشطة</span>
            </span>
            <span class="text-muted small d-none d-md-inline">| بوابة الاستقبال الرئيسية</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button id="sound-toggle-btn" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 shadow-sm" title="تفعيل أو كتم صوت الصافرة عند المسح">
                <i class="fas fa-volume-up text-primary me-1" id="sound-icon"></i>
                <span id="sound-text">صوت التنبيه: مفعّل</span>
            </button>
            <a href="{{ route('job-fair.admin.live', $fair) }}" target="_blank" class="btn btn-sm btn-dark rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                <i class="fas fa-tv text-danger fa-pulse"></i>
                <span>شاشة البث المباشر</span>
            </a>
            <a href="{{ route('job-fair.admin.export', $fair) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 shadow-sm">
                <i class="fas fa-file-excel me-1"></i>
                <span>تصدير الكشف</span>
            </a>
            <a href="{{ route('job-fair.admin.show', $fair) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 shadow-sm">
                <i class="fas fa-arrow-right me-1"></i>
                <span>تفاصيل المعرض</span>
            </a>
        </div>
    </div>

    {{-- بطاقات مؤشرات الحضور (Bento Metrics Row) --}}
    <div class="row g-3 mb-4">
        {{-- إجمالي المسجلين --}}
        <div class="col-6 col-md-3">
            <div class="attendance-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-bold small">إجمالي المسجلين</span>
                    <div class="kpi-icon-box bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div>
                    <h2 class="fw-black mb-0 text-dark" id="stat-registered">{{ $stats['total_registered'] ?? 0 }}</h2>
                    <small class="text-muted">خريج مسجل رسمياً</small>
                </div>
            </div>
        </div>

        {{-- تم الحضور --}}
        <div class="col-6 col-md-3">
            <div class="attendance-kpi-card" style="border-right: 4px solid #10b981;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-success fw-bold small">تم تسجيل دخولهم</span>
                    <div class="kpi-icon-box bg-success-subtle text-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <div>
                    <h2 class="fw-black mb-0 text-success" id="stat-attended">{{ $stats['total_attended'] ?? 0 }}</h2>
                    <small class="text-muted">حاضر داخل المعرض الآن</small>
                </div>
            </div>
        </div>

        {{-- المتبقي للوصول --}}
        <div class="col-6 col-md-3">
            <div class="attendance-kpi-card" style="border-right: 4px solid #f59e0b;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-warning fw-bold small">في انتظار الوصول</span>
                    <div class="kpi-icon-box bg-warning-subtle text-warning">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
                <div>
                    <h2 class="fw-black mb-0 text-warning" id="stat-remaining">{{ $stats['remaining'] ?? 0 }}</h2>
                    <small class="text-muted">خريج متوقع وصوله</small>
                </div>
            </div>
        </div>

        {{-- نسبة الإقبال --}}
        <div class="col-6 col-md-3">
            <div class="attendance-kpi-card" style="border-right: 4px solid #0284c7;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-info fw-bold small">نسبة الحضور</span>
                    <div class="kpi-icon-box bg-info-subtle text-info">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h2 class="fw-black mb-0 text-dark" id="stat-pct">{{ $stats['pct'] ?? 0 }}%</h2>
                        <span class="badge bg-light text-secondary rounded-pill small">من المستهدف</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-gradient-primary" id="stat-progress-bar" role="progressbar" style="width: {{ $stats['pct'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- مساحة العمل التفاعلية المزدوجة (Split Terminal Workspace) --}}
    <div class="row g-4">
        
        {{-- العمود الأيمن: محطة المسح والتحقق الذكي --}}
        <div class="col-12 col-xl-7">
            <div class="scanner-terminal-card h-100">
                
                {{-- رأس المحطة --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <i class="fas fa-qrcode text-warning fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">ماسح بطاقات الحضور والـ QR</h5>
                            <small class="text-white-50">امسح التذكرة الذكية أو أدخل رقم التسجيل مباشرة</small>
                        </div>
                    </div>

                    {{-- أزرار التبديل بين الكاميرا والماسح اللاسلكي --}}
                    <div class="d-flex gap-2">
                        <button type="button" id="tab-camera-btn" class="mode-tab-btn active">
                            <i class="fas fa-camera me-1"></i> كاميرا الجهاز
                        </button>
                        <button type="button" id="tab-manual-btn" class="mode-tab-btn">
                            <i class="fas fa-barcode me-1"></i> ماسح الباركود / يدوي
                        </button>
                    </div>
                </div>

                {{-- منطقة الكاميرا والمسح البصري --}}
                <div id="camera-scan-container" class="scanner-viewport-box mb-3 position-relative">
                    
                    {{-- شاشة الكاميرا المغلقة (الحالة الافتراضية) --}}
                    <div id="camera-idle-view">
                        <div style="font-size: 3.5rem; margin-bottom: 0.75rem;">📷</div>
                        <h6 class="text-white fw-bold mb-2">وجّه كاميرا الجهاز نحو رمز الـ QR الخاص بالخريج</h6>
                        <p class="text-white-50 small mb-3 mx-auto" style="max-width: 440px;">
                            يقوم النظام بالتعرف التلقائي الفوري على بطاقة الزائر وتسجيل الحضور دون الحاجة لأي نقرة إضافية.
                        </p>
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            <button type="button" id="start-camera-btn" class="btn px-4 py-2" style="background-color: var(--terminal-accent); color: #06182e; font-weight: bold; border-radius: 50px; box-shadow: 0 4px 20px rgba(238,202,62,0.4); transition: transform 0.2s;">
                                <i class="fas fa-video me-2"></i> تشغيل الكاميرا الآن
                            </button>
                            <button type="button" id="upload-qr-file-btn" class="btn btn-outline-light px-3 py-2 rounded-pill small" title="رفع صورة أو لقطة شاشة لرمز QR">
                                <i class="fas fa-image me-1 text-warning"></i> مسح صورة QR
                            </button>
                            <input type="file" id="qr-file-input" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    {{-- شاشة تنبيه / خطأ الكاميرا مع التوجيه السهل للمستخدم --}}
                    <div id="camera-error-view" style="display: none;"></div>

                    {{-- شاشة الكاميرا النشطة --}}
                    <div id="camera-active-view" style="display: none;">
                        <div class="viewfinder-wrapper mb-3">
                            <div class="viewfinder-frame"></div>
                            <div class="corner-tl"></div>
                            <div class="corner-br"></div>
                            <div class="laser-scan-line"></div>
                            <div id="reader"></div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                            <select id="camera-select" class="form-select form-select-sm bg-dark text-white border-secondary rounded-pill px-3 py-1" style="max-width: 200px; font-size: 0.8rem; display: none;">
                            </select>
                            <button type="button" id="flip-camera-btn" class="btn btn-sm btn-outline-light rounded-pill px-3" style="display: none;">
                                <i class="fas fa-sync-alt me-1"></i> تبديل الكاميرا
                            </button>
                            <button type="button" id="stop-camera-btn" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="fas fa-stop me-1"></i> إيقاف الكاميرا
                            </button>
                            <button type="button" id="active-upload-qr-btn" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                <i class="fas fa-file-image me-1"></i> رفع صورة
                            </button>
                        </div>
                    </div>
                </div>

                {{-- حقل الإدخال السريع (يدعم قارئ الباركود اللاسلكي USB/Bluetooth أو الكتابة باليد) --}}
                <div class="mt-4">
                    <label class="text-white-50 small fw-bold mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-barcode text-warning me-1"></i> إدخال سريع / قارئ الباركود (Barcode Gun):</span>
                        <span class="badge bg-white bg-opacity-10 text-white-50 small">Enter للتأكيد</span>
                    </label>

                    <div class="barcode-input-group d-flex align-items-center">
                        <span class="ps-3 pe-2 text-warning"><i class="fas fa-keyboard fs-5"></i></span>
                        <input type="text" id="qr-input" 
                               placeholder="امسح الباركود، أو اكتب رقم التسجيل (JF1-0001) أو الرقم الوطني أو الإيميل..."
                               autocomplete="off" autocorrect="off" spellcheck="false" class="form-control">
                        <button type="button" id="manual-submit-btn" class="btn btn-warning rounded-3 px-3 py-2 fw-bold text-dark d-flex align-items-center gap-1">
                            <span>تحقق وتسجيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-2 text-white-50 small">
                        <span><i class="fas fa-lightbulb text-warning me-1"></i> قارئ الباركود يعمل مباشرة عند مسح أي بطاقة.</span>
                        <span id="scan-feedback-indicator" class="text-info" style="display: none;">
                            <i class="fas fa-circle-notch fa-spin me-1"></i> جاري التحقق...
                        </span>
                    </div>
                </div>

                {{-- بطاقات التفاعل اللحظية (Feedback Results) --}}
                
                {{-- 1. نجاح الحضور --}}
                <div class="status-feedback-card status-success" id="result-success">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-initials bg-success fs-4">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill small">✓ تم تأكيد الحضور</span>
                                    <span class="badge bg-white text-success border border-success-subtle" id="result-reg-num"></span>
                                    <span class="text-muted small" id="result-time"></span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1" id="result-name">اسم الخريج</h4>
                                <div class="text-muted small" id="result-major">التخصص والكلية</div>
                            </div>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <i class="fas fa-id-card text-success fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>

                {{-- 2. تحذير مسجل مسبقاً --}}
                <div class="status-feedback-card status-warning" id="result-warning">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-initials bg-warning text-dark fs-4">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small">⚠️ مسجل مسبقاً</span>
                                    <span class="badge bg-white text-warning border" id="result-warning-reg-num"></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" id="result-warning-name">اسم الخريج</h5>
                                <p class="mb-0 text-secondary small" id="result-warning-msg">دخل القاعة سابقاً في الساعة 10:45</p>
                            </div>
                        </div>
                        <div class="text-end d-none d-md-block">
                            <i class="fas fa-history text-warning fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>

                {{-- 3. خطأ في الرمز --}}
                <div class="status-feedback-card status-error" id="result-error">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-initials bg-danger fs-4">
                            <i class="fas fa-times"></i>
                        </div>
                        <div>
                            <span class="badge bg-danger text-white px-2 py-1 rounded-pill small mb-1">❌ رمز غير صالح</span>
                            <h5 class="fw-bold text-danger mb-1" id="result-error-title">لم يتم التعرف على التذكرة</h5>
                            <p class="mb-0 text-muted small" id="result-error-msg">تأكد من صحة الرمز أو ابحث عن الخريج في القائمة بالأسفل للتسجيل اليدوي.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- العمود الأيسر: شريط النشاط اللحظي ودليل المسجلين --}}
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-3 p-md-4 d-flex flex-column">
                    
                    {{-- ترويسة التبويبات --}}
                    <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 py-2 fw-bold small" id="pills-live-tab" data-bs-toggle="pill" data-bs-target="#pills-live" type="button" role="tab">
                                <i class="fas fa-bolt text-warning me-1"></i>
                                البث اللحظي للدخول
                                <span class="badge bg-primary text-white rounded-pill ms-1" id="session-checkins-badge">
                                    {{ count($recentCheckins) }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 py-2 fw-bold small" id="pills-directory-tab" data-bs-toggle="pill" data-bs-target="#pills-directory" type="button" role="tab">
                                <i class="fas fa-address-book text-primary me-1"></i>
                                دليل المسجلين والبحث
                            </button>
                        </li>
                    </ul>

                    {{-- محتوى التبويبات --}}
                    <div class="tab-content flex-grow-1">
                        
                        {{-- تبويب 1: البث اللحظي للحضور --}}
                        <div class="tab-pane fade show active" id="pills-live" role="tabpanel">
                            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                                <span class="text-muted small fw-bold">آخر عمليات الدخول المسجلة:</span>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none text-muted p-0 small" onclick="clearSessionHistory()">
                                    <i class="fas fa-broom me-1"></i> مسح سجل العرض
                                </button>
                            </div>

                            <div class="feed-scroll-box pe-1" id="scan-history-feed">
                                @forelse($recentCheckins as $checkin)
                                    @php
                                        $initial = mb_substr($checkin->graduate?->name ?? 'ز', 0, 1);
                                        $colors = ['#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2'];
                                        $bgColor = $colors[abs(crc32($checkin->graduate?->name ?? 'ز')) % count($colors)];
                                        $timeText = $checkin->check_in_at ? $checkin->check_in_at->format('h:i A') : ($checkin->updated_at ? $checkin->updated_at->format('h:i A') : 'الآن');
                                    @endphp
                                    <div class="attendee-feed-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                                            <div class="avatar-initials" style="background-color: {{ $bgColor }}">
                                                {{ $initial }}
                                            </div>
                                            <div class="text-truncate">
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-dark text-truncate">{{ $checkin->graduate?->name ?? 'خريج مسجل' }}</strong>
                                                    <span class="badge bg-light text-secondary border small">{{ $checkin->registration_number ?? 'JF' }}</span>
                                                </div>
                                                <small class="text-muted text-truncate d-block">
                                                    {{ $checkin->graduate?->major ?? 'تخصص عام' }}
                                                    @if($checkin->graduate?->faculty)
                                                        — {{ $checkin->graduate?->faculty }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end ps-2 flex-shrink-0">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                                <i class="fas fa-check-circle me-1"></i> {{ $timeText }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5" id="empty-history-placeholder">
                                        <div class="text-muted opacity-50 mb-3" style="font-size: 3rem;">
                                            <i class="fas fa-clipboard-check"></i>
                                        </div>
                                        <h6 class="text-dark fw-bold mb-1">لا توجد عمليات دخول حتى الآن</h6>
                                        <p class="text-muted small mb-0">ستظهر بطاقات الخريجين هنا فور مسح تذاكرهم في البوابة.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- تبويب 2: دليل المسجلين والبحث المباشر والتسجيل اليدوي --}}
                        <div class="tab-pane fade" id="pills-directory" role="tabpanel">
                            
                            {{-- حقل تصفية المسجلين --}}
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="directory-search-input" 
                                       class="form-control border-start-0" 
                                       placeholder="ابحث بالاسم، التخصص، أو رقم التسجيل...">
                            </div>

                            <div class="feed-scroll-box pe-1" id="directory-list-container">
                                @forelse($registeredAttendees as $reg)
                                    @php
                                        $initial = mb_substr($reg->graduate?->name ?? 'ز', 0, 1);
                                        $colors = ['#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2'];
                                        $bgColor = $colors[abs(crc32($reg->graduate?->name ?? 'ز')) % count($colors)];
                                    @endphp
                                    <div class="attendee-feed-item directory-item d-flex align-items-center justify-content-between"
                                         data-search="{{ strtolower($reg->graduate?->name . ' ' . $reg->graduate?->major . ' ' . $reg->registration_number . ' ' . $reg->graduate?->national_id) }}">
                                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                                            <div class="avatar-initials" style="background-color: {{ $bgColor }}">
                                                {{ $initial }}
                                            </div>
                                            <div class="text-truncate">
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-dark text-truncate">{{ $reg->graduate?->name ?? 'زائر مسجل' }}</strong>
                                                    <span class="badge bg-light text-secondary border small">{{ $reg->registration_number }}</span>
                                                </div>
                                                <small class="text-muted text-truncate d-block">
                                                    {{ $reg->graduate?->major ?? 'تخصص عام' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end ps-2 flex-shrink-0" id="reg-action-container-{{ $reg->user_id }}">
                                            @if($reg->attended)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                                    <i class="fas fa-check-circle me-1"></i> حاضر
                                                </span>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 quick-checkin-btn"
                                                        data-graduate-id="{{ $reg->user_id }}"
                                                        data-name="{{ $reg->graduate?->name }}">
                                                    <i class="fas fa-user-check me-1"></i> تسجيل يدوي
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <p class="text-muted small mb-0">لا يوجد مسجلين في هذا المعرض حالياً.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Endpoints & Constants
    const checkInUrl = "{{ route('job-fair.admin.check-in') }}";
    const csrfToken = "{{ csrf_token() }}";
    const fairId = {{ $fair->id }};

    // DOM Elements
    const qrInput = document.getElementById('qr-input');
    const manualSubmitBtn = document.getElementById('manual-submit-btn');
    const scanIndicator = document.getElementById('scan-feedback-indicator');
    const statAttendedEl = document.getElementById('stat-attended');
    const statRemainingEl = document.getElementById('stat-remaining');
    const statPctEl = document.getElementById('stat-pct');
    const statProgressBar = document.getElementById('stat-progress-bar');
    const sessionBadge = document.getElementById('session-checkins-badge');
    const feedContainer = document.getElementById('scan-history-feed');
    const soundToggleBtn = document.getElementById('sound-toggle-btn');
    const soundIcon = document.getElementById('sound-icon');
    const soundText = document.getElementById('sound-text');

    // Camera Mode Elements
    const tabCameraBtn = document.getElementById('tab-camera-btn');
    const tabManualBtn = document.getElementById('tab-manual-btn');
    const cameraScanContainer = document.getElementById('camera-scan-container');
    const cameraIdleView = document.getElementById('camera-idle-view');
    const cameraActiveView = document.getElementById('camera-active-view');
    const cameraErrorView = document.getElementById('camera-error-view');
    const startCameraBtn = document.getElementById('start-camera-btn');
    const stopCameraBtn = document.getElementById('stop-camera-btn');
    const flipCameraBtn = document.getElementById('flip-camera-btn');
    const cameraSelect = document.getElementById('camera-select');
    const uploadQrFileBtn = document.getElementById('upload-qr-file-btn');
    const activeUploadQrBtn = document.getElementById('active-upload-qr-btn');
    const qrFileInput = document.getElementById('qr-file-input');

    // Internal State
    let attendedCount = {{ $stats['total_attended'] ?? 0 }};
    let totalRegistered = {{ $stats['total_registered'] ?? 0 }};
    let soundEnabled = true;
    let html5QrcodeScanner = null;
    let isCameraRunning = false;
    let isTransitioning = false;  // guard against concurrent start/stop calls
    let isProcessing = false;
    let availableCameras = [];
    let currentCameraId = null;
    let currentFacingMode = "environment";
    let autoHideTimeout = null;

    // Focus on barcode input initially
    qrInput.focus();

    // Mode Switching
    tabCameraBtn.addEventListener('click', function() {
        tabCameraBtn.classList.add('active');
        tabManualBtn.classList.remove('active');
        cameraScanContainer.style.display = 'block';
    });

    tabManualBtn.addEventListener('click', function() {
        tabManualBtn.classList.add('active');
        tabCameraBtn.classList.remove('active');
        qrInput.focus();
        qrInput.select();
    });

    // Sound Toggle
    soundToggleBtn.addEventListener('click', function() {
        soundEnabled = !soundEnabled;
        if (soundEnabled) {
            soundIcon.className = 'fas fa-volume-up text-primary me-1';
            soundText.textContent = 'صوت التنبيه: مفعّل';
            soundToggleBtn.classList.remove('btn-outline-danger');
            soundToggleBtn.classList.add('btn-outline-secondary');
            playBeep('success');
        } else {
            soundIcon.className = 'fas fa-volume-mute text-danger me-1';
            soundText.textContent = 'صوت التنبيه: مكتوم';
            soundToggleBtn.classList.remove('btn-outline-secondary');
            soundToggleBtn.classList.add('btn-outline-danger');
        }
    });

    // Barcode Gun / Manual Enter Listener
    qrInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            if (val) {
                processCode(val);
                this.value = '';
            }
        }
    });

    manualSubmitBtn.addEventListener('click', function () {
        const val = qrInput.value.trim();
        if (val) {
            processCode(val);
            qrInput.value = '';
            qrInput.focus();
        }
    });

    // Camera Start / Stop / Switch Listeners
    startCameraBtn.addEventListener('click', () => startCamera());
    stopCameraBtn.addEventListener('click', () => stopCamera());

    if (flipCameraBtn) {
        flipCameraBtn.addEventListener('click', async function() {
            if (availableCameras.length > 1) {
                const currentIndex = availableCameras.findIndex(c => c.id === currentCameraId);
                const nextIndex = (currentIndex + 1) % availableCameras.length;
                currentCameraId = availableCameras[nextIndex].id;
                if (cameraSelect) cameraSelect.value = currentCameraId;
                await stopCamera(true);
                await startCamera();
            } else {
                currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
                await stopCamera(true);
                await startCamera();
            }
        });
    }

    if (cameraSelect) {
        cameraSelect.addEventListener('change', async function() {
            currentCameraId = this.value;
            if (isCameraRunning) {
                await stopCamera(true);
                await startCamera();
            }
        });
    }

    // QR Image File Upload Fallback
    const triggerFileInput = () => { if (qrFileInput) qrFileInput.click(); };
    if (uploadQrFileBtn) uploadQrFileBtn.addEventListener('click', triggerFileInput);
    if (activeUploadQrBtn) activeUploadQrBtn.addEventListener('click', triggerFileInput);

    if (qrFileInput) {
        qrFileInput.addEventListener('change', function(e) {
            if (!e.target.files || e.target.files.length === 0) return;
            const file = e.target.files[0];
            // Need a temporary scanner just for file scanning (not the camera instance)
            const fileScannerEl = document.getElementById('reader');
            const tempScanner = new Html5Qrcode('reader');
            scanIndicator.style.display = 'inline-block';
            tempScanner.scanFile(file, true)
                .then(decodedText => {
                    scanIndicator.style.display = 'none';
                    processCode(decodedText);
                })
                .catch(err => {
                    scanIndicator.style.display = 'none';
                    console.error('Scan file error:', err);
                    showResult('error', {
                        title: 'تعذر قراءة الرمز من الصورة',
                        msg: 'تأكد من وضوح رمز الـ QR داخل الصورة أو ابحث عن الخريج في دليل المسجلين بالأسفل.'
                    });
                    playBeep('error');
                })
                .finally(() => {
                    qrFileInput.value = '';
                    try { tempScanner.clear(); } catch (_) {}
                });
        });
    }

    // Destroy scanner instance fully to clear Html5Qrcode internal state machine
    async function destroyScanner() {
        if (!html5QrcodeScanner) return;
        try {
            if (isCameraRunning) {
                await html5QrcodeScanner.stop();
            }
        } catch (e) {
            console.warn('destroyScanner stop error (ignored):', e);
        }
        try {
            await html5QrcodeScanner.clear();
        } catch (e) {
            console.warn('destroyScanner clear error (ignored):', e);
        }
        html5QrcodeScanner = null;
        isCameraRunning = false;
    }

    async function startCamera() {
        // Prevent concurrent transitions
        if (isTransitioning) {
            console.warn('Camera already transitioning, ignoring request.');
            return;
        }
        isTransitioning = true;
        hideCameraError();

        // Check if mediaDevices is supported (requires HTTPS or localhost)
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            isTransitioning = false;
            showCameraError({
                name: 'NotSupportedError',
                message: 'متصفحك أو هذا الرابط لا يدعم ميزة فتح الكاميرا مباشرة. يتطلب ذلك اتصالاً آمناً (HTTPS) أو النطاق المحلي (localhost).'
            });
            return;
        }

        const origBtnHtml = startCameraBtn.innerHTML;
        startCameraBtn.disabled = true;
        startCameraBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري فتح الكاميرا...';

        try {
            // Always destroy any existing instance first
            await destroyScanner();

            // CRITICAL: Show the active view BEFORE creating Html5Qrcode instance.
            // The library computes #reader's dimensions on instantiation — if the
            // element is inside a display:none parent it gets zero size and the
            // video stream never renders.
            cameraIdleView.style.display = 'none';
            cameraActiveView.style.display = 'block';

            // Give the browser one animation frame to lay out the newly-visible div
            await new Promise(resolve => requestAnimationFrame(resolve));

            // Create a fresh instance every time
            html5QrcodeScanner = new Html5Qrcode('reader');

            const config = {
                fps: 15,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    const edgeSize = Math.max(160, Math.floor(minEdge * 0.72));
                    return { width: edgeSize, height: edgeSize };
                },
                aspectRatio: 1.0,
                disableFlip: false
            };

            const onScanSuccess = (decodedText) => {
                if (!isProcessing) {
                    processCode(decodedText);
                }
            };

            // ── STEP 1: Get camera permission via raw getUserMedia ──
            // Triggers Chrome's permission prompt. Stream is stopped immediately;
            // we only need the permission grant so getCameras() returns real IDs.
            if (!currentCameraId) {
                let permStream = null;
                try {
                    permStream = await navigator.mediaDevices.getUserMedia({ video: true });
                } finally {
                    if (permStream) permStream.getTracks().forEach(t => t.stop());
                }

                // ── STEP 2: Enumerate cameras now that permission is granted ──
                try {
                    const devices = await Html5Qrcode.getCameras();
                    availableCameras = devices || [];
                } catch (_) { availableCameras = []; }

                if (availableCameras.length > 0) {
                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    const rearCam = availableCameras.find(c => /back|rear|environment|خلفية/i.test(c.label || ''));
                    currentCameraId = (isMobile && rearCam) ? rearCam.id : availableCameras[0].id;

                    if (availableCameras.length > 1 && cameraSelect) {
                        cameraSelect.innerHTML = '';
                        availableCameras.forEach((dev, idx) => {
                            const opt = document.createElement('option');
                            opt.value = dev.id;
                            opt.textContent = dev.label || `كاميرا ${idx + 1}`;
                            cameraSelect.appendChild(opt);
                        });
                        cameraSelect.value = currentCameraId;
                        cameraSelect.style.display = 'inline-block';
                        if (flipCameraBtn) flipCameraBtn.style.display = 'inline-block';
                    }
                }
            }

            // ── STEP 3: Start scanner with device ID ──
            // Using device ID avoids facingMode OverconstrainedError on built-in
            // webcams (HP TrueVision etc.) that don't expose a facingMode capability.
            if (currentCameraId) {
                await html5QrcodeScanner.start(currentCameraId, config, onScanSuccess);
            } else {
                // Absolute fallback when no device ID could be obtained
                await html5QrcodeScanner.start({ facingMode: 'user' }, config, onScanSuccess);
            }

            isCameraRunning = true;

        } catch (err) {
            console.error('Camera start failed:', err);
            isCameraRunning = false;
            try { if (html5QrcodeScanner) await html5QrcodeScanner.clear(); } catch (_) {}
            html5QrcodeScanner = null;
            cameraActiveView.style.display = 'none';
            cameraIdleView.style.display = 'none';
            showCameraError(err);
        } finally {
            isTransitioning = false;
            startCameraBtn.disabled = false;
            startCameraBtn.innerHTML = origBtnHtml;
        }
    }

    async function stopCamera(keepActiveView = false) {
        if (isTransitioning) {
            console.warn('Camera already transitioning, skipping stop.');
            return;
        }
        isTransitioning = true;
        try {
            await destroyScanner();
        } finally {
            isTransitioning = false;
        }
        if (!keepActiveView) {
            cameraActiveView.style.display = 'none';
            cameraIdleView.style.display = 'block';
        }
    }

    function showCameraError(err) {
        cameraIdleView.style.display = 'none';
        cameraActiveView.style.display = 'none';
        if (!cameraErrorView) return;

        const errName = err ? (err.name || '') : '';
        const errMsg = err ? (err.message || String(err)) : '';
        // Check OverconstrainedError BEFORE permission check — it can contain the word "denied"
        const isOverconstrained = /OverconstrainedError|Overconstrained|AbortError/i.test(errName);
        const isPermissionDenied = !isOverconstrained && /NotAllowedError|PermissionDeniedError/i.test(errName);
        const isNotFound = isOverconstrained || /NotFoundError|DevicesNotFoundError/i.test(errName);

        let html = '';
        if (isPermissionDenied) {
            html = `
                <div class="text-center p-4">
                    <div class="text-warning mb-3" style="font-size: 3.2rem;">
                        <i class="fas fa-video-slash"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">تم رفض إذن الوصول إلى الكاميرا</h5>
                    <p class="text-white-50 small mb-3 mx-auto" style="max-width: 440px; line-height: 1.6;">
                        حظر المتصفح استخدام الكاميرا. لتفعيلها: انقر على أيقونة القفل أو الكاميرا (<i class="fas fa-lock text-warning"></i>) في شريط عنوان المتصفح بالأعلى، ثم اختر <strong>"السماح دائماً للكاميرا"</strong> وأعد المحاولة.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" id="btn-retry-camera" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark">
                            <i class="fas fa-redo me-1"></i> إعادة المحاولة
                        </button>
                        <button type="button" id="btn-error-upload-qr" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-file-image me-1 text-warning"></i> مسح صورة QR بديلة
                        </button>
                        <button type="button" id="btn-close-camera-error" class="btn btn-link text-white-50 btn-sm text-decoration-none">
                            إلغاء
                        </button>
                    </div>
                </div>
            `;
        } else if (isNotFound) {
            html = `
                <div class="text-center p-4">
                    <div class="text-white-50 mb-3" style="font-size: 3.2rem;">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">لم يتم العثور على كاميرا متصلة</h5>
                    <p class="text-white-50 small mb-3 mx-auto" style="max-width: 440px; line-height: 1.6;">
                        لا توجد كاميرا ويب متصلة أو أن الكاميرا قيد الاستخدام من تطبيق آخر. يمكنك استخدام قارئ الباركود اللاسلكي أو رفع صورة الـ QR مباشرة.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" id="btn-focus-barcode" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark">
                            <i class="fas fa-barcode me-1"></i> استخدام قارئ الباركود
                        </button>
                        <button type="button" id="btn-error-upload-qr" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-file-image me-1 text-warning"></i> رفع صورة QR
                        </button>
                        <button type="button" id="btn-close-camera-error" class="btn btn-link text-white-50 btn-sm text-decoration-none">
                            إلغاء
                        </button>
                    </div>
                </div>
            `;
        } else {
            html = `
                <div class="text-center p-4">
                    <div class="text-danger mb-3" style="font-size: 3.2rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">تعذر تشغيل الكاميرا</h5>
                    <p class="text-white-50 small mb-3 mx-auto" style="max-width: 440px; line-height: 1.6;">
                        ${errMsg || 'حدث خطأ غير متوقع أثناء محاولة تشغيل الكاميرا.'}
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" id="btn-retry-camera" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark">
                            <i class="fas fa-redo me-1"></i> إعادة المحاولة
                        </button>
                        <button type="button" id="btn-error-upload-qr" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="fas fa-file-image me-1 text-warning"></i> رفع صورة QR
                        </button>
                        <button type="button" id="btn-close-camera-error" class="btn btn-link text-white-50 btn-sm text-decoration-none">
                            إلغاء
                        </button>
                    </div>
                </div>
            `;
        }

        cameraErrorView.innerHTML = html;
        cameraErrorView.style.display = 'block';

        // Bind error action buttons
        const retryBtn = document.getElementById('btn-retry-camera');
        if (retryBtn) retryBtn.addEventListener('click', () => startCamera());

        const uploadBtn = document.getElementById('btn-error-upload-qr');
        if (uploadBtn) uploadBtn.addEventListener('click', triggerFileInput);

        const closeBtn = document.getElementById('btn-close-camera-error');
        if (closeBtn) closeBtn.addEventListener('click', hideCameraError);

        const focusBarcodeBtn = document.getElementById('btn-focus-barcode');
        if (focusBarcodeBtn) focusBarcodeBtn.addEventListener('click', () => {
            hideCameraError();
            qrInput.focus();
        });
    }

    function hideCameraError() {
        if (cameraErrorView) cameraErrorView.style.display = 'none';
        cameraIdleView.style.display = 'block';
        cameraActiveView.style.display = 'none';
    }

    // Core Check-In Processor
    function processCode(rawCode, directGraduateId = null) {
        if (isProcessing) return;
        isProcessing = true;
        scanIndicator.style.display = 'inline-block';
        hideAllResults();

        let graduateId = directGraduateId;
        let queryCode = rawCode ? rawCode.trim() : '';

        // If not direct graduate ID, extract from standard QR patterns if applicable
        if (!graduateId && queryCode) {
            const patterns = [
                /\/graduate\/profile\/(\d+)/i,
                /\/profile\/(\d+)/i,
                /profile[\/=](\d+)/i,
                /[&?]id=(\d+)/i,
                /^JF-\d+-(\d+)-/i,
                /^(\d{1,6})$/
            ];
            for (const p of patterns) {
                const match = queryCode.match(p);
                if (match) {
                    graduateId = match[1];
                    break;
                }
            }
        }

        const payload = {
            job_fair_id: fairId
        };
        if (graduateId) {
            payload.graduate_id = parseInt(graduateId);
        }
        if (queryCode) {
            payload.code = queryCode;
        }

        fetch(checkInUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async (response) => {
            const data = await response.json();
            isProcessing = false;
            scanIndicator.style.display = 'none';

            if (!response.ok || !data.success) {
                showResult('error', {
                    title: 'لم يتم العثور على تسجيل مطابق',
                    msg: data.message || 'تأكد من رقم التذكرة أو ابحث عن الخريج في دليل المسجلين.'
                });
                playBeep('error');
                return;
            }

            if (data.already_in) {
                showResult('warning', {
                    name: data.graduate,
                    regNum: data.registration_number,
                    msg: `تم تسجيل دخوله مسبقاً في الساعة ${data.check_in_at}`
                });
                playBeep('warning');
            } else {
                showResult('success', {
                    name: data.graduate,
                    major: data.major + (data.faculty ? ' — ' + data.faculty : ''),
                    regNum: data.registration_number,
                    time: data.check_in_at
                });
                playBeep('success');

                // Update Local KPIs
                attendedCount++;
                statAttendedEl.textContent = attendedCount;
                const remaining = Math.max(0, totalRegistered - attendedCount);
                statRemainingEl.textContent = remaining;
                const pct = totalRegistered > 0 ? Math.round((attendedCount / totalRegistered) * 100) : 0;
                statPctEl.textContent = pct + '%';
                statProgressBar.style.width = pct + '%';

                // Add to live stream
                prependToHistory(data);

                // Update Directory Button if visible
                if (data.user_id) {
                    const actionContainer = document.getElementById('reg-action-container-' + data.user_id);
                    if (actionContainer) {
                        actionContainer.innerHTML = `
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                <i class="fas fa-check-circle me-1"></i> حاضر
                            </span>
                        `;
                    }
                }
            }

            // Refocus barcode input
            qrInput.focus();
        })
        .catch((err) => {
            console.error("Check-in error:", err);
            isProcessing = false;
            scanIndicator.style.display = 'none';
            showResult('error', {
                title: 'خطأ في الاتصال بالخادم',
                msg: 'حدث خطأ أثناء معالجة الطلب، يرجى المحاولة مرة أخرى.'
            });
            playBeep('error');
            qrInput.focus();
        });
    }

    // Dynamic Result Display
    function showResult(type, payload) {
        hideAllResults();
        if (autoHideTimeout) clearTimeout(autoHideTimeout);

        if (type === 'success') {
            document.getElementById('result-name').textContent = payload.name;
            document.getElementById('result-major').textContent = payload.major;
            document.getElementById('result-reg-num').textContent = payload.regNum || 'JF';
            document.getElementById('result-time').textContent = payload.time ? 'دخول الساعة: ' + payload.time : '';
            document.getElementById('result-success').style.display = 'block';
        } else if (type === 'warning') {
            document.getElementById('result-warning-name').textContent = payload.name;
            document.getElementById('result-warning-reg-num').textContent = payload.regNum || 'JF';
            document.getElementById('result-warning-msg').textContent = payload.msg;
            document.getElementById('result-warning').style.display = 'block';
        } else if (type === 'error') {
            document.getElementById('result-error-title').textContent = payload.title || 'رمز غير صالح';
            document.getElementById('result-error-msg').textContent = payload.msg || 'تأكد من صحة الرمز وحاول مجدداً';
            document.getElementById('result-error').style.display = 'block';
        }

        // Auto hide after 5 seconds
        autoHideTimeout = setTimeout(hideAllResults, 5000);
    }

    function hideAllResults() {
        ['success', 'warning', 'error'].forEach(t => {
            const el = document.getElementById('result-' + t);
            if (el) el.style.display = 'none';
        });
    }

    // Prepend to Live Stream
    function prependToHistory(data) {
        const placeholder = document.getElementById('empty-history-placeholder');
        if (placeholder) placeholder.remove();

        const initial = (data.graduate || 'ز').charAt(0);
        const colors = ['#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#0891b2'];
        const bgColor = colors[Math.floor(Math.random() * colors.length)];
        const timeStr = data.check_in_at || 'الآن';

        const itemHtml = `
            <div class="attendee-feed-item just-added d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="avatar-initials" style="background-color: ${bgColor}">
                        ${initial}
                    </div>
                    <div class="text-truncate">
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-dark text-truncate">${data.graduate}</strong>
                            <span class="badge bg-light text-secondary border small">${data.registration_number || 'JF'}</span>
                        </div>
                        <small class="text-muted text-truncate d-block">
                            ${data.major || 'تخصص عام'} ${data.faculty ? '— ' + data.faculty : ''}
                        </small>
                    </div>
                </div>
                <div class="text-end ps-2 flex-shrink-0">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                        <i class="fas fa-check-circle me-1"></i> ${timeStr}
                    </span>
                </div>
            </div>
        `;

        feedContainer.insertAdjacentHTML('afterbegin', itemHtml);
        
        // Update badge
        const currentCount = parseInt(sessionBadge.textContent || '0') + 1;
        sessionBadge.textContent = currentCount;
    }

    window.clearSessionHistory = function() {
        feedContainer.innerHTML = `
            <div class="text-center py-5" id="empty-history-placeholder">
                <div class="text-muted opacity-50 mb-3" style="font-size: 3rem;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h6 class="text-dark fw-bold mb-1">تم مسح سجل العرض المؤقت</h6>
                <p class="text-muted small mb-0">ستظهر عمليات الحضور الجديدة هنا مباشرة.</p>
            </div>
        `;
        sessionBadge.textContent = '0';
    };

    // Directory Search Filter
    const searchInput = document.getElementById('directory-search-input');
    const directoryItems = document.querySelectorAll('.directory-item');

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        directoryItems.forEach(item => {
            const text = item.getAttribute('data-search') || '';
            if (!query || text.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // 1-Click Manual Check-in buttons in Directory
    document.querySelectorAll('.quick-checkin-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const gradId = this.getAttribute('data-graduate-id');
            const name = this.getAttribute('data-name');
            if (gradId && confirm(`هل تريد تأكيد تسجيل الحضور اليدوي لـ (${name})؟`)) {
                processCode(null, gradId);
            }
        });
    });

    // Web Audio Synthesizer for instant feedback tones
    function playBeep(type) {
        if (!soundEnabled) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'success') {
                // Happy high double-beep
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(800, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.12);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.18);
            } else if (type === 'warning') {
                // Medium double alert tone
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.25);
            } else {
                // Low error buzz
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(180, ctx.currentTime);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch (e) {
            console.warn("Audio Context warning:", e);
        }
    }
});
</script>
@endpush
