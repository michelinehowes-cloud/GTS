@extends('layouts.app')

@section('title', 'المعارض والفعاليات')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-4 text-white position-relative">
                    <h1 class="h3 fw-bold mb-2 position-relative z-index-1">
                        <i class="fas fa-calendar-star me-2" style="color: #fef08a;"></i> المعارض والفعاليات المشارك بها
                    </h1>
                    <p class="mb-0 text-white-50 position-relative z-index-1">
                        إدارة تواجد شركتك في المعارض والملتقيات والفعاليات ومتابعة المتقدمين بفعالية
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($fairs->isEmpty())
        <div class="alert bg-white border-0 shadow-sm rounded-4 text-center p-5">
            <div class="text-primary mb-3">
                <i class="fas fa-info-circle fa-4x opacity-50"></i>
            </div>
            <h4 class="fw-bold text-dark">لا توجد معارض أو فعاليات تشارك فيها شركتكم حالياً</h4>
            <p class="text-muted mb-0">تواصل مع إدارة الشراكات في الجامعة للانضمام للمعارض والفعاليات القادمة والوصول إلى أفضل الخريجين.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($fairs as $fair)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative transition-all hover-shadow" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                        <div class="card-header bg-white border-bottom pb-0 pt-4 px-4 border-0">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title fw-bold text-dark mb-2">{{ $fair->title }}</h5>
                                    @if($fair->status == 'published')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-semibold border border-success border-opacity-25">
                                            <i class="fas fa-door-open me-1"></i> مفتوح
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 fw-semibold border border-warning border-opacity-25">
                                            <i class="fas fa-lock me-1"></i> مسودة/مغلق
                                        </span>
                                    @endif
                                </div>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 48px; height: 48px;">
                                    <i class="fas fa-calendar-check fs-5"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body px-4 py-3 bg-white">
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3">
                                <div class="text-primary me-3">
                                    <i class="fas fa-map-marker-alt fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">الموقع</div>
                                    <strong class="text-dark">{{ $fair->location }}</strong>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                                <div class="text-primary me-3">
                                    <i class="fas fa-clock fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">تاريخ المعرض</div>
                                    <strong class="text-dark">{{ $fair->event_date->format('Y-m-d') }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-0">
                            {{-- زر الأصول الإعلامية والهوية البصرية --}}
                            <button type="button" class="btn btn-warning w-100 mb-2 rounded-pill py-2 fw-bold text-dark shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border: none;" data-bs-toggle="modal" data-bs-target="#mediaKitModal{{ $fair->id }}">
                                <i class="fas fa-photo-video"></i>
                                <span>الأصول الإعلامية وحزمة الهوية البصرية</span>
                            </button>

                            <a href="{{ route('company.job-fairs.qr-booth', $fair->id) }}" target="_blank" class="btn btn-dark w-100 mb-2 rounded-pill py-2 fw-semibold shadow-sm">
                                <i class="fas fa-print me-1"></i> طباعة باركود الجناح
                            </a>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold shadow-sm">
                                        <i class="fas fa-qrcode me-1"></i> ماسح السير
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold bg-white">
                                        <i class="fas fa-users me-1"></i> الخريجين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal: حزمة الأصول الإعلامية والهوية البصرية للشركة --}}
                <div class="modal fade" id="mediaKitModal{{ $fair->id }}" tabindex="-1" aria-labelledby="mediaKitModalLabel{{ $fair->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                            <div class="modal-header text-white p-4 border-0" style="background: linear-gradient(135deg, #0b192e 0%, #1e3a8a 100%);">
                                <div>
                                    <div class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">
                                        <i class="fas fa-palette me-1"></i> المواد الإعلامية الرسمية
                                    </div>
                                    <h4 class="modal-title fw-bold text-white mb-1" id="mediaKitModalLabel{{ $fair->id }}">
                                        الأصول الإعلامية والهوية البصرية — {{ $fair->title }}
                                    </h4>
                                    <p class="text-white-50 small mb-0">كافة الأصول والشعارات معتمدة وجاهزة للتحميل لاستخدامها في إعلاناتكم ومطبوعات الجناح</p>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4 bg-light">
                                {{-- إرشادات وتوجيهات الهوية البصرية من إدارة المعرض --}}
                                @if($fair->media_kit_description)
                                    <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4" style="background: rgba(30, 58, 138, 0.06); border-right: 4px solid #1e3a8a !important;">
                                        <h6 class="fw-bold text-primary mb-1">
                                            <i class="fas fa-info-circle me-1"></i> إرشادات وتوجيهات الهوية البصرية للمعرض:
                                        </h6>
                                        <p class="small text-dark mb-0" style="white-space: pre-line;">{{ $fair->media_kit_description }}</p>
                                    </div>
                                @endif

                                {{-- حزمة الـ ZIP الرئيسية --}}
                                <div class="card border-0 rounded-3 shadow-sm mb-4" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1.5px dashed #3b82f6 !important;">
                                    <div class="card-body p-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.6rem;">
                                                <i class="fas fa-file-archive"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-primary mb-1">الحقيبة الإعلامية الكاملة (All-in-One Media Kit ZIP)</h6>
                                                <p class="text-muted small mb-0">تحتوي على كافة الشعارات الرسمية (ملونة، أفقية، بيضاء شفافة)، ودليل استخدام الهوية البصرية.</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'media-kit']) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-nowrap shadow-sm">
                                            <i class="fas fa-download me-1"></i> تحميل الحزمة (ZIP)
                                        </a>
                                    </div>
                                </div>

                                {{-- شبكة الملفات الفردية --}}
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="fas fa-images text-warning me-1"></i> الشعارات والأصول الفردية بدقة عالية:
                                </h6>

                                <div class="row g-3 mb-4">
                                    {{-- شعار المعرض الرسمي --}}
                                    <div class="col-md-6">
                                        <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-light border rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <img src="{{ $fair->logo_url }}" alt="شعار المعرض" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo.png') }}';">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">شعار {{ $fair->title }}</div>
                                                    <small class="text-muted">النسخة الأساسية بالألوان الكاملة (PNG)</small>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 mt-auto">
                                                <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'fair-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1 fw-semibold">
                                                    <i class="fas fa-download me-1"></i> تحميل النسخة
                                                </a>
                                                <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'fair-logo-white']) }}" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold" title="النسخة البيضاء للخلفيات الداكنة">
                                                    <i class="fas fa-adjust me-1"></i> بيضاء شفافة
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- شعار المعرض الأفقي --}}
                                    <div class="col-md-6">
                                        <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-light border rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <img src="{{ $fair->horizontal_logo_url }}" alt="شعار المعرض أفقي" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_horizontal.png') }}';">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">شعار المعرض (نسخة أفقية)</div>
                                                    <small class="text-muted">للافتات الطولية والمواقع (PNG)</small>
                                                </div>
                                            </div>
                                            <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'fair-logo-horizontal']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                                <i class="fas fa-download me-1"></i> تحميل الشعار الأفقي
                                            </a>
                                        </div>
                                    </div>

                                    {{-- دليل الهوية البصرية (إذا توفر) --}}
                                    @if($fair->brand_guidelines_path)
                                    <div class="col-md-6">
                                        <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.6rem;">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">دليل استخدام الهوية البصرية</div>
                                                    <small class="text-muted">إرشادات الألوان والخطوط والأبعاد (PDF)</small>
                                                </div>
                                            </div>
                                            <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'brand-guidelines']) }}" class="btn btn-sm btn-outline-danger rounded-pill w-100 mt-auto fw-semibold" target="_blank">
                                                <i class="fas fa-download me-1"></i> تحميل دليل الهوية (PDF)
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- شعار مكتب تدريب الخريجين --}}
                                    <div class="col-md-6">
                                        <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-light border rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <img src="{{ asset('images/logo.jpg') }}" alt="مكتب الخريجين" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">شعار مكتب تدريب الخريجين</div>
                                                    <small class="text-muted">جامعة طرابلس (شعار رسمي عالي الدقة)</small>
                                                </div>
                                            </div>
                                            <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'office-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                                <i class="fas fa-download me-1"></i> تحميل شعار المكتب
                                            </a>
                                        </div>
                                    </div>

                                    {{-- شعار جامعة طرابلس --}}
                                    <div class="col-md-6">
                                        <div class="card border-0 rounded-3 shadow-sm h-100 bg-white p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="bg-dark rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <img src="{{ asset('images/uni_logo_white.png') }}" alt="جامعة طرابلس" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">شعار جامعة طرابلس</div>
                                                    <small class="text-muted">الجهة الأكاديمية الراعية والمنظمة</small>
                                                </div>
                                            </div>
                                            <a href="{{ route('company.job-fairs.download-asset', [$fair->id, 'university-logo']) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-auto fw-semibold">
                                                <i class="fas fa-download me-1"></i> تحميل شعار الجامعة
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- قائمة رعاة المعرض الرسميين --}}
                                @if($fair->sponsors && $fair->sponsors->count() > 0)
                                    <div class="p-3 bg-white rounded-3 shadow-sm border">
                                        <h6 class="fw-bold text-dark mb-2">
                                            <i class="fas fa-award text-warning me-1"></i> الرعاة الرسميون للمعرض:
                                        </h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($fair->sponsors as $sp)
                                                <span class="badge bg-light text-dark border p-2 rounded-pill d-inline-flex align-items-center gap-2">
                                                    <span class="badge bg-warning text-dark rounded-pill">{{ $sp->tier_label }}</span>
                                                    <strong>{{ $sp->name }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="modal-footer bg-white border-0 p-3">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <style>
            .hover-shadow:hover {
                transform: translateY(-5px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
            }
        </style>
    @endif
</div>
@endsection
