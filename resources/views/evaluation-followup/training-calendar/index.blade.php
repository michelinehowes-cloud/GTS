@extends('layouts.app')

@section('title', 'التقويم - ' . $monthName . ' ' . $year)

@push('styles')
<style>
    /* تصميم التقويم المعتمد وفق هوية المنصة */
    .calendar-main-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(13, 56, 130, 0.04);
        overflow: hidden;
    }

    /* رأس التقويم المطابق للصورة */
    .calendar-header-bar {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .calendar-title-heading {
        color: #0d3882;
        font-weight: 800;
        font-size: 1.45rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* حقول الاختيار مع التسمية العائمة (Floating Outline Selects) كما في الصورة */
    .cal-floating-select-wrap {
        position: relative;
        min-width: 170px;
    }
    .cal-floating-label {
        position: absolute;
        top: -9px;
        right: 14px;
        background: #ffffff;
        padding: 0 6px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #0d3882;
        z-index: 2;
        border-radius: 4px;
    }
    .cal-select-box {
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 0.55rem 1rem;
        font-weight: 700;
        font-size: 0.92rem;
        color: #1e293b;
        background-color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .cal-select-box:focus {
        border-color: #0d3882;
        box-shadow: 0 0 0 3px rgba(13, 56, 130, 0.12);
    }

    /* أزرار الأسهم الدائرية/المربعة بهوية المنصة الزرقاء الملكية */
    .btn-cal-arrow {
        width: 38px;
        height: 38px;
        background: #0d3882;
        color: #ffffff !important;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        text-decoration: none !important;
        box-shadow: 0 2px 6px rgba(13, 56, 130, 0.25);
        transition: all 0.2s ease;
    }
    .btn-cal-arrow:hover {
        background: #1e40af;
        transform: scale(1.05);
        color: #ffffff !important;
    }

    /* شريط أسماء الأيام (الأحد .. السبت) */
    .cal-weekdays-row {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .cal-weekday-cell {
        text-align: center;
        padding: 12px 4px;
        font-weight: 700;
        font-size: 0.92rem;
        color: #475569;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* شبكة خلايا الـ 42 يوماً */
    .cal-days-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        background: #e2e8f0; /* الفواصل الدقيقة */
        gap: 1px;
    }
    .cal-day-box {
        background: #ffffff;
        min-height: 105px;
        padding: 6px 7px;
        position: relative;
        display: flex;
        flex-direction: column;
        transition: background 0.15s ease;
        min-width: 0;
        overflow: hidden;
    }
    .cal-day-box:hover {
        background: #fbfcfe;
    }
    .cal-day-box.other-month {
        background: #fcfcfd;
    }
    .cal-day-box.highlight-day {
        border: 2px solid #0d3882 !important;
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 1px #bfdbfe;
    }
    .cal-day-num {
        font-size: 0.90rem;
        font-weight: 700;
        color: #1e293b;
        text-align: left;
        margin-bottom: 4px;
        line-height: 1;
    }
    .cal-day-box.other-month .cal-day-num {
        color: #cbd5e1;
        font-weight: 500;
    }
    .cal-day-box.highlight-day .cal-day-num {
        color: #0d3882;
        font-weight: 800;
    }

    /* أشرطة وكبسولات الفعاليات والتدريب المضغوطة والأنيقة */
    .cal-events-wrap {
        min-width: 0;
        width: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .cal-prog-bar {
        border-radius: 5px;
        padding: 3px 6px;
        font-size: 0.70rem;
        font-weight: 600;
        line-height: 1.25;
        color: #ffffff;
        cursor: pointer;
        display: block;
        text-decoration: none !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }
    .cal-prog-bar:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
        color: #ffffff;
    }
    .cal-prog-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        min-width: 0;
        max-width: 100%;
        font-size: 0.71rem;
        font-weight: 700;
    }
    .cal-prog-loc {
        font-size: 0.63rem;
        color: #fef08a; /* أصفر ذهبي ناعم بارز */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        margin-top: 1px;
        font-weight: 500;
        min-width: 0;
        max-width: 100%;
        opacity: 0.95;
    }

    /* المسار الزمني الخفيف للأيام المستمرة (Timeline Stripe) */
    .cal-timeline-stripe {
        height: 6px;
        background: #0d3882;
        border-radius: 3px;
        margin: 2px 0;
        cursor: pointer;
        opacity: 0.75;
        transition: all 0.15s ease;
        position: relative;
        min-width: 0;
        width: 100%;
        display: block;
    }
    .cal-timeline-stripe:hover {
        opacity: 1;
        height: 9px;
        box-shadow: 0 1px 3px rgba(13, 56, 130, 0.3);
    }
    .stripe-royal { background: #0d3882; }
    .stripe-emerald { background: #059669; }
    .stripe-amber { background: #d97706; }

    .bar-royal { background: #0d3882; }
    .bar-emerald { background: #059669; }
    .bar-amber { background: #d97706; }
    .bar-indigo { background: #4338ca; }

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
            ['label' => 'لوحة التقييم والمتابعة', 'url' => route('evaluation-followup.dashboard')],
            ['label' => 'تقويم التدريبات']
        ]"
        badge="التقويم المعتمد"
    >
        <a href="{{ route('evaluation-followup.training-programs.create') }}" class="btn btn-warning text-dark fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة برنامج تدريب</span>
        </a>
        <a href="{{ route('evaluation-followup.training-programs.index') }}" class="btn btn-light bg-white text-primary fw-bold py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-1.5">
            <i class="fas fa-list"></i>
            <span>دليل البرامج التدريبية</span>
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

    <!-- شريط الفلترة السريعة -->
    <div class="card-modern shadow-sm border-0 rounded-4 p-3 mb-3 d-flex flex-row justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i>تصفية حسب مكان التدريب:</span>
            <select id="locationFilterSelect" class="form-select form-select-sm rounded-pill" style="min-width: 200px;" onchange="filterCalendarByLocation(this.value)">
                <option value="all">جميع القاعات والأماكن</option>
                @foreach($trainings->pluck('location')->filter()->unique() as $loc)
                    <option value="{{ $loc }}">{{ $loc }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-dark"><i class="fas fa-sliders-h text-primary me-1"></i>نمط العرض:</span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-primary active" id="btnModeMilestones" onclick="toggleCalendarMode('milestones')">
                    <i class="fas fa-flag-checkered me-1"></i> جدول المواعيد المعتمد (انطلاق وختام)
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnModeAll" onclick="toggleCalendarMode('all')">
                    <i class="fas fa-grip-lines me-1"></i> إظهار المسار الزمني المستمر
                </button>
            </div>
        </div>
    </div>

    <!-- بطاقة التقويم الرئيسية -->
    <div class="calendar-main-card mb-4">
        
        <!-- رأس التقويم -->
        <div class="calendar-header-bar">
            <div class="calendar-title-heading">
                <i class="fas fa-calendar-alt text-primary"></i>
                <span>التقويم</span>
            </div>

            <!-- الوسط: قوائم اختيار الشهر والسنة مع Floating Labels -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- اختيار الشهر -->
                <div class="cal-floating-select-wrap">
                    <span class="cal-floating-label">الشهر</span>
                    <select id="navMonth" class="form-select cal-select-box" onchange="submitDateJump()">
                        @foreach($arabicMonths as $mNum => $mName)
                            <option value="{{ $mNum }}" {{ (int)$month === $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- اختيار السنة -->
                <div class="cal-floating-select-wrap">
                    <span class="cal-floating-label">السنة</span>
                    <select id="navYear" class="form-select cal-select-box" onchange="submitDateJump()">
                        @for($y = (int)$year - 3; $y <= (int)$year + 3; $y++)
                            <option value="{{ $y }}" {{ (int)$year === $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- اليسار: أزرار الأسهم الملكية للتنقل بين الشهور -->
            <div class="d-flex align-items-center gap-2">
                <!-- زر الشهر السابق -->
                <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="btn-cal-arrow" title="الشهر السابق">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <!-- زر الشهر القادم -->
                <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn-cal-arrow" title="الشهر القادم">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </div>
        </div>

        <!-- شريط أيام الأسبوع من الأحد إلى السبت -->
        <div class="cal-weekdays-row">
            <div class="cal-weekday-cell">الأحد</div>
            <div class="cal-weekday-cell">الإثنين</div>
            <div class="cal-weekday-cell">الثلاثاء</div>
            <div class="cal-weekday-cell">الأربعاء</div>
            <div class="cal-weekday-cell">الخميس</div>
            <div class="cal-weekday-cell">الجمعة</div>
            <div class="cal-weekday-cell">السبت</div>
        </div>

        <!-- شبكة الـ 42 يوماً -->
        <div class="cal-days-grid">
            @foreach($weeks as $week)
                @foreach($week as $dayData)
                    @php
                        $dayNum = $dayData['day'];
                        $isCurrentMonth = $dayData['isCurrentMonth'];
                        $isToday = $dayData['isToday'];
                        $events = $dayData['events'];
                        
                        $boxClass = '';
                        if (!$isCurrentMonth) $boxClass .= ' other-month';
                        if ($isToday) $boxClass .= ' highlight-day';
                    @endphp
                    <div class="cal-day-box {{ $boxClass }}">
                        <div class="cal-day-num">
                            {{ $dayNum }}
                        </div>

                        <div class="cal-events-wrap">
                            @foreach($events as $ev)
                                @php
                                    $t = $ev['training'];
                                    $kind = $ev['kind'];
                                    $barClass = ($kind === 'end') ? 'bar-amber' : (($t->type === 'workshop') ? 'bar-emerald' : 'bar-royal');
                                @endphp

                                @if($kind === 'ongoing')
                                    <!-- مسار زمني ناعم 6px للأيام المستمرة (بدون نصوص مكررة تشوه المظهر) -->
                                    <div class="cal-timeline-stripe stripe-royal cal-prog-item"
                                         data-kind="ongoing"
                                         data-location="{{ $ev['location'] }}"
                                         onclick="openCalendarModal('{{ addslashes($t->title) }}', '{{ addslashes($ev['location']) }}', '{{ $t->start_date ? $t->start_date->format('Y-m-d') : '' }}', '{{ $t->end_date ? $t->end_date->format('Y-m-d') : '' }}', '{{ $t->duration }}', '{{ addslashes($t->instructor_name ?? ($t->trainer->name ?? 'غير محدد')) }}', '{{ $t->seats }}', '{{ $t->status }}', '{{ route('evaluation-followup.training-programs.index') }}')"
                                         title="تدريب مستمر: {{ $ev['label'] }} - المكان: {{ $ev['location'] }}"
                                         style="display: none;">
                                    </div>
                                @else
                                    <!-- كبسولة بارزة لمواعيد الانطلاق والختام -->
                                    <div class="cal-prog-bar {{ $barClass }} cal-prog-item"
                                         data-kind="{{ $kind }}"
                                         data-location="{{ $ev['location'] }}"
                                         onclick="openCalendarModal('{{ addslashes($t->title) }}', '{{ addslashes($ev['location']) }}', '{{ $t->start_date ? $t->start_date->format('Y-m-d') : '' }}', '{{ $t->end_date ? $t->end_date->format('Y-m-d') : '' }}', '{{ $t->duration }}', '{{ addslashes($t->instructor_name ?? ($t->trainer->name ?? 'غير محدد')) }}', '{{ $t->seats }}', '{{ $t->status }}', '{{ route('evaluation-followup.training-programs.index') }}')"
                                         title="{{ $ev['label'] }} - المكان: {{ $ev['location'] }}">
                                        
                                        <div class="cal-prog-title">
                                            <i class="{{ $kind === 'end' ? 'fas fa-flag-checkered text-warning' : 'fas fa-graduation-cap' }} me-1 opacity-75" style="font-size: 0.65rem;"></i>
                                            <span>{{ $ev['short_label'] ?? \Illuminate\Support\Str::limit($ev['label'], 24) }}</span>
                                        </div>
                                        
                                        @if(!empty($ev['location']))
                                            <div class="cal-prog-loc">
                                                <i class="fas fa-map-marker-alt text-warning" style="font-size: 0.6rem;"></i>
                                                <span>{{ $ev['short_location'] ?? \Illuminate\Support\Str::limit($ev['location'], 18) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endforeach
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
                    <p class="text-muted small mb-3">يمكنك إضافة دورات وورش عمل لتظهر في التقويم الزمني والأجندة</p>
                    <a href="{{ route('evaluation-followup.training-programs.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>إضافة برنامج تدريب
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th class="ps-4 py-3">اسم البرنامج التدريبي</th>
                                <th class="py-3">مكان التدريب / القاعة</th>
                                <th class="py-3">الفترة الزمنية</th>
                                <th class="py-3">المدرب المشرف</th>
                                <th class="text-center py-3">المقاعد</th>
                                <th class="text-center py-3">الحالة</th>
                                <th class="text-center pe-4 py-3">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainings->sortByDesc('start_date') as $t)
                            <tr class="table-training-row">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle bg-light text-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('evaluation-followup.training-programs.index') }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                {{ $t->title }}
                                            </a>
                                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                                {{ $t->category ?? 'تدريب عام' }}
                                            </span>
                                        </div>
                                    </div>
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
                                    <a href="{{ route('evaluation-followup.training-programs.index') }}" class="btn btn-sm btn-primary-modern py-1 px-2.5 rounded-2">
                                        <i class="fas fa-eye me-1"></i>التفاصيل
                                    </a>
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

<!-- Modal تفاصيل التدريب التفاعلي -->
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
                    <a id="mEventUrl" href="#" class="btn btn-primary-modern rounded-3 px-3 fw-bold d-flex align-items-center gap-1">
                        <i class="fas fa-eye"></i>
                        <span>عرض البرامج التدريبية</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentMode = 'milestones';

function submitDateJump() {
    const m = document.getElementById('navMonth').value;
    const y = document.getElementById('navYear').value;
    window.location.href = '?month=' + m + '&year=' + y;
}

function toggleCalendarMode(mode) {
    currentMode = mode;
    const btnMilestones = document.getElementById('btnModeMilestones');
    const btnAll = document.getElementById('btnModeAll');

    if (mode === 'milestones') {
        btnMilestones.classList.add('btn-primary');
        btnMilestones.classList.remove('btn-outline-primary');
        btnAll.classList.add('btn-outline-primary');
        btnAll.classList.remove('btn-primary');
    } else {
        btnAll.classList.add('btn-primary');
        btnAll.classList.remove('btn-outline-primary');
        btnMilestones.classList.add('btn-outline-primary');
        btnMilestones.classList.remove('btn-primary');
    }

    applyCalendarFilters();
}

function filterCalendarByLocation(loc) {
    applyCalendarFilters();
}

function applyCalendarFilters() {
    const locSelect = document.getElementById('locationFilterSelect');
    const selectedLoc = locSelect ? locSelect.value : 'all';

    const progItems = document.querySelectorAll('.cal-prog-item');
    progItems.forEach(el => {
        const kind = el.getAttribute('data-kind');
        const loc = el.getAttribute('data-location');

        let modeMatch = true;
        if (currentMode === 'milestones') {
            modeMatch = (kind === 'start' || kind === 'end');
        } else {
            modeMatch = true;
        }

        let locMatch = true;
        if (selectedLoc !== 'all') {
            locMatch = (loc === selectedLoc);
        }

        if (modeMatch && locMatch) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}

function openCalendarModal(title, location, start, end, duration, instructor, seats, status, url) {
    document.getElementById('mEventHeading').innerText = title;
    document.getElementById('mEventLocation').innerText = location || 'غير محدد';
    document.getElementById('mEventDates').innerText = (start || '') + ' إلى ' + (end || '');
    document.getElementById('mEventDuration').innerText = duration || 'غير محدد';
    document.getElementById('mEventInstructor').innerText = instructor || 'غير محدد';
    document.getElementById('mEventSeats').innerText = (seats || '0') + ' مقعد';
    
    document.getElementById('mEventUrl').href = url;

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
