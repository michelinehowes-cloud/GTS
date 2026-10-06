@extends('layouts.app')

@section('title', 'بطاقة الخريج الرقمية')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">

    <!-- Unified Page Hero Banner -->
    <x-page-hero
        title="بطاقة الخريج الرقمية الذكية"
        subtitle="الهوية الرسمية المعتمدة للخريج — صالحة للاستخدام في كافة الدورات التدريبية، معارض التوظيف، والمشاريع والفعاليات المعتمدة بمكتب تدريب الخريجين بجامعة طرابلس."
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

    {{-- ── شريط تبديل الفعاليات والهوية العامة بتصميم معتمد وأنيق ── --}}
    @if(isset($myRegistrations) && $myRegistrations->count() > 0)
    <div class="row justify-content-center mb-4">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm bg-white p-3 p-md-3.5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-layer-group fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">أنماط وتصاريح بطاقة الخريج</h6>
                            <small class="text-muted" style="font-size: 0.76rem;">اختر البطاقة لعرض شعارها أو إبراز تصريح الدخول عند البوابات</small>
                        </div>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                        <i class="fas fa-calendar-check text-success me-1"></i> {{ $myRegistrations->count() }} فعالية نشطة
                    </span>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-center gap-2.5">
                    {{-- خيار 1: الهوية العامة للجامعة (شعار المكتب فقط) --}}
                    @php $isGeneral = !isset($eventContext); @endphp
                    <a href="{{ route('graduate.id-card', ['mode' => 'general']) }}"
                       class="btn rounded-pill text-nowrap d-inline-flex align-items-center gap-2 px-3.5 py-2 transition-all {{ $isGeneral ? 'btn-primary shadow-sm border-primary fw-bold' : 'btn-light border text-dark fw-semibold hover-shadow' }}"
                       style="font-size: 0.86rem;">
                        <img src="{{ asset('storage/logo.jpg') }}" alt="شعار المكتب" class="rounded-circle border" style="width: 22px; height: 22px; object-fit: cover; background: #fff;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                        <span>الهوية العامة (شعار المكتب فقط)</span>
                        @if($isGeneral)
                            <i class="fas fa-check-circle text-warning ms-1"></i>
                        @endif
                    </a>

                    {{-- خيارات الفعاليات النشطة المسجل بها الخريج --}}
                    @foreach($myRegistrations as $reg)
                        @php
                            $isCurrentFair = isset($eventContext) && ($eventContext['fair_id'] == $reg->job_fair_id);
                        @endphp
                        <a href="{{ route('graduate.id-card', ['fair_id' => $reg->job_fair_id]) }}"
                           class="btn rounded-pill text-nowrap d-inline-flex align-items-center gap-2 px-3.5 py-2 transition-all {{ $isCurrentFair ? 'btn-warning text-dark shadow-sm border-warning fw-bold' : 'btn-light border text-dark fw-semibold hover-shadow' }}"
                           style="font-size: 0.86rem;">
                            <img src="{{ $reg->jobFair->logo_url ?? asset('storage/logo.jpg') }}" alt="{{ $reg->jobFair->title }}" class="rounded-circle bg-white border" style="width: 22px; height: 22px; object-fit: contain; padding: 1px;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                            <span>{{ $reg->jobFair->title }}</span>
                            <span class="badge {{ $isCurrentFair ? 'bg-dark text-white' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                #{{ $reg->registration_number }}
                            </span>
                            @if($isCurrentFair)
                                <i class="fas fa-check-circle text-dark ms-0.5"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- البطاقة الرئيسية بتصميمها المعتمد المحبوب -->
    <div class="row justify-content-center my-3">
        <div class="col-12 col-md-7 col-lg-5 col-xl-4">
            <div class="card-modern overflow-hidden p-0 shadow-lg border-0 rounded-4" id="id-card-container" style="background: #ffffff;">
                
                <!-- الجزء العلوي من البطاقة (Header Banner) -->
                <div class="p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #045db0 0%, #1e3a8a 100%);">
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-2.5">
                        <!-- شعار مكتب تدريب الخريجين بجامعة طرابلس الدائم -->
                        <img src="{{ asset('storage/logo.jpg') }}" alt="شعار مكتب الخريجين" class="rounded-circle border border-3 border-white shadow-sm" style="width: 72px; height: 72px; object-fit: cover; background: #ffffff;" onerror="this.src='{{ asset('images/logo.jpg') }}'">
                        
                        <!-- في حال وجود سياق فعالية محددة، يظهر شعار تلك الفعالية تحديداً بجوار شعار المكتب -->
                        @if(isset($eventContext) && !empty($eventContext['logo']))
                            <div style="width: 1.5px; height: 48px; background: rgba(255,255,255,0.35);"></div>
                            @if(!empty($eventContext['is_white_logo']))
                                <img src="{{ $eventContext['logo'] }}" alt="{{ $eventContext['title'] }}" style="height: 50px; width: auto; max-width: 125px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));" onerror="this.style.display='none';">
                            @else
                                <div class="bg-white rounded-3 p-1.5 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                    <img src="{{ $eventContext['logo'] }}" alt="{{ $eventContext['title'] }}" style="width: 46px; height: 46px; object-fit: contain;" onerror="this.parentElement.style.display='none';">
                                </div>
                            @endif
                        @endif
                    </div>

                    <h4 class="fw-bold mb-1 text-white" style="font-size: 1.35rem; letter-spacing: -0.3px;">{{ $graduate->name }}</h4>
                    <p class="mb-2 text-white-50 small fw-bold">خريج معتمد — جامعة طرابلس</p>

                    @if(isset($eventContext))
                        <span class="badge rounded-pill bg-warning text-dark fw-bold px-3 py-1.5 shadow-sm" style="font-size: 0.78rem;">
                            <i class="fas fa-calendar-check me-1 text-dark"></i>
                            {{ $eventContext['title'] }} • {{ $eventContext['badge'] }}
                        </span>
                        @if(!empty($eventContext['date']) || !empty($eventContext['location']))
                        <div class="text-white-50 small mt-1.5 fw-semibold" style="font-size: 0.74rem;">
                            @if(!empty($eventContext['location']))
                                <i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ $eventContext['location'] }}
                            @endif
                            @if(!empty($eventContext['date']) && !empty($eventContext['location']))
                                &nbsp;·&nbsp;
                            @endif
                            @if(!empty($eventContext['date']))
                                <i class="fas fa-calendar-day me-1 text-warning"></i>{{ $eventContext['date'] }}
                            @endif
                        </div>
                        @endif
                    @else
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35); font-size: 0.75rem;">
                            <i class="fas fa-shield-check me-1 text-success"></i> هوية رقمية معتمدة — جامعة طرابلس
                        </span>
                    @endif
                </div>

                <!-- الجزء الأوسط - المعلومات الأكاديمية -->
                <div class="card-body p-4 bg-white">
                    <div class="row g-2.5 mb-3">
                        <!-- الرقم الجامعي فقط بدون كلمة الوطني -->
                        <div class="col-12">
                            <div class="d-flex flex-column bg-light rounded-3 p-2.5 text-center border">
                                <span class="text-muted small fw-bold mb-0.5" style="font-size: 0.78rem;">الرقم الجامعي</span>
                                <span class="text-dark fw-bold fs-5" dir="ltr">{{ $graduate->graduateData->university_id ?? $graduate->national_id ?? '---' }}</span>
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
                            {{ isset($eventContext) ? 'امسح الرمز للدخول السريع وتسجيل الحضور بالمعرض' : 'امسح الرمز للتحقق من هوية الخريج الرسمية المعتمدة' }}
                        </p>
                    </div>
                </div>

                <!-- الجزء السفلي من البطاقة (Footer) -->
                <div class="card-footer bg-light border-top text-center py-2.5">
                    <small class="text-muted fw-bold" style="font-size: 0.78rem;">
                        <i class="fas fa-university me-1 text-primary"></i> مكتب تدريب الخريجين — جامعة طرابلس
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
                text: "{{ isset($eventContext) && !empty($eventContext['qr_data']) ? $eventContext['qr_data'] : route('graduate.profile.public', $graduate->id) }}",
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
