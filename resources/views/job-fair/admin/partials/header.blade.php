{{-- ══════════════════════════════════════════════════════════════════
     الشريط العلوي الموحد لإدارة المعرض والفعاليات
     تصميم متناسق تماماً مع صفحة المعرض الرئيسية (job-fair/public)
     ══════════════════════════════════════════════════════════════════ --}}
@php
    $page = $page ?? 'show';
    $currentStatus = $fair->status ?? 'published';
    $statusColors = [
        'draft' => ['bg' => 'bg-secondary text-white', 'label' => 'مسودة'],
        'published' => ['bg' => 'bg-success text-white', 'label' => 'منشور ومتاح'],
        'ongoing' => ['bg' => 'bg-warning text-dark', 'label' => 'جارٍ الآن 🟢'],
        'completed' => ['bg' => 'bg-info text-dark', 'label' => 'منتهي']
    ];
    $st = $statusColors[$currentStatus] ?? ['bg' => 'bg-light text-dark', 'label' => $currentStatus];
    
    // تحديد رابط العودة الافتراضي
    $defaultBackUrl = isset($backUrl) ? $backUrl : (
        $page === 'show'
            ? route('job-fair.admin.index') 
            : route('job-fair.admin.show', $fair->id)
    );
@endphp

<style>
    /* ══════════════════════════════════════════════════════════════════
       هيدر إدارة المعرض الموحد مع شعارات متطابقة تماماً لصفحة المعرض العامة
       ══════════════════════════════════════════════════════════════════ */
    .job-fair-detail-hero {
        background: linear-gradient(135deg, #091f3c 0%, #03488a 55%, #045db0 100%) !important;
        color: #ffffff !important;
        border-radius: 20px;
        padding: 1.25rem 1.6rem;
        box-shadow: 0 10px 30px rgba(3, 72, 138, 0.22);
        position: relative;
        overflow: visible !important;
        border-bottom: 3.5px solid #eeca3e;
        margin-bottom: 1.5rem;
    }

    /* تأثيرات خلفية ناعمة للهيدر */
    .job-fair-detail-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 220px;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(238, 202, 62, 0.16) 0%, transparent 70%);
        pointer-events: none;
        border-radius: 20px;
    }
    .job-fair-detail-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 180px;
        height: 100%;
        background: radial-gradient(circle at bottom left, rgba(14, 165, 233, 0.14) 0%, transparent 70%);
        pointer-events: none;
        border-radius: 20px;
    }

    /* ── شريط الشعارات العلوي (Brand Logos Strip) مطابق لصفحة المعرض العامة ── */
    .jf-admin-brand-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.9rem;
        padding-bottom: 0.95rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.14);
        position: relative;
        z-index: 2;
    }

    .jf-admin-brand-cluster {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.85rem;
    }

    /* شعار الجامعة الدائري مع إطار ذهبي بارز */
    .jf-brand-main-logo {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #eeca3e;
        object-fit: cover;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.28);
        background: #ffffff;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }
    .jf-brand-main-logo:hover {
        transform: scale(1.06);
    }

    .jf-brand-text {
        line-height: 1.25;
    }
    .jf-brand-text .main {
        color: #ffffff;
        font-weight: 700;
        font-size: 0.92rem;
        letter-spacing: -0.2px;
    }
    .jf-brand-text .sub {
        color: #eeca3e;
        font-size: 0.74rem;
        font-weight: 700;
    }

    /* فاصل رأسي زجاجي شفاف بين الشعارات */
    .jf-brand-divider {
        width: 1px;
        height: 32px;
        background: rgba(255, 255, 255, 0.25);
        margin: 0 4px;
        flex-shrink: 0;
    }

    /* شعار المعرض الأبيض الشفاف بدون أي خلفيات بيضاء مربعة */
    .jf-brand-fair-logo {
        height: 38px;
        width: auto;
        max-width: 140px;
        object-fit: contain;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));
        transition: transform 0.2s ease;
    }
    .jf-brand-fair-logo:hover {
        transform: scale(1.05);
    }

    /* شعار شركة الواحة الأبيض الشفاف (الراعي الاستراتيجي) */
    .jf-brand-waha-logo {
        height: 34px;
        width: auto;
        max-width: 130px;
        object-fit: contain;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.35));
        transition: transform 0.2s ease;
    }
    .jf-brand-waha-logo:hover {
        transform: scale(1.05);
    }

    /* شارة الراعي الاستراتيجي الذهبية المتناسقة */
    .jf-strategic-badge {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.3));
        border: 1px solid rgba(245, 158, 11, 0.55);
        color: #fef08a;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    /* أزرار التنقل السريع في الشريط العلوي */
    .jf-quick-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.22);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .jf-quick-link:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.45);
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    /* ── شريط الإجراءات والعمليات الموحد ── */
    .fair-actions-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .fair-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.95rem;
        font-size: 0.84rem;
        font-weight: 600;
        border-radius: 50rem;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        white-space: nowrap;
    }
    .fair-btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
    }

    .fair-btn-glass {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(4px);
    }
    .fair-btn-glass:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.5);
    }

    /* إصلاح القوائم المنسدلة في الشريط العلوي لمنع حسابات Popper الخاطئة في RTL */
    .fair-actions-toolbar .dropdown-menu {
        z-index: 1075 !important;
        position: absolute !important;
        top: 100% !important;
        margin-top: 8px !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.22) !important;
    }

    @media (max-width: 991.98px) {
        .job-fair-detail-hero {
            padding: 1.15rem 1rem;
        }
        .fair-actions-toolbar {
            width: 100%;
            margin-top: 0.5rem;
            justify-content: flex-start;
        }
    }
</style>

<div class="job-fair-detail-hero">
    {{-- ══════════════════════════════════
         القسم العلوي: الشعارات الرسمية (تطابق كامل لصفحة المعرض العامة)
         ══════════════════════════════════ --}}
    <div class="jf-admin-brand-strip">
        <div class="jf-admin-brand-cluster">
            <!-- 1. شعار الجامعة + اسم المكتب والجامعة -->
            <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="الصفحة الرئيسية للنظام - مكتب تدريب الخريجين جامعة طرابلس">
                <img src="{{ asset('images/logo.jpg') }}" alt="شعار الجامعة" class="jf-brand-main-logo" onerror="this.src='{{ asset('images/gto_logo.jpg') }}'">
                <div class="jf-brand-text d-none d-sm-block text-end">
                    <div class="main">مكتب تدريب الخريجين</div>
                    <div class="sub">جامعة طرابلس</div>
                </div>
            </a>

            <!-- فاصل رأسي -->
            <div class="jf-brand-divider"></div>

            <!-- 2. شعار المعرض الأبيض الشفاف -->
            <a href="{{ route('job-fair.public', $fair->id) }}" target="_blank" title="شعار {{ $fair->title }} - فتح صفحة المعرض العامة">
                <img src="{{ $fair->white_logo_url ?? asset('images/job_fair_logo_white.png') }}" class="jf-brand-fair-logo" alt="{{ $fair->title }}" onerror="this.onerror=null;this.src='{{ asset('images/job_fair_logo_white.png') }}';">
            </a>

            <!-- فاصل رأسي -->
            <div class="jf-brand-divider d-none d-sm-block"></div>

            <!-- 3. شعار شركة الواحة لتنظيم المعارض والمؤتمرات -->
            <div class="d-none d-sm-flex align-items-center">
                <img src="{{ asset('images/wahaexpo_horizontal_white.png') }}" alt="شركة الواحة لتنظيم المعارض والمؤتمرات" class="jf-brand-waha-logo" title="شركة الواحة لتنظيم المعارض والمؤتمرات" onerror="this.src='{{ asset('images/wahaexpo_logo_white.png') }}';">
            </div>
        </div>

        <!-- أزرار التنقل السريع على يسار الهيدر -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('job-fair.public', $fair->id) }}" class="jf-quick-link" target="_blank" title="معاينة الصفحة العامة للمعرض كزائر">
                <i class="fas fa-external-link-alt text-warning"></i>
                <span>صفحة عامة</span>
            </a>
            <a href="{{ route('job-fair.admin.index') }}" class="jf-quick-link" title="العودة لقائمة المعارض والملتقيات">
                <i class="fas fa-arrow-right"></i>
                <span>قائمة المعارض</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════
         القسم السفلي: العنوان، البيانات الوصفية، وشريط الإجراءات
         ══════════════════════════════════ --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 2;">
        
        <!-- تفاصيل الصفحة الحالية والمعرض -->
        <div class="d-flex align-items-center gap-3">
            @if(!empty($defaultBackUrl))
            <a href="{{ $defaultBackUrl }}" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.3);" title="الرجوع">
                <i class="fas fa-arrow-right"></i>
            </a>
            @endif

            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="fw-bold mb-0 fs-4 text-white">
                        {{ $title ?? $fair->title }}
                    </h1>
                    <span class="badge {{ $st['bg'] }} rounded-pill px-2.5 py-1 small fw-bold">
                        {{ $st['label'] }}
                    </span>
                </div>

                @if(!empty($subtitle))
                    <p class="text-white-50 small mb-1 mt-0.5">{{ $subtitle }}</p>
                @elseif($fair->subtitle)
                    <p class="text-white-50 small mb-1 mt-0.5">{{ $fair->subtitle }}</p>
                @endif

                <div class="d-flex align-items-center flex-wrap gap-3 text-white-50 small mt-1">
                    <span><i class="fas fa-calendar-alt me-1 text-warning"></i>{{ $fair->event_date->format('Y-m-d') }}</span>
                    @if($fair->location)
                    <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $fair->location }}</span>
                    @endif
                    @if($fair->start_time)
                    <span><i class="fas fa-clock me-1 text-info"></i>{{ substr($fair->start_time, 0, 5) }} @if($fair->end_time) - {{ substr($fair->end_time, 0, 5) }} @endif</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- أزرار الإجراءات الموحدة أو المخصصة لكل صفحة -->
        <div class="fair-actions-toolbar">
            @if(isset($actions))
                {!! $actions !!}
            @elseif($page === 'projects')
                {{-- أزرار صفحة مشاريع التخرج --}}
                <button type="button" class="fair-btn-action btn btn-warning text-dark shadow-sm fw-bold" onclick="typeof showModalSafe === 'function' ? showModalSafe('addProjectModal') : (new bootstrap.Modal(document.getElementById('addProjectModal'))).show();" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                    <i class="fas fa-plus-circle"></i>
                    <span>إضافة مشروع</span>
                </button>

                <a href="{{ route('job-fair.public.projects.submit-fair', $fair->id) }}" target="_blank" class="fair-btn-action fair-btn-glass" title="فتح نموذج تعبئة المشاريع المخصص للطلبة">
                    <i class="fas fa-file-upload text-warning"></i>
                    <span>نموذج التقديم</span>
                </a>

                <form action="{{ route('job-fair.admin.toggle-feature', $fair->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="feature" value="projects">
                    <button type="submit" class="fair-btn-action fair-btn-glass" title="التبديل بين إظهار المشاريع للجمهور أو إخفائها كـ Coming Soon">
                        <i class="fas {{ $fair->is_projects_published ? 'fa-eye text-success' : 'fa-clock text-warning' }}"></i>
                        <span>{{ $fair->is_projects_published ? 'منشور للجمهور' : 'قيد التحضير' }}</span>
                    </button>
                </form>

                <a href="{{ route('job-fair.public.projects', $fair->id) }}" target="_blank" class="fair-btn-action fair-btn-glass" title="معاينة صفحة المشاريع العامة">
                    <i class="fas fa-desktop text-info"></i>
                    <span>المعرض الرقمي</span>
                </a>

                <a href="{{ route('job-fair.admin.projects.export', $fair->id) }}" class="fair-btn-action fair-btn-glass" title="تصدير كود المشاريع إلى ملف CSV">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>تصدير CSV</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

            @elseif($page === 'visitors')
                {{-- أزرار صفحة الزوار --}}
                <button type="button" class="fair-btn-action btn btn-warning text-dark shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#adminVisitorRegisterModal">
                    <i class="fas fa-user-plus"></i>
                    <span>تسجيل زائر</span>
                </button>

                <button type="button" class="fair-btn-action btn btn-info text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;" data-bs-toggle="modal" data-bs-target="#registrationQrModal">
                    <i class="fas fa-qrcode"></i>
                    <span>رمز الـ QR</span>
                </button>

                <a href="{{ route('job-fair.admin.visitors.export', $fair->id) }}" class="fair-btn-action fair-btn-glass" title="تصدير بيانات الزوار">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>تصدير CSV</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-arrow-right"></i>
                    <span>تفاصيل المعرض</span>
                </a>

            @elseif($page === 'events')
                {{-- أزرار صفحة البرنامج العلمي والفعاليات --}}
                <button class="fair-btn-action btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addEventModal" data-toggle="modal" data-target="#addEventModal">
                    <i class="fas fa-plus-circle"></i>
                    <span>إضافة فعالية جديدة</span>
                </button>

                <form action="{{ route('job-fair.admin.toggle-feature', $fair->id) }}" method="POST" class="d-inline m-0">
                    @csrf
                    <input type="hidden" name="feature" value="program">
                    <button type="submit" class="fair-btn-action fair-btn-glass" title="التبديل بين إظهار الفعاليات للجمهور أو إخفائها كـ Coming Soon">
                        <i class="fas {{ $fair->is_program_published ? 'fa-eye text-success' : 'fa-clock text-warning' }}"></i>
                        <span>{{ $fair->is_program_published ? 'البرنامج منشور' : 'قيد التحضير' }}</span>
                    </button>
                </form>

                <a href="{{ route('job-fair.public.program', $fair->id) }}" target="_blank" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-external-link-alt text-info"></i>
                    <span>معاينة البرنامج للجمهور</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-arrow-right"></i>
                    <span>تفاصيل المعرض</span>
                </a>

            @elseif($page === 'attendance')
                {{-- أزرار صفحة ماسح الحضور --}}
                <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="fair-btn-action btn btn-info text-white shadow-sm" target="_blank" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;">
                    <i class="fas fa-chart-line"></i>
                    <span>المتابعة اللحظية</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-arrow-right"></i>
                    <span>تفاصيل المعرض</span>
                </a>

            @elseif($page === 'live')
                {{-- أزرار صفحة المتابعة اللحظية --}}
                <button onclick="window.location.reload();" class="fair-btn-action btn btn-warning text-dark fw-bold shadow-sm">
                    <i class="fas fa-sync-alt"></i>
                    <span>تحديث البيانات</span>
                </button>

                <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-qrcode text-warning"></i>
                    <span>ماسح QR</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

                <a href="{{ route('job-fair.admin.show', $fair->id) }}" class="fair-btn-action fair-btn-glass">
                    <i class="fas fa-arrow-right"></i>
                    <span>تفاصيل المعرض</span>
                </a>

            @else
                {{-- الأزرار الافتراضية (صفحة تفاصيل المعرض الرئيسية show) --}}
                <!-- زر ماسح QR السريع -->
                <a href="{{ route('job-fair.admin.attendance', $fair->id) }}" class="fair-btn-action btn btn-warning text-dark fw-bold shadow-sm" title="فتح ماسح الباركود لتسجيل حضور الزوار">
                    <i class="fas fa-qrcode"></i>
                    <span>ماسح QR</span>
                </a>

                <!-- المتابعة اللحظية للحضور -->
                <a href="{{ route('job-fair.admin.live', $fair->id) }}" class="fair-btn-action btn btn-info text-white shadow-sm" target="_blank" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none;" title="لوحة المتابعة اللحظية للحضور والإحصائيات">
                    <i class="fas fa-chart-line"></i>
                    <span>المتابعة اللحظية</span>
                </a>

                @include('job-fair.admin.partials.sections-dropdown', ['fair' => $fair])

                <!-- قائمة الخيارات والإعدادات الموحدة -->
                <div class="dropdown d-inline-block position-relative">
                    <button class="fair-btn-action fair-btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="تعديل وتصدير وضبط إعدادات المعرض">
                        <i class="fas fa-sliders-h"></i>
                        <span>خيارات وإعدادات</span>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3 p-2" style="min-width: 230px; z-index: 1075; right: 0; left: auto; top: 100%; margin-top: 6px;">
                        <li>
                            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('job-fair.admin.edit', $fair->id) }}">
                                <i class="fas fa-edit text-primary"></i>
                                <span>تعديل بيانات المعرض</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-2 py-2 px-3 small d-flex align-items-center gap-2" href="{{ route('job-fair.admin.export', $fair->id) }}">
                                <i class="fas fa-file-excel text-success"></i>
                                <span>تصدير بيانات (Excel)</span>
                            </a>
                        </li>
                        <li class="dropdown-divider my-1"></li>
                        <li class="dropdown-header small text-muted fw-bold pb-1 pt-1"><i class="fas fa-flag me-1"></i>تغيير حالة المعرض:</li>

                        @php
                            $statusIcons = [
                                'draft' => ['color' => 'text-secondary', 'icon' => 'fas fa-file-alt'],
                                'published' => ['color' => 'text-success', 'icon' => 'fas fa-check-circle'],
                                'ongoing' => ['color' => 'text-warning', 'icon' => 'fas fa-play-circle'],
                                'completed' => ['color' => 'text-primary', 'icon' => 'fas fa-flag-checkered']
                            ];
                        @endphp
                        @foreach(['draft'=>'مسودة','published'=>'منشور ومتاح','ongoing'=>'جارٍ الآن','completed'=>'منتهي'] as $val => $label)
                        <li>
                            <form action="{{ route('job-fair.admin.status', $fair->id) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="{{ $val }}">
                                <button type="submit" class="dropdown-item rounded-2 py-1.5 px-3 small d-flex align-items-center justify-content-between {{ $fair->status == $val ? 'active fw-bold' : '' }}">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="{{ $statusIcons[$val]['icon'] ?? 'fas fa-circle' }} {{ $fair->status == $val ? 'text-white' : ($statusIcons[$val]['color'] ?? '') }}"></i>
                                        <span>{{ $label }}</span>
                                    </span>
                                    @if($fair->status == $val)
                                        <i class="fas fa-check text-white ms-2"></i>
                                    @endif
                                </button>
                            </form>
                        </li>
                        @endforeach

                        <li class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('job-fair.admin.reset-attendance', $fair->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-2 py-1.5 px-3 small text-danger d-flex align-items-center gap-2" onclick="return confirm('تحذير: هل أنت متأكد من رغبتك في إعادة تهيئة سجلات الحضور بالكامل؟')" title="إعادة تعيين حضور المعرض">
                                    <i class="fas fa-undo"></i>
                                    <span>تصفير سجلات الحضور</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
