@extends('layouts.app')

@section('title', 'تقويم التدريبات - التقييم والمتابعة')
@section('page-title', 'تقويم التدريبات')

@push('styles')
<style>
    /* Hero & Card Modern */
    .calendar-hero-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 1.25rem 1.5rem;
    }

    /* Month Navigation Controls */
    .month-nav-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Desktop Calendar Grid */
    .calendar-grid-container {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .calendar-header-row {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .calendar-day-header-cell {
        padding: 0.85rem 0.5rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #475569;
    }

    .calendar-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #e2e8f0;
        gap: 1px;
    }

    .calendar-day-cell {
        background: #ffffff;
        min-height: 120px;
        padding: 0.65rem;
        display: flex;
        flex-direction: column;
        transition: background 0.15s ease;
    }

    .calendar-day-cell:hover {
        background: #f8fafc;
    }

    .calendar-day-cell.empty {
        background: #f8fafc;
    }

    .calendar-day-cell.today {
        background: #eff6ff;
    }

    .calendar-day-cell.weekend {
        background: #fffbeb;
    }

    .day-num-badge {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .calendar-day-cell.today .day-num-badge {
        background: #1e40af;
        color: #ffffff;
    }

    .day-events-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 0.4rem;
        overflow-y: auto;
        max-height: 80px;
    }

    .event-pill {
        padding: 3px 6px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        text-decoration: none;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        border-right: 3px solid;
        transition: transform 0.15s ease;
    }

    .event-pill:hover {
        transform: translateX(-2px);
    }

    .event-pill.status-active {
        background: #dcfce7;
        color: #166534;
        border-right-color: #22c55e;
    }

    .event-pill.status-inactive {
        background: #fef3c7;
        color: #92400e;
        border-right-color: #f59e0b;
    }

    .event-pill.status-completed {
        background: #f1f5f9;
        color: #475569;
        border-right-color: #94a3b8;
    }

    /* 📱 Mobile Agenda & Event Cards */
    .mobile-event-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        margin-bottom: 0.75rem;
        position: relative;
        border-right: 4px solid;
    }

    .mobile-event-card.status-active { border-right-color: #10b981; }
    .mobile-event-card.status-inactive { border-right-color: #f59e0b; }
    .mobile-event-card.status-completed { border-right-color: #64748b; }

    /* Mini Mobile Date Strip / Grid */
    .mini-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
        background: #ffffff;
        padding: 0.75rem;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        margin-bottom: 1rem;
    }

    .mini-day-cell {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
        position: relative;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .mini-day-cell.empty {
        opacity: 0.2;
    }

    .mini-day-cell.today {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .mini-day-cell.has-events::after {
        content: '';
        position: absolute;
        bottom: 3px;
        width: 5px;
        height: 5px;
        background: #10b981;
        border-radius: 50%;
    }

    .mini-day-cell.active-selected {
        background: #1e40af !important;
        color: #ffffff !important;
    }

    .mini-day-cell.active-selected::after {
        background: #fef08a !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('evaluation-followup.dashboard')],
            ['label' => 'تقويم التدريبات', 'active' => true],
        ]
    ])

    <!-- رأس الصفحة مع التنقل بين الشهور -->
    <div class="calendar-hero-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; font-size: 1.25rem;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h1 class="h5 mb-1 text-primary fw-bold">تقويم التدريبات</h1>
                    <p class="text-muted small mb-0">متابعة مواعيد وجداول البرامج التدريبية</p>
                </div>
            </div>

            <!-- أزرار التنقل -->
            <div class="month-nav-box">
                <a href="{{ route('evaluation-followup.training-calendar', ['month' => $month - 1, 'year' => $year]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-chevron-right me-1"></i>الشهر السابق
                </a>
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill fw-bold">
                    {{ $calendar['month_name'] }} {{ $year }}
                </span>
                <a href="{{ route('evaluation-followup.training-calendar', ['month' => $month + 1, 'year' => $year]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    الشهر التالي<i class="fas fa-chevron-left ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات (2x2 على الموبايل و4 على الديسكتوب) -->
    <div class="row g-2 g-md-3 mb-4">
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'التدريبات النشطة',
            'value' => $trainings->where('status', 'active')->count(),
            'icon' => 'fas fa-play-circle',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي التدريبات',
            'value' => $trainings->count(),
            'icon' => 'fas fa-graduation-cap',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'إجمالي المقاعد',
            'value' => $trainings->sum('seats'),
            'icon' => 'fas fa-users',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-md-3',
            'title' => 'المواقع المختلفة',
            'value' => $trainings->unique('location')->count(),
            'icon' => 'fas fa-map-marker-alt',
            'color' => 'info'
        ])
    </div>

    {{-- 🖥️ عرض سطح المكتب: تقويم شبكي كامل --}}
    <div class="calendar-grid-container d-none d-md-block mb-4">
        <div class="calendar-header-row">
            @foreach($calendar['days'] as $dayName)
            <div class="calendar-day-header-cell">{{ $dayName }}</div>
            @endforeach
        </div>

        <div class="calendar-days-grid">
            @foreach($calendar['weeks'] as $week)
                @foreach($week as $dayData)
                <div class="calendar-day-cell {{ !$dayData['day'] ? 'empty' : '' }} {{ $dayData['day'] && $dayData['day']->isToday() ? 'today' : '' }} {{ $dayData['day'] && $dayData['day']->isWeekend() ? 'weekend' : '' }}">
                    @if($dayData['day'])
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="day-num-badge">{{ $dayData['day']->day }}</span>
                            @if(count($dayData['trainings']) > 0)
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2" style="font-size: 0.65rem;">
                                {{ count($dayData['trainings']) }} تدريب
                            </span>
                            @endif
                        </div>

                        <div class="day-events-list">
                            @foreach($dayData['trainings'] as $t)
                            <div class="event-pill status-{{ $t->status }}" title="{{ $t->title }} - {{ $t->location }}">
                                <i class="fas fa-bookmark me-1" style="font-size: 0.65rem;"></i>{{ $t->title }}
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- 📱 عرض الموبايل: شريط مصغر + قائمة الأحداث والتدريبات للشهر --}}
    <div class="d-md-none mb-4">
        <!-- مصغر التقويم -->
        <div class="mini-calendar-grid">
            @foreach(['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'] as $shortDay)
            <div class="text-center text-muted fw-bold pb-1" style="font-size: 0.72rem;">{{ $shortDay }}</div>
            @endforeach

            @foreach($calendar['weeks'] as $week)
                @foreach($week as $dayData)
                <div class="mini-day-cell {{ !$dayData['day'] ? 'empty' : '' }} {{ $dayData['day'] && $dayData['day']->isToday() ? 'today' : '' }} {{ $dayData['day'] && count($dayData['trainings']) > 0 ? 'has-events' : '' }}"
                     onclick="scrollToDay('day-section-{{ $dayData['day'] ? $dayData['day']->day : '' }}')">
                    {{ $dayData['day'] ? $dayData['day']->day : '' }}
                </div>
                @endforeach
            @endforeach
        </div>

        <!-- قائمة تدريبات الشهر (Agenda View) -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fas fa-list-ul me-2 text-primary"></i>برامج شهر {{ $calendar['month_name'] }}
            </h6>
            <span class="badge bg-light text-primary border rounded-pill">{{ $trainings->count() }} تدريب</span>
        </div>

        @php
            $hasAnyTrainings = false;
        @endphp

        @foreach($calendar['weeks'] as $week)
            @foreach($week as $dayData)
                @if($dayData['day'] && count($dayData['trainings']) > 0)
                    @php $hasAnyTrainings = true; @endphp
                    <div id="day-section-{{ $dayData['day']->day }}" class="mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                                {{ $dayData['day']->format('Y-m-d') }}
                            </span>
                            <small class="text-muted fw-bold">({{ $dayData['day']->locale('ar')->dayName ?? '' }})</small>
                        </div>

                        @foreach($dayData['trainings'] as $training)
                        <div class="mobile-event-card status-{{ $training->status }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold text-dark mb-1 fs-6">{{ $training->title }}</h6>
                                <span class="badge rounded-pill
                                    @if($training->status === 'active') bg-success text-white
                                    @elseif($training->status === 'completed') bg-secondary text-white
                                    @else bg-warning text-dark
                                    @endif" style="font-size: 0.68rem;">
                                    {{ $training->status === 'active' ? 'نشط' : ($training->status === 'completed' ? 'مكتمل' : 'قيد الانتظار') }}
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-1 text-muted small mt-2">
                                @if($training->company)
                                <div><i class="fas fa-building me-1 text-primary"></i>{{ $training->company->name }}</div>
                                @endif
                                @if($training->location)
                                <div><i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $training->location }}</div>
                                @endif
                                <div><i class="fas fa-chair me-1 text-info"></i>المقاعد المتاحة: <strong>{{ $training->seats }}</strong></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endforeach

        @if(!$hasAnyTrainings)
        <div class="text-center py-5 card-modern">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
            <h6 class="text-muted">لا توجد تدريبات مجدولة في هذا الشهر</h6>
        </div>
        @endif
    </div>

    <!-- مفتاح الألوان -->
    <div class="card-modern p-3">
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-3 gap-md-4 small">
            <div class="d-flex align-items-center gap-1">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #22c55e;"></span>
                <span>تدريب نشط</span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></span>
                <span>تدريب متوقف / معلق</span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #94a3b8;"></span>
                <span>تدريب مكتمل</span>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #1e40af;"></span>
                <span>اليوم الحالي</span>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function scrollToDay(elementId) {
        var el = document.getElementById(elementId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('bg-warning', 'bg-opacity-10');
            setTimeout(function() {
                el.classList.remove('bg-warning', 'bg-opacity-10');
            }, 1500);
        }
    }
</script>
@endsection
