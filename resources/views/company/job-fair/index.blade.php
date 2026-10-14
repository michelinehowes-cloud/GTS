@extends('layouts.app')

@section('title', 'المعارض والفعاليات المهنية')

@section('content')
<div class="container-fluid px-2 px-md-3 py-3">
    <!-- الشريط الترحيبي المعتمد بنمط المنصة الموحد -->
    <x-page-hero
        title="المعارض والفعاليات المهنية"
        subtitle="إدارة تواجد شركتكم في ملتقيات التوظيف، وتفعيل ماسح السير الذاتية واستلام بيانات الخريجين المتميزين"
        icon="fas fa-calendar-alt"
        :breadcrumbs="[
            ['label' => 'لوحة تحكم الشركة', 'url' => route('company.dashboard')],
            ['label' => 'المعارض والفعاليات']
        ]"
        :badge="$fairs->count() > 0 ? $fairs->count() . ' ملتقى مشارك به' : null"
        badgeIcon="fas fa-handshake"
    >
        <a href="{{ route('company.dashboard') }}" class="btn btn-light bg-white text-primary fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-home fs-6"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="{{ route('job-opportunities.index') }}" class="btn btn-warning text-dark fw-bold py-2.5 px-3.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 flex-fill flex-sm-grow-0 text-nowrap" style="font-size: 0.88rem; transition: transform 0.2s ease;">
            <i class="fas fa-briefcase fs-6"></i>
            <span>إدارة الشواغر</span>
        </a>
    </x-page-hero>

    @if($fairs->isEmpty())
        <div class="card-modern text-center py-5">
            <div class="card-body py-5">
                <i class="fas fa-calendar-times display-3 text-muted mb-3 opacity-50"></i>
                <h4 class="fw-bold text-dark mb-2">لا توجد معارض أو فعاليات تشارك فيها شركتكم حالياً</h4>
                <p class="text-muted mb-0">تواصل مع إدارة الشراكات والتوظيف في الجامعة للانضمام للمعارض والفعاليات القادمة والوصول إلى أفضل الخريجين.</p>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($fairs as $fair)
                @php
                    $fairCompany = $fair->companies->first();
                    $boothNumber = $fair->my_booth ?? ($fairCompany?->booth_number);
                    $leadsCount = $fair->my_leads_count ?? 0;
                    $isPublished = $fair->status === 'published';
                @endphp
                <div class="col-12 col-md-6 col-xl-4 d-flex">
                    <div class="uiverse-train-card">
                        <!-- رأس البطاقة الملون -->
                        <div class="title-card">
                            <p title="جامعة طرابلس — إدارة التدريب والشراكات">
                                <i class="fas fa-university text-warning"></i>
                                <span>جامعة طرابلس</span>
                            </p>
                            @if($isPublished)
                                <span class="badge rounded-pill bg-success text-white px-2.5 py-1" style="font-size: 0.72rem;">
                                    <i class="fas fa-check-circle me-1"></i> مشارك معتمد
                                </span>
                            @else
                                <span class="badge rounded-pill" style="background: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); font-size: 0.72rem;">
                                    <i class="fas fa-clock me-1"></i> قيد الإعداد
                                </span>
                            @endif
                        </div>

                        <!-- محتوى البطاقة الداخلي الأبيض -->
                        <div class="card-content">
                            <span class="card-category">
                                <i class="fas fa-calendar-star text-warning"></i>
                                <span>ملتقى ومعرض التوظيف السنوي</span>
                            </span>

                            <h5 class="card-title-main" title="{{ $fair->title }}">{{ $fair->title }}</h5>

                            <div class="plain">
                                <span class="badge-contract">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                    <span>{{ $fair->event_date ? $fair->event_date->format('Y-m-d') : 'تاريخ معلن' }}</span>
                                </span>
                                <span class="badge-source">
                                    <i class="fas fa-map-marker-alt text-warning"></i>
                                    <span>{{ Str::limit($fair->location ?: 'جامعة طرابلس', 18) }}</span>
                                </span>
                                @if($boothNumber)
                                    <span class="badge-seats">
                                        <i class="fas fa-store text-success"></i>
                                        <span>جناح {{ $boothNumber }}</span>
                                    </span>
                                @endif
                            </div>

                            <p class="description">
                                {{ Str::limit(strip_tags($fair->description ?: 'فرصة استثنائية لشركتكم للتواصل المباشر مع نخبة خريجي جامعة طرابلس واستقطاب الكفاءات والكوادر المتميزة.'), 120) }}
                            </p>

                            <div class="card-meta-pill">
                                <span>
                                    <i class="fas fa-clock text-info me-1"></i>
                                    {{ $fair->start_time ? \Carbon\Carbon::parse($fair->start_time)->format('H:i') : '09:00 ص' }} - {{ $fair->end_time ? \Carbon\Carbon::parse($fair->end_time)->format('H:i') : '04:00 م' }}
                                </span>
                                <span>
                                    <i class="fas fa-user-graduate text-success me-1"></i>
                                    <strong>{{ $leadsCount }}</strong> مرشح مسجل بالجناح
                                </span>
                            </div>

                            <!-- منطقة أزرار الإجراءات للشركة -->
                            <div class="card-actions-area">
                                <!-- الزر الرئيسي: ماسح السير الذاتية -->
                                <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="card-btn mb-2">
                                    <span>ماسح السير الذاتية (Scanner)</span>
                                    <i class="fas fa-qrcode ms-1"></i>
                                </a>

                                <!-- أزرار سريعة ثانوية متناسقة -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('company.job-fairs.leads', $fair->id) }}" class="btn-sub-action flex-grow-1" title="عرض قائمة الخريجين المتقدمين لجناحكم">
                                        <i class="fas fa-users text-primary"></i>
                                        <span>الخريجين ({{ $leadsCount }})</span>
                                    </a>

                                    <a href="{{ route('company.job-fairs.qr-booth', $fair->id) }}" target="_blank" class="btn-sub-action flex-grow-1" title="طباعة باركود وملصق الجناح الرسمي">
                                        <i class="fas fa-print text-dark"></i>
                                        <span>باركود الجناح</span>
                                    </a>

                                    <button type="button" class="btn-sub-action btn-media-kit flex-grow-1" data-bs-toggle="modal" data-bs-target="#mediaKitModal{{ $fair->id }}" title="تحميل حزمة الهوية البصرية وشعارات المعرض">
                                        <i class="fas fa-photo-video text-warning"></i>
                                        <span>الهوية والأصول</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal: حزمة الأصول الإعلامية والهوية البصرية للشركة --}}
                <div class="modal fade" id="mediaKitModal{{ $fair->id }}" tabindex="-1" aria-labelledby="mediaKitModalLabel{{ $fair->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                            <div class="modal-header text-white p-4 border-0" style="background: linear-gradient(135deg, #0d3882 0%, #1e3a8a 100%);">
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
                                    <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4" style="background: rgba(13, 56, 130, 0.06); border-right: 4px solid #0d3882 !important;">
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
    @endif
</div>

<style>
/* 🌟 بطاقة المعرض بنمط Uiverse المعتمد المتناسق تماماً مع بطاقات فرص العمل 🌟 */
.uiverse-train-card {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg,
            #0d3882 0%,
            #1565c0 30%,
            #1e88e5 60%,
            #f59e0b 90%,
            #d97706 100%);
    padding: 3.5px;
    border-radius: 28px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px -6px rgba(13, 56, 130, 0.15), 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}

.uiverse-train-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px -8px rgba(13, 56, 130, 0.25), 0 8px 20px rgba(245, 158, 11, 0.2);
}

.uiverse-train-card .title-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 10px;
    color: #ffffff;
}

.uiverse-train-card .title-card p {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-shadow: 0 2px 6px rgba(13, 56, 130, 0.6);
    display: inline-flex;
    align-items: center;
    gap: 7px;
}

.uiverse-train-card .title-card svg {
    color: #fbbf24;
    filter: drop-shadow(0 0 6px rgba(251, 191, 36, 0.8));
}

.uiverse-train-card .card-content {
    width: 100%;
    height: 100%;
    background: #ffffff;
    border-radius: 25px;
    color: #475569;
    font-size: 0.88rem;
    padding: 22px 20px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 14px;
    position: relative;
    flex-grow: 1;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.02);
}

.uiverse-train-card .card-content .card-category {
    font-size: 0.78rem;
    font-weight: 800;
    color: #0d3882;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
}

.uiverse-train-card .card-content .card-title-main {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.45;
    margin: 0;
    min-height: 48px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.uiverse-train-card .card-content .plain {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
    flex-wrap: wrap;
}

.uiverse-train-card .card-content .plain > .badge-contract {
    font-size: 0.86rem;
    font-weight: 800;
    color: #0d3882;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    padding: 5px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

.uiverse-train-card .card-content .plain > .badge-source {
    font-size: 0.82rem;
    font-weight: 700;
    color: #b45309;
    background: #fef3c7;
    border: 1px solid #fde68a;
    padding: 5px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

.uiverse-train-card .card-content .plain > .badge-seats {
    font-size: 0.82rem;
    font-weight: 700;
    color: #065f46;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 5px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Tajawal', sans-serif;
    line-height: 1.3;
}

/* حماية أيقونات FontAwesome لضمان عدم ظهور أي مربعات أو تشوهات خطية */
.uiverse-train-card i {
    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', 'FontAwesome' !important;
    font-weight: 900 !important;
    font-style: normal !important;
    border: none !important;
    background: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
    line-height: 1 !important;
    display: inline-block !important;
}

.uiverse-train-card .card-content .description {
    font-size: 0.86rem;
    color: #64748b;
    line-height: 1.65;
    margin: 0;
    flex-grow: 1;
    min-height: 42px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.uiverse-train-card .card-meta-pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #334155;
    font-weight: 600;
}

.uiverse-train-card .card-actions-area {
    display: flex;
    flex-direction: column;
    margin-top: 4px;
}

.uiverse-train-card .card-btn {
    background: linear-gradient(135deg, #0d3882 0%, #1565c0 100%) !important;
    border: 1.5px solid #0d3882 !important;
    color: #ffffff !important;
    font-weight: 800;
    font-size: 0.9rem;
    padding: 0.68rem 1.3rem;
    border-radius: 50px;
    transition: all 0.28s ease;
    box-shadow: 0 4px 14px rgba(13, 56, 130, 0.25);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    text-decoration: none;
    cursor: pointer;
}

.uiverse-train-card .card-btn i {
    color: #fbbf24 !important;
    transition: all 0.28s ease;
}

.uiverse-train-card .card-btn:hover {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    border-color: #f59e0b !important;
    color: #0b1f3a !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(245, 158, 11, 0.4);
}

.uiverse-train-card .card-btn:hover i {
    color: #0b1f3a !important;
}

.uiverse-train-card .btn-sub-action {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-size: 0.76rem;
    font-weight: 700;
    padding: 8px 6px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none;
    transition: all 0.22s ease;
    cursor: pointer;
    white-space: nowrap;
}

.uiverse-train-card .btn-sub-action:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #0d3882;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(13, 56, 130, 0.1);
}

.uiverse-train-card .btn-sub-action.btn-media-kit {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}

.uiverse-train-card .btn-sub-action.btn-media-kit:hover {
    background: #fef3c7;
    border-color: #f59e0b;
    color: #78350f;
    box-shadow: 0 4px 10px rgba(245, 158, 11, 0.15);
}

/* دعم الوضع الليلي للمنصة إن تم تفعيله */
[data-theme="dark"] .uiverse-train-card .card-content,
.dark-mode .uiverse-train-card .card-content {
    background: #0f172a;
    color: #cbd5e1;
}
[data-theme="dark"] .uiverse-train-card .card-title-main,
.dark-mode .uiverse-train-card .card-title-main {
    color: #ffffff;
}
[data-theme="dark"] .uiverse-train-card .plain > .badge-contract,
.dark-mode .uiverse-train-card .plain > .badge-contract {
    color: #93c5fd;
    background: rgba(30, 58, 138, 0.4);
    border-color: rgba(147, 197, 253, 0.3);
}
[data-theme="dark"] .uiverse-train-card .plain > .badge-source,
.dark-mode .uiverse-train-card .plain > .badge-source {
    color: #fcd34d;
    background: rgba(180, 83, 9, 0.25);
    border-color: rgba(251, 191, 36, 0.3);
}
[data-theme="dark"] .uiverse-train-card .plain > .badge-seats,
.dark-mode .uiverse-train-card .plain > .badge-seats {
    color: #6ee7b7;
    background: rgba(6, 95, 70, 0.3);
    border-color: rgba(110, 231, 183, 0.3);
}
[data-theme="dark"] .uiverse-train-card .card-meta-pill,
.dark-mode .uiverse-train-card .card-meta-pill {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
}
[data-theme="dark"] .uiverse-train-card .btn-sub-action,
.dark-mode .uiverse-train-card .btn-sub-action {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.12);
    color: #e2e8f0;
}
</style>
@endsection
