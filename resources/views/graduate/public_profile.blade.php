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
        'seeking_opportunities' => ['label' => 'باحث عن فرصة عمل', 'class' => 'status-seeking', 'icon' => 'fa-magnifying-glass'],
        'employed'              => ['label' => 'موظف حالياً', 'class' => 'status-employed', 'icon' => 'fa-circle-check'],
        'training'              => ['label' => 'في فترة تدريب', 'class' => 'status-training', 'icon' => 'fa-laptop-code'],
        'freelancer'            => ['label' => 'عمل حر / مستقل', 'class' => 'status-freelance', 'icon' => 'fa-user-tie'],
        'unemployed'            => ['label' => 'غير موظف', 'class' => 'status-unemployed', 'icon' => 'fa-clock'],
        'further_study'         => ['label' => 'مستكمل للدراسات العليا', 'class' => 'status-study', 'icon' => 'fa-user-graduate'],
        'continuing_education'  => ['label' => 'مستكمل للدراسات العليا', 'class' => 'status-study', 'icon' => 'fa-user-graduate'],
    ];
    $statusInfo = $statusMap[$empStatus] ?? ['label' => 'غير محدد', 'class' => 'status-neutral', 'icon' => 'fa-user'];

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
    :root {
        --profile-primary: #1e40af;
        --profile-primary-light: #3b82f6;
        --profile-accent: #0284c7;
        --profile-dark: #0f172a;
        --profile-slate: #334155;
        --profile-light-bg: #f8fafc;
        --profile-border: #e2e8f0;
    }

    body {
        background-color: #f1f5f9;
    }

    /* Action Bar */
    .profile-action-bar {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(226, 232, 240, 0.85);
        border-radius: 16px;
        box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.06);
    }

    .action-btn-custom {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .action-btn-custom:hover {
        transform: translateY(-2px);
    }

    /* Hero Banner */
    .profile-hero-card {
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #1e3a8a 100%);
        box-shadow: 0 12px 32px -5px rgba(15, 23, 42, 0.22);
        overflow: hidden;
        position: relative;
        color: #ffffff;
    }

    .hero-pattern-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px), radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 24px 24px;
        background-position: 0 0, 12px 12px;
        pointer-events: none;
        opacity: 0.75;
    }

    .hero-glow {
        position: absolute;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(30, 64, 175, 0) 70%);
        top: -80px;
        left: -40px;
        pointer-events: none;
    }

    .graduate-hero-avatar {
        width: 118px;
        height: 118px;
        border-radius: 24px;
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
        color: #ffffff;
        font-size: 3rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
        position: relative;
        flex-shrink: 0;
    }

    .verified-tick-badge {
        position: absolute;
        bottom: -6px;
        right: -6px;
        background: #10b981;
        color: #ffffff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        border: 3px solid #0f172a;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
    }

    .hero-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 50rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(6px);
        font-size: 0.85rem;
        font-weight: 600;
        color: #e2e8f0;
    }

    /* Modern Cards */
    .profile-card-modern {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .profile-card-modern:hover {
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
    }

    .card-header-modern {
        padding: 16px 20px;
        background: #fafcff;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }

    /* Contact Details */
    .contact-item-row {
        padding: 10px 12px;
        border-radius: 12px;
        background-color: #f8fafc;
        border: 1px solid #edf2f7;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background-color 0.15s;
    }
    .contact-item-row:hover {
        background-color: #f1f5f9;
    }

    .contact-icon-circle {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background-color: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .contact-value-text {
        font-weight: 700;
        font-size: 0.90rem;
        color: #0f172a;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.35;
    }

    .contact-email-text {
        font-size: 0.80rem !important;
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
        letter-spacing: -0.3px;
        direction: ltr;
        text-align: left;
    }

    /* Status Badges */
    .status-badge-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 50rem;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .status-seeking {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .status-employed {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .status-training {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .status-freelance {
        background-color: #faf5ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }
    .status-unemployed {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .status-study {
        background-color: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }
    .status-neutral {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: currentColor;
        animation: pulse-animation 1.8s infinite;
    }
    @keyframes pulse-animation {
        0% { transform: scale(0.9); opacity: 0.8; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.8; }
    }

    /* Skills Badges */
    .skill-tag-pill {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.84rem;
        padding: 6px 14px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .skill-tag-pill:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    /* Copy Toast */
    #copyToast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: #0f172a;
        color: #ffffff;
        padding: 10px 22px;
        border-radius: 50rem;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
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

    /* Print Stylesheet */
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
        .profile-hero-card {
            background: #0f172a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #ffffff !important;
            border-radius: 12px !important;
            box-shadow: none !important;
            margin-bottom: 20px !important;
        }
        .profile-card-modern {
            box-shadow: none !important;
            border: 1px solid #cbd5e1 !important;
            page-break-inside: avoid;
            margin-bottom: 16px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 py-md-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            <!-- 1. شريط الإجراءات العلوي التفاعلي -->
            <div class="profile-action-bar px-3 px-md-4 py-2.5 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2.5">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-light bg-white border action-btn-custom text-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>العودة</span>
                    </a>
                    <span class="text-muted small d-none d-sm-inline">|</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold d-none d-md-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-id-card"></i> السيرة الذاتية الرسمية
                    </span>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- زر نسخ ومشاركة الرابط -->
                    <button type="button" onclick="copyProfileLink()" class="btn btn-light bg-white border action-btn-custom text-dark shadow-sm" title="نسخ رابط السيرة الذاتية">
                        <i class="fa-solid fa-share-nodes text-primary"></i>
                        <span>مشاركة</span>
                    </button>

                    <!-- زر طباعة السيرة الذاتية كـ PDF -->
                    <button type="button" onclick="window.print()" class="btn btn-light bg-white border action-btn-custom text-dark shadow-sm" title="طباعة أو حفظ السيرة الذاتية بصيغة PDF">
                        <i class="fa-solid fa-print text-secondary"></i>
                        <span class="d-none d-sm-inline">طباعة / PDF</span>
                    </button>

                    <!-- زر عرض بطاقة الخريج الرقمية -->
                    @if(auth()->check() && (auth()->id() === $graduate->id || auth()->user()->isAdmin() || auth()->user()->role === 'career_guidance_officer'))
                        <a href="{{ route('graduate.id-card') }}" class="btn btn-outline-primary action-btn-custom bg-white shadow-sm" title="معاينة بطاقة الخريج الرقمية المعتمدة">
                            <i class="fa-solid fa-id-badge"></i>
                            <span>البطاقة الرقمية</span>
                        </a>
                    @endif

                    <!-- زر تحميل ملف CV المرفق إن وجد -->
                    @if($gData && $gData->cv_path)
                        <a href="{{ Storage::url($gData->cv_path) }}" target="_blank" class="btn btn-primary action-btn-custom shadow-sm text-white" style="background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); border: none;">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span>تحميل الـ CV (PDF)</span>
                        </a>
                    @endif

                    <!-- زر مراسلة الخريج للشركات -->
                    @if(auth()->check() && auth()->user()->role === 'company')
                        <a href="{{ route('messages.show', $graduate->id) }}" class="btn btn-success action-btn-custom shadow-sm text-white">
                            <i class="fa-solid fa-comment-dots"></i>
                            <span>مراسلة الخريج</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- 2. بطاقة الغلاف والتعريف بالخريج (Hero Card) -->
            <div class="profile-hero-card p-4 p-md-5 mb-4 position-relative">
                <div class="hero-pattern-bg"></div>
                <div class="hero-glow"></div>

                <div class="position-relative z-1 d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                    <!-- الصورة الشخصية / الأفاتار -->
                    <div class="graduate-hero-avatar">
                        {{ mb_substr($graduate->name, 0, 1) }}
                        <div class="verified-tick-badge" title="خريج معتمد من جامعة طرابلس">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <!-- البيانات والتعريف -->
                    <div class="flex-grow-1 text-center text-md-end">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2);">
                            <i class="fa-solid fa-building-columns text-info" style="font-size: 0.85rem;"></i>
                            <span class="small fw-bold text-white">{{ $university }} • سجل الخريجين الرسمي</span>
                        </div>

                        <h1 class="fw-bold text-white mb-2" style="font-size: 1.85rem; letter-spacing: -0.5px;">
                            {{ $graduate->name }}
                        </h1>

                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 text-white-50 mb-3 flex-wrap" style="font-size: 0.95rem;">
                            <span class="text-warning fw-semibold">
                                <i class="fa-solid fa-graduation-cap me-1"></i> {{ Str::startsWith($major, 'قسم') ? $major : 'قسم ' . $major }}
                            </span>
                            <span>•</span>
                            <span>{{ $faculty }}</span>
                        </div>

                        <!-- شارات الإحصاء السريع -->
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2.5 flex-wrap pt-1">
                            <span class="hero-stat-pill">
                                <i class="fa-solid fa-award text-warning"></i>
                                <span>{{ $degree }}</span>
                            </span>

                            @if($gpa)
                                <span class="hero-stat-pill">
                                    <i class="fa-solid fa-chart-pie text-info"></i>
                                    <span>المعدل: <strong dir="ltr">{{ $gpa }}%</strong> {{ $gpaEval ? '— ' . $gpaEval : '' }}</span>
                                </span>
                            @endif

                            @if($gradYear)
                                <span class="hero-stat-pill">
                                    <i class="fa-solid fa-calendar-days text-light"></i>
                                    <span>دفعة: <strong>{{ $gradYear }}</strong></span>
                                </span>
                            @endif

                            <!-- شارة الحالة الوظيفية -->
                            <span class="status-badge-custom {{ $statusInfo['class'] }}">
                                <span class="pulse-dot"></span>
                                <i class="fa-solid {{ $statusInfo['icon'] }}"></i>
                                <span>{{ $statusInfo['label'] }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. المحتوى الرئيسي المقسم لعمودين متوازنين -->
            <div class="row g-4">

                <!-- العمود الجانبي (معلومات التواصل، المهارات، اللغات) -->
                <div class="col-lg-4 col-md-5 order-lg-1 order-2">
                    <div class="d-flex flex-column gap-4">

                        <!-- بطاقة التواصل -->
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-address-book"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">بيانات الاتصال والتواصل</h6>
                                    <span class="text-muted small">قنوات التواصل المباشرة مع الخريج</span>
                                </div>
                            </div>
                            <div class="p-3.5 p-md-4">
                                <!-- البريد الإلكتروني -->
                                <div class="contact-item-row">
                                    <div class="contact-icon-circle">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">البريد الإلكتروني</span>
                                        <a href="mailto:{{ $graduate->email }}" class="contact-value-text contact-email-text text-primary text-decoration-none" title="{{ $graduate->email }}" dir="ltr">
                                            {{ $graduate->email }}
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-muted p-1" onclick="navigator.clipboard.writeText('{{ $graduate->email }}'); showToast('تم نسخ البريد الإلكتروني');" title="نسخ البريد">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>

                                <!-- الهاتف -->
                                @if($phone)
                                <div class="contact-item-row">
                                    <div class="contact-icon-circle" style="background-color: #f0fdf4; color: #15803d;">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">رقم الهاتف</span>
                                        <a href="tel:{{ $phone }}" class="contact-value-text text-dark text-decoration-none" dir="ltr">
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
                                <div class="contact-item-row">
                                    <div class="contact-icon-circle" style="background-color: #fef2f2; color: #dc2626;">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">العنوان والمدينة</span>
                                        <span class="contact-value-text">{{ $address ? $address : $city }}</span>
                                    </div>
                                </div>
                                @endif

                                <!-- الرقم الوطني / رقم القيد إذا وجد -->
                                @if($graduate->national_id || ($gData && $gData->national_id))
                                <div class="contact-item-row">
                                    <div class="contact-icon-circle" style="background-color: #faf5ff; color: #7e22ce;">
                                        <i class="fa-solid fa-id-card"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="text-muted small d-block" style="font-size: 0.76rem;">رقم القيد / الوطني</span>
                                        <span class="contact-value-text" dir="ltr">{{ $graduate->national_id ?? $gData->national_id }}</span>
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

                        <!-- بطاقة المهارات والكفاءات -->
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-info-subtle text-info">
                                    <i class="fa-solid fa-toolbox"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">المهارات والقدرات</h6>
                                    <span class="text-muted small">الكفاءات التقنية والمهنية</span>
                                </div>
                            </div>
                            <div class="p-3.5 p-md-4">
                                @if(!empty($skills) && count($skills) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($skills as $skill)
                                            <span class="skill-tag-pill">
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-language"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">اللغات المتقنة</h6>
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-success-subtle text-success">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">نبذة مهنية</h5>
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">المؤهل العلمي والمسار الأكاديمي</h5>
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-dark-subtle text-dark">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">الخبرات العملية والمهنية</h5>
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">الشهادات والتراخيص المهنية</h5>
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-info-subtle text-info">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">الدورات التدريبية المعتمدة</h5>
                                    <span class="text-muted small">البرامج التأهيلية التي تم اجتيازها عبر المنظومة</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="row g-3">
                                    @foreach($graduate->trainingApplications as $app)
                                        @if($app->training)
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 small fw-bold">
                                                            <i class="fa-solid fa-check me-1"></i> معتمد
                                                        </span>
                                                        <span class="text-muted small">
                                                            {{ $app->training->type ?? 'تدريب عملي' }}
                                                        </span>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1">{{ $app->training->title }}</h6>
                                                    <p class="text-muted small mb-2 line-clamp-2">{{ Str::limit($app->training->description, 70) }}</p>
                                                </div>
                                                @if($app->training->start_date)
                                                <div class="pt-2 border-top text-muted small d-flex align-items-center gap-1">
                                                    <i class="fa-solid fa-calendar-check text-primary"></i>
                                                    <span>{{ $app->training->start_date->format('Y-m-d') }}</span>
                                                </div>
                                                @endif
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
                        <div class="profile-card-modern">
                            <div class="card-header-modern">
                                <div class="header-icon-box bg-secondary-subtle text-secondary">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">ملاحظات ومعلومات إضافية</h5>
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
    <i class="fa-solid fa-circle-check text-success fs-5"></i>
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
