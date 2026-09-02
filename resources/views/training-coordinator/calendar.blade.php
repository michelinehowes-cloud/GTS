@extends('layouts.app')

@section('title', 'تقويم التدريبات - منسق التدريب')
@section('page-title', 'تقويم التدريبات')

@push('styles')
<style>
    /* Hero & Card Modern */
    .calendar-hero-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 1.5rem;
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
        border-radius: 50%;
        background: #10b981;
    }

    .mini-day-cell.has-events.today::after {
        background: #1d4ed8;
    }

    @media (max-width: 767.98px) {
        .calendar-hero-card {
            padding: 1rem 0.85rem !important;
            border-radius: 14px !important;
        }

        .month-nav-box {
            width: 100% !important;
            justify-content: space-between !important;
        }

        .month-nav-box .btn-group {
            width: 100% !important;
            display: flex !important;
        }

        .month-nav-box .btn-group .btn {
            flex: 1 !important;
            font-size: 0.78rem !important;
            padding: 0.45rem 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['label' => 'الرئيسية', 'url' => route('home')],
            ['label' => 'لوحة تحكم منسق التدريب', 'url' => route('training-coordinator.dashboard')],
            ['label' => 'تقويم البرامج التدريبية', 'active' => true],
        ]
    ])

    {{-- رأس الصفحة مع أدوات التنقل بين الشهور --}}
    <div class="calendar-hero-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-light-primary text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-0 fs-5">تقويم البرامج التدريبية</h3>
                    <p class="text-muted small mb-0">{{ $calendar['month_name'] }} {{ $year }}</p>
                </div>
            </div>

            {{-- أزرار التنقل بين الشهور --}}
            <div class="month-nav-box">
                <div class="btn-group shadow-sm" role="group">
                    <a href="{{ route('training-coordinator.calendar', ['month' => $month - 1, 'year' => $year]) }}"
                        class="btn btn-outline-secondary btn-sm fw-medium">
                        <i class="fas fa-chevron-right me-1"></i> السابق
                    </a>
                    <button class="btn btn-primary btn-sm fw-bold px-3" disabled style="opacity: 1 !important;">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $calendar['month_name'] }} {{ $year }}
                    </button>
                    <a href="{{ route('training-coordinator.calendar', ['month' => $month + 1, 'year' => $year]) }}"
                        class="btn btn-outline-secondary btn-sm fw-medium">
                        التالي <i class="fas fa-chevron-left ms-1"></i>
                    </a>
                </div>

                <a href="{{ route('training-coordinator.trainings') }}" class="btn btn-outline-primary btn-sm fw-medium">
                    <i class="fas fa-list me-1"></i> قائمة التدريبات
                </a>
            </div>
        </div>
    </div>

    {{-- 📊 بطاقات الإحصائيات السريعة (2x2 على الموبايل) --}}
    <div class="row mb-3">
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'التدريبات النشطة',
            'value' => $trainings->where('status', 'active')->count(),
            'icon' => 'fas fa-play-circle',
            'color' => 'success'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'إجمالي التدريبات',
            'value' => $trainings->count(),
            'icon' => 'fas fa-calendar-check',
            'color' => 'primary'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'إجمالي المقاعد',
            'value' => $trainings->sum('seats'),
            'icon' => 'fas fa-users',
            'color' => 'warning'
        ])
        @include('components.stat-card', [
            'col' => 'col-6 col-xl-3 mb-3',
            'title' => 'المواقع المعتمدة',
            'value' => $trainings->unique('location')->count(),
            'icon' => 'fas fa-map-marker-alt',
            'color' => 'info'
        ])
    </div>

    {{-- 📱 عرض خاص بالموبايل: مصغّر الشهر + قائمة الأجندة التفاعلية --}}
    <div class="d-block d-md-none mb-4">
        
        {{-- شبكة أيام الشهر المصغّرة --}}
        <div class="mini-calendar-grid shadow-sm">
            @foreach(['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'] as $shortDay)
                <div class="text-center fw-bold text-muted small py-1" style="font-size: 0.72rem;">{{ $shortDay }}</div>
            @endforeach

            @foreach($calendar['weeks'] as $week)
                @foreach($week as $dayData)
                    @if(!$dayData['day'])
                        <div class="mini-day-cell empty"></div>
                    @else
                        @php
                            $hasEvents = count($dayData['trainings']) > 0;
                            $isToday = $dayData['day']->isToday();
                        @endphp
                        <div class="mini-day-cell {{ $isToday ? 'today' : '' }} {{ $hasEvents ? 'has-events' : '' }}">
                            {{ $dayData['day']->day }}
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>

        {{-- قائمة أجندة التدريبات للهاتف --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">
                <i class="fas fa-stream me-1 text-primary"></i> أجندة تدريبات {{ $calendar['month_name'] }}
            </h6>
            <span class="badge bg-light text-primary border border-primary rounded-pill px-2 py-1 small">
                {{ $trainings->count() }} تدريب
            </span>
        </div>

        @if($trainings->count() > 0)
            @foreach($trainings->sortBy('start_date') as $training)
                @php
                    $statusClass = $training->status === 'active' ? 'status-active' : ($training->status === 'completed' ? 'status-completed' : 'status-inactive');
                    $statusLabel = $training->status === 'active' ? '🟢 نشط' : ($training->status === 'completed' ? '✅ مكتمل' : '⏸ غير نشط');
                @endphp
                <div class="mobile-event-card {{ $statusClass }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                            {{ $training->type_arabic ?? 'تدريب' }}
                        </span>
                        <span class="badge {{ $training->status === 'active' ? 'bg-success text-white' : 'bg-light text-muted border' }} rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark fs-6 mb-2">
                        <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="text-dark text-decoration-none">
                            {{ $training->title }}
                        </a>
                    </h5>

                    <div class="d-flex flex-column gap-1 text-muted small mb-3" style="font-size: 0.78rem;">
                        <div>
                            <i class="fas fa-calendar-day me-1 text-primary"></i>
                            من <strong>{{ $training->start_date ? \Carbon\Carbon::parse($training->start_date)->format('Y-m-d') : '—' }}</strong> 
                            إلى <strong>{{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('Y-m-d') : '—' }}</strong>
                        </div>
                        @if($training->location)
                        <div>
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                            {{ $training->location }}
                        </div>
                        @endif
                        <div>
                            <i class="fas fa-users me-1 text-info"></i>
                            المقاعد: <strong>{{ $training->seats }}</strong> مقعد
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('training-coordinator.trainings.show', $training->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill" style="font-size: 0.75rem;">
                            <i class="fas fa-eye me-1"></i> التفاصيل
                        </a>
                        <a href="{{ route('training-coordinator.trainings.attendance', $training->id) }}" class="btn btn-sm btn-warning-modern flex-grow-1 rounded-pill" style="font-size: 0.75rem;">
                            <i class="fas fa-clipboard-check me-1"></i> الحضور
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card-modern p-4 text-center">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                <h6 class="fw-bold text-dark">لا توجد برامج تدريبية مجدولة</h6>
                <p class="text-muted small mb-3">لم يتم تسجيل برامج تدريبية في هذا الشهر</p>
                <a href="{{ route('training-coordinator.trainings.create') }}" class="btn btn-sm btn-primary-modern rounded-pill px-4 mx-auto">
                    <i class="fas fa-plus me-1"></i> إضافة برنامج تدريب
                </a>
            </div>
        @endif
    </div>

    {{-- 💻 عرض الشاشات الكبيرة (Desktop 7-Column Grid) --}}
    <div class="d-none d-md-block">
        <div class="calendar-grid-container">
            {{-- أسماء أيام الأسبوع --}}
            <div class="calendar-header-row">
                @foreach($calendar['days'] as $day)
                    <div class="calendar-day-header-cell">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            {{-- خلايا التقويم --}}
            <div class="calendar-days-grid">
                @foreach($calendar['weeks'] as $week)
                    @foreach($week as $dayData)
                        @php
                            $isDay = !is_null($dayData['day']);
                            $isToday = $isDay && $dayData['day']->isToday();
                            $isWeekend = $isDay && $dayData['day']->isWeekend();
                        @endphp
                        <div class="calendar-day-cell {{ !$isDay ? 'empty' : '' }} {{ $isToday ? 'today' : '' }} {{ $isWeekend ? 'weekend' : '' }}">
                            @if($isDay)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="day-num-badge">{{ $dayData['day']->day }}</span>
                                    @if($isToday)
                                        <span class="badge bg-primary rounded-pill px-2 py-0" style="font-size: 0.65rem;">اليوم</span>
                                    @endif
                                </div>

                                <div class="day-events-list">
                                    @foreach($dayData['trainings'] as $training)
                                        @php
                                            $pillClass = $training->status === 'active' ? 'status-active' : ($training->status === 'completed' ? 'status-completed' : 'status-inactive');
                                        @endphp
                                        <a href="{{ route('training-coordinator.trainings.show', $training->id) }}"
                                           class="event-pill {{ $pillClass }}"
                                           title="{{ $training->title }} ({{ $training->location }})">
                                            <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i>
                                            {{ Str::limit($training->title, 18) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- مفتاح الألوان --}}
        <div class="card-modern mt-3 p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <span class="fw-bold text-dark small"><i class="fas fa-tags me-1 text-primary"></i> مفتاح ألوان التقويم:</span>
                <div class="d-flex gap-3 flex-wrap small">
                    <span class="d-flex align-items-center gap-1">
                        <span style="width: 12px; height: 12px; background: #22c55e; border-radius: 3px; display: inline-block;"></span> تدريب نشط
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width: 12px; height: 12px; background: #f59e0b; border-radius: 3px; display: inline-block;"></span> تدريب متوقف / معلق
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width: 12px; height: 12px; background: #94a3b8; border-radius: 3px; display: inline-block;"></span> تدريب مكتمل
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span style="width: 12px; height: 12px; background: #1e40af; border-radius: 3px; display: inline-block;"></span> تاريخ اليوم
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection