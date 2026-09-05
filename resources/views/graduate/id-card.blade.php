@extends('layouts.app')

@section('title', 'بطاقة الخريج الرقمية الموحدة')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="بطاقة الخريج الرقمية الموحدة"
        subtitle="الهوية الرسمية الدائمة للخريج — صالحة للاستخدام في كافة الدورات التدريبية، معارض التوظيف، والمشاريع والفعاليات المعتمدة بمكتب تدريب وتأهيل الخريجين بجامعة طرابلس."
        icon="fas fa-id-card-clip"
        :breadcrumbs="[
            ['label' => 'لوحة التحكم', 'url' => route('graduate.dashboard')],
            ['label' => 'بطاقتي الرقمية']
        ]"
        badge="هوية معتمدة مدى الحياة"
        badgeIcon="fas fa-shield-check"
    >
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button onclick="downloadIdCard()" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2" id="download-btn">
                <i class="fas fa-download"></i>
                <span>حفظ كصورة (PNG)</span>
            </button>
            <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-print"></i>
                <span>طباعة الهوية</span>
            </button>
            <a href="{{ route('graduate.profile.public', $graduate->id) }}" target="_blank" class="btn btn-outline-light text-white fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-external-link-alt"></i>
                <span>الملف التعريفي العام ↗</span>
            </a>
        </div>
    </x-page-hero>

    <!-- تنبيه نجاح الحفظ -->
    <div id="saveAlert" class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-none text-center" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <strong>تم حفظ البطاقة بنجاح!</strong> تم تنزيل صورة الهوية الرقمية بجودة فائقة إلى جهازك.
    </div>

    <!-- حاوية التحميل أثناء التصدير -->
    <div id="exportOverlay" class="export-overlay d-none">
        <div class="spinner-border text-warning" role="status" style="width: 3.5rem; height: 3.5rem;"></div>
        <h5 class="text-white mt-3 fw-bold">جاري إعداد البطاقة الرقمية فائقة الدقة...</h5>
        <p class="text-white-50 small mb-0">يرجى الانتظار ثوانٍ قليلة لتجهيز ملف الصورة</p>
    </div>

    <!-- جسم البطاقة الرئيسية -->
    <div class="row justify-content-center my-4">
        <div class="col-12 col-md-8 col-lg-5 col-xl-4">

            <!-- الحاوية القابلة للطباعة والتصدير -->
            <div class="digital-id-card-wrapper" id="id-card-container">
                <div class="digital-id-card shadow-lg">

                    <!-- شريط التعليق (Lanyard Hole Cutout Effect) -->
                    <div class="card-lanyard-bar">
                        <div class="card-lanyard-hole"></div>
                    </div>

                    <!-- رأس الهوية الرقمية -->
                    <div class="card-header-banner">
                        <div class="header-ambient-glow"></div>

                        <!-- شعارات الهيئة والمشروع/المعرض -->
                        <div class="d-flex align-items-center justify-content-center gap-3 mb-2 logos-container">
                            <!-- شعار جامعة طرابلس والمكتب (دائم وأساسي) -->
                            <div class="logo-box main-office-logo" title="مكتب تدريب وتأهيل الخريجين — جامعة طرابلس">
                                <img src="{{ asset('storage/logo.jpg') }}" alt="شعار مكتب الخريجين" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                            </div>

                            <!-- في حالة وجود شعار لمشروع أو معرض نشط يتم إظهاره تلقائياً -->
                            @if(isset($eventContext) && !empty($eventContext['logo']))
                                <div class="logo-divider"></div>
                                <div class="logo-box event-project-logo" title="{{ $eventContext['title'] }}">
                                    <img src="{{ $eventContext['logo'] }}" alt="{{ $eventContext['title'] }}" onerror="this.style.display='none'">
                                </div>
                            @endif
                        </div>

                        <!-- اسم الجهة الراعية والفعالية -->
                        <h6 class="header-authority-title mb-1">
                            مكتب تدريب وتأهيل الخريجين
                        </h6>
                        <div class="header-authority-subtitle mb-2">
                            <span>جامعة طرابلس — University of Tripoli</span>
                        </div>

                        <!-- شارة الهوية / الفعالية التلقائية -->
                        <div class="mt-2">
                            @if(isset($eventContext))
                                <span class="badge rounded-pill event-context-badge">
                                    <i class="fas fa-sparkles text-warning me-1"></i>
                                    {{ $eventContext['title'] }}
                                </span>
                            @else
                                <span class="badge rounded-pill standard-id-badge">
                                    <i class="fas fa-shield-check text-success me-1"></i>
                                    هوية خريج رقمية معتمدة
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- جسم الهوية وبيانات الخريج -->
                    <div class="card-body-content">

                        <!-- أفاتار الخريج مع شارة التوثيق -->
                        <div class="graduate-avatar-section text-center">
                            <div class="avatar-frame mx-auto">
                                <div class="avatar-circle">
                                    <i class="fas fa-user-graduate fa-3x text-primary"></i>
                                </div>
                                <div class="verified-seal" title="خريج موثق بالنظام">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>

                            <h4 class="graduate-name mt-3 mb-1">{{ $graduate->name }}</h4>
                            <div class="graduate-degree-badge mb-2">
                                {{ $graduate->degree ?? 'بكالوريوس' }} — {{ $graduate->faculty ?? ($graduate->graduateData->faculty ?? 'جامعة طرابلس') }}
                            </div>

                            <!-- كود الخريج الموحد الدائم -->
                            <div class="graduate-universal-code">
                                <span class="code-label">كود القيد الموحد:</span>
                                <span class="code-value">UOT-GRAD-{{ str_pad($graduate->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <!-- شبكة البيانات الأكاديمية (Academic Bento Grid) -->
                        <div class="academic-info-grid">
                            <div class="info-cell">
                                <span class="info-cell-lbl"><i class="fas fa-id-card me-1 opacity-75"></i>الرقم الوطني / الجامعي</span>
                                <span class="info-cell-val">{{ $graduate->national_id ?? ($graduate->graduateData->national_id ?? ($graduate->graduateData->university_id ?? '—')) }}</span>
                            </div>

                            <div class="info-cell">
                                <span class="info-cell-lbl"><i class="fas fa-graduation-cap me-1 opacity-75"></i>التخصص الدقيق</span>
                                <span class="info-cell-val" title="{{ $graduate->major ?? ($graduate->graduateData->specialization ?? ($graduate->graduateData->department ?? 'غير محدد')) }}">
                                    {{ Str::limit($graduate->major ?? ($graduate->graduateData->specialization ?? ($graduate->graduateData->department ?? 'غير محدد')), 22) }}
                                </span>
                            </div>

                            <div class="info-cell">
                                <span class="info-cell-lbl"><i class="fas fa-calendar-check me-1 opacity-75"></i>سنة التخرج</span>
                                <span class="info-cell-val">{{ $graduate->graduation_year ?? ($graduate->graduateData->graduation_year ?? '—') }}</span>
                            </div>

                            <div class="info-cell">
                                <span class="info-cell-lbl"><i class="fas fa-award me-1 opacity-75"></i>المعدل التراكمي</span>
                                <span class="info-cell-val">{{ $graduate->gpa ? number_format($graduate->gpa, 2) . '%' : ($graduate->graduateData->gpa ? $graduate->graduateData->gpa . '%' : '—') }}</span>
                            </div>
                        </div>

                        <!-- سياق الفعالية أو التدريب النشط إن وُجد -->
                        @if(isset($eventContext))
                            <div class="context-banner mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-ticket text-warning"></i>
                                        <div>
                                            <span class="d-block fw-bold text-dark small">{{ $eventContext['badge'] }}</span>
                                            <span class="d-block text-muted" style="font-size: 0.72rem;">{{ $eventContext['location'] }}</span>
                                        </div>
                                    </div>
                                    <span class="badge {{ $eventContext['status'] == 'تم الحضور' ? 'bg-success' : 'bg-primary' }} rounded-pill px-2.5 py-1 small">
                                        {{ $eventContext['status'] }}
                                    </span>
                                </div>
                            </div>
                        @elseif(isset($activeTraining))
                            <div class="context-banner training-context mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-chalkboard-user text-primary"></i>
                                        <div>
                                            <span class="d-block fw-bold text-dark small">برنامج تدريبي معتمد</span>
                                            <span class="d-block text-muted" style="font-size: 0.72rem;">{{ Str::limit($activeTraining->training->title, 28) }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success rounded-pill px-2.5 py-1 small">متدرب نشط</span>
                                </div>
                            </div>
                        @else
                            <div class="context-banner general-context mt-3">
                                <div class="d-flex align-items-center gap-2 text-muted small">
                                    <i class="fas fa-circle-check text-success"></i>
                                    <span>بطاقة معتمدة لجميع تدريبات ومعارض ومشاريع المكتب</span>
                                </div>
                            </div>
                        @endif

                        <!-- رمز الـ QR الموحد والثابت -->
                        <div class="qr-code-holder text-center mt-3 pt-3 border-top">
                            <div class="qr-frame-box d-inline-block p-2.5 bg-white rounded-3 shadow-sm border">
                                <div id="universal-qr-code" class="d-flex justify-content-center"></div>
                            </div>
                            <div class="qr-caption mt-2">
                                <i class="fas fa-qrcode text-primary me-1"></i>
                                <span>امسح الرمز لتسجيل الحضور وتأكيد الهوية</span>
                            </div>
                            <div class="qr-hash-text">
                                UOT-QR-{{ strtoupper(substr(md5($graduate->id . $graduate->email), 0, 10)) }}-{{ $graduate->id }}
                            </div>
                        </div>

                    </div>

                    <!-- ذيل البطاقة الرسمي -->
                    <div class="card-footer-banner">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span>جامعة طرابلس — ليبيا</span>
                            <span class="fw-bold">بطاقة عضوية رقمية رسمية</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- Stylesheet للبطاقة الموحدة والشاشات والطباعة -->
<style>
    /* الحاوية الأساسية للبطاقة */
    .digital-id-card-wrapper {
        perspective: 1000px;
        margin: 0 auto;
    }

    .digital-id-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .digital-id-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px -12px rgba(13, 56, 130, 0.25) !important;
    }

    /* فتحة التعليق في أعلى البطاقة */
    .card-lanyard-bar {
        background: #092049;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-lanyard-hole {
        width: 46px;
        height: 9px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.35);
    }

    /* رأس البطاقة الملون */
    .card-header-banner {
        background: linear-gradient(135deg, #092049 0%, #0d3882 50%, #1565c0 100%);
        color: #ffffff;
        padding: 1.8rem 1.5rem 1.4rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .header-ambient-glow {
        position: absolute;
        top: -30%;
        left: -20%;
        width: 140%;
        height: 140%;
        background: radial-gradient(circle at 50% 20%, rgba(245, 158, 11, 0.18) 0%, transparent 60%);
        pointer-events: none;
    }

    .logos-container {
        position: relative;
        z-index: 2;
    }

    .logo-box {
        background: #ffffff;
        padding: 4px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .main-office-logo img {
        height: 48px;
        width: auto;
        border-radius: 8px;
        object-fit: contain;
    }

    .event-project-logo img {
        height: 46px;
        max-width: 110px;
        border-radius: 8px;
        object-fit: contain;
    }

    .logo-divider {
        width: 2px;
        height: 38px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }

    .header-authority-title {
        font-weight: 800;
        font-size: 1.15rem;
        color: #ffffff;
        letter-spacing: -0.2px;
        position: relative;
        z-index: 2;
    }

    .header-authority-subtitle {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.8);
        position: relative;
        z-index: 2;
    }

    .standard-id-badge {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 5px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        backdrop-filter: blur(6px);
    }

    .event-context-badge {
        background: rgba(245, 158, 11, 0.25);
        color: #fef08a;
        border: 1px solid rgba(245, 158, 11, 0.5);
        padding: 5px 14px;
        font-size: 0.82rem;
        font-weight: 800;
        backdrop-filter: blur(6px);
    }

    /* جسم البطاقة */
    .card-body-content {
        padding: 1.6rem 1.4rem;
        background: #ffffff;
    }

    .avatar-frame {
        position: relative;
        width: 82px;
        height: 82px;
        margin-top: -38px;
        margin-bottom: 8px;
    }

    .avatar-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        background: #eff6ff;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 14px rgba(13, 56, 130, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .verified-seal {
        position: absolute;
        bottom: 2px;
        left: 2px;
        width: 24px;
        height: 24px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
    }

    .graduate-name {
        font-weight: 900;
        color: #0f172a;
        font-size: 1.25rem;
    }

    .graduate-degree-badge {
        font-size: 0.85rem;
        color: #475569;
        font-weight: 600;
    }

    .graduate-universal-code {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 50px;
        padding: 4px 14px;
        font-size: 0.82rem;
    }

    .code-label {
        color: #1e40af;
        font-weight: 600;
    }

    .code-value {
        color: #0f172a;
        font-family: monospace;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    /* شبكة البيانات المصغرة */
    .academic-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 1.2rem;
    }

    .info-cell {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 12px;
        display: flex;
        flex-direction: column;
    }

    .info-cell-lbl {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .info-cell-val {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* بانر السياق */
    .context-banner {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 12px;
        padding: 8px 12px;
    }

    .context-banner.training-context {
        background: #f0fdf4;
        border-color: #dcfce7;
    }

    .context-banner.general-context {
        background: #f8fafc;
        border-color: #e2e8f0;
        text-align: center;
        justify-content: center;
        display: flex;
    }

    /* صندوق رمز QR */
    .qr-caption {
        font-size: 0.75rem;
        color: #475569;
        font-weight: 700;
    }

    .qr-hash-text {
        font-size: 0.68rem;
        color: #94a3b8;
        font-family: monospace;
        margin-top: 2px;
    }

    /* ذيل البطاقة */
    .card-footer-banner {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 10px 16px;
        color: #64748b;
    }

    /* حاوية التصدير */
    .export-overlay {
        position: fixed;
        inset: 0;
        background: rgba(11, 31, 58, 0.85);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* قواعد الطباعة الصارمة */
    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* إخفاء كافة عناصر الصفحة ما عدا البطاقة */
        nav, header, footer, .navbar, .sidebar, .x-page-hero, .btn, .alert, #download-btn {
            display: none !important;
        }

        .container-fluid, .row, .col-12, .col-md-8, .col-lg-5 {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .digital-id-card-wrapper {
            margin: 20px auto !important;
            width: 380px !important;
        }

        .digital-id-card {
            box-shadow: none !important;
            border: 2px solid #cbd5e1 !important;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @page {
            size: A6 portrait;
            margin: 8mm;
        }
    }
</style>

<!-- سكريبتات توليد QR وتصدير البطاقة كصورة -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. توليد رمز QR الموحد الدائم للخريج
    // يرتبط برابط الملف الشخصي العام أو كود الخريج ليتعرف عليه كل ماسحات النظام (تدريب، معارض، شركات)
    const qrContainer = document.getElementById('universal-qr-code');
    if (qrContainer) {
        new QRCode(qrContainer, {
            text: "{{ route('graduate.profile.public', $graduate->id) }}",
            width: 160,
            height: 160,
            colorDark : "#092049",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    }
});

// 2. دالة تنزيل وحفظ البطاقة كصورة عالية الدقة PNG
function downloadIdCard() {
    const card = document.getElementById('id-card-container');
    const overlay = document.getElementById('exportOverlay');
    const alertBox = document.getElementById('saveAlert');
    
    if (!card) return;

    overlay.classList.remove('d-none');
    overlay.style.display = 'flex';
    alertBox.classList.add('d-none');

    setTimeout(() => {
        html2canvas(card, {
            scale: 3.5, // دقة فائقة الوضوح (Ultra-HD) للطباعة وللهواتف الذكية
            useCORS: true,
            allowTaint: true,
            backgroundColor: null,
            logging: false,
        }).then(canvas => {
            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.download = 'Graduate_ID_{{ $graduate->id }}_{{ date("Y") }}.png';
                link.href = url;
                link.click();
                
                setTimeout(() => URL.revokeObjectURL(url), 200);
                
                overlay.classList.add('d-none');
                overlay.style.display = 'none';
                alertBox.classList.remove('d-none');
                
                // إخفاء التنبيه بعد 6 ثوانٍ
                setTimeout(() => {
                    alertBox.classList.add('d-none');
                }, 6000);
            }, 'image/png');
        }).catch(err => {
            console.error('Export error:', err);
            overlay.classList.add('d-none');
            overlay.style.display = 'none';
            alert('حدث خطأ أثناء تصدير البطاقة. يرجى المحاولة مرة أخرى أو استخدام زر الطباعة.');
        });
    }, 350);
}
</script>
@endpush
@endsection
