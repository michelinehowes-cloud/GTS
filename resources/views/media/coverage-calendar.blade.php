@extends('layouts.app')

@section('title', 'تقويم وجدول التغطيات الإعلامية للمحطات التدريبية — وحدة الإعلام')
@section('page-title', 'تقويم التغطيات الإعلامية الميدانية')

@push('styles')
<style>
    /* تصميم التقويم المعتمد وفق هوية المنظومة FullCalendar v6 */
    .fc {
        font-family: '29LT Bukra', 'Cairo', 'Tajawal', sans-serif !important;
    }
    .fc .fc-toolbar {
        flex-wrap: wrap !important;
        gap: 10px !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding-bottom: 0.85rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        margin-bottom: 1rem !important;
    }
    .fc .fc-toolbar-title {
        font-size: 1.35rem !important;
        font-weight: 800 !important;
        color: #0d3882 !important;
        letter-spacing: -0.3px;
    }
    .fc .fc-button {
        font-size: 0.84rem !important;
        font-weight: 700 !important;
        padding: 0.42rem 0.9rem !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(13, 56, 130, 0.12) !important;
        transition: all 0.2s ease !important;
    }
    .fc .fc-button-primary {
        background-color: #0d3882 !important;
        border-color: #0d3882 !important;
        color: #ffffff !important;
    }
    .fc .fc-button-primary:hover, 
    .fc .fc-button-primary:focus, 
    .fc .fc-button-primary.fc-button-active {
        background-color: #1e40af !important;
        border-color: #1e40af !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(13, 56, 130, 0.25) !important;
    }
    .fc .fc-button-group {
        border-radius: 8px !important;
        overflow: hidden;
    }
    .fc .fc-button-group > .fc-button {
        border-radius: 0 !important;
    }
    .fc .fc-button-group > .fc-button:first-child {
        border-top-right-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }
    .fc .fc-button-group > .fc-button:last-child {
        border-top-left-radius: 8px !important;
        border-bottom-left-radius: 8px !important;
    }

    /* عناوين الأيام */
    .fc .fc-col-header-cell {
        background: #f8fafc !important;
        padding: 10px 0 !important;
        border-color: #e2e8f0 !important;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.86rem !important;
        font-weight: 800 !important;
        color: #334155 !important;
        text-decoration: none !important;
    }

    /* خلايا الأيام */
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #edf2f7 !important;
    }
    .fc-daygrid-day-top {
        padding: 4px 6px !important;
    }
    .fc-daygrid-day-number {
        font-size: 0.88rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        padding: 2px 7px !important;
        text-decoration: none !important;
        border-radius: 6px;
    }
    .fc-day-other .fc-daygrid-day-number {
        color: #94a3b8 !important;
        opacity: 0.55;
    }
    .fc-day-today {
        background-color: #eff6ff !important;
    }
    .fc-day-today .fc-daygrid-day-number {
        background-color: #0d3882 !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 2px 6px rgba(13, 56, 130, 0.3);
    }

    /* كبسولات الفعاليات والتدريبات الحديثة */
    .fc .fc-daygrid-event {
        border-radius: 7px !important;
        padding: 3px 8px !important;
        margin: 2px 3px !important;
        font-size: 0.76rem !important;
        font-weight: 700 !important;
        border: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        cursor: pointer;
    }
    .fc .fc-daygrid-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18) !important;
        filter: brightness(1.06);
    }
    .fc-h-event {
        border: none !important;
    }
    .fc-event-title {
        font-weight: 700 !important;
        font-size: 0.77rem !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
    }

    /* نافذة المزيد للمناسبات المتعددة */
    .fc .fc-more-popover {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
        z-index: 1050;
    }
    .fc .fc-more-popover .fc-popover-header {
        background: #f8fafc !important;
        font-weight: 700 !important;
        padding: 8px 12px !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    /* طريقة عرض القائمة */
    .fc .fc-list-empty {
        background: #f8fafc !important;
        padding: 2rem !important;
        color: #64748b !important;
        font-weight: 600 !important;
    }
    .fc .fc-list-day-cushion {
        background: #f1f5f9 !important;
        color: #0d3882 !important;
        font-weight: 800 !important;
        padding: 8px 16px !important;
    }
    .fc .fc-list-event:hover td {
        background-color: #f8fafc !important;
    }

    @media (max-width: 576px) {
        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            width: 100%;
            text-align: center;
            order: -1;
            margin-bottom: 0.25rem !important;
        }
        .fc-col-header-cell-cushion {
            font-size: 0.72rem !important;
            padding: 3px 0 !important;
        }
        .fc-daygrid-day-number {
            font-size: 0.75rem !important;
            padding: 2px 4px !important;
        }
        .fc-daygrid-event {
            font-size: 0.70rem !important;
            padding: 2px 4px !important;
        }
    }

    .location-pill {
        background: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 3px 8px;
        font-weight: 600;
        font-size: 0.76rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3" dir="rtl">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Hero Header المعتمد للمركز الإعلامي -->
    <x-page-hero
        title="تقويم وجدول التغطيات الإعلامية للمحطات التدريبية"
        subtitle="متابعة مواعيد البرامج التدريبية، جدولة التغطيات الصحفية الميدانية، وتنسيق التوثيق السحابي لكافة الفعاليات"
        icon="fas fa-calendar-alt"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم الميديا', 'url' => route('media.dashboard')],
            ['label' => 'تقويم وجدول التغطيات']
        ]"
        badge="التقويم المعتمد"
    >
        <a href="{{ route('media.news.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>+ إضافة خبر جديد</span>
        </a>
        <a href="{{ route('media.reports.coverage') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-file-invoice"></i>
            <span>تقارير التغطية الصحفية</span>
        </a>
    </x-page-hero>

    <!-- صف بطاقات Bento الإحصائية المطابقة لهوية النظام -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي البرامج',
            'value' => $stats['total'],
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary',
            'description' => 'جميع التدريبات المجدولة'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'تمت التغطية والتوثيق',
            'value' => $stats['covered'],
            'icon' => 'fas fa-check-double',
            'color' => 'success',
            'description' => 'بيانات وتقارير معتمدة'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'بانتظار التغطية',
            'value' => $stats['pending'],
            'icon' => 'fas fa-clock',
            'color' => 'warning',
            'description' => 'تحت المتابعة أو جارية'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'القاعات والمواقع',
            'value' => $stats['locations'],
            'icon' => 'fas fa-map-marker-alt',
            'color' => 'info',
            'description' => 'أماكن الفعاليات التدريبية'
        ])
    </div>

    <!-- بطاقة التقويم الرئيسية المطابقة تماماً لتقويم منسق التدريب (FullCalendar v6) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="background: #ffffff;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                    <i class="fas fa-calendar-alt me-2"></i>تقويم الفعاليات والمهام الإعلامية
                </h5>
                <!-- دليل ألوان التصنيفات (Legend Bar) -->
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge rounded-pill" style="background: rgba(13, 56, 130, 0.08); color: #0d3882; font-size: 0.75rem; font-weight: 700; padding: 5px 10px;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #0d3882;"></span> دورات
                    </span>
                    <span class="badge rounded-pill" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-size: 0.75rem; font-weight: 700; padding: 5px 10px;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #059669;"></span> ورش
                    </span>
                    <span class="badge rounded-pill" style="background: rgba(29, 78, 216, 0.08); color: #1d4ed8; font-size: 0.75rem; font-weight: 700; padding: 5px 10px;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #1d4ed8;"></span> عملي
                    </span>
                    <span class="badge rounded-pill" style="background: rgba(217, 119, 6, 0.08); color: #d97706; font-size: 0.75rem; font-weight: 700; padding: 5px 10px;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #d97706;"></span> ندوات
                    </span>
                </div>
            </div>

            <!-- أشرطة الفلترة السريعة حسب المكان وحالة التغطية -->
            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <span class="small fw-bold text-muted d-none d-sm-inline"><i class="fas fa-filter text-primary me-1"></i>تصفية:</span>
                <!-- فلتر حالة التغطية -->
                <select id="statusFilterSelect" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 160px;" onchange="applyFilters()">
                    <option value="all">جميع حالات التغطية</option>
                    <option value="covered">✅ تمت التغطية والتوثيق</option>
                    <option value="pending">⏳ بانتظار التغطية</option>
                    <option value="not_required">⚪ غير مطلوبة</option>
                </select>

                <!-- فلتر الموقع -->
                <select id="locationFilterSelect" class="form-select form-select-sm rounded-pill" style="width: auto; min-width: 170px;" onchange="applyFilters()">
                    <option value="all">جميع القاعات والأماكن</option>
                    @foreach($trainings->pluck('location')->filter()->unique() as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- جدول البرامج التدريبية ومهام التغطية التفصيلي أسفل التقويم -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: #ffffff;">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark fs-6">جدول مهام التغطية الميدانية والتوثيق الصحفي</h6>
                    <small class="text-muted">متابعة مواعيد الفعاليات ومواقع الانعقاد وحالة البيان الصحفي</small>
                </div>
            </div>
            <span class="badge bg-light text-primary border rounded-pill px-3 py-1.5 fw-bold">
                {{ $trainings->count() }} برامج مسجلة
            </span>
        </div>

        <div class="card-body p-0">
            @if($trainings->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted opacity-50 mb-3"></i>
                    <h6 class="fw-bold text-dark mb-1">لا توجد برامج تدريبية مسجلة</h6>
                    <p class="text-muted small mb-0">لم يتم جدولة أي دورات أو برامج تدريبية حالياً.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light" style="font-size: 0.82rem;">
                            <tr class="text-muted text-uppercase">
                                <th class="ps-4 py-3" style="width: 40px;">#</th>
                                <th class="py-3" style="min-width: 220px;">البرنامج التدريبي</th>
                                <th class="py-3">النوع</th>
                                <th class="py-3">مكان التدريب / القاعة</th>
                                <th class="py-3">الفترة الزمنية</th>
                                <th class="py-3">الجهة / المدرب</th>
                                <th class="text-center py-3">حالة التغطية</th>
                                <th class="text-center pe-4 py-3" style="min-width: 180px;">الإجراءات والتقرير الصحفي</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.88rem;">
                            @foreach($trainings as $idx => $t)
                            <tr>
                                <td class="ps-4 font-monospace text-muted small">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; 
                                            @if($t->type === 'workshop') background: rgba(5,150,105,0.1); color:#059669;
                                            @elseif($t->type === 'seminar') background: rgba(217,119,6,0.1); color:#d97706;
                                            @elseif($t->type === 'internship') background: rgba(29,78,216,0.1); color:#1d4ed8;
                                            @else background: rgba(13,56,130,0.1); color:#0d3882;
                                            @endif">
                                            <i class="{{ $t->type === 'workshop' ? 'fas fa-tools' : ($t->type === 'seminar' ? 'fas fa-bullhorn' : ($t->type === 'internship' ? 'fas fa-laptop-code' : 'fas fa-graduation-cap')) }}" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="fw-bold text-dark">{{ $t->title }}</span>
                                                @if($t->media_press_release || $t->media_coverage_summary)
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 py-0.5 px-1.5" style="font-size: 0.65rem;" title="تمت كتابة التقرير الصحفي">
                                                        <i class="fas fa-feather-alt me-0.5"></i>محرر
                                                    </span>
                                                @endif
                                            </div>
                                            @if($t->company)
                                                <div class="text-muted small" style="font-size: 0.75rem;">
                                                    <i class="fas fa-building me-1"></i>{{ $t->company->name }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeMap = [
                                            'course' => ['label' => 'دورة تدريبية', 'bg' => '#eff6ff', 'color' => '#1d4ed8'],
                                            'workshop' => ['label' => 'ورشة عمل', 'bg' => '#ecfdf5', 'color' => '#059669'],
                                            'seminar' => ['label' => 'ندوة علمية', 'bg' => '#fef3c7', 'color' => '#d97706'],
                                            'internship' => ['label' => 'تدريب عملي', 'bg' => '#f5f3ff', 'color' => '#7c3aed'],
                                        ];
                                        $tc = $typeMap[$t->type] ?? ['label' => $t->type, 'bg' => '#f1f5f9', 'color' => '#475569'];
                                    @endphp
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: {{ $tc['bg'] }}; color: {{ $tc['color'] }}; font-size: 0.72rem;">
                                        {{ $tc['label'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($t->location)
                                        <span class="location-pill">
                                            <i class="fas fa-map-marker-alt text-danger"></i>
                                            <span>{{ $t->location }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted small">جامعة طرابلس</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-dark font-monospace fw-semibold">
                                            <i class="fas fa-calendar-alt text-primary me-1"></i>{{ $t->start_date ? $t->start_date->format('Y-m-d') : '--' }}
                                        </span>
                                        <span class="text-muted font-monospace" style="font-size: 0.74rem;">
                                            إلى: {{ $t->end_date ? $t->end_date->format('Y-m-d') : '--' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-user-tie text-secondary me-1"></i>
                                        {{ $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($t->media_coverage_status === 'covered')
                                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 0.74rem;">
                                            <i class="fas fa-check-circle me-1"></i>تمت التغطية
                                        </span>
                                    @elseif($t->media_coverage_status === 'pending' || is_null($t->media_coverage_status))
                                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; font-size: 0.74rem;">
                                            <i class="fas fa-hourglass-half me-1"></i>بانتظار التغطية
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.74rem;">
                                            <i class="fas fa-minus-circle me-1"></i>غير مطلوبة
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="{{ route('media.reports.coverage.edit', $t->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-2 px-2.5 py-1 shadow-sm" style="font-size: 0.76rem;" title="كتابة التقرير والبيان الصحفي">
                                            <i class="fas fa-feather-alt me-1"></i>كتابة التقرير
                                        </a>
                                        <a href="{{ route('media.reports.coverage.show', $t->id) }}" class="btn btn-sm btn-light border text-primary rounded-2 px-2 py-1" style="font-size: 0.76rem;" title="عرض التقرير الرسمي">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1" style="font-size: 0.76rem;" onclick="openCoverageStatusModal({{ $t->id }}, '{{ $t->media_coverage_status ?? 'pending' }}', '{{ addslashes($t->title) }}')" title="تحديث حالة التغطية">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal تفاصيل التغطية والبرنامج التفاعلي عند النقر على أي فعالية في التقويم -->
<div class="modal fade" id="calEventModal" tabindex="-1" aria-hidden="true" onclick="if(event.target === this) closeCalendarModal()">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: #0d3882;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check fs-5 text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0" id="mEventTitle">تفاصيل البرنامج التدريبي والتغطية الإعلامية</h6>
                </div>
                <button type="button" class="btn-close btn-close-white m-0" onclick="closeCalendarModal()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                    <h5 id="mEventHeading" class="fw-bold text-dark mb-0 fs-6"></h5>
                    <span id="mEventCoverageBadge" class="badge rounded-pill px-3 py-1.5 fw-bold flex-shrink-0" style="font-size: 0.75rem;"></span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 border d-flex flex-column gap-2.5 small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt text-danger fs-6" style="width: 22px;"></i>
                        <span class="text-muted">مكان التدريب / القاعة:</span>
                        <strong id="mEventLocation" class="text-primary fw-bold fs-6"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary" style="width: 22px;"></i>
                        <span class="text-muted">الفترة الزمنية:</span>
                        <strong id="mEventDates" class="text-dark font-monospace"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-hourglass-half text-warning" style="width: 22px;"></i>
                        <span class="text-muted">نوع ومدة التدريب:</span>
                        <strong id="mEventDuration" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-info" style="width: 22px;"></i>
                        <span class="text-muted">المدرب المشرف:</span>
                        <strong id="mEventInstructor" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-feather-alt text-success" style="width: 22px;"></i>
                        <span class="text-muted">حالة البيان الصحفي:</span>
                        <strong id="mEventPressStatus" class="text-dark"></strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2 flex-wrap gap-2">
                    <button type="button" class="btn btn-light border rounded-3 px-3 fw-semibold" onclick="closeCalendarModal()" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إغلاق
                    </button>
                    <div class="d-flex gap-2 flex-wrap">
                        <a id="mEventTrainingUrl" href="#" class="btn btn-outline-primary rounded-3 px-2.5 d-flex align-items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            <span>لوحة التدريب</span>
                        </a>
                        <a id="mEventEditUrl" href="#" class="btn btn-warning text-dark fw-bold rounded-3 px-3 d-flex align-items-center gap-1 shadow-sm">
                            <i class="fas fa-feather-alt"></i>
                            <span>كتابة التقرير</span>
                        </a>
                        <a id="mEventShowUrl" href="#" class="btn btn-primary rounded-3 px-3 fw-bold d-flex align-items-center gap-1" style="background: #0d3882;">
                            <i class="fas fa-file-invoice"></i>
                            <span>معاينة التقرير</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تحديث حالة التغطية السريع -->
<div class="modal fade" id="coverageStatusModal" tabindex="-1" dir="rtl">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">تحديث حالة التغطية الإعلامية</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal"></button>
            </div>
            <form id="coverageStatusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3" id="coverageModalTrainingTitle"></p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">حالة التغطية الجديدة</label>
                        <select name="media_coverage_status" class="form-select rounded-3 p-2.5" id="coverageStatusSelect" required>
                            <option value="pending">⏳ بانتظار التغطية / قيد المتابعة</option>
                            <option value="covered">✅ تمت التغطية والتوثيق</option>
                            <option value="not_required">⚪ لا يتطلب تغطية</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">حفظ التغيير</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar = null;
const allCalendarEvents = @json($calendarTrainings ?? []);

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const isSmallScreen = window.innerWidth < 576;
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ar',
            direction: 'rtl',
            height: 'auto',
            dayMaxEvents: 2, // يحول التراكم إلى كبسولة أنيقة (+X المزيد)
            dayHeaderFormat: isSmallScreen ? { weekday: 'narrow' } : { weekday: 'short' },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                list: 'قائمة'
            },
            events: allCalendarEvents,
            eventContent: function(arg) {
                let type = (arg.event.extendedProps && arg.event.extendedProps.type) ? arg.event.extendedProps.type : 'course';
                let iconClass = 'fa-graduation-cap';
                if (type === 'workshop') iconClass = 'fa-tools';
                else if (type === 'internship') iconClass = 'fa-laptop-code';
                else if (type === 'seminar') iconClass = 'fa-bullhorn';

                let coverageBadge = '';
                if (arg.event.extendedProps && arg.event.extendedProps.coverageStatus === 'covered') {
                    coverageBadge = '<i class="fas fa-check-circle ms-1 text-warning" title="تمت التغطية"></i>';
                }

                let customHtml = `
                    <div class="d-flex align-items-center gap-1.5 overflow-hidden w-100 text-white" style="line-height: 1.25;">
                        <i class="fas ${iconClass} me-1" style="font-size: 0.68rem; opacity: 0.9; flex-shrink: 0;"></i>
                        <span class="text-truncate fw-bold" style="font-size: 0.74rem;">${arg.event.title}</span>
                        ${coverageBadge}
                    </div>
                `;
                return { html: customHtml };
            },
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                const props = info.event.extendedProps || {};
                openCalendarModal(
                    props.rawTitle || info.event.title,
                    props.location || 'جامعة طرابلس',
                    props.startDate || '',
                    props.endDate || '',
                    (props.typeArabic || '') + (props.duration ? ' (' + props.duration + ')' : ''),
                    props.instructor || 'غير محدد',
                    props.coverageStatus || 'pending',
                    props.coverageStatusText || 'بانتظار التغطية',
                    props.hasPressRelease || false,
                    props.reportShowUrl || info.event.url || '#',
                    props.reportEditUrl || '#',
                    props.trainingShowUrl || '#'
                );
            }
        });
        calendar.render();
    }
});

function applyFilters() {
    if (!calendar) return;
    const loc = document.getElementById('locationFilterSelect').value;
    const status = document.getElementById('statusFilterSelect').value;

    calendar.removeAllEvents();
    let filtered = allCalendarEvents;

    if (loc !== 'all') {
        filtered = filtered.filter(function(e) {
            return e.extendedProps && e.extendedProps.location === loc;
        });
    }

    if (status !== 'all') {
        filtered = filtered.filter(function(e) {
            return e.extendedProps && e.extendedProps.coverageStatus === status;
        });
    }

    calendar.addEventSource(filtered);
}

function openCalendarModal(title, location, start, end, duration, instructor, coverageStatus, coverageStatusText, hasPressRelease, showUrl, editUrl, trainingUrl) {
    document.getElementById('mEventHeading').innerText = title;
    document.getElementById('mEventLocation').innerText = location || 'جامعة طرابلس';
    document.getElementById('mEventDates').innerText = (start || '') + (end ? ' إلى ' + end : '');
    document.getElementById('mEventDuration').innerText = duration || 'غير محدد';
    document.getElementById('mEventInstructor').innerText = instructor || 'غير محدد';
    
    // شارة حالة التغطية
    const badgeEl = document.getElementById('mEventCoverageBadge');
    badgeEl.innerText = coverageStatusText || 'بانتظار التغطية الإعلامية';
    if (coverageStatus === 'covered') {
        badgeEl.className = 'badge rounded-pill px-3 py-1.5 fw-bold';
        badgeEl.style.background = '#dcfce7';
        badgeEl.style.color = '#15803d';
        badgeEl.style.border = '1px solid #86efac';
    } else if (coverageStatus === 'pending') {
        badgeEl.className = 'badge rounded-pill px-3 py-1.5 fw-bold';
        badgeEl.style.background = '#fef3c7';
        badgeEl.style.color = '#b45309';
        badgeEl.style.border = '1px solid #fcd34d';
    } else {
        badgeEl.className = 'badge rounded-pill px-3 py-1.5 fw-bold';
        badgeEl.style.background = '#f1f5f9';
        badgeEl.style.color = '#475569';
        badgeEl.style.border = '1px solid #cbd5e1';
    }

    // حالة البيان الصحفي
    document.getElementById('mEventPressStatus').innerHTML = hasPressRelease
        ? '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>تم تحرير البيان الصحفي</span>'
        : '<span class="text-muted"><i class="fas fa-hourglass-half me-1"></i>قيد الإعداد والتحرير</span>';
    
    document.getElementById('mEventShowUrl').href = showUrl || '#';
    document.getElementById('mEventEditUrl').href = editUrl || '#';
    const trainingBtn = document.getElementById('mEventTrainingUrl');
    if (trainingBtn) {
        trainingBtn.href = trainingUrl || '#';
    }

    const modalEl = document.getElementById('calEventModal');
    if (!modalEl) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        let m = bootstrap.Modal.getInstance(modalEl);
        if (!m) m = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
        m.show();
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
    }
}

function closeCalendarModal() {
    const modalEl = document.getElementById('calEventModal');
    if (!modalEl) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const m = bootstrap.Modal.getInstance(modalEl);
        if (m) {
            try { m.hide(); } catch(e) {}
        }
    }

    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-modal', 'false');
    modalEl.removeAttribute('role');
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');

    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
}

function openCoverageStatusModal(trainingId, currentStatus, title) {
    document.getElementById('coverageStatusSelect').value = currentStatus;
    document.getElementById('coverageModalTrainingTitle').innerText = 'البرنامج: ' + title;
    document.getElementById('coverageStatusForm').action = `/media/trainings/${trainingId}/coverage-status`;

    new bootstrap.Modal(document.getElementById('coverageStatusModal')).show();
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCalendarModal();
    }
});
</script>
@endpush
