@extends('layouts.app')

@section('title', 'تقويم التدريبات والفعاليات')

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
<div class="container-fluid">

    <!-- الشريط الأزرق الموحد المعتمد في المنظومة -->
    <x-page-hero
        title="تقويم البرامج التدريبية ومواقع الانعقاد"
        subtitle="متابعة توزيع المواعيد والجدول الشهري لكافة الدورات وورش العمل مع تحديد قاعات وأماكن التدريب"
        icon="fas fa-calendar-alt"
        :breadcrumbs="[
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم منسق التدريب', 'url' => route('training-coordinator.dashboard')],
            ['label' => 'تقويم التدريبات']
        ]"
        badge="التقويم المعتمد"
    >
        <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة برنامج تدريب</span>
        </a>
        <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-list"></i>
            <span>قائمة التدريبات</span>
        </a>
    </x-page-hero>

    <!-- بطاقات الإحصائيات -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'التدريبات النشطة',
            'value' => $stats['active'] ?? $trainings->where('status', 'active')->count(),
            'icon' => 'fas fa-play-circle',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي البرامج',
            'value' => $stats['total'] ?? $trainings->count(),
            'icon' => 'fas fa-calendar-check',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي المقاعد',
            'value' => $stats['seats'] ?? $trainings->sum('seats'),
            'icon' => 'fas fa-users',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'القاعات والمواقع',
            'value' => $stats['locations'] ?? $trainings->pluck('location')->filter()->unique()->count(),
            'icon' => 'fas fa-map-marker-alt',
            'color' => 'info'
        ])
    </div>

    <!-- بطاقة التقويم الرئيسية المطابقة تماماً لتقويم المنظومة الحديث (FullCalendar) -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h5 class="card-title mb-0 text-primary fw-bold fs-6 fs-md-5">
                    <i class="fas fa-calendar-alt me-2"></i>تقويم التدريبات والفعاليات
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

            <!-- شريط الفلترة السريعة حسب المكان -->
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted d-none d-sm-inline"><i class="fas fa-map-marker-alt text-danger me-1"></i>الموقع:</span>
                <select id="locationFilterSelect" class="form-select form-select-sm rounded-pill" style="min-width: 180px;" onchange="filterCalendarByLocation(this.value)">
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

    <!-- جدول البرامج التدريبية ومواقع الانعقاد التفصيلي أسفل التقويم -->
    <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark fs-6">جدول البرامج التدريبية ومواقع الانعقاد</h6>
                    <small class="text-muted">استعراض تفصيلي لأماكن التدريب، المدربين، والفترات الزمنية</small>
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
                    <p class="text-muted small mb-3">لم يتم جدولة أي دورات أو برامج تدريبية حتى الآن.</p>
                    <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-sm btn-primary-modern rounded-pill px-3">
                        <i class="fas fa-plus-circle me-1"></i>إضافة برنامج تدريبي جديد
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 40px;">#</th>
                                <th>اسم البرنامج التدريبي</th>
                                <th>نوع التدريب</th>
                                <th>مكان التدريب / القاعة</th>
                                <th>الفترة الزمنية</th>
                                <th>المدرب</th>
                                <th class="text-center">المقاعد</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center pe-4" style="width: 160px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                            <a href="{{ route('training-coordinator.trainings.show', $t->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                {{ $t->title }}
                                            </a>
                                            @if($t->company)
                                                <div class="text-muted" style="font-size: 0.75rem;">
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
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-dark font-monospace fw-semibold">
                                            <i class="fas fa-calendar-alt text-primary me-1"></i>{{ $t->start_date ? $t->start_date->format('Y-m-d') : '--' }}
                                        </span>
                                        <span class="text-muted font-monospace" style="font-size: 0.75rem;">
                                            إلى: {{ $t->end_date ? $t->end_date->format('Y-m-d') : '--' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-chalkboard-teacher text-info me-1"></i>
                                        {{ $t->instructor_name ?? ($t->trainer->name ?? 'غير محدد') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-monospace">
                                        {{ $t->seats ?? 'مفتوح' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;
                                        @if($t->status === 'active') background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;
                                        @elseif($t->status === 'completed') background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;
                                        @else background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;
                                        @endif">
                                        <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>{{ $t->status === 'active' ? 'نشط' : ($t->status === 'completed' ? 'مكتمل' : 'مسودة') }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('training-coordinator.trainings.show', $t->id) }}" class="btn btn-primary-modern py-1 px-2.5 rounded-2" title="تفاصيل البرنامج">
                                            <i class="fas fa-eye me-1"></i>عرض
                                        </a>
                                        <a href="{{ route('training-coordinator.trainings.attendance', $t->id) }}" class="btn btn-light border py-1 px-2 rounded-2 ms-1 text-dark" title="سجل الحضور">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                        @if($t->status === 'active')
                                        <a href="{{ route('training-coordinator.trainings.scanner', $t->id) }}" class="btn btn-light border py-1 px-2 rounded-2 ms-1 text-dark" title="ماسح الباركود">
                                            <i class="fas fa-qrcode"></i>
                                        </a>
                                        @endif
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

<!-- Modal تفاصيل التدريب التفاعلي عند النقر على أي فعالية في التقويم -->
<div class="modal fade" id="calEventModal" tabindex="-1" aria-hidden="true" onclick="if(event.target === this) closeCalendarModal()">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header text-white py-3 px-4" style="background: #0d3882;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check fs-5 text-warning"></i>
                    <h6 class="modal-title fw-bold mb-0" id="mEventTitle">تفاصيل البرنامج التدريبي</h6>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="closeCalendarModal()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h5 id="mEventHeading" class="fw-bold text-dark mb-3"></h5>

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
                        <span class="text-muted">المدة المقدرة:</span>
                        <strong id="mEventDuration" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-info" style="width: 22px;"></i>
                        <span class="text-muted">المدرب المشرف:</span>
                        <strong id="mEventInstructor" class="text-dark"></strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-users text-success" style="width: 22px;"></i>
                        <span class="text-muted">المقاعد المتاحة:</span>
                        <strong id="mEventSeats" class="text-dark"></strong>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <button type="button" class="btn btn-light border rounded-3 px-4 fw-semibold" onclick="closeCalendarModal()" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> إغلاق
                    </button>
                    <div class="d-flex gap-2">
                        <a id="mEventAttendance" href="#" class="btn btn-outline-primary rounded-3 px-3 fw-bold d-flex align-items-center gap-1">
                            <i class="fas fa-user-check"></i>
                            <span>سجل الحضور</span>
                        </a>
                        <a id="mEventUrl" href="#" class="btn btn-primary-modern rounded-3 px-3 fw-bold d-flex align-items-center gap-1">
                            <i class="fas fa-eye"></i>
                            <span>لوحة التدريب</span>
                        </a>
                    </div>
                </div>
            </div>
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

                let customHtml = `
                    <div class="d-flex align-items-center gap-1.5 overflow-hidden w-100 text-white" style="line-height: 1.25;">
                        <i class="fas ${iconClass} me-1" style="font-size: 0.68rem; opacity: 0.9; flex-shrink: 0;"></i>
                        <span class="text-truncate fw-bold" style="font-size: 0.74rem;">${arg.event.title}</span>
                    </div>
                `;
                return { html: customHtml };
            },
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                const props = info.event.extendedProps || {};
                openCalendarModal(
                    props.rawTitle || info.event.title,
                    props.location || 'غير محدد',
                    props.startDate || '',
                    props.endDate || '',
                    props.duration || 'غير محدد',
                    props.instructor || 'غير محدد',
                    props.seats || '0',
                    props.status || 'active',
                    props.showUrl || info.event.url || '#',
                    props.attendanceUrl || '#'
                );
            }
        });
        calendar.render();
    }
});

function filterCalendarByLocation(loc) {
    if (!calendar) return;
    calendar.removeAllEvents();
    if (loc === 'all') {
        calendar.addEventSource(allCalendarEvents);
    } else {
        const filtered = allCalendarEvents.filter(function(e) {
            return e.extendedProps && e.extendedProps.location === loc;
        });
        calendar.addEventSource(filtered);
    }
}

function openCalendarModal(title, location, start, end, duration, instructor, seats, status, url, attendanceUrl) {
    document.getElementById('mEventHeading').innerText = title;
    document.getElementById('mEventLocation').innerText = location || 'غير محدد';
    document.getElementById('mEventDates').innerText = (start || '') + (end ? ' إلى ' + end : '');
    document.getElementById('mEventDuration').innerText = duration || 'غير محدد';
    document.getElementById('mEventInstructor').innerText = instructor || 'غير محدد';
    document.getElementById('mEventSeats').innerText = (seats || '0') + ' مقعد';
    
    document.getElementById('mEventUrl').href = url || '#';
    const attendanceBtn = document.getElementById('mEventAttendance');
    if (attendanceBtn) {
        if (attendanceUrl && attendanceUrl !== '#') {
            attendanceBtn.href = attendanceUrl;
            attendanceBtn.style.display = 'inline-flex';
        } else {
            attendanceBtn.style.display = 'none';
        }
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

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeCalendarModal();
    }
});
</script>
@endpush