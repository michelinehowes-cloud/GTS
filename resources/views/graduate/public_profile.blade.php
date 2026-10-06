@extends('layouts.app')

@section('title', 'السيرة الذاتية — ' . $graduate->name)

@php
    $gData = $graduate->graduateData;
    $major = $gData->major ?? $graduate->major ?? $graduate->specialization ?? 'هندسة البرمجيات';
    $faculty = $gData->faculty ?? $graduate->faculty ?? 'كلية تقنية المعلومات';
    $university = $gData->university ?? $graduate->university ?? 'جامعة طرابلس';
    $degree = $gData->degree ?? $graduate->degree ?? $graduate->qualification ?? 'بكالوريوس';
    $gpa = $gData->gpa ?? $graduate->gpa ?? null;
    $gradYear = $gData->graduation_year ?? $graduate->graduation_year ?? null;
    $phone = $gData->phone ?? $graduate->phone ?? null;
    $address = $gData->address ?? $graduate->address ?? null;
    $city = $gData->city ?? $graduate->city ?? 'طرابلس';
    $workExp = $gData->work_experience ?? $graduate->experiences ?? null;
    $certifications = $gData->certifications ?? null;
    
    $skills = $gData->skills ?? $graduate->skills ?? [];
    if (is_string($skills)) {
        $skills = array_filter(array_map('trim', explode(',', $skills)));
    }
    
    $languages = $gData->languages ?? $graduate->languages ?? [];
    if (is_string($languages)) {
        $languages = array_filter(array_map('trim', explode(',', $languages)));
    }
    
    $empStatus = $gData->employment_status ?? 'seeking_opportunities';
    $statusMap = [
        'seeking_opportunities' => ['label' => 'باحث عن فرصة عمل', 'badge_class' => 'bg-warning text-dark border-warning', 'icon' => 'fa-solid fa-magnifying-glass'],
        'employed'              => ['label' => 'موظف حالياً', 'badge_class' => 'bg-success text-white border-success', 'icon' => 'fa-solid fa-circle-check'],
        'training'              => ['label' => 'في فترة تدريب', 'badge_class' => 'bg-info text-white border-info', 'icon' => 'fa-solid fa-laptop-code'],
        'freelancer'            => ['label' => 'عمل حر / مستقل', 'badge_class' => 'bg-purple text-white border-purple', 'icon' => 'fa-solid fa-user-tie'],
        'unemployed'            => ['label' => 'غير موظف', 'badge_class' => 'bg-secondary text-white border-secondary', 'icon' => 'fa-solid fa-clock'],
        'further_study'         => ['label' => 'مستكمل للدراسات العليا', 'badge_class' => 'bg-primary text-white border-primary', 'icon' => 'fa-solid fa-user-graduate'],
        'continuing_education'  => ['label' => 'مستكمل للدراسات العليا', 'badge_class' => 'bg-primary text-white border-primary', 'icon' => 'fa-solid fa-user-graduate'],
    ];
    $statusInfo = $statusMap[$empStatus] ?? ['label' => 'غير محدد', 'badge_class' => 'bg-light text-secondary border', 'icon' => 'fa-solid fa-user'];

    // GPA Evaluation
    $gpaEval = null;
    if ($gpa) {
        $gpaFloat = (float) $gpa;
        if ($gpaFloat >= 85) $gpaEval = 'ممتاز (Honors)';
        elseif ($gpaFloat >= 75) $gpaEval = 'جيد جداً (Very Good)';
        elseif ($gpaFloat >= 65) $gpaEval = 'جيد (Good)';
        else $gpaEval = 'مقبول (Pass)';
    }

    // Clean Phone for WhatsApp
    $cleanPhone = preg_replace('/[^0-9]/', '', (string)$phone);
    if ($cleanPhone && str_starts_with($cleanPhone, '0')) {
        $whatsappPhone = '218' . substr($cleanPhone, 1);
    } elseif ($cleanPhone && !str_starts_with($cleanPhone, '218')) {
        $whatsappPhone = '218' . $cleanPhone;
    } else {
        $whatsappPhone = $cleanPhone;
    }
@endphp

@push('styles')
<style>
    /* =========================================================================
       هوية المنظومة الموحدة — مكتب تدريب الخريجين بجامعة طرابلس
       ========================================================================= */
    :root {
        --bento-primary: #0d3882;
        --bento-primary-mid: #1565c0;
        --bento-primary-light: #1e88e5;
        --bento-gold: #f59e0b;
        --bento-gold-light: #fbbf24;
        --bento-bg: #f8fafc;
        --bento-surface: #ffffff;
        --bento-border: #e2e8f0;
        --bento-gradient: linear-gradient(135deg, #0d3882 0%, #1565c0 50%, #1e88e5 100%);
    }

    body {
        background-color: var(--bento-bg);
        font-family: '29LT Bukra', 'Cairo', 'Tajawal', sans-serif;
    }

    /* 1. شريط الإجراءات العلوي بنمط هوية المنظومة */
    .profile-action-bar {
        background: #ffffff;
        border: 1px solid var(--bento-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    .action-btn-system {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .action-btn-system:hover {
        transform: translateY(-2px);
    }

    .btn-system-primary {
        background: var(--bento-gradient);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 12px rgba(13, 56, 130, 0.25);
    }
    .btn-system-primary:hover {
        box-shadow: 0 6px 16px rgba(13, 56, 130, 0.35);
        color: #ffffff !important;
    }

    /* 2. بانر الهوية الرسمي الموحد (Page Hero Banner) */
    .system-hero-card {
        background: var(--bento-gradient) !important;
        color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(13, 71, 161, 0.2) !important;
        overflow: hidden;
        position: relative;
    }

    .hero-ambient-glow {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        opacity: 0.25;
        background: radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.4) 0%, transparent 60%);
        pointer-events: none;
    }

    .system-hero-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #ffffff;
        color: var(--bento-primary);
        font-size: 2.6rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid rgba(255, 255, 255, 0.45);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        position: relative;
        flex-shrink: 0;
    }

    .system-verified-badge {
        position: absolute;
        bottom: 0px;
        right: 0px;
        background: #10b981;
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.5);
    }

    .system-hero-badge {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 50rem;
        padding: 6px 14px;
        font-size: 0.82rem;
        font-weight: 700;
        backdrop-filter: blur(6px);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .system-hero-gold-badge {
        background: var(--bento-gold-light);
        color: #1e293b;
        border: 1px solid var(--bento-gold);
        border-radius: 50rem;
        padding: 6px 14px;
        font-size: 0.82rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* 3. كروت المحتوى بنمط هوية المنظومة (Bento Cards) */
    .system-card {
        background: #ffffff;
        border: 1px solid var(--bento-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .system-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
    }

    .system-card-header {
        padding: 14px 18px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .system-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .system-card-title {
        color: var(--bento-primary);
        font-weight: 800;
        font-size: 1.08rem;
        margin-bottom: 0;
    }

    /* 4. صفوف بيانات التواصل */
    .system-contact-row {
        padding: 10px 12px;
        border-radius: 12px;
        background-color: #f8fafc;
        border: 1px solid #edf2f7;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.15s ease;
    }
    .system-contact-row:hover {
        background-color: #f1f5f9;
    }

    .system-contact-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background-color: #eff6ff;
        color: var(--bento-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .system-contact-value {
        font-weight: 700;
        font-size: 0.90rem;
        color: #0f172a;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.35;
    }

    .system-email-text {
        font-size: 0.81rem !important;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
        letter-spacing: -0.2px;
        direction: ltr;
        text-align: left;
    }

    /* 5. شارات المهارات */
    .system-skill-pill {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: var(--bento-primary);
        font-weight: 700;
        font-size: 0.84rem;
        padding: 6px 14px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .system-skill-pill:hover {
        background: var(--bento-primary);
        color: #ffffff;
        border-color: var(--bento-primary);
        transform: translateY(-2px);
    }

    /* 6. إشعار النسخ العائم */
    #copyToast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: var(--bento-primary);
        color: #ffffff;
        padding: 10px 22px;
        border-radius: 50rem;
        font-size: 0.9rem;
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(13, 56, 130, 0.35);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }
    #copyToast.show {
        transform: translateX(-50%) translateY(0);
    }

    /* 7. قواعد الطباعة المخصصة للـ PDF */
    @media print {
        body {
            background-color: #ffffff !important;
            font-size: 11pt;
            color: #000000 !important;
        }
        .profile-action-bar,
        .btn-print-hide,
        .navbar,
        .sidebar,
        #sidebar,
        .app-sidebar,
        footer,
        #copyToast {
            display: none !important;
        }
        .main-content, .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .system-hero-card {
            background: #0d3882 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            box-shadow: none !important;
            margin-bottom: 20px !important;
        }
        .system-card {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            page-break-inside: avoid;
            margin-bottom: 16px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-4 py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">

            <!-- 1. شريط الإجراءات العلوي التفاعلي بهوية المنظومة -->
            <div class="profile-action-bar px-3 px-md-4 py-2.5 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2.5">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-light bg-white border action-btn-system text-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>العودة</span>
                    </a>
                    <span class="text-muted small d-none d-sm-inline">|</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold d-none d-md-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-graduation-cap text-primary"></i> منصة الخريجين الرسمية
                    </span>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- زر نسخ ومشاركة الرابط -->
                    <button type="button" onclick="copyProfileLink()" class="btn btn-light bg-white border action-btn-system text-dark shadow-sm" title="نسخ رابط السيرة الذاتية">
                        <i class="fa-solid fa-share-nodes text-primary"></i>
                        <span>مشاركة</span>
                    </button>

                    <!-- زر طباعة السيرة الذاتية كـ PDF -->
                    <button type="button" onclick="window.print()" class="btn btn-light bg-white border action-btn-system text-dark shadow-sm" title="طباعة أو حفظ السيرة الذاتية بصيغة PDF">
                        <i class="fa-solid fa-print text-secondary"></i>
                        <span class="d-none d-sm-inline">طباعة / PDF</span>
                    </button>

                    <!-- زر عرض بطاقة الخريج الرقمية -->
                    @if(auth()->check() && (auth()->id() === $graduate->id || auth()->user()->isAdmin() || auth()->user()->role === 'career_guidance_officer'))
                        <a href="{{ route('graduate.id-card') }}" class="btn btn-outline-primary action-btn-system bg-white shadow-sm" title="معاينة بطاقة الخريج الرقمية المعتمدة">
                            <i class="fa-solid fa-id-badge"></i>
                            <span>البطاقة الرقمية</span>
                        </a>
                    @endif

                    <!-- زر تحميل ملف CV المرفق إن وجد -->
                    @if($gData && $gData->cv_path)
                        <a href="{{ Storage::url($gData->cv_path) }}" target="_blank" class="btn btn-system-primary action-btn-system">
                            <i class="fa-solid fa-file-pdf text-warning"></i>
                            <span>تحميل الـ CV (PDF)</span>
                        </a>
                    @endif

                    <!-- زر مراسلة الخريج للشركات والإدارة -->
                    @if(auth()->check() && auth()->id() !== $graduate->id && in_array(auth()->user()->role, ['company', 'admin', 'career_guidance_officer', 'training_coordinator', 'partnership_officer', 'staff']))
                        <a href="{{ route('messages.show', $graduate->id) }}" class="btn btn-success action-btn-system shadow-sm text-white">
                            <i class="fa-solid fa-comment-dots"></i>
                            <span>مراسلة الخريج</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- 2. بانر التعريف الرئيسي الموحد بهوية المنظومة (Royal Blue & Gold Hero) -->
            <div class="system-hero-card p-4 p-md-5 mb-4 position-relative">
                <div class="hero-ambient-glow"></div>

                <div class="position-relative z-1 d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                    <!-- الصورة الرمزية للخريج بهوية المنظومة -->
                    <div class="system-hero-avatar">
                        {{ mb_substr($graduate->name, 0, 1) }}
                        <div class="system-verified-badge" title="خريج معتمد من جامعة طرابلس">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <!-- البيانات والتعريف الرسمي -->
                    <div class="flex-grow-1 text-center text-md-end">
                        <div class="d-inline-flex align-items-center gap-2 mb-2">
                            <!-- شعار وهوية المنظومة المعتمدة -->
                            <span class="system-hero-badge">
                                <img src="{{ asset('storage/logo.jpg') }}" onerror="this.src='{{ asset('images/logo.jpg') }}'" class="rounded-circle" style="width: 20px; height: 20px; object-fit: cover;">
                                <span>{{ $university }} — مكتب تدريب الخريجين</span>
                            </span>
                        </div>

                        <h1 class="fw-bold text-white mb-2" style="font-size: 2rem; letter-spacing: -0.5px;">
                            {{ $graduate->name }}
                        </h1>

                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 text-white-50 mb-3 flex-wrap" style="font-size: 0.96rem;">
                            <span style="color: #fbbf24; font-weight: 700;">
                                <i class="fa-solid fa-graduation-cap me-1"></i> {{ Str::startsWith($major, 'قسم') ? $major : 'قسم ' . $major }}
                            </span>
                            <span>•</span>
                            <span class="text-white text-opacity-90">{{ $faculty }}</span>
                        </div>

                        <!-- شارات الإحصاء السريع بهوية المنظومة -->
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 flex-wrap pt-1">
                            <span class="system-hero-badge">
                                <i class="fa-solid fa-award text-warning"></i>
                                <span>{{ $degree }}</span>
                            </span>

                            @if($gpa)
                                <span class="system-hero-gold-badge">
                                    <i class="fa-solid fa-chart-pie"></i>
                                    <span>المعدل: <strong dir="ltr">{{ $gpa }}%</strong> {{ $gpaEval ? '— ' . $gpaEval : '' }}</span>
                                </span>
                            @endif

                            @if($gradYear)
                                <span class="system-hero-badge">
                                    <i class="fa-solid fa-calendar-days text-warning"></i>
                                    <span>دفعة: <strong>{{ $gradYear }}</strong></span>
                                </span>
                            @endif

                            <!-- شارة الحالة الوظيفية الرسمية -->
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold {{ $statusInfo['badge_class'] }}" style="font-size: 0.82rem;">
                                <i class="{{ $statusInfo['icon'] }} me-1"></i>
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. المحتوى الرئيسي المقسم لعمودين متوازنين بهوية المنظومة -->
            <div class="row g-4">

                <!-- العمود الجانبي (معلومات التواصل، المهارات، اللغات) -->
                <div class="col-lg-4 col-md-5 order-lg-1 order-2">
                    <div class="d-flex flex-column gap-4">

                        <!-- بطاقة التواصل -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-address-book"></i>
                                </div>
                                <div>
                                    <h6 class="system-card-title">بيانات الاتصال والتواصل</h6>
                                    <span class="text-muted small">قنوات التواصل المباشرة مع الخريج</span>
                                </div>
                            </div>
                            <div class="p-3.5 p-md-4">
                                <!-- البريد الإلكتروني -->
                                <div class="system-contact-row">
                                    <div class="system-contact-icon">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">البريد الإلكتروني</span>
                                        <a href="mailto:{{ $graduate->email }}" class="system-contact-value system-email-text text-primary text-decoration-none" title="{{ $graduate->email }}" dir="ltr">
                                            {{ $graduate->email }}
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-muted p-1" onclick="navigator.clipboard.writeText('{{ $graduate->email }}'); showToast('تم نسخ البريد الإلكتروني');" title="نسخ البريد">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>

                                <!-- الهاتف -->
                                @if($phone)
                                <div class="system-contact-row">
                                    <div class="system-contact-icon" style="background-color: #f0fdf4; color: #15803d;">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">رقم الهاتف</span>
                                        <a href="tel:{{ $phone }}" class="system-contact-value text-dark text-decoration-none" dir="ltr">
                                            {{ $phone }}
                                        </a>
                                    </div>
                                    @if($whatsappPhone)
                                        <a href="https://wa.me/{{ $whatsappPhone }}" target="_blank" class="btn btn-sm text-white d-inline-flex align-items-center justify-content-center shadow-sm" style="background-color: #25D366; width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;" title="محادثة عبر واتساب">
                                            <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem; color: #ffffff;"></i>
                                        </a>
                                    @endif
                                </div>
                                @endif

                                <!-- المدينة والعنوان -->
                                @if($address || $city)
                                <div class="system-contact-row">
                                    <div class="system-contact-icon" style="background-color: #fef2f2; color: #dc2626;">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">العنوان والمدينة</span>
                                        <span class="system-contact-value">{{ $address ? $address : $city }}</span>
                                    </div>
                                </div>
                                @endif

                                <!-- الرقم الوطني / رقم القيد -->
                                @if($graduate->national_id || ($gData && $gData->national_id))
                                <div class="system-contact-row">
                                    <div class="system-contact-icon" style="background-color: #faf5ff; color: #7e22ce;">
                                        <i class="fa-solid fa-id-card"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">رقم القيد / الوطني</span>
                                        <span class="system-contact-value" dir="ltr">{{ $graduate->national_id ?? $gData->national_id }}</span>
                                    </div>
                                </div>
                                @endif

                                <!-- الروابط المهنية -->
                                @if(($gData && ($gData->linkedin_url || $gData->portfolio_url)))
                                <div class="pt-3 border-top mt-3 d-flex gap-2">
                                    @if($gData->linkedin_url)
                                        <a href="{{ $gData->linkedin_url }}" target="_blank" class="btn btn-outline-primary flex-grow-1 py-2 rounded-3 fw-bold d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                                            <i class="fa-brands fa-linkedin fs-5"></i>
                                            <span>LinkedIn</span>
                                        </a>
                                    @endif
                                    @if($gData->portfolio_url)
                                        <a href="{{ $gData->portfolio_url }}" target="_blank" class="btn btn-outline-dark flex-grow-1 py-2 rounded-3 fw-bold d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                                            <i class="fa-solid fa-globe fs-5"></i>
                                            <span>معرض الأعمال</span>
                                        </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- بطاقة المهارات والقدرات -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-info-subtle text-info">
                                    <i class="fa-solid fa-toolbox"></i>
                                </div>
                                <div>
                                    <h6 class="system-card-title">المهارات والكفاءات</h6>
                                    <span class="text-muted small">القدرات الفنية والمهنية للخريج</span>
                                </div>
                            </div>
                            <div class="p-3.5 p-md-4">
                                @if(!empty($skills) && count($skills) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($skills as $skill)
                                            <span class="system-skill-pill">
                                                <i class="fa-solid fa-circle-check text-primary" style="font-size: 0.75rem;"></i>
                                                <span>{{ $skill }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fa-solid fa-clipboard-list fa-2x opacity-25 mb-2"></i>
                                        <p class="small mb-0">لم يتم تحديد مهارات بعد في هذا الملف.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- بطاقة اللغات -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-language"></i>
                                </div>
                                <div>
                                    <h6 class="system-card-title">اللغات المتقنة</h6>
                                    <span class="text-muted small">مهارات التواصل اللغوي</span>
                                </div>
                            </div>
                            <div class="p-3.5 p-md-4">
                                @if(!empty($languages) && count($languages) > 0)
                                    <div class="d-flex flex-column gap-2.5">
                                        @foreach($languages as $lang)
                                            <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light border">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa-solid fa-globe text-secondary"></i>
                                                    <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $lang }}</span>
                                                </div>
                                                <span class="badge bg-white text-secondary border rounded-pill px-2.5 py-1 small">
                                                    {{ $loop->first ? 'اللغة الأم / متقن' : 'متقدم' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 p-2.5 rounded-3 bg-light border">
                                        <i class="fa-solid fa-globe text-secondary"></i>
                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">اللغة العربية (اللغة الأم)</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <!-- العمود الرئيسي (المسار الأكاديمي، الخبرات العملية، الدورات التدريبية) -->
                <div class="col-lg-8 col-md-7 order-lg-2 order-1">
                    <div class="d-flex flex-column gap-4">

                        <!-- بطاقة النبذة المهنية -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-success-subtle text-success">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">نبذة مهنية</h5>
                                    <span class="text-muted small">ملخص الأهداف والمجال التخصصي</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <p class="text-secondary lh-lg mb-0" style="font-size: 0.95rem;">
                                    خريج متميز من <strong>{{ $university }}</strong> — <strong>{{ $faculty }}</strong> في تخصص <strong>{{ Str::startsWith($major, 'قسم') ? Str::replaceFirst('قسم ', '', $major) : $major }}</strong>. 
                                    يمتلك خلفية معرفية وتطبيقية متينة في التخصص، مع جاهزية تامة للمشاركة في مشاريع العمل والتدريب والتطوير الوظيفي لدى المؤسسات والشركات الرائدة.
                                    @if($workExp)
                                        يمتلك خبرة سابقة تتمثل في: <em>"{{ $workExp }}"</em>.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- بطاقة المؤهل العلمي (Academic Qualification) -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">المؤهل العلمي والمسار الأكاديمي</h5>
                                    <span class="text-muted small">تفاصيل الشهادة والجامعة المعتمدة</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="p-3.5 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.08rem;">
                                                {{ $degree }} في {{ Str::startsWith($major, 'قسم') ? Str::replaceFirst('قسم ', '', $major) : $major }}
                                            </h5>
                                            <div class="text-primary fw-bold" style="font-size: 0.92rem;">
                                                <i class="fa-solid fa-building-columns me-1"></i> {{ $university }} — {{ $faculty }}
                                            </div>
                                        </div>
                                        @if($gradYear)
                                            <span class="badge bg-primary text-white rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.82rem;">
                                                سنة التخرج: {{ $gradYear }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="row g-3 mt-1 pt-2 border-top">
                                        @if($gpa)
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <i class="fa-solid fa-chart-line text-success fa-fw"></i>
                                                <span>المعدل العام التراكمي:</span>
                                                <strong class="text-dark" dir="ltr">{{ $gpa }}%</strong>
                                                @if($gpaEval)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">{{ $gpaEval }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <i class="fa-solid fa-certificate text-warning fa-fw"></i>
                                                <span>الدرجة والاعتماد:</span>
                                                <strong class="text-dark">{{ $degree }} معتمد</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- بطاقة الخبرات العملية والمهنية -->
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-dark-subtle text-dark">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">الخبرات العملية والمهنية</h5>
                                    <span class="text-muted small">السجل الوظيفي والخبرات السابقة</span>
                                </div>
                            </div>
                            <div class="p-4">
                                @if($workExp)
                                    <div class="p-3.5 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                                                <i class="fa-solid fa-user-check me-1"></i> خبرة مهنية سابقة
                                            </span>
                                            <span class="badge bg-white text-secondary border rounded-pill px-2.5 py-1 small">
                                                سجل العمل والأنشطة
                                            </span>
                                        </div>
                                        <div class="text-dark fw-semibold lh-lg p-1" style="font-size: 0.95rem;">
                                            {{ $workExp }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-briefcase fa-2x opacity-25 mb-2"></i>
                                        <p class="mb-1 fw-semibold">خريج جديد جاهز لبدء المسار المهني</p>
                                        <span class="small">لم يتم إدراج خبرات سابقة، الخريج متاح للفرص الوظيفية والبرامج التدريبية المباشرة.</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- بطاقة الشهادات المهنية والتخصصية إن وجدت -->
                        @if($certifications)
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">الشهادات والتراخيص المهنية</h5>
                                    <span class="text-muted small">الشهادات الاحترافية والتخصصية</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="p-3 rounded-3 bg-light border text-dark fw-semibold">
                                    {{ $certifications }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- بطاقة التدريبات والدورات المعتمدة عبر المنظومة -->
                        @if($graduate->trainingApplications && $graduate->trainingApplications->count() > 0)
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-info-subtle text-info">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">الدورات التدريبية المعتمدة</h5>
                                    <span class="text-muted small">البرامج التأهيلية التي تم اجتيازها عبر المنظومة</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    @foreach($graduate->trainingApplications as $app)
                                        @if($app->training)
                                        @php
                                            $totalDays = $app->training->total_days_count ?? 1;
                                            $attendedDays = $app->attended_days_count;
                                            $attendancePct = $app->attendance_percentage;
                                            
                                            // ألوان الشارات حسب النسبة
                                            if ($attendancePct >= 80) {
                                                $badgeColor = 'success';
                                                $barColor = '#10b981';
                                            } elseif ($attendancePct >= 50) {
                                                $badgeColor = 'warning';
                                                $barColor = '#f59e0b';
                                            } else {
                                                $badgeColor = 'danger';
                                                $barColor = '#ef4444';
                                            }

                                            // تسمية نوع البرنامج
                                            $typeLabel = match($app->training->type) {
                                                'workshop' => 'ورشة عمل',
                                                'course' => 'دورة تدريبية',
                                                'seminar' => 'ندوة علمية',
                                                'internship' => 'تدريب عملي',
                                                default => ($app->training->type ?: 'برنامج تدريبي')
                                            };
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-2">
                                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 small fw-bold">
                                                                <i class="fa-solid fa-check me-1"></i> معتمد
                                                            </span>
                                                            <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} border border-{{ $badgeColor }}-subtle rounded-pill px-2.5 py-0.5 small fw-bold d-inline-flex align-items-center gap-1">
                                                                <i class="fa-solid fa-user-check"></i> حضور {{ $attendancePct }}%
                                                            </span>
                                                        </div>
                                                        <span class="text-muted small">
                                                            {{ $typeLabel }}
                                                        </span>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">{{ $app->training->title }}</h6>
                                                    @if($app->training->description)
                                                        <p class="text-muted small mb-2 line-clamp-2" style="font-size: 0.84rem;">{{ Str::limit($app->training->description, 75) }}</p>
                                                    @endif

                                                    {{-- شريط نسبة الحضور وإحصائيات الأيام --}}
                                                    <div class="p-2 rounded-2 bg-white border border-light-subtle mb-2.5">
                                                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.76rem;">
                                                            <span class="text-secondary fw-semibold">
                                                                <i class="fa-solid fa-clipboard-check text-primary me-1"></i>حضور الورشة:
                                                            </span>
                                                            <span class="fw-bold text-{{ $badgeColor }}">
                                                                {{ $attendancePct }}% 
                                                                <span class="text-muted fw-normal">({{ $attendedDays }} من {{ $totalDays }} {{ $totalDays > 1 ? 'أيام' : 'يوم' }})</span>
                                                            </span>
                                                        </div>
                                                        <div class="progress rounded-pill" style="height: 5px; background-color: #f1f5f9;">
                                                            <div class="progress-bar rounded-pill" role="progressbar" 
                                                                 style="width: {{ $attendancePct }}%; background-color: {{ $barColor }};" 
                                                                 aria-valuenow="{{ $attendancePct }}" aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="pt-2 border-top text-muted small d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 0.78rem;">
                                                    @if($app->training->start_date)
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="fa-solid fa-calendar-check text-primary"></i>
                                                        <span>{{ $app->training->start_date->format('Y-m-d') }}</span>
                                                    </div>
                                                    @endif
                                                    @if($app->training->location)
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="fa-solid fa-location-dot text-secondary"></i>
                                                        <span>{{ $app->training->location }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- ملاحظات أو توصيات إضافية -->
                        @if($gData && $gData->notes)
                        <div class="system-card">
                            <div class="system-card-header">
                                <div class="system-icon-box bg-secondary-subtle text-secondary">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </div>
                                <div>
                                    <h5 class="system-card-title">ملاحظات ومعلومات إضافية</h5>
                                    <span class="text-muted small">بيانات وتوصيات مقدمة من المكتب</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <p class="text-secondary lh-lg mb-0" style="font-size: 0.92rem;">
                                    {{ $gData->notes }}
                                </p>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- عنصر الإشعار العائم عند النسخ (Toast) -->
<div id="copyToast">
    <i class="fa-solid fa-circle-check text-white fs-5"></i>
    <span id="toastMessage">تم نسخ الرابط بنجاح!</span>
</div>
@endsection

@push('scripts')
<script>
function showToast(message) {
    const toast = document.getElementById('copyToast');
    const msgSpan = document.getElementById('toastMessage');
    if (!toast || !msgSpan) return;
    msgSpan.textContent = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2600);
}

function copyProfileLink() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('تم نسخ رابط السيرة الذاتية إلى الحافظة بنجاح!');
        }).catch(() => {
            prompt('انسخ الرابط أدناه:', window.location.href);
        });
    } else {
        prompt('انسخ الرابط أدناه:', window.location.href);
    }
}
</script>
@endpush
