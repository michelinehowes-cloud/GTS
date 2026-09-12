@extends('layouts.app')

@section('title', 'السير الذاتية المستلمة - ' . $fair->title)
@section('page-title', 'السير الذاتية المستلمة')

@push('styles')
<style>
    /* =========================================================================
       تصميم السيرة الذاتية الاحترافية A4 للشركات (Professional A4 CV Document)
       ========================================================================= */
    .lead-card {
        background: #ffffff;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease !important;
        position: relative;
        overflow: hidden;
    }
    .lead-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 14px 30px rgba(13, 56, 130, 0.12) !important;
        border-color: #bfdbfe !important;
    }

    /* نافذة السيرة الذاتية: مستطيلة وعريضة بنسبة ورقة A4 حقيقية بدون حبس داخل سكرول ضيق */
    .cv-modal-dialog {
        max-width: 1020px !important;
        width: 96% !important;
        margin: 1.5rem auto !important;
    }

    /* شريط أدوات وتصرفات الشركة العلوي (خارج الورقة) */
    .cv-recruiter-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    /* ورقة السيرة الذاتية A4 الرسمية (A4 Sheet Container) */
    .cv-a4-sheet {
        background: #ffffff !important;
        border-radius: 14px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 15px 45px rgba(15, 23, 42, 0.12) !important;
        overflow: hidden;
        position: relative;
    }

    /* ترويسة ورقة السيرة الذاتية الملكية */
    .cv-a4-header {
        background: linear-gradient(135deg, #092552 0%, #0d3882 50%, #175bb5 100%);
        color: #ffffff;
        padding: 1.75rem 2.25rem 1.5rem;
        position: relative;
    }

    /* عناوين الأقسام الرئيسية داخل ورقة A4 */
    .cv-a4-section-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0d3882;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 0.4rem;
    }
    .cv-a4-section-title i {
        color: #0d3882;
        font-size: 0.92rem;
    }

    /* كتل البطاقات المدمجة داخل السيرة الذاتية */
    .cv-box-block {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
    }

    /* كبسولات التواصل المدمجة في الهيدر */
    .cv-header-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.3rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff !important;
        text-decoration: none !important;
        font-size: 0.80rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.22);
        transition: all 0.2s ease;
    }
    .cv-header-pill:hover {
        background: rgba(255, 255, 255, 0.28);
        transform: translateY(-1px);
    }
    .cv-header-pill.whatsapp {
        background: rgba(34, 197, 94, 0.25);
        border-color: rgba(34, 197, 94, 0.45);
    }
    .cv-header-pill.whatsapp:hover {
        background: rgba(34, 197, 94, 0.42);
    }
    .cv-header-pill.linkedin {
        background: rgba(10, 102, 194, 0.35);
        border-color: rgba(10, 102, 194, 0.55);
    }

    /* بطاقات الدورات التدريبية المعتمدة */
    .cv-training-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        transition: border-color 0.2s ease;
    }
    .cv-training-item:hover {
        border-color: #059669;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="bento-card mb-4 p-0 overflow-hidden">
        <div class="d-flex align-items-center justify-content-between p-4 flex-wrap gap-3"
             style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-users text-white" style="font-size:1.5rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-1">السير الذاتية المستلمة</h4>
                    <p class="text-white mb-0" style="opacity:.8;font-size:.9rem;">
                        <i class="fas fa-briefcase me-1"></i>{{ $fair->title }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('company.job-fairs.scanner', $fair->id) }}"
                   class="btn btn-light fw-bold rounded-pill px-4" style="color:#1e3a8a;">
                    <i class="fas fa-qrcode me-2"></i>ماسح السير
                </a>
                <a href="{{ route('company.job-fairs.index') }}"
                   class="btn rounded-pill px-4 fw-bold" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-right me-2"></i>رجوع
                </a>
            </div>
        </div>
        {{-- Stats Bar --}}
        <div class="d-flex border-top" style="border-color:rgba(0,0,0,0.05) !important;">
            <div class="flex-fill text-center py-3 px-2" style="border-left:1px solid rgba(0,0,0,0.05);">
                <div class="fw-bold fs-4" style="color:#1e3a8a;">{{ $visits->total() }}</div>
                <div class="text-muted small">إجمالي السير المستلمة</div>
            </div>
            <div class="flex-fill text-center py-3 px-2" style="border-left:1px solid rgba(0,0,0,0.05);">
                <div class="fw-bold fs-4 text-success">{{ $visits->filter(fn($v) => ($v->graduate?->graduateData?->gpa ?? $v->graduate?->gpa) >= 80)->count() }}</div>
                <div class="text-muted small">معدل ≥ 80% (متميز)</div>
            </div>
            <div class="flex-fill text-center py-3 px-2">
                <div class="fw-bold fs-4 text-info">{{ $visits->filter(fn($v) => $v->graduate?->graduateData?->cv_path)->count() }}</div>
                <div class="text-muted small">لديهم ملف PDF مرفق</div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    @if($visits->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-user-slash"></i>
            </div>
            <h4>لم تستلم أي سير ذاتية بعد!</h4>
            <p>استخدم ماسح السير الذاتية لجمع بيانات الخريجين في المعرض.</p>
            <a href="{{ route('company.job-fairs.scanner', $fair->id) }}" class="btn btn-primary-modern px-5 mt-2">
                <i class="fas fa-qrcode me-2"></i>افتح الماسح
            </a>
        </div>
    @else
        {{-- Cards Grid --}}
        <div class="row g-4">
            @foreach($visits as $visit)
            @php 
                $grad = $visit->graduate; 
                $gData = $grad->graduateData;
                $gpaVal = $gData->gpa ?? $grad->gpa;
                $initial = mb_substr($grad->name ?? '', 0, 1);

                $statusPills = [
                    'pending'     => ['label' => 'قيد الدراسة والمراجعة', 'badge' => 'bg-warning-subtle text-warning border-warning-subtle'],
                    'shortlisted' => ['label' => 'القائمة القصيرة ⭐', 'badge' => 'bg-info-subtle text-info border-info-subtle'],
                    'accepted'    => ['label' => 'مقبول مبدئياً ✅', 'badge' => 'bg-success-subtle text-success border-success-subtle'],
                    'rejected'    => ['label' => 'غير متوافق ❌', 'badge' => 'bg-secondary-subtle text-secondary border-secondary-subtle'],
                ];
                $currPill = $statusPills[$visit->status] ?? $statusPills['pending'];
            @endphp
            <div class="col-xl-4 col-md-6">
                <div class="lead-card h-100 d-flex flex-column" style="padding: 1.25rem !important; cursor:default;">
                    
                    {{-- شريط علوي أنيق داخل البطاقة: حالة المقابلة + المعدل بمسافات داخلية آمنة --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold border {{ $currPill['badge'] }}" style="font-size: 0.74rem;">
                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>{{ $currPill['label'] }}
                        </span>
                        @if($gpaVal)
                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold"
                              style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.78rem;">
                            <i class="fas fa-star text-warning me-1"></i>{{ number_format($gpaVal, 2) }}%
                        </span>
                        @endif
                    </div>

                    {{-- بيانات المرشح: الصورة الرمزية، الاسم، التخصص بالكامل، والبريد --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="position-relative flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                 style="width: 54px; height: 54px; background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%); color: #ffffff; font-size: 1.4rem; font-weight: 800; border: 2.5px solid #ffffff;">
                                {{ $initial }}
                            </div>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" title="خريج موثق" style="width: 14px; height: 14px;"></span>
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <h5 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 1.05rem;">{{ $grad->name }}</h5>
                            <div class="d-flex align-items-center gap-1.5 text-primary fw-semibold small text-truncate mb-1" style="font-size: 0.84rem;">
                                <i class="fas fa-graduation-cap flex-shrink-0"></i>
                                <span class="text-truncate">{{ $gData->major ?? $grad->major ?? $grad->specialization ?? 'هندسة البرمجيات' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-muted small text-truncate" style="font-size: 0.76rem;">
                                <i class="fas fa-envelope flex-shrink-0 text-secondary"></i>
                                <span class="text-truncate" dir="ltr">{{ $grad->email }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- لوحة التفاصيل السريعة المتناسقة --}}
                    <div class="rounded-3 p-2.5 mb-3 d-flex flex-column gap-2" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.78rem;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="fas fa-university text-primary" style="width: 14px;"></i>
                                <span>الجامعة:</span>
                            </div>
                            <span class="fw-semibold text-dark text-truncate ms-2" style="max-width: 65%;">{{ $gData->university ?? $grad->university ?? 'جامعة طرابلس' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="fas fa-calendar-alt text-primary" style="width: 14px;"></i>
                                <span>سنة التخرج:</span>
                            </div>
                            <span class="fw-bold text-dark">دفعة {{ $gData->graduation_year ?? $grad->graduation_year ?? '—' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="fas fa-clock text-primary" style="width: 14px;"></i>
                                <span>وقت المقابلة:</span>
                            </div>
                            <span class="fw-bold text-dark font-monospace">{{ $visit->created_at->format('H:i') }}</span>
                        </div>
                        @php $trnCount = $grad->trainingApplications ? $grad->trainingApplications->count() : 0; @endphp
                        @if($trnCount > 0)
                        <div class="d-flex align-items-center justify-content-between pt-1.5 border-top" style="border-color: #e2e8f0 !important;">
                            <div class="d-flex align-items-center gap-2 text-muted">
                                <i class="fas fa-certificate text-success" style="width: 14px;"></i>
                                <span>الدورات المعتمدة:</span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                {{ $trnCount }} دورات معتمدة
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- أزرار الإجراءات بأيقونات متناسقة وتباعد مضمون --}}
                    <div class="d-flex align-items-center gap-2 mt-auto pt-2 border-top" style="border-color: #f1f5f9 !important;">
                        <button type="button"
                                class="btn btn-sm flex-fill fw-bold rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                style="background: linear-gradient(135deg, #0d3882 0%, #1e40af 100%); color: #ffffff; border: none; font-size: 0.84rem;"
                                data-bs-toggle="modal"
                                data-bs-target="#leadModal{{ $visit->id }}">
                            <i class="fas fa-id-card"></i>
                            <span>السيرة الذاتية</span>
                        </button>
                        <a href="{{ route('messages.show', $grad->id) }}"
                           class="btn btn-sm flex-fill fw-bold rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2"
                           style="background: #f0fdf4; color: #166534; border: 1px solid #86efac; font-size: 0.84rem;">
                            <i class="fas fa-envelope"></i>
                            <span>مراسلة</span>
                        </a>
                        @if($gData?->cv_path)
                        <a href="{{ Storage::url($gData->cv_path) }}"
                           target="_blank"
                           class="btn btn-sm rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center shadow-sm"
                           style="background: #fdf4ff; color: #7c3aed; border: 1px solid #f0abfc; width: 36px; height: 36px;"
                           title="تحميل ملف السيرة الذاتية PDF الأصلي">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $visits->links() }}
        </div>
    @endif
</div>

{{-- Detail CV Modals (النافذة الواحدة المعتمدة للسيرة الذاتية الاحترافية A4) --}}
@foreach($visits as $visit)
@php 
    $grad = $visit->graduate;
    $gData = $grad->graduateData;
    $major = $gData->major ?? $grad->major ?? $grad->specialization ?? 'هندسة البرمجيات';
    $faculty = $gData->faculty ?? $grad->faculty ?? 'كلية تقنية المعلومات';
    $university = $gData->university ?? $grad->university ?? 'جامعة طرابلس';
    $degree = $gData->degree ?? $grad->degree ?? $grad->qualification ?? 'بكالوريوس';
    $gpa = $gData->gpa ?? $grad->gpa;
    $gradYear = $gData->graduation_year ?? $grad->graduation_year ?? '—';
    $phone = $gData->phone ?? $grad->phone ?? null;
    $address = $gData->address ?? $grad->address ?? null;
    $city = $gData->city ?? $grad->city ?? 'طرابلس';
    $workExp = $gData->work_experience ?? $grad->experiences ?? null;
    $notes = $visit->notes ?? null;
    $initial = mb_substr($grad->name, 0, 1);
    
    // Skills
    $skills = $gData->skills ?? $grad->skills ?? [];
    if (is_string($skills)) {
        $skills = array_filter(array_map('trim', explode(',', $skills)));
    }
    
    // Languages
    $languages = $gData->languages ?? $grad->languages ?? [];
    if (is_string($languages)) {
        $languages = array_filter(array_map('trim', explode(',', $languages)));
    }

    // Employment Status
    $empStatus = $gData->employment_status ?? 'seeking_opportunities';
    $statusMap = [
        'seeking_opportunities' => ['label' => 'باحث عن فرصة عمل', 'badge_class' => 'bg-warning text-dark border-warning', 'icon' => 'fas fa-search'],
        'employed'              => ['label' => 'موظف حالياً', 'badge_class' => 'bg-success text-white border-success', 'icon' => 'fas fa-check-circle'],
        'training'              => ['label' => 'في فترة تدريب', 'badge_class' => 'bg-info text-white border-info', 'icon' => 'fas fa-laptop-code'],
        'freelancer'            => ['label' => 'عمل حر / مستقل', 'badge_class' => 'bg-purple text-white border-purple', 'icon' => 'fas fa-user-tie'],
        'unemployed'            => ['label' => 'غير موظف', 'badge_class' => 'bg-secondary text-white border-secondary', 'icon' => 'fas fa-clock'],
        'further_study'         => ['label' => 'مستكمل للدراسات العليا', 'badge_class' => 'bg-primary text-white border-primary', 'icon' => 'fas fa-user-graduate'],
    ];
    $statusInfo = $statusMap[$empStatus] ?? ['label' => 'باحث عن عمل', 'badge_class' => 'bg-warning text-dark', 'icon' => 'fas fa-search'];

    // GPA Evaluation
    $gpaEval = null;
    if ($gpa) {
        $gpaFloat = (float) $gpa;
        if ($gpaFloat >= 85) $gpaEval = 'ممتاز مع مرتبة الشرف ⭐';
        elseif ($gpaFloat >= 75) $gpaEval = 'جيد جداً';
        elseif ($gpaFloat >= 65) $gpaEval = 'جيد';
        else $gpaEval = 'مقبول';
    }

    // Social Links
    $linkedinUrl = $gData->linkedin ?? $grad->linkedin ?? null;
    $portfolioUrl = $gData->portfolio ?? $grad->portfolio ?? null;

    // Clean Phone for WhatsApp
    $cleanPhone = preg_replace('/[^0-9]/', '', (string)$phone);
    if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
        $whatsappPhone = '218' . substr($cleanPhone, 1);
    } elseif ($cleanPhone && !str_starts_with($cleanPhone, '218')) {
        $whatsappPhone = '218' . $cleanPhone;
    } else {
        $whatsappPhone = $cleanPhone;
    }

    // Trainings applications
    $trainings = $grad->trainingApplications ?? collect();

    // Lead Status Badges
    $leadStatusBadge = [
        'pending'     => ['label' => 'قيد الدراسة والمراجعة', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle'],
        'shortlisted' => ['label' => 'مدرج بالقائمة القصيرة ⭐', 'class' => 'bg-info-subtle text-info border border-info-subtle'],
        'accepted'    => ['label' => 'مقبول مبدئياً ✅', 'class' => 'bg-success-subtle text-success border border-success-subtle'],
        'rejected'    => ['label' => 'غير متوافق حالياً ❌', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle'],
    ];
    $st = $leadStatusBadge[$visit->status] ?? $leadStatusBadge['pending'];
@endphp
<div class="modal fade" id="leadModal{{ $visit->id }}" tabindex="-1" aria-hidden="true">
    {{-- نلغي scrollable و centered لنتيح العرض المستطيل الكامل بحرية دون حبس المحتوى في مربع ضيق --}}
    <div class="modal-dialog cv-modal-dialog">
        <div class="modal-content border-0 bg-transparent shadow-none">

            {{-- 1. شريط إجراءات الشركة والفرز العلوي (خارج ورقة الـ A4) --}}
            <div class="cv-recruiter-toolbar mb-3 no-print">
                {{-- فورم تقييم المقابلة الداخلي للشركة --}}
                <div class="d-flex align-items-center gap-3 flex-wrap flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-dark small d-block">قرار المقابلة والفرز:</span>
                            <span class="text-muted" style="font-size: 0.72rem;">تقييم داخلي خاص بشركتكم</span>
                        </div>
                    </div>
                    <form action="{{ route('company.job-fairs.visit-outcome', $visit->id) }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <select name="outcome" class="form-select form-select-sm rounded-pill fw-semibold border shadow-none" style="min-width: 220px;">
                            <option value="pending" {{ $visit->status == 'pending' ? 'selected' : '' }}>⏳ قيد الدراسة والمراجعة</option>
                            <option value="shortlisted" {{ $visit->status == 'shortlisted' ? 'selected' : '' }}>⭐ مدرج في القائمة القصيرة</option>
                            <option value="accepted" {{ $visit->status == 'accepted' ? 'selected' : '' }}>✅ مقبول مبدئياً للتوظيف</option>
                            <option value="rejected" {{ $visit->status == 'rejected' ? 'selected' : '' }}>❌ غير متوافق حالياً (مرفوض)</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm flex-shrink-0">
                            <i class="fas fa-save me-1"></i>حفظ
                        </button>
                    </form>
                </div>

                {{-- أزرار الإجراءات السريعة --}}
                <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" onclick="printCvSheet('leadCvSheet{{ $visit->id }}')">
                        <i class="fas fa-print text-primary"></i>
                        <span>طباعة السيرة الذاتية (A4)</span>
                    </button>
                    @if($gData?->cv_path ?? false)
                    <a href="{{ Storage::url($gData->cv_path) }}" target="_blank"
                       class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5"
                       style="background: #fdf4ff; color: #7c3aed; border: 1px solid #f0abfc;">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF الأصلي</span>
                    </a>
                    @endif
                    <a href="{{ route('messages.show', $grad->id) }}"
                       class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm">
                        <i class="fas fa-envelope"></i>
                        <span>مراسلة</span>
                    </a>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 fw-bold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إغلاق
                    </button>
                </div>
            </div>

            {{-- 2. ورقة السيرة الذاتية الاحترافية A4 المتكاملة --}}
            <div class="cv-a4-sheet" id="leadCvSheet{{ $visit->id }}">

                {{-- ترويسة السيرة الذاتية الأكاديمية الملكية --}}
                <div class="cv-a4-header">
                    {{-- الشريط العلوي للترويسة: الشعار والاسم --}}
                    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom" style="border-color: rgba(255,255,255,0.2) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-university text-warning" style="font-size: 1.1rem;"></i>
                            <span class="fw-bold" style="font-size: 0.88rem; letter-spacing: 0.2px;">جامعة طرابلس — منظومة تدريب وتأهيل الخريجين</span>
                        </div>
                    </div>

                    {{-- بيانات المرشح الرئيسية --}}
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                                 style="width: 80px; height: 80px; background: #ffffff; color: #0d3882; font-size: 2.2rem; font-weight: 800; border: 4px solid rgba(255,255,255,0.85);">
                                {{ $initial }}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h2 class="fw-bold text-white mb-0" style="font-size: 1.55rem;">{{ $grad->name }}</h2>
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold {{ $statusInfo['badge_class'] }}" style="font-size: 0.76rem;">
                                    <i class="{{ $statusInfo['icon'] }} me-1"></i>{{ $statusInfo['label'] }}
                                </span>
                                <span class="badge rounded-pill px-2.5 py-1 text-white" style="background: rgba(255,255,255,0.2); font-size: 0.74rem;">
                                    <i class="fas fa-check-circle text-info me-1"></i>خريج موثق
                                </span>
                            </div>
                            <div class="text-white mb-2" style="font-size: 0.95rem; opacity: 0.95;">
                                <i class="fas fa-graduation-cap me-1.5 text-warning"></i>{{ $degree }} في {{ $major }} — {{ $faculty }} ({{ $university }})
                            </div>
                            {{-- كبسولات التواصل المباشر --}}
                            <div class="d-flex flex-wrap gap-2" style="font-size: 0.82rem;">
                                <a href="mailto:{{ $grad->email }}" class="cv-header-pill">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $grad->email }}</span>
                                </a>
                                @if($phone)
                                <a href="tel:{{ $phone }}" class="cv-header-pill" dir="ltr">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $phone }}</span>
                                </a>
                                @if($whatsappPhone)
                                <a href="https://wa.me/{{ $whatsappPhone }}" target="_blank" class="cv-header-pill whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>واتساب</span>
                                </a>
                                @endif
                                @endif
                                @if($city || $address)
                                <span class="cv-header-pill">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $city }} {{ $address ? '— ' . $address : '' }}</span>
                                </span>
                                @endif
                                @if($linkedinUrl)
                                <a href="{{ $linkedinUrl }}" target="_blank" class="cv-header-pill linkedin">
                                    <i class="fab fa-linkedin"></i>
                                    <span>LinkedIn</span>
                                </a>
                                @endif
                                @if($portfolioUrl)
                                <a href="{{ $portfolioUrl }}" target="_blank" class="cv-header-pill">
                                    <i class="fas fa-globe"></i>
                                    <span>معرض الأعمال</span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- محتوى السيرة الذاتية: تقسيم عمودين هندسي A4 يعرض كافة البيانات دفعة واحدة دون سكرول --}}
                <div class="p-4" style="background: #ffffff;">
                    <div class="row g-4">

                        {{-- العمود الأيمن (المحتوى الأكاديمي والمهني الأساسي - 65%) --}}
                        <div class="col-lg-7 col-md-7">
                            
                            {{-- 1. النبذة والملخص المهني --}}
                            <div class="mb-3">
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-user-tie"></i>
                                    <span>النبذة والملخص المهني</span>
                                </h6>
                                <div class="cv-box-block">
                                    <p class="text-secondary mb-0" style="font-size: 0.88rem; line-height: 1.75;">
                                        {{ $notes ?: ($gData->notes ?: "خريج متميز متخصص في {$major} من {$faculty} بـ {$university}. يمتلك كفاءة علمية ومهارية عالية مع جاهزية تامة للمشاركة في مشاريع العمل والفرص الوظيفية المتاحة، والاندماج السريع في بيئات العمل الاحترافية.") }}
                                    </p>
                                </div>
                            </div>

                            {{-- 2. المؤهل العلمي والأكاديمي --}}
                            <div class="mb-3">
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>المؤهل العلمي والأكاديمي</span>
                                </h6>
                                <div class="cv-box-block">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $degree }} في {{ $major }}</div>
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small fw-bold">دفعة {{ $gradYear }}</span>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <i class="fas fa-university me-1 text-secondary"></i>{{ $faculty }} — {{ $university }}
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center pt-1">
                                        @if($gpa)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.78rem;">
                                            <i class="fas fa-star text-warning me-1"></i>المعدل: {{ number_format($gpa, 2) }}%
                                        </span>
                                        @endif
                                        @if($gpaEval)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.78rem;">
                                            {{ $gpaEval }}
                                        </span>
                                        @endif
                                        @if($gData->sector ?? null)
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            {{ $gData->sector }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 3. البرامج والدورات التدريبية المعتمدة بالمنظومة --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="cv-a4-section-title mb-0 border-0 pb-0">
                                        <i class="fas fa-award text-success"></i>
                                        <span>البرامج والدورات التدريبية المعتمدة</span>
                                    </h6>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                        {{ $trainings->count() }} دورات مجتازة
                                    </span>
                                </div>

                                @if($trainings->isEmpty())
                                    <div class="text-center py-2.5 rounded-3 text-muted small bg-light" style="border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-graduation-cap me-1"></i>لم يتم تسجيل دورات تدريبية معتمدة داخل المنظومة حتى الآن.
                                    </div>
                                @else
                                    <div class="d-flex flex-column">
                                        @foreach($trainings as $app)
                                        <div class="cv-training-item">
                                            <div class="d-flex align-items-center gap-2 min-w-0">
                                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark small text-truncate" title="{{ $app->training->title ?? 'برنامج تدريبي معتمد' }}">
                                                        {{ $app->training->title ?? 'برنامج تدريبي معتمد' }}
                                                    </div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">
                                                        <span class="text-primary fw-semibold"><i class="fas fa-shield-alt me-1"></i>معتمد</span>
                                                        @if($app->attended_at || $app->created_at)
                                                            <span class="mx-1">•</span>
                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $app->attended_at ? $app->attended_at->format('Y-m-d') : $app->created_at->format('Y-m-d') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1"
                                                      style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.78rem;">
                                                    <i class="fas fa-chart-pie"></i>
                                                    <span>نسبة الحضور: {{ $app->attendance_percentage }}%</span>
                                                </span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- 4. الخبرات المهنية والمشاريع --}}
                            <div>
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-briefcase"></i>
                                    <span>الخبرات المهنية والمشاريع</span>
                                </h6>
                                <div class="cv-box-block">
                                    @if(!empty($workExp))
                                        <p class="text-secondary small mb-0 lh-base" style="white-space: pre-line; line-height: 1.65;">
                                            {{ $workExp }}
                                        </p>
                                    @else
                                        <div class="text-center text-muted small py-1">
                                            <i class="fas fa-info-circle me-1"></i>خريج جديد جاهز ومستعد لبدء مسيرته المهنية والمشاركة في مشاريع العمل.
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                        {{-- العمود الأيسر (المؤشرات، المهارات، اللغات، والتوثيق - 35%) --}}
                        <div class="col-lg-5 col-md-5">

                            {{-- بطاقة المؤشرات الأكاديمية السريعة --}}
                            <div class="mb-3">
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-chart-line"></i>
                                    <span>المؤشرات الأكاديمية</span>
                                </h6>
                                <div class="cv-box-block">
                                    <div class="row g-2 text-center">
                                        <div class="col-6 border-end">
                                            <div class="text-muted" style="font-size: 0.70rem;">المعدل التراكمي</div>
                                            <div class="fw-bold text-primary" style="font-size: 1.15rem;">
                                                {{ $gpa ? number_format($gpa, 2) . '%' : '—' }}
                                            </div>
                                            @if($gpaEval)
                                                <div class="text-warning fw-bold" style="font-size: 0.68rem;">{{ $gpaEval }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted" style="font-size: 0.70rem;">سنة التخرج</div>
                                            <div class="fw-bold text-dark" style="font-size: 1.15rem;">
                                                {{ $gradYear }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.68rem;">دفعة التخرج</div>
                                        </div>
                                    </div>
                                    <hr class="my-2" style="border-color: #e2e8f0;">
                                    <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                        <span class="text-muted"><i class="fas fa-clock me-1"></i>تسجيل المعرض:</span>
                                        <span class="fw-bold text-dark font-monospace">{{ $visit->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- المهارات والكفاءات --}}
                            <div class="mb-3">
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-tools"></i>
                                    <span>المهارات والكفاءات</span>
                                </h6>
                                <div class="cv-box-block">
                                    @if(count($skills) > 0)
                                        <div class="d-flex flex-wrap gap-1.5">
                                            @foreach($skills as $skill)
                                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold"
                                                      style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.76rem;">
                                                    <i class="fas fa-check me-1 text-primary" style="font-size: 0.65rem;"></i>{{ $skill }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted small text-center py-1">لم تُسجل مهارات إضافية بعد.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- اللغات --}}
                            <div class="mb-3">
                                <h6 class="cv-a4-section-title">
                                    <i class="fas fa-language"></i>
                                    <span>اللغات</span>
                                </h6>
                                <div class="cv-box-block">
                                    @if(count($languages) > 0)
                                        <div class="d-flex flex-wrap gap-1.5">
                                            @foreach($languages as $lang)
                                                <span class="badge rounded-pill px-2.5 py-1 fw-semibold"
                                                      style="background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; font-size: 0.76rem;">
                                                    <i class="fas fa-globe me-1 text-primary"></i>{{ $lang }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; font-size: 0.76rem;">
                                            <i class="fas fa-globe me-1 text-primary"></i>العربية (اللغة الأم)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- ملف السيرة الذاتية PDF الأصلي إن وجد --}}
                            @if($gData?->cv_path ?? false)
                            <div class="mb-3">
                                <div class="p-2.5 rounded-3 d-flex align-items-center justify-content-between" style="background: #fef2f2; border: 1px solid #fecaca;">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark small text-truncate">ملف PDF الأصلي</div>
                                            <div class="text-muted" style="font-size: 0.70rem;">مرفوع من الخريج</div>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($gData->cv_path) }}" target="_blank"
                                       class="btn btn-sm btn-danger rounded-pill px-2.5 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-download"></i>
                                        <span>تحميل</span>
                                    </a>
                                </div>
                            </div>
                            @endif

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function printCvSheet(sheetId) {
    var sheet = document.getElementById(sheetId);
    if (!sheet) return;
    
    var printContents = sheet.innerHTML;
    var printWindow = window.open('', '', 'height=900,width=1050');
    printWindow.document.write('<!DOCTYPE html><html dir="rtl" lang="ar"><head><title>السيرة الذاتية الرقمية المعتمدة — جامعة طرابلس</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">');
    printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
    printWindow.document.write('<style>');
    printWindow.document.write('@page { size: A4; margin: 8mm; }');
    printWindow.document.write('body { font-family: "29LT Bukra", "Cairo", "Tajawal", sans-serif; direction: rtl; background: #fff; color: #1e293b; padding: 10px; }');
    printWindow.document.write('.no-print, button, form { display: none !important; }');
    printWindow.document.write('.cv-a4-sheet { border: none !important; box-shadow: none !important; }');
    printWindow.document.write('.cv-a4-header { border-radius: 8px !important; }');
    printWindow.document.write('.cv-box-block { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>
@endpush

@endsection
