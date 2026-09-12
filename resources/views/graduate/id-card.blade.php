@extends('layouts.app')

@section('title', 'بطاقة الخريج الرقمية')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="بطاقة الخريج الرقمية الذكية"
        subtitle="الهوية الرسمية المعتمدة للخريج — صالحة للاستخدام في كافة الدورات التدريبية، معارض التوظيف، والمشاريع والفعاليات المعتمدة بمكتب تدريب وتأهيل الخريجين بجامعة طرابلس."
        icon="fas fa-id-badge"
        :breadcrumbs="[
            ['label' => 'منصة الخريجين', 'url' => route('graduate.dashboard')],
            ['label' => 'بطاقتي الرقمية']
        ]"
        badge="هوية معتمدة"
        badgeIcon="fas fa-check-circle"
    >
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button onclick="downloadIdCard()" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2" id="download-btn">
                <i class="fas fa-download"></i>
                <span>حفظ كصورة (PNG)</span>
            </button>
            <button onclick="window.print()" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-print"></i>
                <span>طباعة البطاقة</span>
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

    <!-- البطاقة الرئيسية بتصميمها المعتمد المحبوب -->
    <div class="row justify-content-center my-3">
        <div class="col-12 col-md-7 col-lg-5 col-xl-4">
            <div class="card-modern overflow-hidden p-0 shadow-lg border-0 rounded-4" id="id-card-container" style="background: #ffffff;">
                
                <!-- الجزء العلوي من البطاقة (Header Banner) -->
                <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #045db0 0%, #1e3a8a 100%);">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                        <!-- شعار مكتب تدريب الخريجين بجامعة طرابلس -->
                        <img src="{{ asset('storage/logo.jpg') }}" alt="شعار مكتب الخريجين" class="rounded-circle border border-3 border-white shadow-sm" style="width: 75px; height: 75px; object-fit: cover; background: #ffffff;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                        
                        <!-- في حال وجود سياق فعالية أو معرض، إظهار اللوقو الأبيض المعتمد -->
                        @if(isset($eventContext) && !empty($eventContext['logo']))
                            <div style="width: 1px; height: 48px; background: rgba(255,255,255,0.35);"></div>
                            <img src="{{ $eventContext['logo'] }}" alt="{{ $eventContext['title'] }}" style="height: 48px; width: auto; max-width: 120px; object-fit: contain;" onerror="this.src='{{ asset('images/job_fair_logo_white.png') }}'">
                        @endif
                    </div>

                    <h4 class="fw-bold mb-1 text-white" style="font-size: 1.35rem; letter-spacing: -0.3px;">{{ $graduate->name }}</h4>
                    <p class="mb-2 text-white-50 small fw-bold">خريج معتمد — جامعة طرابلس</p>

                    @if(isset($eventContext))
                        <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1.5 shadow-sm" style="font-size: 0.78rem;">
                            <i class="fas fa-star me-1 text-dark"></i>
                            {{ $eventContext['title'] }} • {{ $eventContext['badge'] }}
                        </span>
                    @else
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); font-size: 0.75rem;">
                            <i class="fas fa-shield-check me-1 text-success"></i> هوية رقمية معتمدة
                        </span>
                    @endif
                </div>

                <!-- الجزء الأوسط - المعلومات الأكاديمية والوطنية -->
                <div class="card-body p-4 bg-white">
                    <div class="row g-2.5 mb-3">
                        <!-- الرقم الجامعي / الوطني -->
                        <div class="col-12">
                            <div class="d-flex flex-column bg-light rounded-3 p-2.5 text-center border">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.78rem;">الرقم الوطني / الجامعي</span>
                                <span class="text-dark fw-bold fs-5" dir="ltr">{{ $graduate->graduateData->university_id ?? $graduate->graduateData->national_id ?? $graduate->national_id ?? '---' }}</span>
                            </div>
                        </div>

                        <!-- الكلية -->
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-2.5 text-center border h-100">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.78rem;">الكلية</span>
                                <span class="text-dark fw-bold" style="font-size: 0.88rem;">{{ $graduate->graduateData->faculty ?? $graduate->faculty ?? 'كلية تقنية المعلومات' }}</span>
                            </div>
                        </div>

                        <!-- القسم / التخصص -->
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-2.5 text-center border h-100">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.78rem;">القسم / التخصص</span>
                                <span class="text-dark fw-bold" style="font-size: 0.88rem;">{{ $graduate->graduateData->major ?? $graduate->graduateData->department ?? $graduate->major ?? 'هندسة البرمجيات' }}</span>
                            </div>
                        </div>

                        <!-- المعدل وسنة التخرج إذا توفرا -->
                        @if($graduate->graduateData && ($graduate->graduateData->gpa || $graduate->graduateData->graduation_year))
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-2 text-center border h-100">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.75rem;">المعدل التراكمي</span>
                                <span class="text-dark fw-bold" style="font-size: 0.88rem;" dir="ltr">{{ $graduate->graduateData->gpa ? $graduate->graduateData->gpa . '%' : '---' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column bg-light rounded-3 p-2 text-center border h-100">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.75rem;">سنة التخرج</span>
                                <span class="text-dark fw-bold" style="font-size: 0.88rem;">{{ $graduate->graduateData->graduation_year ?? '---' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- الـ QR Code مع الأقواس التأطيرية الأيقونية -->
                    <div class="text-center mt-3 pt-2 border-top">
                        <div class="d-inline-block p-3 bg-white border rounded-4 shadow-sm position-relative">
                            <!-- الأقواس الزرقاء المحيطة بالباركود -->
                            <div class="position-absolute top-0 start-0 w-25 h-25 border-top border-start border-primary border-3 rounded-top-4 rounded-start-4" style="margin: -2px;"></div>
                            <div class="position-absolute top-0 end-0 w-25 h-25 border-top border-end border-primary border-3 rounded-top-4 rounded-end-4" style="margin: -2px;"></div>
                            <div class="position-absolute bottom-0 start-0 w-25 h-25 border-bottom border-start border-primary border-3 rounded-bottom-4 rounded-start-4" style="margin: -2px;"></div>
                            <div class="position-absolute bottom-0 end-0 w-25 h-25 border-bottom border-end border-primary border-3 rounded-bottom-4 rounded-end-4" style="margin: -2px;"></div>
                            
                            <div id="qr-code" class="d-flex justify-content-center p-2"></div>
                        </div>
                        <p class="text-muted small mt-2.5 fw-bold mb-0" style="font-size: 0.82rem;">
                            <i class="fas fa-qrcode me-1 text-primary"></i>
                            امسح الرمز لتسجيل الحضور والتحقق من الهوية
                        </p>
                    </div>
                </div>

                <!-- الجزء السفلي من البطاقة (Footer) -->
                <div class="card-footer bg-light border-top text-center py-2.5">
                    <small class="text-muted fw-bold" style="font-size: 0.78rem;">
                        <i class="fas fa-university me-1 text-primary"></i> مكتب تدريب وتأهيل الخريجين — جامعة طرابلس
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #id-card-container, #id-card-container * {
            visibility: visible;
        }
        #id-card-container {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 380px;
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
        }
    }
</style>

<!-- مكتبات إنشاء QR Code وتصدير الصورة بجودة فائقة -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qrContainer = document.getElementById('qr-code');
        if (qrContainer) {
            new QRCode(qrContainer, {
                text: "{{ route('graduate.profile.public', $graduate->id) }}",
                width: 180,
                height: 180,
                colorDark: "#1e3a8a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });

    function downloadIdCard() {
        const cardElement = document.getElementById('id-card-container');
        const btn = document.getElementById('download-btn');
        if (!cardElement) return;

        const originalBtnHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>جاري التصدير...</span>';
        }

        html2canvas(cardElement, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'بطاقة-الخريج-{{ Str::slug($graduate->name) }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            const alertEl = document.getElementById('saveAlert');
            if (alertEl) {
                alertEl.classList.remove('d-none');
                setTimeout(() => alertEl.classList.add('d-none'), 4000);
            }
        }).catch(err => {
            console.error('Download error:', err);
            alert('حدث خطأ أثناء تنزيل الصورة، يرجى استخدام زر الطباعة.');
        }).finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
            }
        });
    }
</script>
@endsection
